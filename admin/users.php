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

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_user':
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $name = trim($_POST['name']);
            
            // Validate input
            if (empty($username) || empty($email) || empty($password) || empty($name)) {
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                exit;
            }
            
            // Check if username or email already exists
            $checkStmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
            $checkStmt->execute([$username, $email]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
                exit;
            }
            
            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password, role, name, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$username, $email, $hashedPassword, $role, $name]);
            
            echo json_encode(['success' => $result]);
            exit;
            
        case 'update_user':
            $id = (int)$_POST['id'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            $name = trim($_POST['name']);
            $status = $_POST['status'];
            
            // Validate input
            if (empty($username) || empty($email) || empty($name)) {
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                exit;
            }
            
            // Check if username or email already exists for other users
            $checkStmt = $conn->prepare("SELECT id FROM admin_users WHERE (username = ? OR email = ?) AND id != ?");
            $checkStmt->execute([$username, $email, $id]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
                exit;
            }
            
            // Update user
            $stmt = $conn->prepare("UPDATE admin_users SET username = ?, email = ?, role = ?, name = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$username, $email, $role, $name, $status, $id]);
            
            echo json_encode(['success' => $result]);
            exit;
            
        case 'delete_user':
            $id = (int)$_POST['id'];
            
            // Prevent deleting own account
            if ($id == $currentUser['id']) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            echo json_encode(['success' => $result]);
            exit;
            
        case 'reset_password':
            $id = (int)$_POST['id'];
            $newPassword = $_POST['new_password'];
            
            if (empty($newPassword)) {
                echo json_encode(['success' => false, 'error' => 'Password cannot be empty']);
                exit;
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET password = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$hashedPassword, $id]);
            
            echo json_encode(['success' => $result]);
            exit;
    }
}

// Get all users
$stmt = $conn->prepare("SELECT * FROM admin_users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">User Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                <i class="fas fa-plus"></i> Add New User
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle me-2 text-muted"></i>
                                                <?php echo htmlspecialchars($user['name']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <?php echo date('M j, Y g:i A', strtotime($user['last_login'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editUser(<?php echo $user['id']; ?>)" 
                                                        title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="resetPassword(<?php echo $user['id']; ?>)" 
                                                        title="Reset Password">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <?php if ($user['id'] != $currentUser['id']): ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteUser(<?php echo $user['id']; ?>)" 
                                                        title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm">
                        <div class="mb-3">
                            <label for="createName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="createName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="createUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="createUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="createEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="createEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="createPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="createPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="createRole" class="form-label">Role</label>
                            <select class="form-control" id="createRole" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createUser()">Create User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUserId" name="id">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-control" id="editRole" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-control" id="editStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateUser()">Update User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <input type="hidden" id="resetUserId" name="id">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="resetUserPassword()">Reset Password</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/users.js"></script>
</body>
</html>
