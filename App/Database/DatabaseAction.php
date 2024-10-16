<?php

namespace App\Database;

use App\Objects\Currency;
use App\Objects\ExchangeRate;

class DatabaseAction
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = new Connection();
    }

    public function getAllCurrencies(): array
    {
        $sql = "SELECT * FROM `currencies`";
        $stmt = $this->connection->getPdo()->query($sql);
        return DataToObjectTransformer::makeCurrenciesArrayFromData($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getCurrencyByCode($code): Currency
    {
        $sql = "SELECT * FROM `currencies`
                WHERE `Code` = :code";
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'code' => $code
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($data) == 0) {
            throw new \Exception("Wrong code");
        }
        return DataToObjectTransformer::makeCurrencyFromData($data);
    }

    public function addCurrency(Currency $currency): void
    {
        $code = $currency->getCode();
        $fullName = $currency->getFullName();
        $sign = $currency->getSign();
        $sql = "INSERT INTO `currencies` 
                (ID, Code, FullName, Sign) VALUES (null, :code, :fullName, :sign)";

        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'code' => $code,
            'fullName' => $fullName,
            'sign' => $sign
        ]);
    }

    public function getAllExchangeRates(): array
    {
        $sql = "SELECT  exchangerates.ID,
                        BaseCurrency.ID AS BaseCurrencyID,
                        BaseCurrency.Code AS BaseCurrencyCode,
                        BaseCurrency.FullName AS BaseCurrencyFullName,
                        BaseCurrency.Sign AS BaseCurrencySign,
                        TargetCurrency.ID AS TargetCurrencyID,
                        TargetCurrency.Code AS TargetCurrencyCode,
                        TargetCurrency.FullName AS TargetCurrencyFullName,
                        TargetCurrency.Sign AS TargetCurrencySign,
                        exchangerates.Rate
                FROM `exchangerates`
                JOIN `currencies` AS BaseCurrency
                ON BaseCurrency.ID = exchangerates.BaseCurrencyID    
                JOIN `currencies` AS TargetCurrency
                ON TargetCurrency.ID = exchangerates.TargetCurrencyID;";
        $stmt = $this->connection->getPdo()->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return DataToObjectTransformer::makeExchangeRatesArrayFromData($data);
    }

    public function getExchangeRateByCurrenciesCodes(string $baseCurrencyCode, string $targetCurrencyCode): ExchangeRate
    {
        $sql = "SELECT  exchangerates.ID,
                        BaseCurrency.ID AS BaseCurrencyID,
                        BaseCurrency.Code AS BaseCurrencyCode,
                        BaseCurrency.FullName AS BaseCurrencyFullName,
                        BaseCurrency.Sign AS BaseCurrencySign,
                        TargetCurrency.ID AS TargetCurrencyID,
                        TargetCurrency.Code AS TargetCurrencyCode,
                        TargetCurrency.FullName AS TargetCurrencyFullName,
                        TargetCurrency.Sign AS TargetCurrencySign,
                        exchangerates.Rate
                FROM `exchangerates`
                JOIN `currencies` AS BaseCurrency
                ON BaseCurrency.ID = exchangerates.BaseCurrencyID    
                JOIN `currencies` AS TargetCurrency
                ON TargetCurrency.ID = exchangerates.TargetCurrencyID
                WHERE BaseCurrency.Code = :baseCurrencyCode AND TargetCurrency.Code = :targetCurrencyCode";
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'baseCurrencyCode' => $baseCurrencyCode,
            'targetCurrencyCode' => $targetCurrencyCode
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($data) === 0) {
            throw new \Exception("No pair {$baseCurrencyCode}{$targetCurrencyCode} in database");
        }
        $data = $data[0];
        return DataToObjectTransformer::makeExchangeRateFromData($data);
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): void
    {
        $baseCurrencyId = $exchangeRate->getBaseCurrencyId();
        $targetCurrencyId = $exchangeRate->getTargetCurrencyId();
        $rate = $exchangeRate->getRate();
        $sql = "INSERT INTO `exchangerates` 
                (ID, BaseCurrencyId, TargetCurrencyId, Rate) VALUES (null, :baseCurrencyId, :targetCurrencyId, :rate)";

        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'baseCurrencyId' => $baseCurrencyId,
            'targetCurrencyId' => $targetCurrencyId,
            'rate' => $rate
        ]);
    }

    public function patchExchangeRate(ExchangeRate $exchangeRate): void
    {
        $exchangeRateId = $exchangeRate->getId();
        $newRate = $exchangeRate->getRate();
        $sql = "UPDATE `exchangerates` 
                SET Rate = :newRate
                WHERE ID = :exchangeRateId";
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'newRate' => $newRate,
            'exchangeRateId' => $exchangeRateId
        ]);
    }
}