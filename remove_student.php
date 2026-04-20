<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit(); }

$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student' ORDER BY name");
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Manage Students</h1>
        <p class="text-secondary">View and remove students from the system</p>
    </div>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">';
        echo '<i class="fas fa-check-circle"></i>';
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        echo '</div>';
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-error">';
        echo '<i class="fas fa-times-circle"></i>';
        echo $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        echo '</div>';
    }
    ?>

    <div class="table-container">
        <div class="table-header">
            <h2 class="card-title">
                <i class="fas fa-users"></i>
                All Students
            </h2>
            <div class="table-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           id="studentSearch" 
                           placeholder="Search students..." 
                           onkeyup="filterStudents()">
                </div>
                <a href="add_student.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Add Student
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Performance</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <?php 
                    if (mysqli_num_rows($students) > 0) {
                        while($r = mysqli_fetch_assoc($students)) {
                            // Get student statistics
                            $student_id = $r['id'];
                            $total_attendance = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id")->fetch_assoc()['count'];
                            $present_days = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id AND status = 'present'")->fetch_assoc()['count'];
                            $attendance_percentage = $total_attendance > 0 ? round(($present_days / $total_attendance) * 100, 1) : 0;
                            
                            $avg_result = mysqli_query($conn, "SELECT AVG(marks) as avg FROM marks WHERE student_id = $student_id")->fetch_assoc();
                            $average_marks = $avg_result['avg'] ? round($avg_result['avg'], 1) : 0;
                            
                            $performance_color = $average_marks >= 70 ? 'success' : ($average_marks >= 50 ? 'warning' : 'danger');
                            $attendance_color = $attendance_percentage >= 75 ? 'success' : ($attendance_percentage >= 50 ? 'warning' : 'danger');
                        ?>
                            <tr data-student-name="<?php echo strtolower($r['name']); ?>" data-student-email="<?php echo strtolower($r['email']); ?>">
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?php echo strtoupper(substr($r['name'], 0, 2)); ?>
                                        </div>
                                        <div class="student-details">
                                            <div class="student-name"><?php echo htmlspecialchars($r['name']); ?></div>
                                            <div class="student-id">ID: <?php echo $r['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="email-info">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($r['email']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="performance-info">
                                        <span class="performance-score <?php echo $performance_color; ?>">
                                            <?php echo $average_marks; ?>%
                                        </span>
                                        <div class="performance-label">
                                            <?php echo $average_marks >= 70 ? 'Excellent' : ($average_marks >= 50 ? 'Good' : 'Needs Work'); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="attendance-info">
                                        <span class="attendance-score <?php echo $attendance_color; ?>">
                                            <?php echo $attendance_percentage; ?>%
                                        </span>
                                        <div class="attendance-label">
                                            <?php echo $present_days; ?>/<?php echo $total_attendance; ?> days
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_student.php?id=<?php echo $r['id']; ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="Edit Student">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view_marks.php?search=<?php echo urlencode($r['name']); ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="View Marks">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="view_attendance.php?search=<?php echo urlencode($r['name']); ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="View Attendance">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        <a href="delete_student.php?id=<?php echo $r['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirmDelete('<?php echo htmlspecialchars($r['name']); ?>')"
                                           title="Delete Student">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-secondary">No students found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (mysqli_num_rows($students) > 0): ?>
            <div class="table-footer">
                <div class="record-count">
                    <i class="fas fa-info-circle"></i>
                    Showing <?php echo mysqli_num_rows($students); ?> students
                </div>
                <div class="bulk-actions">
                    <button class="btn btn-sm btn-secondary" onclick="exportStudents()">
                        <i class="fas fa-download"></i>
                        Export List
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Summary -->
    <div class="dashboard-grid" style="margin-top: 2rem;">
        <?php
        // Calculate overall statistics
        $total_students = mysqli_num_rows($students);
        $avg_attendance = 0;
        $avg_marks = 0;
        
        if ($total_students > 0) {
            mysqli_data_seek($students, 0);
            $total_attendance_sum = 0;
            $total_marks_sum = 0;
            $student_count = 0;
            
            while($r = mysqli_fetch_assoc($students)) {
                $student_id = $r['id'];
                $attendance_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id AND status = 'present'")->fetch_assoc();
                $total_attendance_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id")->fetch_assoc();
                
                if ($total_attendance_result['count'] > 0) {
                    $total_attendance_sum += ($attendance_result['count'] / $total_attendance_result['count']) * 100;
                }
                
                $marks_result = mysqli_query($conn, "SELECT AVG(marks) as avg FROM marks WHERE student_id = $student_id")->fetch_assoc();
                if ($marks_result['avg']) {
                    $total_marks_sum += $marks_result['avg'];
                    $student_count++;
                }
            }
            
            $avg_attendance = $total_students > 0 ? round($total_attendance_sum / $total_students, 1) : 0;
            $avg_marks = $student_count > 0 ? round($total_marks_sum / $student_count, 1) : 0;
        }
        ?>
        
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value"><?php echo $total_students; ?></div>
            <div class="stat-label">Total Students</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value"><?php echo $avg_marks; ?>%</div>
            <div class="stat-label">Average Marks</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value"><?php echo $avg_attendance; ?>%</div>
            <div class="stat-label">Average Attendance</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-value">
                <?php 
                $excellent_count = 0;
                mysqli_data_seek($students, 0);
                while($r = mysqli_fetch_assoc($students)) {
                    $student_id = $r['id'];
                    $marks_result = mysqli_query($conn, "SELECT AVG(marks) as avg FROM marks WHERE student_id = $student_id")->fetch_assoc();
                    if ($marks_result['avg'] && $marks_result['avg'] >= 70) {
                        $excellent_count++;
                    }
                }
                echo $excellent_count;
                ?>
            </div>
            <div class="stat-label">Excellent Performers</div>
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

.table-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
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

.student-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.student-id {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.email-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.email-info i {
    font-size: 0.75rem;
}

.performance-info,
.attendance-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.performance-score,
.attendance-score {
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.performance-score.success,
.attendance-score.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.performance-score.warning,
.attendance-score.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.performance-score.danger,
.attendance-score.danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.performance-label,
.attendance-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.table-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border);
    background: var(--background);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.record-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.bulk-actions {
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: none;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .table-footer {
        flex-direction: column;
        align-items: stretch;
    }
    
    .student-info {
        gap: 0.75rem;
    }
    
    .student-avatar {
        width: 40px;
        height: 40px;
        font-size: 0.875rem;
    }
}
</style>

<script>
// Filter students
function filterStudents() {
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#studentsTableBody tr');
    
    rows.forEach(row => {
        const studentName = row.dataset.studentName || '';
        const studentEmail = row.dataset.studentEmail || '';
        
        if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Confirm delete
function confirmDelete(studentName) {
    return confirm(`Are you sure you want to delete "${studentName}"?\n\nThis action will permanently remove the student and all their attendance and marks records.`);
}

// Export students (placeholder)
function exportStudents() {
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    
    // Create CSV content
    let csvContent = "ID,Name,Email,Performance,Attendance\n";
    
    // Get all rows (including hidden ones for now)
    const rows = document.querySelectorAll('#studentsTableBody tr');
    
    rows.forEach(row => {
        // Check if row should be included based on search
        const studentName = (row.dataset.studentName || '').toLowerCase();
        const studentEmail = (row.dataset.studentEmail || '').toLowerCase();
        
        // Include row if no search or if it matches search
        if (!searchTerm || studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
            const nameElement = row.querySelector('.student-name');
            const emailElement = row.querySelector('.email-info');
            const performanceElement = row.querySelector('.performance-score');
            const attendanceElement = row.querySelector('.attendance-score');
            
            const studentName = nameElement ? nameElement.textContent.trim() : '';
            const studentEmail = emailElement ? emailElement.textContent.trim().replace(/\s+/g, ' ').trim() : '';
            const performanceText = performanceElement ? performanceElement.textContent.trim() : '';
            const attendanceText = attendanceElement ? attendanceElement.textContent.trim() : '';
            
            // Extract just the student ID from the ID element
            const idElement = row.querySelector('.student-id');
            const studentId = idElement ? idElement.textContent.replace('ID: ', '').trim() : '';
            
            csvContent += `"${studentId}","${studentName}","${studentEmail}","${performanceText}","${attendanceText}"\n`;
        }
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'students_list.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    alert('Students list exported successfully!');
}

// Clear search on Escape key
document.getElementById('studentSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        this.value = '';
        filterStudents();
    }
});
</script>
