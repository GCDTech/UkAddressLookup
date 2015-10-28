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
        manualAddressElements = document.getElementById("manual-fields"),
        resultItems = document.getElementsByClassName("result-item"),
        searchAddressElement = document.getElementById("search-fields"),
        insertManualAddressLink = document.getElementById("manual-address-link"),
        manualAddressPar = document.getElementById("manual-address-par"),
        searchAddressPar = document.getElementById("search-address-par"),
        searchLink = document.getElementById("search-address-link"),
        houseNumber = self.findChildViewBridge('HouseNumber'),
        postCodeSearch = self.findChildViewBridge('PostCodeSearch'),
        searchError = document.getElementById("search-error"),
        searchResultsMsg = document.getElementById("search-results-msg"),
        resultItemsList = document.getElementById("search-results-items"),
        spinnerGif = document.getElementById('spinner'),
        searchButton = self.findChildViewBridge('Search'),
        country = self.findChildViewBridge('Country'),
    // address fields
        line1 = self.findChildViewBridge('Line1'),
        line2 = self.findChildViewBridge('Line2'),
        town = self.findChildViewBridge('Town'),
        county = self.findChildViewBridge('County'),
        postCode = self.findChildViewBridge('PostCode'),
        addressProperties = ['AddressLine1', 'AddressLine2', 'Town', 'County', 'Postcode'];

    function hide(el) {
        el.style.display = 'none';
    }
    function show(el) {
        el.style.display = 'block';
    }
    function setText(el, text) {
        el.innerHTML = text;
    }
    function removeClass(el, className) {
        el.classList.remove( className );
    }
    function addClass(el, className) {
        el.classList.add( className );
    }

    // hide spinner on loading
    hide(spinnerGif);

    // if the's a post code we suppose that there's an address set
    if(postCode.viewNode.value != '') {
        showAddressFields();
    } else {
        // default configuration
        hide(manualAddressElements);
        hide(searchAddressPar);
        hide(searchError);
    }

    if (country.getValue() != 'GB') {
        showAddressFields();
        searchLink.hide();
    } else {
        showSearchFields()
        hide(searchError);
    }

    // address manual entry
    insertManualAddressLink.click(function()
    {
        console.log(insertManualAddressLink);
        hide(searchResultsMsg);
        showAddressFields();
        return false;
    });
    // search address
    searchLink.click(function()
    {
        hide(searchResultsMsg);
        setText(resultItemsList, '');
        showSearchFields();
        return false;
    });

    // if country changes and is different from uk show the manual entry
    country.attachClientEventHandler("ValueChanged", function()
    {
        if (country.getValue() != 'GB') {
            showAddressFields();
            hide(searchLink);
        } else {
            showSearchFields();
            hide(searchError);
        }
    });

    // search address
    searchButton.attachClientEventHandler("OnButtonPressed", function()
    {
        hide(searchError);
        show(spinnerGif);

        setText(searchResultsMsg, '');
        removeClass(searchResultsMsg, alertClass);
        // if post Code is empty show an error message
        if (!postCodeSearch.viewNode.value) {
            hide(spinnerGif);
            setText(searchError, 'Insert a valid Post Code');
            show(searchError);
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
                        addClass(searchResultsMsg, alertClass);
                        setText(searchResultsMsg, searchResultsMsg.innerHTML +  "We found " + response.length + " results");
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
                        setText(resultItemsList, resultString);
                    }
                }  else {
                    addClass(searchResultsMsg, alertClass);
                    var errMsg = "Sorry, we couldn't find any addresses matching your search. Please verify the postcode, or enter the address manually.";
                    setText( searchResultsMsg, searchResultsMsg.innerHTML + errMsg );
                }
            });
        return false;
    });

    // show fields for search an address
    function showSearchFields()
    {
        show(manualAddressPar);
        hide(manualAddressElements);
        show(searchAddressElement);
        hide(searchLink);
    }

    // show address fields
    function showAddressFields()
    {
        show(manualAddressPar);
        show(manualAddressElements);
        hide(searchAddressElement);
        show(searchLink);
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
//    resultItemsList.on("click", "li.result-item", function()
//    {
//        var itemValues = $(this).find("span"),
//            addressObj = {};
//
//        itemValues.each(function()
//        {
//            var currEl = $(this);
//            addressObj[currEl.attr('class')] = currEl.text();
//        });
//        setAddressFields(addressObj);
//        showAddressFields();
//        return false;
//    });
};

window.rhubarb.viewBridgeClasses.AddressUkPafLookupViewBridge = bridge;