<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\Controls\CompositeControlModel;

class AddressUkPafLookupModel extends CompositeControlModel
{
    public $Country = "GB";

    public $searchPressedEvent;

    public $HouseNumber = "";

    public $Postcode = "";

    public $Line1 = "";
    public $Line2 = "";
    public $Town = "";
    public $CountyCode = "";


    public function __construct()
    {
        parent::__construct();

        $this->searchPressedEvent = new Event();
    }
}