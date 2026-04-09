// Job Applications Management JavaScript

// Handle status change
document.addEventListener('DOMContentLoaded', function() {
    console.log('Job Applications JS loaded');

    // Status change handlers
    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const appId = this.dataset.appId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;

            if (newStatus !== currentStatus) {
                updateApplicationStatus(appId, newStatus, this);
            }
        });
    });
});

// Update application status (legacy function for dropdowns)
function updateApplicationStatus(appId, status, selectElement) {
    // Use common function if available, otherwise fallback to local implementation
    if (window.updateStatus && typeof window.updateStatus === 'function') {
        window.updateStatus(appId, status, 'application');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', appId);
    formData.append('status', status);

    fetch('job_applications.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectElement.dataset.currentStatus = status;
            if (window.showAlert) {
                window.showAlert('Status updated successfully', 'success');
            } else {
                alert('Status updated successfully');
            }
        } else {
            // Revert the select to previous value
            selectElement.value = selectElement.dataset.currentStatus;
            if (window.showAlert) {
                window.showAlert('Failed to update status', 'danger');
            } else {
                alert('Failed to update status');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        selectElement.value = selectElement.dataset.currentStatus;
        showAlert('An error occurred while updating status', 'danger');
    });
}

// View application details
function viewApplication(appId) {
    console.log('viewApplication called with ID:', appId);

    fetch(`application_details.php?id=${appId}`)
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(html => {
        console.log('HTML received, length:', html.length);
        document.getElementById('applicationDetailsContent').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('viewApplicationModal'));
        modal.show();
        console.log('Modal shown');
    })
    .catch(error => {
        console.error('Error loading application details:', error);
        showAlert('Failed to load application details', 'danger');
    });
}

// Add note to application
function addNote(appId) {
    document.getElementById('noteAppId').value = appId;
    document.getElementById('noteText').value = '';
    new bootstrap.Modal(document.getElementById('addNoteModal')).show();
}

// Save note
function saveNote() {
    const appId = document.getElementById('noteAppId').value;
    const note = document.getElementById('noteText').value.trim();
    
    if (!note) {
        showAlert('Please enter a note', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_note');
    formData.append('id', appId);
    formData.append('note', note);
    
    fetch('job_applications.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addNoteModal')).hide();
            showAlert('Note added successfully', 'success');
            // Refresh the page to show the note indicator
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.error || 'Failed to add note', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while adding note', 'danger');
    });
}

// Schedule interview
function scheduleInterview(appId) {
    document.getElementById('interviewAppId').value = appId;
    document.getElementById('interviewDate').value = '';
    document.getElementById('interviewNotes').value = '';
    new bootstrap.Modal(document.getElementById('scheduleInterviewModal')).show();
}

// Save interview schedule
function saveInterview() {
    const appId = document.getElementById('interviewAppId').value;
    const interviewDate = document.getElementById('interviewDate').value;
    const interviewNotes = document.getElementById('interviewNotes').value.trim();
    
    if (!interviewDate) {
        showAlert('Please select interview date and time', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'schedule_interview');
    formData.append('id', appId);
    formData.append('interview_date', interviewDate);
    formData.append('interview_notes', interviewNotes);
    
    fetch('job_applications.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('scheduleInterviewModal')).hide();
            showAlert('Interview scheduled successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to schedule interview', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while scheduling interview', 'danger');
    });
}

// Delete application
function deleteApplication(appId) {
    if (!confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_application');
    formData.append('id', appId);
    
    fetch('job_applications.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Application deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete application', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting application', 'danger');
    });
}

// Export applications
function exportApplications() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = 'job_applications.php?' + params.toString();
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

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            bootstrap.Modal.getInstance(modal)?.hide();
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

// Wrapper functions for modal actions (to avoid conflicts)
function addNoteFromModal(appId) {
    console.log('addNoteFromModal called with ID:', appId);

    // Close the application details modal first
    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('viewApplicationModal'));
    if (detailsModal) {
        console.log('Closing details modal...');
        detailsModal.hide();
    }

    // Wait for modal to close, then open add note modal
    setTimeout(() => {
        console.log('Opening add note modal...');
        addNote(appId);
    }, 300);
}

function scheduleInterviewFromModal(appId) {
    console.log('scheduleInterviewFromModal called with ID:', appId);

    // Close the application details modal first
    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('viewApplicationModal'));
    if (detailsModal) {
        console.log('Closing details modal...');
        detailsModal.hide();
    }

    // Wait for modal to close, then open schedule interview modal
    setTimeout(() => {
        console.log('Opening schedule interview modal...');
        scheduleInterview(appId);
    }, 300);
}

// Bulk actions functionality
function selectAllApplications() {
    const checkboxes = document.querySelectorAll('.application-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActionsVisibility();
}

function updateBulkActionsVisibility() {
    const checkedBoxes = document.querySelectorAll('.application-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (bulkActions) {
        bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }
}

// Bulk status update
function bulkUpdateStatus(status) {
    const checkedBoxes = document.querySelectorAll('.application-checkbox:checked');
    const appIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (appIds.length === 0) {
        showAlert('Please select applications to update', 'warning');
        return;
    }

    if (!confirm(`Are you sure you want to update ${appIds.length} applications to ${status}?`)) {
        return;
    }

    // Update each application
    let completed = 0;
    appIds.forEach(appId => {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', appId);
        formData.append('status', status);

        fetch('job_applications.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            completed++;
            if (completed === appIds.length) {
                showAlert(`${appIds.length} applications updated successfully`, 'success');
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            completed++;
            if (completed === appIds.length) {
                showAlert('Some applications may not have been updated', 'warning');
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
}

// Update status from modal (for action buttons in application details)
function updateStatus(appId, status) {
    console.log('updateStatus called with ID:', appId, 'Status:', status);

    let confirmMessage = '';
    let successMessage = '';

    switch(status) {
        case 'selected':
            confirmMessage = 'Mark this application as selected?';
            successMessage = 'Application marked as selected';
            break;
        case 'on_hold':
            confirmMessage = 'Put this application on hold?';
            successMessage = 'Application put on hold';
            break;
        case 'rejected':
            confirmMessage = 'Reject this application?';
            successMessage = 'Application rejected';
            break;
        default:
            confirmMessage = `Update application status to ${status}?`;
            successMessage = 'Application status updated';
    }

    if (!confirm(confirmMessage)) {
        console.log('User cancelled status update');
        return;
    }

    console.log('Sending status update request...');
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', appId);
    formData.append('status', status);

    fetch('job_applications.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status update response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Status update response data:', data);
        if (data.success) {
            showAlert(successMessage, 'success');
            // Close modal and refresh page
            const modal = bootstrap.Modal.getInstance(document.getElementById('viewApplicationModal'));
            if (modal) {
                modal.hide();
            }
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.error || 'Failed to update status', 'danger');
        }
    })
    .catch(error => {
        console.error('Status update error:', error);
        showAlert('An error occurred while updating status', 'danger');
    });
}
