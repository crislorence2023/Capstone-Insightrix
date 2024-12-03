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

$perf_distribution = array(
    'low' => 0,
    'moderate' => 0,
    'high' => 0
);

$total_subjects = 0;
while($row = $result->fetch_assoc()) {
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
    margin-top: 2rem;
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

.chart-container {
    height: 320px;
    position: relative;
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: var(--card-shadow);
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
    </style>
</head>
<body>
    <main class="dashboard">
        <section class="welcome-section">
            <h1 class="welcome-title">
                Welcome, <span id="welcome-text"></span>
            </h1>
            <p class="welcome-subtitle">Access your evaluation dashboard and track your academic progress</p>
        </section>

        <div class="card border-info shadow-sm mb-4">
    <div class="container mt-4 mb-3 bg-light">
        <p class=" mb-3 bg-teal">Subject Performance Distribution (Current Semester)</p>
        <canvas id="performanceDistChart" width="100%" height="40"></canvas>
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
                        Your evaluation data is protected with industry-standard encryption. All information is handled with strict confidentiality and complies with data protection regulations.
                    </p>
                </div>
            </article>

            
</div>
</div>
</div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>


document.addEventListener('DOMContentLoaded', function() {
    // Initialize performance distribution chart
    function initializePerformanceChart() {
        var ctx = document.getElementById('performanceDistChart');
        if (!ctx) return; // Check if canvas exists
        
        ctx = ctx.getContext('2d');
        var performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Low Performance\n(≤ 2.0)', 'Moderate Performance\n(2.1 - 4.0)', 'High Performance\n(> 4.0)'],
                datasets: [{
                    label: 'Number of Subjects',
                    data: [
                        <?php echo isset($perf_distribution['low']) ? $perf_distribution['low'] : 0; ?>,
                        <?php echo isset($perf_distribution['moderate']) ? $perf_distribution['moderate'] : 0; ?>,
                        <?php echo isset($perf_distribution['high']) ? $perf_distribution['high'] : 0; ?>
                    ],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.8)',  // red for low
                        'rgba(255, 193, 7, 0.8)',  // yellow for moderate
                        'rgba(13, 110, 253, 0.8)'  // blue for high
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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Number of Subjects'
                        }
                    }
                },
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
                        }
                    }
                }
            }
        });
    }

    // Call the initialization function
    initializePerformanceChart();
});



$(document).ready(function() {

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