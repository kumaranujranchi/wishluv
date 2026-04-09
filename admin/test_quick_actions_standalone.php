<?php
// Standalone test for quick actions without authentication
require_once '../config/database.php';

// Simulate logged in session for testing
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'test_admin';
$_SESSION['admin_name'] = 'Test Administrator';

$conn = getDBConnection();

// Handle AJAX requests for testing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'test_connection':
            echo json_encode([
                'success' => true,
                'message' => 'AJAX connection working!',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;
            
        case 'update_status':
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            $type = $_POST['type'] ?? 'application';
            
            try {
                if ($type === 'lead') {
                    $stmt = $conn->prepare("UPDATE leads SET status = ?, updated_at = NOW() WHERE id = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE job_applications SET status = ?, updated_at = NOW() WHERE id = ?");
                }
                
                $result = $stmt->execute([$status, $id]);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Status updated successfully' : 'Failed to update status'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
            
        case 'add_note':
            $id = (int)$_POST['id'];
            $note = trim($_POST['note']);
            $type = $_POST['type'] ?? 'application';
            
            if (!empty($note)) {
                try {
                    $noteWithTimestamp = "[" . date('Y-m-d H:i:s') . " - Test Admin] " . $note;
                    
                    if ($type === 'lead') {
                        $stmt = $conn->prepare("UPDATE leads SET notes = CONCAT(IFNULL(notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
                    } else {
                        $stmt = $conn->prepare("UPDATE job_applications SET hr_notes = CONCAT(IFNULL(hr_notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
                    }
                    
                    $result = $stmt->execute([$noteWithTimestamp, $id]);
                    echo json_encode([
                        'success' => $result,
                        'message' => $result ? 'Note added successfully' : 'Failed to add note'
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Note cannot be empty']);
            }
            exit;
            
        case 'schedule_interview':
            $id = (int)$_POST['id'];
            $interview_date = $_POST['interview_date'];
            $interview_notes = trim($_POST['interview_notes'] ?? '');
            
            try {
                $stmt = $conn->prepare("UPDATE job_applications SET interview_date = ?, interview_notes = ?, status = 'interview', updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$interview_date, $interview_notes, $id]);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Interview scheduled successfully' : 'Failed to schedule interview'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
    }
}

// Get sample data for testing
$sampleApp = null;
$sampleLead = null;
$dbStatus = 'Connected';

try {
    $stmt = $conn->prepare("SELECT id, full_name, email, status FROM job_applications LIMIT 1");
    $stmt->execute();
    $sampleApp = $stmt->fetch();
} catch (Exception $e) {
    $appError = $e->getMessage();
}

try {
    $stmt = $conn->prepare("SELECT id, name, email, status FROM leads LIMIT 1");
    $stmt->execute();
    $sampleLead = $stmt->fetch();
} catch (Exception $e) {
    $leadError = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Actions Test - Standalone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .test-section { margin-bottom: 2rem; }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
        .test-result { margin-top: 1rem; padding: 1rem; border-radius: 0.375rem; }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .console-output { background: #000; color: #0f0; padding: 1rem; border-radius: 0.375rem; font-family: monospace; height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4"><i class="fas fa-test-tube"></i> Quick Actions Test - Standalone</h1>
        
        <!-- System Status -->
        <div class="test-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-server"></i> System Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Database:</strong> 
                            <span class="status-good"><?php echo $dbStatus; ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Sample Application:</strong> 
                            <span class="<?php echo $sampleApp ? 'status-good' : 'status-bad'; ?>">
                                <?php echo $sampleApp ? "ID: {$sampleApp['id']} - {$sampleApp['full_name']}" : 'None found'; ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Sample Lead:</strong> 
                            <span class="<?php echo $sampleLead ? 'status-good' : 'status-bad'; ?>">
                                <?php echo $sampleLead ? "ID: {$sampleLead['id']} - {$sampleLead['name']}" : 'None found'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Status -->
        <div class="test-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-code"></i> JavaScript Status</h5>
                </div>
                <div class="card-body">
                    <div id="jsStatus" class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Checking JavaScript status...
                    </div>
                    <button class="btn btn-primary" onclick="testBasicFunctions()">Test Basic Functions</button>
                    <button class="btn btn-info" onclick="testAjaxConnection()">Test AJAX Connection</button>
                </div>
            </div>
        </div>

        <!-- Quick Action Tests -->
        <div class="test-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt"></i> Quick Action Tests</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if ($sampleApp): ?>
                        <div class="col-md-6">
                            <h6>Application Actions (ID: <?php echo $sampleApp['id']; ?>)</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="addNote(<?php echo $sampleApp['id']; ?>, 'application')">
                                    <i class="fas fa-sticky-note"></i> Add Note
                                </button>
                                <button class="btn btn-info" onclick="scheduleInterview(<?php echo $sampleApp['id']; ?>)">
                                    <i class="fas fa-calendar-plus"></i> Schedule Interview
                                </button>
                                <button class="btn btn-success" onclick="updateStatus(<?php echo $sampleApp['id']; ?>, 'selected', 'application')">
                                    <i class="fas fa-check"></i> Mark Selected
                                </button>
                                <button class="btn btn-warning" onclick="updateStatus(<?php echo $sampleApp['id']; ?>, 'on_hold', 'application')">
                                    <i class="fas fa-pause"></i> Put on Hold
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($sampleLead): ?>
                        <div class="col-md-6">
                            <h6>Lead Actions (ID: <?php echo $sampleLead['id']; ?>)</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="addNote(<?php echo $sampleLead['id']; ?>, 'lead')">
                                    <i class="fas fa-sticky-note"></i> Add Note
                                </button>
                                <button class="btn btn-success" onclick="updateStatus(<?php echo $sampleLead['id']; ?>, 'contacted', 'lead')">
                                    <i class="fas fa-phone"></i> Mark Contacted
                                </button>
                                <button class="btn btn-warning" onclick="updateStatus(<?php echo $sampleLead['id']; ?>, 'qualified', 'lead')">
                                    <i class="fas fa-star"></i> Mark Qualified
                                </button>
                                <button class="btn btn-info" onclick="updateStatus(<?php echo $sampleLead['id']; ?>, 'converted', 'lead')">
                                    <i class="fas fa-check-circle"></i> Mark Converted
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="test-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-clipboard-list"></i> Test Results</h5>
                </div>
                <div class="card-body">
                    <div id="testResults">
                        <p class="text-muted">Click any button above to test functionality...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Console Output -->
        <div class="test-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-terminal"></i> Console Output</h5>
                </div>
                <div class="card-body">
                    <div id="consoleOutput" class="console-output">
                        Console initialized...<br>
                    </div>
                    <button class="btn btn-secondary btn-sm mt-2" onclick="clearConsole()">Clear Console</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Application Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="noteAppId" value="">
                    <div class="mb-3">
                        <label for="noteText" class="form-label">Note</label>
                        <textarea class="form-control" id="noteText" rows="4" placeholder="Enter your note here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote('application')">Save Note</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addLeadNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Lead Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="noteLeadId" value="">
                    <div class="mb-3">
                        <label for="noteText" class="form-label">Note</label>
                        <textarea class="form-control" id="noteText" rows="4" placeholder="Enter your note here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote('lead')">Save Note</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scheduleInterviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Interview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="interviewAppId" value="">
                    <div class="mb-3">
                        <label for="interviewDate" class="form-label">Interview Date & Time</label>
                        <input type="datetime-local" class="form-control" id="interviewDate">
                    </div>
                    <div class="mb-3">
                        <label for="interviewNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="interviewNotes" rows="3"></textarea>
                    </div>
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
        function log(message) {
            const console = document.getElementById('consoleOutput');
            const timestamp = new Date().toLocaleTimeString();
            console.innerHTML += `[${timestamp}] ${message}<br>`;
            console.scrollTop = console.scrollHeight;
        }

        function clearConsole() {
            document.getElementById('consoleOutput').innerHTML = 'Console cleared...<br>';
        }

        function addTestResult(message, type = 'info') {
            const results = document.getElementById('testResults');
            const div = document.createElement('div');
            div.className = `test-result test-${type}`;
            div.innerHTML = message;
            results.appendChild(div);
        }

        function testBasicFunctions() {
            log('Testing basic functions...');
            
            const tests = [
                { name: 'showAlert', func: window.showAlert },
                { name: 'updateStatus', func: window.updateStatus },
                { name: 'addNote', func: window.addNote },
                { name: 'scheduleInterview', func: window.scheduleInterview },
                { name: 'Bootstrap', func: window.bootstrap }
            ];
            
            let passed = 0;
            tests.forEach(test => {
                if (typeof test.func === 'function' || (test.name === 'Bootstrap' && test.func)) {
                    log(`✅ ${test.name} - OK`);
                    passed++;
                } else {
                    log(`❌ ${test.name} - MISSING`);
                }
            });
            
            const message = `Basic Functions Test: ${passed}/${tests.length} passed`;
            addTestResult(message, passed === tests.length ? 'success' : 'error');
        }

        function testAjaxConnection() {
            log('Testing AJAX connection...');
            
            fetch('test_quick_actions_standalone.php', {
                method: 'POST',
                body: new URLSearchParams({ action: 'test_connection' })
            })
            .then(response => response.json())
            .then(data => {
                log(`✅ AJAX connection successful: ${data.message}`);
                addTestResult('AJAX Connection Test: PASSED', 'success');
            })
            .catch(error => {
                log(`❌ AJAX connection failed: ${error.message}`);
                addTestResult('AJAX Connection Test: FAILED', 'error');
            });
        }

        // Check JavaScript status on load
        document.addEventListener('DOMContentLoaded', function() {
            const statusDiv = document.getElementById('jsStatus');
            
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
                log('✅ All JavaScript functions loaded successfully');
            } else {
                statusDiv.className = 'alert alert-danger';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Some JavaScript functions are missing:<br>' +
                    checks.filter(check => !check.available).map(check => `- ${check.name}`).join('<br>');
                log('❌ Some JavaScript functions are missing');
            }
        });
    </script>
</body>
</html>
