<?php


namespace Gcd\UkAddressLookup;


use Rhubarb\Leaf\Presenters\Controls\ControlPresenter;

class AddressUkPafLookup extends ControlPresenter
{
    private $defaultValues;
    protected $view;
    const pafServerUrl = "http://paf.gcdtech.com/paf-data.php?simple=1&api=2&output=json";

    public function __construct($name = "")
    {
        parent::__construct($name);
    }

    protected function createView()
    {
        $view = new AddressUkPafLookupView();
        return $view;
    }

    protected function configureView()
    {
        parent::configureView();
        $this->view->defaultValues = $this->defaultValues;

        $this->view->AttachEventHandler( "SearchPressed", function ( $houseNumber, $postCodeSearch ) {
            if(!isset($postCodeSearch)) {
                return json_decode([]);
            }
            $searchParams = [];
            $searchParams[ 'postcode' ] = urlencode( $postCodeSearch );
            if (isset( $houseNumber )) {
                $searchParams[ 'num' ] = urlencode( $houseNumber );
            }
            $pafSettings = new PafSettings();
            $requestUrl = implode( '&', [ self::pafServerUrl, "apikey=" . $pafSettings->ApiKey,
                http_build_query( $searchParams, '&' ) ] );

            $response = file_get_contents($requestUrl);
            return json_decode($response);
        } );
    }
}