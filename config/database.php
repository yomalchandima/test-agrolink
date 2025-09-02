<?php
// Database configuration
$host = 'localhost';
$dbname = 'agrolink_db'; // You can change this to your database name
$username = 'root'; // You can change this to your database username
$password = ''; // XAMPP MySQL has no password by default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
