<?php
// modules/auth/login_process.php - DEBUG VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/auth.php';

echo "<h2>Login Process Started</h2>";
echo "POST Data:<br>";
print_r($_POST);
echo "<hr>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Not a POST request!";
    exit;
}

$type = $_POST['login_type'] ?? 'unknown';
echo "Login Type: " . htmlspecialchars($type) . "<br>";

$success = false;

if ($type === 'superadmin') {
    $key = $_POST['superKey'] ?? '';
    echo "Superadmin Key entered: " . htmlspecialchars($key) . "<br>";
    $success = login('', '', '', $key);
} elseif ($type === 'admin') {
    $email = $_POST['identifier'] ?? '';
    $pass = $_POST['password'] ?? '';
    echo "Admin Email: " . htmlspecialchars($email) . "<br>";
    $success = login($email, $pass);
} elseif ($type === 'staff') {
    $staffId = $_POST['identifier'] ?? '';
    $storeName = $_POST['storeName'] ?? '';
    $pass = $_POST['password'] ?? '';
    echo "Staff ID: " . htmlspecialchars($staffId) . " | Store: " . htmlspecialchars($storeName) . "<br>";
    $success = login($staffId, $pass, $storeName);
} else {
    echo "Unknown login type!";
}

echo "<br>Login result: " . ($success ? "SUCCESS" : "FAILED") . "<br>";

if ($success) {
    echo "Redirecting to index.php...<br>";
    header("Location: ../../index.php");
    exit;
} else {
    echo "Redirecting back to login with error...<br>";
    header("Location: login.php?error=Invalid credentials - Debug mode active");
    exit;
}
?>