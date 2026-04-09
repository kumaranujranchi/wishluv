<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get applications with resumes for testing
$stmt = $conn->prepare("SELECT id, full_name, resume_path FROM job_applications WHERE resume_path IS NOT NULL AND resume_path != '' LIMIT 5");
$stmt->execute();
$applications = $stmt->fetchAll();

// Test results
$test_results = [
    'download_script_exists' => file_exists('download_resume.php'),
    'uploads_directory_exists' => is_dir('../uploads/resumes'),
    'uploads_readable' => is_readable('../uploads/resumes'),
    'applications_with_resumes' => count($applications)
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Resume Fix - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">Resume Download Fix - Test Results</h1>
                </div>

                <!-- System Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-check-circle"></i> System Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Download Script Exists:</span>
                                    <span class="badge bg-<?php echo $test_results['download_script_exists'] ? 'success' : 'danger'; ?>">
                                        <?php echo $test_results['download_script_exists'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Uploads Directory Exists:</span>
                                    <span class="badge bg-<?php echo $test_results['uploads_directory_exists'] ? 'success' : 'danger'; ?>">
                                        <?php echo $test_results['uploads_directory_exists'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Uploads Directory Readable:</span>
                                    <span class="badge bg-<?php echo $test_results['uploads_readable'] ? 'success' : 'danger'; ?>">
                                        <?php echo $test_results['uploads_readable'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Applications with Resumes:</span>
                                    <span class="badge bg-<?php echo $test_results['applications_with_resumes'] > 0 ? 'success' : 'warning'; ?>">
                                        <?php echo $test_results['applications_with_resumes']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Applications -->
                <?php if (!empty($applications)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-test-tube"></i> Test Resume Downloads</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Applicant Name</th>
                                        <th>Resume Path</th>
                                        <th>File Exists</th>
                                        <th>Test Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                    <?php 
                                    $file_path = '../' . $app['resume_path'];
                                    $file_exists = file_exists($file_path);
                                    ?>
                                    <tr>
                                        <td><?php echo $app['id']; ?></td>
                                        <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                        <td><code><?php echo htmlspecialchars($app['resume_path']); ?></code></td>
                                        <td>
                                            <span class="badge bg-<?php echo $file_exists ? 'success' : 'danger'; ?>">
                                                <?php echo $file_exists ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($file_exists): ?>
                                                <a href="download_resume.php?id=<?php echo $app['id']; ?>" 
                                                   target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> Test Download
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">File not found</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>No applications with resumes found.</strong> 
                    Submit a test job application with a resume to test the download functionality.
                </div>
                <?php endif; ?>

                <!-- Integration Test -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-link"></i> Integration Test</h5>
                    </div>
                    <div class="card-body">
                        <p>Test the resume download functionality in the actual admin panel:</p>
                        <div class="d-grid gap-2">
                            <a href="job_applications.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Go to Job Applications (Test Resume Links)
                            </a>
                            <?php if (!empty($applications)): ?>
                            <a href="application_details.php?id=<?php echo $applications[0]['id']; ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Application Details (Test Resume Button)
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Success Criteria -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-check-double"></i> Success Criteria</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>✅ Fix is Working If:</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Download script exists and is accessible
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Resume files download successfully
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        No "file not found" errors
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        Works in both job applications list and details
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>🔧 If Still Not Working:</h6>
                                <ol>
                                    <li>Check file permissions (644 for files, 755 for directories)</li>
                                    <li>Verify uploads directory exists and is readable</li>
                                    <li>Check hosting provider file access restrictions</li>
                                    <li>Review PHP error logs for detailed error messages</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="text-center">
                    <a href="resume_download_fix.php" class="btn btn-outline-info me-2">
                        <i class="fas fa-tools"></i> Run Full Diagnostics
                    </a>
                    <a href="job_applications.php" class="btn btn-primary me-2">
                        <i class="fas fa-list"></i> Test in Job Applications
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh test results every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
        
        // Log test results to console
        console.log('Resume Download Fix Test Results:', <?php echo json_encode($test_results); ?>);
    </script>
</body>
</html>
