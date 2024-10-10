<?php

namespace App\Objects;

class CurrencyExchange
{

private int $id;
private int $baseCurrencyId;
private int $targetCurrencyId;
private float $rate;

    /**
     * @param int $baseCurrencyId
     * @param int $targetCurrencyId
     * @param float $rate
     */
    public function __construct(int $id, int $baseCurrencyId, int $targetCurrencyId, float $rate)
    {
        $this->id = $id;
        $this->baseCurrencyId = $baseCurrencyId;
        $this->targetCurrencyId = $targetCurrencyId;
        $this->rate = $rate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getBaseCurrencyId(): int
    {
        return $this->baseCurrencyId;
    }

    public function setBaseCurrencyId(int $baseCurrencyId): void
    {
        $this->baseCurrencyId = $baseCurrencyId;
    }

    public function getTargetCurrencyId(): int
    {
        return $this->targetCurrencyId;
    }

    public function setTargetCurrencyId(int $targetCurrencyId): void
    {
        $this->targetCurrencyId = $targetCurrencyId;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
}