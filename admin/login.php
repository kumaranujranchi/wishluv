<?php
require_once 'auth.php';

$error = '';
$success = '';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if ($auth->login($username, $password)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $auth->lastError ?: 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Wishluv Buildcon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-red: #ed1b24;
            --dark-red: #b2141a;
        }
        body {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Quicksand', sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            border: none;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .login-header h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .login-body {
            padding: 2.5rem 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #444;
            margin-bottom: 0.5rem;
        }
        .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border: 2px solid #ed1b24;
            border-right: none;
            color: #ed1b24;
        }
        .form-control {
            border: 2px solid #ed1b24;
            padding: 12px 15px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: var(--dark-red);
            box-shadow: none;
            outline: none;
        }
        .password-toggle {
            cursor: pointer;
            background-color: #f8f9fa;
            border: 2px solid #ed1b24;
            border-left: none;
            color: #666;
            transition: all 0.3s ease;
        }
        .password-toggle:hover {
            color: #ed1b24;
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: white;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(237, 27, 36, 0.4);
            color: white;
        }
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h3><i class="fas fa-user-shield"></i></h3>
                        <h4 class="fw-bold">Admin Panel</h4>
                        <p class="mb-0 opacity-75">Wishluv Buildcon</p>
                    </div>
                    <div class="login-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Enter username"
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Enter password" required>
                                    <span class="input-group-text password-toggle" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="fas fa-sign-in-alt me-2"></i> LOGIN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
