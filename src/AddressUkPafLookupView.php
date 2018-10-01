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

    /** @var AddressUkPafLookupModel $model */
    protected $model;

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
            $country = new DropDown("Country"),
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

        $country->addCssClassNames();
    }

    public function printViewContent()
    {
        ?>

        <label class="c-label">Country <span class="u-negative">*</span></label>
        <div class="c-select-box u-marg-bottom">
            <?php
            $this->leaves['Country']->addCssClassNames('c-text-input');
            print $this->leaves['Country'];
            ?>
        </div>

        <div class="search-fields u-pos-rel">
            <div class="search-results c-module c-dropdown c-dropdown--paf u-bordered u-fill-white u-shadow-light">

                <ul class="search-results-items u-micro"></ul>
            </div>

            <?php

            print '<label class="c-label">Address Lookup <span class="u-negative">*</span></label>';

            ?>

            <div class="u-pos-rel">
                <div class="o-flex o-flex--align-middle o-flex--flex-start">
                    <?php
                    $this->leaves['HouseNumber']->addCssClassNames('c-text-input c-text-input--very-short u-marg-right-half');
                    print $this->leaves['HouseNumber'];
                    ?>
                    <div class="o-flex o-flex--align-middle o-flex__item o-flex--flex-end dont-grow">
                        <?php
                        $this->leaves['PostCodeSearch']->addCssClassNames('c-text-input c-text-input--short');
                        print $this->leaves['PostCodeSearch'];

                        $this->leaves['Search']->addCssClassNames('c-button c-text-input-button');
                        print $this->leaves['Search'];
                        ?>
                        <span class='spinner'></span>
                    </div>
                </div>
            </div>

            <span class="js-validation-message search-error u-marg-bottom"><p>Insert a valid Post Code</p></span>

        </div>

        <p class="manual-address-par _help u-marg-top">Don't know the postcode? <a class="manual-address-link" href='#'>enter their address manually</a>.</p>

        <div class="manual-fields">
            <?php

            $countyLabel = "County";
            $postcodeLabel = "Postcode";
            if ($this->model->Country == "US") {
                $countyLabel = "State";
                $postcodeLabel = "Zip Code";
            }

            print '<label class="c-label">Address Line 1 <span class="u-negative">*</span></label>';
            $this->leaves['Line1']->addCssClassNames('c-text-input c-text-input--reg');
            print $this->leaves['Line1'];

            print '<label class="c-label">Address Line 2 <span class="u-negative">*</span></label>';
            $this->leaves['Line2']->addCssClassNames('c-text-input c-text-input--reg');
            print $this->leaves['Line2'];

            print '<label class="c-label">Town <span class="u-negative">*</span></label>';
            $this->leaves['Town']->addCssClassNames('c-text-input c-text-input--reg');
            print $this->leaves['Town'];

            print '<label class="c-label"> '.$countyLabel.' <span class="u-negative">*</span></label>';
            $this->leaves['County']->addCssClassNames('c-text-input c-text-input--reg');
            print $this->leaves['County'];

            print '<label class="c-label">'.$postcodeLabel.' <span class="u-negative">*</span></label>';
            $this->leaves['Postcode']->addCssClassNames('c-text-input c-text-input--short');
            print $this->leaves['Postcode'];

            ?>
        </div>

        <p class="search-address-link _help u-marg-top"><b><a href='#'>Search again</a></b></p>
        <?php
    }
}
