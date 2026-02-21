<?php
session_start();
require_once 'config.php';

$conn = getDBConnection();

// Handle report generation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if ($student) {
        if ($action == 'profile') {
            generateProfileReport($student);
        } elseif ($action == 'confirmation') {
            generateConfirmationReport($student);
        }
    }
    $stmt->close();
}

function generateProfileReport($student) {
    // Generate HTML report that can be printed or converted to PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Student Profile Report - ' . $student['student_id'] . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
            .section { margin-bottom: 20px; }
            .section h3 { background: #f5f5f5; padding: 10px; }
            .info-table { width: 100%; border-collapse: collapse; }
            .info-table td { padding: 8px; border-bottom: 1px solid #ddd; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
            @media print {
                .no-print { display: none; }
                body { margin: 0; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Student Profile Summary Report</h1>
            <p>Generated on: ' . date('F j, Y g:i A') . '</p>
        </div>
        
        <div class="section">
            <h3>Personal Information</h3>
            <table class="info-table">
                <tr><td><strong>Full Name:</strong></td><td>' . htmlspecialchars($student['full_name']) . '</td></tr>
                <tr><td><strong>Student ID:</strong></td><td>' . htmlspecialchars($student['student_id']) . '</td></tr>
                <tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($student['email']) . '</td></tr>
                <tr><td><strong>Date of Birth:</strong></td><td>' . date('F j, Y', strtotime($student['date_of_birth'])) . '</td></tr>
            </table>
        </div>
        
        <div class="section">
            <h3>Academic Information</h3>
            <table class="info-table">
                <tr><td><strong>Course of Study:</strong></td><td>' . htmlspecialchars($student['course_of_study']) . '</td></tr>
                <tr><td><strong>Enrollment Date:</strong></td><td>' . date('F j, Y', strtotime($student['enrollment_date'])) . '</td></tr>
                <tr><td><strong>Academic Status:</strong></td><td>' . $student['academic_status'] . '</td></tr>
                <tr><td><strong>Registration Date:</strong></td><td>' . date('F j, Y g:i A', strtotime($student['created_at'])) . '</td></tr>
            </table>
        </div>
        
        <div class="footer">
            <p>This is an auto-generated student profile report from Student Management System</p>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" class="btn">Print Report</button>
            <button onclick="window.close()" class="btn">Close</button>
        </div>
        
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>
    </html>';
    
    echo $html;
    exit;
}

function generateConfirmationReport($student) {
    // Generate registration confirmation slip
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Registration Confirmation - ' . $student['student_id'] . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .confirmation-card { border: 2px solid #333; padding: 20px; max-width: 600px; margin: 0 auto; }
            .header { text-align: center; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
            .university { font-size: 24px; font-weight: bold; }
            .title { font-size: 20px; color: #666; }
            .student-info { margin: 20px 0; }
            .info-row { margin: 10px 0; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #333; padding-top: 10px; }
            .metadata { background: #f9f9f9; padding: 15px; margin: 15px 0; }
            @media print {
                .no-print { display: none; }
                body { margin: 10px; }
            }
        </style>
    </head>
    <body>
        <div class="confirmation-card">
            <div class="header">
                <div class="university">UNIVERSITY MANAGEMENT SYSTEM</div>
                <div class="title">REGISTRATION CONFIRMATION SLIP</div>
            </div>
            
            <div class="student-info">
                <div class="info-row"><strong>Student:</strong> ' . htmlspecialchars($student['full_name']) . '</div>
                <div class="info-row"><strong>Student ID:</strong> ' . htmlspecialchars($student['student_id']) . '</div>
                <div class="info-row"><strong>Email:</strong> ' . htmlspecialchars($student['email']) . '</div>
                <div class="info-row"><strong>Course:</strong> ' . htmlspecialchars($student['course_of_study']) . '</div>
            </div>
            
            <div class="metadata">
                <h4>Registration Metadata</h4>
                <div class="info-row"><strong>Registration Timestamp:</strong> ' . date('F j, Y g:i A', strtotime($student['created_at'])) . '</div>
                <div class="info-row"><strong>Enrollment Date:</strong> ' . date('F j, Y', strtotime($student['enrollment_date'])) . '</div>
                <div class="info-row"><strong>Academic Status:</strong> ' . $student['academic_status'] . '</div>
                <div class="info-row"><strong>Course Summary:</strong> ' . htmlspecialchars($student['course_of_study']) . ' Program</div>
            </div>
            
            <div class="footer">
                <p><strong>This document confirms the student registration in our system.</strong></p>
                <p>Generated automatically on ' . date('F j, Y') . '</p>
                <p>For verification, please contact the administration office.</p>
            </div>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" class="btn">Print Confirmation</button>
            <button onclick="window.close()" class="btn">Close</button>
        </div>
        
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>
    </html>';
    
    echo $html;
    exit;
}

// Fetch all students for report selection
$students = [];
$sql = "SELECT id, full_name, student_id FROM students ORDER BY full_name";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports</title>
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
                <h3 class="center-align">Student Reports</h3>
                <p class="center-align flow-text">Generate and download student reports and confirmation slips</p>

                <div class="card">
                    <div class="card-content">
                        <h5>Available Reports</h5>
                        
                        <?php if (count($students) > 0): ?>
                            <div class="row">
                                <div class="col s12 m6">
                                    <div class="card-panel blue lighten-5">
                                        <h6>Profile Summary Report</h6>
                                        <p>Comprehensive student profile including personal and academic information.</p>
                                        <div class="input-field">
                                            <select id="profileStudent">
                                                <option value="" disabled selected>Choose student</option>
                                                <?php foreach ($students as $student): ?>
                                                    <option value="<?php echo $student['id']; ?>">
                                                        <?php echo htmlspecialchars($student['full_name']) . ' (' . htmlspecialchars($student['student_id']) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button onclick="generateProfileReport()" class="btn blue waves-effect waves-light">
                                            <i class="material-icons left">description</i>
                                            Generate Profile Report
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="col s12 m6">
                                    <div class="card-panel green lighten-5">
                                        <h6>Registration Confirmation Slip</h6>
                                        <p>Official registration confirmation with metadata and course summary.</p>
                                        <div class="input-field">
                                            <select id="confirmationStudent">
                                                <option value="" disabled selected>Choose student</option>
                                                <?php foreach ($students as $student): ?>
                                                    <option value="<?php echo $student['id']; ?>">
                                                        <?php echo htmlspecialchars($student['full_name']) . ' (' . htmlspecialchars($student['student_id']) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button onclick="generateConfirmationReport()" class="btn green waves-effect waves-light">
                                            <i class="material-icons left">assignment_turned_in</i>
                                            Generate Confirmation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="center-align">
                                <p class="flow-text">No students available for report generation.</p>
                                <a href="register.php" class="btn blue waves-effect waves-light">
                                    <i class="material-icons left">person_add</i>
                                    Register Students First
                                </a>
                            </div>
                        <?php endif; ?>
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

        function generateProfileReport() {
            var studentId = document.getElementById('profileStudent').value;
            if (studentId) {
                window.open('reports.php?action=profile&id=' + studentId, '_blank');
            } else {
                M.toast({html: 'Please select a student first'});
            }
        }

        function generateConfirmationReport() {
            var studentId = document.getElementById('confirmationStudent').value;
            if (studentId) {
                window.open('reports.php?action=confirmation&id=' + studentId, '_blank');
            } else {
                M.toast({html: 'Please select a student first'});
            }
        }
    </script>
</body>
</html>