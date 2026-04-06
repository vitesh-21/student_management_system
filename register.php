<?php
// 1. DATABASE CONNECTION
define('DB_SERVER', 'sql302.infinityfree.com');
define('DB_USERNAME', 'if0_41413175');
define('DB_PASSWORD', 'vtesh1234');
define('DB_NAME', 'if0_41413175_student');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$message = "";
$msg_class = ""; 

if(isset($_POST['signup'])){

    $full_name  = $_POST['full_name'];
    $reg_number = $_POST['reg_number'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $course     = $_POST['course']; // Now captured from the select dropdown
    $year       = (int)$_POST['year'];
    $semester   = (int)$_POST['semester'];

    // 2. CHECK IF REGISTRATION NUMBER ALREADY EXISTS
    $check = $conn->prepare("SELECT id FROM students WHERE reg_number = ?");
    $check->bind_param("s", $reg_number);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0) {
        $message = "Error: Registration Number already exists!";
        $msg_class = "color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px; border: 1px solid #f5c6cb;";
    } else {
        // 3. PROCEED WITH INSERT
        $stmt = $conn->prepare("INSERT INTO students 
        (full_name, reg_number, password, email, course, created_at, year, semester) 
        VALUES (?,?,?,?,?,NOW(),?,?)");

        $stmt->bind_param("sssssii",
            $full_name,
            $reg_number,
            $password,
            $email,
            $course,
            $year,
            $semester
        );

        if($stmt->execute()){
            $message = "Student registered successfully! <a href='index.php'>Login here</a>";
            $msg_class = "color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; border: 1px solid #c3e6cb;";
        } else {
            $message = "Registration failed: " . $conn->error;
            $msg_class = "color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px; border: 1px solid #f5c6cb;";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Sign Up</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background: #ecf0f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .box{
            background: white;
            padding: 30px;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Important for width alignment */
        }
        button {
            width: 100%;
            padding: 12px;
            background: #00a65a;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        button:hover { background: #008d4c; }
        .alert { margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Student Sign Up</h2>

    <?php if($message != ""): ?>
        <div class="alert" style="<?php echo $msg_class; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="text" name="reg_number" placeholder="Registration Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="course" required>
            <option value="" disabled selected>Select Your Course</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Business Information Technology">Business Information Technology</option>
            <option value="Cyber Security">Cyber Security</option>
            <option value="Software Engineering">Software Engineering</option>
        </select>

        <select name="year" required>
            <option value="" disabled selected>Select Year</option>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
        </select>

        <select name="semester" required>
            <option value="" disabled selected>Select Semester</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
        </select>

        <button type="submit" name="signup">Sign Up</button>
    </form>
</div>

</body>
</html>