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

    public static function showCurrencyByCode(string $code, DatabaseAction $databaseAction = null): void
    {
        if (is_null($databaseAction)) {
            $databaseAction = new DatabaseAction();
        }
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
            Action::showCurrencyByCode($code, $databaseAction);
        }
    }

    public static function showAllExchangeRates(): void
    {
        $databaseAction = new DatabaseAction();
        $data = $databaseAction->getAllExchangeRates();
        $arrayExchangeRates = DataToObjectTransformer::makeExchangeRatesArrayFromData($data);

        //This needed to initialize all currencies in currencyExchange Objects
        foreach ($arrayExchangeRates as $exchangeRate) {
            $data = $databaseAction->getCurrencyByID($exchangeRate->getBaseCurrencyId());
            $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $data = $databaseAction->getCurrencyByID($exchangeRate->getTargetCurrencyId());
            $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $exchangeRate->initializeCurrencies($baseCurrency, $targetCurrency);
        }
        echo json_encode($arrayExchangeRates);
    }

    public static function showExchangeRateByCodes(string $codes): void
    {
        $baseCurrencyCode = substr($codes, 0, 3);
        $targetCurrencyCode = substr($codes, 3, 3);

        $databaseAction = new DatabaseAction();
        $data = $databaseAction->getCurrencyByCode($baseCurrencyCode);
        $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

        $data = $databaseAction->getCurrencyByCode($targetCurrencyCode);
        $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

        $data = $databaseAction->getExchangeRateByCurrenciesID($baseCurrency->getId(), $targetCurrency->getId());

        $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($data);
        $exchangeRate->initializeCurrencies($baseCurrency, $targetCurrency);

        echo json_encode($exchangeRate);
    }
}