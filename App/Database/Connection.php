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
        try {
            $this->pdoConnection = new \PDO("mysql:host=$hostname;dbname=$dbname;port=$port;",$login,$password);
        } catch (PDOException $exception){
            var_dump($exception->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function getPdo(): \PDO
    {
        return $this->pdoConnection;
    }
}