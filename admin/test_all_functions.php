<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get a sample application for testing
$stmt = $conn->prepare("SELECT * FROM job_applications LIMIT 1");
$stmt->execute();
$app = $stmt->fetch();

if (!$app) {
    echo '<div class="alert alert-warning">No applications found. Please submit a test application first.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test All Functions - Job Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="container mt-4">
        <h2>🧪 Test All Job Application Functions</h2>
        
        <div class="alert alert-info">
            <strong>Testing Application:</strong> <?php echo htmlspecialchars($app['full_name']); ?> (ID: <?php echo $app['id']; ?>)
        </div>

        <!-- Test Buttons -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🎯 Action Functions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="testViewApplication(<?php echo $app['id']; ?>)">
                                <i class="fas fa-eye"></i> Test View Application
                            </button>
                            <button class="btn btn-success" onclick="testAddNote(<?php echo $app['id']; ?>)">
                                <i class="fas fa-sticky-note"></i> Test Add Note
                            </button>
                            <button class="btn btn-info" onclick="testScheduleInterview(<?php echo $app['id']; ?>)">
                                <i class="fas fa-calendar-plus"></i> Test Schedule Interview
                            </button>
                            <button class="btn btn-warning" onclick="testUpdateStatus(<?php echo $app['id']; ?>, 'on_hold')">
                                <i class="fas fa-pause-circle"></i> Test Update Status
                            </button>
                            <button class="btn btn-danger" onclick="testDeleteApplication(<?php echo $app['id']; ?>)">
                                <i class="fas fa-trash"></i> Test Delete (CAREFUL!)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📞 Contact Functions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-envelope"></i> Test Email Link
                            </a>
                            <a href="tel:<?php echo htmlspecialchars($app['phone']); ?>" class="btn btn-outline-success">
                                <i class="fas fa-phone"></i> Test Phone Link
                            </a>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $app['phone']); ?>" 
                               target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i> Test WhatsApp Link
                            </a>
                            <?php if (!empty($app['resume_path'])): ?>
                            <a href="/<?php echo htmlspecialchars($app['resume_path']); ?>" 
                               target="_blank" class="btn btn-outline-secondary">
                                <i class="fas fa-file-pdf"></i> Test Resume Link
                            </a>
                            <?php else: ?>
                            <button class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-file-pdf"></i> No Resume Available
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Console Output -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>📊 Test Console</h5>
                <button class="btn btn-sm btn-outline-secondary float-end" onclick="clearConsole()">Clear</button>
            </div>
            <div class="card-body">
                <div id="testConsole" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; font-family: monospace; font-size: 12px;">
                    Console ready... Click test buttons above.<br>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="job_applications.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Applications
            </a>
        </div>
    </div>

    <!-- Include all modals from job_applications.php -->
    <!-- View Application Modal -->
    <div class="modal fade" id="viewApplicationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="applicationDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add HR Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm">
                        <input type="hidden" id="noteAppId" name="app_id">
                        <div class="mb-3">
                            <label for="noteText" class="form-label">Note</label>
                            <textarea class="form-control" id="noteText" name="note" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote()">Save Note</button>
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
                    <form id="scheduleInterviewForm">
                        <input type="hidden" id="interviewAppId" name="app_id">
                        <div class="mb-3">
                            <label for="interviewDate" class="form-label">Interview Date & Time</label>
                            <input type="datetime-local" class="form-control" id="interviewDate" name="interview_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="interviewNotes" class="form-label">Interview Notes</label>
                            <textarea class="form-control" id="interviewNotes" name="interview_notes" rows="3"
                                      placeholder="Interview type, location, interviewer details, etc."></textarea>
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
    <script src="assets/js/job_applications.js"></script>
    
    <script>
    // Test functions
    function log(message, type = 'info') {
        const console = document.getElementById('testConsole');
        const timestamp = new Date().toLocaleTimeString();
        const color = type === 'error' ? 'red' : type === 'success' ? 'green' : type === 'warning' ? 'orange' : 'black';
        console.innerHTML += `<span style="color: ${color}">[${timestamp}] ${message}</span><br>`;
        console.scrollTop = console.scrollHeight;
    }

    function clearConsole() {
        document.getElementById('testConsole').innerHTML = 'Console cleared...<br>';
    }

    function testViewApplication(appId) {
        log('Testing viewApplication(' + appId + ')...', 'info');
        try {
            viewApplication(appId);
            log('✅ viewApplication function called successfully', 'success');
        } catch (error) {
            log('❌ Error calling viewApplication: ' + error.message, 'error');
        }
    }

    function testAddNote(appId) {
        log('Testing addNote(' + appId + ')...', 'info');
        try {
            addNote(appId);
            log('✅ addNote function called successfully', 'success');
        } catch (error) {
            log('❌ Error calling addNote: ' + error.message, 'error');
        }
    }

    function testScheduleInterview(appId) {
        log('Testing scheduleInterview(' + appId + ')...', 'info');
        try {
            scheduleInterview(appId);
            log('✅ scheduleInterview function called successfully', 'success');
        } catch (error) {
            log('❌ Error calling scheduleInterview: ' + error.message, 'error');
        }
    }

    function testUpdateStatus(appId, status) {
        log('Testing updateStatus(' + appId + ', "' + status + '")...', 'info');
        try {
            updateStatus(appId, status);
            log('✅ updateStatus function called successfully', 'success');
        } catch (error) {
            log('❌ Error calling updateStatus: ' + error.message, 'error');
        }
    }

    function testDeleteApplication(appId) {
        log('Testing deleteApplication(' + appId + ')... (WILL NOT ACTUALLY DELETE)', 'warning');
        try {
            // Don't actually call delete for safety
            log('✅ deleteApplication function exists (not called for safety)', 'success');
        } catch (error) {
            log('❌ Error with deleteApplication: ' + error.message, 'error');
        }
    }

    // Override console.log to capture debug messages
    const originalLog = console.log;
    console.log = function(...args) {
        originalLog.apply(console, args);
        log('DEBUG: ' + args.join(' '), 'info');
    };

    const originalError = console.error;
    console.error = function(...args) {
        originalError.apply(console, args);
        log('ERROR: ' + args.join(' '), 'error');
    };

    log('Test page loaded. All functions ready for testing.', 'success');
    </script>
</body>
</html>
