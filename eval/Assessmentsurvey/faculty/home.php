<?php $faculty_id = $_SESSION['login_id'] ?>

<?php
include('db_connect.php');

function ordinal_suffix($num) {
    $num = $num % 100;
    if ($num < 11 || $num > 13) {
        switch ($num % 10) {
            case 1: return $num . 'st';
            case 2: return $num . 'nd';
            case 3: return $num . 'rd';
        }
    }
    return $num . 'th';
}

$faculty_id = $_SESSION['login_id'];
$academic_id = $_SESSION['academic']['id'];
$student_query = "SELECT 
    COUNT(DISTINCT sl.id) as total_students,
    COUNT(DISTINCT fa.class_id) as total_classes,
    COUNT(DISTINCT fa.subject_id) as total_subjects
FROM faculty_assignments fa
LEFT JOIN class_list cl ON fa.class_id = cl.id
LEFT JOIN student_list sl ON cl.id = sl.class_id
WHERE fa.faculty_id = {$faculty_id} 
AND fa.academic_year_id = {$academic_id}
AND fa.is_active = 1";

$student_result = $conn->query($student_query);
$student_summary = $student_result->fetch_assoc();

// Get all subjects for this instructor in current semester
$query = "SELECT 
    r.subject_id,
    s.code,
    s.subject,
    (SELECT AVG(ea.rate)
    FROM evaluation_answers ea 
    INNER JOIN evaluation_list el ON ea.evaluation_id = el.evaluation_id
    WHERE el.faculty_id = r.faculty_id 
    AND el.subject_id = r.subject_id
    AND el.academic_id = r.academic_id) as average_rating
FROM restriction_list r
INNER JOIN subject_list s ON r.subject_id = s.id
WHERE r.faculty_id = {$faculty_id} 
AND r.academic_id = {$academic_id}
GROUP BY r.subject_id";

$result = $conn->query($query);

$total_subjects = 0;
$perf_distribution = array(
    'low' => 0,
    'moderate' => 0,
    'high' => 0
);

while($row = $result->fetch_assoc()) {
    if ($row['average_rating'] !== null) {
        $total_subjects++;
        $rating = floatval($row['average_rating']);
        
        if($rating <= 2) {
            $perf_distribution['low']++;
        } else if($rating > 2 && $rating <= 4) {
            $perf_distribution['moderate']++;
        } else {
            $perf_distribution['high']++;
        }
    }
}

$astat = array("Not Yet Started", "Started", "Closed");
$login_name = $_SESSION['login_name'];



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Evaluation System - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
       </style>


<style>
       :root {
    --primary-color: #2563eb;
    --secondary-color: #3b82f6;
    --accent-color: #ef4444;
    --success-color: #22c55e;
    --warning-color: #eab308;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --background-light: #f8fafc;
    --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --transition-base: all 0.2s ease-in-out;
}

body {
    font-family: 'Montserrat', sans-serif;
    line-height: 1.6;
    color: var(--text-primary);
    background-color: var(--background-light);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.welcome-section {
    margin-bottom: 2.5rem;
    animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.welcome-title {
    font-size: 2rem;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.welcome-subtitle {
    color: var(--text-secondary);
    font-size: 1.125rem;
    max-width: 36rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 6.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 1rem;
    padding: 1.75rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition-base);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
}

.card-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.25rem;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
    transition: var(--transition-base);
}

.academic-icon {
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--primary-color);
}

.security-icon {
    background-color: rgba(34, 197, 94, 0.1);
    color: var(--success-color);
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    letter-spacing: -0.025em;
}

.academic-info {
    margin-top: 1.25rem;
    padding: 1rem;
    background-color: rgba(241, 245, 249, 0.5);
    border-radius: 0.75rem;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    font-weight: 500;
    margin-right: 0.75rem;
    color: var(--text-secondary);
    min-width: 120px;
}

