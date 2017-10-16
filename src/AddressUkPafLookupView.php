<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Leaf\Controls\Common\Buttons\Button;
use Rhubarb\Leaf\Controls\Common\SelectionControls\DropDown\DropDown;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Leaf\Leaves\Controls\ControlView;
use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;

class AddressUkPafLookupView extends ControlView
{
    public $htmlType = "address";
    protected $requiresContainerDiv = true;
    protected $requiresStateInput = true;

    public function getDeploymentPackage()
    {
        return new LeafDeploymentPackage(
            __DIR__ . "/AddressUkPafLookupViewBridge.js",
            VENDOR_DIR."/components/jquery/jquery.min.js",
            VENDOR_DIR."/components/jqueryui/jquery-ui.min.js"
            );
    }

    protected function getViewBridgeName()
    {
        return "AddressUkPafLookupViewBridge";
    }

    protected function createSubLeaves()
    {
        $this->registerSubLeaf(
            $country = new DropDown("CountryCode"),
            $houseNumber = new TextBox("HouseNumber"),
            $postCodeSearch = new TextBox("PostCodeSearch"),
            $search = new Button("Search", "Search", function () {
            }),
            new TextBox("Line1"),
            new TextBox("Line2"),
            new TextBox("Town"),
            new TextBox("County"),
            new TextBox("Postcode")
        );

        $countriesList = [];
        foreach (Country::getCountriesList() as $key => $value) {
            $countriesList[] = [$key, $value];
        }
        $country->setSelectionItems([["", "Please select..."], $countriesList]);
        $postCodeSearch->setPlaceholderText("Postcode");
        $houseNumber->setPlaceholderText("No.");
    }

    public function printViewContent()
    {
        $this->layoutItemsWithContainer("", ["CountryCode"]);
        ?>
        <div class="search-fields">
            <div class="search-results">
                <span class="search-results-msg"></span>
                <ul class="search-results-items"></ul>
            </div>
            <?php
            $this->layoutItemsWithContainer("", [
                "Find Address" => "{HouseNumber}{PostCodeSearch}{Search}<span class='spinner'></span>"
            ]);
            ?>
            <span class="search-error">Insert a valid Post Code</span>
        </div>

        <p class="manual-address-par _help">Don't know the postcode? <a class="manual-address-link" href='#'>enter their
                address manually</a>.</p>
        <p class="search-address-link _help"><b><a href='#'>Search again</a></b></p>
        <div class="manual-fields">
            <?php
            $this->layoutItemsWithContainer("", [
                "Address Line 1" => "Line1",
                "Address Line 2" => "Line2",
                "Town",
                "County",
                "Postcode"
            ]);
            ?>
        </div>
        <?php
    }
}