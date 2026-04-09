<?php
require_once 'auth.php';
$auth->requireLogin();

// Check if user has admin role
if (!$auth->hasRole('admin')) {
    header('Location: dashboard.php');
    exit;
}

$conn = getDBConnection();
$currentUser = $auth->getCurrentUser();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = false;
    $error = '';
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_company_info':
                $company_name = trim($_POST['company_name']);
                $company_email = trim($_POST['company_email']);
                $company_phone = trim($_POST['company_phone']);
                $company_address = trim($_POST['company_address']);
                $company_website = trim($_POST['company_website']);
                
                // Create settings table if it doesn't exist
                $conn->exec("CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(255) UNIQUE,
                    setting_value TEXT,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                
                // Update or insert settings
                $settings = [
                    'company_name' => $company_name,
                    'company_email' => $company_email,
                    'company_phone' => $company_phone,
                    'company_address' => $company_address,
                    'company_website' => $company_website
                ];
                
                foreach ($settings as $key => $value) {
                    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$key, $value, $value]);
                }
                
                $success = true;
                break;
                
            case 'update_email_settings':
                $smtp_host = trim($_POST['smtp_host']);
                $smtp_port = trim($_POST['smtp_port']);
                $smtp_username = trim($_POST['smtp_username']);
                $smtp_password = trim($_POST['smtp_password']);
                $from_email = trim($_POST['from_email']);
                $from_name = trim($_POST['from_name']);
                
                $emailSettings = [
                    'smtp_host' => $smtp_host,
                    'smtp_port' => $smtp_port,
                    'smtp_username' => $smtp_username,
                    'smtp_password' => $smtp_password,
                    'from_email' => $from_email,
                    'from_name' => $from_name
                ];
                
                foreach ($emailSettings as $key => $value) {
                    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$key, $value, $value]);
                }
                
                $success = true;
                break;
                
            case 'update_system_settings':
                $timezone = $_POST['timezone'];
                $date_format = $_POST['date_format'];
                $items_per_page = (int)$_POST['items_per_page'];
                $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
                
                $systemSettings = [
                    'timezone' => $timezone,
                    'date_format' => $date_format,
                    'items_per_page' => $items_per_page,
                    'maintenance_mode' => $maintenance_mode
                ];
                
                foreach ($systemSettings as $key => $value) {
                    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$key, $value, $value]);
                }
                
                $success = true;
                break;
        }
    }
}

// Get current settings
function getSetting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

