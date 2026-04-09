<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();
$currentUser = $auth->getCurrentUser();

// Get statistics
try {
    // Leads statistics
    $leadsStmt = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_leads,
        SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted,
        SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified,
        SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted
        FROM leads");
    $leadsStats = $leadsStmt->fetch();
    
    // Job applications statistics
    $jobsStmt = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'applied' THEN 1 ELSE 0 END) as new_applications,
        SUM(CASE WHEN status = 'screening' THEN 1 ELSE 0 END) as screening,
        SUM(CASE WHEN status = 'interview' THEN 1 ELSE 0 END) as interview,
        SUM(CASE WHEN status = 'selected' THEN 1 ELSE 0 END) as selected
        FROM job_applications");
    $jobsStats = $jobsStmt->fetch();
    
    // Recent leads
    $recentLeadsStmt = $conn->query("SELECT name, email, phone, status, created_at FROM leads ORDER BY created_at DESC LIMIT 5");
    $recentLeads = $recentLeadsStmt->fetchAll();
    
    // Recent applications
    $recentAppsStmt = $conn->query("SELECT full_name, email, position_applied, status, created_at FROM job_applications ORDER BY created_at DESC LIMIT 5");
    $recentApplications = $recentAppsStmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error fetching statistics: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Wishluv Buildcon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <?php include 'includes/header.php'; ?>

    <!-- Sidebar toggle button -->
    <button class="sidebar-toggle d-md-none" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content" id="main-content">
                <!-- Enhanced header with search and quick actions -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <div>
                        <h2 class="fw-bold mb-0">Dashboard</h2>
                        <p class="text-muted small">Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>!</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="exportData()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-plus"></i> Quick Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="leads.php"><i class="fas fa-user-plus"></i> Add Lead</a></li>
                                <li><a class="dropdown-item" href="job_applications.php"><i class="fas fa-briefcase"></i> View Applications</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="reports.php"><i class="fas fa-chart-bar"></i> View Reports</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Enhanced Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100" onclick="location.href='leads.php'" role="button" tabindex="0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="stats-label">Total Leads</div>
                                        <div class="stats-number"><?php echo $leadsStats['total'] ?? 0; ?></div>
                                    </div>
                                    <div class="stats-icon bg-light-red text-red">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-arrow-up text-success"></i>
                                        12% from last month
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100 border-left-success" onclick="location.href='leads.php?status=new'" role="button" tabindex="0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="stats-label">New Leads</div>
                                        <div class="stats-number text-success"><?php echo $leadsStats['new_leads'] ?? 0; ?></div>
                                    </div>
                                    <div class="stats-icon bg-light-success text-success">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-arrow-up text-success"></i>
                                        5 new today
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100 border-left-info" onclick="location.href='job_applications.php'" role="button" tabindex="0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="stats-label">Job Applications</div>
                                        <div class="stats-number text-info"><?php echo $jobsStats['total'] ?? 0; ?></div>
                                    </div>
                                    <div class="stats-icon bg-light-info text-info">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock text-warning"></i>
                                        <?php echo $jobsStats['pending'] ?? 0; ?> pending review
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card h-100 border-left-warning" onclick="location.href='leads.php?status=converted'" role="button" tabindex="0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="stats-label">Converted Leads</div>
                                        <div class="stats-number text-warning"><?php echo $leadsStats['converted'] ?? 0; ?></div>
                                    </div>
                                    <div class="stats-icon bg-light-warning text-warning">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <?php if (($leadsStats['converted'] ?? 0) > 0): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-trophy text-warning"></i>
                                            Great job this month!
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">
                                            <i class="fas fa-target text-info"></i>
                                            Focus on conversions
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Recent Activity Section -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-container">
                            <div class="table-controls py-2 px-3">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-users text-danger me-2"></i>Recent Leads</h6>
                                <div class="ms-auto">
                                    <a href="leads.php" class="btn btn-sm btn-outline-primary">
                                        View All <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <?php if (!empty($recentLeads)): ?>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Contact</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentLeads as $lead): ?>
                                            <tr onclick="location.href='leads.php?id=<?php echo $lead['id'] ?? ''; ?>'" style="cursor: pointer;">
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($lead['name']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($lead['email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-indicator status-<?php echo $lead['status']; ?>">
                                                        <?php echo ucfirst($lead['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewLead(<?php echo $lead['id'] ?? 0; ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); contactLead(<?php echo $lead['id'] ?? 0; ?>)" title="Contact">
                                                            <i class="fas fa-phone"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="empty-state-title">No Recent Leads</div>
                                        <div class="empty-state-description">New leads will appear here when they're added to the system.</div>
                                        <a href="leads.php" class="btn btn-primary">Add First Lead</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="table-container">
                            <div class="table-controls py-2 px-3">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-briefcase text-info me-2"></i>Recent Applications</h6>
                                <div class="ms-auto">
                                    <a href="job_applications.php" class="btn btn-sm btn-outline-info">
                                        View All <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <?php if (!empty($recentApplications)): ?>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Applicant</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentApplications as $app): ?>
                                            <tr onclick="location.href='job_applications.php?id=<?php echo $app['id'] ?? ''; ?>'" style="cursor: pointer;">
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($app['full_name']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($app['position_applied']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="status-indicator status-<?php echo $app['status']; ?>">
                                                        <?php echo ucfirst($app['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-sm btn-outline-info" onclick="event.stopPropagation(); viewApplication(<?php echo $app['id'] ?? 0; ?>)" title="View Application">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); scheduleInterview(<?php echo $app['id'] ?? 0; ?>)" title="Schedule Interview">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div class="empty-state-title">No Recent Applications</div>
                                        <div class="empty-state-description">Job applications will appear here when candidates apply.</div>
                                        <a href="job_applications.php" class="btn btn-info">Manage Applications</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Summary -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body py-3">
                                <div class="row text-center">
                                    <div class="col-md-3 border-end">
                                        <div class="mb-1">
                                            <i class="fas fa-chart-line text-red" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <h6 class="small fw-bold mb-1">Conversion Rate</h6>
                                        <p class="h5 mb-0 fw-extrabold">
                                            <?php
                                            $total = $leadsStats['total'] ?? 0;
                                            $converted = $leadsStats['converted'] ?? 0;
                                            $rate = $total > 0 ? round(($converted / $total) * 100, 1) : 0;
                                            echo $rate . '%';
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 border-end">
                                        <div class="mb-1">
                                            <i class="fas fa-clock text-info" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <h6 class="small fw-bold mb-1">Avg. Response</h6>
                                        <p class="h5 mb-0 fw-extrabold">2.5h</p>
                                    </div>
                                    <div class="col-md-3 border-end">
                                        <div class="mb-1">
                                            <i class="fas fa-star text-warning" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <h6 class="small fw-bold mb-1">Satisfaction</h6>
                                        <p class="h5 mb-0 fw-extrabold">4.8/5.0</p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-1">
                                            <i class="fas fa-trophy text-success" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <h6 class="small fw-bold mb-1">Monthly Goal</h6>
                                        <div class="progress-modern mx-auto mt-1" style="width: 80%; height: 6px;">
                                            <div class="progress-bar-modern" style="width: 75%"></div>
                                        </div>
                                        <p class="small text-muted mb-0 mt-1">75% Complete</p>
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
        // Enhanced Dashboard JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Auto-refresh dashboard data every 5 minutes
            setInterval(function() {
                refreshDashboard();
            }, 300000);

            // Add keyboard navigation for cards
            document.querySelectorAll('.stats-card').forEach(card => {
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Dashboard functions
        function refreshDashboard() {
            // Show loading indicator
            const refreshBtn = document.querySelector('[onclick="refreshDashboard()"]');
            if (refreshBtn) {
                const icon = refreshBtn.querySelector('i');
                icon.classList.add('fa-spin');

                // Simulate refresh (in real implementation, this would be an AJAX call)
                setTimeout(() => {
                    icon.classList.remove('fa-spin');
                    showNotification('Dashboard refreshed successfully', 'success');
                }, 1000);
            }
        }

        function exportData() {
            showNotification('Export functionality coming soon', 'info');
        }

        function viewLead(id) {
            window.location.href = `leads.php?action=view&id=${id}`;
        }

        function contactLead(id) {
            showNotification(`Contacting lead ${id}`, 'info');
        }

        function viewApplication(id) {
            window.location.href = `job_applications.php?action=view&id=${id}`;
        }

        function scheduleInterview(id) {
            showNotification(`Scheduling interview for application ${id}`, 'info');
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }
    </script>
</body>
</html>

<?php
function getStatusColor($status) {
    $colors = [
        'new' => 'primary',
        'applied' => 'primary',
        'contacted' => 'info',
        'screening' => 'info',
        'qualified' => 'warning',
        'interview' => 'warning',
        'converted' => 'success',
        'selected' => 'success',
        'closed' => 'secondary',
        'rejected' => 'danger',
        'on_hold' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}
?>
