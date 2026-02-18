<?php 
    $current_page = basename($_SERVER['PHP_SELF']); 
?>

<!-- Added ID 'sidebarWrapper' -->
<div class="sidebar shadow-sm" id="sidebarWrapper">
    <ul class="nav flex-column mt-2">
        <!-- DASHBOARD LINK -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" 
               href="<?php echo $root; ?>dashboard/dashboard.php">
                <i class="fas fa-th-large me-3"></i>Dashboard
            </a>
        </li>

       <li class="nav-item">
    <!-- SENIOR DEV FIX: Check for both enrollment filenames -->
    <?php 
        $isEnrollmentActive = ($current_page == 'enrollment.php' || $current_page == 'bb_enrollment.php') ? 'active' : ''; 
    ?>
    <a class="nav-link <?php echo $isEnrollmentActive; ?>" 
       href="<?php echo $root; ?>enrollment/enrollment.php">
        <i class="fas fa-user-edit me-3"></i>Enrollment
    </a>
</li>

        <!-- UPLOAD DOCUMENTS LINK -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'upload.php') ? 'active' : ''; ?>" 
               href="<?php echo $root; ?>upload/upload.php">
                <i class="fas fa-file-upload me-3"></i>Uploaded Documents
            </a>
        </li>

        <!-- HISTORY LINK -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>" 
               href="<?php echo $root; ?>history/history.php">
                <i class="fas fa-history me-3"></i>History
            </a>
        </li>

      
    </ul>
</div>