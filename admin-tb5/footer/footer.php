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