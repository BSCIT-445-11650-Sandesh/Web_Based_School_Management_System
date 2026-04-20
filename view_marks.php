<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role'])) { header("Location: login.php"); exit(); }

// Handle search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if ($_SESSION['role'] == 'student') {
    $id = $_SESSION['user_id'];
    $query = "SELECT marks.id, users.name, subjects.subject_name, marks.marks
              FROM marks
              JOIN subjects ON marks.subject_id = subjects.id
              JOIN users ON marks.student_id = users.id
              WHERE marks.student_id = '$id'";
    
    if (!empty($search)) {
        $query .= " AND (users.name LIKE '%$search%' OR subjects.subject_name LIKE '%$search%')";
    }
    
    $query .= " ORDER BY subjects.subject_name";
} else {
    $query = "SELECT marks.id, users.name, subjects.subject_name, marks.marks
              FROM marks
              JOIN subjects ON marks.subject_id = subjects.id
              JOIN users ON marks.student_id = users.id";
    
    if (!empty($search)) {
        $query .= " WHERE (users.name LIKE '%$search%' OR subjects.subject_name LIKE '%$search%')";
    }
    
    $query .= " ORDER BY users.name, subjects.subject_name";
}

$result = mysqli_query($conn, $query);

// Calculate statistics
$total_marks = 0;
$subject_count = 0;
$student_count = 0;
$grades = ['A+' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];

$marks_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $marks_data[] = $row;
    $total_marks += $row['marks'];
    $subject_count++;
    
    // Calculate grade
    $grade = 'F';
    if ($row['marks'] >= 90) $grade = 'A+';
    elseif ($row['marks'] >= 80) $grade = 'A';
    elseif ($row['marks'] >= 70) $grade = 'B';
    elseif ($row['marks'] >= 60) $grade = 'C';
    elseif ($row['marks'] >= 50) $grade = 'D';
    
    $grades[$grade]++;
}

$average_marks = $subject_count > 0 ? round($total_marks / $subject_count, 1) : 0;

// Reset result pointer for display
mysqli_data_seek($result, 0);
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Marks & Performance</h1>
        <p class="text-secondary">
            <?php 
            if ($_SESSION['role'] == 'student') {
                echo 'View your academic performance and grades';
            } else {
                echo 'Manage and analyze student performance';
            }
            ?>
        </p>
    </div>

    <?php if ($_SESSION['role'] == 'student' && $subject_count > 0): ?>
        <!-- Performance Summary for Students -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?php echo $average_marks; ?></div>
                <div class="stat-label">Average Marks</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-value"><?php echo $subject_count; ?></div>
                <div class="stat-label">Subjects</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value"><?php echo $grades['A+'] + $grades['A']; ?></div>
                <div class="stat-label">Excellent Grades</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-value"><?php echo $average_marks; ?>%</div>
                <div class="stat-label">Performance</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <div class="table-header">
            <h2 class="card-title">
                <i class="fas fa-chart-bar"></i>
                <?php echo $_SESSION['role'] == 'student' ? 'My Marks' : 'All Marks'; ?>
            </h2>
            <div class="table-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Search by student or subject..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           onkeyup="performSearch()">
                </div>
                <?php if ($_SESSION['role'] != 'student'): ?>
                    <a href="add_marks.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Marks
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
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Performance</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($marks_data) > 0) {
                        foreach ($marks_data as $r) {
                            // Calculate grade
                            $grade = 'F';
                            $grade_class = 'danger';
                            if ($r['marks'] >= 90) { $grade = 'A+'; $grade_class = 'success'; }
                            elseif ($r['marks'] >= 80) { $grade = 'A'; $grade_class = 'success'; }
                            elseif ($r['marks'] >= 70) { $grade = 'B'; $grade_class = 'warning'; }
                            elseif ($r['marks'] >= 60) { $grade = 'C'; $grade_class = 'warning'; }
                            elseif ($r['marks'] >= 50) { $grade = 'D'; $grade_class = 'secondary'; }
                            
                            // Performance bar width
                            $performance_width = ($r['marks'] / 100) * 100;
                            $performance_color = $r['marks'] >= 70 ? 'success' : ($r['marks'] >= 50 ? 'warning' : 'danger');
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
                                    <div class="marks-value">
                                        <span class="marks-number"><?php echo $r['marks']; ?></span>
                                        <span class="marks-total">/100</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="grade-badge <?php echo $grade_class; ?>">
                                        <?php echo $grade; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="performance-bar">
                                        <div class="performance-fill <?php echo $performance_color; ?>" 
                                             style="width: <?php echo $performance_width; ?>%"></div>
                                        <span class="performance-text"><?php echo $r['marks']; ?>%</span>
                                    </div>
                                </td>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" onclick="editMarks(<?php echo $r['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php 
                        }
                    } else {
                        $colspan = $_SESSION['role'] == 'student' ? 4 : ($_SESSION['role'] == 'admin' ? 6 : 5);
                        echo '<tr><td colspan="' . $colspan . '" class="text-center text-secondary">No marks records found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (count($marks_data) > 0): ?>
            <div class="table-footer">
                <div class="record-count">
                    <i class="fas fa-info-circle"></i>
                    Showing <?php echo count($marks_data); ?> marks records
                </div>
                <?php if ($_SESSION['role'] == 'student'): ?>
                    <div class="performance-summary">
                        <strong>Overall Performance:</strong> 
                        <span class="performance-badge <?php echo $average_marks >= 70 ? 'success' : ($average_marks >= 50 ? 'warning' : 'danger'); ?>">
                            <?php echo $average_marks; ?>% - <?php echo $average_marks >= 70 ? 'Excellent' : ($average_marks >= 50 ? 'Good' : 'Needs Improvement'); ?>
                        </span>
                    </div>
                <?php endif; ?>
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
.subject-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.student-info i,
.subject-info i {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.marks-value {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
}

.marks-number {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.marks-total {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.grade-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
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

.performance-bar {
    position: relative;
    width: 100px;
    height: 8px;
    background: var(--border);
    border-radius: 4px;
    overflow: hidden;
}

.performance-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.performance-fill.success {
    background: var(--success);
}

.performance-fill.warning {
    background: var(--warning);
}

.performance-fill.danger {
    background: var(--danger);
}

.performance-text {
    position: absolute;
    right: -35px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.75rem;
    color: var(--text-secondary);
    white-space: nowrap;
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

.performance-summary {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.performance-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.performance-badge.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.performance-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.performance-badge.danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: none;
    }
    
    .table-footer {
        flex-direction: column;
        align-items: stretch;
    }
    
    .performance-bar {
        width: 80px;
    }
    
    .performance-text {
        right: -30px;
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

function editMarks(id) {
    window.location.href = 'edit_marks.php?id=' + id;
}

// Allow search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});
</script>
