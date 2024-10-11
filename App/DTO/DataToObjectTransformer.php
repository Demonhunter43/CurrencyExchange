<?php

namespace App\DTO;

use App\Objects\Currency;

class DataToObjectTransformer
{
    static function makeCurrenciesArrayFromData($data):array
    {
        $i = 0;
        foreach ($data as $currency) {
            $arrayCurrencies[$i] = new Currency($currency["ID"], $currency["Code"], $currency["FullName"], $currency["Sign"]);
            $i++;
        }
        return $arrayCurrencies;
    }
}