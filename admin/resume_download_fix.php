<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get server information
$server_info = [
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not available',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Not available',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'Not available',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Not available',
    'current_dir' => __DIR__,
    'parent_dir' => dirname(__DIR__),
    'uploads_dir_relative' => '../uploads/resumes/',
    'uploads_dir_absolute' => dirname(__DIR__) . '/uploads/resumes/'
];

// Test file access methods
$test_results = [];

// Get a sample resume for testing
$stmt = $conn->prepare("SELECT id, full_name, resume_path FROM job_applications WHERE resume_path IS NOT NULL AND resume_path != '' LIMIT 1");
$stmt->execute();
$sample_app = $stmt->fetch();

if ($sample_app) {
    $resume_path = $sample_app['resume_path']; // e.g., "uploads/resumes/resume_123.pdf"
    
    // Test different path methods
    $test_paths = [
        'relative_parent' => '../' . $resume_path,
        'absolute_from_root' => $_SERVER['DOCUMENT_ROOT'] . '/' . $resume_path,
        'relative_from_current' => dirname(__DIR__) . '/' . $resume_path,
        'web_absolute' => '/' . $resume_path,
        'direct_path' => $resume_path
    ];
    
    foreach ($test_paths as $method => $path) {
        $test_results[$method] = [
            'path' => $path,
            'file_exists' => file_exists($path),
            'is_readable' => file_exists($path) ? is_readable($path) : false,
            'file_size' => file_exists($path) ? filesize($path) : 0
        ];
    }
}

// Check uploads directory
$uploads_checks = [
    'uploads_exists' => is_dir('../uploads'),
    'resumes_exists' => is_dir('../uploads/resumes'),
    'uploads_writable' => is_writable('../uploads'),
    'resumes_writable' => is_writable('../uploads/resumes'),
    'uploads_permissions' => is_dir('../uploads') ? substr(sprintf('%o', fileperms('../uploads')), -4) : 'N/A',
    'resumes_permissions' => is_dir('../uploads/resumes') ? substr(sprintf('%o', fileperms('../uploads/resumes')), -4) : 'N/A'
];

// List files in uploads directory
$resume_files = [];
if (is_dir('../uploads/resumes')) {
    $files = scandir('../uploads/resumes');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $resume_files[] = [
                'name' => $file,
                'size' => filesize('../uploads/resumes/' . $file),
                'permissions' => substr(sprintf('%o', fileperms('../uploads/resumes/' . $file)), -4),
                'modified' => date('Y-m-d H:i:s', filemtime('../uploads/resumes/' . $file))
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
    <title>Resume Download Fix - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">Resume Download Fix & Diagnostics</h1>
                </div>

                <!-- Server Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-server"></i> Server Environment</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <?php foreach ($server_info as $key => $value): ?>
                                <tr>
                                    <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</strong></td>
                                    <td><code><?php echo htmlspecialchars($value); ?></code></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Directory Checks -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-folder"></i> Directory & Permissions Check</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($uploads_checks as $check => $result): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span><?php echo ucwords(str_replace('_', ' ', $check)); ?>:</span>
                                    <span class="badge bg-<?php echo $result === true || $result === '0755' || $result === '0644' ? 'success' : 'danger'; ?>">
                                        <?php echo is_bool($result) ? ($result ? 'Yes' : 'No') : $result; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- File Access Tests -->
                <?php if ($sample_app): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-file-pdf"></i> Resume File Access Tests</h5>
                        <small class="text-muted">Testing with: <?php echo htmlspecialchars($sample_app['full_name']); ?> (<?php echo htmlspecialchars($sample_app['resume_path']); ?>)</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Path</th>
                                        <th>File Exists</th>
                                        <th>Readable</th>
                                        <th>Size</th>
                                        <th>Test Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($test_results as $method => $result): ?>
                                    <tr>
                                        <td><strong><?php echo ucwords(str_replace('_', ' ', $method)); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($result['path']); ?></code></td>
                                        <td>
                                            <span class="badge bg-<?php echo $result['file_exists'] ? 'success' : 'danger'; ?>">
                                                <?php echo $result['file_exists'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $result['is_readable'] ? 'success' : 'danger'; ?>">
                                                <?php echo $result['is_readable'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $result['file_size'] > 0 ? number_format($result['file_size']) . ' bytes' : 'N/A'; ?></td>
                                        <td>
                                            <?php if ($method === 'web_absolute' || $method === 'relative_parent'): ?>
                                                <a href="<?php echo $method === 'web_absolute' ? $result['path'] : '../' . $sample_app['resume_path']; ?>" 
                                                   target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Test
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Server-side only</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Resume Files List -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Resume Files in Directory</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($resume_files)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Permissions</th>
                                            <th>Modified</th>
                                            <th>Test Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resume_files as $file): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($file['name']); ?></td>
                                            <td><?php echo number_format($file['size']); ?> bytes</td>
                                            <td>
                                                <span class="badge bg-<?php echo $file['permissions'] === '0644' ? 'success' : 'warning'; ?>">
                                                    <?php echo $file['permissions']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $file['modified']; ?></td>
                                            <td>
                                                <a href="../uploads/resumes/<?php echo urlencode($file['name']); ?>" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Test
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No resume files found in the uploads/resumes directory.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recommended Solution -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-lightbulb"></i> Recommended Solution</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>Best Practice for Hosted Environments:</h6>
                            <p>Create a dedicated download script that handles file serving securely and works across all hosting environments.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>✅ Current Working Method:</h6>
                                <ul>
                                    <?php if (isset($test_results['web_absolute']) && $test_results['web_absolute']['file_exists']): ?>
                                        <li><strong>Web Absolute Path:</strong> <code>/uploads/resumes/file.pdf</code></li>
                                    <?php endif; ?>
                                    <?php if (isset($test_results['relative_parent']) && $test_results['relative_parent']['file_exists']): ?>
                                        <li><strong>Relative Parent Path:</strong> <code>../uploads/resumes/file.pdf</code></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>🔧 Implementation Steps:</h6>
                                <ol>
                                    <li>Create secure download script</li>
                                    <li>Update admin panel links</li>
                                    <li>Add access control</li>
                                    <li>Test on hosting environment</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Fix Buttons -->
                <div class="text-center">
                    <button onclick="implementFix()" class="btn btn-success btn-lg me-2">
                        <i class="fas fa-tools"></i> Implement Recommended Fix
                    </button>
                    <a href="job_applications.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Applications
                    </a>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function implementFix() {
            if (confirm('This will create a secure download script and update the admin panel. Continue?')) {
                // Redirect to implementation
                window.location.href = 'implement_resume_fix.php';
            }
        }
    </script>
</body>
</html>
