<?php

namespace App;
// This abstract class calls all need methods and send respond
use App\Database\Connection;
use App\Database\DatabaseAction;
use App\DTO\DataToObjectTransformer;
use App\Objects\Currency;

class Action
{
    public static function showAllCurrencies(): void
    {
        $databaseAction = new DatabaseAction();
        $data = $databaseAction->getAllCurrencies();
        $arrayCurrencies = DataToObjectTransformer::makeCurrenciesArrayFromData($data);
        echo json_encode($arrayCurrencies);
    }

    public static function showCurrencyByCode(string $code): void
    {
        $databaseAction = new DatabaseAction();
        $data = $databaseAction->getCurrencyByCode($code);
        $currency = DataToObjectTransformer::makeCurrencyFromData($data);
        echo json_encode($currency);
    }

    public static function addCurrency(array $postData): void
    {
        $databaseAction = new DatabaseAction();
        $fullName = $postData["name"];
        $code = $postData["code"];
        $sign = $postData["sign"];
        $newCurrency = new Currency(null, $code, $fullName, $sign);

        if ($databaseAction->addCurrency($newCurrency)) {
            $data = $databaseAction->getCurrencyByCode($code);
            $currency = DataToObjectTransformer::makeCurrencyFromData($data);
            echo json_encode($currency);
        }
    }

    public static function showAllExchangeRates(): void
    {
        $databaseAction = new DatabaseAction();
        $data = $databaseAction->getAllExchangeRates();
        $arrayExchangeRates = DataToObjectTransformer::makeExchangeRatesArrayFromData($data);
        foreach ($arrayExchangeRates as $exchangeRate) {

            $data = $databaseAction->getCurrencyByID($exchangeRate->getBaseCurrencyId());
            $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $data = $databaseAction->getCurrencyByID($exchangeRate->getTargetCurrencyId());
            $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $exchangeRate->initializeCurrencies($baseCurrency, $targetCurrency);
        }
        echo json_encode($arrayExchangeRates);
    }
}