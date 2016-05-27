<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Leaf\Leaves\Controls\CompositeControl;

class AddressUkPafLookup extends CompositeControl
{
    const pafServerUrl = "http://paf.gcdtech.com/paf-data.php?simple=1&api=2&output=json";

    protected function getViewClass()
    {
        return AddressUkPafLookupView::class;
    }

    protected function createModel()
    {
        return new AddressUkPafLookupModel();
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->searchPressedEvent->attachHandler(function($houseNumber, $postCodeSearch){

            if ( ! isset( $postCodeSearch )) {
                return json_decode([]);
            }

            $searchParams             = [];
            $searchParams['postcode'] = urlencode($postCodeSearch);

            $pafSettings            = PafSettings::singleton();
            $searchParams['apikey'] = $pafSettings->apiKey;

            if (isset( $houseNumber )) {
                $searchParams['num'] = urlencode($houseNumber);
            }
            $requestUrl = self::pafServerUrl . '&' . http_build_query($searchParams, '&');

            try {
                $response = file_get_contents($requestUrl);
                return json_decode($response);

            } catch (\Exception $e) {
                return null;
            }
        });
    }



    protected function createCompositeValue()
    {
        return [
            "Line1" => $this->model->Line1,
            "Line2" => $this->model->Line2,
            "Town" => $this->model->Town,
            "County" => $this->model->County,
            "Postcode" => $this->model->Postcode
        ];
    }

    /**
     * The place to parse the value property and break into the sub values for sub controls to bind to
     *
     * @param $compositeValue
     */
    protected function parseCompositeValue($compositeValue)
    {
        $props = ["Line1", "Line2", "Town", "County", "Postcode" ];

        foreach($props as $prop){
            if (isset($compositeValue[$prop])){
                $this->$prop = $compositeValue[$prop];
            }
        }
    }
}