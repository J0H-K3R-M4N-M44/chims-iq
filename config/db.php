<?php
// config/db.php - CHIMS-IQ Database Connection
$host = 'localhost';
$db   = 'chimsiq_db';
$user = 'root';           
$pass = 'root';               

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// For direct access in included files
if (!isset($pdo)) {
    $pdo = $GLOBALS['pdo'];
}

?>