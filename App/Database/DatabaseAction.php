<?php

namespace App\Database;

use App\Objects\Currency;
use App\Objects\ExchangeRate;

class DatabaseAction
{
    private Connection $connection;

    public function __construct()
    {
    }

    public function connect(): DatabaseResponse
    {
        try {
            $this->connection = new Connection();
        } catch (\PDOException $exception) {
            return new DatabaseResponse(500, null, "Can't connect to DB");
        }
        return new DatabaseResponse(200);
    }


    public function getAllCurrencies(): array
    {
        $sql = "SELECT * FROM `currencies`";
        $stmt = $this->connection->getPdo()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCurrencyByCode($code): array
    {
        $sql = "SELECT * FROM `currencies`
                WHERE `Code` = :code";
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'code' => $code
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
    }

    public function getCurrencyByID($id): array
    {
        $sql = "SELECT * FROM `currencies` WHERE `ID` = $id";
        $stmt = $this->connection->getPdo()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
    }

    public function addCurrency(Currency $currency): bool
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
        return true;
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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getExchangeRateByCurrenciesID(int $baseCurrencyID, int $targetCurrencyID): array
    {
        $sql = "SELECT * FROM `exchangerates` 
                WHERE BaseCurrencyId = :baseCurrencyID AND TargetCurrencyId = :targetCurrencyID";

        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'baseCurrencyID' => $baseCurrencyID,
            'targetCurrencyID' => $targetCurrencyID
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
    }

    public function getExchangeRateByCurrenciesCodes(string $baseCurrencyCode, string $targetCurrencyCode): DatabaseResponse
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
        try {
            $stmt->execute([
                'baseCurrencyCode' => $baseCurrencyCode,
                'targetCurrencyCode' => $targetCurrencyCode
            ]);
        } catch (\PDOException $e){
            return new DatabaseResponse(404, null, $e->getMessage());
        }
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($data) === 0){
            return new DatabaseResponse(404, null, "This pair is not presented");
        }
        $data = $data[0];
        return new DatabaseResponse(200, $data, null);
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): bool
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
        return true;
    }

    public function patchExchangeRate(ExchangeRate $exchangeRate): DatabaseResponse
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
        return new DatabaseResponse(200);
    }
}