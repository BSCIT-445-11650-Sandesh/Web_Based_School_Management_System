<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role'])) { header("Location: login.php"); exit(); }

// Handle search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if ($_SESSION['role'] == 'student') {
    $id = $_SESSION['user_id'];
    $query = "SELECT attendance.id, users.name, subjects.subject_name, attendance.date, attendance.status
              FROM attendance
              JOIN subjects ON attendance.subject_id = subjects.id
              JOIN users ON attendance.student_id = users.id
              WHERE attendance.student_id = '$id'";
    
    if (!empty($search)) {
        $query .= " AND (users.name LIKE '%$search%' OR subjects.subject_name LIKE '%$search%' OR attendance.date LIKE '%$search%')";
    }
    
    $query .= " ORDER BY attendance.date DESC";
} else {
    $query = "SELECT attendance.id, users.name, subjects.subject_name, attendance.date, attendance.status
              FROM attendance
              JOIN subjects ON attendance.subject_id = subjects.id
              JOIN users ON attendance.student_id = users.id";
    
    if (!empty($search)) {
        $query .= " WHERE (users.name LIKE '%$search%' OR subjects.subject_name LIKE '%$search%' OR attendance.date LIKE '%$search%')";
    }
    
    $query .= " ORDER BY attendance.date DESC";
}

$result = mysqli_query($conn, $query);
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Attendance Records</h1>
        <p class="text-secondary">
            <?php 
            if ($_SESSION['role'] == 'student') {
                echo 'View your attendance history';
            } else {
                echo 'Manage and view all attendance records';
            }
            ?>
        </p>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2 class="card-title">
                <i class="fas fa-calendar-alt"></i>
                <?php echo $_SESSION['role'] == 'student' ? 'My Attendance' : 'All Attendance'; ?>
            </h2>
            <div class="table-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Search by student, subject, or date..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           onkeyup="performSearch()">
                </div>
                <?php if ($_SESSION['role'] != 'student'): ?>
                    <a href="mark_attendance.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Mark Attendance
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <?php if ($_SESSION['role'] != 'student'): ?>
                            <th>Student</th>
                        <?php endif; ?>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while($r = mysqli_fetch_assoc($result)) { 
                            $status_class = $r['status'] == 'present' ? 'success' : 'danger';
                            $status_icon = $r['status'] == 'present' ? 'check-circle' : 'times-circle';
                        ?>
                            <tr>
                                <?php if ($_SESSION['role'] != 'student'): ?>
                                    <td>
                                        <div class="student-info">
                                            <i class="fas fa-user-circle"></i>
                                            <?php echo htmlspecialchars($r['name']); ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <div class="subject-info">
                                        <i class="fas fa-book"></i>
                                        <?php echo htmlspecialchars($r['subject_name']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M d, Y', strtotime($r['date'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="fas fa-<?php echo $status_icon; ?>"></i>
                                        <?php echo ucfirst($r['status']); ?>
                                    </span>
                                </td>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" onclick="editAttendance(<?php echo $r['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php 
                        }
                    } else {
                        $colspan = $_SESSION['role'] == 'student' ? 3 : ($_SESSION['role'] == 'admin' ? 5 : 4);
                        echo '<tr><td colspan="' . $colspan . '" class="text-center text-secondary">No attendance records found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-footer">
                <div class="record-count">
                    <i class="fas fa-info-circle"></i>
                    Showing <?php echo mysqli_num_rows($result); ?> attendance records
                </div>
            </div>
        <?php endif; ?>
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

.student-info,
.subject-info,
.date-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.student-info i,
.subject-info i,
.date-info i {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.status-badge.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-badge.danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.table-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border);
    background: var(--background);
}

.record-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: none;
    }
}
</style>

<script>
function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('search', searchValue);
    window.location.href = currentUrl.toString();
}

function editAttendance(id) {
    window.location.href = 'edit_attendance.php?id=' + id;
}

// Allow search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});
</script>
