<?php

require_once 'vendor/autoload.php';

header('Content-type: json/application');


$router = new \App\Router();
$router->run();