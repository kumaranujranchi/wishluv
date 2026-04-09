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
        // Silent block - act like it succeeded but do nothing
        echo json_encode(['success' => true, 'message' => 'Thank you for contacting us!']);
        exit;
    }

    // Get form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9+\-\s()]{10,15}$/', $phone)) {
        $errors[] = 'Please enter a valid phone number';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Combine first and last name
    $full_name = $first_name . ' ' . $last_name;
    
    // Insert into contact_inquiries table
    $stmt = $conn->prepare("INSERT INTO contact_inquiries (name, email, phone, message, status) VALUES (?, ?, ?, ?, 'new')");
    $result = $stmt->execute([$full_name, $email, $phone, $message]);
    
    if ($result) {
        // Also create a lead entry
        $leadStmt = $conn->prepare("INSERT INTO leads (name, email, phone, message, source, status, priority) VALUES (?, ?, ?, ?, 'contact_form', 'new', 'medium')");
        $leadStmt->execute([$full_name, $email, $phone, $message]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you for contacting us! We will get back to you soon.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Sorry, there was an error submitting your message. Please try again.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Contact form error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was a technical error. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your request.'
    ]);
}
?>
