<?php

namespace App\Database;

class DatabaseResponse
{
    private ?object $data; //TODO Need to do array
    private int $code;
    private ?string $errorMessage;


    public function __construct(int $code, ?object $data = null, string $errorMessage = null)
    {
        $this->data = $data;
        $this->code = $code;
        $this->errorMessage = $errorMessage;
    }

    public function isNotSuccess(): bool
    {
        if ($this->code === 200 || $this->code === 201) {
            return false;
        }
        return true;
    }

    public function getData(): ?object
    {
        return $this->data;
    }

    public function setData(?object $data): void
    {
        $this->data = $data;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

}