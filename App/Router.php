<?php

namespace App;

// This class checks string q from URL and then uses needed Action function
use App\DTO\Action;
use App\Http\HttpResponse;

class Router
{
    private string $q;
    private string $httpMethod;
    private array $httpRequest;

    public function __construct()
    {
        $this->q = $_GET['q'];
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->httpRequest = $_REQUEST;
    }

    public function run(): void
    {

        // GET /currencies
        if ($this->httpMethod === "GET" && $this->q === "currencies") {
            $httpResponse = Action::showAllCurrencies();
            $httpResponse->sendJSON();
            exit();
        }
        // POST /currencies
        if ($this->httpMethod === "POST" && $this->q === "currencies") {
            // Checking for wrong Body
            if (array_key_exists("name", $this->httpRequest)
                && array_key_exists("code", $this->httpRequest)
                && array_key_exists("sign", $this->httpRequest)) {

                $fullName = $this->httpRequest["name"];
                $code = $this->httpRequest["code"];
                $sign = $this->httpRequest["sign"];
            } else {
                $httpResponse = new HttpResponse(400, null, "Wrong body");
                $httpResponse->sendJSON();
                exit();
            }

            $httpResponse = Action::addCurrency($fullName, $code, $sign);
            $httpResponse->sendJSON();
            exit();
        }
        //GET /exchangeRates
        if ($this->httpMethod === "GET" && $this->q === "exchangeRates") {
            $httpResponse = Action::showAllExchangeRates();
            $httpResponse->sendJSON();
            exit();
        }
        //POST /exchangeRates
        if ($this->httpMethod === "POST" && $this->q === "exchangeRates") {
            // Checking for wrong Body
            if (array_key_exists("baseCurrencyCode", $this->httpRequest)
                && array_key_exists("targetCurrencyCode", $this->httpRequest)
                && array_key_exists("rate", $this->httpRequest)) {

                $baseCurrencyCode = $this->httpRequest["baseCurrencyCode"];
                $targetCurrencyCode = $this->httpRequest["targetCurrencyCode"];
                $rate = $this->httpRequest["rate"];
            } else {
                $httpResponse = new HttpResponse(400, null, "Wrong body");
                $httpResponse->sendJSON();
                exit();
            }

            $httpResponse = Action::addExchangeRate($baseCurrencyCode, $targetCurrencyCode, $rate);
            $httpResponse->sendJSON();
            exit();
        }


        // With     sign / in URL
        $qArray = explode("/", $this->q);

        // GET /currency/EUR
        if ($this->httpMethod === "GET" && ($qArray[0] === "currency")) {
            // Checking for wrong URL
            $code = $qArray[1];
            if (strlen($code) != 3) {
                $httpResponse = new HttpResponse(404, null, "Wrong URL");
                $httpResponse->sendJSON();
                exit();
            }
            $httpResponse = Action::showCurrencyByCode($code);
            $httpResponse->sendJSON();
            exit();
        }

        // GET /exchangeRate/USDRUB
        if ($this->httpMethod === "GET" && ($qArray[0] === "exchangeRates")) {
            // Checking for wrong URL
            $codes = $qArray[1];
            if (strlen($codes) != 6) {
                $httpResponse = new HttpResponse(404, null, "Wrong URL");
                $httpResponse->sendJSON();
                exit();
            }

            $baseCurrencyCode = substr($codes, 0, 3);
            $targetCurrencyCode = substr($codes, 3, 3);

            $httpResponse = Action::showExchangeRateByCodes($baseCurrencyCode, $targetCurrencyCode);
            $httpResponse->sendJSON();
            exit();
        }
        // PATCH /exchangeRate/USDRUB
        if ($this->httpMethod === "PATCH" && ($qArray[0] === "exchangeRate")) {
            // Checking for wrong URL
            $codes = $qArray[1];
            if (strlen($codes) != 6) {
                $httpResponse = new HttpResponse(404, null, "Wrong URL");
                $httpResponse->sendJSON();
                exit();
            }
            // Checking for wrong Body
            var_dump($this->httpRequest["rate"]); //TODO Постман вместе с Патчем не отправляет Боди((
            exit();
            /*if (array_key_exists("rate", $this->httpRequest)) {
                $rate = $this->httpRequest["rate"];
            } else {
                $httpResponse = new HttpResponse(400, null, "Wrong body");
                $httpResponse->sendJSON();
                exit();
            }

            $baseCurrencyCode = substr($codes, 0, 3);
            $targetCurrencyCode = substr($codes, 3, 3);

            $httpResponse = Action::patchExchangeRateByCodes($baseCurrencyCode, $targetCurrencyCode, $rate);
            $httpResponse->sendJSON();
            exit();*/
        }

        $httpResponse = new HttpResponse(404, null, "Wrong URL");
        $httpResponse->sendJSON();
    }
}