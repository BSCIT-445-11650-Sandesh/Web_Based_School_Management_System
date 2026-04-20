<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit(); }

$teachers = mysqli_query($conn, "SELECT * FROM users WHERE role='teacher' ORDER BY name");
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Manage Teachers</h1>
        <p class="text-secondary">View and remove teachers from the system</p>
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
                <i class="fas fa-chalkboard-teacher"></i>
                All Teachers
            </h2>
            <div class="table-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           id="teacherSearch" 
                           placeholder="Search teachers..." 
                           onkeyup="filterTeachers()">
                </div>
                <a href="add_teacher.php" class="btn btn-primary">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Add Teacher
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="teachersTableBody">
                    <?php 
                    if (mysqli_num_rows($teachers) > 0) {
                        while($r = mysqli_fetch_assoc($teachers)) {
                            ?>
                            <tr data-teacher-name="<?php echo strtolower($r['name']); ?>" data-teacher-email="<?php echo strtolower($r['email']); ?>">
                                <td>
                                    <div class="teacher-info">
                                        <div class="teacher-avatar">
                                            <?php echo strtoupper(substr($r['name'], 0, 2)); ?>
                                        </div>
                                        <div class="teacher-details">
                                            <div class="teacher-name"><?php echo htmlspecialchars($r['name']); ?></div>
                                            <div class="teacher-id">ID: <?php echo $r['id']; ?></div>
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
                                    <div class="action-buttons">
                                        <a href="edit_teacher.php?id=<?php echo $r['id']; ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="Edit Teacher">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_teacher.php?id=<?php echo $r['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirmDelete('<?php echo htmlspecialchars($r['name']); ?>')"
                                           title="Delete Teacher">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-center text-secondary">No teachers found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (mysqli_num_rows($teachers) > 0): ?>
            <div class="table-footer">
                <div class="record-count">
                    <i class="fas fa-info-circle"></i>
                    Showing <?php echo mysqli_num_rows($teachers); ?> teachers
                </div>
                <div class="bulk-actions">
                    <button class="btn btn-sm btn-secondary" onclick="exportTeachers()">
                        <i class="fas fa-download"></i>
                        Export List
                    </button>
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

.teacher-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.teacher-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.teacher-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.teacher-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.teacher-id {
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
    
    .teacher-info {
        gap: 0.75rem;
    }
    
    .teacher-avatar {
        width: 40px;
        height: 40px;
        font-size: 0.875rem;
    }
}
</style>

<script>
// Filter teachers
function filterTeachers() {
    const searchTerm = document.getElementById('teacherSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#teachersTableBody tr');
    
    rows.forEach(row => {
        const teacherName = row.dataset.teacherName || '';
        const teacherEmail = row.dataset.teacherEmail || '';
        
        if (teacherName.includes(searchTerm) || teacherEmail.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Confirm delete
function confirmDelete(teacherName) {
    return confirm(`Are you sure you want to delete "${teacherName}"?\n\nThis action will permanently remove the teacher from the system.`);
}

// Export teachers (placeholder)
function exportTeachers() {
    const searchTerm = document.getElementById('teacherSearch').value.toLowerCase();
    
    // Create CSV content
    let csvContent = "ID,Name,Email\n";
    
    // Get all rows
    const rows = document.querySelectorAll('#teachersTableBody tr');
    
    rows.forEach(row => {
        // Check if row should be included based on search
        const teacherName = (row.dataset.teacherName || '').toLowerCase();
        const teacherEmail = (row.dataset.teacherEmail || '').toLowerCase();
        
        // Include row if no search or if it matches search
        if (!searchTerm || teacherName.includes(searchTerm) || teacherEmail.includes(searchTerm)) {
            const nameElement = row.querySelector('.teacher-name');
            const emailElement = row.querySelector('.email-info');
            const idElement = row.querySelector('.teacher-id');
            
            const teacherName = nameElement ? nameElement.textContent.trim() : '';
            const teacherEmail = emailElement ? emailElement.textContent.trim().replace(/\s+/g, ' ').trim() : '';
            const teacherId = idElement ? idElement.textContent.replace('ID: ', '').trim() : '';
            
            csvContent += `"${teacherId}","${teacherName}","${teacherEmail}"\n`;
        }
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'teachers_list.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    alert('Teachers list exported successfully!');
}

// Clear search on Escape key
document.getElementById('teacherSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        this.value = '';
        filterTeachers();
    }
});
</script>
