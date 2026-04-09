// Common Admin Panel JavaScript Functions
// This file contains shared functionality for all admin pages

// Global variables
let currentModal = null;
let isProcessing = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Common JS loaded');
    initializeCommonFeatures();
});

// Initialize common features
function initializeCommonFeatures() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Add loading states to buttons
    addLoadingStates();
    
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
}

// Show alert message
function showAlert(message, type = 'info', duration = 5000) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.admin-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show admin-alert`;
    alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after duration
    if (duration > 0) {
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, duration);
    }
}

// Show loading spinner
function showLoading(element) {
    if (element) {
        element.disabled = true;
        const originalText = element.innerHTML;
        element.dataset.originalText = originalText;
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }
}

// Hide loading spinner
function hideLoading(element) {
    if (element && element.dataset.originalText) {
        element.disabled = false;
        element.innerHTML = element.dataset.originalText;
        delete element.dataset.originalText;
    }
}

// Add loading states to buttons
function addLoadingStates() {
    document.querySelectorAll('button[onclick]').forEach(button => {
        const originalOnClick = button.getAttribute('onclick');
        button.addEventListener('click', function(e) {
            if (!isProcessing) {
                showLoading(this);
            }
        });
    });
}

// Generic AJAX request handler
function makeAjaxRequest(url, data, successCallback, errorCallback) {
    if (isProcessing) {
        showAlert('Please wait for the current operation to complete', 'warning');
        return;
    }
    
    isProcessing = true;
    
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        isProcessing = false;
        if (data.success) {
            if (successCallback) successCallback(data);
        } else {
            if (errorCallback) errorCallback(data);
            else showAlert(data.error || 'Operation failed', 'danger');
        }
    })
    .catch(error => {
        isProcessing = false;
        console.error('AJAX Error:', error);
        if (errorCallback) errorCallback({error: error.message});
        else showAlert('Network error occurred. Please try again.', 'danger');
    });
}

// Update status function (universal)
function updateStatus(id, status, type = 'application') {
    if (!id || !status) {
        showAlert('Invalid parameters for status update', 'danger');
        return;
    }
    
    const endpoint = type === 'lead' ? 'leads.php' : 'job_applications.php';
    
    makeAjaxRequest(endpoint, {
        action: 'update_status',
        id: id,
        status: status
    }, 
    function(data) {
        showAlert(`Status updated to "${status}" successfully`, 'success');
        setTimeout(() => location.reload(), 1500);
    },
    function(error) {
        showAlert('Failed to update status: ' + (error.error || 'Unknown error'), 'danger');
    });
}

// Add note function (universal)
function addNote(id, type = 'application') {
    const modalId = type === 'lead' ? 'addLeadNoteModal' : 'addNoteModal';
    const inputId = type === 'lead' ? 'noteLeadId' : 'noteAppId';
    
    // Check if modal exists
    const modal = document.getElementById(modalId);
    if (!modal) {
        showAlert('Note modal not found. Please refresh the page.', 'danger');
        return;
    }
    
    // Set the ID and clear previous text
    const idInput = document.getElementById(inputId);
    const textInput = document.getElementById('noteText');
    
    if (idInput) idInput.value = id;
    if (textInput) textInput.value = '';
    
    // Show modal
    currentModal = new bootstrap.Modal(modal);
    currentModal.show();
}

// Save note function (universal)
function saveNote(type = 'application') {
    const inputId = type === 'lead' ? 'noteLeadId' : 'noteAppId';
    const modalId = type === 'lead' ? 'addLeadNoteModal' : 'addNoteModal';
    const endpoint = type === 'lead' ? 'leads.php' : 'job_applications.php';
    
    const id = document.getElementById(inputId)?.value;
    const note = document.getElementById('noteText')?.value?.trim();
    
    if (!note) {
        showAlert('Please enter a note', 'warning');
        return;
    }
    
    makeAjaxRequest(endpoint, {
        action: 'add_note',
        id: id,
        note: note
    },
    function(data) {
        // Close modal
        if (currentModal) {
            currentModal.hide();
            currentModal = null;
        }
        showAlert('Note added successfully', 'success');
        setTimeout(() => location.reload(), 1500);
    },
    function(error) {
        showAlert('Failed to add note: ' + (error.error || 'Unknown error'), 'danger');
    });
}

// Schedule interview function
function scheduleInterview(id) {
    const modal = document.getElementById('scheduleInterviewModal');
    if (!modal) {
        showAlert('Interview modal not found. Please refresh the page.', 'danger');
        return;
    }
    
    // Set the ID and clear previous data
    const idInput = document.getElementById('interviewAppId');
    const dateInput = document.getElementById('interviewDate');
    const notesInput = document.getElementById('interviewNotes');
    
    if (idInput) idInput.value = id;
    if (dateInput) dateInput.value = '';
    if (notesInput) notesInput.value = '';
    
    // Show modal
    currentModal = new bootstrap.Modal(modal);
    currentModal.show();
}

// Save interview function
function saveInterview() {
    const id = document.getElementById('interviewAppId')?.value;
    const interviewDate = document.getElementById('interviewDate')?.value;
    const interviewNotes = document.getElementById('interviewNotes')?.value?.trim();
    
    if (!interviewDate) {
        showAlert('Please select interview date and time', 'warning');
        return;
    }
    
    makeAjaxRequest('job_applications.php', {
        action: 'schedule_interview',
        id: id,
        interview_date: interviewDate,
        interview_notes: interviewNotes
    },
    function(data) {
        // Close modal
        if (currentModal) {
            currentModal.hide();
            currentModal = null;
        }
        showAlert('Interview scheduled successfully', 'success');
        setTimeout(() => location.reload(), 1500);
    },
    function(error) {
        showAlert('Failed to schedule interview: ' + (error.error || 'Unknown error'), 'danger');
    });
}

// Delete function (universal)
function deleteItem(id, type = 'application', confirmMessage = null) {
    const defaultMessage = type === 'lead' ? 
        'Are you sure you want to delete this lead?' : 
        'Are you sure you want to delete this application?';
    
    const message = confirmMessage || defaultMessage;
    
    if (!confirm(message)) {
        return;
    }
    
    const endpoint = type === 'lead' ? 'leads.php' : 'job_applications.php';
    const action = type === 'lead' ? 'delete_lead' : 'delete_application';
    
    makeAjaxRequest(endpoint, {
        action: action,
        id: id
    },
    function(data) {
        showAlert(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully`, 'success');
        setTimeout(() => location.reload(), 1500);
    },
    function(error) {
        showAlert(`Failed to delete ${type}: ` + (error.error || 'Unknown error'), 'danger');
    });
}

