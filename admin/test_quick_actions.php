<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get sample data for testing
$stmt = $conn->prepare("SELECT id, full_name, email, status FROM job_applications LIMIT 3");
$stmt->execute();
$applications = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT id, name, email, status FROM leads LIMIT 3");
$stmt->execute();
$leads = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Quick Actions - Wishluv Buildcon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quick Actions Testing</h1>
                </div>

                <!-- JavaScript Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-code"></i> JavaScript Status</h5>
                    </div>
                    <div class="card-body">
                        <div id="jsStatus" class="alert alert-info">
                            <i class="fas fa-spinner fa-spin"></i> Checking JavaScript status...
                        </div>
                    </div>
                </div>

                <!-- Test Applications Quick Actions -->
                <?php if (!empty($applications)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-briefcase"></i> Test Application Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Quick Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td><?php echo $app['id']; ?></td>
                                        <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['email']); ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo ucfirst($app['status']); ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" onclick="addNote(<?php echo $app['id']; ?>, 'application')" title="Add Note">
                                                    <i class="fas fa-sticky-note"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info" onclick="scheduleInterview(<?php echo $app['id']; ?>)" title="Schedule Interview">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $app['id']; ?>, 'selected', 'application')" title="Mark as Selected">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="updateStatus(<?php echo $app['id']; ?>, 'on_hold', 'application')" title="Put on Hold">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Test Leads Quick Actions -->
                <?php if (!empty($leads)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-users"></i> Test Lead Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Quick Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td><?php echo $lead['id']; ?></td>
                                        <td><?php echo htmlspecialchars($lead['name']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($lead['status']); ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" onclick="addNote(<?php echo $lead['id']; ?>, 'lead')" title="Add Note">
                                                    <i class="fas fa-sticky-note"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $lead['id']; ?>, 'contacted', 'lead')" title="Mark as Contacted">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="updateStatus(<?php echo $lead['id']; ?>, 'qualified', 'lead')" title="Mark as Qualified">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info" onclick="updateStatus(<?php echo $lead['id']; ?>, 'converted', 'lead')" title="Mark as Converted">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Test Results -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-test-tube"></i> Test Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="testResults">
                            <p class="text-muted">Click any quick action button above to test functionality...</p>
                        </div>
                    </div>
                </div>

                <!-- Debug Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bug"></i> Debug Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Browser Console</h6>
                                <p class="small">Press F12 and check the Console tab for any JavaScript errors.</p>
                                <button class="btn btn-sm btn-outline-primary" onclick="testConsole()">Test Console Logging</button>
                            </div>
                            <div class="col-md-6">
                                <h6>Network Requests</h6>
                                <p class="small">Check the Network tab in browser dev tools to see AJAX requests.</p>
                                <button class="btn btn-sm btn-outline-info" onclick="testAjax()">Test AJAX Request</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="noteAppId" value="">
                        <div class="mb-3">
                            <label for="noteText" class="form-label">Note</label>
                            <textarea class="form-control" id="noteText" rows="4" placeholder="Enter your note here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote('application')">Save Note</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Lead Note Modal -->
    <div class="modal fade" id="addLeadNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Lead Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="noteLeadId" value="">
                        <div class="mb-3">
                            <label for="noteText" class="form-label">Note</label>
                            <textarea class="form-control" id="noteText" rows="4" placeholder="Enter your note here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote('lead')">Save Note</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Interview Modal -->
    <div class="modal fade" id="scheduleInterviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Interview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="interviewAppId" value="">
                        <div class="mb-3">
                            <label for="interviewDate" class="form-label">Interview Date & Time</label>
                            <input type="datetime-local" class="form-control" id="interviewDate">
                        </div>
                        <div class="mb-3">
                            <label for="interviewNotes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="interviewNotes" rows="3" placeholder="Interview notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveInterview()">Schedule Interview</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-common.js"></script>
    <script>
        // Check JavaScript status
        document.addEventListener('DOMContentLoaded', function() {
            const statusDiv = document.getElementById('jsStatus');
            
            // Check if common functions are available
            const checks = [
                { name: 'showAlert', available: typeof window.showAlert === 'function' },
                { name: 'updateStatus', available: typeof window.updateStatus === 'function' },
                { name: 'addNote', available: typeof window.addNote === 'function' },
                { name: 'scheduleInterview', available: typeof window.scheduleInterview === 'function' },
                { name: 'Bootstrap', available: typeof bootstrap !== 'undefined' }
            ];
            
            const allWorking = checks.every(check => check.available);
            
            if (allWorking) {
                statusDiv.className = 'alert alert-success';
                statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> All JavaScript functions loaded successfully!';
            } else {
                statusDiv.className = 'alert alert-danger';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Some JavaScript functions are missing:<br>' +
                    checks.filter(check => !check.available).map(check => `- ${check.name}`).join('<br>');
            }
        });
        
        // Test functions
        function testConsole() {
            console.log('✅ Console logging test successful');
            console.log('Available functions:', {
                showAlert: typeof window.showAlert,
                updateStatus: typeof window.updateStatus,
                addNote: typeof window.addNote,
                scheduleInterview: typeof window.scheduleInterview
            });
            showAlert('Check browser console (F12) for test results', 'info');
        }
        
        function testAjax() {
            showAlert('Testing AJAX request...', 'info');
            
            fetch('leads.php', {
                method: 'POST',
                body: new FormData()
            })
            .then(response => {
                showAlert('AJAX test successful - check Network tab in dev tools', 'success');
            })
            .catch(error => {
                showAlert('AJAX test failed: ' + error.message, 'danger');
            });
        }
        
        // Override functions to add test logging
        const originalUpdateStatus = window.updateStatus;
        window.updateStatus = function(id, status, type) {
            document.getElementById('testResults').innerHTML += 
                `<div class="alert alert-info">Testing updateStatus(${id}, "${status}", "${type}")</div>`;
            return originalUpdateStatus(id, status, type);
        };
        
        const originalAddNote = window.addNote;
        window.addNote = function(id, type) {
            document.getElementById('testResults').innerHTML += 
                `<div class="alert alert-info">Testing addNote(${id}, "${type}")</div>`;
            return originalAddNote(id, type);
        };
        
        const originalScheduleInterview = window.scheduleInterview;
        window.scheduleInterview = function(id) {
            document.getElementById('testResults').innerHTML += 
                `<div class="alert alert-info">Testing scheduleInterview(${id})</div>`;
            return originalScheduleInterview(id);
        };
    </script>
</body>
</html>
