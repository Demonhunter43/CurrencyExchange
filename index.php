<?php

use App\Database\Connection;

require_once 'vendor/autoload.php';

header('Content-type: json/application');


$router = new \App\Router($_GET['q'], $_SERVER['REQUEST_METHOD']);
$router->run();