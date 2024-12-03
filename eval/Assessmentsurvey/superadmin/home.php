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

// Add this query to get the default academic year
$default_academic = $conn->query("SELECT * FROM academic_list WHERE is_default = 1")->fetch_assoc();

// If no default is set, get the latest academic year
if (!$default_academic) {
    $default_academic = $conn->query("SELECT * FROM academic_list ORDER BY id DESC LIMIT 1")->fetch_assoc();
}

// Update the session with the current academic year info
$_SESSION['academic'] = $default_academic;
?>

<div class="dashboard-container">
    <!-- Header Section -->
    <div class="header-card">
        <div class="header-content">
            <div class="semester-info">
                <h4>Academic Year</h4>
                <h2><?php echo $default_academic['year'].' '.(ordinal_suffix($default_academic['semester'])) ?> Semester</h2>
            </div>
            <div class="status-badge <?php echo strtolower(str_replace(' ', '-', $astat[$default_academic['status']])) ?>">
                <?php echo $astat[$default_academic['status']] ?>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card faculty">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $conn->query("SELECT * FROM faculty_list")->num_rows; ?></h3>
                    <p>Faculty Members</p>
                </div>
            </div>
            <div class="stat-footer">
                <a href="./indexsuperadmin.php?page=faculty_list" class="view-details">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="stat-card students">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $conn->query("SELECT * FROM student_list")->num_rows; ?></h3>
                    <p>Students</p>
                </div>
            </div>
            <div class="stat-footer">
                <a href="./indexsuperadmin.php?page=student_list" class="view-details">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="stat-card users">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $conn->query("SELECT * FROM users")->num_rows; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-footer">
                <a href="./indexsuperadmin.php?page=user_list" class="view-details">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="stat-card classes">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $conn->query("SELECT * FROM class_list")->num_rows; ?></h3>
                    <p>Active Classes</p>
                </div>
            </div>
            <div class="stat-footer">
                <a href="./indexsuperadmin.php?page=class_list" class="view-details">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 2rem;
    background-color: #f8f9fa;
    min-height: 100vh;
}

.header-card {
    background: linear-gradient(135deg, #1a6d8f, #3ba4d9);

    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.semester-info h4 {
    margin: 0;
    opacity: 0.9;
    font-size: 1rem;
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
    font-size: 0.9rem;
    background: rgba(255, 255, 255, 0.2);
}

.status-badge.not-yet-started { background-color: #ffd700; color: #000; }
.status-badge.on-going { background-color: #00ff7f; color: #000; }
.status-badge.closed { background-color: #ff6b6b; color: white; }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
}

.faculty .stat-icon { background-color: #e3f2fd; color: #1976d2; }
.students .stat-icon { background-color: #f3e5f5; color: #7b1fa2; }
.users .stat-icon { background-color: #e8f5e9; color: #388e3c; }
.classes .stat-icon { background-color: #fff3e0; color: #f57c00; }

.stat-details h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
}

.stat-details p {
    margin: 0.25rem 0 0 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.stat-footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.view-details {
    text-decoration: none;
    color: #666;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
    transition: color 0.2s;
}

.view-details:hover {
    color: #2193b0;
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