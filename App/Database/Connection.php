<?php

namespace App\Database;

class Connection
{
    private \PDO $pdoConnection;

    public function __construct()
    {
        require 'connectionInfo.php';

        /**
         * @var  $hostname ,
         * @var  $dbname ,
         * @var  $port ,
         * @var  $login ,
         * @var  $password
         */
        $this->pdoConnection = new \PDO("mysql:host=$hostname;dbname=$dbname;port=$port;", $login, $password);
    }

    /**
     * @return mixed
     */
    public function getPdo(): \PDO
    {
        return $this->pdoConnection;
    }
}