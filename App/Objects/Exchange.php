<?php

namespace App\Objects;

class Exchange
{
    private Currency $baseCurrency;
    private Currency $targetCurrency;
    private float $rate;
    private float $amount;
    private float $convertedAmount;

    /**
     * @param Currency $baseCurrency
     * @param Currency $targetCurrency
     * @param float $rate
     * @param float $amount
     */
    public function __construct(Currency $baseCurrency, Currency $targetCurrency, float $rate, float $amount)
    {
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->rate = $rate;
        $this->amount = $amount;
    }

    public function convert(): void
    {
        $this->convertedAmount = $this->amount * $this->rate;
    }
    public function jsonSerialize(): array
    {
        return [
            'baseCurrency' => $this->baseCurrency,
            'targetCurrency' => $this->targetCurrency,
            'rate' => $this->rate,
            'amount' => $this->rate,
            'convertedAmount' => $this->convertedAmount
        ];
    }
}