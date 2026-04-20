<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','teacher'])) {
    header("Location: login.php"); exit();
}

$message = "";
$message_type = "";

// Handle form submission
if (isset($_POST['update_attendance'])) {
    $attendance_id = $_POST['attendance_id'];
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Validate date
    if (empty($date)) {
        $message = "Date is required";
        $message_type = "error";
    } else {
        // Update attendance record
        $attendance_id = mysqli_real_escape_string($conn, $attendance_id);
        $student_id = mysqli_real_escape_string($conn, $student_id);
        $subject_id = mysqli_real_escape_string($conn, $subject_id);
        $date = mysqli_real_escape_string($conn, $date);
        $status = mysqli_real_escape_string($conn, $status);
        
        $update_query = "UPDATE attendance SET student_id = '$student_id', subject_id = '$subject_id', date = '$date', status = '$status' WHERE id = '$attendance_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $message = "Attendance updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}

// Get attendance record to edit
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $attendance_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT a.id, a.student_id, a.subject_id, a.date, a.status, u.name as student_name, s.subject_name 
              FROM attendance a 
              JOIN users u ON a.student_id = u.id 
              JOIN subjects s ON a.subject_id = s.id 
              WHERE a.id = '$attendance_id'";
    
    $result = mysqli_query($conn, $query);
    $attendance_record = mysqli_fetch_assoc($result);
    
    if (!$attendance_record) {
        $message = "Attendance record not found";
        $message_type = "error";
    }
} else {
    header("Location: view_attendance.php");
    exit();
}

$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY name");
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY subject_name");
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Edit Attendance</h1>
        <p class="text-secondary">Update student attendance information</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($attendance_record)): ?>
        <form method="POST" class="modern-form">
            <input type="hidden" name="attendance_id" value="<?php echo $attendance_record['id']; ?>">
            
            <div class="form-group">
                <label for="student_id" class="form-label">
                    <i class="fas fa-user-graduate"></i>
                    Select Student
                </label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <?php while($r = mysqli_fetch_assoc($students)): ?>
                        <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $attendance_record['student_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($r['name']); ?> (<?php echo htmlspecialchars($r['email']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subject_id" class="form-label">
                    <i class="fas fa-book"></i>
                    Select Subject
                </label>
                <select name="subject_id" id="subject_id" class="form-select" required>
                    <?php while($r = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $attendance_record['subject_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($r['subject_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date" class="form-label">
                    <i class="fas fa-calendar-alt"></i>
                    Date
                </label>
                <input type="date" 
                       id="date" 
                       name="date" 
                       class="form-input" 
                       value="<?php echo $attendance_record['date']; ?>"
                       required>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Select date for attendance
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">
                    <i class="fas fa-user-check"></i>
                    Attendance Status
                </label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="status" value="present" <?php echo ($attendance_record['status'] == 'present') ? 'checked' : ''; ?>>
                        <span class="radio-label">
                            <i class="fas fa-check-circle"></i>
                            Present
                        </span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="status" value="absent" <?php echo ($attendance_record['status'] == 'absent') ? 'checked' : ''; ?>>
                        <span class="radio-label">
                            <i class="fas fa-times-circle"></i>
                            Absent
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_attendance" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Attendance
                </button>
                <a href="view_attendance.php" class="btn btn-secondary">
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

.radio-group {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.radio-option {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.2s;
    flex: 1;
}

.radio-option:hover {
    border-color: var(--primary);
}

.radio-option input[type="radio"] {
    display: none;
}

.radio-option input[type="radio"]:checked + .radio-label {
    color: var(--primary);
}

.radio-option input[type="radio"]:checked {
    border-color: var(--primary);
    background: rgba(79, 70, 229, 0.05);
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--text-secondary);
    transition: color 0.2s;
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
    
    .radio-group {
        flex-direction: column;
    }
}
</style>
