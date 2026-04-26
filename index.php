<?php
// index.php - Main Dashboard
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: modules/auth/login.php");
    exit;
}

requireRole(['superadmin', 'admin', 'staff']); // Allow all for now
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHIMS-IQ Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f0f2f5; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #3C3489; }
        .role { font-size: 18px; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
        .super { background: #4CAF50; color: white; }
        .admin { background: #3C3489; color: white; }
        .staff { background: #FF9800; color: white; }
        a { display: inline-block; margin: 10px 5px; padding: 12px 20px; background: #3C3489; color: white; text-decoration: none; border-radius: 5px; }
        a:hover { background: #2a2466; }
    </style>
</head>
<body>
    <div class="container">
        <h1>CHIMS-IQ Dashboard</h1>
        
        <div class="role 
            <?php 
                if ($_SESSION['role'] == 'superadmin') echo 'super'; 
                elseif ($_SESSION['role'] == 'admin') echo 'admin'; 
                else echo 'staff'; 
            ?>">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong> 
            (<?= strtoupper($_SESSION['role']) ?>)
            <?php if ($_SESSION['store_id']): ?>
                | Store ID: <?= $_SESSION['store_id'] ?>
            <?php endif; ?>
        </div>

        <p><strong>Welcome to CHIMS-IQ</strong></p>

        <div>
            <?php if ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin'): ?>
                <a href="modules/products/">Products Management</a>
                <a href="modules/po/">Purchase Orders</a>
                <a href="modules/recovery/">Data Recovery</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'staff'): ?>
                <a href="modules/stock/">View Stock & Flag Items</a>
            <?php endif; ?>

            <a href="modules/auth/login.php?logout=1" style="background: #d32f2f;">Logout</a>
        </div>

        <hr>
        <p><small>Debug Info: Role = <?= $_SESSION['role'] ?> | StoreID = <?= $_SESSION['store_id'] ?? 'NULL (Superadmin)' ?></small></p>
    </div>
</body>
</html>