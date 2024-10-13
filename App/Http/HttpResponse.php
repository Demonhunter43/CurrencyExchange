<?php

namespace App\Http;

class HttpResponse
{
    private ?object $data; //TODO Do object
    private int $code;
    private ?string $errorMessage;

    /**
     * @param ?array $data
     * @param int $code
     * @param ?string $errorMessage
     */
    public function __construct(int $code, ?object $data = null, ?string $errorMessage = null)
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
        if (is_null($this->data)){
            echo json_encode(array("message" => $this->errorMessage));
        }
        echo json_encode($this->data);
    }
}