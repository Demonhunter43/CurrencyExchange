<?php

namespace App;

// This class checks string q from URL and then returns need Action object
class Router
{
    private string $q;

    public function __construct(string $q)
    {
        $this->q = $q;
    }

    /*public function getAction()
    {
        switch ($q){
            case ()
        }
    }*/
}