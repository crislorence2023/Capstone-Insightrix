<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

    body, .navbar, .dropdown-menu {
        font-family: 'Montserrat', sans-serif;
    }

    .user-img {
        border-radius: 50%;
        height: 32px;
        width: 32px;
        object-fit: cover;
    }
    .navbar-brand {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .nav-link {
        color: #333;
    }
    .section-name {
    color: #0056b3;
    font-weight: bold;
    white-space: nowrap; /* Prevents text wrapping */
    overflow: visible; /* Ensures text isn't cut off */
    display: inline-block; /* Keeps the text together */
    width: auto; /* Allows the container to expand */
}
    .login-name {
        color: #FF9767;
    }
    
    .notification-badge {
        position: absolute;
        top: 0px;
        right: 5px;
        padding: 3px 6px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        font-size: 10px;
    }
    
    .notification-dropdown {
        width: 300px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notification-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-item.unread {
        background-color: #f8f9fa;
    }
    
    .notification-content {
        font-size: 0.9rem;
        color: #333;
    }
    
    .notification-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<!-- Add this line in your header -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light bg-white">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php if(isset($_SESSION['login_id'])): ?>
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fa-solid fa-bars"></i>
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="navbar-brand" href="./">
         
              
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <?php if($_SESSION['login_type'] != 1): // Show only for non-admin users ?>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-bell"></i>
                <span class="notification-badge" id="notification-count" style="display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationDropdown">
                <div class="dropdown-header">
                    Notifications
                    <a href="#" class="float-right text-decoration-none" id="markAllRead">Mark all as read</a>
                </div>
                <div class="dropdown-divider"></div>
                <div id="notification-list">
                    <!-- Notifications will be populated here -->
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="#" id="viewAllNotifications">View all notifications</a>
            </div>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fa-solid fa-expand"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="assets/uploads/<?php echo $_SESSION['login_avatar'] ?>" alt="" class="user-img border">
                <span class="ml-2"><?php echo ucwords($_SESSION['login_firstname']) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <span class="dropdown-item-text">
                    <?php 
                    // Use firstname and lastname if available, otherwise use email
                    if(isset($_SESSION['login_firstname']) && isset($_SESSION['login_lastname'])) {
                        echo ucwords($_SESSION['login_firstname'] . ' ' . $_SESSION['login_lastname']);
                    } elseif(isset($_SESSION['login_email'])) {
                        echo $_SESSION['login_email'];
                    } else {
                        echo "User";
                    }
                    ?>
                </span>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0)" id="manage_account">
                    <i class="fa-solid fa-gear"></i> Manage Account
                </a>
                <a class="dropdown-item" href="javascript:void(0)" id="logout_btn">
                    <i class="fa-solid fa-power-off"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<script>
$(document).ready(function(){
    $('#manage_account').click(function(){
        uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>');
    });

    // Make the hamburger icon functional
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-collapse');
        $('body').toggleClass('sidebar-open');
    });

    <?php if($_SESSION['login_type'] != 1): // Only add notification logic for non-admin users ?>
    // Function to fetch notifications
    function fetchNotifications() {
        $.ajax({
            url: 'ajax.php?action=get_notifications',
            method: 'POST',
            data: { user_id: <?php echo $_SESSION['login_id'] ?> },
            success: function(response) {
                try {
                    const notifications = JSON.parse(response);
                    updateNotificationUI(notifications);
                    checkLowRatings(notifications);
                } catch (error) {
                    console.error('Error processing notifications:', error);
                }
            }
        });
    }

    // Function to update notification UI
    function updateNotificationUI(notifications) {
        const unreadCount = notifications.filter(n => !n.is_read).length;
        const $notificationCount = $('#notification-count');
        const $notificationList = $('#notification-list');

        // Update notification count
        if (unreadCount > 0) {
            $notificationCount.text(unreadCount).show();
        } else {
            $notificationCount.hide();
        }

        // Update notification list
        $notificationList.empty();
        if (notifications.length === 0) {
            $notificationList.append('<div class="dropdown-item">No notifications</div>');
        } else {
            notifications.forEach(notification => {
                const notificationHtml = `
                    <div class="notification-item ${notification.is_read ? '' : 'unread'}" data-id="${notification.id}">
                        <div class="notification-content">${notification.message}</div>
                        <div class="notification-time">${notification.created_at}</div>
                    </div>
                `;
                $notificationList.append(notificationHtml);
            });
        }
    }

    // Function to check for low ratings and display toasts
    function checkLowRatings(notifications) {
        const lowRatingNotifications = notifications.filter(n => !n.is_read && n.message.includes('has received an average rating'));
        
        lowRatingNotifications.forEach(notification => {
            toastr.warning(notification.message, 'Low Rating Alert', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: true,
                onclick: function() {
                    // Mark the notification as read when clicked
                    $.ajax({
                        url: 'ajax.php?action=mark_notification_read',
                        method: 'POST',
                        data: { notification_id: notification.id },
                        success: function() {
                            fetchNotifications();
                        }
                    });
                }
            });
        });
    }

    // Mark notification as read when clicked
    $(document).on('click', '.notification-item', function() {
        const notificationId = $(this).data('id');
        $.ajax({
            url: 'ajax.php?action=mark_notification_read',
            method: 'POST',
            data: { notification_id: notificationId },
            success: function() {
                fetchNotifications();
            }
        });
    });

    // Mark all notifications as read
    $('#markAllRead').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=mark_all_notifications_read',
            method: 'POST',
            data: { user_id: <?php echo $_SESSION['login_id'] ?> },
            success: function() {
                fetchNotifications();
                toastr.clear(); // Clear all toasts when marking all as read
            }
        });
    });

    // Initial fetch and periodic updates
    fetchNotifications();
    setInterval(fetchNotifications, 60000); // Check every minute
    <?php endif; ?>

    // Add new logout handler
    $('#logout_btn').click(function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=logout4_coe',
            method: 'POST',
            success: function(resp) {
                // Clear any session storage or local storage if needed
                localStorage.clear();
                sessionStorage.clear();
                
                // Clear cookies
                document.cookie.split(";").forEach(function(c) { 
                    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
                });
                
                // Redirect to login page
                location.href = 'stafflogin_coe.php';
            }
        });
    });
});


(document).ready(function(){
    function toggleSidebar() {
        $('body').toggleClass('sidebar-collapse');
        $('body').toggleClass('sidebar-open');
        
        // Force a re-flow to ensure proper rendering
        $('.main-sidebar').hide().show(0);
    }

    // Hamburger menu click event
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        toggleSidebar();
    });

    // Check and set initial state
    function checkSidebarState() {
        if ($(window).width() <= 768) {
            $('body').addClass('sidebar-collapse');
        } else {
            $('body').removeClass('sidebar-collapse');
        }
    }

    // Run on page load
    checkSidebarState();

    // Run on window resize
    $(window).resize(function() {
        checkSidebarState();
    });

    // Override AdminLTE's sidebar toggle behavior
    $.AdminLTE = $.AdminLTE || {};
    $.AdminLTE.options = $.AdminLTE.options || {};
    $.AdminLTE.options.sidebarToggleSelector = '[data-widget="pushmenu"]';
    $(document).off('click', $.AdminLTE.options.sidebarToggleSelector);
    $(document).on('click', $.AdminLTE.options.sidebarToggleSelector, function(e) {
        e.preventDefault();
        toggleSidebar();
    });
});
</script>