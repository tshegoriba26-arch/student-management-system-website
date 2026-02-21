import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Dashboard = () => {
  const [students, setStudents] = useState([]);
  const [filteredStudents, setFilteredStudents] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  // Using React state and lifecycle hooks (useEffect)
  useEffect(() => {
    fetchStudents();
  }, []);

  // Effect for filtering students
  useEffect(() => {
    const filtered = students.filter(student =>
      student.full_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      student.student_id.toLowerCase().includes(searchTerm.toLowerCase()) ||
      student.course_of_study.toLowerCase().includes(searchTerm.toLowerCase())
    );
    setFilteredStudents(filtered);
  }, [searchTerm, students]);

  const fetchStudents = async () => {
    try {
      setLoading(true);
      const response = await axios.get('http://localhost/student-management-system/api/students.php');
      setStudents(response.data);
      setError('');
    } catch (err) {
      setError('Failed to fetch students');
      console.error('Error fetching students:', err);
    } finally {
      setLoading(false);
    }
  };

  // Event handler using React event handlers
  const handleSearch = (event) => {
    setSearchTerm(event.target.value);
  };

  const handleDelete = async (studentId) => {
    if (window.confirm('Are you sure you want to delete this student?')) {
      try {
        // In a real application, you would call your API here
        // await axios.delete(`/api/students/${studentId}`);
        
        // For demo purposes, we'll just filter out the student
        setStudents(students.filter(student => student.id !== studentId));
      } catch (err) {
        setError('Failed to delete student');
      }
    }
  };

  if (loading) {
    return (
      <div className="center-align">
        <div className="preloader-wrapper big active">
          <div className="spinner-layer spinner-blue-only">
            <div className="circle-clipper left">
              <div className="circle"></div>
            </div>
            <div className="gap-patch">
              <div className="circle"></div>
            </div>
            <div className="circle-clipper right">
              <div className="circle"></div>
            </div>
          </div>
        </div>
        <p>Loading students...</p>
      </div>
    );
  }

  return (
    <div>
      <h3 className="center-align">Student Dashboard (React)</h3>
      
      {error && (
        <div className="card-panel red lighten-4 red-text">
          {error}
        </div>
      )}

      <div className="card">
        <div className="card-content">
          <div className="row">
            <div className="input-field col s12">
              <input
                type="text"
                id="search"
                value={searchTerm}
                onChange={handleSearch}
                placeholder="Search by name, ID, or course..."
              />
              <label htmlFor="search">Search Students</label>
            </div>
          </div>

          <h5>Student Records ({filteredStudents.length})</h5>
          
          {filteredStudents.length > 0 ? (
            <table className="striped responsive-table">
              <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Course</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredStudents.map(student => (
                  <tr key={student.id}>
                    <td>{student.student_id}</td>
                    <td>{student.full_name}</td>
                    <td>{student.email}</td>
                    <td>{student.course_of_study}</td>
                    <td>
                      <span className={`status-badge ${student.academic_status.toLowerCase()}`}>
                        {student.academic_status}
                      </span>
                    </td>
                    <td>
                      <Link 
                        to={`/profile/${student.id}`}
                        className="btn-small blue waves-effect waves-light"
                      >
                        <i className="material-icons">visibility</i>
                      </Link>
                      <button 
                        className="btn-small red waves-effect waves-light ml-1"
                        onClick={() => handleDelete(student.id)}
                      >
                        <i className="material-icons">delete</i>
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <div className="center-align">
              <p className="flow-text">No students found.</p>
              <Link to="/register" className="btn blue waves-effect waves-light">
                <i className="material-icons left">person_add</i>
                Register First Student
              </Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Dashboard;