<?php
require_once '../config/database.php';

// Create database tables
function createTables() {
    $conn = getDBConnection();
    
    try {
        // Create leads table
        $leadsTable = "
        CREATE TABLE IF NOT EXISTS leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            message TEXT,
            property_interest VARCHAR(255),
            budget_range VARCHAR(100),
            status ENUM('new', 'contacted', 'qualified', 'converted', 'closed') DEFAULT 'new',
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            source VARCHAR(100) DEFAULT 'website',
            notes TEXT,
            assigned_to VARCHAR(255),
            follow_up_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($leadsTable);
        echo "Leads table created successfully.<br>";
        
        // Create job applications table
        $jobApplicationsTable = "
        CREATE TABLE IF NOT EXISTS job_applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            position_applied VARCHAR(255) NOT NULL,
            experience_years INT,
            current_company VARCHAR(255),
            current_salary DECIMAL(10,2),
            expected_salary DECIMAL(10,2),
            notice_period VARCHAR(100),
            education VARCHAR(255),
            skills TEXT,
            resume_path VARCHAR(500),
            cover_letter TEXT,
            address TEXT,
            city VARCHAR(100),
            state VARCHAR(100),
            pincode VARCHAR(10),
            status ENUM('applied', 'screening', 'interview', 'selected', 'rejected', 'on_hold') DEFAULT 'applied',
            interview_date DATETIME,
            interview_notes TEXT,
            hr_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($jobApplicationsTable);
        echo "Job applications table created successfully.<br>";
        
        // Create admin users table
        $adminTable = "
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            role ENUM('admin', 'manager', 'user') DEFAULT 'user',
            is_active BOOLEAN DEFAULT TRUE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($adminTable);
        echo "Admin users table created successfully.<br>";
        
        // Create default admin user
        $defaultAdmin = "
        INSERT IGNORE INTO admin_users (username, email, password, full_name, role) 
        VALUES ('admin', 'admin@wishluv.com', ?, 'Administrator', 'admin')";
        
        $stmt = $conn->prepare($defaultAdmin);
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->execute([$hashedPassword]);
        echo "Default admin user created (username: admin, password: admin123).<br>";
        
        // Create contact inquiries table
        $contactTable = "
        CREATE TABLE IF NOT EXISTS contact_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255),
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($contactTable);
        echo "Contact inquiries table created successfully.<br>";
        
        echo "<br><strong>Database setup completed successfully!</strong><br>";
        echo "<a href='login.php'>Go to Admin Login</a>";
        
    } catch(PDOException $e) {
        echo "Error creating tables: " . $e->getMessage();
    }
}

// Run the setup
createTables();
?>
