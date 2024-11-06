<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  date_default_timezone_set("Asia/Manila");
  
  ob_start();
  $title = isset($_GET['page']) ? ucwords(str_replace("_", ' ', $_GET['page'])) : "Home";
  ?>
  <title><?php echo $title ?> | <?php echo $_SESSION['system']['name'] ?></title>
  <?php ob_end_flush() ?>
  <link rel="icon" href="./logo/Evalucator-nobg2.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
   <!-- SweetAlert2 -->
  <link rel="stylesheet" href="assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
   <!-- Jquery-UI -->
  <link rel="stylesheet" href="assets/plugins/jquery-ui/jquery-ui.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="assets/plugins/toastr/toastr.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="assets/plugins/dropzone/min/dropzone.min.css">
  <!-- DateTimePicker -->
  <link rel="stylesheet" href="assets/dist/css/jquery.datetimepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Switch Toggle -->
  <link rel="stylesheet" href="assets/plugins/bootstrap4-toggle/css/bootstrap4-toggle.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/dist/css/styles.css">
	<script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
 <!-- summernote -->
  <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
   <!-- Jquery-UI -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  

  
</head>