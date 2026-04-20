<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','teacher'])) {
    header("Location: login.php"); exit();
}

$message = "";
$message_type = "";

// Handle form submission
if (isset($_POST['update_marks'])) {
    $marks_id = $_POST['marks_id'];
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $marks = $_POST['marks'];

    // Validate marks range
    if ($marks < 0 || $marks > 100) {
        $message = "Marks must be between 0 and 100";
        $message_type = "error";
    } else {
        // Update marks record
        $marks_id = mysqli_real_escape_string($conn, $marks_id);
        $student_id = mysqli_real_escape_string($conn, $student_id);
        $subject_id = mysqli_real_escape_string($conn, $subject_id);
        $marks = mysqli_real_escape_string($conn, $marks);
        
        $update_query = "UPDATE marks SET student_id = '$student_id', subject_id = '$subject_id', marks = '$marks' WHERE id = '$marks_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $message = "Marks updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}

// Get marks record to edit
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $marks_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT m.id, m.student_id, m.subject_id, m.marks, u.name as student_name, s.subject_name 
              FROM marks m 
              JOIN users u ON m.student_id = u.id 
              JOIN subjects s ON m.subject_id = s.id 
              WHERE m.id = '$marks_id'";
    
    $result = mysqli_query($conn, $query);
    $marks_record = mysqli_fetch_assoc($result);
    
    if (!$marks_record) {
        $message = "Marks record not found";
        $message_type = "error";
    }
} else {
    header("Location: view_marks.php");
    exit();
}

$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY name");
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY subject_name");
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Edit Marks</h1>
        <p class="text-secondary">Update student marks information</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($marks_record)): ?>
        <form method="POST" class="modern-form">
            <input type="hidden" name="marks_id" value="<?php echo $marks_record['id']; ?>">
            
            <div class="form-group">
                <label for="student_id" class="form-label">
                    <i class="fas fa-user-graduate"></i>
                    Select Student
                </label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <?php while($r = mysqli_fetch_assoc($students)): ?>
                        <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $marks_record['student_id']) ? 'selected' : ''; ?>>
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
                        <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $marks_record['subject_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($r['subject_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="marks" class="form-label">
                    <i class="fas fa-chart-line"></i>
                    Marks (0-100)
                </label>
                <input type="number" 
                       id="marks" 
                       name="marks" 
                       class="form-input" 
                       value="<?php echo $marks_record['marks']; ?>"
                       placeholder="Enter marks (e.g., 85)" 
                       min="0" 
                       max="100" 
                       required>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Enter marks between 0 and 100
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_marks" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Marks
                </button>
                <a href="view_marks.php" class="btn btn-secondary">
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
// Auto-validate marks input
document.getElementById('marks').addEventListener('input', function(e) {
    const value = parseInt(e.target.value);
    if (value > 100) {
        e.target.value = 100;
    } else if (value < 0) {
        e.target.value = 0;
    }
});
</script>
