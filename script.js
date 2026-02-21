// Student Management System JavaScript

// Initialize all Materialize components
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select elements
    var selectElems = document.querySelectorAll('select');
    M.FormSelect.init(selectElems);
    
    // Initialize modal elements if any
    var modalElems = document.querySelectorAll('.modal');
    M.Modal.init(modalElems);
    
    // Initialize tooltips if any
    var tooltipElems = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltipElems);
});

// Search and filter functionality
function setupTableFilter() {
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status_filter');
    const table = document.getElementById('studentTable');
    
    if (!table) return;
    
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value.toLowerCase() : '';

        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            if (cells.length < 5) continue;

            const name = cells[1].textContent.toLowerCase();
            const id = cells[0].textContent.toLowerCase();
            const course = cells[3].textContent.toLowerCase();
            const status = cells[4].textContent.toLowerCase();

            const matchesSearch = searchTerm === '' || 
                                name.includes(searchTerm) || 
                                id.includes(searchTerm) || 
                                course.includes(searchTerm);
            
            const matchesStatus = statusValue === '' || status.includes(statusValue);

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
}

// Form validation
function validateStudentForm(form) {
    const fullName = form.full_name.value.trim();
    const studentId = form.student_id.value.trim();
    const email = form.email.value.trim();
    const dateOfBirth = form.date_of_birth.value;
    const course = form.course_of_study.value;
    const enrollmentDate = form.enrollment_date.value;

    let errors = [];

    if (fullName.length < 2) {
        errors.push('Full name must be at least 2 characters long');
    }

    if (!studentId.match(/^[A-Z0-9]{6,}$/)) {
        errors.push('Student ID must be at least 6 alphanumeric characters (uppercase letters and numbers only)');
    }

    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        errors.push('Please enter a valid email address');
    }

    if (!dateOfBirth) {
        errors.push('Date of birth is required');
    }

    if (!course) {
        errors.push('Course of study is required');
    }

    if (!enrollmentDate) {
        errors.push('Enrollment date is required');
    }

    // Check if enrollment date is not in the future
    const today = new Date().toISOString().split('T')[0];
    if (enrollmentDate > today) {
        errors.push('Enrollment date cannot be in the future');
    }

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }

    return true;
}

// Error display function
function showErrors(errors) {
    let errorHtml = '<ul>';
    errors.forEach(error => {
        errorHtml += `<li>${error}</li>`;
    });
    errorHtml += '</ul>';

    M.toast({
        html: errorHtml,
        classes: 'red',
        displayLength: 4000
    });
}

// Success message
function showSuccess(message) {
    M.toast({
        html: message,
        classes: 'green',
        displayLength: 3000
    });
}

// Delete confirmation
function confirmDelete() {
    return confirm('Are you sure you want to delete this student record? This action cannot be undone.');
}

// Auto-format student ID to uppercase
function formatStudentId(input) {
    input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
}

// Calculate age from date of birth
function calculateAge(dateString) {
    const today = new Date();
    const birthDate = new Date(dateString);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// Show age when date of birth is selected
function showAge(input) {
    if (input.value) {
        const age = calculateAge(input.value);
        let ageDisplay = document.getElementById('ageDisplay');
        
        if (!ageDisplay) {
            ageDisplay = document.createElement('div');
            ageDisplay.id = 'ageDisplay';
            ageDisplay.className = 'age-display';
            input.parentNode.appendChild(ageDisplay);
        }
        
        ageDisplay.textContent = `Age: ${age} years`;
        
        if (age < 16) {
            ageDisplay.style.color = 'red';
            ageDisplay.title = 'Student seems too young. Please verify date of birth.';
        } else {
            ageDisplay.style.color = 'green';
        }
    }
}

// Export data functionality
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    const csv = [];

    for (let row of rows) {
        const rowData = [];
        const cols = row.querySelectorAll('td, th');
        
        for (let col of cols) {
            // Remove action buttons text
            let text = col.textContent.trim();
            if (text.includes('View') || text.includes('Edit') || text.includes('Delete')) {
                text = '';
            }
            rowData.push(`"${text}"`);
        }
        
        csv.push(rowData.join(','));
    }

    const csvString = csv.join('\n');
    const blob = new Blob([csvString], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = filename || 'students.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Sort table functionality
function sortTable(tableId, columnIndex, isNumeric = false) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isAscending = !table.getAttribute('data-sort-asc');

    rows.sort((a, b) => {
        const aVal = a.cells[columnIndex].textContent.trim();
        const bVal = b.cells[columnIndex].textContent.trim();

        if (isNumeric) {
            return isAscending ? aVal - bVal : bVal - aVal;
        } else {
            return isAscending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        }
    });

    // Remove existing rows
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

    // Add sorted rows
    rows.forEach(row => tbody.appendChild(row));

    // Update sort indicator
    table.setAttribute('data-sort-asc', isAscending);
    
    // Update header classes for visual feedback
    const headers = table.querySelectorAll('th');
    headers.forEach(header => header.classList.remove('sorted-asc', 'sorted-desc'));
    
    if (headers[columnIndex]) {
        headers[columnIndex].classList.add(isAscending ? 'sorted-asc' : 'sorted-desc');
    }
}

// Initialize when page loads
window.addEventListener('load', function() {
    setupTableFilter();
    
    // Add sort handlers to table headers
    const table = document.getElementById('studentTable');
    if (table) {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.title = 'Click to sort';
            header.addEventListener('click', () => {
                const isNumeric = index === 0 || index === 5; // Adjust based on your column types
                sortTable('studentTable', index, isNumeric);
            });
        });
    }
});

// AJAX functions for React integration
async function fetchStudents() {
    try {
        const response = await fetch('api/students.php');
        return await response.json();
    } catch (error) {
        console.error('Error fetching students:', error);
        return [];
    }
}

async function fetchStudentProfile(id) {
    try {
        const response = await fetch(`api/profile.php?id=${id}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching student profile:', error);
        return null;
    }
}