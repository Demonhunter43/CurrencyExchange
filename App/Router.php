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
            Action::showAllCurrencies();
            exit();
        }
        // POST /currencies
        if ($this->httpMethod === "POST" && $this->q === "currencies") {
            Action::addCurrency($this->httpRequest);
            exit();
        }
        //GET /exchangeRates
        if ($this->httpMethod === "GET" && $this->q === "exchangeRates") {
            Action::showAllExchangeRates();
            exit();
        }


        // With     sign / in URL
        $qArray = explode("/", $this->q);
        // GET /currency/EUR
        if ($this->httpMethod === "GET" && ($qArray[0] === "currency")) {
            Action::showCurrencyByCode($qArray[1]);
            exit();
        }

        if ($this->httpMethod === "GET" && ($qArray[0] === "exchangeRates")) {
            Action::showExchangeRateByCodes($qArray[1]);
            exit();
        }

    }
}