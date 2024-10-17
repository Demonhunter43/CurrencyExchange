<?php

namespace App\Database;

class Connection
{
    private \PDO $pdoConnection;

    public function __construct()
    {
        $this->pdoConnection = new \PDO('sqlite: currency_exchange.db');
    }

    /**
     * @return mixed
     */
    public function getPdo(): \PDO
    {
        return $this->pdoConnection;
    }
}