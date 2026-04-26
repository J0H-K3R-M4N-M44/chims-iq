<?php
require_once '../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['login_type'] ?? '';

    if ($type === 'superadmin') {
        $success = login('', '', '', $_POST['superKey'] ?? '');
    } elseif ($type === 'admin') {
        $success = login($_POST['identifier'], $_POST['password']);
    } elseif ($type === 'staff') {
        $success = login($_POST['identifier'], $_POST['password'], $_POST['storeName']);
    } else {
        $success = false;
    }

    if ($success) {
        header("Location: ../../index.php");
    } else {
        header("Location: login.php?error=Invalid credentials");
    }
    exit;
}
?>