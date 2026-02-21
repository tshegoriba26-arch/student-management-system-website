<?php
session_start();
require_once 'config.php';

$conn = getDBConnection();

// Fetch all students using arrays
$students = [];
$sql = "SELECT * FROM students ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Search functionality
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $students = array_filter($students, function($student) use ($search) {
        return stripos($student['full_name'], $search) !== false || 
               stripos($student['student_id'], $search) !== false ||
               stripos($student['course_of_study'], $search) !== false;
    });
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="blue darken-3">
        <div class="nav-wrapper container">
            <a href="index.php" class="brand-logo">Student Management</a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="register.php">Register Student</a></li>
                <li><a href="reports.php">Reports</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h3 class="center-align">Student Management Dashboard</h3>
                
                <!-- Search and Filter Section -->
                <div class="card">
                    <div class="card-content">
                        <div class="row">
                            <div class="input-field col s12 m8">
                                <input type="text" id="search" placeholder="Search by name, ID, or course...">
                                <label for="search">Search Students</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select id="status_filter">
                                    <option value="">All Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Suspended">Suspended</option>
                                    <option value="Graduated">Graduated</option>
                                </select>
                                <label>Filter by Status</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-content">
                        <h5>Student Records (<?php echo count($students); ?>)</h5>
                        
                        <?php if (count($students) > 0): ?>
                            <table class="striped responsive-table" id="studentTable">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Enrollment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                            <td><?php echo htmlspecialchars($student['course_of_study']); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo strtolower($student['academic_status']); ?>">
                                                    <?php echo $student['academic_status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($student['enrollment_date'])); ?></td>
                                            <td>
                                                <a href="profile.php?id=<?php echo $student['id']; ?>" class="btn-small blue waves-effect waves-light">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                <a href="update.php?id=<?php echo $student['id']; ?>" class="btn-small orange waves-effect waves-light">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn-small red waves-effect waves-light" onclick="return confirmDelete()">
                                                    <i class="material-icons">delete</i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="center-align">
                                <p class="flow-text">No students found.</p>
                                <a href="register.php" class="btn blue waves-effect waves-light">
                                    <i class="material-icons left">person_add</i>
                                    Register First Student
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this student record? This action cannot be undone.');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize select
            var elems = document.querySelectorAll('select');
            var instances = M.FormSelect.init(elems);

            // Search functionality
            const searchInput = document.getElementById('search');
            const statusFilter = document.getElementById('status_filter');
            const table = document.getElementById('studentTable');
            const rows = table ? table.getElementsByTagName('tbody')[0].getElementsByTagName('tr') : [];

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();

                for (let row of rows) {
                    const cells = row.getElementsByTagName('td');
                    const name = cells[1].textContent.toLowerCase();
                    const id = cells[0].textContent.toLowerCase();
                    const course = cells[3].textContent.toLowerCase();
                    const status = cells[4].textContent.toLowerCase();

                    const matchesSearch = name.includes(searchTerm) || id.includes(searchTerm) || course.includes(searchTerm);
                    const matchesStatus = statusValue === '' || status.includes(statusValue);

                    row.style.display = matchesSearch && matchesStatus ? '' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }
            if (statusFilter) {
                statusFilter.addEventListener('change', filterTable);
            }
        });
    </script>
</body>
</html>