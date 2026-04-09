<?php
require_once 'auth.php';
$auth->requireLogin();

$conn = getDBConnection();
$leadId = (int)($_GET['id'] ?? 0);

if (!$leadId) {
    echo '<div class="alert alert-danger">Invalid lead ID</div>';
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM leads WHERE id = ?");
    $stmt->execute([$leadId]);
    $lead = $stmt->fetch();
    
    if (!$lead) {
        echo '<div class="alert alert-danger">Lead not found</div>';
        exit;
    }
} catch(PDOException $e) {
    echo '<div class="alert alert-danger">Error loading lead details</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-8">
        <h5>Contact Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Name:</strong></td>
                <td><?php echo htmlspecialchars($lead['name']); ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>">
                        <?php echo htmlspecialchars($lead['email']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>
                    <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>">
                        <?php echo htmlspecialchars($lead['phone']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Source:</strong></td>
                <td><?php echo htmlspecialchars($lead['source'] ?? 'Website'); ?></td>
            </tr>
        </table>
        
        <h5>Lead Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Property Interest:</strong></td>
                <td><?php echo htmlspecialchars($lead['property_interest'] ?? 'Not specified'); ?></td>
            </tr>
            <tr>
                <td><strong>Budget Range:</strong></td>
                <td><?php echo htmlspecialchars($lead['budget_range'] ?? 'Not specified'); ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="badge bg-<?php echo getStatusColor($lead['status']); ?>">
                        <?php echo ucfirst($lead['status']); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Priority:</strong></td>
                <td>
                    <span class="badge bg-<?php echo getPriorityColor($lead['priority']); ?>">
                        <?php echo ucfirst($lead['priority']); ?>
                    </span>
                </td>
            </tr>
            <?php if (!empty($lead['assigned_to'])): ?>
            <tr>
                <td><strong>Assigned To:</strong></td>
                <td><?php echo htmlspecialchars($lead['assigned_to']); ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($lead['follow_up_date'])): ?>
            <tr>
                <td><strong>Follow-up Date:</strong></td>
                <td><?php echo date('M j, Y', strtotime($lead['follow_up_date'])); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        
        <?php if (!empty($lead['message'])): ?>
        <h5>Message</h5>
        <div class="card">
            <div class="card-body">
                <?php echo nl2br(htmlspecialchars($lead['message'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($lead['notes'])): ?>
        <h5>Notes</h5>
        <div class="notes-section">
            <?php 
            $notes = explode("\n", $lead['notes']);
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
    </div>
    
    <div class="col-md-4">
        <h5>Timeline</h5>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6>Lead Created</h6>
                    <p class="text-muted mb-0"><?php echo date('M j, Y g:i A', strtotime($lead['created_at'])); ?></p>
                </div>
            </div>
            
            <?php if ($lead['updated_at'] !== $lead['created_at']): ?>
            <div class="timeline-item">
                <div class="timeline-marker bg-info"></div>
                <div class="timeline-content">
                    <h6>Last Updated</h6>
                    <p class="text-muted mb-0"><?php echo date('M j, Y g:i A', strtotime($lead['updated_at'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <h5 class="mt-4">Quick Actions</h5>
        <div class="d-grid gap-2">
            <button class="btn btn-primary" onclick="addNote(<?php echo $lead['id']; ?>, 'lead')" data-bs-toggle="tooltip" title="Add a note to this lead">
                <i class="fas fa-sticky-note"></i> Add Note
            </button>
            <button class="btn btn-success" onclick="updateStatus(<?php echo $lead['id']; ?>, 'contacted', 'lead')" data-bs-toggle="tooltip" title="Mark this lead as contacted">
                <i class="fas fa-phone"></i> Mark as Contacted
            </button>
            <button class="btn btn-warning" onclick="updateStatus(<?php echo $lead['id']; ?>, 'qualified', 'lead')" data-bs-toggle="tooltip" title="Mark this lead as qualified">
                <i class="fas fa-star"></i> Mark as Qualified
            </button>
            <button class="btn btn-info" onclick="updateStatus(<?php echo $lead['id']; ?>, 'converted', 'lead')" data-bs-toggle="tooltip" title="Mark this lead as converted">
                <i class="fas fa-check-circle"></i> Mark as Converted
            </button>
        </div>
        
        <h5 class="mt-4">Contact Actions</h5>
        <div class="d-grid gap-2">
            <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" class="btn btn-outline-primary">
                <i class="fas fa-envelope"></i> Send Email
            </a>
            <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>" class="btn btn-outline-success">
                <i class="fas fa-phone"></i> Call Now
            </a>
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone']); ?>" 
               target="_blank" class="btn btn-outline-success">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
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

<!-- Add Note Modal -->
<div class="modal fade" id="addLeadNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="noteLeadId" value="">
                    <div class="mb-3">
                        <label for="noteText" class="form-label">Note</label>
                        <textarea class="form-control" id="noteText" rows="4" placeholder="Enter your note here..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNote('lead')">Save Note</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin-common.js"></script>
<script>
// Legacy function for backward compatibility
function updateStatus(leadId, status) {
    // Use the new common function
    window.updateStatus(leadId, status, 'lead');
}

// Legacy function for backward compatibility
function addNote(leadId) {
    // Use the new common function
    window.addNote(leadId, 'lead');
}
</script>

<?php
function getStatusColor($status) {
    $colors = [
        'new' => 'primary',
        'contacted' => 'info',
        'qualified' => 'warning',
        'converted' => 'success',
        'closed' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}

function getPriorityColor($priority) {
    $colors = [
        'high' => 'danger',
        'medium' => 'warning',
        'low' => 'success'
    ];
    return $colors[$priority] ?? 'secondary';
}
?>
