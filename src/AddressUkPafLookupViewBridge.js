"use strict";
var pafBridge = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.HtmlViewBridge.apply(this, arguments);
};

pafBridge.prototype = new window.rhubarb.viewBridgeClasses.HtmlViewBridge();
pafBridge.prototype.constructor = pafBridge;

pafBridge.prototype.attachEvents = function () {
    var self = this;

    this.resultsDropDown = self.findChildViewBridge('Results');

    var errorElement = self.viewNode.getElementsByClassName('paf-search-error')[0];

    this.searchButton = self.findChildViewBridge('Search');

    this.searchButton.attachClientEventHandler('ButtonPressCompleted', function (response) {
        if (response == 'success') {
            errorElement.classList.add('-hidden');
            self.resultsDropDown.viewNode.classList.remove('-hidden');
        } else {
            // Error response
            errorElement.innerHTML = response;
            errorElement.classList.remove('-hidden');
            self.resultsDropDown.viewNode.classList.add('-hidden');
        }
    });

    self.findChildViewBridge('PostcodeSearch').viewNode.onkeypress = function (event) {
        if (event.keyCode == 13) {
            self.searchButton.viewNode.click();
            event.preventDefault();
        }
    };

    var addressSummary = self.viewNode.getElementsByClassName('paf-address-summary')[0];
    addressSummary = addressSummary.getElementsByClassName('paf-address-part');
    this.addressSummaryFields = {};
    for (var i = 0; i < addressSummary.length; i++) {
        var fieldName = addressSummary[i].getAttribute('data-paf-field');
        if (fieldName) {
            this.addressSummaryFields[fieldName] = addressSummary[i];
        }
    }

    this.addressFields = {};
    var subPresenters = self.getSubPresenters();
    for (i = 0; i < subPresenters.length; i++) {
        var subPresenter = subPresenters[i];
        if (subPresenter.viewNode.classList.contains('paf-address-field')) {
            this.addressFields[subPresenter.presenterName] = subPresenter;
        }
    }

    var searchAgainLink = self.viewNode.getElementsByClassName('paf-search-again-link')[0];
    if (searchAgainLink) {
        searchAgainLink.onclick = function () {
            self.showSearchFields();
        };
    }

    var manualAddressLink = self.viewNode.getElementsByClassName('paf-manual-address-link')[0];
    if (manualAddressLink) {
        this.manualAddressAction = self.viewNode.getElementsByClassName('paf-manual-address-action')[0];
        manualAddressLink.onclick = function () {
            self.showAddress(true);
        };
    }

    this.resultsDropDown.attachClientEventHandler('ValueChanged', function (dropDown, newValue) {
        if (!newValue) {
            return;
        }

        var address = dropDown.getSelectedItem();

        for (var fieldName in address.data) {
            if (!address.data.hasOwnProperty(fieldName)) {
                continue;
            }

            if (fieldName in self.addressFields) {
                self.addressFields[fieldName].setValue(address.data[fieldName]);
            }

            if (fieldName in self.addressSummaryFields) {
                self.addressSummaryFields[fieldName].innerHTML = address.data[fieldName];
            }
        }

        // Empty any fields that didn't have values in the selected address
        for (fieldName in self.addressFields) {
            if (self.addressFields.hasOwnProperty(fieldName) && !address.data.hasOwnProperty(fieldName)) {
                self.addressFields[fieldName].setValue('');
                self.addressSummaryFields[fieldName].innerHTML = '';
            }
        }

        self.hideEmptyAddressFields();
        self.showAddress();

        dropDown.setValue('');
    });

    self.viewNode.getElementsByClassName('paf-change-address-button')[0].onclick = function () {
        self.showAddress(true);
    };
};

pafBridge.prototype.hideEmptyAddressFields = function () {
    for (var addressPart in this.addressFields) {
        if (!this.addressFields.hasOwnProperty(addressPart)) {
            continue;
        }

        var addressField = this.addressFields[addressPart];
        if (addressField.getValue().trim() == '') {
            $(addressField.viewNode).closest('.c-form__group').addClass('-hidden');
        } else {
            $(addressField.viewNode).closest('.c-form__group').removeClass('-hidden');
        }
    }
};

pafBridge.prototype.setAddressField = function (fieldName, value) {
    if (fieldName in this.addressFields) {
        this.addressFields[fieldName].setValue(value);
    }

    if (fieldName in this.addressSummaryFields) {
        this.addressSummaryFields[fieldName].innerHTML = value;
    }
};

pafBridge.prototype.isAddressPopulated = function () {
    for (var fieldName in this.addressFields) {
        if (this.addressFields.hasOwnProperty(fieldName)) {
            if (this.addressFields[fieldName].getValue() != '') {
                return true;
            }
        }
    }

    return false;
};

pafBridge.prototype.showAddress = function (showFields) {
    this.showElements('paf-address');
    this.hideElements('paf-search-fields');
    if (this.manualAddressAction) {
        this.manualAddressAction.classList.add('-hidden');
    }

    if (showFields) {
        this.hideElements('paf-address-summary');
        this.showElements('paf-address-fields');
    } else {
        this.showElements('paf-address-summary');
        this.hideElements('paf-address-fields');
    }
};

pafBridge.prototype.showSearchFields = function () {
    this.hideElements('paf-address', 'paf-search-error');
    this.showElements('paf-search-fields');
    this.resultsDropDown.viewNode.classList.add('-hidden');
    if (this.manualAddressAction) {
        this.manualAddressAction.classList.remove('-hidden');
    }
};

pafBridge.prototype.showSearchOrSummary = function () {
    if (this.isAddressPopulated()) {
        this.showAddress(false);
    } else {
        this.showSearchFields();
    }
};

pafBridge.prototype.hideElements = function (elementId) {
    for (var i = 0; i < arguments.length; i++) {
        this.viewNode.getElementsByClassName(arguments[i])[0].classList.add('-hidden');
    }
};

pafBridge.prototype.showElements = function (elementId) {
    for (var i = 0; i < arguments.length; i++) {
        this.viewNode.getElementsByClassName(arguments[i])[0].classList.remove('-hidden');
    }
};

pafBridge.prototype.getValue = function(){
    var fields = [
        'Organisation',
        'AddressLine1',
        'AddressLine2',
        'AddressLine3',
        'Town',
        'County',
        'Postcode'
    ];

    var address = [];

    for(var i=0; i<fields.length; i++){
        address[fields[i]] = this.findViewBridge(fields[i]).getValue();
    }

    return address;
};

window.rhubarb.viewBridgeClasses.AddressUkPafLookupViewBridge = pafBridge;
