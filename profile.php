<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['id']);
$student = getStudentProfile($student_id);

if (!$student) {
    header("Location: dashboard.php");
    exit();
}

// Function to get student profile using return values
function getStudentProfile($id) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $student;
}

// Function to get status badge class using constants
function getStatusBadgeClass($status) {
    switch ($status) {
        case STATUS_ACTIVE:
            return 'green';
        case STATUS_PENDING:
            return 'orange';
        case STATUS_SUSPENDED:
            return 'red';
        case STATUS_GRADUATED:
            return 'blue';
        default:
            return 'grey';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
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
                <div class="card">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s12">
                                <h4 class="center-align">Student Profile</h4>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col s12 m6">
                                <div class="info-section">
                                    <h5>Personal Information</h5>
                                    <div class="divider"></div>
                                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                                    <p><strong>Date of Birth:</strong> <?php echo date('F j, Y', strtotime($student['date_of_birth'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="col s12 m6">
                                <div class="info-section">
                                    <h5>Academic Information</h5>
                                    <div class="divider"></div>
                                    <p><strong>Course of Study:</strong> <?php echo htmlspecialchars($student['course_of_study']); ?></p>
                                    <p><strong>Enrollment Date:</strong> <?php echo date('F j, Y', strtotime($student['enrollment_date'])); ?></p>
                                    <p><strong>Academic Status:</strong> 
                                        <span class="badge <?php echo getStatusBadgeClass($student['academic_status']); ?> white-text">
                                            <?php echo $student['academic_status']; ?>
                                        </span>
                                    </p>
                                    <p><strong>Registration Date:</strong> <?php echo date('F j, Y g:i A', strtotime($student['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col s12 center-align">
                                <a href="dashboard.php" class="btn blue waves-effect waves-light">
                                    <i class="material-icons left">arrow_back</i>
                                    Back to Dashboard
                                </a>
                                <a href="update.php?id=<?php echo $student['id']; ?>" class="btn orange waves-effect waves-light">
                                    <i class="material-icons left">edit</i>
                                    Edit Profile
                                </a>
                                <a href="reports.php?action=profile&id=<?php echo $student['id']; ?>" class="btn green waves-effect waves-light">
                                    <i class="material-icons left">description</i>
                                    Generate Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>