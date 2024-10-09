<?php
require_once 'vendor/autoload.php';

$hostname = 'localhost';
$dbname = 'currencyexchange';
$login = 'root';
$password = '';
$port = 3306;

echo "loh";

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname;port=$port;",$login,$password);
} catch (PDOException $exception){
    var_dump($exception->getMessage());
}

$sql = "SELECT * FROM `currencies`";

$stmt = $pdo->query($sql);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

var_dump($users);