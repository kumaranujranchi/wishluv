<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();
$currentUser = $auth->getCurrentUser();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'update_status':
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            $stmt = $conn->prepare("UPDATE leads SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$status, $id]);
            echo json_encode(['success' => $result]);
            exit;
            
        case 'add_note':
            $id = (int)$_POST['id'];
            $note = trim($_POST['note']);
            if (!empty($note)) {
                $stmt = $conn->prepare("UPDATE leads SET notes = CONCAT(IFNULL(notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
                $noteWithTimestamp = "[" . date('Y-m-d H:i:s') . " - " . $currentUser['name'] . "] " . $note;
                $result = $stmt->execute([$noteWithTimestamp, $id]);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Note cannot be empty']);
            }
            exit;
            
        case 'delete_inquiry':
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
            $result = $stmt->execute([$id]);
            echo json_encode(['success' => $result]);
            exit;
    }
}

// Get filter parameters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$whereConditions = [];
$params = [];

if (!empty($status)) {
    $whereConditions[] = "status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $whereConditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countStmt = $conn->prepare("SELECT COUNT(*) FROM leads $whereClause");
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get inquiries
$stmt = $conn->prepare("
    SELECT * FROM leads 
    $whereClause 
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

// Get statistics
$statsStmt = $conn->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_inquiries,
        SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted,
        SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified,
        SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted
    FROM leads
");
$statsStmt->execute();
$stats = $statsStmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Inquiries - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">Contact Inquiries</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportInquiries()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['total']; ?></h4>
                                        <p class="mb-0">Total Inquiries</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-envelope fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['new_inquiries']; ?></h4>
                                        <p class="mb-0">New Inquiries</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-bell fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['contacted']; ?></h4>
                                        <p class="mb-0">Contacted</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-phone fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['converted']; ?></h4>
                                        <p class="mb-0">Converted</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select class="form-control" name="status">
                                    <option value="">All Status</option>
                                    <option value="new" <?php echo $status === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="contacted" <?php echo $status === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                    <option value="qualified" <?php echo $status === 'qualified' ? 'selected' : ''; ?>>Qualified</option>
                                    <option value="converted" <?php echo $status === 'converted' ? 'selected' : ''; ?>>Converted</option>
                                    <option value="lost" <?php echo $status === 'lost' ? 'selected' : ''; ?>>Lost</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="search" placeholder="Search inquiries..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="contact_inquiries.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Inquiries Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($inquiries)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                            No inquiries found
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($inquiries as $inquiry): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle me-2 text-muted"></i>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($inquiry['name']); ?></div>
                                                        <?php if (!empty($inquiry['property_interest'])): ?>
                                                            <small class="text-muted">Interested in: <?php echo htmlspecialchars($inquiry['property_interest']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>">
                                                        <?php echo htmlspecialchars($inquiry['email']); ?>
                                                    </a>
                                                </div>
                                                <?php if (!empty($inquiry['phone'])): ?>
                                                <div class="mt-1">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <a href="tel:<?php echo htmlspecialchars($inquiry['phone']); ?>">
                                                        <?php echo htmlspecialchars($inquiry['phone']); ?>
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="message-preview">
                                                    <?php echo htmlspecialchars(substr($inquiry['message'], 0, 100)); ?>
                                                    <?php if (strlen($inquiry['message']) > 100): ?>...<?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm status-select" 
                                                        data-inquiry-id="<?php echo $inquiry['id']; ?>" 
                                                        data-current-status="<?php echo $inquiry['status']; ?>">
                                                    <option value="new" <?php echo $inquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                    <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                    <option value="qualified" <?php echo $inquiry['status'] === 'qualified' ? 'selected' : ''; ?>>Qualified</option>
                                                    <option value="converted" <?php echo $inquiry['status'] === 'converted' ? 'selected' : ''; ?>>Converted</option>
                                                    <option value="lost" <?php echo $inquiry['status'] === 'lost' ? 'selected' : ''; ?>>Lost</option>
                                                </select>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewInquiry(<?php echo $inquiry['id']; ?>)" 
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" 
                                                            onclick="addNote(<?php echo $inquiry['id']; ?>)" 
                                                            title="Add Note">
                                                        <i class="fas fa-sticky-note"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteInquiry(<?php echo $inquiry['id']; ?>)" 
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <nav aria-label="Inquiries pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status); ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                                </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status); ?>&search=<?php echo urlencode($search); ?>">Next</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- View Inquiry Modal -->
    <div class="modal fade" id="viewInquiryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inquiry Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="inquiryDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm">
                        <input type="hidden" id="noteInquiryId" name="inquiry_id">
                        <div class="mb-3">
                            <label for="noteText" class="form-label">Note</label>
                            <textarea class="form-control" id="noteText" name="note" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote()">Save Note</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/leads.js"></script>
</body>
</html>
