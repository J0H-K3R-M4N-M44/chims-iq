<?php
// test_db.php - ROOT folder
require_once 'config/db.php';

echo "✅ Database Connected Successfully!<br>";
echo "MySQL Version: " . $pdo->query("SELECT VERSION()")->fetchColumn() . "<br>";
echo "Current Database: " . $pdo->query("SELECT DATABASE()")->fetchColumn() . "<br>";
echo "Tables in chimsiq_db: " . $pdo->query("SHOW TABLES")->rowCount() . "<br>";

echo "<h3>✅ Phase 2 Complete - Database is Ready!</h3>";
?>
