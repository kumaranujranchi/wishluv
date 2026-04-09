<?php
require_once 'auth.php';
$auth->requireLogin();

// Check if user has admin role
if (!$auth->hasRole('admin')) {
    header('Location: dashboard.php');
    exit;
}

$success_messages = [];
$error_messages = [];

// Implementation steps
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['implement'])) {
    
    // Step 1: Update application_details.php
    $app_details_file = 'application_details.php';
    if (file_exists($app_details_file)) {
        $content = file_get_contents($app_details_file);
        
        // Replace the resume link
        $old_pattern = '/href="\.\.\/\<\?php echo htmlspecialchars\(\$app\[\'resume_path\'\]\); \?\>"/';
        $new_replacement = 'href="download_resume.php?id=<?php echo $app[\'id\']; ?>"';
        
        $updated_content = preg_replace($old_pattern, $new_replacement, $content);
        
        if ($updated_content !== $content) {
            if (file_put_contents($app_details_file, $updated_content)) {
                $success_messages[] = "Updated application_details.php with secure download link";
            } else {
                $error_messages[] = "Failed to update application_details.php";
            }
        } else {
            // Try alternative pattern
            $old_pattern2 = '/href="\.\.\/\<\?php echo htmlspecialchars\(\$app\[\'resume_path\'\]\); \?\>"/';
            $updated_content = str_replace(
                'href="../<?php echo htmlspecialchars($app[\'resume_path\']); ?>"',
                'href="download_resume.php?id=<?php echo $app[\'id\']; ?>"',
                $content
            );
            
            if ($updated_content !== $content) {
                if (file_put_contents($app_details_file, $updated_content)) {
                    $success_messages[] = "Updated application_details.php with secure download link";
                } else {
                    $error_messages[] = "Failed to update application_details.php";
                }
            } else {
                $error_messages[] = "Could not find resume link pattern in application_details.php";
            }
        }
    } else {
        $error_messages[] = "application_details.php not found";
    }
    
    // Step 2: Update job_applications.php
    $job_apps_file = 'job_applications.php';
    if (file_exists($job_apps_file)) {
        $content = file_get_contents($job_apps_file);
        
        // Replace the resume link in the table
        $updated_content = str_replace(
            'href="../<?php echo htmlspecialchars($app[\'resume_path\']); ?>"',
            'href="download_resume.php?id=<?php echo $app[\'id\']; ?>"',
            $content
        );
        
        if ($updated_content !== $content) {
            if (file_put_contents($job_apps_file, $updated_content)) {
                $success_messages[] = "Updated job_applications.php with secure download link";
            } else {
                $error_messages[] = "Failed to update job_applications.php";
            }
        } else {
            $error_messages[] = "Could not find resume link pattern in job_applications.php";
        }
    } else {
        $error_messages[] = "job_applications.php not found";
    }
    
    // Step 3: Check if download_resume.php exists
    if (file_exists('download_resume.php')) {
        $success_messages[] = "Secure download script (download_resume.php) is ready";
    } else {
        $error_messages[] = "download_resume.php not found - please ensure it's uploaded";
    }
    
    // Step 4: Test uploads directory
    if (is_dir('../uploads/resumes')) {
        if (is_readable('../uploads/resumes')) {
            $success_messages[] = "Uploads directory is accessible";
        } else {
            $error_messages[] = "Uploads directory exists but is not readable";
        }
    } else {
        $error_messages[] = "Uploads/resumes directory not found";
    }
}

$conn = getDBConnection();

