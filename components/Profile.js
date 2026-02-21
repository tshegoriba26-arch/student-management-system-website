import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';

const Profile = () => {
  const { id } = useParams();
  const [student, setStudent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchStudentProfile();
  }, [id]);

  const fetchStudentProfile = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`http://localhost/student-management-system/api/profile.php?id=${id}`);
      setStudent(response.data);
      setError('');
    } catch (err) {
      setError('Failed to fetch student profile');
      console.error('Error fetching profile:', err);
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadgeClass = (status) => {
    switch (status) {
      case 'Active': return 'green';
      case 'Pending': return 'orange';
      case 'Suspended': return 'red';
      case 'Graduated': return 'blue';
      default: return 'grey';
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
        <p>Loading student profile...</p>
      </div>
    );
  }

  if (error || !student) {
    return (
      <div className="center-align">
        <div className="card-panel red lighten-4 red-text">
          {error || 'Student not found'}
        </div>
        <Link to="/" className="btn blue waves-effect waves-light">
          Back to Dashboard
        </Link>
      </div>
    );
  }

  return (
    <div>
      <div className="card">
        <div className="card-content">
          <div className="row">
            <div className="col s12">
              <h4 className="center-align">Student Profile (React)</h4>
            </div>
          </div>

          <div className="row">
            <div className="col s12 m6">
              <div className="info-section">
                <h5>Personal Information</h5>
                <div className="divider"></div>
                <p><strong>Full Name:</strong> {student.full_name}</p>
                <p><strong>Student ID:</strong> {student.student_id}</p>
                <p><strong>Email:</strong> {student.email}</p>
                <p><strong>Date of Birth:</strong> {new Date(student.date_of_birth).toLocaleDateString()}</p>
              </div>
            </div>

            <div className="col s12 m6">
              <div className="info-section">
                <h5>Academic Information</h5>
                <div className="divider"></div>
                <p><strong>Course of Study:</strong> {student.course_of_study}</p>
                <p><strong>Enrollment Date:</strong> {new Date(student.enrollment_date).toLocaleDateString()}</p>
                <p><strong>Academic Status:</strong>
                  <span className={`badge ${getStatusBadgeClass(student.academic_status)} white-text`}>
                    {student.academic_status}
                  </span>
                </p>
                <p><strong>Registration Date:</strong> {new Date(student.created_at).toLocaleString()}</p>
              </div>
            </div>
          </div>

          <div className="row">
            <div className="col s12 center-align">
              <Link to="/" className="btn blue waves-effect waves-light">
                <i className="material-icons left">arrow_back</i>
                Back to Dashboard
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Profile;