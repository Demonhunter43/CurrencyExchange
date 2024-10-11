<?php

namespace App\Objects;

class Currency implements \JsonSerializable
{
    private int $id;
    private string $code;
    private string $fullName;
    private string $sign;

    /**
     * @param string $code
     * @param string $fullName
     * @param string $sign
     */
    public function __construct(int $id, string $code, string $fullName, string $sign)
    {
        $this->id = $id;
        $this->code = $code;
        $this->fullName = $fullName;
        $this->sign = $sign;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getCode(): string
    {
        return $this->code;
    }


    public function getFullName(): string
    {
        return $this->fullName;
    }


    public function getSign(): string
    {
        return $this->sign;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->fullName,
            'code' => $this->code,
            'sign' => $this->sign
        ];
    }
}