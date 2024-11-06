<?php include('db_connect.php'); ?>
<?php 
function ordinal_suffix($num){
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

<div class="container-fluid">
  <div class="col-12">
    <div class="card bg-teal-700 text-white mb-4">
      <div class="card-body">
        <h5 class="font-weight-bold">Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</h5>
        <h6 class="mb-0">Evaluation Status: <?php echo $astat[$_SESSION['academic']['status']] ?></h6>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="small-box shadow-sm border">
        <div class="inner">
          <h3><?php echo $conn->query("SELECT * FROM faculty_list")->num_rows; ?></h3>
          <p>Total Faculties</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-tie"></i>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
      <div class="small-box shadow-sm border">
        <div class="inner">
          <h3><?php echo $conn->query("SELECT * FROM student_list")->num_rows; ?></h3>
          <p>Total Students</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-graduate"></i>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
      <div class="small-box shadow-sm border">
        <div class="inner">
          <h3><?php echo $conn->query("SELECT * FROM users")->num_rows; ?></h3>
          <p>Total Users</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
      <div class="small-box shadow-sm border">
        <div class="inner">
          <h3><?php echo $conn->query("SELECT * FROM class_list")->num_rows; ?></h3>
          <p>Total Classes</p>
        </div>
        <div class="icon">
          <i class="fas fa-chalkboard"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .container-fluid{
  
  }
.small-box {
  position: relative;
  padding: 20px;
  margin-bottom: 20px;
  border-radius: 5px;
  background-color: #FAFEFF;
}

.small-box .inner {
  padding: 10px;
}

.small-box .icon {
  position: absolute;
  top: 15px;
  right: 15px;
  font-size: 40px;
  color: rgba(0,0,0,0.15);
}

.small-box h3 {
  font-size: 38px;
  font-weight: bold;
  margin: 0 0 10px 0;
  white-space: nowrap;
  padding: 0;
}

.small-box p {
  font-size: 15px;
  margin-bottom: 0;
}

.bg-teal-700 {
  background: linear-gradient(135deg, #FF8C00, #FF4500);


}
</style>