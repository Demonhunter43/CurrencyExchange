<?php

namespace App;
// This abstract class calls all need methods and check Database response and return HTTP response to router
use App\Database\DatabaseAction;
use App\Http\HttpResponse;
use App\Objects\Currency;
use App\Objects\Exchange;
use App\Objects\ExchangeRate;
use Exception;

class Controller
{

    public static function showAllCurrencies(): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
            $data = $databaseAction->getAllCurrencies();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        return new HttpResponse(200, $data);
    }

    public static function showCurrencyByCode(string $code, DatabaseAction $databaseAction = null): HttpResponse
    {
        if (is_null($databaseAction)) {
            try {
                $databaseAction = new DatabaseAction();
            } catch (Exception $e) {
                return new HttpResponse(500, null, $e->getMessage());
            }
        }

        try {
            $currency = $databaseAction->getCurrencyByCode($code);
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        return new HttpResponse(200, ["0" => ["0" => $currency]]);
    }

    public static function addCurrency(string $fullName, string $code, string $sign): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }

        try {
            $newCurrency = new Currency(null, $code, $fullName, $sign);
            $databaseAction->addCurrency($newCurrency);
        } catch (Exception $e) {
            return new HttpResponse(409, null, $e->getMessage());
        }
        $httpResponse = Controller::showCurrencyByCode($code, $databaseAction);
        $httpResponse->setCode(201);
        return $httpResponse;
    }

    public static function showAllExchangeRates(): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
            $arrayExchangeRates = $databaseAction->getAllExchangeRates();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        return new HttpResponse(200, $arrayExchangeRates);
    }

    public static function showExchangeRateByCodes(string $baseCurrencyCode, string $targetCurrencyCode): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        try {
            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        } catch (Exception $e) {
            return new HttpResponse(404, null, $e->getMessage());
        }
        return new HttpResponse(200, ["0" => $exchangeRate]);
    }

    public static function addExchangeRate(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        try {
            $baseCurrency = $databaseAction->getCurrencyByCode($baseCurrencyCode);
            $targetCurrency = $databaseAction->getCurrencyByCode($targetCurrencyCode);
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        $newExchangeRate = new ExchangeRate(null, $baseCurrency, $targetCurrency, $rate);
        try {
            $databaseAction->addExchangeRate($newExchangeRate);
        } catch (Exception $e) {
            return new HttpResponse(409, null, $e->getMessage());
        }
        try {
            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        return new HttpResponse(201, ["0" => $exchangeRate]);
    }

    public static function patchExchangeRateByCodes(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        try {
            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
            $exchangeRate->setRate($rate);
            $databaseAction->patchExchangeRate($exchangeRate);
        } catch (Exception $e) {
            return new HttpResponse(404, null, $e->getMessage());
        }
        return new HttpResponse(200, ["0" => $exchangeRate]);
    }

    public static function getExchange(string $baseCurrencyCode, string $targetCurrencyCode, float $amount): HttpResponse
    {
        try {
            $databaseAction = new DatabaseAction();
        } catch (Exception $e) {
            return new HttpResponse(500, null, $e->getMessage());
        }
        try {
            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, $targetCurrencyCode);
            $exchange = new Exchange($exchangeRate->getBaseCurrency(), $exchangeRate->getTargetCurrency(), $exchangeRate->getRate(), $amount);
            $exchange->convert();
            return new HttpResponse(200, ["0" => $exchange], null);
        } catch (Exception $e) {
        }

        try {
            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($targetCurrencyCode, $baseCurrencyCode);
            $exchange = new Exchange($exchangeRate->getBaseCurrency(), $exchangeRate->getTargetCurrency(), 1 / ($exchangeRate->getRate()), $amount);
            $exchange->convert();
            return new HttpResponse(200, ["0" => $exchange], null);
        } catch (Exception $e) {
        }
        try {
            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes($baseCurrencyCode, "USD");
            $baseCurrency = $exchangeRate->getBaseCurrency();
            $baseUSD_rate = $exchangeRate->getRate();

            $exchangeRate = $databaseAction->getExchangeRateByCurrenciesCodes("USD", $targetCurrencyCode);
            $targetCurrency = $exchangeRate->getBaseCurrency();
            $USD_targetRate = $exchangeRate->getRate();
            $exchange = new Exchange($baseCurrency, $targetCurrency, $baseUSD_rate * $USD_targetRate, $amount);
            $exchange->convert();
            return new HttpResponse(200, ["0" => $exchange], null);
        } catch (Exception $e) {
        }
        return new HttpResponse(404, null, "No exchange rates for this codes");
    }
}