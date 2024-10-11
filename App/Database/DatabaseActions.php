<?php

namespace App\Database;

class DatabaseActions
{
    public static function getAllCurrencies(): array
    {
        require_once 'App/Database/connectionInfo.php';

        /**
         * @var  $hostname ,
         * @var  $dbname ,
         * @var  $port ,
         * @var  $login ,
         * @var  $password
         */

        $connection = new Connection($hostname, $dbname, $port, $login, $password);
        $sql = "SELECT * FROM `currencies`";
        $stmt = $connection->getPdoConnection()->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}