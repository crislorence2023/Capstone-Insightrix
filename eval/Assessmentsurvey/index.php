<?php
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function escape_template($string) {
    // Remove potential Twig/template syntax
    $string = preg_replace('/{[{%].*?[%}]}/', '', $string);
    // Escape special characters
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function escape_template_array($array) {
    return array_map(function($item) {
        if (is_array($item)) {
            return escape_template_array($item);
        }
        return escape_template($item);
    }, $array);
}

// Escape session data
if (isset($_SESSION['system'])) {
    $_SESSION['system'] = escape_template_array($_SESSION['system']);
}

$headers = [
    'X-Frame-Options' => 'SAMEORIGIN',
    'X-XSS-Protection' => '1; mode=block',
    'X-Content-Type-Options' => 'nosniff',
    'Referrer-Policy' => 'strict-origin-origin-when-cross-origin',
    'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
    'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://cdn.jsdelivr.net https://stackpath.bootstrapcdn.com https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://stackpath.bootstrapcdn.com https://cdn.remixicon.com https://cdnjs.cloudflare.com https://code.ionicframework.com; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdn.remixicon.com https://cdnjs.cloudflare.com data:; img-src 'self' data: https:; connect-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com; frame-src 'self'; form-action 'self';",
];

foreach ($headers as $key => $value) {
    header("$key: $value");
}
?>
<!DOCTYPE html>
<html lang="en">
<?php 

	if(!isset($_SESSION['login_id']))
	    header('location:login.php');
    include 'db_connect.php';
    ob_start();
  if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = escape_template($v);
    }
  }
  ob_end_flush();

	include 'header.php' 
?>


<style>
 
  
  </style>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<!-- Add cookie consent modal -->
<div class="modal fade" id="cookieModal" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cookie Consent</h5>
      </div>
      <div class="modal-body">
        <p>We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="acceptCookies()">Accept</button>
        <button type="button" class="btn btn-secondary" onclick="declineCookies()">Decline</button>
      </div>
    </div>
  </div>
</div>

<div class="wrapper">
  <?php 
  if($_SESSION['login_view_folder'] == 'staff-cme/'){
    include 'staff_cme_topbar.php';
  } elseif($_SESSION['login_view_folder'] == 'superadmin/') {
    include 'superadmintopbar.php';
  } elseif($_SESSION['login_view_folder'] == 'staff-coe/') {
    include 'staff_coe_topbar.php';
  } else {
    include 'topbar.php';
  }
  ?>
  <?php include $_SESSION['login_view_folder'].'sidebar.php' ?>
 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="background-color: #FAFEFF;">
  	 <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
	    <div class="toast-body text-white">
	    </div>
	  </div>
    <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            
          </div><!-- /.col -->

        </div><!-- /.row -->
           
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
         <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            $view_folder = $_SESSION['login_view_folder'];
            
            // Get all PHP files in the user's view folder
            $allowed_pages = array_map(
                function($file) {
                    return pathinfo($file, PATHINFO_FILENAME);
                },
                glob($view_folder . "*.php")
            );
            
            // Check if requested page exists in user's view folder
            if (!in_array($page, $allowed_pages)) {
                include '404.html';
            } else {
                include $view_folder . $page . '.php';
            }
          ?>
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
    <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()" style="background-color: #007bff; border-color: #007bff; color: white;">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #6c757d; border-color: #6c757d; color: white;">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer 
  <footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="">Pasilang</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b><?php echo $_SESSION['system']['name'] ?></b>
    </div>
  </footer>
</div> -->
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<!-- Bootstrap -->
<?php include 'footer.php' ?>

<!-- Before closing body tag, add this script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!localStorage.getItem('cookieConsent')) {
        $('#cookieModal').modal('show');
    }
});

function acceptCookies() {
    localStorage.setItem('cookieConsent', 'accepted');
    
    // Enable different types of cookies
    enableAnalytics();
    enablePreferences();
    enableMarketing();
    
    $('#cookieModal').modal('hide');
}

function declineCookies() {
    localStorage.setItem('cookieConsent', 'declined');
    
    // Only enable essential cookies
    disableNonEssentialCookies();
    
    $('#cookieModal').modal('hide');
}

function enableAnalytics() {
    // Initialize analytics tools like Google Analytics
    // Example: gtag('consent', 'update', {'analytics_storage': 'granted'});
}

function enablePreferences() {
    // Enable preference cookies
    document.cookie = "preferences_enabled=true; expires=Fri, 31 Dec 2024 23:59:59 GMT";
}

function disableNonEssentialCookies() {
    // Remove any non-essential cookies
    // Keep only those required for basic site functionality
}
</script>
</body>
</html>