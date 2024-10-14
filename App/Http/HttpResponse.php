<?php

namespace App\Http;

use App\Objects\Currency;
use App\Objects\ExchangeRate;

/*include_once (ExchangeRate::class);
include_once (Currency::class);*/

class HttpResponse
{
    private ?array $data;
    private int $code;
    private ?string $errorMessage;

    /**
     * @param ?array $data
     * @param int $code
     * @param ?string $errorMessage
     */
    public function __construct(int $code, ?array $data = null, ?string $errorMessage = null)
    {
        $this->data = $data;
        $this->code = $code;
        $this->errorMessage = $errorMessage;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function sendJSON(): void
    {
        http_response_code($this->code);
        if (is_null($this->data)) {
            echo json_encode(array("message" => $this->errorMessage));
        } elseif (count($this->data) === 1) {
            echo json_encode($this->data[0]);
        } else {
            echo json_encode($this->data);
        }
    }
}