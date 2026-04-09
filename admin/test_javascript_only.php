<?php
// JavaScript-only test for quick actions (no database required)

// Handle AJAX requests with mock responses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // Simulate processing delay
    usleep(500000); // 0.5 second delay
    
    switch ($_POST['action']) {
        case 'test_connection':
            echo json_encode([
                'success' => true,
                'message' => 'AJAX connection working perfectly!',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;
            
        case 'update_status':
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            $type = $_POST['type'] ?? 'application';
            
            echo json_encode([
                'success' => true,
                'message' => "Status updated to '{$status}' for {$type} ID {$id}",
                'data' => ['id' => $id, 'status' => $status, 'type' => $type]
            ]);
            exit;
            
        case 'add_note':
            $id = (int)$_POST['id'];
            $note = trim($_POST['note']);
            $type = $_POST['type'] ?? 'application';
            
            if (!empty($note)) {
                echo json_encode([
                    'success' => true,
                    'message' => "Note added successfully to {$type} ID {$id}",
                    'data' => ['id' => $id, 'note' => $note, 'type' => $type]
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Note cannot be empty']);
            }
            exit;
            
        case 'schedule_interview':
            $id = (int)$_POST['id'];
            $interview_date = $_POST['interview_date'];
            $interview_notes = trim($_POST['interview_notes'] ?? '');
            
            echo json_encode([
                'success' => true,
                'message' => "Interview scheduled successfully for application ID {$id}",
                'data' => ['id' => $id, 'interview_date' => $interview_date, 'interview_notes' => $interview_notes]
            ]);
            exit;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaScript Quick Actions Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .test-section { margin-bottom: 2rem; }
        .status-indicator { padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .status-warning { background-color: #fff3cd; color: #856404; }
        .console-output { background: #000; color: #0f0; padding: 1rem; border-radius: 0.375rem; font-family: monospace; height: 300px; overflow-y: auto; }
        .test-result { margin: 0.5rem 0; padding: 0.75rem; border-radius: 0.375rem; border-left: 4px solid; }
        .test-success { background-color: #d4edda; border-color: #28a745; color: #155724; }
        .test-error { background-color: #f8d7da; border-color: #dc3545; color: #721c24; }
        .test-info { background-color: #d1ecf1; border-color: #17a2b8; color: #0c5460; }
        .btn-test { margin: 0.25rem; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-bolt text-warning"></i> 
                    Quick Actions JavaScript Test
                </h1>
                <p class="lead">Testing all quick action functionality without database dependencies</p>
            </div>
        </div>

        <!-- JavaScript Status Check -->
        <div class="test-section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> JavaScript Function Status</h5>
                </div>
                <div class="card-body">
                    <div id="jsStatus" class="mb-3">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <span>Checking JavaScript functions...</span>
                        </div>
                    </div>
                    <div class="row" id="functionChecks"></div>
                </div>
            </div>
        </div>

        <!-- AJAX Connection Test -->
        <div class="test-section">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-wifi"></i> AJAX Connection Test</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-info" onclick="testAjaxConnection()">
                        <i class="fas fa-satellite-dish"></i> Test AJAX Connection
                    </button>
                    <div id="ajaxStatus" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Quick Action Tests -->
        <div class="test-section">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-rocket"></i> Quick Action Function Tests</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-briefcase"></i> Application Actions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-test" onclick="testAddNote(123, 'application')">
                                    <i class="fas fa-sticky-note"></i> Test Add Application Note
                                </button>
                                <button class="btn btn-info btn-test" onclick="testScheduleInterview(123)">
                                    <i class="fas fa-calendar-plus"></i> Test Schedule Interview
                                </button>
                                <button class="btn btn-success btn-test" onclick="testUpdateStatus(123, 'selected', 'application')">
                                    <i class="fas fa-check"></i> Test Mark as Selected
                                </button>
                                <button class="btn btn-warning btn-test" onclick="testUpdateStatus(123, 'on_hold', 'application')">
                                    <i class="fas fa-pause"></i> Test Put on Hold
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users"></i> Lead Actions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-test" onclick="testAddNote(456, 'lead')">
                                    <i class="fas fa-sticky-note"></i> Test Add Lead Note
                                </button>
                                <button class="btn btn-success btn-test" onclick="testUpdateStatus(456, 'contacted', 'lead')">
                                    <i class="fas fa-phone"></i> Test Mark as Contacted
                                </button>
                                <button class="btn btn-warning btn-test" onclick="testUpdateStatus(456, 'qualified', 'lead')">
                                    <i class="fas fa-star"></i> Test Mark as Qualified
                                </button>
                                <button class="btn btn-info btn-test" onclick="testUpdateStatus(456, 'converted', 'lead')">
                                    <i class="fas fa-check-circle"></i> Test Mark as Converted
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tests -->
        <div class="test-section">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-window-maximize"></i> Modal Function Tests</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary" onclick="addNote(123, 'application')">
                                <i class="fas fa-sticky-note"></i> Open Application Note Modal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-success" onclick="addNote(456, 'lead')">
                                <i class="fas fa-sticky-note"></i> Open Lead Note Modal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-info" onclick="scheduleInterview(123)">
                                <i class="fas fa-calendar-plus"></i> Open Interview Modal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="test-section">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Test Results</h5>
                </div>
                <div class="card-body">
                    <div id="testResults">
                        <p class="text-muted">Test results will appear here...</p>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearResults()">
                        <i class="fas fa-trash"></i> Clear Results
                    </button>
                </div>
            </div>
        </div>

        <!-- Console Output -->
        <div class="test-section">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-terminal"></i> Console Output</h5>
                </div>
                <div class="card-body">
                    <div id="consoleOutput" class="console-output">
                        [SYSTEM] Console initialized - Quick Actions Test v1.0<br>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearConsole()">
                            <i class="fas fa-eraser"></i> Clear Console
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="runAllTests()">
                            <i class="fas fa-play"></i> Run All Tests
                        </button>
                    </div>
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
        let testCount = 0;
        let passedTests = 0;

        function log(message, type = 'info') {
            const console = document.getElementById('consoleOutput');
            const timestamp = new Date().toLocaleTimeString();
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
            console.innerHTML += `[${timestamp}] ${icon} ${message}<br>`;
            console.scrollTop = console.scrollHeight;
        }

        function clearConsole() {
            document.getElementById('consoleOutput').innerHTML = '[SYSTEM] Console cleared<br>';
        }

        function addTestResult(message, type = 'info') {
            const results = document.getElementById('testResults');
            const div = document.createElement('div');
            div.className = `test-result test-${type}`;
            div.innerHTML = `<strong>Test ${++testCount}:</strong> ${message}`;
            results.appendChild(div);
            
            if (type === 'success') passedTests++;
        }

        function clearResults() {
            document.getElementById('testResults').innerHTML = '<p class="text-muted">Test results cleared...</p>';
            testCount = 0;
            passedTests = 0;
        }

        function checkJavaScriptFunctions() {
            log('Checking JavaScript function availability...', 'info');
            
            const functions = [
                { name: 'showAlert', func: window.showAlert, required: true },
                { name: 'updateStatus', func: window.updateStatus, required: true },
                { name: 'addNote', func: window.addNote, required: true },
                { name: 'scheduleInterview', func: window.scheduleInterview, required: true },
                { name: 'saveNote', func: window.saveNote, required: true },
                { name: 'saveInterview', func: window.saveInterview, required: true },
                { name: 'Bootstrap', func: window.bootstrap, required: true }
            ];
            
            const statusDiv = document.getElementById('jsStatus');
            const checksDiv = document.getElementById('functionChecks');
            let allPassed = true;
            
            checksDiv.innerHTML = '';
            
            functions.forEach(func => {
                const isAvailable = typeof func.func === 'function' || (func.name === 'Bootstrap' && func.func);
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-2';
                
                const statusClass = isAvailable ? 'status-success' : 'status-error';
                const icon = isAvailable ? 'fas fa-check-circle' : 'fas fa-times-circle';
                
                col.innerHTML = `
                    <div class="status-indicator ${statusClass}">
                        <i class="${icon}"></i> ${func.name}
                    </div>
                `;
                
                checksDiv.appendChild(col);
                
                if (func.required && !isAvailable) {
                    allPassed = false;
                    log(`Missing required function: ${func.name}`, 'error');
                } else if (isAvailable) {
                    log(`Function available: ${func.name}`, 'success');
                }
            });
            
            if (allPassed) {
                statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> All JavaScript functions loaded successfully!
                    </div>
                `;
                addTestResult('JavaScript Functions Check: PASSED', 'success');
            } else {
                statusDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Some required JavaScript functions are missing!
                    </div>
                `;
                addTestResult('JavaScript Functions Check: FAILED', 'error');
            }
        }

        function testAjaxConnection() {
            log('Testing AJAX connection...', 'info');
            
            fetch('test_javascript_only.php', {
                method: 'POST',
                body: new URLSearchParams({ action: 'test_connection' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`AJAX connection successful: ${data.message}`, 'success');
                    document.getElementById('ajaxStatus').innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> ${data.message}
                        </div>
                    `;
                    addTestResult('AJAX Connection: PASSED', 'success');
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                log(`AJAX connection failed: ${error.message}`, 'error');
                document.getElementById('ajaxStatus').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Connection failed: ${error.message}
                    </div>
                `;
                addTestResult('AJAX Connection: FAILED', 'error');
            });
        }

        function testUpdateStatus(id, status, type) {
            log(`Testing updateStatus(${id}, ${status}, ${type})...`, 'info');
            
            fetch('test_javascript_only.php', {
                method: 'POST',
                body: new URLSearchParams({ 
                    action: 'update_status',
                    id: id,
                    status: status,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`Status update successful: ${data.message}`, 'success');
                    addTestResult(`Status Update (${type}): PASSED - ${data.message}`, 'success');
                    if (window.showAlert) {
                        window.showAlert(data.message, 'success');
                    }
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                log(`Status update failed: ${error.message}`, 'error');
                addTestResult(`Status Update (${type}): FAILED - ${error.message}`, 'error');
            });
        }

        function testAddNote(id, type) {
            log(`Testing addNote(${id}, ${type})...`, 'info');
            
            const testNote = `Test note added at ${new Date().toLocaleString()}`;
            
            fetch('test_javascript_only.php', {
                method: 'POST',
                body: new URLSearchParams({ 
                    action: 'add_note',
                    id: id,
                    note: testNote,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`Add note successful: ${data.message}`, 'success');
                    addTestResult(`Add Note (${type}): PASSED - ${data.message}`, 'success');
                    if (window.showAlert) {
                        window.showAlert(data.message, 'success');
                    }
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                log(`Add note failed: ${error.message}`, 'error');
                addTestResult(`Add Note (${type}): FAILED - ${error.message}`, 'error');
            });
        }

        function testScheduleInterview(id) {
            log(`Testing scheduleInterview(${id})...`, 'info');
            
            const testDate = new Date();
            testDate.setDate(testDate.getDate() + 7); // Next week
            const interviewDate = testDate.toISOString().slice(0, 16);
            
            fetch('test_javascript_only.php', {
                method: 'POST',
                body: new URLSearchParams({ 
                    action: 'schedule_interview',
                    id: id,
                    interview_date: interviewDate,
                    interview_notes: 'Test interview scheduled via automation'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`Schedule interview successful: ${data.message}`, 'success');
                    addTestResult(`Schedule Interview: PASSED - ${data.message}`, 'success');
                    if (window.showAlert) {
                        window.showAlert(data.message, 'success');
                    }
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                log(`Schedule interview failed: ${error.message}`, 'error');
                addTestResult(`Schedule Interview: FAILED - ${error.message}`, 'error');
            });
        }

        function runAllTests() {
            log('Running comprehensive test suite...', 'info');
            clearResults();
            
            // Test sequence with delays
            setTimeout(() => checkJavaScriptFunctions(), 100);
            setTimeout(() => testAjaxConnection(), 500);
            setTimeout(() => testUpdateStatus(123, 'selected', 'application'), 1000);
            setTimeout(() => testUpdateStatus(456, 'contacted', 'lead'), 1500);
            setTimeout(() => testAddNote(123, 'application'), 2000);
            setTimeout(() => testAddNote(456, 'lead'), 2500);
            setTimeout(() => testScheduleInterview(123), 3000);
            
            setTimeout(() => {
                log(`Test suite completed: ${passedTests}/${testCount} tests passed`, passedTests === testCount ? 'success' : 'warning');
                if (window.showAlert) {
                    const message = `Test Suite Complete: ${passedTests}/${testCount} tests passed`;
                    const type = passedTests === testCount ? 'success' : 'warning';
                    window.showAlert(message, type);
                }
            }, 3500);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            log('Page loaded, initializing tests...', 'info');
            setTimeout(() => checkJavaScriptFunctions(), 500);
        });
    </script>
</body>
</html>
