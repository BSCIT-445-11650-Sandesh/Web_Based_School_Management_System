<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','teacher'])) {
    header("Location: login.php"); exit();
}

$message = "";
$message_type = "";

if (isset($_POST['mark'])) {
    $sid   = $_POST['student_id'];
    $subid = $_POST['subject_id'];
    $date  = $_POST['date'];
    $stat  = $_POST['status'];

    // Check if attendance already exists for this student, subject, and date
    $check_query = "SELECT id FROM attendance WHERE student_id = '$sid' AND subject_id = '$subid' AND date = '$date'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $message = "Attendance already marked for this student, subject, and date";
        $message_type = "warning";
    } else {
        if (mysqli_query($conn, "INSERT INTO attendance (student_id, subject_id, date, status) VALUES ('$sid','$subid','$date','$stat')")) {
            $message = "Attendance marked successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}

// Handle batch attendance submission
if (isset($_POST['batch_mark'])) {
    $subject_id = $_POST['batch_subject'];
    $date = $_POST['batch_date'];
    $attendance_data = $_POST['attendance'] ?? [];
    
    $success_count = 0;
    $error_count = 0;
    $duplicate_count = 0;
    
    foreach ($attendance_data as $student_id => $status) {
        // Sanitize inputs
        $student_id = mysqli_real_escape_string($conn, $student_id);
        $status = mysqli_real_escape_string($conn, $status);
        $subject_id = mysqli_real_escape_string($conn, $subject_id);
        $date = mysqli_real_escape_string($conn, $date);
        
        // Check if attendance already exists
        $check_query = "SELECT id FROM attendance WHERE student_id = '$student_id' AND subject_id = '$subject_id' AND date = '$date'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $duplicate_count++;
        } else {
            // Insert attendance record
            if (mysqli_query($conn, "INSERT INTO attendance (student_id, subject_id, date, status) VALUES ('$student_id','$subject_id','$date','$status')")) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }
    
    // Generate appropriate message
    if ($success_count > 0 && $error_count == 0 && $duplicate_count == 0) {
        $message = "Batch attendance marked successfully! $success_count students processed.";
        $message_type = "success";
    } elseif ($duplicate_count > 0 && $success_count == 0) {
        $message = "All attendance records already exist for this date and subject.";
        $message_type = "warning";
    } elseif ($error_count > 0) {
        $message = "Batch attendance partially completed. $success_count successful, $error_count failed, $duplicate_count duplicates.";
        $message_type = "warning";
    } else {
        $message = "Batch attendance completed. $success_count successful, $duplicate_count duplicates skipped.";
        $message_type = "success";
    }
}

$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY name");
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY subject_name");
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Mark Attendance</h1>
        <p class="text-secondary">Record student attendance for different subjects</p>
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
                <label for="date" class="form-label">
                    <i class="fas fa-calendar-alt"></i>
                    Date
                </label>
                <input type="date" 
                       id="date" 
                       name="date" 
                       class="form-input" 
                       required>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Select the date for attendance
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">
                    <i class="fas fa-user-check"></i>
                    Attendance Status
                </label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="status" value="present" checked>
                        <span class="radio-label">
                            <i class="fas fa-check-circle"></i>
                            Present
                        </span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="status" value="absent">
                        <span class="radio-label">
                            <i class="fas fa-times-circle"></i>
                            Absent
                        </span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="mark" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i>
                    Mark Attendance
                </button>
                <a href="view_attendance.php" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    View Attendance
                </a>
            </div>
        </form>
    </div>

    <!-- Quick Batch Attendance -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-users"></i>
                Quick Batch Attendance
            </h2>
            <p class="text-secondary" style="margin: 0; font-size: 0.875rem;">
                Mark attendance for all students in a subject at once
            </p>
        </div>
        <div class="batch-attendance">
            <form method="POST" class="batch-form" id="batchForm">
                <div class="batch-controls">
                    <div class="form-group">
                        <label for="batch_subject" class="form-label">
                            <i class="fas fa-book"></i>
                            Subject
                        </label>
                        <select name="batch_subject" id="batch_subject" class="form-select" required>
                            <option value="">Select subject...</option>
                            <?php 
                            mysqli_data_seek($subjects, 0);
                            while($r = mysqli_fetch_assoc($subjects)): ?>
                                <option value="<?php echo $r['id']; ?>">
                                    <?php echo htmlspecialchars($r['subject_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="batch_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Date
                        </label>
                        <input type="date" 
                               id="batch_date" 
                               name="batch_date" 
                               class="form-input" 
                               required>
                    </div>
                    
                    <button type="button" class="btn btn-primary" onclick="loadBatchStudents()">
                        <i class="fas fa-users"></i>
                        Load Students
                    </button>
                </div>

                <div id="batchStudents" style="display: none;">
                    <div class="batch-header">
                        <h3>Mark Attendance for All Students</h3>
                        <div class="batch-actions">
                            <button type="button" class="btn btn-sm btn-success" onclick="markAllPresent()">
                                <i class="fas fa-check"></i>
                                All Present
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="markAllAbsent()">
                                <i class="fas fa-times"></i>
                                All Absent
                            </button>
                        </div>
                    </div>
                    <div class="students-grid" id="studentsGrid">
                        <!-- Students will be loaded here via JavaScript -->
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="batch_mark" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save All Attendance
                        </button>
                    </div>
                </div>
            </form>
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

.batch-attendance {
    padding: 0;
}

.batch-controls {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
    margin-bottom: 2rem;
}

.batch-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.batch-header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.batch-actions {
    display: flex;
    gap: 0.5rem;
}

.students-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.student-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--border-radius);
    transition: all 0.2s;
}

.student-card:hover {
    border-color: var(--primary);
}

.student-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.student-details {
    display: flex;
    flex-direction: column;
}

.student-name {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.student-email {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.attendance-toggle {
    display: flex;
    gap: 0.25rem;
}

.toggle-btn {
    padding: 0.375rem 0.75rem;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-secondary);
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.2s;
}

.toggle-btn:first-child {
    border-radius: 4px 0 0 4px;
}

.toggle-btn:last-child {
    border-radius: 0 4px 4px 0;
}

.toggle-btn.active.present {
    background: var(--success);
    color: white;
    border-color: var(--success);
}

.toggle-btn.active.absent {
    background: var(--danger);
    color: white;
    border-color: var(--danger);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .radio-group {
        flex-direction: column;
    }
    
    .batch-controls {
        grid-template-columns: 1fr;
    }
    
    .batch-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .students-grid {
        grid-template-columns: 1fr;
    }
    
    .student-card {
        padding: 0.75rem;
    }
}
</style>

<script>
// Set today's date as default
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').value = today;
    document.getElementById('batch_date').value = today;
});

// Load students for batch attendance
function loadBatchStudents() {
    const subjectId = document.getElementById('batch_subject').value;
    const date = document.getElementById('batch_date').value;
    
    if (!subjectId || !date) {
        alert('Please select both subject and date');
        return;
    }
    
    // Show loading state
    const studentsGrid = document.getElementById('studentsGrid');
    studentsGrid.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">Loading students...</p>';
    document.getElementById('batchStudents').style.display = 'block';
    
    // Fetch students (in real app, this would be an AJAX call)
    <?php
    $students_list = [];
    mysqli_data_seek($students, 0);
    while($r = mysqli_fetch_assoc($students)) {
        $students_list[] = [
            'id' => $r['id'],
            'name' => $r['name'],
            'email' => $r['email']
        ];
    }
    echo 'const students = ' . json_encode($students_list) . ';';
    ?>
    
    setTimeout(() => {
        let html = '';
        students.forEach(student => {
            const initials = student.name.split(' ').map(n => n[0]).join('').toUpperCase();
            html += `
                <div class="student-card">
                    <div class="student-info">
                        <div class="student-avatar">${initials}</div>
                        <div class="student-details">
                            <div class="student-name">${student.name}</div>
                            <div class="student-email">${student.email}</div>
                        </div>
                    </div>
                    <div class="attendance-toggle">
                        <input type="hidden" name="attendance[${student.id}]" value="present" id="att_${student.id}">
                        <button type="button" class="toggle-btn present active" onclick="toggleAttendance(${student.id}, 'present')">
                            <i class="fas fa-check"></i> Present
                        </button>
                        <button type="button" class="toggle-btn absent" onclick="toggleAttendance(${student.id}, 'absent')">
                            <i class="fas fa-times"></i> Absent
                        </button>
                    </div>
                </div>
            `;
        });
        studentsGrid.innerHTML = html;
    }, 500);
}

// Toggle attendance status
function toggleAttendance(studentId, status) {
    const input = document.getElementById(`att_${studentId}`);
    const presentBtn = input.nextElementSibling;
    const absentBtn = presentBtn.nextElementSibling;
    
    input.value = status;
    
    if (status === 'present') {
        presentBtn.classList.add('active', 'present');
        absentBtn.classList.remove('active', 'absent');
    } else {
        presentBtn.classList.remove('active', 'present');
        absentBtn.classList.add('active', 'absent');
    }
}

// Mark all as present
function markAllPresent() {
    document.querySelectorAll('.attendance-toggle').forEach(toggle => {
        const input = toggle.querySelector('input');
        const presentBtn = toggle.querySelector('.present');
        const absentBtn = toggle.querySelector('.absent');
        
        input.value = 'present';
        presentBtn.classList.add('active', 'present');
        absentBtn.classList.remove('active', 'absent');
    });
}

// Mark all as absent
function markAllAbsent() {
    document.querySelectorAll('.attendance-toggle').forEach(toggle => {
        const input = toggle.querySelector('input');
        const presentBtn = toggle.querySelector('.present');
        const absentBtn = toggle.querySelector('.absent');
        
        input.value = 'absent';
        presentBtn.classList.remove('active', 'present');
        absentBtn.classList.add('active', 'absent');
    });
}

// Clear form on successful submission
<?php if ($message_type === 'success'): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.modern-form').reset();
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').value = today;
});
<?php endif; ?>
</script>