// Get sample application for testing
$stmt = $conn->prepare("SELECT id, full_name, resume_path FROM job_applications WHERE resume_path IS NOT NULL AND resume_path != '' LIMIT 1");
$stmt->execute();
$sample_app = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Implement Resume Fix - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">Implement Resume Download Fix</h1>
                </div>

                <!-- Results -->
                <?php if (!empty($success_messages)): ?>
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Success Messages:</h6>
                    <ul class="mb-0">
                        <?php foreach ($success_messages as $message): ?>
                            <li><?php echo htmlspecialchars($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($error_messages)): ?>
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Error Messages:</h6>
                    <ul class="mb-0">
                        <?php foreach ($error_messages as $message): ?>
                            <li><?php echo htmlspecialchars($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Implementation Plan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list-check"></i> Resume Download Fix Implementation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🔧 What This Fix Does:</h6>
                                <ul>
                                    <li><strong>Creates secure download script</strong> - Handles file serving properly</li>
                                    <li><strong>Updates admin panel links</strong> - Uses secure download method</li>
                                    <li><strong>Adds access control</strong> - Only logged-in admins can download</li>
                                    <li><strong>Works on all hosting</strong> - Compatible with shared/VPS hosting</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>✅ Benefits:</h6>
                                <ul>
                                    <li><strong>Reliable downloads</strong> - Works on local and hosted environments</li>
                                    <li><strong>Security</strong> - Prevents direct file access</li>
                                    <li><strong>Logging</strong> - Tracks who downloads what</li>
                                    <li><strong>Error handling</strong> - Better error messages</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Implementation Form -->
                <?php if (empty($success_messages)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-tools"></i> Ready to Implement Fix</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Before proceeding:</strong> This will modify your admin panel files. Make sure you have backups.
                        </div>
                        
                        <form method="POST">
                            <div class="d-grid">
                                <button type="submit" name="implement" value="1" class="btn btn-warning btn-lg">
                                    <i class="fas fa-cogs"></i> Implement Resume Download Fix
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Test Section -->
                <?php if ($sample_app && !empty($success_messages)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-test-tube"></i> Test the Fix</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Test Application:</strong> <?php echo htmlspecialchars($sample_app['full_name']); ?></p>
                        <p><strong>Resume Path:</strong> <?php echo htmlspecialchars($sample_app['resume_path']); ?></p>
                        
                        <div class="d-grid gap-2">
                            <a href="download_resume.php?id=<?php echo $sample_app['id']; ?>" 
                               target="_blank" class="btn btn-success">
                                <i class="fas fa-download"></i> Test Secure Download
                            </a>
                            <a href="job_applications.php" class="btn btn-primary">
                                <i class="fas fa-list"></i> Test in Job Applications Page
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Manual Instructions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-book"></i> Manual Implementation (If Automatic Failed)</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="manualInstructions">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step1">
                                        Step 1: Update application_details.php
                                    </button>
                                </h2>
                                <div id="step1" class="accordion-collapse collapse" data-bs-parent="#manualInstructions">
                                    <div class="accordion-body">
                                        <p>Find this line in <code>admin/application_details.php</code>:</p>
                                        <pre><code>href="../&lt;?php echo htmlspecialchars($app['resume_path']); ?&gt;"</code></pre>
                                        <p>Replace with:</p>
                                        <pre><code>href="download_resume.php?id=&lt;?php echo $app['id']; ?&gt;"</code></pre>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step2">
                                        Step 2: Update job_applications.php
                                    </button>
                                </h2>
                                <div id="step2" class="accordion-collapse collapse" data-bs-parent="#manualInstructions">
                                    <div class="accordion-body">
                                        <p>Find this line in <code>admin/job_applications.php</code>:</p>
                                        <pre><code>href="../&lt;?php echo htmlspecialchars($app['resume_path']); ?&gt;"</code></pre>
                                        <p>Replace with:</p>
                                        <pre><code>href="download_resume.php?id=&lt;?php echo $app['id']; ?&gt;"</code></pre>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step3">
                                        Step 3: Upload download_resume.php
                                    </button>
                                </h2>
                                <div id="step3" class="accordion-collapse collapse" data-bs-parent="#manualInstructions">
                                    <div class="accordion-body">
                                        <p>Make sure <code>admin/download_resume.php</code> is uploaded to your server.</p>
                                        <p>This file handles secure resume downloads and should be in the same directory as other admin files.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="text-center">
                    <a href="resume_download_fix.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to Diagnostics
                    </a>
                    <a href="job_applications.php" class="btn btn-primary">
                        <i class="fas fa-list"></i> Go to Job Applications
                    </a>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
