<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Leaf\Presenters\Controls\CompositeControlPresenter;
use Rhubarb\Stem\Models\Model;

/**
 * @property AddressUkPafLookupView $view
 * @property string $Country
 * @property bool $IncludeCountry
 * @property bool $UseDropDownForResults
 * @property bool $ResultDropDownSize
 * @property string ManualAddressEntryText
 */
class AddressUkPafLookup extends CompositeControlPresenter
{
    public static $addressFields = ['Organisation', 'AddressLine1', 'AddressLine2', 'AddressLine3', 'Town', 'County', 'Postcode'];

    protected function initialiseModel()
    {
        parent::initialiseModel();

        $this->model->Country = 'GB';
        $this->model->IncludeCountry = true;
        $this->model->UseDropDownForResults = false;
        $this->model->ManualAddressEntryText = 'Enter address manually';
    }

    protected function getPublicModelPropertyList()
    {
        $properties = parent::getPublicModelPropertyList();
        $properties[] = 'IncludeCountry';
        $properties[] = 'UseDropDownForResults';
        return $properties;
    }

    protected function createView()
    {
        return new AddressUkPafLookupView();
    }

    public function applyToModel(Model $model)
    {
        foreach (self::$addressFields as $field) {
            $model->$field = $this->model->$field;
        }
    }

    protected function getData($dataKey, $viewIndex = false)
    {
        if ($dataKey == 'AddressPopulated') {
            return $this->isAddressPopulated();
        }

        return parent::getData($dataKey, $viewIndex);
    }

    /**
     * @return bool
     */
    public function isAddressPopulated()
    {
        foreach (self::$addressFields as $field) {
            if ($this->getData($field) != "") {
                return true;
            }
        }
        return false;
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->AttachEventHandler('Search', function () {
            $houseNumber = $this->model->HouseNumber;
            $postcode = $this->model->PostcodeSearch;

            if (trim($postcode) == '') {
                throw new PafException('No postcode', 'Please enter a postcode');
            }

            $pafSettings = new PafSettings();
            $searchParams['apikey'] = $pafSettings->ApiKey;
            $searchParams['postcode'] = $postcode;

            if (trim($houseNumber) != '') {
                $searchParams['num'] = $houseNumber;
            }

            $requestUrl = $pafSettings->PafRequestUrl . '&' . http_build_query($searchParams, '&');

            $response = @file_get_contents($requestUrl);

            if ($response === false) {
                throw new PafException('Request failed');
            }

            $response = json_decode($response, true);

            if ($response === false && json_last_error()) {
                throw new PafException('Invalid response: ' . json_last_error_msg());
            }

            usort($response, function ($a, $b) {
                return strnatcmp($a['AddressLine1'], $b['AddressLine1']);
            });

            return $response;
        });
    }

    protected function extractBoundData()
    {
        return $this->model;
    }
}
