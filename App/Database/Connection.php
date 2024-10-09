<?php

namespace App\Database;

class Connection
{
    private $pdoConnection;
    public function __construct($hostname, $dbname, $port, $login, $password)
    {
        try {
            $this->pdoConnection = new \PDO("mysql:host=$hostname;dbname=$dbname;port=$port;",$login,$password);
        } catch (PDOException $exception){
            var_dump($exception->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function getPdoConnection()
    {
        return $this->pdoConnection;
    }
}