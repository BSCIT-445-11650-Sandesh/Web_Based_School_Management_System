<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit(); }

include 'config.php';

// Get statistics for dashboard
$total_students = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='student'")->fetch_assoc()['count'];
$total_teachers = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='teacher'")->fetch_assoc()['count'];
$total_attendance = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance")->fetch_assoc()['count'];
$total_marks = mysqli_query($conn, "SELECT COUNT(*) as count FROM marks")->fetch_assoc()['count'];
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p class="text-secondary">Manage students, teachers, and track performance</p>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value"><?php echo $total_students; ?></div>
            <div class="stat-label">Total Students</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-value"><?php echo $total_teachers; ?></div>
            <div class="stat-label">Total Teachers</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value"><?php echo $total_attendance; ?></div>
            <div class="stat-label">Attendance Records</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value"><?php echo $total_marks; ?></div>
            <div class="stat-label">Marks Records</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
        </div>
        <div class="quick-actions">
            <div class="action-grid">
                <a href="add_student.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-content">
                        <h3>Add Student</h3>
                        <p>Register a new student</p>
                    </div>
                </a>

                <a href="add_teacher.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="action-content">
                        <h3>Add Teacher</h3>
                        <p>Register a new teacher</p>
                    </div>
                </a>

                <a href="remove_student.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-user-minus"></i>
                    </div>
                    <div class="action-content">
                        <h3>Remove Student</h3>
                        <p>Delete student records</p>
                    </div>
                </a>

                <a href="mark_attendance.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="action-content">
                        <h3>Mark Attendance</h3>
                        <p>Record student attendance</p>
                    </div>
                </a>

                <a href="add_marks.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-content">
                        <h3>Add Marks</h3>
                        <p>Enter student marks</p>
                    </div>
                </a>

                <a href="view_attendance.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="action-content">
                        <h3>View Attendance</h3>
                        <p>Check attendance records</p>
                    </div>
                </a>

                <a href="view_marks.php" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h3>View Marks</h3>
                        <p>Analyze student performance</p>
                    </div>
                </a>
            </div>
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

@media (max-width: 768px) {
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .action-item {
        padding: 1rem;
    }
}
</style>
