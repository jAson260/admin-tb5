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
    <link rel="icon" type="image/x-icon" href="<?php echo $root; ?>img/favicon.ico">
    
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
        .navbar { 
            height: var(--header-height); 
            background-color: var(--royal-blue) !important; 
            z-index: 1050;
            padding: 0.5rem 1rem;
        }
        .navbar-logo { 
            height: 55px; 
            width: 55px; 
            object-fit: cover; 
            background-color: white;
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-size: 1rem;
            white-space: normal;
            line-height: 1.3;
        }

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
            overflow-y: auto;
        }
        .sidebar .nav-link { color: #333; padding: 12px 20px; font-weight: 500; }
        .sidebar .nav-link.active { background-color: var(--royal-blue); color: white !important; }

        /* Content Area */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 30px; 
            transition: all 0.3s ease; 
            min-height: calc(100vh - var(--header-height)); 
        }

        /* Login Backgrounds */
        .login-page {
            background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 40%, #1b77cc 100%);
            background-attachment: fixed;
            height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center; position: relative;
        }
        .login-page::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(249, 250, 255, 0.45); z-index: 1; }
        .login-card { z-index: 2; width: 100%; max-width: 400px; background: rgba(255, 255, 255, 0.85) !important; backdrop-filter: blur(8px); border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1); }

        /* Notifications */
        .notif-unread { border-left: 4px solid var(--royal-blue); background: #f0f4ff; }

        /* Buttons */
        .btn-royal { 
            background-color: #4169E1 !important;
            color: white !important; 
            border: none;
            transition: 0.3s;
        }
        .btn-royal:hover { 
            background-color: #2e51b8 !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Compact Floating Labels */
        .form-floating-sm > .form-control,
        .form-floating-sm > .form-select {
            height: calc(2.5rem + 2px);
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }
        .text-royal:hover {
            text-decoration: underline !important;
        }
        .form-floating-sm > label {
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
        }
        .form-floating-sm > .form-control:focus ~ label,
        .form-floating-sm > .form-control:not(:placeholder-shown) ~ label,
        .form-floating-sm > .form-select ~ label {
            transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
        }

        /* Sidebar Overlay */
        .sidebar-overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0; 
            background: rgba(0,0,0,0.5); 
            z-index: 1035; 
        }
        .sidebar-overlay.show { display: block; }

        /* ===== MOBILE RESPONSIVE STYLES ===== */
        @media (max-width: 992px) {
            :root {
                --header-height: 70px;
            }
            
            /* Header adjustments */
            .navbar { 
                height: var(--header-height);
                padding: 0.5rem 0.75rem;
            }
            
            /* Logo sizes for mobile */
            .navbar-logo { 
                height: 40px; 
                width: 40px;
            }
            
            /* Brand text - hide on very small screens */
            .navbar-brand {
                font-size: 0.85rem;
                max-width: calc(100vw - 180px); /* Leave space for buttons */
            }
            
            /* Notification and profile buttons */
            .mobile-nav-icons {
                gap: 0.5rem;
            }
            
            .mobile-nav-icons .fa-bell,
            .mobile-nav-icons .fa-user-circle {
                font-size: 1.3rem !important;
            }
            
            /* Notification dropdown */
            .dropdown-menu {
                width: 280px !important;
                max-width: calc(100vw - 20px);
            }
            
            /* Sidebar */
            .sidebar { 
                left: calc(var(--sidebar-width) * -1);
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }
            .sidebar.show { left: 0; }
            
            /* Main content */
            .main-content { 
                margin-left: 0; 
                padding: 15px;
            }
            
            /* Sidebar toggle button */
            #sidebarToggle {
                padding: 0.25rem 0.5rem;
            }
        }

        @media (max-width: 576px) {
            :root {
                --header-height: 65px;
            }
            
            .navbar { 
                height: var(--header-height);
                padding: 0.5rem;
            }
            
            /* Extra small screens - make logos even smaller */
            .navbar-logo { 
                height: 35px; 
                width: 35px;
            }
            
            /* Hide second logo on very small screens */
            .navbar-logo:nth-child(2) {
                display: none;
            }
            
            /* Adjust brand text */
            .navbar-brand {
                font-size: 0.7rem;
                line-height: 1.2;
                max-width: calc(100vw - 150px);
            }
            
            /* Smaller icons */
            .mobile-nav-icons .fa-bell,
            .mobile-nav-icons .fa-user-circle {
                font-size: 1.1rem !important;
            }
            
            /* Notification badge */
            #notifCount {
                font-size: 0.5rem !important;
            }
            
            /* Main content padding */
            .main-content { 
                padding: 10px;
            }
            
            /* Dropdown menu full width on mobile */
            .dropdown-menu {
                width: calc(100vw - 20px) !important;
                margin: 0 10px;
            }
        }

        /* Tablet landscape */
        @media (min-width: 768px) and (max-width: 991px) {
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .navbar-logo { 
                height: 45px; 
                width: 45px;
            }
        }

        /* Smooth transitions */
        .navbar-brand, .navbar-logo, .mobile-nav-icons i {
            transition: all 0.3s ease;
        }

        /* Fix notification badge position on mobile */
        @media (max-width: 576px) {
            .position-relative .badge {
                transform: translate(50%, -50%) !important;
            }
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
        <button class="btn btn-link text-white d-lg-none me-2 p-1" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center flex-grow-1 flex-lg-grow-0" href="<?php echo $root; ?>dashboard/dashboard.php">
            <img src="<?php echo $root; ?>img/logo1.png" alt="Logo" class="navbar-logo rounded-circle border border-white border-1">
            <img src="<?php echo $root; ?>img/logo2.png" alt="Logo" class="navbar-logo rounded-circle border border-white border-1 d-none d-sm-inline">  
            <span class="ms-2 d-none d-md-inline">The Big Five Training and Assessment Center Inc. | Big Blossom Institute Inc.</span>
            <span class="ms-2 d-inline d-md-none small">TB5 | BBI</span>
        </a>
        
        <div class="d-flex align-items-center mobile-nav-icons">
           

            <!-- Account -->
            <div class="dropdown">
    <a href="#" class="text-white text-decoration-none" data-bs-toggle="dropdown">
        <i class="fas fa-user-circle fa-2x"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow">
        <li>
            <a class="dropdown-item small" href="<?php echo $root; ?>useraccount/useraccount.php">
                <i class="fas fa-user me-2"></i>My Account
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
    <a class="dropdown-item text-danger small" href="<?php echo $root; ?>login/logout.php">
        <i class="fas fa-sign-out-alt me-2"></i>Logout
    </a>
</li>
    </ul>
</div>
        </div>
    </div>
</nav>
<div style="height: var(--header-height);"></div>


<?php endif; ?>