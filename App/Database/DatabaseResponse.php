<?php

namespace App\Database;

class DatabaseResponse
{
    private ?string $data;
    private int $code;

    /**
     * @param ?string $data
     * @param int $code
     */
    public function __construct(int $code, string $data = null)
    {
        $this->data = $data;
        $this->code = $code;
    }
    public function isSuccess(): bool
    {
        if ($this->code === 200 || $this->code === 201){
            return true;
        }
        return false;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
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

}