var bridge = function(presenterPath)
{
    window.rhubarb.viewBridgeClasses.HtmlViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.HtmlViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function()
{

    var self = this,
        alertClass = "c-alert",
        manualAddressElements = $(".manual-fields"),
        searchAddressElement = $(".search-fields"),
        insertManualAddressLink = $(".manual-address-link"),
        manualAddressPar = $(".manual-address-par"),
        searchLink = $(".search-address-link"),
        houseNumber = self.findChildViewBridge('HouseNumber'),
        postCodeSearch = self.findChildViewBridge('PostCodeSearch'),
        searchError = $(".search-error"),
        searchResultsMsg = $(".search-results-msg"),
        resultItemsList = $(".search-results-items"),
        spinnerGif = $('.spinner'),
        searchButton = self.findChildViewBridge('Search'),
        country = self.findChildViewBridge('Country'),
    // address fields
        line1 = self.findChildViewBridge('Line1'),
        line2 = self.findChildViewBridge('Line2'),
        town = self.findChildViewBridge('Town'),
        county = self.findChildViewBridge('County'),
        postCode = self.findChildViewBridge('PostCode'),
        addressProperties = ['AddressLine1', 'AddressLine2', 'Town', 'County', 'Postcode'];

    // hide spinner on loading
    spinnerGif.hide();

    // if the's a post code we suppose that there's an address set
    if (postCode.viewNode.value != '') {
        showAddressFields();
    } else {
        // default configuration
        manualAddressElements.hide();
        searchLink.hide();
        searchError.hide();
    }

    if (country.getValue() != 'GB') {
        showAddressFields();
        searchLink.hide();
    } else {
        showSearchFields()
        searchError.hide();
    }

    // address manual entry
    insertManualAddressLink.click(function()
    {
        searchResultsMsg.hide();
        showAddressFields();
        return false;
    });
    // search address
    searchLink.click(function()
    {
        searchResultsMsg.hide();
        resultItemsList.empty();
        showSearchFields();
        return false;
    });

    // if country changes and is different from uk show the manual entry
    country.attachClientEventHandler("ValueChanged", function()
    {
        if (country.getValue() != 'GB') {
            showAddressFields();
            searchLink.hide();
        } else {
            showSearchFields();
            searchError.hide();
        }
    });

    // search address
    searchButton.attachClientEventHandler("OnButtonPressed", function()
    {
        searchError.hide();
        spinnerGif.show();
        searchResultsMsg.removeClass(alertClass).empty();
        // if post Code is empty show an error message
        if (!postCodeSearch.viewNode.value) {
            spinnerGif.hide();
            searchError.show();
            return false;
        }

        self.raiseServerEvent("SearchPressed", houseNumber.viewNode.value, postCodeSearch.viewNode.value,
            function(response)
            {
                spinnerGif.hide();
                // single result fill address fields and fill them
                if(response) {
                    if (response.length == 1) {
                        showAddressFields();
                        setAddressFields(response[0]);
                    } else {
                        searchResultsMsg.addClass(alertClass).append("We found " + response.length + " results");
                        var resultString = "";
                        for (var i in response) {
                            var currItem = response[i];

                            resultString +=
                                "<li class='result-item'>";
                            for (var i in addressProperties) {
                                var property = addressProperties[i],
                                    value = currItem[property];
                                if (typeof value === 'undefined') {
                                    value = '';
                                }
                                resultString += " <span class='" + property + "'>" + value + "</span>";
                            }
                            resultString += "</li>";
                        }
                        resultItemsList.html(resultString);
                    }
                }  else {
                    searchResultsMsg.addClass(alertClass).append("Sorry, we couldn't find any addresses matching your search. Please verify the postcode, or enter the address manually.");
                }
            });
        return false;
    });

    // show fields for search an address
    function showSearchFields()
    {
        manualAddressPar.show();
        manualAddressElements.hide();
        searchAddressElement.show();
        searchLink.hide();
    }

    // show address fields
    function showAddressFields()
    {
        manualAddressPar.hide();
        manualAddressElements.show();
        searchAddressElement.hide();
        searchLink.show();
    }

    // set address fields
    function setAddressFields(addressObj)
    {
        if (addressObj['AddressLine1'] != undefined) {
            line1.viewNode.value = addressObj['AddressLine1'];
        }

        if (addressObj['AddressLine2'] != undefined) {
            line2.viewNode.value = addressObj['AddressLine2'];
        }

        if (addressObj['Town'] != undefined) {
            town.viewNode.value = addressObj['Town'];
        }

        if (addressObj['County'] != undefined) {
            county.viewNode.value = addressObj['County'];
        }

        if (addressObj['Postcode'] != undefined) {
            postCode.viewNode.value = addressObj['Postcode'];
        }
    }

    // click event on resultItem of the search, map values in array and set address fields
    resultItemsList.on("click", "li.result-item", function()
    {
        var itemValues = $(this).find("span"),
            addressObj = {};

        itemValues.each(function()
        {
            var currEl = $(this);
            addressObj[currEl.attr('class')] = currEl.text();
        });
        setAddressFields(addressObj);
        showAddressFields();
        return false;
    });
};

window.rhubarb.viewBridgeClasses.AddressUkPafLookupViewBridge = bridge;