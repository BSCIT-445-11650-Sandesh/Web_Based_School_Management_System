<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); exit(); 
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $teacher_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // First check if teacher exists
    $check_query = "SELECT name FROM users WHERE id = '$teacher_id' AND role = 'teacher'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_message'] = "Teacher not found";
    } else {
        $teacher = mysqli_fetch_assoc($check_result);
        
        // Delete teacher (no need to delete related records as teachers don't have attendance/marks)
        if (mysqli_query($conn, "DELETE FROM users WHERE id = '$teacher_id' AND role = 'teacher'")) {
            $_SESSION['success_message'] = "Teacher '{$teacher['name']}' has been successfully deleted.";
        } else {
            $_SESSION['error_message'] = "Error deleting teacher: " . mysqli_error($conn);
        }
    }
}

header("Location: remove_teacher.php");
exit();
?>
