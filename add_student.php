<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit(); }

$message = "";
$message_type = "";

if (isset($_POST['add_student'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    // Validation
    if (empty($name) || empty($email) || empty($pass)) {
        $message = "All fields are required";
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
    } elseif (strlen($pass) < 4) {
        $message = "Password must be at least 4 characters long";
        $message_type = "error";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $message = "Email address already exists";
            $message_type = "warning";
        } else {
            // Insert new student
            $name = mysqli_real_escape_string($conn, $name);
            $email = mysqli_real_escape_string($conn, $email);
            $pass = mysqli_real_escape_string($conn, $pass);
            
            if (mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name','$email','$pass','student')")) {
                $message = "Student added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . mysqli_error($conn);
                $message_type = "error";
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Add New Student</h1>
        <p class="text-secondary">Register a new student in the system</p>
    </div>

    <div class="form-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="modern-form" id="addStudentForm">
            <div class="form-group">
                <label for="name" class="form-label">
                    <i class="fas fa-user"></i>
                    Full Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-input" 
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
                    Password
                </label>
                <div class="password-input-group">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Enter password" 
                           required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Minimum 4 characters. This will be used for login.
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Confirm Password
                </label>
                <div class="password-input-group">
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           class="form-input" 
                           placeholder="Confirm password" 
                           required>
                    <button type="button" class="password-toggle" onclick="toggleConfirmPassword()">
                        <i class="fas fa-eye" id="confirmPasswordToggleIcon"></i>
                    </button>
                </div>
                <div class="form-hint" id="passwordMatch" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                    Passwords match
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_student" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Add Student
                </button>
                <a href="admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>

    <!-- Recent Students Added -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-users"></i>
                Recently Added Students
            </h2>
        </div>
        <div class="recent-students">
            <?php
            $recent_students = mysqli_query($conn, 
                "SELECT name, email FROM users 
                 WHERE role='student' 
                 ORDER BY id DESC LIMIT 5");
            
            if (mysqli_num_rows($recent_students) > 0) {
                echo '<div class="recent-list">';
                while ($row = mysqli_fetch_assoc($recent_students)) {
                    echo '<div class="recent-item">';
                    echo '<div class="student-info">';
                    echo '<div class="student-avatar">' . strtoupper(substr($row['name'], 0, 2)) . '</div>';
                    echo '<div class="student-details">';
                    echo '<strong>' . htmlspecialchars($row['name']) . '</strong>';
                    echo '<span>' . htmlspecialchars($row['email']) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="student-meta">';
                    echo '<span class="join-date">Student</span>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p class="text-center text-secondary">No students added yet.</p>';
            }
            ?>
        </div>
    </div>
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

.recent-students {
    padding: 0;
}

.recent-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.recent-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: var(--background);
    border-radius: var(--border-radius);
    border: 1px solid var(--border);
    transition: all 0.2s;
}

.recent-item:hover {
    border-color: var(--primary);
    transform: translateY(-1px);
}

.student-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.student-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.student-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.student-details strong {
    color: var(--text-primary);
    font-size: 0.875rem;
}

.student-details span {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.student-meta {
    display: flex;
    align-items: center;
}

.join-date {
    font-size: 0.75rem;
    color: var(--text-secondary);
    background: var(--surface);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    border: 1px solid var(--border);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .recent-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .student-meta {
        align-self: stretch;
        justify-content: center;
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

function toggleConfirmPassword() {
    const confirmPasswordInput = document.getElementById('confirm_password');
    const toggleIcon = document.getElementById('confirmPasswordToggleIcon');
    
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        confirmPasswordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Real-time validation
function validateName() {
    const nameInput = document.getElementById('name');
    const name = nameInput.value;
    
    // Remove any numbers or special characters except spaces
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
    
    // Allow only alphanumeric, @, ., _, %, +, - before @
    // Allow only alphanumeric, ., - after @
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

// Check password match
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchHint = document.getElementById('passwordMatch');
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            matchHint.style.display = 'flex';
            matchHint.style.color = 'var(--success)';
            matchHint.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
        } else {
            matchHint.style.display = 'flex';
            matchHint.style.color = 'var(--danger)';
            matchHint.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
        }
    } else {
        matchHint.style.display = 'none';
    }
}

// Add event listeners
document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);
document.getElementById('password').addEventListener('input', checkPasswordMatch);
document.getElementById('name').addEventListener('input', validateName);
document.getElementById('email').addEventListener('input', validateEmail);

// Clear form on successful submission
<?php if ($message_type === 'success'): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addStudentForm').reset();
    document.getElementById('passwordMatch').style.display = 'none';
});
<?php endif; ?>

// Form validation before submission
document.getElementById('addStudentForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    
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
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 4) {
        e.preventDefault();
        alert('Password must be at least 4 characters long!');
        return false;
    }
});
</script>
