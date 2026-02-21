<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
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
            <div class="col s12 center-align">
                <h3>Welcome to Student Management System</h3>
                <p class="flow-text">A modern web-based system for student registration and academic management</p>
                
                <div class="row">
                    <div class="col s12 m4">
                        <div class="card-panel blue white-text">
                            <i class="material-icons large">person_add</i>
                            <h5>Student Registration</h5>
                            <p>Register new students with comprehensive information</p>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card-panel green white-text">
                            <i class="material-icons large">dashboard</i>
                            <h5>Management Dashboard</h5>
                            <p>View and manage all student records</p>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card-panel orange white-text">
                            <i class="material-icons large">assessment</i>
                            <h5>Reports</h5>
                            <p>Generate student reports and confirmation slips</p>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <a href="dashboard.php" class="btn-large blue waves-effect waves-light">
                        <i class="material-icons left">dashboard</i>
                        Go to Dashboard
                    </a>
                    <a href="register.php" class="btn-large green waves-effect waves-light">
                        <i class="material-icons left">person_add</i>
                        Register Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>