<?php
$dbServer = "";
$dbUser = "";
$dbPassword = "";
$dbName = "";
try {
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle error (optional: log or display a message)
    die("Connection failed: " . $e->getMessage());
}
 ?>