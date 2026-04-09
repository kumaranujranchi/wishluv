<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get a sample application with resume
$stmt = $conn->prepare("SELECT id, full_name, resume_path FROM job_applications WHERE resume_path IS NOT NULL AND resume_path != '' LIMIT 1");
$stmt->execute();
$app = $stmt->fetch();

if (!$app) {
    echo '<div class="alert alert-warning">No applications with resumes found. Upload a test application first.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resume Download Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Resume Download Test</h2>
        
        <div class="card">
            <div class="card-body">
                <h5>Application: <?php echo htmlspecialchars($app['full_name']); ?></h5>
                <p><strong>Resume Path:</strong> <?php echo htmlspecialchars($app['resume_path']); ?></p>
                
                <div class="mt-3">
                    <h6>Test Links:</h6>
                    <div class="d-grid gap-2">
                        <a href="../<?php echo htmlspecialchars($app['resume_path']); ?>" 
                           target="_blank" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> View Resume (Correct Path)
                        </a>
                        
                        <a href="<?php echo htmlspecialchars($app['resume_path']); ?>" 
                           target="_blank" class="btn btn-secondary">
                            <i class="fas fa-file-pdf"></i> View Resume (Wrong Path - for comparison)
                        </a>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        The first link should work (opens the resume).<br>
                        The second link will likely show a 404 error.
                    </small>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="job_applications.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Applications
            </a>
        </div>
    </div>
</body>
</html>
