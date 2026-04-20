<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); exit(); 
}

$message = "";
$message_type = "";

// Handle form submission
if (isset($_POST['update_student'])) {
    $student_id = $_POST['student_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($name) || empty($email)) {
        $message = "Name and email are required";
        $message_type = "error";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $message = "Name should only contain alphabetic characters and spaces";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address";
        $message_type = "error";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $message = "Email should only contain @ as special character";
        $message_type = "error";
    } elseif (!empty($password) && strlen($password) < 4) {
        $message = "Password must be at least 4 characters long";
        $message_type = "error";
    } else {
        // Check if email already exists (excluding current student)
        $student_id = mysqli_real_escape_string($conn, $student_id);
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        
        $check_query = "SELECT id FROM users WHERE email = '$email' AND id != '$student_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $message = "Email address already exists";
            $message_type = "warning";
        } else {
            // Update student record
            if (!empty($password)) {
                // Update with password
                $password = mysqli_real_escape_string($conn, $password);
                $update_query = "UPDATE users SET name = '$name', email = '$email', password = '$password' WHERE id = '$student_id'";
            } else {
                // Update without password
                $update_query = "UPDATE users SET name = '$name', email = '$email' WHERE id = '$student_id'";
            }
            
            if (mysqli_query($conn, $update_query)) {
                $message = "Student updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . mysqli_error($conn);
                $message_type = "error";
            }
        }
    }
}

// Get student record to edit
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM users WHERE id = '$student_id' AND role = 'student'";
    
    $result = mysqli_query($conn, $query);
    $student_record = mysqli_fetch_assoc($result);
    
    if (!$student_record) {
        $message = "Student not found";
        $message_type = "error";
    }
} else {
    header("Location: remove_student.php");
    exit();
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Edit Student</h1>
        <p class="text-secondary">Update student information</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($student_record)): ?>
        <form method="POST" class="modern-form" id="editStudentForm">
            <input type="hidden" name="student_id" value="<?php echo $student_record['id']; ?>">
            
            <div class="form-group">
                <label for="name" class="form-label">
                    <i class="fas fa-user"></i>
                    Full Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-input" 
                       value="<?php echo htmlspecialchars($student_record['name']); ?>"
                       placeholder="Enter student's full name" 
                       pattern="[A-Za-z\s]+"
                       title="Only alphabetic characters and spaces are allowed"
                       required>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Only letters and spaces are allowed (no numbers)
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-input" 
                       value="<?php echo htmlspecialchars($student_record['email']); ?>"
                       placeholder="student@example.com" 
                       pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                       title="Only @ is allowed as special character in email"
                       required>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Only @ is allowed as special character
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    New Password
                </label>
                <div class="password-input-group">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Leave blank to keep current password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Leave blank to keep current password, or enter new password (min 4 characters)
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_student" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Student
                </button>
                <a href="remove_student.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.text-secondary {
    color: var(--text-secondary);
}

.modern-form {
    max-width: 500px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-label i {
    color: var(--primary);
    font-size: 0.875rem;
}

.form-hint {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.form-hint i {
    font-size: 0.75rem;
}

.password-input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.2s;
}

.password-toggle:hover {
    color: var(--primary);
    background: rgba(79, 70, 229, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Real-time validation
function validateName() {
    const nameInput = document.getElementById('name');
    const name = nameInput.value;
    
    const cleanedName = name.replace(/[^a-zA-Z\s]/g, '');
    
    if (name !== cleanedName) {
        nameInput.value = cleanedName;
        nameInput.style.borderColor = 'var(--danger)';
        showValidationMessage('name', 'Numbers and special characters are not allowed');
    } else {
        nameInput.style.borderColor = '';
        hideValidationMessage('name');
    }
}

function validateEmail() {
    const emailInput = document.getElementById('email');
    const email = emailInput.value;
    
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (!emailPattern.test(email) && email.length > 0) {
        emailInput.style.borderColor = 'var(--danger)';
        showValidationMessage('email', 'Only @ is allowed as special character');
    } else {
        emailInput.style.borderColor = '';
        hideValidationMessage('email');
    }
}

function showValidationMessage(field, message) {
    let messageDiv = document.getElementById(field + 'Validation');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.id = field + 'Validation';
        messageDiv.className = 'validation-message';
        messageDiv.style.color = 'var(--danger)';
        messageDiv.style.fontSize = '0.75rem';
        messageDiv.style.marginTop = '0.25rem';
        messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        
        const formGroup = document.getElementById(field).closest('.form-group');
        formGroup.appendChild(messageDiv);
    }
}

function hideValidationMessage(field) {
    const messageDiv = document.getElementById(field + 'Validation');
    if (messageDiv) {
        messageDiv.remove();
    }
}

// Add event listeners
document.getElementById('name').addEventListener('input', validateName);
document.getElementById('email').addEventListener('input', validateEmail);

// Clear form on successful submission
<?php if ($message_type === 'success'): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Clear password field on successful update
    document.getElementById('password').value = '';
});
<?php endif; ?>

// Form validation before submission
document.getElementById('editStudentForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Validate name format
    if (!/^[a-zA-Z\s]+$/.test(name)) {
        e.preventDefault();
        alert('Name should only contain alphabetic characters and spaces!');
        return false;
    }
    
    // Validate email format
    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
        e.preventDefault();
        alert('Email should only contain @ as special character!');
        return false;
    }
    
    // Validate password if provided
    if (password.length > 0 && password.length < 4) {
        e.preventDefault();
        alert('Password must be at least 4 characters long!');
        return false;
    }
});
</script>
