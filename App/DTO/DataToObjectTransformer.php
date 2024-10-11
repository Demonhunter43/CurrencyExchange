<?php

namespace App\DTO;

use App\Objects\Currency;
use App\Objects\CurrencyExchange;

class DataToObjectTransformer
{
    static function makeCurrenciesArrayFromData($data):array
    {
        $i = 0;
        foreach ($data as $currency) {
            $arrayCurrencies[$i] = DataToObjectTransformer::makeCurrencyFromData($currency);
            $i++;
        }
        return $arrayCurrencies;
    }
    static function makeCurrencyFromData($data):Currency
    {
        $dataCurrency = $data[0];
        return new Currency($dataCurrency["ID"], $dataCurrency["Code"], $dataCurrency["FullName"], $dataCurrency["Sign"]);
    }
}