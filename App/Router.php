<?php

namespace App;

// This class checks string q from URL and then uses needed Action function
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
            $httpResponse = Action::addCurrency($this->httpRequest);
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
            $httpResponse = Action::addExchangeRate($this->httpRequest);
            $httpResponse->sendJSON();
            exit();
        }


        // With     sign / in URL
        $qArray = explode("/", $this->q);
        // GET /currency/EUR
        if ($this->httpMethod === "GET" && ($qArray[0] === "currency")) {
            $httpResponse = Action::showCurrencyByCode($qArray[1]);
            $httpResponse->sendJSON();
            exit();
        }

        if ($this->httpMethod === "GET" && ($qArray[0] === "exchangeRates")) {
            $httpResponse = Action::showExchangeRateByCodes($qArray[1]);
            $httpResponse->sendJSON();
            exit();
        }
    }
}