<?php

namespace App;
// This abstract class calls all need methods and send respond
use App\Database\Connection;
use App\Database\DatabaseActions;
use App\DTO\DataToObjectTransformer;
use App\Objects\Currency;

class Action
{
    public static function showAllCurrencies(): void
    {
        $data = DatabaseActions::getAllCurrencies();
        $arrayCurrencies = DataToObjectTransformer::makeCurrenciesArrayFromData($data);
        echo json_encode($arrayCurrencies);
    }

    public function showCurrencyByCode()
    {

    }
}