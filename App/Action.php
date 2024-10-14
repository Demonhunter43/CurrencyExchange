<?php

namespace App;
// This abstract class calls all need methods and check Database response and return HTTP response to router
use App\Database\Connection;
use App\Database\DatabaseAction;
use App\DTO\DataToObjectTransformer;
use App\Http\HttpResponse;
use App\Objects\Currency;
use App\Objects\ExchangeRate;

class Action
{
    public static function showAllCurrencies(): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $data = $databaseAction->getAllCurrencies();
        $arrayCurrencies = DataToObjectTransformer::makeCurrenciesArrayFromData($data);
        return new HttpResponse(200, $arrayCurrencies);
    }

    public static function showCurrencyByCode(string $code, DatabaseAction $databaseAction = null): HttpResponse
    {
        if (!is_null($databaseAction)) {
            $data = $databaseAction->getCurrencyByCode($code);
            $currency = DataToObjectTransformer::makeCurrencyFromData($data);
            echo json_encode($currency);
        }

        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $data = $databaseAction->getCurrencyByCode($code);
        $currency = DataToObjectTransformer::makeCurrencyFromData($data);
        return new HttpResponse(200, ["0" => $currency]);
    }

    public static function addCurrency(array $postData): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $fullName = $postData["name"];
        $code = $postData["code"];
        $sign = $postData["sign"];
        $newCurrency = new Currency(null, $code, $fullName, $sign);

        if ($databaseAction->addCurrency($newCurrency)) {
            $httpResponse = Action::showCurrencyByCode($code, $databaseAction);
        }
        $httpResponse->setCode(201);
        return $httpResponse;
    }

    public static function showAllExchangeRates(): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
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
        return new HttpResponse(200, $arrayExchangeRates);
    }

    public static function showExchangeRateByCodes(string $codes): HttpResponse
    {
        $baseCurrencyCode = substr($codes, 0, 3);
        $targetCurrencyCode = substr($codes, 3, 3);

        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }

        $data = $databaseAction->getCurrencyByCode($baseCurrencyCode);
        $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

        $data = $databaseAction->getCurrencyByCode($targetCurrencyCode);
        $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

        $data = $databaseAction->getExchangeRateByCurrenciesID($baseCurrency->getId(), $targetCurrency->getId());
        $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($data);

        $exchangeRate->initializeCurrencies($baseCurrency, $targetCurrency);

        return new HttpResponse(200, ["0" => $exchangeRate]);
    }

    public static function addExchangeRate(array $postData): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
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
        }
        return new HttpResponse(201, ["0" => $exchangeRate]);
    }
}