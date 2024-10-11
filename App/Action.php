<?php

namespace App;
// This abstract class uses special class to get data from Database in Objects (Currency and CurrencyExchange)
// and transform in JSON needed format.
use App\Database\Connection;
use App\Objects\Currency;

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
        $sql = "SELECT * FROM `currencies`";
        $stmt = $connection->getPdoConnection()->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $i = 0;
        foreach ($data as $currency) {
            $arrayCurrencies[$i] = new Currency($currency["ID"], $currency["Code"], $currency["FullName"], $currency["Sign"]);
            $i++;
        }
        echo json_encode($arrayCurrencies);
    }

    public function showCurrencyByCode()
    {

    }
}