.info-value {
    font-weight: 600;
    color: var(--text-primary);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: var(--transition-base);
}

.status-not-started {
    background-color: rgba(234, 179, 8, 0.1);
    color: var(--warning-color);
}

.status-started {
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--primary-color);
}

.status-closed {
    background-color: rgba(239, 68, 68, 0.1);
    color: var(--accent-color);
}

.security-content {
    padding: 1.25rem;
    background-color: rgba(34, 197, 94, 0.05);
    border-radius: 0.75rem;
    margin-top: 1.25rem;
    border: 1px solid rgba(34, 197, 94, 0.1);
}

.security-text {
    color: var(--text-secondary);
    font-size: 0.9375rem;
    line-height: 1.6;
}

.chart-wrapper {
    position: relative;
    margin: 2rem auto;
    width: 100%;
    max-width: 1200px;
    margin-bottom: 1rem;
}


.chart-container {
    position: relative;
    width: 100%;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: var(--card-shadow);
    padding-top: 2rem;
    padding-bottom: 3.5rem;
    padding-left: 1rem;
    padding-right: 1rem;
  
    height: 460px;
    
    
}

.chart-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 2rem;
    padding-left: 0.5rem;

}

/* Adjust container for different screen sizes */
@media (max-width: 768px) {
    .chart-container {
        padding: 1rem;
        min-height: 260px;
        max-height: 350px; /* Smaller max height for mobile */
        margin: 0.5rem 0;
    }

    .chart-title {
        font-size: 1rem;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .chart-container {
        min-height: 200px;
        max-height: 300px; /* Even smaller for very small screens */
        padding: 0.75rem;
    }
}
.callout {
    padding: 1rem;
    background-color: rgba(37, 99, 235, 0.05);
    border-left: 4px solid var(--primary-color);
    border-radius: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 1.5rem;
}

@keyframes fadeIn {
    from { 
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .dashboard {
        padding: 1.25rem;
    }

    .welcome-title {
        font-size: 1.75rem;
    }

    .welcome-subtitle {
        font-size: 1rem;
    }

    .dashboard-card {
        padding: 1.5rem;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
}

        .update-highlight {
            animation: highlight 1s ease-in-out;
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-card {
                padding: 1.25rem;
            }
        }

        .dashboard-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .students-icon {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .summary-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            text-align: center;
        }

        .summary-item {
    padding: 1rem;
    border: 1px solid #dee2e6; /* Light grey border */
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    transition: transform 0.2s;
    background: none; /* Remove background */
}


        .summary-item:hover {
            transform: translateY(-2px);
        }

        .summary-number {
            display: block;
            font-size: 1.8rem;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-size: 0.9rem;
            color: #666;
        }

        @media (min-width: 769px) {
            .summary-content {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .view-details-link {
    text-align: right;
    padding-top: 10px;
    margin-top: 10px;
    border-top: 1px solid #eee;
}

.view-details-link .btn-link {
    color: #0d6efd;
    text-decoration: none;
    font-size: 0.9rem;
}

.view-details-link .btn-link:hover {
    text-decoration: underline;
}

#studentListModal .modal-dialog {
    max-width: 800px;
}

#studentListModal .table {
    margin-bottom: 0;
}

#studentListModal .modal-body {
    padding: 1.5rem;
}

.table-responsive {
    margin: -1rem;
}

.student-count {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 1rem;
}

.teaching-footer {
    padding: 15px;
    border-top: 1px solid #eee;
    text-align: right;
}

.view-details {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: color 0.3s ease;
}

.view-details:hover {
    color: #0056b3;
}

.view-details i {
    font-size: 12px;
}

    .status-indicator {
        font-size: 1.2rem !important;
        padding: 0.3rem 0.8rem;
        border-radius: 1rem;
        display: inline-block;
    }

    .status-indicator.not-yet-started {
        background-color: rgba(234, 179, 8, 0.1);
        color: var(--warning-color);
    }

    .academic-status-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Status-specific container gradients */
.academic-status-item.not-yet-started {
    background: linear-gradient(135deg, #ffffff, #fff3cd);
    
   
}

.academic-status-item.started {
    background: linear-gradient(135deg, #ffffff, #cfe2ff);
    
}

.academic-status-item.closed {
    background: linear-gradient(135deg, #ffffff, #f8d7da);
    
}

/* Make the status value take full width for the gradient effect */
.status-value {
    width: 100%;
    padding: 0.5rem 1rem;
    border-radius: 6px;
}

    .status-content {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        text-align: center;
        
    }

    .status-label {
        font-weight: 500 !important;
        font-size: 1rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .status-value {
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .status-dot {
        font-size: 0.5rem;
    }

    /* Status-specific styles */
    .status-value.not-yet-started {
        color: var(--warning-color);
    }

    .status-value.started {
        color: var(--primary-color);
    }

    .status-value.closed {
        color: var(--accent-color);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .summary-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-areas: 
                "item1 item2"
                "item3 item3"
                "item4 item4";
            gap: 1rem;
        }

        .summary-item:nth-child(1) { grid-area: item1; }
        .summary-item:nth-child(2) { grid-area: item2; }
        .summary-item:nth-child(3) { grid-area: item3; }
        .summary-item:nth-child(4) { grid-area: item4; }

        .academic-status-item {
            grid-column: span 2;
            margin-top: 0.5rem;
        }
    }

    /* Hover effect */
    .academic-status-item:hover {
        background-color: #f8f9fa;
    }

</style>
</head>
<body>


    <main class="dashboard">
     
    
        <section class="welcome-section">
            <h1 class="welcome-title">
                Welcome, <span id="welcome-text"></span>
            </h1>
            <p class="welcome-subtitle">Access your evaluation dashboard and track your evaluation results</p>
        </section>
       
            <!-- Add new Student Summary Card -->
            <article class="dashboard-card">
    <div class="card-header">
        <div class="card-icon students-icon">
            <i class="fas fa-users"></i>
        </div>
        <h2 class="card-title">Teaching Summary</h2>
    </div>
    <div class="summary-content">
        <div class="summary-item">
            <span class="summary-number"><?php echo $student_summary['total_students']; ?></span>
            <span class="summary-label">Total Students</span>
        </div>
        <div class="summary-item">
            <span class="summary-number"><?php echo $student_summary['total_subjects']; ?></span>
            <span class="summary-label">Subjects Taught</span>
        </div>
        <div class="summary-item">
            <span class="summary-number"><?php echo $_SESSION['academic']['year']; ?></span>
            <span class="summary-label"><?php echo ordinal_suffix($_SESSION['academic']['semester']); ?> Semester</span>
        </div>
        <div class="summary-item academic-status-item <?php echo strtolower(str_replace(' ', '-', $astat[$_SESSION['academic']['status']])); ?>">
            <div class="status-content">
                <span class="status-label">Academic Status</span>
                <span class="status-value <?php echo strtolower(str_replace(' ', '-', $astat[$_SESSION['academic']['status']])); ?>">
                    </i>
                    <?php echo $astat[$_SESSION['academic']['status']]; ?>
                </span>
            </div>
        </div>
    </div>
    <div class="teaching-footer">
        <a href="./index.php?page=StudentList" class="view-details">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</article>


        <div class="chart-wrapper">
    <div class="chart-container">
        <h3 class="chart-title">Subject Performance Distribution (Current Semester)</h3>
        <canvas id="performanceDistChart"></canvas>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Academic Progress Card -->
    <article class="dashboard-card">
        <div class="card-header">
            <div class="card-icon academic-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h2 class="card-title">Academic Progress</h2>
        </div>
        <div id="academic-info" class="academic-info">
            <div class="info-item">
                <span class="info-label">Current Period:</span>
                <span id="academic-year" class="info-value">
                    <?php echo $_SESSION['academic']['year'] . ' ' . ordinal_suffix($_SESSION['academic']['semester']); ?> Semester
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span id="academic-status" class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $astat[$_SESSION['academic']['status']])); ?>">
                    <?php echo $astat[$_SESSION['academic']['status']]; ?>
                </span>
            </div>
        </div>
    </article>

    <!-- Data Security Card -->
    <article class="dashboard-card">
        <div class="card-header">
            <div class="card-icon security-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 class="card-title">Data Security</h2>
        </div>
        <div class="security-content">
            <p class="security-text">
                The evaluation results are strictly confidential and accessible only to authorized personnel. All data is securely stored and handled in accordance with institutional privacy policies and data protection regulations.
            </p>
        </div>
    </article>
</div>

            
</div>
</div>
</div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>


document.addEventListener('DOMContentLoaded', function() {
   
    function initializePerformanceChart() {
        var ctx = document.getElementById('performanceDistChart');
        if (!ctx) return;
        
        ctx = ctx.getContext('2d');
        var performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Low\n(≤ 2.0)', 'Moderate\n(2.1 - 4.0)', 'High\n(> 4.0)'],
                datasets: [{
                    label: 'Number of Subjects (Performances)',
                    data: [
                        <?php echo isset($perf_distribution['low']) ? $perf_distribution['low'] : 0; ?>,
                        <?php echo isset($perf_distribution['moderate']) ? $perf_distribution['moderate'] : 0; ?>,
                        <?php echo isset($perf_distribution['high']) ? $perf_distribution['high'] : 0; ?>
                    ],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(13, 110, 253, 0.8)'
                    ],
                    borderColor: [
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(13, 110, 253, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = <?php echo isset($total_subjects) ? $total_subjects : 0; ?>;
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `Subjects: ${value} (${percentage}%)`;
                            }
                        },
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: true,
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Number of Subjects',
                            font: {
                                size: 13
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 20,
                        bottom: 20
                    }
                }
            }
        });

        // Simplified resize handler
        function handleResize() {
            const container = document.querySelector('.chart-container');
            if (window.innerWidth <= 768) {
                container.style.height = '350px';
            } else {
                container.style.height = '450px';
            }
            performanceChart.resize();
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial sizing
    }

    initializePerformanceChart();
});
   



$(document).ready(function() {
    load_students();
    const text = '<?php echo $login_name; ?>';
    const speed = 50;
    let i = 0;

    // Typing Animation
    function typeWriter() {
        if (i < text.length) {
            document.getElementById('welcome-text').innerHTML += text.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }

    // Update Academic Information
    function updateAcademicInfo() {
        fetch('ajax.php?action=check_academic_year', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(response => {
            if (response) {
                const currentYear = document.getElementById('academic-year').textContent.trim();
                const currentStatus = document.getElementById('academic-status').textContent.trim();
                
                const newSemester = ordinalSuffix(response.semester);
                const newYear = `${response.year} ${newSemester} Semester`;
                const statusTexts = ["Not Yet Started", "Started", "Closed"];
                const newStatus = statusTexts[response.status];
                
                if (currentYear !== newYear) {
                    document.getElementById('academic-year').textContent = newYear;
                    highlightAcademicInfo();
                }
                
                if (currentStatus !== newStatus) {
                    const statusElement = document.getElementById('academic-status');
                    statusElement.textContent = newStatus;
                    statusElement.className = `status-badge status-${newStatus.toLowerCase().replace(/\s+/g, '-')}`;
                    highlightAcademicInfo();
                }
            }
        })
        .catch(error => console.error('Error checking academic year:', error));
    }

    function highlightAcademicInfo() {
        const academicInfo = document.getElementById('academic-info');
        academicInfo.classList.add('update-highlight');
        setTimeout(() => {
            academicInfo.classList.remove('update-highlight');
        }, 1000);
    }

    load_class();

function load_class(){
start_load();
$.ajax({
    url:"ajax.php?action=get_class",
    method:'POST',
    data:{fid:<?php echo $faculty_id ?>},
    error:function(err){
        console.log(err);
        alert_toast("An error occurred",'error');
        end_load();
    },
    success:function(resp){
        if(resp){
            resp = JSON.parse(resp);
            if(Object.keys(resp).length <= 0 ){
                $('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to display.</a>');
            }else{
                $('#class-list').html('');
                Object.keys(resp).map(k=>{
                    let ratingClass = '';
                    let ratingBadge = '';
                    
                    if (resp[k].average_rating !== null) {
                        const rating = parseFloat(resp[k].average_rating);
                        if (rating <= 2) {
                            ratingClass = 'text-black';
                            ratingBadge = '<span class="badge bg-danger">Low Performance</span>';
                        } else if (rating > 2 && rating <= 4) {
                            ratingClass = 'text-black';
                            ratingBadge = '<span class="badge bg-warning">Moderate Performance</span>';
                        } else {
                            ratingClass = 'text-black';
                            ratingBadge = '<span class="badge bg-success">High Performance</span>';
                        }
                    }
                    
                    $('#class-list').append(
                        `<a href="javascript:void(0)" 
                            data-json='${JSON.stringify(resp[k])}' 
                            data-id="${resp[k].id}" 
                            class="list-group-item list-group-item-action show-result ${ratingClass}">
                            <div class="subject-text">${resp[k].class} - ${resp[k].subj}</div>
                            ${ratingBadge}
                        </a>`
                    );
                });
            }
        }
    },
    complete:function(){
        end_load();
        anchor_func();
        if('<?php echo isset($_GET['rid']) ?>' == 1){
            $('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]').trigger('click');
        }else{
            $('.show-result').first().trigger('click');
        }
    }
});
}

function load_students() {
    $.ajax({
        url: "ajax.php?action=get_students",
        method: 'POST',
        data: {
            faculty_id: <?php echo $faculty_id ?>
        },
        success: function(response) {
            try {
                const students = JSON.parse(response);
                let html = '';
                
                if (students.length > 0) {
                    // Create the table structure
                    html = `
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    // Add each student to the table
                    students.forEach(student => {
                        html += `
                            <tr>
                                <td>${student.school_id}</td>
                                <td>${student.student_name}</td>
                                <td>${student.class}</td>
                                <td>${student.subject}</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                        <div class="text-muted">
                            Total Students: ${students.length}
                        </div>
                    `;
                } else {
                    html = `
                        <div class="alert alert-info">
                            No students found for your classes this semester.
                        </div>
                    `;
                }
                
                // Assuming you have a container with id 'student-list'
                $('#student-list').html(html);
                
            } catch (e) {
                console.error('Error parsing student data:', e);
                $('#student-list').html(`
                    <div class="alert alert-danger">
                        Error loading student data. Please try again later.
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#student-list').html(`
                <div class="alert alert-danger">
                    Error loading student data. Please try again later.
                </div>
            `);
        }
    });
}


    // Add ordinal suffix to numbers
    function ordinalSuffix(num) {
        num = num % 100;
        if (num < 11 || num > 13) {
            switch (num % 10) {
                case 1: return num + 'st';
                case 2: return num + 'nd';
                case 3: return num + 'rd';
            }
        }
        return num + 'th';
    }

    // Load Performance Overview
    function getOrdinal(n) {
        var s = ["th", "st", "nd", "rd"];
        var v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    }

    

    // Initial setup
    window.addEventListener('load', () => {
        typeWriter();
        updateAcademicInfo();
        loadPerformanceOverview();
    });

    // Periodic updates
    setInterval(updateAcademicInfo, 30000);
    loadPerformanceOverview();
});
</script>

</body>
</html>