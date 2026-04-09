<?php
session_start();
require_once '../config/database.php';

class Auth {
    private $conn;
    public $lastError = '';
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function login($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email, password, full_name, role, is_active FROM admin_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->lastError = 'User not found in database.';
                return false;
            }
            
            if (!$user['is_active']) {
                $this->lastError = 'User account is inactive.';
                return false;
            }
            
            if (!password_verify($password, $user['password'])) {
                $this->lastError = 'Password verification failed. Incorrect password.';
                return false;
            }

            if ($user && $user['is_active'] && password_verify($password, $user['password'])) {
                /* 
                // Update last login (Disabled as column might be missing)
                $updateStmt = $this->conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                */
                
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];
                
                return true;
            }
            return false;
        } catch(PDOException $e) {
            $this->lastError = 'Database Error: ' . $e->getMessage();
            return false;
        } catch(Exception $e) {
            $this->lastError = 'General Error: ' . $e->getMessage();
            return false;
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'email' => $_SESSION['admin_email'],
                'name' => $_SESSION['admin_name'],
                'role' => $_SESSION['admin_role']
            ];
        }
        return null;
    }
    
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['admin_role'] === $role;
    }
    
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) return false;
        
        $role = $_SESSION['admin_role'];
        
        // Admin has all permissions
        if ($role === 'admin') return true;
        
        // Define role permissions
        $permissions = [
            'manager' => ['view_leads', 'edit_leads', 'view_applications', 'edit_applications'],
            'user' => ['view_leads', 'view_applications']
        ];
        
        return isset($permissions[$role]) && in_array($permission, $permissions[$role]);
    }
}

// Create global auth instance
$auth = new Auth();
?>
