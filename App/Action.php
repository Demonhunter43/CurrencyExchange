<?php

namespace App;
// This abstract class calls all need methods and send respond
use App\Database\Connection;
use App\Database\DatabaseActions;
use App\Objects\Currency;

class Action
{
    public static function showAllCurrencies(): void
    {
        $data = DatabaseActions::getAllCurrencies();
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