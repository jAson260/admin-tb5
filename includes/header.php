<?php 
    // SENIOR DEV UNIFIED PATH LOGIC
    $currentPath = $_SERVER['PHP_SELF']; 
    $currentPage = basename($currentPath); 
    $currentDir = basename(dirname($currentPath));
    
    // Define 1-level deep subfolders
    $subfolders = ['dashboard', 'enrollment', 'history', 'register', 'useraccount', 'forgot password', 'upload'];

    // 1. Check for 2-levels deep (Big Blossom subfolder)
    if (strpos($currentPath, 'bigblossomenrollment') !== false) {
        $root = '../../';
    } 
    // 2. Check for 1-level deep subfolders
    elseif (in_array($currentDir, $subfolders)) {
        $root = '../';
    } 
    // 3. Otherwise, we are in the root directory
    else {
        $root = '';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Big Five Training and Assessment Center</title>
    
    <!-- Use $root for assets so they never break -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --royal-blue: #4169E1; 
            --royal-dark: #2e51b8;
            --header-height: 80px;
            --sidebar-width: 260px;
        }
        body { background-color: #ffffff; overflow-x: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .bg-royal { background-color: var(--royal-blue) !important; color: #fff !important; }
        .text-royal { color: var(--royal-blue) !important; }
        
        /* Fixed Header */
        .navbar { height: var(--header-height); background-color: var(--royal-blue) !important; z-index: 1050; }
        .navbar-logo { height: 55px; width: 55px; object-fit: cover; background-color: white; }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            padding-top: 20px;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            z-index: 1040;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link { color: #333; padding: 12px 20px; font-weight: 500; }
        .sidebar .nav-link.active { background-color: var(--royal-blue); color: white !important; }

        /* Content Area */
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: all 0.3s ease; min-height: calc(100vh - var(--header-height)); }

        /* Login Backgrounds */
        .login-page {
            background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 40%, #1b77cc 100%);
            background-attachment: fixed;
            height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center; position: relative;
        }
        .login-page::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(249, 250, 255, 0.45); z-index: 1; }
        .login-card { z-index: 2; width: 100%; max-width: 400px; background: rgba(255, 255, 255, 0.85) !important; backdrop-filter: blur(8px); border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1); }

        /* Mobile Logic */
        @media (max-width: 992px) {
            .sidebar { left: calc(var(--sidebar-width) * -1); }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; padding: 20px; }
        }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1035; }
        .sidebar-overlay.show { display: block; }

        /* Notifications */
        .notif-unread { border-left: 4px solid var(--royal-blue); background: #f0f4ff; }

        /* This should be in your header.php <style> section */
.btn-royal { 
    background-color: #4169E1 !important; /* Royal Blue */
    color: white !important; 
    border: none;
    transition: 0.3s;
}

.btn-royal:hover { 
    background-color: #2e51b8 !important; /* Royal Dark */
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
/* Compact Floating Labels */
.form-floating-sm > .form-control,
.form-floating-sm > .form-select {
    height: calc(2.5rem + 2px); /* Normal is 3.5rem, this makes it slimmer */
    padding: 1rem 0.75rem;
    font-size: 0.9rem;
}
/* Alternative: Underline only on hover for a cleaner look */
.text-royal:hover {
    text-decoration: underline !important;
}
.form-floating-sm > label {
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
}

/* Adjust label position when floating */
.form-floating-sm > .form-control:focus ~ label,
.form-floating-sm > .form-control:not(:placeholder-shown) ~ label,
.form-floating-sm > .form-select ~ label {
    transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
}
    </style>
</head>
<body>

<?php 
    // 2. USE THE DEFINED VARIABLE HERE
    // Logic: Hide navbar on Login, Register, and Forgot Password pages
    $authPages = ['index.php', 'register.php', 'forgotpassword.php'];
    if (!in_array($currentPage, $authPages)): 
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-link text-white d-lg-none me-2" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="<?php echo $root; ?>dashboard/dashboard.php">
            <img src="<?php echo $root; ?>img/logo1.png" alt="Logo" class="navbar-logo rounded-circle border border-white border-1">
            <img src="<?php echo $root; ?>img/logo2.png" alt="Logo" class="navbar-logo rounded-circle border border-white border-1">  
            <span class="ms-2">The Big Five Training and Assessment Center Inc. | Big Blossom Institute Inc.</span>
        </a>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Notification Bell -->
            <div class="dropdown me-4">
                <div class="position-relative" id="notifBell" data-bs-toggle="dropdown" style="cursor:pointer;">
                    <i class="fas fa-bell fa-lg text-white"></i>
                    <span id="notifCount" class="badge rounded-pill bg-danger d-none position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;">0</span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="width: 300px;">
                    <li class="dropdown-header fw-bold border-bottom">Recent Notifications</li>
                    <div id="notifItems"></div>
                    <li class="text-center py-2 border-top"><a href="<?php echo $root; ?>history/history.php" class="text-royal small text-decoration-none">View History</a></li>
                </ul>
            </div>

            <!-- Account -->
            <div class="dropdown">
                <a href="#" class="text-white text-decoration-none" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-2x"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="<?php echo $root; ?>useraccount/useraccount.php">My Account</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo $root; ?>index.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div style="height: var(--header-height);"></div>
<?php endif; ?>