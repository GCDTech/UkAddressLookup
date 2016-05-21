<?php

namespace Gcd\UkAddressLookup;

class AddressUkPafLookup extends CompositeControl
{
    const pafServerUrl = "http://paf.gcdtech.com/paf-data.php?simple=1&api=2&output=json";

    protected function initialiseModel()
    {
        parent::initialiseModel();

        $this->model->Country = "GB";
    }

    protected function getViewClass()
    {
        return AddressUkPafLookupView::class;
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

    protected function extractBoundData()
    {
        return $this->model;
    }

}