<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();
$appId = (int)($_GET['id'] ?? 0);

if (!$appId) {
    echo '<div class="alert alert-danger">Invalid application ID</div>';
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM job_applications WHERE id = ?");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    
    if (!$app) {
        echo '<div class="alert alert-danger">Application not found</div>';
        exit;
    }
} catch(PDOException $e) {
    echo '<div class="alert alert-danger">Error loading application details</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-8">
        <h5>Personal Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Full Name:</strong></td>
                <td><?php echo htmlspecialchars($app['full_name']); ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($app['email']); ?>">
                        <?php echo htmlspecialchars($app['email']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>
                    <a href="tel:<?php echo htmlspecialchars($app['phone']); ?>">
                        <?php echo htmlspecialchars($app['phone']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td>
                    <?php if (!empty($app['address'])): ?>
                        <?php echo nl2br(htmlspecialchars($app['address'])); ?>
                        <?php if (!empty($app['city']) || !empty($app['state']) || !empty($app['pincode'])): ?>
                            <br>
                            <?php echo htmlspecialchars($app['city']); ?>
                            <?php if (!empty($app['state'])): ?>, <?php echo htmlspecialchars($app['state']); ?><?php endif; ?>
                            <?php if (!empty($app['pincode'])): ?> - <?php echo htmlspecialchars($app['pincode']); ?><?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">Not provided</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <h5>Professional Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Position Applied:</strong></td>
                <td>
                    <span class="badge bg-primary"><?php echo htmlspecialchars($app['position_applied']); ?></span>
                </td>
            </tr>
            <tr>
                <td><strong>Experience:</strong></td>
                <td>
                    <?php if ($app['experience_years']): ?>
                        <?php echo $app['experience_years']; ?> years
                    <?php else: ?>
                        <span class="text-muted">Not specified</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong>Current Company:</strong></td>
                <td><?php echo htmlspecialchars($app['current_company'] ?? 'Not specified'); ?></td>
            </tr>
            <tr>
                <td><strong>Current Salary:</strong></td>
                <td>
                    <?php if ($app['current_salary']): ?>
                        ₹<?php echo number_format($app['current_salary']); ?>
                    <?php else: ?>
                        <span class="text-muted">Not disclosed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong>Expected Salary:</strong></td>
                <td>
                    <?php if ($app['expected_salary']): ?>
                        ₹<?php echo number_format($app['expected_salary']); ?>
                    <?php else: ?>
                        <span class="text-muted">Not specified</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong>Notice Period:</strong></td>
                <td><?php echo htmlspecialchars($app['notice_period'] ?? 'Not specified'); ?></td>
            </tr>
            <tr>
                <td><strong>Education:</strong></td>
                <td><?php echo htmlspecialchars($app['education'] ?? 'Not specified'); ?></td>
            </tr>
        </table>
        
        <?php if (!empty($app['skills'])): ?>
        <h5>Skills</h5>
        <div class="card">
            <div class="card-body">
                <?php 
                $skills = explode(',', $app['skills']);
                foreach ($skills as $skill) {
                    $skill = trim($skill);
                    if (!empty($skill)) {
                        echo '<span class="badge bg-secondary me-1 mb-1">' . htmlspecialchars($skill) . '</span>';
                    }
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($app['cover_letter'])): ?>
        <h5>Cover Letter</h5>
        <div class="card">
            <div class="card-body">
                <?php echo nl2br(htmlspecialchars($app['cover_letter'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($app['hr_notes'])): ?>
        <h5>HR Notes</h5>
        <div class="notes-section">
            <?php 
            $notes = explode("\n", $app['hr_notes']);
            foreach ($notes as $note) {
                if (trim($note)) {
                    echo '<div class="note-item">';
                    echo '<div>' . nl2br(htmlspecialchars($note)) . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($app['interview_notes'])): ?>
        <h5>Interview Notes</h5>
        <div class="card">
            <div class="card-body">
                <?php echo nl2br(htmlspecialchars($app['interview_notes'])); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <h5>Application Status</h5>
        <div class="card mb-3">
            <div class="card-body">
                <div class="text-center">
                    <span class="badge bg-<?php echo getStatusColor($app['status']); ?> fs-6">
                        <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <?php if (!empty($app['interview_date'])): ?>
        <h5>Interview Details</h5>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Date & Time:</strong><br>
                <?php echo date('M j, Y g:i A', strtotime($app['interview_date'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <h5>Timeline</h5>
        <div class="timeline mb-3">
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6>Application Submitted</h6>
                    <p class="text-muted mb-0"><?php echo date('M j, Y g:i A', strtotime($app['created_at'])); ?></p>
                </div>
            </div>
            
            <?php if ($app['updated_at'] !== $app['created_at']): ?>
            <div class="timeline-item">
                <div class="timeline-marker bg-info"></div>
                <div class="timeline-content">
                    <h6>Last Updated</h6>
                    <p class="text-muted mb-0"><?php echo date('M j, Y g:i A', strtotime($app['updated_at'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($app['interview_date'])): ?>
            <div class="timeline-item">
                <div class="timeline-marker bg-warning"></div>
                <div class="timeline-content">
                    <h6>Interview Scheduled</h6>
                    <p class="text-muted mb-0"><?php echo date('M j, Y g:i A', strtotime($app['interview_date'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <h5>Quick Actions</h5>
        <div class="d-grid gap-2 mb-3">
            <button class="btn btn-primary" onclick="addNoteFromModal(<?php echo $app['id']; ?>)">
                <i class="fas fa-sticky-note"></i> Add HR Note
            </button>
            <button class="btn btn-info" onclick="scheduleInterviewFromModal(<?php echo $app['id']; ?>)">
                <i class="fas fa-calendar-plus"></i> Schedule Interview
            </button>
            <button class="btn btn-success" onclick="updateStatus(<?php echo $app['id']; ?>, 'selected')">
                <i class="fas fa-check-circle"></i> Mark as Selected
            </button>
            <button class="btn btn-warning" onclick="updateStatus(<?php echo $app['id']; ?>, 'on_hold')">
                <i class="fas fa-pause-circle"></i> Put on Hold
            </button>
            <button class="btn btn-danger" onclick="updateStatus(<?php echo $app['id']; ?>, 'rejected')">
                <i class="fas fa-times-circle"></i> Reject Application
            </button>
        </div>
        
        <h5>Contact Actions</h5>
        <div class="d-grid gap-2">
            <a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="btn btn-outline-primary">
                <i class="fas fa-envelope"></i> Send Email
            </a>
            <a href="tel:<?php echo htmlspecialchars($app['phone']); ?>" class="btn btn-outline-success">
                <i class="fas fa-phone"></i> Call Now
            </a>
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $app['phone']); ?>" 
               target="_blank" class="btn btn-outline-success">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <?php if (!empty($app['resume_path'])): ?>
            <a href="download_resume.php?id=<?php echo $app['id']; ?>"
               target="_blank" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf"></i> View Resume
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.note-item {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    border-left: 4px solid #007bff;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin-common.js"></script>
<script>
// Legacy function for backward compatibility
function updateStatus(appId, status) {
    // Use the new common function
    window.updateStatus(appId, status, 'application');
}

// Legacy functions for modal actions
function addNoteFromModal(appId) {
    window.addNoteFromModal(appId);
}

function scheduleInterviewFromModal(appId) {
    window.scheduleInterviewFromModal(appId);
}
</script>

<?php
function getStatusColor($status) {
    $colors = [
        'applied' => 'primary',
        'screening' => 'info',
        'interview' => 'warning',
        'selected' => 'success',
        'rejected' => 'danger',
        'on_hold' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}
?>
