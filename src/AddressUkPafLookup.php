<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Leaf\Presenters\Controls\CompositeControlPresenter;

class AddressUkPafLookup extends CompositeControlPresenter
{
    const pafServerUrl = "http://paf.gcdtech.com/paf-data.php?simple=1&api=2&output=json";

    public function __construct($name = "")
    {
        parent::__construct($name);
    }

    protected function initialiseModel()
    {
        parent::initialiseModel();

        $this->model->Country = "GB";
    }

    protected function createView()
    {
        return new AddressUkPafLookupView();
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->AttachEventHandler("SearchPressed", function ($houseNumber, $postCodeSearch) {
            if ( ! isset( $postCodeSearch )) {
                return json_decode([]);
            }
            $searchParams             = [];
            $searchParams['postcode'] = urlencode($postCodeSearch);

            $pafSettings            = new PafSettings();
            $searchParams['apikey'] = $pafSettings->ApiKey;

            if (isset( $houseNumber )) {
                $searchParams['num'] = urlencode($houseNumber);
            }
            $requestUrl = self::pafServerUrl . '&' . http_build_query($searchParams, '&');

            $response = file_get_contents($requestUrl);

            return json_decode($response);
        });
    }

    protected function extractBoundData()
    {
        return $this->model;
    }

}