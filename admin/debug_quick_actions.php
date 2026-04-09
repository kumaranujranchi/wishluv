<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Test database connection
$dbStatus = 'Connected';
try {
    $conn->query('SELECT 1');
} catch (Exception $e) {
    $dbStatus = 'Error: ' . $e->getMessage();
}

// Test if we can fetch sample data
$sampleApp = null;
$sampleLead = null;

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

// Handle test AJAX requests
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
            
        case 'test_update_status':
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            $type = $_POST['type'] ?? 'application';
            
            if ($type === 'lead') {
                $stmt = $conn->prepare("UPDATE leads SET status = ?, updated_at = NOW() WHERE id = ?");
            } else {
                $stmt = $conn->prepare("UPDATE job_applications SET status = ?, updated_at = NOW() WHERE id = ?");
            }
            
            $result = $stmt->execute([$status, $id]);
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Status updated successfully' : 'Failed to update status',
                'data' => ['id' => $id, 'status' => $status, 'type' => $type]
            ]);
            exit;
            
        case 'test_add_note':
            $id = (int)$_POST['id'];
            $note = trim($_POST['note']);
            $type = $_POST['type'] ?? 'application';
            
            if (!empty($note)) {
                $noteWithTimestamp = "[" . date('Y-m-d H:i:s') . " - Debug Test] " . $note;
                
                if ($type === 'lead') {
                    $stmt = $conn->prepare("UPDATE leads SET notes = CONCAT(IFNULL(notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE job_applications SET hr_notes = CONCAT(IFNULL(hr_notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
                }
                
                $result = $stmt->execute([$noteWithTimestamp, $id]);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Note added successfully' : 'Failed to add note',
                    'data' => ['id' => $id, 'note' => $note, 'type' => $type]
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Note cannot be empty']);
            }
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Quick Actions - Wishluv Buildcon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .debug-section { margin-bottom: 2rem; }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
        .test-result { margin-top: 1rem; padding: 1rem; border-radius: 0.375rem; }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4"><i class="fas fa-bug"></i> Quick Actions Debug Tool</h1>
        
        <!-- System Status -->
        <div class="debug-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-server"></i> System Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Database:</strong> 
                            <span class="<?php echo $dbStatus === 'Connected' ? 'status-good' : 'status-bad'; ?>">
                                <?php echo $dbStatus; ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Sample Application:</strong> 
                            <span class="<?php echo $sampleApp ? 'status-good' : 'status-bad'; ?>">
                                <?php echo $sampleApp ? "ID: {$sampleApp['id']}" : 'None found'; ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Sample Lead:</strong> 
                            <span class="<?php echo $sampleLead ? 'status-good' : 'status-bad'; ?>">
                                <?php echo $sampleLead ? "ID: {$sampleLead['id']}" : 'None found'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Tests -->
        <div class="debug-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-code"></i> JavaScript Function Tests</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Functions</h6>
                            <button class="btn btn-primary btn-sm" onclick="testBasicFunctions()">Test Basic Functions</button>
                            <button class="btn btn-info btn-sm" onclick="testAjaxConnection()">Test AJAX Connection</button>
                        </div>
                        <div class="col-md-6">
                            <h6>Quick Actions</h6>
                            <?php if ($sampleApp): ?>
                            <button class="btn btn-success btn-sm" onclick="testUpdateStatus(<?php echo $sampleApp['id']; ?>, 'selected', 'application')">Test App Status Update</button>
                            <button class="btn btn-warning btn-sm" onclick="testAddNote(<?php echo $sampleApp['id']; ?>, 'application')">Test App Note</button>
                            <?php endif; ?>
                            <?php if ($sampleLead): ?>
                            <button class="btn btn-success btn-sm" onclick="testUpdateStatus(<?php echo $sampleLead['id']; ?>, 'contacted', 'lead')">Test Lead Status Update</button>
                            <button class="btn btn-warning btn-sm" onclick="testAddNote(<?php echo $sampleLead['id']; ?>, 'lead')">Test Lead Note</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="testResults" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Manual Test Buttons -->
        <div class="debug-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-hand-pointer"></i> Manual Quick Action Tests</h5>
                </div>
                <div class="card-body">
                    <?php if ($sampleApp): ?>
                    <div class="mb-3">
                        <h6>Test Application Actions (ID: <?php echo $sampleApp['id']; ?>)</h6>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary" onclick="addNote(<?php echo $sampleApp['id']; ?>, 'application')">
                                <i class="fas fa-sticky-note"></i> Add Note
                            </button>
                            <button class="btn btn-info" onclick="scheduleInterview(<?php echo $sampleApp['id']; ?>)">
                                <i class="fas fa-calendar-plus"></i> Schedule Interview
                            </button>
                            <button class="btn btn-success" onclick="updateStatus(<?php echo $sampleApp['id']; ?>, 'selected', 'application')">
                                <i class="fas fa-check"></i> Mark Selected
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($sampleLead): ?>
                    <div class="mb-3">
                        <h6>Test Lead Actions (ID: <?php echo $sampleLead['id']; ?>)</h6>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary" onclick="addNote(<?php echo $sampleLead['id']; ?>, 'lead')">
                                <i class="fas fa-sticky-note"></i> Add Note
                            </button>
                            <button class="btn btn-success" onclick="updateStatus(<?php echo $sampleLead['id']; ?>, 'contacted', 'lead')">
                                <i class="fas fa-phone"></i> Mark Contacted
                            </button>
                            <button class="btn btn-warning" onclick="updateStatus(<?php echo $sampleLead['id']; ?>, 'qualified', 'lead')">
                                <i class="fas fa-star"></i> Mark Qualified
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Console Output -->
        <div class="debug-section">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-terminal"></i> Console Output</h5>
                </div>
                <div class="card-body">
                    <div id="consoleOutput" style="background: #000; color: #0f0; padding: 1rem; border-radius: 0.375rem; font-family: monospace; height: 200px; overflow-y: auto;">
                        Console initialized...<br>
                    </div>
                    <button class="btn btn-secondary btn-sm mt-2" onclick="clearConsole()">Clear Console</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Modals -->
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
            
            fetch('debug_quick_actions.php', {
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

        function testUpdateStatus(id, status, type) {
            log(`Testing updateStatus(${id}, ${status}, ${type})...`);
            
            fetch('debug_quick_actions.php', {
                method: 'POST',
                body: new URLSearchParams({ 
                    action: 'test_update_status',
                    id: id,
                    status: status,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`✅ Status update successful: ${data.message}`);
                    addTestResult(`Status Update Test (${type}): PASSED`, 'success');
                } else {
                    log(`❌ Status update failed: ${data.message}`);
                    addTestResult(`Status Update Test (${type}): FAILED`, 'error');
                }
            })
            .catch(error => {
                log(`❌ Status update error: ${error.message}`);
                addTestResult(`Status Update Test (${type}): ERROR`, 'error');
            });
        }

        function testAddNote(id, type) {
            log(`Testing addNote(${id}, ${type})...`);
            
            const testNote = `Test note added at ${new Date().toLocaleString()}`;
            
            fetch('debug_quick_actions.php', {
                method: 'POST',
                body: new URLSearchParams({ 
                    action: 'test_add_note',
                    id: id,
                    note: testNote,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`✅ Add note successful: ${data.message}`);
                    addTestResult(`Add Note Test (${type}): PASSED`, 'success');
                } else {
                    log(`❌ Add note failed: ${data.message}`);
                    addTestResult(`Add Note Test (${type}): FAILED`, 'error');
                }
            })
            .catch(error => {
                log(`❌ Add note error: ${error.message}`);
                addTestResult(`Add Note Test (${type}): ERROR`, 'error');
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            log('Debug tool initialized');
            testBasicFunctions();
        });
    </script>
</body>
</html>
