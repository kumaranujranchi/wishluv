<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get a sample application with resume for testing
$stmt = $conn->prepare("SELECT * FROM job_applications WHERE resume_path IS NOT NULL AND resume_path != '' LIMIT 1");
$stmt->execute();
$app = $stmt->fetch();

if (!$app) {
    echo '<div class="alert alert-warning">No applications with resumes found. Please submit a test application first.</div>';
    exit;
}

// Test file existence
$resume_file_path = '../' . $app['resume_path'];
$file_exists = file_exists($resume_file_path);
$file_readable = is_readable($resume_file_path);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Application Actions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>🔧 Debug Application Actions</h2>
        
        <!-- File Path Testing -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>📁 Resume File Path Testing</h5>
            </div>
            <div class="card-body">
                <p><strong>Application:</strong> <?php echo htmlspecialchars($app['full_name']); ?></p>
                <p><strong>Database Path:</strong> <code><?php echo htmlspecialchars($app['resume_path']); ?></code></p>
                <p><strong>Full File Path:</strong> <code><?php echo htmlspecialchars($resume_file_path); ?></code></p>
                <p><strong>File Exists:</strong> 
                    <span class="badge bg-<?php echo $file_exists ? 'success' : 'danger'; ?>">
                        <?php echo $file_exists ? 'YES' : 'NO'; ?>
                    </span>
                </p>
                <p><strong>File Readable:</strong> 
                    <span class="badge bg-<?php echo $file_readable ? 'success' : 'danger'; ?>">
                        <?php echo $file_readable ? 'YES' : 'NO'; ?>
                    </span>
                </p>
                
                <div class="mt-3">
                    <h6>Test Links:</h6>
                    <div class="d-grid gap-2">
                        <a href="/<?php echo htmlspecialchars($app['resume_path']); ?>"
                           target="_blank" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Current Implementation (/)
                        </a>
                        <a href="../<?php echo htmlspecialchars($app['resume_path']); ?>"
                           target="_blank" class="btn btn-secondary">
                            <i class="fas fa-file-pdf"></i> With ../ prefix
                        </a>
                        <a href="<?php echo htmlspecialchars($app['resume_path']); ?>"
                           target="_blank" class="btn btn-info">
                            <i class="fas fa-file-pdf"></i> Without prefix (relative)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Testing -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>🎯 Action Buttons Testing</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="testAddNote(<?php echo $app['id']; ?>)">
                                <i class="fas fa-sticky-note"></i> Test Add HR Note
                            </button>
                            <button class="btn btn-info" onclick="testScheduleInterview(<?php echo $app['id']; ?>)">
                                <i class="fas fa-calendar-plus"></i> Test Schedule Interview
                            </button>
                            <button class="btn btn-success" onclick="testUpdateStatus(<?php echo $app['id']; ?>, 'selected')">
                                <i class="fas fa-check-circle"></i> Test Mark as Selected
                            </button>
                            <button class="btn btn-warning" onclick="testUpdateStatus(<?php echo $app['id']; ?>, 'on_hold')">
                                <i class="fas fa-pause-circle"></i> Test Put on Hold
                            </button>
                            <button class="btn btn-danger" onclick="testUpdateStatus(<?php echo $app['id']; ?>, 'rejected')">
                                <i class="fas fa-times-circle"></i> Test Reject Application
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Contact Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-envelope"></i> Test Send Email
                            </a>
                            <a href="tel:<?php echo htmlspecialchars($app['phone']); ?>" class="btn btn-outline-success">
                                <i class="fas fa-phone"></i> Test Call Now
                            </a>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $app['phone']); ?>" 
                               target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i> Test WhatsApp
                            </a>
                            <button class="btn btn-outline-secondary" onclick="testViewResume()">
                                <i class="fas fa-file-pdf"></i> Test View Resume
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Console Output -->
        <div class="card">
            <div class="card-header">
                <h5>📊 Test Results</h5>
            </div>
            <div class="card-body">
                <div id="testResults" class="alert alert-info">
                    Click the test buttons above to see results here...
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="job_applications.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Applications
            </a>
        </div>
    </div>

    <script>
    // Test functions
    function logResult(message, type = 'info') {
        const resultsDiv = document.getElementById('testResults');
        const timestamp = new Date().toLocaleTimeString();
        resultsDiv.innerHTML += `<div class="alert alert-${type} alert-sm mb-1">[${timestamp}] ${message}</div>`;
        resultsDiv.scrollTop = resultsDiv.scrollHeight;
    }

    function testAddNote(appId) {
        logResult('Testing Add Note functionality...', 'info');
        
        const formData = new FormData();
        formData.append('action', 'add_note');
        formData.append('id', appId);
        formData.append('note', 'Test note from debug page');

        fetch('job_applications.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                logResult('✅ Add Note: SUCCESS', 'success');
            } else {
                logResult('❌ Add Note: FAILED - ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            logResult('❌ Add Note: ERROR - ' + error.message, 'danger');
            console.error('Add Note Error:', error);
        });
    }

    function testScheduleInterview(appId) {
        logResult('Testing Schedule Interview functionality...', 'info');
        
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const interviewDate = tomorrow.toISOString().slice(0, 16);

        const formData = new FormData();
        formData.append('action', 'schedule_interview');
        formData.append('id', appId);
        formData.append('interview_date', interviewDate);
        formData.append('interview_notes', 'Test interview from debug page');

        fetch('job_applications.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                logResult('✅ Schedule Interview: SUCCESS', 'success');
            } else {
                logResult('❌ Schedule Interview: FAILED - ' + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            logResult('❌ Schedule Interview: ERROR - ' + error.message, 'danger');
            console.error('Schedule Interview Error:', error);
        });
    }

    function testUpdateStatus(appId, status) {
        logResult(`Testing Update Status to '${status}'...`, 'info');
        
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
                logResult(`✅ Update Status to '${status}': SUCCESS`, 'success');
            } else {
                logResult(`❌ Update Status to '${status}': FAILED - ` + (data.error || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            logResult(`❌ Update Status to '${status}': ERROR - ` + error.message, 'danger');
            console.error('Update Status Error:', error);
        });
    }

    function testViewResume() {
        logResult('Testing View Resume functionality...', 'info');
        
        const resumeUrl = '../<?php echo htmlspecialchars($app['resume_path']); ?>';
        
        // Test if URL is accessible
        fetch(resumeUrl, { method: 'HEAD' })
        .then(response => {
            if (response.ok) {
                logResult('✅ View Resume: File accessible, opening in new tab...', 'success');
                window.open(resumeUrl, '_blank');
            } else {
                logResult(`❌ View Resume: File not accessible (HTTP ${response.status})`, 'danger');
            }
        })
        .catch(error => {
            logResult('❌ View Resume: ERROR - ' + error.message, 'danger');
            console.error('View Resume Error:', error);
        });
    }

    // Clear results
    function clearResults() {
        document.getElementById('testResults').innerHTML = 'Test results cleared...';
    }
    </script>
</body>
</html>
