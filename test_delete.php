<?php
// Test file to verify delete functionality works
session_start();
include 'config.php';

echo "<h2>Testing Delete Functionality</h2>";

// Show current students
echo "<h3>Current Students:</h3>";
$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student'");
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th></tr>";
while($r = mysqli_fetch_assoc($students)) {
    echo "<tr><td>".$r['id']."</td><td>".$r['name']."</td><td>".$r['email']."</td></tr>";
}
echo "</table>";

// Show delete_student.php exists
if (file_exists('delete_student.php')) {
    echo "<h3>✅ delete_student.php file exists</h3>";
} else {
    echo "<h3>❌ delete_student.php file missing</h3>";
}

echo "<p><a href='remove_student.php'>Go to Remove Student Page</a></p>";
?>
