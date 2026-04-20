<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); 
    exit(); 
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $student_id = $_GET['id'];
    
    // First delete related records from attendance table
    mysqli_query($conn, "DELETE FROM attendance WHERE student_id = $student_id");
    
    // Then delete related records from marks table
    mysqli_query($conn, "DELETE FROM marks WHERE student_id = $student_id");
    
    // Finally delete the student from users table
    $result = mysqli_query($conn, "DELETE FROM users WHERE id = $student_id AND role = 'student'");
    
    if ($result) {
        $_SESSION['success_message'] = "Student deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting student: " . mysqli_error($conn);
    }
}

header("Location: remove_student.php");
exit();
?>