// Modal wrapper functions for application details
function addNoteFromModal(id) {
    console.log('addNoteFromModal called with ID:', id);
    
    // Close the application details modal first
    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('viewApplicationModal'));
    if (detailsModal) {
        detailsModal.hide();
    }
    
    // Wait for modal to close, then open add note modal
    setTimeout(() => {
        addNote(id, 'application');
    }, 300);
}

function scheduleInterviewFromModal(id) {
    console.log('scheduleInterviewFromModal called with ID:', id);
    
    // Close the application details modal first
    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('viewApplicationModal'));
    if (detailsModal) {
        detailsModal.hide();
    }
    
    // Wait for modal to close, then open schedule interview modal
    setTimeout(() => {
        scheduleInterview(id);
    }, 300);
}

// Initialize keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
            });
        }
        
        // Ctrl/Cmd + S to save (prevent default browser save)
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            // Could trigger save action if in a form
        }
    });
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(amount);
}

// Export functions for global use
window.showAlert = showAlert;
window.updateStatus = updateStatus;
window.addNote = addNote;
window.saveNote = saveNote;
window.scheduleInterview = scheduleInterview;
window.saveInterview = saveInterview;
window.deleteItem = deleteItem;
window.addNoteFromModal = addNoteFromModal;
window.scheduleInterviewFromModal = scheduleInterviewFromModal;
window.makeAjaxRequest = makeAjaxRequest;

console.log('Admin Common JS initialized successfully');
