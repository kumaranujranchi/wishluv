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
            
        case 'delete_lead':
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
            $result = $stmt->execute([$id]);
            echo json_encode(['success' => $result]);
            exit;

        case 'add_lead':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $property_interest = trim($_POST['property_interest'] ?? '');
            $budget_range = trim($_POST['budget_range'] ?? '');
            $priority = $_POST['priority'] ?? 'medium';

            if (empty($name) || empty($email) || empty($phone)) {
                echo json_encode(['success' => false, 'error' => 'Name, email, and phone are required']);
                exit;
            }

            try {
                $stmt = $conn->prepare("INSERT INTO leads (name, email, phone, message, property_interest, budget_range, priority, source) VALUES (?, ?, ?, ?, ?, ?, ?, 'manual')");
                $result = $stmt->execute([$name, $email, $phone, $message, $property_interest, $budget_range, $priority]);
                echo json_encode(['success' => $result]);
            } catch(PDOException $e) {
                if ($e->getCode() == 23000) {
                    echo json_encode(['success' => false, 'error' => 'Email or phone already exists']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Database error occurred']);
                }
            }
            exit;
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$priority_filter = $_GET['priority'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = [];
$params = [];

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($priority_filter)) {
    $where_conditions[] = "priority = ?";
    $params[] = $priority_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM leads $where_clause";
$count_stmt = $conn->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// Get leads
$query = "SELECT * FROM leads $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$leads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads Management - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">Leads Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportLeads()">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                                <i class="fas fa-plus"></i> Add Lead
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter Section -->
                <div class="search-filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by name, email, phone...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="contacted" <?php echo $status_filter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="qualified" <?php echo $status_filter === 'qualified' ? 'selected' : ''; ?>>Qualified</option>
                                <option value="converted" <?php echo $status_filter === 'converted' ? 'selected' : ''; ?>>Converted</option>
                                <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                                <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted">
                            Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_records); ?> 
                            of <?php echo $total_records; ?> leads
                        </span>
                    </div>
                    <?php if (!empty($search) || !empty($status_filter) || !empty($priority_filter)): ?>
                    <div>
                        <a href="leads.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Leads Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($leads)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Interest</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($lead['name']); ?></strong>
                                            <?php if (!empty($lead['notes'])): ?>
                                                <i class="fas fa-sticky-note text-warning ms-1" title="Has notes"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($lead['email']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($lead['phone']); ?></small>
                                        </td>
                                        <td>
                                            <?php if (!empty($lead['property_interest'])): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($lead['property_interest']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($lead['budget_range'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($lead['budget_range']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm status-select" 
                                                    data-lead-id="<?php echo $lead['id']; ?>" 
                                                    data-current-status="<?php echo $lead['status']; ?>">
                                                <option value="new" <?php echo $lead['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                <option value="contacted" <?php echo $lead['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                <option value="qualified" <?php echo $lead['status'] === 'qualified' ? 'selected' : ''; ?>>Qualified</option>
                                                <option value="converted" <?php echo $lead['status'] === 'converted' ? 'selected' : ''; ?>>Converted</option>
                                                <option value="closed" <?php echo $lead['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getPriorityColor($lead['priority']); ?>">
                                                <?php echo ucfirst($lead['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewLead(<?php echo $lead['id']; ?>)" 
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="addNote(<?php echo $lead['id']; ?>)" 
                                                        title="Add Note">
                                                    <i class="fas fa-sticky-note"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteLead(<?php echo $lead['id']; ?>)" 
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Leads pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No leads found</h5>
                            <p class="text-muted">Try adjusting your search criteria or add a new lead.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- View Lead Modal -->
    <div class="modal fade" id="viewLeadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lead Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="leadDetailsContent">
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
                        <input type="hidden" id="noteLeadId" name="lead_id">
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

    <!-- Add Lead Modal -->
    <div class="modal fade" id="addLeadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addLeadForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leadName" class="form-label">Name *</label>
                                    <input type="text" class="form-control" id="leadName" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leadEmail" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="leadEmail" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leadPhone" class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" id="leadPhone" name="phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leadPriority" class="form-label">Priority</label>
                                    <select class="form-select" id="leadPriority" name="priority">
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leadPropertyInterest" class="form-label">Property Interest</label>
                                    <input type="text" class="form-control" id="leadPropertyInterest" name="property_interest">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="leadBudget" class="form-label">Budget Range</label>
                                    <input type="text" class="form-control" id="leadBudget" name="budget_range">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="leadMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="leadMessage" name="message" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveLead()">Save Lead</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-common.js"></script>
    <script src="assets/js/leads.js"></script>
</body>
</html>

<?php
function getPriorityColor($priority) {
    $colors = [
        'high' => 'danger',
        'medium' => 'warning',
        'low' => 'success'
    ];
    return $colors[$priority] ?? 'secondary';
}
?>
