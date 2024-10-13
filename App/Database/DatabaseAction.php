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
            return new DatabaseResponse(500, "Can't connect to DB");
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
        $sql = "SELECT * FROM `exchangerates`";
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
}