<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');
     
body{
    width: 100%;
}
    .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #0d6efd;
    color: white;
    font-weight: 500;
    font-size: 14px;
    text-transform: uppercase;
    border: 2px solid #ffffff;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

@media screen and (max-width: 768px) {
    .user-avatar {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}
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
        margin-left: 1rem;
        color: grey;
        font-size: 15px;
        font-family: 'Poppins';
        font-weight: 300;
        letter-spacing: 2px;
    }
    
    .notification-dropdown {
    width: 350px;
    max-height: 80vh; /* Limit height to 80% of viewport height */
    overflow: hidden; /* Hide overflow on container */
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: fixed; /* Fixed positioning */
    right: 10px; /* Align to right with padding */
    top: 60px; /* Position below navbar */
    background: white;
    border-radius: 8px;
    z-index: 1050; /* Ensure it's above other content */
}

.notification-header {
    position: sticky;
    top: 0;
    background: white;
    z-index: 1;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.notification-list {
    max-height: calc(80vh - 60px); /* Subtract header height */
    overflow-y: auto;
    padding: 0;
    margin: 0;
    scrollbar-width: thin;
}

.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 3px;
}

.notification-time {
    font-size: 0.8rem;
    color: #6c757d;
}

.notification-item .mark-read {
    display: none;
    font-size: 0.8rem;
    color: #007bff;
}

