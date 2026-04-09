<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <!-- Sidebar sections -->
        <div class="sidebar-section">Main</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Customer Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'active' : ''; ?>" href="leads.php">
                    <i class="fas fa-users"></i>
                    <span>Leads Management</span>
                    <?php if (isset($leadsStats) && $leadsStats['new_leads'] > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo $leadsStats['new_leads']; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'job_applications.php' ? 'active' : ''; ?>" href="job_applications.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Job Applications</span>
                    <?php if (isset($jobsStats) && $jobsStats['new_applications'] > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo $jobsStats['new_applications']; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact_inquiries.php' ? 'active' : ''; ?>" href="contact_inquiries.php">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Inquiries</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Analytics</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
        </ul>

        <?php if ($auth->hasRole('admin')): ?>
        <div class="sidebar-section">Administration</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-user-cog"></i>
                    <span>User Management</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
        <?php endif; ?>

        <!-- Admin label moved to bottom -->
        <div class="sidebar-admin-label">
            <div class="admin-badge">
                <i class="fas fa-shield-alt me-2"></i>
                <span>Admin Panel</span>
            </div>
        </div>
    </div>
</nav>


