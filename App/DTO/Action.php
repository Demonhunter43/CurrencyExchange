<?php

namespace App\DTO;
// This abstract class calls all need methods and check Database response and return HTTP response to router
use App\Database\DatabaseAction;
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
            return new HttpResponse(200, ["0" => $currency]);
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

    public static function addCurrency(string $fullName, string $code, string $sign): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
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
        return new HttpResponse(200, $arrayExchangeRates);
    }

    public static function showExchangeRateByCodes(string $baseCurrencyCode, string $targetCurrencyCode): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $dataExchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
        return new HttpResponse(200, ["0" => $exchangeRate]);
    }

    public static function addExchangeRate(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }

        $data = $databaseAction->getCurrencyByCode($baseCurrencyCode);
        $baseCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

        $data = $databaseAction->getCurrencyByCode($targetCurrencyCode);
        $targetCurrency = DataToObjectTransformer::makeCurrencyFromData($data);

        $newExchangeRate = new ExchangeRate(null, $baseCurrency, $targetCurrency, $rate);

        if ($databaseAction->addExchangeRate($newExchangeRate)) {
            $dataExchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
            $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
        }
        return new HttpResponse(201, ["0" => $exchangeRate]);
    }

    public static function patchExchangeRateByCodes(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): HttpResponse
    {
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $dataExchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
        $exchangeRate->setRate($rate);
        $databaseResponse = $databaseAction->patchExchangeRate($exchangeRate);
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse(500, null, "Something went wrong when UPDATE");
        }
        return new HttpResponse(200, ["0" => $exchangeRate]);
    }

}