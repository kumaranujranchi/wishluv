<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();
$currentUser = $auth->getCurrentUser();

// Get date range from filters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Get leads statistics
$leadsStmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_leads,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_leads,
        SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted_leads,
        SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified_leads,
        SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_leads,
        SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) as lost_leads
    FROM leads 
    WHERE DATE(created_at) BETWEEN ? AND ?
");
$leadsStmt->execute([$start_date, $end_date]);
$leadsStats = $leadsStmt->fetch();

// Get job applications statistics
$jobsStmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_applications,
        SUM(CASE WHEN status = 'applied' THEN 1 ELSE 0 END) as new_applications,
        SUM(CASE WHEN status = 'screening' THEN 1 ELSE 0 END) as screening_applications,
        SUM(CASE WHEN status = 'interview' THEN 1 ELSE 0 END) as interview_applications,
        SUM(CASE WHEN status = 'selected' THEN 1 ELSE 0 END) as selected_applications,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_applications,
        SUM(CASE WHEN status = 'on_hold' THEN 1 ELSE 0 END) as on_hold_applications
    FROM job_applications 
    WHERE DATE(created_at) BETWEEN ? AND ?
");
$jobsStmt->execute([$start_date, $end_date]);
$jobsStats = $jobsStmt->fetch();

// Get daily leads trend
$dailyLeadsStmt = $conn->prepare("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM leads 
    WHERE DATE(created_at) BETWEEN ? AND ? 
    GROUP BY DATE(created_at) 
    ORDER BY date DESC 
    LIMIT 30
");
$dailyLeadsStmt->execute([$start_date, $end_date]);
$dailyLeads = $dailyLeadsStmt->fetchAll();

// Get daily job applications trend
$dailyJobsStmt = $conn->prepare("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM job_applications 
    WHERE DATE(created_at) BETWEEN ? AND ? 
    GROUP BY DATE(created_at) 
    ORDER BY date DESC 
    LIMIT 30
");
$dailyJobsStmt->execute([$start_date, $end_date]);
$dailyJobs = $dailyJobsStmt->fetchAll();

// Get top property interests
$propertyStmt = $conn->prepare("
    SELECT property_interest, COUNT(*) as count 
    FROM leads 
    WHERE property_interest IS NOT NULL AND property_interest != '' 
    AND DATE(created_at) BETWEEN ? AND ?
    GROUP BY property_interest 
    ORDER BY count DESC 
    LIMIT 10
");
$propertyStmt->execute([$start_date, $end_date]);
$propertyInterests = $propertyStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Wishluv Buildcon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Reports & Analytics</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReport()">
                                <i class="fas fa-download"></i> Export Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-filter"></i> Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $leadsStats['total_leads']; ?></h4>
                                        <p class="mb-0">Total Leads</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
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
                                        <h4><?php echo $leadsStats['converted_leads']; ?></h4>
                                        <p class="mb-0">Converted Leads</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
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
                                        <h4><?php echo $jobsStats['total_applications']; ?></h4>
                                        <p class="mb-0">Job Applications</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-briefcase fa-2x"></i>
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
                                        <h4><?php echo $jobsStats['selected_applications']; ?></h4>
                                        <p class="mb-0">Selected Candidates</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-user-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Leads Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="leadsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Job Applications Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="jobsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trends and Details -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Daily Trends</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="trendsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Top Property Interests</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($propertyInterests)): ?>
                                    <?php foreach ($propertyInterests as $property): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><?php echo htmlspecialchars($property['property_interest']); ?></span>
                                            <span class="badge bg-primary"><?php echo $property['count']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No property interest data available for this period.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Leads Status Chart
    const leadsCtx = document.getElementById('leadsChart').getContext('2d');
    new Chart(leadsCtx, {
        type: 'doughnut',
        data: {
            labels: ['New', 'Contacted', 'Qualified', 'Converted', 'Lost'],
            datasets: [{
                data: [
                    <?php echo $leadsStats['new_leads']; ?>,
                    <?php echo $leadsStats['contacted_leads']; ?>,
                    <?php echo $leadsStats['qualified_leads']; ?>,
                    <?php echo $leadsStats['converted_leads']; ?>,
                    <?php echo $leadsStats['lost_leads']; ?>
                ],
                backgroundColor: ['#6c757d', '#0dcaf0', '#ffc107', '#198754', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Jobs Status Chart
    const jobsCtx = document.getElementById('jobsChart').getContext('2d');
    new Chart(jobsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Applied', 'Screening', 'Interview', 'Selected', 'Rejected', 'On Hold'],
            datasets: [{
                data: [
                    <?php echo $jobsStats['new_applications']; ?>,
                    <?php echo $jobsStats['screening_applications']; ?>,
                    <?php echo $jobsStats['interview_applications']; ?>,
                    <?php echo $jobsStats['selected_applications']; ?>,
                    <?php echo $jobsStats['rejected_applications']; ?>,
                    <?php echo $jobsStats['on_hold_applications']; ?>
                ],
                backgroundColor: ['#6c757d', '#0dcaf0', '#ffc107', '#198754', '#dc3545', '#fd7e14']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Trends Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: [
                <?php foreach (array_reverse($dailyLeads) as $day): ?>
                    '<?php echo date('M j', strtotime($day['date'])); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Leads',
                data: [
                    <?php foreach (array_reverse($dailyLeads) as $day): ?>
                        <?php echo $day['count']; ?>,
                    <?php endforeach; ?>
                ],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }, {
                label: 'Job Applications',
                data: [
                    <?php foreach (array_reverse($dailyJobs) as $day): ?>
                        <?php echo $day['count']; ?>,
                    <?php endforeach; ?>
                ],
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function exportReport() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        window.location.href = `reports.php?export=csv&start_date=${startDate}&end_date=${endDate}`;
    }
    </script>
</body>
</html>
