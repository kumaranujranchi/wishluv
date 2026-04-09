// User Management JavaScript

// Create new user
function createUser() {
    const formData = new FormData(document.getElementById('createUserForm'));
    formData.append('action', 'create_user');
    
    fetch('users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('User created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.error || 'Failed to create user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while creating user', 'danger');
    });
}

// Edit user
function editUser(userId) {
    // Find user data from the table
    const userRow = document.querySelector(`button[onclick="editUser(${userId})"]`).closest('tr');
    const cells = userRow.querySelectorAll('td');
    
    // Extract user data
    const name = cells[0].textContent.trim();
    const username = cells[1].textContent.trim();
    const email = cells[2].textContent.trim();
    const role = cells[3].textContent.toLowerCase().trim();
    const status = cells[4].textContent.toLowerCase().trim();
    
    // Populate edit form
    document.getElementById('editUserId').value = userId;
    document.getElementById('editName').value = name;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
    document.getElementById('editStatus').value = status;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

// Update user
function updateUser() {
    const formData = new FormData(document.getElementById('editUserForm'));
    formData.append('action', 'update_user');
    
    fetch('users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('User updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.error || 'Failed to update user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating user', 'danger');
    });
}

// Delete user
function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_user');
    formData.append('id', userId);
    
    fetch('users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('User deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.error || 'Failed to delete user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting user', 'danger');
    });
}

// Reset password
function resetPassword(userId) {
    document.getElementById('resetUserId').value = userId;
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

// Reset user password
function resetUserPassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        showAlert('Passwords do not match', 'warning');
        return;
    }
    
    if (newPassword.length < 6) {
        showAlert('Password must be at least 6 characters long', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'reset_password');
    formData.append('id', document.getElementById('resetUserId').value);
    formData.append('new_password', newPassword);
    
    fetch('users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Password reset successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
        } else {
            showAlert(data.error || 'Failed to reset password', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while resetting password', 'danger');
    });
}

// Show alert message
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-floating');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-floating`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    `;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Create user form validation
    const createForm = document.getElementById('createUserForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            createUser();
        });
    }
    
    // Edit user form validation
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateUser();
        });
    }
    
    // Reset password form validation
    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', function(e) {
            e.preventDefault();
            resetUserPassword();
        });
    }
    
    // Password confirmation validation
    const confirmPassword = document.getElementById('confirmPassword');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            const newPassword = document.getElementById('newPassword').value;
            if (this.value && this.value !== newPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Search functionality (if needed)
function searchUsers(query) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Export users (if needed)
function exportUsers() {
    window.location.href = 'users.php?export=csv';
}
