<?php

namespace App;

// This class checks string q from URL and then uses needed Action function
use App\DTO\Action;
use App\Http\HttpResponse;

class Router
{
    private ?string $q;
    private string $httpMethod;
    private array $postBody;

    public function __construct()
    {
        if (array_key_exists('q', $_GET)) {
            $this->q = $_GET['q'];
        } else {
            $this->q = null;
        }
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->postBody = $_REQUEST;
    }

    public function run(): void
    {
        if (is_null($this->q)) {
            $httpResponse = new HttpResponse(404, null, "Wrong URL");
            $httpResponse->sendJSON();
            exit();
        }

        // GET /currencies
        if ($this->httpMethod === "GET" && $this->q === "currencies") {
            $httpResponse = Action::showAllCurrencies();
            $httpResponse->sendJSON();
            exit();
        }
        // POST /currencies
        if ($this->httpMethod === "POST" && $this->q === "currencies") {
            // Checking for wrong Body
            if (array_key_exists("name", $this->postBody)
                && array_key_exists("code", $this->postBody)
                && array_key_exists("sign", $this->postBody)) {

                $fullName = $this->postBody["name"];
                $code = $this->postBody["code"];
                $sign = $this->postBody["sign"];
                if (strlen($code) != 3) {
                    $httpResponse = new HttpResponse(400, null, "Wrong body");
                    $httpResponse->sendJSON();
                    exit();
                }
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
            if (array_key_exists("baseCurrencyCode", $this->postBody)
                && array_key_exists("targetCurrencyCode", $this->postBody)
                && array_key_exists("rate", $this->postBody)) {
                $baseCurrencyCode = $this->postBody["baseCurrencyCode"];
                $targetCurrencyCode = $this->postBody["targetCurrencyCode"];
                $rate = $this->postBody["rate"];
                if (!is_numeric($rate)) {
                    $httpResponse = new HttpResponse(400, null, "Wrong body");
                    $httpResponse->sendJSON();
                    exit();
                }
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
        if (count($qArray) == 1) {
            $httpResponse = new HttpResponse(404, null, "Wrong URL");
            $httpResponse->sendJSON();
            exit();
        }
        // GET /currency/USD
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
        if ($this->httpMethod === "GET" && ($qArray[0] === "exchangeRate")) {
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
            $patchBody = file_get_contents('php://input');
            if (str_contains($patchBody, "rate=")) {
                $patchBody = explode("=", $patchBody);
                if (is_numeric($patchBody[1])) {
                    $newRate = (float)$patchBody[1];
                } else {
                    $httpResponse = new HttpResponse(400, null, "Wrong body");
                    $httpResponse->sendJSON();
                    exit();
                }
            } else {
                $httpResponse = new HttpResponse(400, null, "Wrong body");
                $httpResponse->sendJSON();
                exit();
            }
            $baseCurrencyCode = substr($codes, 0, 3);
            $targetCurrencyCode = substr($codes, 3, 3);

            $httpResponse = Action::patchExchangeRateByCodes($baseCurrencyCode, $targetCurrencyCode, $newRate);
            $httpResponse->sendJSON();
            exit();
        }

        $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
        $httpResponse->sendJSON();
    }
}