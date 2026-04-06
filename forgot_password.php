<?php
// 1. DATABASE CONNECTION (The Bridge)
define('DB_SERVER', 'sql302.infinityfree.com');
define('DB_USERNAME', 'if0_41413175');
define('DB_PASSWORD', 'vtesh1234');
define('DB_NAME', 'if0_41413175_student');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";
$msg_class = "";
$redirect = false; // Flag to trigger redirect

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameInput = mysqli_real_escape_string($conn, $_POST['usernameInput']);
    $newPass       = $_POST['newPassword'];
    $confirm       = $_POST['confirmPassword'];

    if ($newPass !== $confirm) {
        $msg = "Passwords do not match!";
        $msg_class = "error";
    } else {
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);

        // 1. Check Admin
        $checkAdmin = $conn->query("SELECT id FROM admins WHERE username='$usernameInput'");
        
        // 2. Check Student
        $checkStudent = $conn->query("SELECT id FROM students WHERE full_name='$usernameInput'");
        
        if ($checkAdmin->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE admins SET password=? WHERE username=?");
            $stmt->bind_param("ss", $hashedPass, $usernameInput);
            $stmt->execute();
            $msg = "Admin password updated! Redirecting to login...";
            $msg_class = "success";
            $redirect = true; 
        } elseif ($checkStudent->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE students SET password=? WHERE full_name=?");
            $stmt->bind_param("ss", $hashedPass, $usernameInput);
            $stmt->execute();
            $msg = "Student password updated! Redirecting to login...";
            $msg_class = "success";
            $redirect = true;
        } else {
            $msg = "Username not found in our records.";
            $msg_class = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body { background: #1a1a2e; font-family: 'Segoe UI', sans-serif; 
        display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 30px; border-radius: 10px; width: 350px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #1a1a2e; margin-top: 0; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        button { width: 100%; background: #28a745; color: white; padding: 12px;
         border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #218838; }
        .msg { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; font-size: 14px; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { color: #007bff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Reset Password</h2>

    <?php if($msg): ?>
        <div class="msg <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label style="font-size: 12px; font-weight: bold; color: #555;">Username</label>
        <input type="text" name="usernameInput" placeholder="Enter your username" required>
        
        <label style="font-size: 12px; font-weight: bold; color: #555;">New Password</label>
        <input type="password" name="newPassword" placeholder="••••••••" required>
        
        <label style="font-size: 12px; font-weight: bold; color: #555;">Confirm Password</label>
        <input type="password" name="confirmPassword" placeholder="••••••••" required>

        <button type="submit">Update Password</button>

        <div class="back-link">
            <a href="index.php">Back to Login</a>
        </div>
    </form>
</div>

</body>
</html>