<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','teacher'])) {
    header("Location: login.php"); exit();
}

$message = "";
$message_type = "";

if (isset($_POST['add_marks'])) {
    $sid = $_POST['student_id'];
    $subid = $_POST['subject_id'];
    $marks = $_POST['marks'];

    // Validate marks range
    if ($marks < 0 || $marks > 100) {
        $message = "Marks must be between 0 and 100";
        $message_type = "error";
    } else {
        // Check if marks already exist for this student and subject
        $check_query = "SELECT id FROM marks WHERE student_id = '$sid' AND subject_id = '$subid'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $message = "Marks already exist for this student and subject combination";
            $message_type = "warning";
        } else {
            if (mysqli_query($conn, "INSERT INTO marks (student_id, subject_id, marks) VALUES ('$sid','$subid','$marks')")) {
                $message = "Marks added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . mysqli_error($conn);
                $message_type = "error";
            }
        }
    }
}

$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY name");
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY subject_name");
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Add Student Marks</h1>
        <p class="text-secondary">Enter marks for students in different subjects</p>
    </div>

    <div class="form-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="modern-form">
            <div class="form-group">
                <label for="student_id" class="form-label">
                    <i class="fas fa-user-graduate"></i>
                    Select Student
                </label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <option value="">Choose a student...</option>
                    <?php while($r = mysqli_fetch_assoc($students)): ?>
                        <option value="<?php echo $r['id']; ?>">
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
                    <option value="">Choose a subject...</option>
                    <?php while($r = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?php echo $r['id']; ?>">
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
                <button type="submit" name="add_marks" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Add Marks
                </button>
                <a href="view_marks.php" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    View All Marks
                </a>
            </div>
        </form>
    </div>

    <!-- Recent Marks Added -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-history"></i>
                Recently Added Marks
            </h2>
        </div>
        <div class="recent-marks">
            <?php
            $recent_marks = mysqli_query($conn, 
                "SELECT m.marks, u.name as student_name, s.subject_name, m.id 
                 FROM marks m 
                 JOIN users u ON m.student_id = u.id 
                 JOIN subjects s ON m.subject_id = s.id 
                 ORDER BY m.id DESC LIMIT 5");
            
            if (mysqli_num_rows($recent_marks) > 0) {
                echo '<div class="recent-list">';
                while ($row = mysqli_fetch_assoc($recent_marks)) {
                    $grade = 'F';
                    $grade_class = 'danger';
                    if ($row['marks'] >= 90) { $grade = 'A+'; $grade_class = 'success'; }
                    elseif ($row['marks'] >= 80) { $grade = 'A'; $grade_class = 'success'; }
                    elseif ($row['marks'] >= 70) { $grade = 'B'; $grade_class = 'warning'; }
                    elseif ($row['marks'] >= 60) { $grade = 'C'; $grade_class = 'warning'; }
                    elseif ($row['marks'] >= 50) { $grade = 'D'; $grade_class = 'secondary'; }
                    
                    echo '<div class="recent-item">';
                    echo '<div class="recent-info">';
                    echo '<strong>' . htmlspecialchars($row['student_name']) . '</strong>';
                    echo '<span>' . htmlspecialchars($row['subject_name']) . '</span>';
                    echo '</div>';
                    echo '<div class="recent-marks-value">';
                    echo '<span class="marks-number">' . $row['marks'] . '</span>';
                    echo '<span class="grade-badge ' . $grade_class . '">' . $grade . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p class="text-center text-secondary">No marks added yet.</p>';
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

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

.recent-marks {
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
}

.recent-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.recent-info strong {
    color: var(--text-primary);
    font-size: 0.875rem;
}

.recent-info span {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.recent-marks-value {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.marks-number {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.grade-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.625rem;
    font-weight: 600;
    text-transform: uppercase;
}

.grade-badge.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.grade-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.grade-badge.secondary {
    background: rgba(107, 114, 128, 0.1);
    color: var(--text-secondary);
}

.grade-badge.danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .recent-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .recent-marks-value {
        align-self: stretch;
        justify-content: space-between;
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

// Clear form on successful submission
<?php if ($message_type === 'success'): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.modern-form').reset();
});
<?php endif; ?>
</script>
