<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\sidebar\sidebar.php
?>
<style>
    .sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        width: var(--sidebar-width);
        height: calc(100vh - 70px);
        background: white;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        overflow-y: auto;
        z-index: 999;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 18px 25px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .sidebar-menu a:hover {
        background: #f8f9fa;
        color: var(--primary-blue);
        border-left: 4px solid var(--primary-blue);
        padding-left: 21px;
    }
    
    .sidebar-menu a.active {
        background: var(--primary-blue);
        color: white;
        border-left: 4px solid #2948b8;
        padding-left: 21px;
    }
    
    .sidebar-menu a.active:hover {
        background: var(--primary-blue);
        color: white;
    }
    
    .sidebar-menu i {
        font-size: 1.2rem;
        width: 25px;
        text-align: center;
    }
    
    .menu-label {
        flex: 1;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Function to check if current page matches
function isActive($page) {
    global $current_page;
    return $current_page === $page ? 'active' : '';
}
?>

<aside class="sidebar">
    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="../admin-dashboard/" class="<?php echo isActive('admin-dashboard.php'); ?>">
                <i class="bi bi-speedometer2"></i>
                <span class="menu-label">Dashboard</span>
            </a>
        </li>
        
        <!-- Logs -->
        <li>
            <a href="../logs/" class="<?php echo isActive('logs.php'); ?>">
                <i class="bi bi-journal-text"></i>
                <span class="menu-label">Logs</span>
            </a>
        </li>
        
        <!-- Student Records -->
        <li>
            <a href="../student-records/" class="<?php echo isActive('student-records.php'); ?>">
                <i class="bi bi-person-lines-fill"></i>
                <span class="menu-label">Student Records</span>
            </a>
        </li>
        
        <!-- Account Management -->
        <li>
            <a href="../account-management/" class="<?php echo isActive('account-management.php'); ?>">
                <i class="bi bi-people-fill"></i>
                <span class="menu-label">Account Management</span>
            </a>
        </li>
        
        <!-- Batch Management -->
        <li>
            <a href="../create-batch/" class="<?php echo isActive('create-batch.php'); ?>">
                <i class="bi bi-collection"></i>
                <span class="menu-label">Batch Management</span>
            </a>
        </li>
        
        <!-- Course Management -->
        <li>
            <a href="../course-creation/" class="<?php echo isActive('course-creation.php'); ?>">
                <i class="bi bi-book"></i>
                <span class="menu-label">Course Management</span>
            </a>
        </li>

        <!-- Subject Management -->
        <li>
            <a href="../subject-management/" class="<?php echo isActive('subject-management.php'); ?>">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span class="menu-label">Subject Management</span>
            </a>
        </li>
        
        <!-- TOR -->
        <li>
            <a href="../tor-grades/" class="<?php echo isActive('tor-grades.php'); ?>">
                <i class="bi bi-clipboard-data"></i>
                <span class="menu-label">TOR</span>
            </a>
        </li>
        
        <!-- Document Management -->
        <li>
            <a href="../documents-approval/" class="<?php echo isActive('documents-approval.php'); ?>">
                <i class="bi bi-file-earmark-check"></i>
                <span class="menu-label">Document Management</span>
            </a>
        </li>
        
        <!-- Print Management -->
        <li>
            <a href="../print-management/" class="<?php echo isActive('print-management.php'); ?>">
                <i class="bi bi-printer-fill"></i>
                <span class="menu-label">Print Management</span>
            </a>
        </li>
    </ul>
</aside>