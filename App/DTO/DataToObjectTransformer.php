<?php

namespace App\DTO;

use App\Objects\Currency;
use App\Objects\ExchangeRate;

class DataToObjectTransformer
{
    static function makeCurrenciesArrayFromData($data): array
    {
        $i = 0;
        foreach ($data as $currency) {
            $arrayCurrencies[$i] = DataToObjectTransformer::makeCurrencyFromData($currency);
            $i++;
        }
        return $arrayCurrencies;
    }

    static function makeCurrencyFromData($dataCurrency): Currency
    {
        return new Currency($dataCurrency["ID"], $dataCurrency["Code"], $dataCurrency["FullName"], $dataCurrency["Sign"]);
    }

    static function makeExchangeRatesArrayFromData($data): array
    {
        $i = 0;
        foreach ($data as $dataExchangeRate) {
            $arrayExchangeRates[$i] = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
            $i++;
        }
        return $arrayExchangeRates;
    }

    static function makeExchangeRateFromData(array $dataExchangeRate): ExchangeRate
    {
        $baseCurrency = new Currency($dataExchangeRate["BaseCurrencyID"],$dataExchangeRate["BaseCurrencyCode"],$dataExchangeRate["BaseCurrencyFullName"],$dataExchangeRate["BaseCurrencySign"]); //TODO Currencies constructor, Exchange constructor и переделать везде, где есть этотметод
        $targetCurrency = new Currency($dataExchangeRate["TargetCurrencyID"],$dataExchangeRate["TargetCurrencyCode"],$dataExchangeRate["TargetCurrencyFullName"],$dataExchangeRate["TargetCurrencySign"]);
        return new ExchangeRate($dataExchangeRate["ID"],$baseCurrency, $targetCurrency, $dataExchangeRate["Rate"]);
    }
}