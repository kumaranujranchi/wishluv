<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();

// Get application ID and validate
$app_id = (int)($_GET['id'] ?? 0);

if (!$app_id) {
    http_response_code(400);
    die('Invalid application ID');
}

try {
    // Get application details
    $stmt = $conn->prepare("SELECT id, full_name, resume_path FROM job_applications WHERE id = ?");
    $stmt->execute([$app_id]);
    $app = $stmt->fetch();
    
    if (!$app) {
        http_response_code(404);
        die('Application not found');
    }
    
    if (empty($app['resume_path'])) {
        http_response_code(404);
        die('No resume file found for this application');
    }
    
    // Construct file path - try multiple methods for compatibility
    $resume_filename = basename($app['resume_path']);
    $possible_paths = [
        // Method 1: Relative to admin directory
        '../' . $app['resume_path'],
        // Method 2: Relative to parent directory
        dirname(__DIR__) . '/' . $app['resume_path'],
        // Method 3: From document root
        $_SERVER['DOCUMENT_ROOT'] . '/' . $app['resume_path'],
        // Method 4: Direct path if already absolute
        $app['resume_path']
    ];
    
    $file_path = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path) && is_readable($path)) {
            $file_path = $path;
            break;
        }
    }
    
    if (!$file_path) {
        http_response_code(404);
        die('Resume file not found on server. Checked paths: ' . implode(', ', $possible_paths));
    }
    
    // Security check - ensure file is in uploads directory
    $real_path = realpath($file_path);
    $uploads_path = realpath(dirname(__DIR__) . '/uploads');
    
    if (!$real_path || !$uploads_path || strpos($real_path, $uploads_path) !== 0) {
        http_response_code(403);
        die('Access denied - file outside allowed directory');
    }
    
    // Get file info
    $file_size = filesize($file_path);
    $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    
    // Set appropriate content type
    $content_types = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    $content_type = $content_types[$file_extension] ?? 'application/octet-stream';
    
    // Generate safe filename for download
    $safe_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $app['full_name']);
    $download_filename = $safe_name . '_resume.' . $file_extension;
    
    // Log the download attempt
    error_log("Resume download: User {$auth->getCurrentUser()['username']} downloading resume for application {$app_id} ({$app['full_name']})");
    
    // Set headers for file download
    header('Content-Type: ' . $content_type);
    header('Content-Length: ' . $file_size);
    header('Content-Disposition: inline; filename="' . $download_filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Prevent any output before file
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Output file
    readfile($file_path);
    exit;
    
} catch (Exception $e) {
    error_log("Resume download error: " . $e->getMessage());
    http_response_code(500);
    die('Error accessing resume file: ' . $e->getMessage());
}
?>
