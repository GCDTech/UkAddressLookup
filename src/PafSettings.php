<?php

namespace Gcd\UkAddressLookup;

use Rhubarb\Crown\Settings;

/**
 * Common settings needed for modelling.
 *
 * @property string $ApiKey
 * @property string $PafRequestUrl
 */
class PafSettings extends Settings
{
    protected function initialiseDefaultValues()
    {
        $this->PafRequestUrl = 'http://paf.gcdtech.com/paf-data.php?simple=1&api=2&output=json';
    }
}
