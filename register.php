<?php
session_start();
require_once 'config.php';

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $date_of_birth = $_POST['date_of_birth'];
    $course_of_study = trim($_POST['course_of_study']);
    $enrollment_date = $_POST['enrollment_date'];

    // Validate inputs using string functions
    if (empty($full_name) || strlen($full_name) < 2) {
        $errors[] = "Full name must be at least 2 characters long";
    }

    if (empty($student_id) || !preg_match('/^[A-Z0-9]{6,}$/', $student_id)) {
        $errors[] = "Student ID must be at least 6 alphanumeric characters";
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

    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Check if student ID or email already exists
        $check_sql = "SELECT id FROM students WHERE student_id = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $student_id, $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "Student ID or Email already exists";
        } else {
            // Insert using prepared statements to prevent SQL injection
            $sql = "INSERT INTO students (full_name, student_id, email, date_of_birth, course_of_study, enrollment_date) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $full_name, $student_id, $email, $date_of_birth, $course_of_study, $enrollment_date);
            
            if ($stmt->execute()) {
                $success = "Student registered successfully!";
                // Clear form
                $_POST = array();
            } else {
                $errors[] = handleError($stmt->error);
            }
            $stmt->close();
        }
        $check_stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
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
                <h3 class="center-align">Student Registration</h3>
                
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
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="full_name" name="full_name" type="text" class="validate" 
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                                    <label for="full_name">Full Name</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="student_id" name="student_id" type="text" class="validate"
                                           value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>" required>
                                    <label for="student_id">Student ID</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="email" name="email" type="email" class="validate"
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="date_of_birth" name="date_of_birth" type="date" class="validate"
                                           value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>" required>
                                    <label for="date_of_birth">Date of Birth</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="enrollment_date" name="enrollment_date" type="date" class="validate"
                                           value="<?php echo isset($_POST['enrollment_date']) ? htmlspecialchars($_POST['enrollment_date']) : ''; ?>" required>
                                    <label for="enrollment_date">Enrollment Date</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <select id="course_of_study" name="course_of_study" required>
                                        <option value="" disabled selected>Choose course</option>
                                        <option value="Computer Science" <?php echo (isset($_POST['course_of_study']) && $_POST['course_of_study'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                        <option value="Information Technology" <?php echo (isset($_POST['course_of_study']) && $_POST['course_of_study'] == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                        <option value="Software Engineering" <?php echo (isset($_POST['course_of_study']) && $_POST['course_of_study'] == 'Software Engineering') ? 'selected' : ''; ?>>Software Engineering</option>
                                        <option value="Data Science" <?php echo (isset($_POST['course_of_study']) && $_POST['course_of_study'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                                        <option value="Cybersecurity" <?php echo (isset($_POST['course_of_study']) && $_POST['course_of_study'] == 'Cybersecurity') ? 'selected' : ''; ?>>Cybersecurity</option>
                                    </select>
                                    <label for="course_of_study">Course of Study</label>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col s12 center-align">
                                    <button type="submit" class="btn-large blue waves-effect waves-light">
                                        <i class="material-icons left">person_add</i>
                                        Register Student
                                    </button>
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