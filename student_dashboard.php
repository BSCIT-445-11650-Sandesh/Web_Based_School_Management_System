<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') { header("Location: login.php"); exit(); }

include 'config.php';

// Get student-specific statistics
$student_id = $_SESSION['user_id'];
$total_attendance = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id")->fetch_assoc()['count'];
$present_days = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = $student_id AND status = 'present'")->fetch_assoc()['count'];
$total_marks = mysqli_query($conn, "SELECT COUNT(*) as count FROM marks WHERE student_id = $student_id")->fetch_assoc()['count'];
$average_marks = 0;
if ($total_marks > 0) {
    $avg_result = mysqli_query($conn, "SELECT AVG(marks) as avg FROM marks WHERE student_id = $student_id")->fetch_assoc();
    $average_marks = round($avg_result['avg'], 1);
}
$attendance_percentage = 0;
if ($total_attendance > 0) {
    $attendance_percentage = round(($present_days / $total_attendance) * 100, 1);
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="dashboard-header">
        <h1>Welcome back, <?php echo $_SESSION['name']; ?>! 👋</h1>
        <p class="text-secondary">Track your attendance and academic performance</p>
    </div>

    <!-- Personal Statistics Cards -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value"><?php echo $attendance_percentage; ?>%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value"><?php echo $average_marks; ?></div>
            <div class="stat-label">Average Marks</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-value"><?php echo $present_days; ?></div>
            <div class="stat-label">Days Present</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-value"><?php echo $total_marks; ?></div>
            <div class="stat-label">Total Marks</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-bolt"></i>
                My Academic Records
            </h2>
        </div>
        <div class="quick-actions">
            <div class="action-grid">
                <a href="view_attendance.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="action-content">
                        <h3>My Attendance</h3>
                        <p>View your attendance history</p>
                    </div>
                </a>

                <a href="view_marks.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h3>My Marks</h3>
                        <p>Check your academic performance</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Summary -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-history"></i>
                Recent Activity
            </h2>
        </div>
        <div class="activity-summary">
            <?php
            // Get recent attendance
            $recent_attendance = mysqli_query($conn, "SELECT a.date, a.status, s.subject_name FROM attendance a JOIN subjects s ON a.subject_id = s.id WHERE a.student_id = $student_id ORDER BY a.date DESC LIMIT 3");
            if (mysqli_num_rows($recent_attendance) > 0) {
                echo '<div class="activity-list">';
                while ($row = mysqli_fetch_assoc($recent_attendance)) {
                    $status_icon = $row['status'] == 'present' ? 'check-circle' : 'times-circle';
                    $status_color = $row['status'] == 'present' ? 'success' : 'danger';
                    echo '<div class="activity-item">';
                    echo '<div class="activity-icon ' . $status_color . '"><i class="fas fa-' . $status_icon . '"></i></div>';
                    echo '<div class="activity-content">';
                    echo '<strong>' . $row['subject_name'] . '</strong>';
                    echo '<span class="activity-date">' . date('M d, Y', strtotime($row['date'])) . '</span>';
                    echo '</div>';
                    echo '<div class="activity-status ' . $status_color . '">' . ucfirst($row['status']) . '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p class="text-center text-secondary">No recent attendance records found.</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.text-secondary {
    color: var(--text-secondary);
}

.quick-actions {
    padding: 0;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--background);
    border-radius: var(--border-radius);
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid var(--border);
}

.action-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-content h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.action-content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

.activity-summary {
    padding: 0;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--background);
    border-radius: var(--border-radius);
    border: 1px solid var(--border);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.activity-icon.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.activity-icon.danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.activity-content {
    flex: 1;
}

.activity-content strong {
    display: block;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.activity-date {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.activity-status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.activity-status.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.activity-status.danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

@media (max-width: 768px) {
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .action-item {
        padding: 1rem;
    }
    
    .activity-item {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .activity-status {
        align-self: flex-start;
    }
}
</style>
