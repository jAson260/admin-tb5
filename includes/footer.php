<?php 
    // SENIOR DEV LOGIC: Path Adjustment
    $currentDir = basename(dirname($_SERVER['PHP_SELF']));
    $subfolders = ['dashboard', 'enrollment', 'history', 'register', 'useraccount', 'forgot password', 'upload'];
    $root = in_array($currentDir, $subfolders) ? '../' : '';
?>

    <!-- INSTITUTIONAL FOOTER SECTION -->
    <footer class="mt-auto py-5 border-top bg-white">
        <div class="container-fluid text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h6 class="fw-bold text-royal text-uppercase mb-3" style="letter-spacing: 1px;">About Us</h6>
                    
                    <p class="mb-2 small text-muted">
                        <i class="fab fa-facebook-square fa-lg me-2 text-primary"></i>
                     
                        <a href="https://www.facebook.com/thebigfivetrainingandassessmentcenterinc" target="_blank" class="text-decoration-bold text-primary">
                            FB Page
                        </a>
                    </p>
                    
                   <p class="mb-0 small text-muted">
    <a href="https://www.google.com/maps/search/?api=1&query=Vitra+Building+123+P.+Alcantara+St.+San+Pablo+City+Philippines" 
       target="_blank" 
       class="text-decoration-none text-muted address-link">
        <i class="fas fa-map-marker-alt fa-lg me-2 text-danger"></i>
        <strong>Address:</strong> 
        <span class="hover-underline">Vitra Building. 123 P. Alcantara St. Brgy. VII-A, San Pablo City, Philippines, 4000</span>
        <br>
        <small class="text-royal" style="font-size: 10px;">(Click to view on Google Maps)</small>
    </a>
</p>

<style>
    /* Senior Developer Polish: Add a subtle hover effect */
    .address-link:hover .hover-underline {
        text-decoration: underline;
        color: var(--royal-blue);
    }
    .address-link:hover i {
        transform: scale(1.2);
        transition: 0.3s;
    }
</style>

                    <hr class="mx-auto my-4" style="width: 100px; border-top: 2px solid var(--royal-blue);">
                    
                    <div class="text-muted" style="font-size: 0.75rem;">
                        &copy; <?php echo date('Y'); ?> <strong>The Big Five Training and Assessment Center Inc.</strong><br>
                        All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- BOOTSTRAP & JQUERY SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Responsive Sidebar Toggle Logic
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarWrapper = document.getElementById('sidebarWrapper');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if(sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebarWrapper.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        if(sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebarWrapper.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Dynamic Notification System
        function loadNotifications() {
            $.ajax({
                url: '<?php echo $root; ?>fetch_notifications.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.count > 0) {
                        $('#notifCount').text(data.count).removeClass('d-none');
                    } else {
                        $('#notifCount').addClass('d-none');
                    }

                    let html = '';
                    if(data.list && data.list.length > 0) {
                        data.list.forEach(function(notif) {
                            let unreadClass = notif.status === 'unread' ? 'notif-unread' : '';
                            html += `
                                <li class="p-3 notif-item ${unreadClass}">
                                    <div class="fw-bold small text-dark">${notif.title}</div>
                                    <div class="text-muted" style="font-size: 11px;">${notif.message}</div>
                                    <div class="text-royal mt-1" style="font-size: 9px;">${notif.created_at}</div>
                                </li>`;
                        });
                    } else {
                        html = '<li class="p-4 text-center text-muted small">No new notifications</li>';
                    }
                    $('#notifItems').html(html);
                }
            });
        }

        $(document).ready(function() {
            loadNotifications();
            setInterval(loadNotifications, 30000); 
        });
    </script>
</body>
</html>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#pass');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function (e) {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the icon class
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
        
        // Optional: Change color when active
        this.querySelector('i').classList.toggle('text-royal');
    });
</script>
<script>
    // Logic for Login Page Toggle
    const toggleLoginPassword = document.querySelector('#toggleLoginPassword');
    const loginPassword = document.querySelector('#loginPassword');
    const loginEyeIcon = document.querySelector('#loginEyeIcon');

    if(toggleLoginPassword) {
        toggleLoginPassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            loginPassword.setAttribute('type', type);
            
            // Toggle the icon class
            loginEyeIcon.classList.toggle('fa-eye');
            loginEyeIcon.classList.toggle('fa-eye-slash');
            
            // Highlight color when viewing
            loginEyeIcon.classList.toggle('text-royal');
        });
    }
</script>