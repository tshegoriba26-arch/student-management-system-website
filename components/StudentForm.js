import React, { useState } from 'react';
import axios from 'axios';

const StudentForm = () => {
  const [formData, setFormData] = useState({
    full_name: '',
    student_id: '',
    email: '',
    date_of_birth: '',
    course_of_study: '',
    enrollment_date: ''
  });
  const [errors, setErrors] = useState([]);
  const [success, setSuccess] = useState('');
  const [loading, setLoading] = useState(false);

  // Event handler for form input changes
  const handleChange = (event) => {
    const { name, value } = event.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: value
    }));
  };

  // Event handler for form submission
  const handleSubmit = async (event) => {
    event.preventDefault();
    setLoading(true);
    setErrors([]);
    setSuccess('');

    try {
      // In a real application, you would call your API here
      // const response = await axios.post('/api/students', formData);
      
      // For demo purposes, we'll simulate a successful registration
      setTimeout(() => {
        setSuccess('Student registered successfully!');
        setFormData({
          full_name: '',
          student_id: '',
          email: '',
          date_of_birth: '',
          course_of_study: '',
          enrollment_date: ''
        });
        setLoading(false);
      }, 1000);

    } catch (err) {
      setErrors(['Failed to register student. Please try again.']);
      setLoading(false);
    }
  };

  return (
    <div>
      <h3 className="center-align">Register Student (React)</h3>

      {errors.length > 0 && (
        <div className="card-panel red lighten-4 red-text">
          <ul>
            {errors.map((error, index) => (
              <li key={index}>{error}</li>
            ))}
          </ul>
        </div>
      )}

      {success && (
        <div className="card-panel green lighten-4 green-text">
          {success}
        </div>
      )}

      <div className="card">
        <div className="card-content">
          <form onSubmit={handleSubmit}>
            <div className="row">
              <div className="input-field col s12 m6">
                <input
                  id="full_name"
                  name="full_name"
                  type="text"
                  value={formData.full_name}
                  onChange={handleChange}
                  required
                />
                <label htmlFor="full_name">Full Name</label>
              </div>
              <div className="input-field col s12 m6">
                <input
                  id="student_id"
                  name="student_id"
                  type="text"
                  value={formData.student_id}
                  onChange={handleChange}
                  required
                />
                <label htmlFor="student_id">Student ID</label>
              </div>
            </div>

            <div className="row">
              <div className="input-field col s12">
                <input
                  id="email"
                  name="email"
                  type="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                />
                <label htmlFor="email">Email</label>
              </div>
            </div>

            <div className="row">
              <div className="input-field col s12 m6">
                <input
                  id="date_of_birth"
                  name="date_of_birth"
                  type="date"
                  value={formData.date_of_birth}
                  onChange={handleChange}
                  required
                />
                <label htmlFor="date_of_birth">Date of Birth</label>
              </div>
              <div className="input-field col s12 m6">
                <input
                  id="enrollment_date"
                  name="enrollment_date"
                  type="date"
                  value={formData.enrollment_date}
                  onChange={handleChange}
                  required
                />
                <label htmlFor="enrollment_date">Enrollment Date</label>
              </div>
            </div>

            <div className="row">
              <div className="input-field col s12">
                <select
                  id="course_of_study"
                  name="course_of_study"
                  value={formData.course_of_study}
                  onChange={handleChange}
                  required
                >
                  <option value="" disabled>Choose course</option>
                  <option value="Computer Science">Computer Science</option>
                  <option value="Information Technology">Information Technology</option>
                  <option value="Software Engineering">Software Engineering</option>
                  <option value="Data Science">Data Science</option>
                  <option value="Cybersecurity">Cybersecurity</option>
                </select>
                <label htmlFor="course_of_study">Course of Study</label>
              </div>
            </div>

            <div className="row">
              <div className="col s12 center-align">
                <button 
                  type="submit" 
                  className="btn-large blue waves-effect waves-light"
                  disabled={loading}
                >
                  {loading ? (
                    <>
                      <div className="preloader-wrapper small active">
                        <div className="spinner-layer spinner-white-only">
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
                      <span style={{ marginLeft: '10px' }}>Registering...</span>
                    </>
                  ) : (
                    <>
                      <i className="material-icons left">person_add</i>
                      Register Student
                    </>
                  )}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default StudentForm;