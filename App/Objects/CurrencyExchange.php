<?php

namespace App\Objects;

class CurrencyExchange  implements \JsonSerializable
{

    private int $id;
    private int $baseCurrencyId;
    private int $targetCurrencyId;
    private float $rate;
    private Currency $baseCurrency;
    private Currency $targetCurrency;

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

    public function initializeCurrencies(Currency $baseCurrency, Currency $targetCurrency): void
    {
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'baseCurrency' => $this->baseCurrency,
            'targetCurrency' => $this->targetCurrency,
            'rate' => $this->rate
        ];
    }
}