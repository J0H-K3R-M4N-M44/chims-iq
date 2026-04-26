<?php
// modules/auth/login.php - SELF-CONTAINED DEBUG VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['login_type'] ?? '';

    $success = false;
    if ($type === 'superadmin') {
        $success = login('', '', '', $_POST['superKey'] ?? '');
    } elseif ($type === 'admin') {
        $success = login($_POST['identifier'] ?? '', $_POST['password'] ?? '');
    } elseif ($type === 'staff') {
        $success = login($_POST['identifier'] ?? '', $_POST['password'] ?? '', $_POST['storeName'] ?? '');
    }

    if ($success) {
        header("Location: ../../index.php");
        exit;
    } else {
        $message = "Invalid credentials. Please check your input.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHIMS-IQ Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 420px; }
        h2 { text-align: center; color: #3C3489; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; }
        input, select, button { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 16px; }
        button { background: #3C3489; color: white; font-weight: bold; cursor: pointer; }
        button:hover { background: #2a2466; }
        .error { color: red; text-align: center; font-weight: bold; }
        .field { display: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>CHIMS-IQ Login</h2>
        
        <?php if ($message): ?>
            <p class="error"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Login Type</label>
            <select name="login_type" id="login_type" onchange="toggleFields()" required>
                <option value="admin">Admin (Store Owner)</option>
                <option value="staff">Staff</option>
                <option value="superadmin">Superadmin</option>
            </select>

            <div id="admin_fields" class="field">
                <label>Email</label>
                <input type="email" name="identifier" value="admin@pchardwarehub.ph" required>
                <label>Password</label>
                <input type="password" name="password" value="password" required>
            </div>

            <div id="staff_fields" class="field">
                <label>Staff ID</label>
                <input type="text" name="identifier" value="TECH-001" required>
                <label>Store Name</label>
                <input type="text" name="storeName" value="PC Hardware Hub" required>
                <label>Password</label>
                <input type="password" name="password" value="password" required>
            </div>

            <div id="super_fields" class="field">
                <label>Superadmin Secret Key</label>
                <input type="text" name="superKey" value="CHIMS-IQ-SUPER-2026-SECURE-KEY" required>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('login_type').value;
            document.getElementById('admin_fields').style.display = (type === 'admin') ? 'block' : 'none';
            document.getElementById('staff_fields').style.display = (type === 'staff') ? 'block' : 'none';
            document.getElementById('super_fields').style.display = (type === 'superadmin') ? 'block' : 'none';
        }
        window.onload = toggleFields;
    </script>
</body>
</html>