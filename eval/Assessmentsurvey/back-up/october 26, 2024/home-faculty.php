<?php include('db_connect.php');
function ordinal_suffix1($num){
    $num = $num % 100;
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}
$astat = array("Not Yet Started","On-going","Closed");
?>

<div class="col-12">
    <div class="card">
      <div class="card-body">
        Welcome <?php echo $_SESSION['login_name'] ?>!
        <br>
        <div class="col-md-5">
          <div class="callout callout-info">
            <h5><b>Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix1($_SESSION['academic']['semester'])) ?> Semester</b></h5>
            <h6><b>Evaluation Status: <?php echo $astat[$_SESSION['academic']['status']] ?></b></h6>
          </div>
        </div>
        <!-- Add this div for notifications -->
        <div id="notifications"></div>
      </div>
    </div>
</div>

<!-- Add these CDN links in your header -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Configure toastr options
    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Function to fetch and display notifications
    function fetchNotifications() {
        $.ajax({
            url: 'ajax.php?action=get_notifications',
            method: 'POST',
            data: { faculty_id: <?php echo $_SESSION['login_id'] ?> },
            success: function(response) {
                const notifications = JSON.parse(response);
                if (notifications.length > 0) {
                    let notificationHtml = '<div class="alert alert-warning">';
                    notificationHtml += '<h4>Notifications:</h4>';
                    notificationHtml += '<ul>';
                    notifications.forEach(notification => {
                        notificationHtml += `<li>${notification.message}</li>`;
                        toastr.warning(notification.message, 'Low Rating Alert');
                    });
                    notificationHtml += '</ul>';
                    notificationHtml += '<button class="btn btn-sm btn-primary" id="markAllRead">Mark All as Read</button>';
                    notificationHtml += '</div>';
                    $('#notifications').html(notificationHtml);
                } else {
                    $('#notifications').html('');
                }
            }
        });
    }

    // Fetch notifications on page load
    fetchNotifications();

    // Set up interval to fetch notifications every 5 minutes
    setInterval(fetchNotifications, 5 * 60 * 1000);

    // Handle "Mark All as Read" button click
    $(document).on('click', '#markAllRead', function() {
        $.ajax({
            url: 'ajax.php?action=mark_all_notifications_read',
            method: 'POST',
            data: { faculty_id: <?php echo $_SESSION['login_id'] ?> },
            success: function(response) {
                if (response) {
                    fetchNotifications();
                    toastr.success('All notifications marked as read');
                }
            }
        });
    });
});
</script>