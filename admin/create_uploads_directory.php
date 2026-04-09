<?php
require_once 'auth.php';
$auth->requireLogin();

$messages = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_directories'])) {
    
    // Create uploads directory structure
    $directories = [
        '../uploads' => 'Main uploads directory',
        '../uploads/resumes' => 'Resumes directory'
    ];
    
    foreach ($directories as $dir => $description) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                $messages[] = ['type' => 'success', 'message' => "Created: $description ($dir)"];
            } else {
                $messages[] = ['type' => 'danger', 'message' => "Failed to create: $description ($dir)"];
            }
        } else {
            $messages[] = ['type' => 'info', 'message' => "Already exists: $description ($dir)"];
        }
    }
    
    // Set proper permissions
    if (is_dir('../uploads')) {
        chmod('../uploads', 0755);
        $messages[] = ['type' => 'success', 'message' => 'Set permissions for uploads directory (755)'];
    }
    
    if (is_dir('../uploads/resumes')) {
        chmod('../uploads/resumes', 0755);
        $messages[] = ['type' => 'success', 'message' => 'Set permissions for resumes directory (755)'];
    }
    
    // Create .htaccess for security (optional)
    $htaccess_content = "# Protect uploads directory\n";
    $htaccess_content .= "Options -Indexes\n";
    $htaccess_content .= "# Allow specific file types\n";
    $htaccess_content .= "<FilesMatch \"\\.(pdf|doc|docx)$\">\n";
    $htaccess_content .= "    Order Allow,Deny\n";
    $htaccess_content .= "    Allow from all\n";
    $htaccess_content .= "</FilesMatch>\n";
    
    if (file_put_contents('../uploads/.htaccess', $htaccess_content)) {
        $messages[] = ['type' => 'success', 'message' => 'Created .htaccess for security'];
    }
    
    // Create index.php to prevent directory listing
    $index_content = "<?php\n// Prevent directory access\nheader('Location: ../admin/');\nexit;\n?>";
    
    if (file_put_contents('../uploads/index.php', $index_content)) {
        $messages[] = ['type' => 'success', 'message' => 'Created index.php for security'];
    }
    
    if (file_put_contents('../uploads/resumes/index.php', $index_content)) {
        $messages[] = ['type' => 'success', 'message' => 'Created resumes/index.php for security'];
    }
    
    $success = true;
}

// Check current status
$status = [
    'uploads_exists' => is_dir('../uploads'),
    'resumes_exists' => is_dir('../uploads/resumes'),
    'uploads_writable' => is_writable('../uploads'),
    'resumes_writable' => is_writable('../uploads/resumes'),
    'uploads_permissions' => is_dir('../uploads') ? substr(sprintf('%o', fileperms('../uploads')), -4) : 'N/A',
    'resumes_permissions' => is_dir('../uploads/resumes') ? substr(sprintf('%o', fileperms('../uploads/resumes')), -4) : 'N/A'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Uploads Directory - Wishluv Buildcon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3><i class="fas fa-folder-plus"></i> Create Uploads Directory Structure</h3>
            </div>
            <div class="card-body">
                
                <!-- Messages -->
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="alert alert-<?php echo $msg['type']; ?>">
                            <?php echo htmlspecialchars($msg['message']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Current Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Current Directory Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($status as $check => $result): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span><?php echo ucwords(str_replace('_', ' ', $check)); ?>:</span>
                                    <span class="badge bg-<?php echo ($result === true || $result === '0755') ? 'success' : 'danger'; ?>">
                                        <?php echo is_bool($result) ? ($result ? 'Yes' : 'No') : $result; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Directory Structure -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-sitemap"></i> Required Directory Structure</h5>
                    </div>
                    <div class="card-body">
                        <pre><code>public_html/
├── uploads/                    (755 permissions)
│   ├── .htaccess              (security file)
│   ├── index.php              (prevent directory listing)
│   └── resumes/               (755 permissions)
│       ├── index.php          (prevent directory listing)
│       ├── resume_file1.pdf   (644 permissions)
│       ├── resume_file2.pdf   (644 permissions)
│       └── ...
└── admin/
    ├── download_resume.php
    └── ...</code></pre>
                    </div>
                </div>

                <!-- Create Directories Form -->
                <?php if (!$status['uploads_exists'] || !$status['resumes_exists']): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-exclamation-triangle"></i> Action Required</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Missing directories detected!</strong> Click the button below to create the required directory structure.</p>
                        
                        <form method="POST">
                            <div class="d-grid">
                                <button type="submit" name="create_directories" value="1" class="btn btn-warning btn-lg">
                                    <i class="fas fa-folder-plus"></i> Create Uploads Directory Structure
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Directory Structure Complete!</h5>
                    <p class="mb-0">All required directories exist and have proper permissions.</p>
                </div>
                <?php endif; ?>

                <!-- Test Upload -->
                <?php if ($success || ($status['uploads_exists'] && $status['resumes_exists'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-test-tube"></i> Test File Upload</h5>
                    </div>
                    <div class="card-body">
                        <p>Now that the directory structure is ready, test the file upload functionality:</p>
                        
                        <div class="d-grid gap-2">
                            <a href="../apply.php" target="_blank" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Test Job Application Form (Upload Resume)
                            </a>
                            <a href="check_resume_files.php" class="btn btn-info">
                                <i class="fas fa-search"></i> Check Resume Files Status
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Manual Instructions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-book"></i> Manual Instructions (If Automatic Creation Failed)</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="manualInstructions">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step1">
                                        Step 1: Using Hosting File Manager
                                    </button>
                                </h2>
                                <div id="step1" class="accordion-collapse collapse" data-bs-parent="#manualInstructions">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Login to your hosting control panel (cPanel/Plesk)</li>
                                            <li>Open File Manager</li>
                                            <li>Navigate to <code>public_html/</code></li>
                                            <li>Create folder: <code>uploads</code></li>
                                            <li>Inside uploads, create folder: <code>resumes</code></li>
                                            <li>Set permissions: 755 for directories</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step2">
                                        Step 2: Using FTP Client
                                    </button>
                                </h2>
                                <div id="step2" class="accordion-collapse collapse" data-bs-parent="#manualInstructions">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Connect to your server via FTP</li>
                                            <li>Navigate to <code>public_html/</code></li>
                                            <li>Create directory: <code>uploads</code></li>
                                            <li>Create subdirectory: <code>uploads/resumes</code></li>
                                            <li>Set permissions: 755 for both directories</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="text-center">
                    <a href="check_resume_files.php" class="btn btn-outline-info me-2">
                        <i class="fas fa-search"></i> Check Files Status
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
