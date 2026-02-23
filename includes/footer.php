<?php 
    $currentDir = basename(dirname($_SERVER['PHP_SELF']));
    $subfolders = ['dashboard', 'enrollment', 'history', 'register', 'useraccount', 'forgot password', 'upload', 'bigblossomenrollment'];
    $root = in_array($currentDir, $subfolders) ? '../' : '';
    if ($currentDir == 'bigblossomenrollment') { $root = '../../'; }
?>

<style>
    /* Senior Developer Extra-Compact Footer Styles */
    .footer-premium { background: #fdfdfd; border-top: 1px solid #eceef1; }
    .footer-heading { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; color: #999; margin-bottom: 8px !important; }
    
    /* Extra small pill */
    .fb-pill-xs {
        display: inline-flex; align-items: center; padding: 5px 15px; border-radius: 50px;
        background: rgba(65, 105, 225, 0.03); color: var(--royal-blue); font-weight: 600; font-size: 0.75rem;
        transition: 0.2s; text-decoration: none; border: 1px solid rgba(65, 105, 225, 0.08);
    }
    .fb-pill-xs:hover { background: var(--royal-blue); color: #fff !important; }

    /* Extra small map card */
    .map-card-xs {
        padding: 8px 12px; border-radius: 10px; background: white; transition: 0.2s;
        border: 1px solid #f0f0f0; display: inline-block; text-decoration: none !important; width: 100%; max-width: 280px;
    }
    .map-card-xs:hover { border-color: var(--royal-blue); background: #fbfcfe; }
    
    .bottom-bar-xs { background: var(--royal-dark); padding: 8px 0; color: rgba(255,255,255,0.5); font-size: 0.65rem; }
    
    /* Utility for a small divider line */
    @media (min-width: 768px) { .vr-line { border-right: 1px solid #eee; height: 40px; margin: 0 15px; } }
</style>

<footer class="footer-premium mt-3 pt-3">
    <div class="container mb-3">
        <div class="d-flex flex-wrap justify-content-center align-items-center">
            
            <!-- Connect Section -->
            <div class="text-center px-1">
                <p class="footer-heading">Contact us </p>
                <a href="https://www.facebook.com/thebigfivetrainingandassessmentcenterinc" target="_blank" class="fb-pill-xs">
                    <i class="fab fa-facebook me-2 text-primary"></i> FB Page
                </a>
            </div>

            <!-- Visual Separator for Desktop -->
            <div class="d-none d-md-block vr-line"></div>

            <!-- Location Section -->
            <div class="text-center px-1">
                <p class="footer-heading">Address</p>
                <a href="https://www.google.com/maps/search/?api=1&query=Vitra+Building+123+P.+Alcantara+St.+San+Pablo+City+Philippines" 
                   target="_blank" class="map-card-xs text-start">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-danger me-2" style="font-size: 0.7rem;"></i>
                        <div style="line-height: 1.1;">
                            <span class="d-block fw-bold text-dark" style="font-size: 0.75rem;">Vitra Bldg, San Pablo City</span>
                            <span class="text-muted" style="font-size: 10px;">123 P. Alcantara St. Brgy VII-A</span>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Ultra-slim Bottom Bar -->
    <div class="bottom-bar-xs text-center">
        <div class="container">
            &copy; <?php echo date('Y'); ?> <strong>The Big Five Learning and Assesment Center</strong> | All rights reserve
        </div>
    </div>
</footer>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    const $root = "<?php echo $root; ?>";

    // Dynamic Notifications
    function loadNotifications() {
        $.ajax({
            url: $root + 'fetch_notifications.php',
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
                    data.list.forEach(notif => {
                        let unreadClass = notif.status === 'unread' ? 'notif-unread' : '';
                        html += `<li class="p-3 border-bottom ${unreadClass}"><div class="fw-bold small">${notif.title}</div><div class="text-muted small">${notif.message}</div></li>`;
                    });
                } else {
                    html = '<li class="p-3 text-center text-muted small">No notifications</li>';
                }
                $('#notifItems').html(html);
            }
        });
    }
    loadNotifications();
    setInterval(loadNotifications, 60000);

    // Sidebar/Menu Controls
    $('#sidebarToggle, #sidebarOverlay').on('click', function() {
        $('#sidebarWrapper, #sidebarOverlay').toggleClass('show');
    });

    // Integrated Password Toggle Logic
    function handleToggle(btnID, inputID) {
        $(btnID).on('click', function(e) {
            e.preventDefault();
            const input = $(inputID);
            const icon = $(this).find('i');
            const isPass = input.attr('type') === 'password';
            input.attr('type', isPass ? 'text' : 'password');
            icon.toggleClass('fa-eye fa-eye-slash').toggleClass('text-royal text-muted');
        });
    }
    handleToggle('#togglePassword', '#pass'); 
    handleToggle('#toggleLoginPassword', '#loginPassword');
});
</script>