<?php
$dsn = 'mysql:host=localhost;dbname=databaseisim';
$username = 'kulaniciadi';
$password = 'sifre';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>