<?php
require_once '../../includes/auth.php';

if (isLoggedIn()) {
    header("Location: ../../index.php");
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CHIMS-IQ Login</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.2); width: 380px; }
        input, select, button { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #3C3489; color: white; font-weight: bold; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>CHIMS-IQ Login</h2>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="login_process.php">
            <label>Login Type</label>
            <select name="login_type" id="login_type" onchange="toggleFields()">
                <option value="admin">Admin (Store Owner)</option>
                <option value="staff">Staff</option>
                <option value="superadmin">Superadmin</option>
            </select>

            <div id="admin_fields">
                <input type="email" name="identifier" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div id="staff_fields" style="display:none;">
                <input type="text" name="identifier" placeholder="Staff ID (e.g. TECH-001)" required>
                <input type="text" name="storeName" placeholder="Store Name" required>
                <input type="password" name="password" placeholder="Password (default = Store Name)" required>
            </div>

            <div id="super_fields" style="display:none;">
                <input type="text" name="superKey" placeholder="Superadmin Secret Key" required>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('login_type').value;
            document.getElementById('admin_fields').style.display = type === 'admin' ? 'block' : 'none';
            document.getElementById('staff_fields').style.display = type === 'staff' ? 'block' : 'none';
            document.getElementById('super_fields').style.display = type === 'superadmin' ? 'block' : 'none';
        }
    </script>
</body>
</html>