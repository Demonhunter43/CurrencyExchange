<?php

namespace App;
// This abstract class calls all need methods and send respond
use App\Database\Connection;
use App\Database\DatabaseAction;
use App\DTO\DataToObjectTransformer;
use App\Objects\Currency;
use App\Objects\ExchangeRate;

class Action
{
    public static function showAllCurrencies(): void
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isSuccess()) {
            $data = $databaseAction->getAllCurrencies();
            $arrayCurrencies = DataToObjectTransformer::makeCurrenciesArrayFromData($data);
            echo json_encode($arrayCurrencies);
        }
        //TODO Sent HHTPResponse with 500
    }

    public static function showCurrencyByCode(string $code, DatabaseAction $databaseAction = null): void
    {
        if (!is_null($databaseAction)) {
            $data = $databaseAction->getCurrencyByCode($code);
            $currency = DataToObjectTransformer::makeCurrencyFromData($data);
            echo json_encode($currency);
        }
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();

        if ($databaseResponse->isSuccess()) {
            $data = $databaseAction->getCurrencyByCode($code);
            $currency = DataToObjectTransformer::makeCurrencyFromData($data);
            echo json_encode($currency);
        }
        //TODO //TODO Sent HHTPResponse with some code
    }

    public static function addCurrency(array $postData): void
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isSuccess()) {
            $fullName = $postData["name"];
            $code = $postData["code"];
            $sign = $postData["sign"];
            $newCurrency = new Currency(null, $code, $fullName, $sign);

            if ($databaseAction->addCurrency($newCurrency)) {
                Action::showCurrencyByCode($code, $databaseAction);
            }
        }
        //TODO //TODO Sent HHTPResponse with some code
    }

    public static function showAllExchangeRates(): void
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isSuccess()) {
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
        //TODO //TODO Sent HHTPResponse with some code
    }

    public static function showExchangeRateByCodes(string $codes): void
    {
        $baseCurrencyCode = substr($codes, 0, 3);
        $targetCurrencyCode = substr($codes, 3, 3);

        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isSuccess()) {
            $data = $databaseAction->getCurrencyByCode($baseCurrencyCode);
            $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $data = $databaseAction->getCurrencyByCode($targetCurrencyCode);
            $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $data = $databaseAction->getExchangeRateByCurrenciesID($baseCurrency->getId(), $targetCurrency->getId());
            $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($data);

            $exchangeRate->initializeCurrencies($baseCurrency, $targetCurrency);

            echo json_encode($exchangeRate);
        }
        //TODO //TODO Sent HHTPResponse with some code
    }

    public static function addExchangeRate(array $postData): void
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isSuccess()) {
            $baseCurrencyCode = $postData["baseCurrencyCode"];
            $targetCurrencyCode = $postData["targetCurrencyCode"];
            $rate = $postData["rate"];

            $data = $databaseAction->getCurrencyByCode($baseCurrencyCode);
            $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $data = $databaseAction->getCurrencyByCode($targetCurrencyCode);
            $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

            $newExchangeRate = new ExchangeRate(null, $baseCurrency->getId(), $targetCurrency->getId(), $rate);

            if ($databaseAction->addExchangeRate($newExchangeRate)) {
                $data = $databaseAction->getExchangeRateByCurrenciesID($baseCurrency->getId(), $targetCurrency->getId());
                $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($data);

                $exchangeRate->initializeCurrencies($baseCurrency, $targetCurrency);
                echo json_encode($exchangeRate);
            }
        }
        //TODO //TODO Sent HHTPResponse with some code
    }
}