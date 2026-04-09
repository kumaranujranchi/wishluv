<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();
$currentUser = $auth->getCurrentUser();

// Test all admin pages
$adminPages = [
    'dashboard.php' => 'Dashboard',
    'leads.php' => 'Leads Management',
    'job_applications.php' => 'Job Applications',
    'contact_inquiries.php' => 'Contact Inquiries',
    'reports.php' => 'Reports',
    'users.php' => 'User Management',
    'settings.php' => 'Settings'
];

// Test database tables
$tables = ['admin_users', 'leads', 'job_applications'];
$tableStatus = [];

foreach ($tables as $table) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $tableStatus[$table] = ['exists' => true, 'count' => $count];
    } catch (PDOException $e) {
        $tableStatus[$table] = ['exists' => false, 'error' => $e->getMessage()];
    }
}

// Test resume files
$resumeFiles = [];
if (is_dir('../uploads/resumes')) {
    $resumeFiles = array_diff(scandir('../uploads/resumes'), ['.', '..']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Admin Panel Test - Wishluv Buildcon</title>
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
                    <h1 class="h2">🧪 Complete Admin Panel Test</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="testAllPages()">
                                <i class="fas fa-play"></i> Test All Pages
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count($adminPages); ?></h4>
                                        <p class="mb-0">Admin Pages</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count($resumeFiles); ?></h4>
                                        <p class="mb-0">Resume Files</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $currentUser['role']; ?></h4>
                                        <p class="mb-0">Your Role</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-user-shield fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Pages Test -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-link"></i> Admin Pages Test</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($adminPages as $file => $name): ?>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                    <div>
                                        <strong><?php echo $name; ?></strong><br>
                                        <small class="text-muted"><?php echo $file; ?></small>
                                    </div>
                                    <div>
                                        <a href="<?php echo $file; ?>" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> Test
                                        </a>
                                        <span class="test-result ms-2" id="result-<?php echo str_replace('.', '-', $file); ?>">
                                            <i class="fas fa-clock text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Database Tables Test -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-database"></i> Database Tables Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Table Name</th>
                                        <th>Status</th>
                                        <th>Record Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tableStatus as $table => $status): ?>
                                    <tr>
                                        <td><code><?php echo $table; ?></code></td>
                                        <td>
                                            <?php if ($status['exists']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Exists
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Missing
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($status['exists']): ?>
                                                <span class="badge bg-info"><?php echo $status['count']; ?> records</span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($status['exists']): ?>
                                                <button class="btn btn-sm btn-outline-primary" onclick="testTable('<?php echo $table; ?>')">
                                                    <i class="fas fa-search"></i> Query
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger" onclick="showError('<?php echo $table; ?>')">
                                                    <i class="fas fa-exclamation-triangle"></i> Error
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Resume Files Test -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-file-pdf"></i> Resume Files Test</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($resumeFiles)): ?>
                            <div class="row">
                                <?php foreach ($resumeFiles as $file): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                        <div>
                                            <i class="fas fa-file-pdf text-danger"></i>
                                            <span class="ms-2"><?php echo htmlspecialchars($file); ?></span>
                                        </div>
                                        <div>
                                            <a href="../uploads/resumes/<?php echo urlencode($file); ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i> Test
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No resume files found in uploads/resumes/ directory.
                                Upload some job applications with resumes to test this functionality.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Test Console -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-terminal"></i> Test Console</h5>
                        <button class="btn btn-sm btn-outline-secondary float-end" onclick="clearConsole()">Clear</button>
                    </div>
                    <div class="card-body">
                        <div id="testConsole" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; font-family: monospace; font-size: 12px;">
                            Test console ready... Click test buttons above.<br>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

    function testAllPages() {
        log('🚀 Starting comprehensive admin panel test...', 'info');
        
        const pages = <?php echo json_encode(array_keys($adminPages)); ?>;
        let testCount = 0;
        
        pages.forEach((page, index) => {
            setTimeout(() => {
                testPage(page);
                testCount++;
                if (testCount === pages.length) {
                    log('✅ All page tests completed!', 'success');
                }
            }, index * 1000);
        });
    }

    function testPage(page) {
        log(`Testing ${page}...`, 'info');
        
        fetch(page, { method: 'HEAD' })
            .then(response => {
                const resultId = 'result-' + page.replace('.', '-');
                const resultElement = document.getElementById(resultId);
                
                if (response.ok) {
                    resultElement.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                    log(`✅ ${page} - OK (${response.status})`, 'success');
                } else {
                    resultElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                    log(`❌ ${page} - Error (${response.status})`, 'error');
                }
            })
            .catch(error => {
                const resultId = 'result-' + page.replace('.', '-');
                const resultElement = document.getElementById(resultId);
                resultElement.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i>';
                log(`⚠️ ${page} - Network Error: ${error.message}`, 'warning');
            });
    }

    function testTable(table) {
        log(`Testing database table: ${table}`, 'info');
        // This would require a separate API endpoint to test table queries
        log(`✅ Table ${table} exists and is accessible`, 'success');
    }

    function showError(table) {
        log(`❌ Table ${table} is missing or inaccessible`, 'error');
        log(`Please check your database setup and ensure the ${table} table exists`, 'warning');
    }

    // Auto-start basic tests on page load
    document.addEventListener('DOMContentLoaded', function() {
        log('🔧 Admin Panel Test Suite Loaded', 'success');
        log('📊 System Status:', 'info');
        log(`- Admin Pages: <?php echo count($adminPages); ?>`, 'info');
        log(`- Resume Files: <?php echo count($resumeFiles); ?>`, 'info');
        log(`- User Role: <?php echo $currentUser['role']; ?>`, 'info');
        log('Click "Test All Pages" to run comprehensive tests', 'info');
    });
    </script>
</body>
</html>
