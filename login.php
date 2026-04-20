<?php
session_start();
include 'config.php';

$error = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = strtolower(trim($user['role']));

            $redirect = ['admin' => 'admin_dashboard.php', 'teacher' => 'teacher_dashboard.php', 'student' => 'student_dashboard.php'];
            header("Location: " . ($redirect[$_SESSION['role']] ?? 'login.php'));
            exit();
        }
    }
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Attendance & Performance Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body >

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to your account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" style="width:100%; padding:11px;">Login</button>
        </form>
        
        <div style="text-align:center; margin-top:15px;">
            <a href="forgot_password.php" style="color: #4f46e5; text-decoration:none; font-size:13px;">
                <i class="fas fa-key"></i>
                Forgot Password?
            </a>
        </div>
        
        <div style="text-align:center; margin-top:10px; color:#555; font-size:12px;">
            <strong>Demo Credentials:</strong><br>
            Admin: admin@email.com / 1234<br>
            Teacher: teacher@email.com / 1234<br>
            Student: adnan@email.com / 1234
        </div>
    </div>
</div>

</body>
</html>
