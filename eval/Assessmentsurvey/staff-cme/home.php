<?php 
// Include database connection at the start
include('./db_connect.php');

// Check if academic session is set
if(!isset($_SESSION['academic'])) {
    // Get default academic year
    $academic = $conn->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();
    if($academic) {
        $_SESSION['academic'] = $academic;
    }
}

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

<div class="dashboard-container">
    <!-- Header Section -->
    <div class="header-card">
        <div class="header-content">
            <div class="semester-info">
                <h4>Academic Year</h4>
                <h2><?php 
                    if(isset($_SESSION['academic']) && isset($_SESSION['academic']['year']) && isset($_SESSION['academic']['semester'])) {
                        echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester']));
                    } else {
                        echo "No Academic Year Set";
                    }
                ?> Semester</h2>
            </div>
            <div class="status-badge <?php 
                if(isset($_SESSION['academic'])) {
                    echo strtolower(str_replace(' ', '-', $astat[$_SESSION['academic']['status']]));
                }
            ?>">
                <?php 
                    if(isset($_SESSION['academic'])) {
                        echo $astat[$_SESSION['academic']['status']];
                    } 
                ?>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Faculty Stats -->
        <div class="stat-card faculty">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-details">
                    <h3><?php 
                        // Only count faculty in CME department
                        echo $conn->query("SELECT * FROM faculty_list WHERE department='CME'")->num_rows; 
                    ?></h3>
                    <p>Faculty Members</p>
                </div>
            </div>
        </div>

        <!-- Classes Stats -->
        <div class="stat-card classes">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stat-details">
                    <h3><?php 
                        // Count classes in CME department
                        echo $conn->query("SELECT * FROM class_list WHERE department='CME'")->num_rows; 
                    ?></h3>
                    <p>Classes</p>
                </div>
            </div>
        </div>

        <!-- Students Stats -->
        <div class="stat-card students">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-details">
                    <h3><?php 
                        // Count students in CME classes
                        $query = "SELECT COUNT(DISTINCT s.id) as count 
                                FROM student_list s 
                                INNER JOIN class_list c ON s.class_id = c.id 
                                WHERE c.department='CME'";
                        $result = $conn->query($query);
                        echo $result->fetch_assoc()['count'];
                    ?></h3>
                    <p>Students</p>
                </div>
            </div>
        </div>

        <!-- Subjects Stats -->
        <div class="stat-card subjects">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-details">
                    <h3><?php 
                        // Count subjects in CME department
                        echo $conn->query("SELECT * FROM subject_list WHERE department='CME'")->num_rows; 
                    ?></h3>
                    <p>Subjects</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 2rem;
    
    min-height: calc(100vh - 60px);
    border-radius: 15px;
}

.header-card {
    
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    background: linear-gradient(to right, #2193b0, #6dd5ed);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.semester-info {
 
    padding: 1.5rem;
    border-radius: 10px;
    color: #fff;
}

.semester-info h4 {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.semester-info h2 {
    margin: 0.5rem 0 0 0;
    font-size: 1.8rem;
    font-weight: 600;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.status-badge.not-yet-started { background: #ffd700; color: #000; }
.status-badge.on-going { background: #4CAF50; color: #fff; }
.status-badge.closed { background: #f44336; color: #fff; }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.faculty .stat-icon { background: #e3f2fd; color: #1976d2; }
.classes .stat-icon { background: #f3e5f5; color: #7b1fa2; }
.students .stat-icon { background: #e8f5e9; color: #388e3c; }
.subjects .stat-icon { background: #fff3e0; color: #f57c00; }

.stat-details h3 {
    margin: 0;
    font-size: 1.8rem;
    color: #333;
}

.stat-details p {
    margin: 0;
    color: #666;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
}
</style>