.notification-item:hover .mark-read {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.empty-notifications {
    padding: 20px;
    text-align: center;
    color: #6c757d;
}

.notification-badge {
    position: absolute;
    
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 1px 6px;
    font-size: 10px;
    transform: translate(50%, -50%);
}

.notification-item {
    transition: all 0.3s ease;
}

.notification-item.unread {
    background-color: #f8f9fa;
    border-left: 3px solid #0d6efd;
}

.notification-item.read {
    background-color: white;
    border-left: 3px solid transparent;
}

.notification-content {
    color: #212529;
}

.notification-item.unread .notification-content {
    font-weight: 500;
}

.notification-item .mark-read-btn {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notification-item:hover .mark-read-btn {
    opacity: 1;
}

.dropdown-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.dropdown-header .action-btn {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: none;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
}

.dropdown-header .action-btn:hover {
    background-color: #e9ecef;
    color: #495057;
}

.new-badge {
    background-color: #0d6efd;
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    margin-left: 0.5rem;
}
@media screen and (max-width: 768px) {
    /* Hide the firstname on mobile */
    .navbar .ml-2 {
        display: none;
    }
    
    /* Ensure login-name is visible and properly styled on mobile */
   
    
    /* Adjust navbar spacing for mobile */
    .navbar-nav {
        flex-direction: row;
        align-items: center;
    }
    
    /* Adjust user image size for mobile */
    .user-img {
        height: 28px;
        width: 28px;
    }
    
    /* Ensure dropdown menu doesn't get cut off */
    .navbar .dropdown-menu {
        position: absolute;
        right: 0;
        left: auto;
        max-width: 280px;
    }
    
    /* Adjust navbar brand spacing */
    .navbar-brand {
        font-size: 1rem;
        padding: 0.5rem 0;
    }
    
    /* Ensure notification dropdown is properly positioned */
    .notification-dropdown {
        width: 100%;
        max-width: 320px;
        right: -10px;
    }
}

/* Additional media query for very small screens */
@media screen and (max-width: 480px) {
    .login-name {
        max-width: 150px;
        font-size: 12px !important;
    }
    
    .navbar-brand {
        max-width: 200px;
    }
    
    .notification-dropdown {
        max-width: 300px;
    }
}

/* Enlarge all icons */
.navbar-nav .nav-link i {
    font-size: 1.3rem; /* Adjust size as needed */
}

/* Specific styles for the logout icon and text */
.logout-icon, .logout-text {
    color: #b22222;
    font-size: 1.1rem;
    font-weight: 600;
}
</style>

<!-- Add this line in your header -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Navbar -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<nav class="main-header navbar navbar-expand navbar-light bg-white">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php if(isset($_SESSION['login_id'])): ?>
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="ri-menu-line"></i>
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="navbar-brand" href="./">
                <span class="login-name">
                    <?php 
                    if(isset($_SESSION['login_type']) && $_SESSION['login_type'] == 3): // Student type
                        // Get class info from class_list table
                        $class_qry = $conn->query("SELECT cl.curriculum, cl.level, cl.section, cl.department 
                                                FROM class_list cl 
                                                INNER JOIN student_list sl ON cl.id = sl.class_id 
                                                WHERE sl.id = '".$_SESSION['login_id']."'");
                        if($class_qry->num_rows > 0):
                            $class_data = $class_qry->fetch_assoc();
                            // Construct class display string
                            $class_str = '';
                            if(!empty($class_data['curriculum'])) $class_str .= $class_data['curriculum'].' ';
                            if(!empty($class_data['level'])) $class_str .= $class_data['level'];
                            if(!empty($class_data['section'])) $class_str .= '-'.$class_data['section'];
                            if(!empty($class_data['department'])) $class_str .= ' ('.$class_data['department'].')';
                            echo $class_str;
                        endif;
                    endif; 
                    ?>
                </span>
                <span class="section-name">
                    <?php echo isset($page_title) ? $page_title : ''; ?> 
                </span>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
    <?php if($_SESSION['login_type'] == 2): // Show only for faculty users (type 2) ?>
<li class="nav-item dropdown">
    <a class="nav-link" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="ri-notification-3-line"></i>
        <span class="notification-badge" id="notification-count" style="display: none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationDropdown">
    <div class="dropdown-header d-flex justify-content-between align-items-center p-3">
        <span class="font-weight-bold">Notifications</span>
        <div>
            <a href="#" class="action-btn mr-2" id="markAllRead">
                <i class="ri-check-double-line"></i> Mark all read
            </a>
            <a href="#" class="action-btn" id="deleteAllNotifications">
                <i class="ri-delete-bin-line"></i> Clear all
            </a>
        </div>
    </div>
    <div class="dropdown-divider m-0"></div>
    <div id="notification-container" class="notification-list">
        <!-- Notifications will be inserted here -->
    </div>
</div>
</li>
<?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="ri-fullscreen-line"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="assets/uploads/<?php echo $_SESSION['login_avatar'] ?>" alt="" class="user-img border">
                <span class="ml-2"><?php echo ucwords($_SESSION['login_firstname']) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <span class="dropdown-item-text"><?php echo $_SESSION['login_name'] ?></span>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0)" id="manage_account">
                    <i class="ri-settings-3-line"></i> Manage Account
                </a>
                <a class="dropdown-item" href="ajax.php?action=logout">
                    <i class="ri-logout-box-line logout-icon"></i> <span class="logout-text">Logout</span>
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
</body>
</html>


<script>
$(document).ready(function(){

    function createUserAvatar(name, imagePath) {
    const container = document.createElement('div');
    container.className = 'user-avatar';
    
    if (imagePath && imagePath !== 'default_avatar.jpg') {
        const img = document.createElement('img');
        img.src = 'assets/uploads/' + imagePath;
        img.alt = name;
        img.onerror = function() {
            this.remove();
            container.textContent = getInitials(name);
        };
        container.appendChild(img);
    } else {
        container.textContent = getInitials(name);
    }
    
    return container;
}

function getInitials(name) {
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .slice(0, 2);
}

// Replace all avatar images with the new component
document.addEventListener('DOMContentLoaded', function() {
    const userImgElement = document.querySelector('.user-img');
    if (userImgElement) {
        const userName = document.querySelector('#navbarDropdown .ml-2').textContent.trim();
        const avatarPath = userImgElement.getAttribute('src').split('/').pop();
        const avatar = createUserAvatar(userName, avatarPath);
        userImgElement.replaceWith(avatar);
    }
});






    $('#manage_account').click(function(){
        uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>');
    });

    function toggleSidebar() {
        $('body').toggleClass('sidebar-collapse');
        $('body').toggleClass('sidebar-open');
        $('.main-sidebar').hide().show(0);
    }

    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        toggleSidebar();
    });

    function checkSidebarState() {
        if ($(window).width() <= 768) {
            $('body').addClass('sidebar-collapse');
        } else {
            $('body').removeClass('sidebar-collapse');
        }
    }

    checkSidebarState();
    $(window).resize(function() {
        checkSidebarState();
    });

    <?php if($_SESSION['login_type'] == 2): ?>
    function fetchNotifications() {
        $.ajax({
            url: 'ajax.php?action=get_notifications',
            method: 'POST',
            data: { faculty_id: <?php echo $_SESSION['login_id'] ?> },
            success: function(response) {
                try {
                    const notifications = JSON.parse(response);
                    if (Array.isArray(notifications)) {
                        updateNotificationUI(notifications);
                    } else {
                        console.error('Unexpected response format:', response);
                    }
                } catch (error) {
                    console.error('Error processing notifications:', error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch notifications:', error);
            }
        });
    }

    function updateNotificationUI(notifications) {
        const unreadCount = notifications.filter(n => !n.is_read).length;
        const $notificationCount = $('#notification-count');
        const $notificationContainer = $('#notification-container');

        if (unreadCount > 0) {
            $notificationCount.text(unreadCount).show();
        } else {
            $notificationCount.hide();
        }

        $notificationContainer.empty();
        if (notifications.length === 0) {
            $notificationContainer.append(`
                <div class="empty-notifications p-4 text-center text-gray-500">
                    <i class="fa-regular fa-bell-slash fa-2x mb-2"></i>
                    <p class="mb-0">No notifications</p>
                </div>
            `);
        } else {
            notifications.forEach(notification => {
                const notificationHtml = `
                    <div class="notification-item p-3 border-bottom ${notification.is_read ? 'read' : 'unread'}" 
                         data-id="${notification.id}">
                        <div class="notification-content mb-2">
                            ${notification.message}
                            ${notification.is_recent ? '<span class="badge badge-info ml-2">New</span>' : ''}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${notification.formatted_date}</small>
                            ${!notification.is_read ? `
                                <button class="btn btn-sm btn-link text-primary mark-read p-0" onclick="markAsRead(${notification.id})">
                                    <i class="fa-solid fa-check"></i> Mark as read
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                $notificationContainer.append(notificationHtml);
            });
        }
    }

    function markAsRead(notificationId) {
    const $notificationItem = $(`.notification-item[data-id="${notificationId}"]`);
    const $markReadBtn = $notificationItem.find('.mark-read');
    
    // Disable button and show loading state
    $markReadBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

    $.ajax({
        url: 'ajax.php?action=mark_notification_read',
        method: 'POST',
        data: { notification_id: notificationId },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    // Update UI immediately
                    $notificationItem.removeClass('unread').addClass('read');
                    $markReadBtn.fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Update notification count
                    const $notificationCount = $('#notification-count');
                    const currentCount = parseInt($notificationCount.text());
                    if (currentCount > 1) {
                        $notificationCount.text(currentCount - 1);
                    } else {
                        $notificationCount.hide();
                    }
                } else {
                    // Show error and restore button
                    alert(result.message || 'Failed to mark notification as read');
                    $markReadBtn.prop('disabled', false)
                        .html('<i class="fa-solid fa-check"></i> Mark as read');
                }
            } catch (error) {
                console.error('Error processing response:', error);
                alert('An error occurred while marking the notification as read');
                $markReadBtn.prop('disabled', false)
                    .html('<i class="fa-solid fa-check"></i> Mark as read');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax request failed:', error);
            alert('Failed to mark notification as read. Please try again.');
            $markReadBtn.prop('disabled', false)
                .html('<i class="fa-solid fa-check"></i> Mark as read');
        }
    });
}

    // Make markAsRead function globally accessible
    window.markAsRead = function(notificationId) {
        const $notificationItem = $(`.notification-item[data-id="${notificationId}"]`);
        const $markReadBtn = $notificationItem.find('.mark-read');
        
        // Disable button and show loading state
        $markReadBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'ajax.php?action=mark_notification_read',
            method: 'POST',
            data: { notification_id: notificationId },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        // Update UI immediately
                        $notificationItem.removeClass('unread').addClass('read');
                        $markReadBtn.remove();
                        // Refresh notifications to update counts
                        fetchNotifications();
                    } else {
                        console.error('Failed to mark notification as read:', result.message);
                        // Restore button state
                        $markReadBtn.prop('disabled', false)
                            .html('<i class="fa-solid fa-check"></i> Mark as read');
                    }
                } catch (error) {
                    console.error('Error processing response:', error);
                    // Restore button state
                    $markReadBtn.prop('disabled', false)
                        .html('<i class="fa-solid fa-check"></i> Mark as read');
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to mark notification as read:', error);
                // Restore button state
                $markReadBtn.prop('disabled', false)
                    .html('<i class="fa-solid fa-check"></i> Mark as read');
            }
        });
    };

    $('#markAllRead').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Marking all...');
        
        $.ajax({
            url: 'ajax.php?action=mark_all_notifications_read',
            method: 'POST',
            data: { faculty_id: <?php echo $_SESSION['login_id'] ?> },
            success: function(response) {
                fetchNotifications();
                $('#markAllRead').prop('disabled', false)
                    .html('<i class="fa-solid fa-check-double"></i> Mark all read');
            },
            error: function(xhr, status, error) {
                console.error('Failed to mark all notifications as read:', error);
                $('#markAllRead').prop('disabled', false)
                    .html('<i class="fa-solid fa-check-double"></i> Mark all read');
            }
        });
    });

    $('#deleteAllNotifications').click(function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) {
            $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Clearing...');
            
            $.ajax({
                url: 'ajax.php?action=delete_all_notifications',
                method: 'POST',
                data: { faculty_id: <?php echo $_SESSION['login_id'] ?> },
                success: function(response) {
                    fetchNotifications();
                    $('#deleteAllNotifications').prop('disabled', false)
                        .html('<i class="fa-solid fa-trash"></i> Clear all');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete notifications:', error);
                    $('#deleteAllNotifications').prop('disabled', false)
                        .html('<i class="fa-solid fa-trash"></i> Clear all');
                }
            });
        }
    });

    let isNotificationDropdownOpen = false;

    $('#notificationDropdown').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        isNotificationDropdownOpen = !isNotificationDropdownOpen;
        $('.notification-dropdown').toggleClass('show', isNotificationDropdownOpen);
    });

    $('.notification-dropdown').on('click', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.notification-dropdown, #notificationDropdown').length) {
            isNotificationDropdownOpen = false;
            $('.notification-dropdown').removeClass('show');
        }
    });

    fetchNotifications();
    setInterval(fetchNotifications, 60000);
    <?php endif; ?>
});
</script>

