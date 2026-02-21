import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Dashboard from './components/Dashboard';
import StudentForm from './components/StudentForm';
import Profile from './components/Profile';
import 'materialize-css/dist/css/materialize.min.css';

// Functional vs Class Components Discussion:
// We use functional components because:
// 1. Simpler syntax and less code
// 2. Better performance with hooks
// 3. Easier to test and debug
// 4. Future of React with concurrent features
// 5. Better TypeScript support
// 6. No 'this' binding issues
// 7. Can use all React features with hooks

function App() {
  return (
    <Router>
      <div className="App">
        <nav className="blue darken-3">
          <div className="nav-wrapper container">
            <a href="/" className="brand-logo">Student Management (React)</a>
            <ul id="nav-mobile" className="right hide-on-med-and-down">
              <li><a href="/">Dashboard</a></li>
              <li><a href="/register">Register Student</a></li>
            </ul>
          </div>
        </nav>

        <div className="container">
          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/register" element={<StudentForm />} />
            <Route path="/profile/:id" element={<Profile />} />
          </Routes>
        </div>
      </div>
    </Router>
  );
}

export default App;