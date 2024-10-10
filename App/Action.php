<?php

namespace App;
// This abstract class uses special class to get data from Database in Objects (Currency and CurrencyExchange)
// and transform in JSON needed format.
use App\Database\Connection;

class Action
{
    public static function showAllCurrencies(): void
    {

        require_once 'App/Database/connectionInfo.php';

        /**
         * @var  $hostname,
         * @var  $dbname,
         * @var  $port,
         * @var  $login,
         * @var  $password
         */

        $connection = new Connection($hostname, $dbname, $port, $login, $password);
        $sql = "SELECT * FROM `exchangerates`";
        $stmt = $connection->getPdoConnection()->query($sql);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($users);
    }

    public function showCurrencyByCode()
    {

    }
}