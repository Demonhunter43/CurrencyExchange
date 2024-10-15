<?php

namespace App\Objects;

class ExchangeRate  implements \JsonSerializable
{

    private ?int $id;
    private int $baseCurrencyId;
    private int $targetCurrencyId;
    private float $rate;
    private Currency $baseCurrency;
    private Currency $targetCurrency;


    public function __construct(?int $id, Currency $baseCurrency, Currency $targetCurrency, float $rate)
    {
        $this->id = $id;
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->rate = $rate;
        $this->baseCurrencyId = $baseCurrency->getId();
        $this->targetCurrencyId = $targetCurrency->getId();
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

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }
}