$settings = [
    'company_name' => getSetting($conn, 'company_name', 'Wishluv Buildcon'),
    'company_email' => getSetting($conn, 'company_email', 'info@wishluvbuildcon.com'),
    'company_phone' => getSetting($conn, 'company_phone', '+91 9876543210'),
    'company_address' => getSetting($conn, 'company_address', 'Your Company Address'),
    'company_website' => getSetting($conn, 'company_website', 'https://wishluvbuildcon.com'),
    'smtp_host' => getSetting($conn, 'smtp_host'),
    'smtp_port' => getSetting($conn, 'smtp_port', '587'),
    'smtp_username' => getSetting($conn, 'smtp_username'),
    'smtp_password' => getSetting($conn, 'smtp_password'),
    'from_email' => getSetting($conn, 'from_email'),
    'from_name' => getSetting($conn, 'from_name'),
    'timezone' => getSetting($conn, 'timezone', 'Asia/Kolkata'),
    'date_format' => getSetting($conn, 'date_format', 'Y-m-d'),
    'items_per_page' => getSetting($conn, 'items_per_page', '20'),
    'maintenance_mode' => getSetting($conn, 'maintenance_mode', '0')
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">System Settings</h1>
                </div>

                <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Settings updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Settings Tabs -->
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">
                            <i class="fas fa-building"></i> Company Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                            <i class="fas fa-envelope"></i> Email Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                            <i class="fas fa-cog"></i> System Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button" role="tab">
                            <i class="fas fa-database"></i> Backup & Restore
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="settingsTabContent">
                    <!-- Company Info Tab -->
                    <div class="tab-pane fade show active" id="company" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_company_info">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_name" class="form-label">Company Name</label>
                                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                                       value="<?php echo htmlspecialchars($settings['company_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_email" class="form-label">Company Email</label>
                                                <input type="email" class="form-control" id="company_email" name="company_email" 
                                                       value="<?php echo htmlspecialchars($settings['company_email']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_phone" class="form-label">Company Phone</label>
                                                <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                                       value="<?php echo htmlspecialchars($settings['company_phone']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_website" class="form-label">Company Website</label>
                                                <input type="url" class="form-control" id="company_website" name="company_website" 
                                                       value="<?php echo htmlspecialchars($settings['company_website']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="company_address" class="form-label">Company Address</label>
                                        <textarea class="form-control" id="company_address" name="company_address" rows="3"><?php echo htmlspecialchars($settings['company_address']); ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Company Info
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings Tab -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_email_settings">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                                       value="<?php echo htmlspecialchars($settings['smtp_host']); ?>" 
                                                       placeholder="smtp.gmail.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtp_port" class="form-label">SMTP Port</label>
                                                <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                                       value="<?php echo htmlspecialchars($settings['smtp_port']); ?>" 
                                                       placeholder="587">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtp_username" class="form-label">SMTP Username</label>
                                                <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                                       value="<?php echo htmlspecialchars($settings['smtp_username']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="smtp_password" class="form-label">SMTP Password</label>
                                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                                       value="<?php echo htmlspecialchars($settings['smtp_password']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="from_email" class="form-label">From Email</label>
                                                <input type="email" class="form-control" id="from_email" name="from_email" 
                                                       value="<?php echo htmlspecialchars($settings['from_email']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="from_name" class="form-label">From Name</label>
                                                <input type="text" class="form-control" id="from_name" name="from_name" 
                                                       value="<?php echo htmlspecialchars($settings['from_name']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Email Settings
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="testEmail()">
                                        <i class="fas fa-paper-plane"></i> Test Email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- System Settings Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_system_settings">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="timezone" class="form-label">Timezone</label>
                                                <select class="form-control" id="timezone" name="timezone">
                                                    <option value="Asia/Kolkata" <?php echo $settings['timezone'] === 'Asia/Kolkata' ? 'selected' : ''; ?>>Asia/Kolkata</option>
                                                    <option value="UTC" <?php echo $settings['timezone'] === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                                    <option value="America/New_York" <?php echo $settings['timezone'] === 'America/New_York' ? 'selected' : ''; ?>>America/New_York</option>
                                                    <option value="Europe/London" <?php echo $settings['timezone'] === 'Europe/London' ? 'selected' : ''; ?>>Europe/London</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_format" class="form-label">Date Format</label>
                                                <select class="form-control" id="date_format" name="date_format">
                                                    <option value="Y-m-d" <?php echo $settings['date_format'] === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                                    <option value="d/m/Y" <?php echo $settings['date_format'] === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                                    <option value="m/d/Y" <?php echo $settings['date_format'] === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="items_per_page" class="form-label">Items Per Page</label>
                                                <select class="form-control" id="items_per_page" name="items_per_page">
                                                    <option value="10" <?php echo $settings['items_per_page'] === '10' ? 'selected' : ''; ?>>10</option>
                                                    <option value="20" <?php echo $settings['items_per_page'] === '20' ? 'selected' : ''; ?>>20</option>
                                                    <option value="50" <?php echo $settings['items_per_page'] === '50' ? 'selected' : ''; ?>>50</option>
                                                    <option value="100" <?php echo $settings['items_per_page'] === '100' ? 'selected' : ''; ?>>100</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                                           <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="maintenance_mode">
                                                        Maintenance Mode
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">Enable to put the site in maintenance mode</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save System Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Backup & Restore Tab -->
                    <div class="tab-pane fade" id="backup" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Database Backup</h5>
                                        <p class="text-muted">Create a backup of your database</p>
                                        <button type="button" class="btn btn-success" onclick="createBackup()">
                                            <i class="fas fa-download"></i> Create Backup
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>System Information</h5>
                                        <table class="table table-sm">
                                            <tr>
                                                <td>PHP Version:</td>
                                                <td><?php echo PHP_VERSION; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Server Software:</td>
                                                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Database:</td>
                                                <td>MySQL <?php echo $conn->getAttribute(PDO::ATTR_SERVER_VERSION); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function testEmail() {
        alert('Email test functionality would be implemented here');
    }
    
    function createBackup() {
        if (confirm('Create a database backup? This may take a few moments.')) {
            alert('Backup functionality would be implemented here');
        }
    }
    </script>
</body>
</html>
