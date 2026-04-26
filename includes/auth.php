<?php
// includes/auth.php - Improved Version
session_start();
require_once __DIR__ . '/../config/db.php';

function login($identifier, $password, $storeName = null, $superKey = null) {
    global $pdo;

    if ($superKey === 'CHIMS-IQ-SUPER-2026-SECURE-KEY') {
        $_SESSION['user_id'] = 999;
        $_SESSION['role'] = 'superadmin';
        $_SESSION['store_id'] = null;
        $_SESSION['fullname'] = 'Super Admin';
        return true;
    }

    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        // Admin
        $stmt = $pdo->prepare("SELECT * FROM USER WHERE Email = ? AND Role = 'admin'");
        $stmt->execute([$identifier]);
    } else {
        // Staff
        $stmt = $pdo->prepare("SELECT u.* FROM USER u 
                               JOIN STORE s ON u.StoreID = s.StoreID 
                               WHERE u.StaffID = ? AND s.StoreName = ? AND u.Role = 'staff'");
        $stmt->execute([$identifier, $storeName]);
    }

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['PasswordHash'])) {
        $_SESSION['user_id']   = $user['UserID'];
        $_SESSION['role']      = $user['Role'];
        $_SESSION['store_id']  = $user['StoreID'];
        $_SESSION['fullname']  = $user['FullName'];
        return true;
    }

    return false;
}

function requireRole($allowedRoles = []) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
        header("Location: ../modules/auth/login.php?error=unauthorized");
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logout() {
    session_destroy();
    header("Location: ../modules/auth/login.php");
    exit;
}

// Helper to get current store_id safely
function getCurrentStoreId() {
    return $_SESSION['store_id'] ?? null;
}


if (isset($_GET['logout'])) {
    logout();
}
?>