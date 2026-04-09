<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get all applications with resume paths
$stmt = $conn->prepare("SELECT id, full_name, email, resume_path, created_at FROM job_applications WHERE resume_path IS NOT NULL AND resume_path != '' ORDER BY created_at DESC");
$stmt->execute();
$applications = $stmt->fetchAll();

// Check uploads directory
$uploads_dir = '../uploads';
$resumes_dir = '../uploads/resumes';

$directory_status = [
    'uploads_exists' => is_dir($uploads_dir),
    'resumes_exists' => is_dir($resumes_dir),
    'uploads_writable' => is_writable($uploads_dir),
    'resumes_writable' => is_writable($resumes_dir),
    'uploads_permissions' => is_dir($uploads_dir) ? substr(sprintf('%o', fileperms($uploads_dir)), -4) : 'N/A',
    'resumes_permissions' => is_dir($resumes_dir) ? substr(sprintf('%o', fileperms($resumes_dir)), -4) : 'N/A'
];

// List actual files in directory
$actual_files = [];
if (is_dir($resumes_dir)) {
    $files = scandir($resumes_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && !is_dir($resumes_dir . '/' . $file)) {
            $actual_files[] = [
                'name' => $file,
                'size' => filesize($resumes_dir . '/' . $file),
                'modified' => date('Y-m-d H:i:s', filemtime($resumes_dir . '/' . $file))
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Files Checker - Wishluv Buildcon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h3><i class="fas fa-search"></i> Resume Files Diagnostic</h3>
            </div>
            <div class="card-body">
                
                <!-- Directory Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-folder"></i> Directory Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($directory_status as $check => $result): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span><?php echo ucwords(str_replace('_', ' ', $check)); ?>:</span>
                                    <span class="badge bg-<?php echo ($result === true || $result === '0755' || $result === '0644') ? 'success' : 'danger'; ?>">
                                        <?php echo is_bool($result) ? ($result ? 'Yes' : 'No') : $result; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Actual Files in Directory -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-file"></i> Actual Files in uploads/resumes/ Directory</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($actual_files)): ?>
                            <div class="alert alert-success">
                                <strong>Found <?php echo count($actual_files); ?> file(s) in the directory:</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Modified</th>
                                            <th>Test Access</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($actual_files as $file): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($file['name']); ?></code></td>
                                            <td><?php echo number_format($file['size']); ?> bytes</td>
                                            <td><?php echo $file['modified']; ?></td>
                                            <td>
                                                <a href="../uploads/resumes/<?php echo urlencode($file['name']); ?>" 
                                                   target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Test
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>No files found in uploads/resumes/ directory!</strong>
                                <p class="mb-0 mt-2">This means resume files were not uploaded to the server or are in a different location.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Database vs File System Comparison -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-database"></i> Database vs File System Comparison</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($applications)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Applicant</th>
                                            <th>Resume Path (Database)</th>
                                            <th>File Exists</th>
                                            <th>Status</th>
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
                                                    <span class="text-success"><i class="fas fa-check"></i> OK</span>
                                                <?php else: ?>
                                                    <span class="text-danger"><i class="fas fa-times"></i> Missing</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No applications with resume paths found in database.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Solutions -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-lightbulb"></i> Solutions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🔧 If No Files Found:</h6>
                                <ol>
                                    <li><strong>Create uploads directory:</strong>
                                        <br><code>mkdir uploads/resumes</code>
                                        <br><code>chmod 755 uploads/resumes</code>
                                    </li>
                                    <li><strong>Test file upload:</strong> Submit a new job application with resume</li>
                                    <li><strong>Check hosting file manager:</strong> Verify uploads directory exists</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6>📁 If Files Exist but Not Accessible:</h6>
                                <ol>
                                    <li><strong>Check permissions:</strong> Files should be 644, directories 755</li>
                                    <li><strong>Check .htaccess:</strong> Ensure no rules block file access</li>
                                    <li><strong>Contact hosting:</strong> Some hosts restrict direct file access</li>
                                </ol>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="alert alert-warning">
                                <strong><i class="fas fa-exclamation-triangle"></i> Most Likely Issue:</strong>
                                The uploads/resumes directory doesn't exist on your hosting server, or resume files were not properly uploaded when applications were submitted.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-4 text-center">
                    <a href="create_uploads_directory.php" class="btn btn-success me-2">
                        <i class="fas fa-folder-plus"></i> Create Uploads Directory
                    </a>
                    <a href="../apply.php" target="_blank" class="btn btn-info me-2">
                        <i class="fas fa-upload"></i> Test File Upload
                    </a>
                    <a href="job_applications.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Applications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
