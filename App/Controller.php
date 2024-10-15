<?php

namespace App;
// This abstract class calls all need methods and check Database response and return HTTP response to router
use App\Database\DatabaseAction;
use App\Database\DataToObjectTransformer;
use App\Http\HttpResponse;
use App\Objects\Currency;
use App\Objects\Exchange;
use App\Objects\ExchangeRate;

class Controller
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
            $httpResponse = Controller::showCurrencyByCode($code, $databaseAction);
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
        $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $dataExchangeRate = $databaseResponse->getData();
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
            $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
            if ($databaseResponse->isNotSuccess()) {
                return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
            }
            $dataExchangeRate = $databaseResponse->getData();
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
        $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }
        $dataExchangeRate = $databaseResponse->getData();
        $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
        $exchangeRate->setRate($rate);
        $databaseResponse = $databaseAction->patchExchangeRate($exchangeRate);
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse(500, null, "Something went wrong when UPDATE");
        }
        return new HttpResponse(200, ["0" => $exchangeRate]);
    }

    public static function getExchange(string $baseCurrencyCode, string $targetCurrencyCode, float $amount): HttpResponse
    {
        $exchange = null;
        $databaseAction = new DatabaseAction();
        $databaseResponse = $databaseAction->connect();
        if ($databaseResponse->isNotSuccess()) {
            return new HttpResponse($databaseResponse->getCode(), null, $databaseResponse->getErrorMessage());
        }

        $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        if ($databaseResponse->isSuccess()) {
            $dataExchangeRate = $databaseResponse->getData();
            $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
            $exchange = new Exchange($exchangeRate->getBaseCurrency(), $exchangeRate->getTargetCurrency(), $exchangeRate->getRate(), $amount);
        } else {

            $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes($targetCurrencyCode, $baseCurrencyCode);
            if ($databaseResponse->isSuccess()) {
                $dataExchangeRate = $databaseResponse->getData();
                $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
                $exchange = new Exchange($exchangeRate->getTargetCurrency(), $exchangeRate->getBaseCurrency(), 1 / ($exchangeRate->getRate()), $amount);
            } else {

                $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, "USD");
                if ($databaseResponse->isSuccess()) {
                    $dataExchangeRate = $databaseResponse->getData();
                    $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
                    $baseCurrency = $exchangeRate->getBaseCurrency();
                    $baseUSD_rate = $exchangeRate->getRate();
                    $databaseResponse = $databaseAction->getExchangeRateByCurrenciesCodes("USD", $targetCurrencyCode);
                    if ($databaseResponse->isSuccess()) {
                        $dataExchangeRate = $databaseResponse->getData();
                        $exchangeRate = DataToObjectTransformer::makeExchangeRateFromData($dataExchangeRate);
                        $targetCurrency = $exchangeRate->getTargetCurrency();
                        $USD_targetRate = $exchangeRate->getRate();
                        $exchange = new Exchange($baseCurrency, $targetCurrency, $baseUSD_rate * $USD_targetRate, $amount);
                    }
                }
            }
        }
        if (is_null($exchange)) {
            return new HttpResponse(404, null, "No exchange rates for this codes");
        }
        $exchange->convert();
        return new HttpResponse(200, ["0" => $exchange], null);
    }
}