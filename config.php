<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Tshegofatso13#');
define('DB_NAME', 'student_management');

// Academic status constants
define('STATUS_ACTIVE', 'Active');
define('STATUS_PENDING', 'Pending');
define('STATUS_SUSPENDED', 'Suspended');
define('STATUS_GRADUATED', 'Graduated');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Error handling function
function handleError($error) {
    error_log("Student Management System Error: " . $error);
    return "An error occurred. Please try again later.";
}
?>