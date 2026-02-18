<?php
// Database configuration
define('DB_HOST', 'localhost');     // Usually 'localhost' in Laragon
define('DB_USER', 'root');          // Default user in Laragon
define('DB_PASS', '');              // Default password is empty in Laragon
define('DB_NAME', 'tb5enrollmentsystemdb'); // Replace with your actual database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 (recommended)
$conn->set_charset("utf8mb4");

// Optional: Set timezone
date_default_timezone_set('Asia/Manila');
?>