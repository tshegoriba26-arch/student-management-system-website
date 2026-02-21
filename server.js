const express = require('express');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'build')));

// Mock API endpoints for React app
app.get('/api/students', (req, res) => {
  // Simulate database response
  const mockStudents = [
    {
      id: 1,
      full_name: 'John Doe',
      student_id: 'STU001',
      email: 'john.doe@university.edu',
      date_of_birth: '2000-01-15',
      course_of_study: 'Computer Science',
      enrollment_date: '2023-09-01',
      academic_status: 'Active',
      created_at: '2023-08-15T10:30:00Z'
    },
    {
      id: 2,
      full_name: 'Jane Smith',
      student_id: 'STU002',
      email: 'jane.smith@university.edu',
      date_of_birth: '1999-05-20',
      course_of_study: 'Information Technology',
      enrollment_date: '2023-09-01',
      academic_status: 'Active',
      created_at: '2023-08-20T14:45:00Z'
    }
  ];
  
  res.json(mockStudents);
});

app.get('/api/students/:id', (req, res) => {
  const studentId = parseInt(req.params.id);
  const mockStudent = {
    id: studentId,
    full_name: 'John Doe',
    student_id: 'STU001',
    email: 'john.doe@university.edu',
    date_of_birth: '2000-01-15',
    course_of_study: 'Computer Science',
    enrollment_date: '2023-09-01',
    academic_status: 'Active',
    created_at: '2023-08-15T10:30:00Z'
  };
  
  res.json(mockStudent);
});

// Serve React app for all other routes
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'build', 'index.html'));
});

app.listen(PORT, () => {
  console.log(`Node.js server running on port ${PORT}`);
  console.log(`React app with Node.js backend simulation is ready!`);
});