<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $conn = getDBConnection();
    
    // Honeypot check for bots
    if (!empty($_POST['website'])) {
        // Silent block
        echo json_encode(['success' => true, 'message' => 'Thank you for your application!']);
        exit;
    }

    // Get form data
    $name = trim($_POST['name'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $current_salary = trim($_POST['current_salary'] ?? '');
    $expected_salary = trim($_POST['expected_salary'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $position = trim($_POST['position'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($qualification)) {
        $errors[] = 'Qualification is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9+\-\s()]{10,15}$/', $phone)) {
        $errors[] = 'Please enter a valid phone number';
    }
    
    if (empty($position)) {
        $errors[] = 'Position applying for is required';
    }
    
    // Handle file upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/resumes/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = 'Resume must be a PDF, DOC, or DOCX file';
        } else {
            $file_name = 'resume_' . time() . '_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
                $resume_path = 'uploads/resumes/' . $file_name;
            } else {
                $errors[] = 'Failed to upload resume';
            }
        }
    } else {
        $errors[] = 'Resume is required';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Map qualification values
    $qualification_map = [
        '1' => '10th',
        '2' => '12th',
        '3' => 'Graduate',
        '4' => 'Post Graduate'
    ];
    $education = $qualification_map[$qualification] ?? $qualification;
    
    // Convert salary values
    $current_salary_num = !empty($current_salary) ? (float)str_replace(',', '', $current_salary) : null;
    $expected_salary_num = !empty($expected_salary) ? (float)str_replace(',', '', $expected_salary) : null;
    
    // Generate a dummy email if not provided (since it's required in DB)
    $email = strtolower(str_replace(' ', '.', $name)) . '@applicant.temp';
    
    // Insert into job_applications table
    $stmt = $conn->prepare("
        INSERT INTO job_applications 
        (full_name, email, phone, position_applied, education, current_salary, expected_salary, city, resume_path, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'applied')
    ");
    
    $result = $stmt->execute([
        $name,
        $email,
        $phone,
        $position,
        $education,
        $current_salary_num,
        $expected_salary_num,
        $city,
        $resume_path
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you for your application! We will review your profile and get back to you soon.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Sorry, there was an error submitting your application. Please try again.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Job application error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was a technical error. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Job application error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your request.'
    ]);
}
?>
