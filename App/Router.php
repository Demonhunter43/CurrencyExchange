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

        if ($this->method === "GET" && $this->q === "currencies") {
            Action::showAllCurrencies();
        }

        if ($this->method === "GET" && ($this->q === "currencies")) {
            Action::showCurrencyByCode();
        }

        if ($this->method === "POST") {
            switch ($q) {
                case ()
            }
        }

        if ($this->method === "PATCH") {
            switch ($q) {
                case ()
            }
        }
    }
}