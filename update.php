<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['id']);
$errors = [];
$success = '';

// Fetch current student data
$conn = getDBConnection();
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $date_of_birth = $_POST['date_of_birth'];
    $course_of_study = trim($_POST['course_of_study']);
    $enrollment_date = $_POST['enrollment_date'];
    $academic_status = $_POST['academic_status'];

    // Validate inputs
    if (empty($full_name) || strlen($full_name) < 2) {
        $errors[] = "Full name must be at least 2 characters long";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }

    if (empty($date_of_birth)) {
        $errors[] = "Date of birth is required";
    }

    if (empty($course_of_study)) {
        $errors[] = "Course of study is required";
    }

    if (empty($enrollment_date)) {
        $errors[] = "Enrollment date is required";
    }

    if (empty($academic_status)) {
        $errors[] = "Academic status is required";
    }

    if (empty($errors)) {
        // Check if email already exists for other students
        $check_sql = "SELECT id FROM students WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $student_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "Email already exists for another student";
        } else {
            // Update using prepared statements
            $update_sql = "UPDATE students SET full_name = ?, email = ?, date_of_birth = ?, 
                          course_of_study = ?, enrollment_date = ?, academic_status = ? 
                          WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssssi", $full_name, $email, $date_of_birth, 
                                   $course_of_study, $enrollment_date, $academic_status, $student_id);
            
            if ($update_stmt->execute()) {
                $success = "Student information updated successfully!";
                // Refresh student data
                $student = array_merge($student, [
                    'full_name' => $full_name,
                    'email' => $email,
                    'date_of_birth' => $date_of_birth,
                    'course_of_study' => $course_of_study,
                    'enrollment_date' => $enrollment_date,
                    'academic_status' => $academic_status
                ]);
            } else {
                $errors[] = handleError($update_stmt->error);
            }
            $update_stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
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
                <h3 class="center-align">Update Student Information</h3>
                
                <?php if (!empty($errors)): ?>
                    <div class="card-panel red lighten-4 red-text">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="card-panel green lighten-4 green-text">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-content">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="full_name" name="full_name" type="text" class="validate" 
                                           value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                                    <label for="full_name">Full Name</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="student_id" type="text" class="validate" 
                                           value="<?php echo htmlspecialchars($student['student_id']); ?>" disabled>
                                    <label for="student_id">Student ID (Cannot be changed)</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="email" name="email" type="email" class="validate"
                                           value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="date_of_birth" name="date_of_birth" type="date" class="validate"
                                           value="<?php echo $student['date_of_birth']; ?>" required>
                                    <label for="date_of_birth">Date of Birth</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="enrollment_date" name="enrollment_date" type="date" class="validate"
                                           value="<?php echo $student['enrollment_date']; ?>" required>
                                    <label for="enrollment_date">Enrollment Date</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <select id="course_of_study" name="course_of_study" required>
                                        <option value="" disabled>Choose course</option>
                                        <option value="Computer Science" <?php echo $student['course_of_study'] == 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
                                        <option value="Information Technology" <?php echo $student['course_of_study'] == 'Information Technology' ? 'selected' : ''; ?>>Information Technology</option>
                                        <option value="Software Engineering" <?php echo $student['course_of_study'] == 'Software Engineering' ? 'selected' : ''; ?>>Software Engineering</option>
                                        <option value="Data Science" <?php echo $student['course_of_study'] == 'Data Science' ? 'selected' : ''; ?>>Data Science</option>
                                        <option value="Cybersecurity" <?php echo $student['course_of_study'] == 'Cybersecurity' ? 'selected' : ''; ?>>Cybersecurity</option>
                                    </select>
                                    <label for="course_of_study">Course of Study</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <select id="academic_status" name="academic_status" required>
                                        <option value="" disabled>Choose status</option>
                                        <option value="Active" <?php echo $student['academic_status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Pending" <?php echo $student['academic_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Suspended" <?php echo $student['academic_status'] == 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                                        <option value="Graduated" <?php echo $student['academic_status'] == 'Graduated' ? 'selected' : ''; ?>>Graduated</option>
                                    </select>
                                    <label for="academic_status">Academic Status</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col s12 center-align">
                                    <button type="submit" class="btn-large blue waves-effect waves-light">
                                        <i class="material-icons left">update</i>
                                        Update Student
                                    </button>
                                    <a href="profile.php?id=<?php echo $student['id']; ?>" class="btn-large grey waves-effect waves-light">
                                        <i class="material-icons left">cancel</i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            var instances = M.FormSelect.init(elems);
        });
    </script>
</body>
</html>