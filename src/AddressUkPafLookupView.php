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
        <div class="search-fields">
            <div class="search-results">
                <span class="search-results-msg"></span>
                <ul class="search-results-items"></ul>
            </div>
            <?php
            $this->printFieldset("", [
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