// Leads Management JavaScript

// Handle status change
document.addEventListener('DOMContentLoaded', function() {
    // Status change handlers
    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const leadId = this.dataset.leadId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;
            
            if (newStatus !== currentStatus) {
                updateLeadStatus(leadId, newStatus, this);
            }
        });
    });
});

// Update lead status
function updateLeadStatus(leadId, status, selectElement) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', leadId);
    formData.append('status', status);
    
    fetch('leads.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectElement.dataset.currentStatus = status;
            showAlert('Status updated successfully', 'success');
        } else {
            // Revert the select to previous value
            selectElement.value = selectElement.dataset.currentStatus;
            showAlert('Failed to update status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        selectElement.value = selectElement.dataset.currentStatus;
        showAlert('An error occurred while updating status', 'danger');
    });
}

// View lead details
function viewLead(leadId) {
    fetch(`lead_details.php?id=${leadId}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('leadDetailsContent').innerHTML = html;
        new bootstrap.Modal(document.getElementById('viewLeadModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to load lead details', 'danger');
    });
}

// Add note to lead
function addNote(leadId) {
    document.getElementById('noteLeadId').value = leadId;
    document.getElementById('noteText').value = '';
    new bootstrap.Modal(document.getElementById('addNoteModal')).show();
}

// Save note
function saveNote() {
    const leadId = document.getElementById('noteLeadId').value;
    const note = document.getElementById('noteText').value.trim();
    
    if (!note) {
        showAlert('Please enter a note', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_note');
    formData.append('id', leadId);
    formData.append('note', note);
    
    fetch('leads.php', {
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

// Delete lead
function deleteLead(leadId) {
    if (!confirm('Are you sure you want to delete this lead? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_lead');
    formData.append('id', leadId);
    
    fetch('leads.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Lead deleted successfully', 'success');
            // Remove the row from table or refresh page
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete lead', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting lead', 'danger');
    });
}

// Save new lead
function saveLead() {
    const form = document.getElementById('addLeadForm');
    const formData = new FormData(form);
    formData.append('action', 'add_lead');
    
    // Basic validation
    const name = formData.get('name').trim();
    const email = formData.get('email').trim();
    const phone = formData.get('phone').trim();
    
    if (!name || !email || !phone) {
        showAlert('Please fill in all required fields', 'warning');
        return;
    }
    
    fetch('leads.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addLeadModal')).hide();
            showAlert('Lead added successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.error || 'Failed to add lead', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while adding lead', 'danger');
    });
}

// Export leads
function exportLeads() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = 'leads.php?' + params.toString();
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
    // Ctrl/Cmd + N to add new lead
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        new bootstrap.Modal(document.getElementById('addLeadModal')).show();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            bootstrap.Modal.getInstance(modal)?.hide();
        });
    }
});

// Auto-refresh functionality (optional)
let autoRefreshInterval;
function startAutoRefresh(intervalMinutes = 5) {
    autoRefreshInterval = setInterval(() => {
        location.reload();
    }, intervalMinutes * 60 * 1000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
