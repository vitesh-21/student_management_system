<?php
// Database configuration
define('DB_SERVER', 'sql302.infinityfree.com');
define('DB_USERNAME', 'if0_41413175');
define('DB_PASSWORD', 'vtesh1234');
define('DB_NAME', 'if0_41413175_student');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn->connect_error){
    die("ERROR: Could not connect. " . $conn->connect_error);
}

// Set charset to utf8mb4 (best for names and text)
$conn->set_charset("utf8mb4");

// Error reporting - very helpful for debugging on InfinityFree
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>