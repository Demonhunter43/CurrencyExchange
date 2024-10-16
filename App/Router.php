<?php

namespace App;


use App\Http\HttpResponse;

define("POST_CUR", "POST/currencies");
define("POST_RATE", "POST/exchangeRates");
define("GET_EXCHANGE", "GET/exchange");
define("GET_CUR", "GET/currency");
define("GET_RATE", "GET/exchangeRate");
define("PATCH_RATE", "PATCH/exchangeRate");

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

    public function checkAndGetInput(string $flag): array
    {
        switch ($flag) {
            case POST_CUR:
                if (array_key_exists("name", $this->postBody)
                    && array_key_exists("code", $this->postBody)
                    && array_key_exists("sign", $this->postBody)) {

                    $fullName = $this->postBody["name"];
                    $code = $this->postBody["code"];
                    $sign = $this->postBody["sign"];
                    if (is_numeric($code) || is_numeric($fullName) || is_numeric($sign)) {
                        $httpResponse = new HttpResponse(400, null, "Wrong body");
                        $httpResponse->sendJSON();
                        exit();
                    }
                } else {
                    $httpResponse = new HttpResponse(400, null, "Wrong body");
                    $httpResponse->sendJSON();
                    exit();
                }
                $input = [
                    "fullName" => $fullName,
                    "code" => $code,
                    "sign" => $sign
                ];
                break;
            case POST_RATE:
                if (array_key_exists("baseCurrencyCode", $this->postBody)
                    && array_key_exists("targetCurrencyCode", $this->postBody)
                    && array_key_exists("rate", $this->postBody)) {
                    $baseCurrencyCode = $this->postBody["baseCurrencyCode"];
                    $targetCurrencyCode = $this->postBody["targetCurrencyCode"];
                    $rate = $this->postBody["rate"];
                    if (!is_numeric($rate) || is_numeric($baseCurrencyCode) || is_numeric($targetCurrencyCode)) {
                        $httpResponse = new HttpResponse(400, null, "Wrong body");
                        $httpResponse->sendJSON();
                        exit();
                    }
                } else {
                    $httpResponse = new HttpResponse(400, null, "Wrong body");
                    $httpResponse->sendJSON();
                    exit();
                }
                $input = [
                    "bodyCurrencyCode" => $baseCurrencyCode,
                    "targetCurrencyCode" => $targetCurrencyCode,
                    "rate" => $rate
                ];
                break;
            case GET_EXCHANGE: // To convert from code to code AMOUNT of money
                $parsedUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                parse_str($parsedUrl, $query_parts);
                // Checking URL
                if (array_key_exists("from", $query_parts) || array_key_exists("to", $query_parts) || array_key_exists("amount", $query_parts)) {
                    $from = $query_parts["from"];
                    $to = $query_parts["to"];
                    $amount = $query_parts["amount"];
                } else {
                    $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
                    $httpResponse->sendJSON();
                    exit();
                }
                // Check parameters
                if (is_numeric($from) || is_numeric($to) || !is_numeric($amount)) {
                    $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
                    $httpResponse->sendJSON();
                    exit();
                }
                $input = [
                    "baseCurrencyCode" => $from,
                    "targetCurrencyCode" => $to,
                    "amount" => $amount
                ];
                break;
            case GET_CUR:
                $code = explode("/", $this->q)[1];
                if (strlen($code) != 3) {
                    $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
                    $httpResponse->sendJSON();
                    exit();
                }
                $input = [
                    "code" => $code
                ];
                break;
            case GET_RATE:
                // Checking for Wrong URL or method
                $codes = explode("/", $this->q)[1];
                if (strlen($codes) != 6) {
                    $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
                    $httpResponse->sendJSON();
                    exit();
                }
                $baseCurrencyCode = substr($codes, 0, 3);
                $targetCurrencyCode = substr($codes, 3, 3);
                $input = [
                    "baseCurrencyCode" => $baseCurrencyCode,
                    "targetCurrencyCode" => $targetCurrencyCode,
                ];
                break;
            case PATCH_RATE:
                // Checking for Wrong URL or method
                $codes = explode("/", $this->q)[1];
                if (strlen($codes) != 6) {
                    $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
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
                $input = [
                    "baseCurrencyCode" => $baseCurrencyCode,
                    "targetCurrencyCode" => $targetCurrencyCode,
                    "newRate" => $newRate
                ];
        }
        return $input;
    }

    public function run(): void
    {
        if (is_null($this->q)) {
            $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
            $httpResponse->sendJSON();
            exit();
        }

        // GET /currencies
        if ($this->httpMethod === "GET" && $this->q === "currencies") {
            $httpResponse = Controller::showAllCurrencies();
            $httpResponse->sendJSON();
            exit();
        }
        // POST /currencies
        if ($this->httpMethod === "POST" && $this->q === "currencies") {
            $input = $this->checkAndGetInput(POST_CUR);
            $httpResponse = Controller::addCurrency($input["fullName"], $input["code"], $input["sign"]);
            $httpResponse->sendJSON();
            exit();
        }
        //GET /exchangeRates
        if ($this->httpMethod === "GET" && $this->q === "exchangeRates") {
            $httpResponse = Controller::showAllExchangeRates();
            $httpResponse->sendJSON();
            exit();
        }
        //POST /exchangeRates
        if ($this->httpMethod === "POST" && $this->q === "exchangeRates") {
            $input = $this->checkAndGetInput(POST_RATE);
            $httpResponse = Controller::addExchangeRate($input["baseCurrencyCode"], $input["targetCurrencyCode"], $input["rate"]);
            $httpResponse->sendJSON();
            exit();
        }
        // GET /exchange?from=BASE_CURRENCY_CODE&to=TARGET_CURRENCY_CODE&amount=$AMOUNT #
        if ($this->httpMethod === "GET" && $this->q == "exchange") {
            $input = $this->checkAndGetInput(GET_EXCHANGE);
            $httpResponse = Controller::getExchange($input["baseCurrencyCode"], $input["targetCurrencyCode"], $input["amount"]);
            $httpResponse->sendJSON();
            exit();
        }

        // With     sign / in URL
        $qArray = explode("/", $this->q);
        if (count($qArray) == 1) {
            $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
            $httpResponse->sendJSON();
            exit();
        }
        $qWord = $qArray[0];

        // GET /currency/USD
        if ($this->httpMethod === "GET" && ($qWord === "currency")) {
            $input = $this->checkAndGetInput(GET_CUR);
            $httpResponse = Controller::showCurrencyByCode($input["code"]);
            $httpResponse->sendJSON();
            exit();
        }

        // GET /exchangeRate/USDRUB
        if ($this->httpMethod === "GET" && ($qWord === "exchangeRate")) {
            $input = $this->checkAndGetInput(GET_RATE);
            $httpResponse = Controller::showExchangeRateByCodes($input["baseCurrencyCode"], $input["targetCurrencyCode"]);
            $httpResponse->sendJSON();
            exit();
        }
        // PATCH /exchangeRate/USDRUB
        if ($this->httpMethod === "PATCH" && ($qWord === "exchangeRate")) {
            $input = $this->checkAndGetInput(PATCH_RATE);
            $httpResponse = Controller::patchExchangeRateByCodes($input["baseCurrencyCode"], $input["targetCurrencyCode"], $input["newRate"]);
            $httpResponse->sendJSON();
            exit();
        }
        $httpResponse = new HttpResponse(404, null, "Wrong URL or method");
        $httpResponse->sendJSON();
    }
}