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
    static function makeCurrencyFromData($dataCurrency):Currency
    {
        return new Currency($dataCurrency["ID"], $dataCurrency["Code"], $dataCurrency["FullName"], $dataCurrency["Sign"]);
    }
    static function makeExchangeRatesArrayFromData($data):array
    {
        $i = 0;
        foreach ($data as $exchangeRate) {
            $arrayExchangeRates[$i] = DataToObjectTransformer::makeExchangeRateFromData($exchangeRate);
            $i++;
        }
        return $arrayExchangeRates;
    }
    static function makeExchangeRateFromData($dataExchangeRate):CurrencyExchange
    {
        return new CurrencyExchange($dataExchangeRate["ID"], $dataExchangeRate["BaseCurrencyId"], $dataExchangeRate["TargetCurrencyId"], $dataExchangeRate["Rate"]);
    }
}