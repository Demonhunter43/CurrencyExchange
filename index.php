<?php
require_once 'vendor/autoload.php';

header('Content-type: json/application');

$hostname = 'localhost';
$dbname = 'currencyexchange';
$login = 'root';
$password = '';
$port = 3306;

$connection = new \App\Database\Connection($hostname, $dbname, $port, $login, $password);

$sql = "SELECT * FROM `currencies`";

$stmt = $connection->getPdoConnection()->query($sql);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


var_dump($users[0]);
echo json_encode($users);

die($_GET['q']);