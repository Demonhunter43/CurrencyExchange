<?php

namespace App\Objects;

class Exchange implements \JsonSerializable
{
    private Currency $baseCurrency;
    private Currency $targetCurrency;
    private float $rate;
    private float $amount;
    private float $convertedAmount;


    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }

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
        $this->convertedAmount = round($this->amount * $this->rate, 7);
    }

    public function jsonSerialize(): array
    {
        return [
            'baseCurrency' => $this->baseCurrency,
            'targetCurrency' => $this->targetCurrency,
            'rate' => $this->rate,
            'amount' => $this->amount,
            'convertedAmount' => $this->convertedAmount
        ];
    }
}