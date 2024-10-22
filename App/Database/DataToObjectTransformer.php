<?php

namespace App\Database;

use App\Objects\Currency;
use App\Objects\ExchangeRate;

class DataToObjectTransformer
{
    static function makeObjectsArray($data): array
    {
        $i = 0;
        foreach ($data as $objectData) {
            $array[$i] = DataToObjectTransformer::makeObject($objectData);
            $i++;
        }
        return $array;
    }

    static function makeObject(array $dataObject): Currency|ExchangeRate
    {
        if (array_key_exists("BaseCurrencyID", $dataObject)) {
            $baseCurrency = new Currency($dataObject["BaseCurrencyID"], $dataObject["BaseCurrencyCode"], $dataObject["BaseCurrencyFullName"], $dataObject["BaseCurrencySign"]);
            $targetCurrency = new Currency($dataObject["TargetCurrencyID"], $dataObject["TargetCurrencyCode"], $dataObject["TargetCurrencyFullName"], $dataObject["TargetCurrencySign"]);
            return new ExchangeRate($dataObject["ID"], $baseCurrency, $targetCurrency, $dataObject["Rate"]);
        } else {
            return new Currency($dataObject["ID"], $dataObject["Code"], $dataObject["FullName"], $dataObject["Sign"]);
        }
    }
}