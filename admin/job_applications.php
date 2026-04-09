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
            $stmt = $conn->prepare("UPDATE job_applications SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$status, $id]);
            echo json_encode(['success' => $result]);
            exit;
            
        case 'add_note':
            $id = (int)$_POST['id'];
            $note = trim($_POST['note']);
            if (!empty($note)) {
                $stmt = $conn->prepare("UPDATE job_applications SET hr_notes = CONCAT(IFNULL(hr_notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
                $noteWithTimestamp = "[" . date('Y-m-d H:i:s') . " - " . $currentUser['name'] . "] " . $note;
                $result = $stmt->execute([$noteWithTimestamp, $id]);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Note cannot be empty']);
            }
            exit;
            
        case 'schedule_interview':
            $id = (int)$_POST['id'];
            $interview_date = $_POST['interview_date'];
            $interview_notes = trim($_POST['interview_notes'] ?? '');
            
            $stmt = $conn->prepare("UPDATE job_applications SET interview_date = ?, interview_notes = ?, status = 'interview', updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$interview_date, $interview_notes, $id]);
            echo json_encode(['success' => $result]);
            exit;
            
        case 'delete_application':
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM job_applications WHERE id = ?");
            $result = $stmt->execute([$id]);
            echo json_encode(['success' => $result]);
            exit;
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$position_filter = $_GET['position'] ?? '';
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

if (!empty($position_filter)) {
    $where_conditions[] = "position_applied LIKE ?";
    $params[] = "%$position_filter%";
}

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR current_company LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM job_applications $where_clause";
$count_stmt = $conn->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// Get applications
$query = "SELECT * FROM job_applications $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$applications = $stmt->fetchAll();

// Get unique positions for filter
$positions_stmt = $conn->query("SELECT DISTINCT position_applied FROM job_applications ORDER BY position_applied");
$positions = $positions_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications - Wishluv Buildcon Admin</title>
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
                    <h1 class="h2">Job Applications</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportApplications()">
                                <i class="fas fa-download"></i> Export
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
                                   placeholder="Search by name, email, phone, company...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="applied" <?php echo $status_filter === 'applied' ? 'selected' : ''; ?>>Applied</option>
                                <option value="screening" <?php echo $status_filter === 'screening' ? 'selected' : ''; ?>>Screening</option>
                                <option value="interview" <?php echo $status_filter === 'interview' ? 'selected' : ''; ?>>Interview</option>
                                <option value="selected" <?php echo $status_filter === 'selected' ? 'selected' : ''; ?>>Selected</option>
                                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="on_hold" <?php echo $status_filter === 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="position" class="form-label">Position</label>
                            <select class="form-select" id="position" name="position">
                                <option value="">All Positions</option>
                                <?php foreach ($positions as $position): ?>
                                <option value="<?php echo htmlspecialchars($position); ?>" 
                                        <?php echo $position_filter === $position ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($position); ?>
                                </option>
                                <?php endforeach; ?>
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
                            of <?php echo $total_records; ?> applications
                        </span>
                    </div>
                    <?php if (!empty($search) || !empty($status_filter) || !empty($position_filter)): ?>
                    <div>
                        <a href="job_applications.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Applications Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($applications)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Position</th>
                                        <th>Experience</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($app['full_name']); ?></strong>
                                                <?php if (!empty($app['hr_notes'])): ?>
                                                    <i class="fas fa-sticky-note text-warning ms-1" title="Has notes"></i>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['phone']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($app['position_applied']); ?></span>
                                            <?php if (!empty($app['current_company'])): ?>
                                                <br><small class="text-muted">Currently at: <?php echo htmlspecialchars($app['current_company']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($app['experience_years']): ?>
                                                <?php echo $app['experience_years']; ?> years
                                            <?php else: ?>
                                                <span class="text-muted">Not specified</span>
                                            <?php endif; ?>
                                            <?php if ($app['expected_salary']): ?>
                                                <br><small class="text-muted">Expected: ₹<?php echo number_format($app['expected_salary']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm status-select" 
                                                    data-app-id="<?php echo $app['id']; ?>" 
                                                    data-current-status="<?php echo $app['status']; ?>">
                                                <option value="applied" <?php echo $app['status'] === 'applied' ? 'selected' : ''; ?>>Applied</option>
                                                <option value="screening" <?php echo $app['status'] === 'screening' ? 'selected' : ''; ?>>Screening</option>
                                                <option value="interview" <?php echo $app['status'] === 'interview' ? 'selected' : ''; ?>>Interview</option>
                                                <option value="selected" <?php echo $app['status'] === 'selected' ? 'selected' : ''; ?>>Selected</option>
                                                <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                <option value="on_hold" <?php echo $app['status'] === 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                                            </select>
                                            <?php if ($app['interview_date']): ?>
                                                <br><small class="text-info">
                                                    <i class="fas fa-calendar"></i> 
                                                    <?php echo date('M j, Y', strtotime($app['interview_date'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo date('M j, Y', strtotime($app['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewApplication(<?php echo $app['id']; ?>)" 
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="addNote(<?php echo $app['id']; ?>)" 
                                                        title="Add Note">
                                                    <i class="fas fa-sticky-note"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="scheduleInterview(<?php echo $app['id']; ?>)" 
                                                        title="Schedule Interview">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </button>
                                                <?php if (!empty($app['resume_path'])): ?>
                                                <a href="download_resume.php?id=<?php echo $app['id']; ?>"
                                                   target="_blank" class="btn btn-sm btn-outline-secondary"
                                                   title="View Resume">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteApplication(<?php echo $app['id']; ?>)" 
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
                        <nav aria-label="Applications pagination">
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
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No job applications found</h5>
                            <p class="text-muted">Try adjusting your search criteria.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- View Application Modal -->
    <div class="modal fade" id="viewApplicationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="applicationDetailsContent">
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
                    <h5 class="modal-title">Add HR Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm">
                        <input type="hidden" id="noteAppId" name="app_id">
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

    <!-- Schedule Interview Modal -->
    <div class="modal fade" id="scheduleInterviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Interview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleInterviewForm">
                        <input type="hidden" id="interviewAppId" name="app_id">
                        <div class="mb-3">
                            <label for="interviewDate" class="form-label">Interview Date & Time</label>
                            <input type="datetime-local" class="form-control" id="interviewDate" name="interview_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="interviewNotes" class="form-label">Interview Notes</label>
                            <textarea class="form-control" id="interviewNotes" name="interview_notes" rows="3"
                                      placeholder="Interview type, location, interviewer details, etc."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveInterview()">Schedule Interview</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-common.js"></script>
    <script src="assets/js/job_applications.js"></script>
</body>
</html>
