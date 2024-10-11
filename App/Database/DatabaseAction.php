<?php

namespace App\Database;

use App\Objects\Currency;
use App\Objects\CurrencyExchange;

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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCurrencyByCode($code): array
    {

        $sql = "SELECT * FROM `currencies` WHERE `Code` = :code";
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'code'=> $code
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function addCurrency(Currency $currency): bool
    {
        $code = $currency->getCode();
        $fullName = $currency->getFullName();
        $sign = $currency->getSign();
        $sql = "INSERT INTO `currencies` (ID, Code, FullName, Sign) VALUES (null, :code, :fullName, :sign)";
        
        $stmt = $this->connection->getPdo()->prepare($sql);
        $stmt->execute([
            'code'=> $code,
            'fullName'=> $fullName,
            'sign'=> $sign
        ]);
        return true;
    }
}