"use strict";
var pafBridge = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.HtmlViewBridge.apply(this, arguments);
};

pafBridge.prototype = new window.rhubarb.viewBridgeClasses.HtmlViewBridge();
pafBridge.prototype.constructor = pafBridge;

pafBridge.prototype.attachEvents = function () {
    var self = this;

    this.resultsDropDown = self.findChildViewBridge('Results');

    var errorElement = document.getElementById('paf-search-error');

    self.findChildViewBridge('Search').attachClientEventHandler('ButtonPressCompleted', function(response) {
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

    this.addressFields = {};
    var subPresenters = self.getSubPresenters();
    for (var i = 0; i < subPresenters.length; i++) {
        var subPresenter = subPresenters[i];
        if (subPresenter.viewNode.classList.contains('paf-address-field')) {
            this.addressFields[subPresenter.presenterName] = subPresenter;
        }
    }

    var searchAgainLink = document.getElementById('paf-search-again-link');
    if (searchAgainLink) {
        searchAgainLink.onclick = function () {
            self.showSearchFields();
        };
    }

    var manualAddressLink = document.getElementById('paf-manual-address-link');
    if (manualAddressLink) {
        this.manualAddressAction = document.getElementById('paf-manual-address-action');
        manualAddressLink.onclick = function() {
            self.showAddress();
        };
    }

    this.resultsDropDown.attachClientEventHandler('ValueChanged', function(dropDown, newValue)
    {
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
        }

        self.hideEmptyAddressFields();
        self.showAddress();

        dropDown.setValue('');
    });
};

pafBridge.prototype.hideEmptyAddressFields = function()
{
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

pafBridge.prototype.showAddress = function()
{
    this.showElements('paf-address-fields');
    this.hideElements('paf-search-fields');
    if (this.manualAddressAction) {
        this.manualAddressAction.classList.add('-hidden');
    }
};

pafBridge.prototype.showSearchFields = function()
{
    this.hideElements('paf-address-fields', 'paf-search-error');
    this.showElements('paf-search-fields');
    this.resultsDropDown.viewNode.classList.add('-hidden');
    if (this.manualAddressAction) {
        this.manualAddressAction.classList.remove('-hidden');
    }
};

pafBridge.prototype.hideElements = function(elementId) {
    for (var i = 0; i < arguments.length; i++) {
        document.getElementById(arguments[i]).classList.add('-hidden');
    }
};

pafBridge.prototype.showElements = function(elementId) {
    for (var i = 0; i < arguments.length; i++) {
        document.getElementById(arguments[i]).classList.remove('-hidden');
    }
};

window.rhubarb.viewBridgeClasses.AddressUkPafLookupViewBridge = pafBridge;
