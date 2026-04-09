<?php
// Local Setup Test Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

$tests = [];
$allPassed = true;

// Test 1: PHP Version
$phpVersion = phpversion();
$tests['PHP Version'] = [
    'status' => version_compare($phpVersion, '7.4.0', '>='),
    'message' => "PHP $phpVersion " . (version_compare($phpVersion, '7.4.0', '>=') ? '✅' : '❌ (Need 7.4+)')
];

// Test 2: Required Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    $tests["Extension: $ext"] = [
        'status' => extension_loaded($ext),
        'message' => extension_loaded($ext) ? "✅ $ext loaded" : "❌ $ext missing"
    ];
    if (!extension_loaded($ext)) $allPassed = false;
}

// Test 3: Database Connection
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        $conn = getDBConnection();
        $tests['Database Connection'] = [
            'status' => true,
            'message' => '✅ Database connection successful'
        ];
    } else {
        $tests['Database Connection'] = [
            'status' => false,
            'message' => '❌ config/database.php not found'
        ];
        $allPassed = false;
    }
} catch (Exception $e) {
    $tests['Database Connection'] = [
        'status' => false,
        'message' => '❌ Database connection failed: ' . $e->getMessage()
    ];
    $allPassed = false;
}

// Test 4: File Permissions
$directories = ['uploads', 'uploads/resumes'];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $writable = is_writable($dir);
    $tests["Directory: $dir"] = [
        'status' => $writable,
        'message' => $writable ? "✅ $dir is writable" : "❌ $dir is not writable"
    ];
    if (!$writable) $allPassed = false;
}

// Test 5: Required Files
$requiredFiles = [
    'index.php',
    'contact.php',
    'apply.php',
    'css/custom.css',
    'admin/dashboard.php',
    'admin/auth.php',
    'api/contact_form.php',
    'api/job_application.php'
];

foreach ($requiredFiles as $file) {
    $exists = file_exists($file);
    $tests["File: $file"] = [
        'status' => $exists,
        'message' => $exists ? "✅ $file exists" : "❌ $file missing"
    ];
    if (!$exists) $allPassed = false;
}

// Test 6: Database Tables (if connection works)
if (isset($conn)) {
    $requiredTables = ['admin_users', 'leads', 'job_applications'];
    foreach ($requiredTables as $table) {
        try {
            $stmt = $conn->prepare("SELECT 1 FROM $table LIMIT 1");
            $stmt->execute();
            $tests["Table: $table"] = [
                'status' => true,
                'message' => "✅ Table $table exists"
            ];
        } catch (Exception $e) {
            $tests["Table: $table"] = [
                'status' => false,
                'message' => "❌ Table $table missing or inaccessible"
            ];
            $allPassed = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Setup Test - Wishluv Buildcon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .test-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .test-pass {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .test-fail {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .overall-status {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .status-pass {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .status-fail {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><i class="fas fa-server"></i> Local Server Setup Test</h2>
                    </div>
                    <div class="card-body">
                        
                        <!-- Overall Status -->
                        <div class="overall-status <?php echo $allPassed ? 'status-pass' : 'status-fail'; ?>">
                            <?php if ($allPassed): ?>
                                <i class="fas fa-check-circle"></i> All Tests Passed! Your local setup is ready.
                            <?php else: ?>
                                <i class="fas fa-exclamation-triangle"></i> Some Tests Failed. Please fix the issues below.
                            <?php endif; ?>
                        </div>

                        <!-- Test Results -->
                        <h4><i class="fas fa-list-check"></i> Test Results</h4>
                        <?php foreach ($tests as $testName => $result): ?>
                            <div class="test-item <?php echo $result['status'] ? 'test-pass' : 'test-fail'; ?>">
                                <strong><?php echo htmlspecialchars($testName); ?>:</strong> 
                                <?php echo $result['message']; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Quick Actions -->
                        <div class="mt-4">
                            <h4><i class="fas fa-tools"></i> Quick Actions</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fas fa-database"></i> Database Setup</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if (isset($conn)): ?>
                                                <a href="admin/setup_database.php" class="btn btn-success btn-sm">
                                                    <i class="fas fa-play"></i> Setup Database Tables
                                                </a>
                                            <?php else: ?>
                                                <p class="text-danger">Fix database connection first</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fas fa-globe"></i> Test Website</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($allPassed): ?>
                                                <a href="index.php" class="btn btn-primary btn-sm me-2">
                                                    <i class="fas fa-home"></i> Homepage
                                                </a>
                                                <a href="contact.php" class="btn btn-info btn-sm">
                                                    <i class="fas fa-envelope"></i> Contact Form
                                                </a>
                                            <?php else: ?>
                                                <p class="text-warning">Fix issues above first</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="mt-4">
                            <h4><i class="fas fa-info-circle"></i> System Information</h4>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>PHP Version:</strong></td>
                                        <td><?php echo phpversion(); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Server Software:</strong></td>
                                        <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Document Root:</strong></td>
                                        <td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Current Directory:</strong></td>
                                        <td><?php echo __DIR__; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Upload Max Size:</strong></td>
                                        <td><?php echo ini_get('upload_max_filesize'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Post Max Size:</strong></td>
                                        <td><?php echo ini_get('post_max_size'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <?php if ($allPassed): ?>
                        <div class="alert alert-success mt-4">
                            <h5><i class="fas fa-rocket"></i> Next Steps:</h5>
                            <ol>
                                <li>Visit <a href="admin/setup_database.php">Database Setup</a> to create tables</li>
                                <li>Test the <a href="contact.php">Contact Form</a> styling</li>
                                <li>Test the <a href="apply.php">Job Application Form</a></li>
                                <li>Login to <a href="admin/">Admin Panel</a> (admin/password)</li>
                                <li>Test all admin functionality</li>
                            </ol>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning mt-4">
                            <h5><i class="fas fa-exclamation-triangle"></i> Issues to Fix:</h5>
                            <ul>
                                <?php foreach ($tests as $testName => $result): ?>
                                    <?php if (!$result['status']): ?>
                                        <li><?php echo htmlspecialchars($testName); ?>: <?php echo strip_tags($result['message']); ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Test Links -->
                        <div class="mt-4 text-center">
                            <h5>Test Links:</h5>
                            <a href="test_form_styling.html" class="btn btn-outline-primary me-2">
                                <i class="fas fa-paint-brush"></i> Form Styling Test
                            </a>
                            <a href="admin/test_complete_admin.php" class="btn btn-outline-success">
                                <i class="fas fa-cog"></i> Admin Panel Test
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
