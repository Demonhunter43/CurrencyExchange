<?php

namespace App;

// This class checks string q from URL and then uses needed Action function
class Router
{
    private string $q;
    private string $method;

    public function __construct(string $q, string $method)
    {
        $this->q = $q;
        $this->method = $method;
    }

    public function run(): void
    {

        if ($this->method == "GET" && $this->q === "currencies") {
            Action::showAllCurrencies();
            return;
        }
        $qArray = explode("/", $this->q);

        if ($this->method == "GET" && ($qArray[1] === "currency")) {
            Action::showCurrencyByCode($qArray[2]);
        }
    }
}