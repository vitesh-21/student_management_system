<?php
session_start();

// Enable error reporting to find the exact line if it crashes
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "sql302.infinityfree.com";
$username   = "if0_41413175";
$password   = "vtesh1234"; 
$dbname     = "if0_41413175_student";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $emailInput    = trim($_POST['email']);
    $usernameInput = trim($_POST['username']);
    $passInput     = trim($_POST['password']);
    $roleInput     = $_POST['role'];

    // 1. Set the query based on Role
    if ($roleInput == 'Admin') {
        $query = "SELECT * FROM admins WHERE email = ? AND username = ?";
        $redirect_page = "admin.php";
    } else {
        // Checking against 'full_name' as per your original student table structure
        $query = "SELECT * FROM students WHERE email = ? AND full_name = ?";
        $redirect_page = "student_dashboard.php";
    }

    // 2. Prepare and Execute
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $emailInput, $usernameInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // 3. SECURE CHECK: Compare typed password with the hashed password in DB
        if (password_verify($passInput, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $roleInput;
            header("Location: " . $redirect_page);
            exit();
        } else { 
            $error_msg = "Invalid password. (Note: Old plain-text passwords will not work)"; 
        }
    } else { 
        $error_msg = "No user found with those credentials."; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Portal</title>
    <style>
        body { background: #1a1a2e; font-family: 'Segoe UI', sans-serif; 
        display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 10px; width: 380px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #1a1a2e; margin-bottom: 20px; }
        label { font-size: 13px; font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        input, select, button { width: 100%; padding: 12px; margin-bottom: 15px;
         border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { background: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;
         margin-bottom: 15px; text-align: center; font-size: 14px; }
        .footer-links { text-align: center; margin-top: 10px; }
        .footer-links a { color: #007bff; text-decoration: none; font-size: 14px; }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h2>SYSTEM LOGIN</h2>
        <?php if($error_msg) echo "<div class='error'>$error_msg</div>"; ?>
        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="xx@gmail.com" required>

            <label>Username / Full Name</label>
            <input type="text" name="username" placeholder="Enter username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <label>Login As</label>
            <select name="role">
                <option value="Admin">Admin</option>
                <option value="Student">Student</option>
            </select>

            <button type="submit" name="login_btn">Login to Dashboard</button>
        </form>
        <div class="footer-links">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>