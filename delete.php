<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['id']);

// Fetch student data before deletion for logging
$conn = getDBConnection();
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: dashboard.php");
    exit();
}

// Delete student record using prepared statement
$delete_sql = "DELETE FROM students WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $student_id);

if ($delete_stmt->execute()) {
    // Log deletion information to file using PHP file processing
    $log_message = "[" . date('Y-m-d H:i:s') . "] DELETED - Student ID: " . $student['student_id'] . 
                  ", Name: " . $student['full_name'] . 
                  ", Email: " . $student['email'] . 
                  ", Course: " . $student['course_of_study'] . "\n";
    
    file_put_contents('deletion_log.txt', $log_message, FILE_APPEND | LOCK_EX);
    
    $_SESSION['success'] = "Student record deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting student record: " . $delete_stmt->error;
}

$delete_stmt->close();
$conn->close();

header("Location: dashboard.php");
exit();
?>