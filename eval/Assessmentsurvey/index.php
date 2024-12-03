<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
	if(!isset($_SESSION['login_id']))
	    header('location:login.php');
    include 'db_connect.php';
    ob_start();
  if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
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
  <?php include 'topbar.php' ?>
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
            
            // Whitelist of allowed pages (based on sidebar navigation)
            $whitelist = [
                'closed',
                'Completed Eval',
                'done',
                'evaluate',
                'home',
                'topbar',
                'closed',
                'done',
                'result',
                'StudentList',

                'config',       // Block access to configuration files
                        'log',          // Block access to log files
                        'tmp',          // Block access to temporary files
                        'cache',        // Block access to cache files
                        '.php',         // Block direct PHP file access attempts
                        '.ini',         // Block access to ini files
                        '.env',         // Block access to environment files
                        'backup',       // Block access to backup files
                        'admin',        // Block direct admin directory access
                        'includes',     // Block access to includes directory
                        'upload',       // Block direct access to upload directory
                        'system',       // Block access to system files
                        'database',     // Block database related files
                        'sql',          // Block SQL files
                        'install',      // Block installation files
                        'setup',        // Block setup files
                        'phpinfo',      // Block phpinfo access
                        'shell',        // Block shell access
                        'cmd',          // Block command execution
                        'exec' ,         // Block execution attempts
                        'closed',
               
            ];

            // Blacklist of explicitly forbidden pages
            $blacklist = [
              '../',          // Block directory traversal attempts
                'config',
                'settings',
                'system',
                'database',
                'logs',
                'admin_access',
                'evaluation-status',
                'report',
                'academic_list',
                'department',
                'subject_list',
                'class_list',
                'Room',
                'AssignClassSubjects',
                'new_faculty',
                'faculty_list',
                'new_student',
                'student_list',
                'Classroom',
                'staff_list',
                'cme_staff',
                'ceas_staff',
                'coe_staff',
                'questionnaire',
                'view_surveys',
                'LiveClassroom',
                'new_user',
                'user_list',
              
                'sensitive_data',
                'staff_cme_index',
                'staffindex',
                'staff_coe_index',
                'staff_ceas_index',
                'admin_class',
                'indexsuperadmin',
                'ajax',
                'change_password',
                'db_connect',
                'header',
                'footer',
                'index',
                
            ];

            // Check if page is allowed
            if (in_array($page, $blacklist)) {
                include '403.html'; // Access forbidden
            } elseif (!in_array($page, $whitelist)) {
                include '404.html'; // Page not found
            } else {
                // Check user role and include appropriate page
                if ($_SESSION['login_type'] == '2') {
                    if (file_exists('faculty/'.$page.".php")) {
                        include 'faculty/'.$page.'.php';
                    } else {
                        include '404.html';
                    }
                } else {
                    if (file_exists('student/'.$page.".php")) {
                        include 'student/'.$page.'.php';
                    } else {
                        include '404.html';
                    }
                }
            }
        ?>
    </div>
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