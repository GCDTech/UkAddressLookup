<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Leaf\Presenters\Controls\Buttons\Button;
use Rhubarb\Leaf\Presenters\Controls\ControlView;
use Rhubarb\Leaf\Presenters\Controls\Selection\DropDown\DropDown;
use Rhubarb\Leaf\Presenters\Controls\Text\TextBox\TextBox;

class AddressUkPafLookupView extends ControlView
{
    protected $htmlType = "address";

    public function __construct($htmlType = "address")
    {
        $this->htmlType = $htmlType;

        $this->requiresContainer   = true;
        $this->requiresStateInputs = true;
    }

    public function getDeploymentPackage()
    {
        $package                      = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . "/AddressUkPafLookupViewBridge.js";

        return $package;
    }

    protected function getClientSideViewBridgeName()
    {
        return "AddressUkPafLookupViewBridge";
    }

    public function createPresenters()
    {
        $this->AddPresenters(
            $country = new DropDown("Country"),
            $houseNumber = new TextBox("HouseNumber", 10),
            $postCodeSearch = new TextBox("PostCodeSearch", 15),
            $search = new Button("Search", "Search", function () {
            }),
            new TextBox("Line1", 50),
            new TextBox("Line2", 30),
            new TextBox("Town", 30),
            new TextBox("County", 20),
            new TextBox("PostCode", 10)
        );

        $countriesList = [];
        foreach (Country::getCountriesList() as $key => $value) {
            $countriesList[] = [$key, $value];
        }
        $country->SetSelectionItems([["", "Please select..."], $countriesList]);
        $postCodeSearch->setPlaceholderText("Postcode");
        $houseNumber->setPlaceholderText("No.");
    }

    public function printViewContent()
    {
        $this->printFieldset("", ["Country"]);
        ?>
        <div id="search-fields">
            <div id="search-results">
                <span id="search-results-msg"></span>
                <ul id="search-results-items"></ul>
            </div>
            <?php
            $this->printFieldset("", [
                "Find Address" => "{HouseNumber}{PostCodeSearch}{Search}<span id='spinner' class='spinner'></span>"
            ]);
            ?>
            <span id="search-error"></span>
        </div>

        <p id="manual-address-par" class="_help">Don't know the postcode? <a id="manual-address-link" href='#'>enter their address manually</a>.</p>
        <p id="search-address-par" class="_help"><b><a id="search-address-link" href='#'>Search again</a></b></p>
        <div id="manual-fields">
            <?php
            $this->printFieldset("", [
                "Address Line 1" => "Line1",
                "Address Line 2" => "Line2",
                "Town",
                "County",
                "PostCode"
            ]);
            ?>
        </div>
        <?php
    }
}