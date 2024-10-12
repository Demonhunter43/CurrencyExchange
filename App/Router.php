<?php

namespace App;

// This class checks string q from URL and then uses needed Action function
class Router
{
    private string $q;
    private string $method;

    public function __construct()
    {
        $this->q = $_GET['q'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function run(): void
    {
        // GET /currencies
        if ($this->method === "GET" && $this->q === "currencies") {
            Action::showAllCurrencies();
            exit();
        }
        // POST /currencies
        if ($this->method === "POST" && $this->q === "currencies") {
            Action::addCurrency($_REQUEST);
            exit();
        }
        //GET /exchangeRates
        if ($this->method === "GET" && $this->q === "exchangeRates") {
            Action::showAllExchangeRates();
            exit();
        }

        // With     sign / in URL

        $qArray = explode("/", $this->q);

        // GET /currency/EUR
        if ($this->method === "GET" && ($qArray[0] === "currency")) {
            Action::showCurrencyByCode($qArray[1]);
            exit();
        }




        if ($this->method === "POST" && ($qArray[0] === "currencies")) {
            Action::showCurrencyByCode($qArray[1]);
            exit();
        }

    }
}