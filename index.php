<?php
require_once 'vendor/autoload.php';

header('Content-type: json/application');

require_once 'App/Database/connectionInfo.php';

/**
 * @var  $hostname,
 * @var  $dbname,
 * @var  $port,
 * @var  $login,
 * @var  $password
 */

$connection = new \App\Database\Connection($hostname, $dbname, $port, $login, $password);

$sql = "SELECT * FROM `currencies`";

$stmt = $connection->getPdoConnection()->query($sql);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


var_dump($users[0]);
echo json_encode($users);

die($_GET['q']);