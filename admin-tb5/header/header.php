<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Big Five Training and Assessment Center Inc.</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico">
    
    <style>
        :root {
            --primary-blue: #4169E1;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .header {
            background: linear-gradient(135deg, #4169E1 0%, #5B7FEB 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-icon {
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .header-icon:hover {
            transform: scale(1.1);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--primary-blue);
            font-size: 1.2rem;
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-dropdown .dropdown-menu {
            min-width: 180px;
        }
        
        .content-wrapper {
            margin-top: 70px;
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - 70px);
        }
        
        .main-content {
            padding: 30px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="../admin-dashboard/admin-dashboard.php" class="logo">
                <img src="../assets/img/tb5-logo.png" alt="The Big Five Training and Assessment Center Logo">
            </a>
            <a href="../admin-dashboard/admin-dashboard.php" class="logo">
                <img src="../assets/img/bbi-logo.png" alt="Big Blossom Institute Inc. Logo">
            </a>
            <a href="../admin-dashboard/admin-dashboard.php" style="text-decoration: none; color: white;">
                <h1 class="header-title">The Big Five Training and Assessment Center Inc.</h1>
            </a>
        </div>
        <div class="header-right">
            <i class="bi bi-bell-fill header-icon"></i>
            <div class="dropdown user-dropdown">
                <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-fill"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="../profile/profile.php">
                            <i class="bi bi-person me-2"></i>Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="/login/logout">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>