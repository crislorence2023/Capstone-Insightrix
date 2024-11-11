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

$astat = array("Not Yet Started", "Started", "Closed");
$login_name = $_SESSION['login_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Teacher Evaluation System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success-color: #22c55e;
            --warning-color: #eab308;
            --border-radius: 12px;
            --transition: all 0.3s ease;
            --glow-color: rgba(37, 99, 235, 0.2);
            --animate-duration: 20s;
            --progress-color: #4299e1;  /* Added progress color */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
          
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
          
            line-height: 1.6;
            padding: 2rem;
        }

        .welcome-container {
            max-width: 1200px;
            margin: 0 auto 2rem;
            padding: 1.5rem;
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .welcome-avatar {
            width: 48px;
            height: 48px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .welcome-message {
            font-size: 1.25rem;
            font-weight: 500;
        }

        #welcome-text {
            color: var(--primary-color);
            font-weight: 600;
        }

        .grid-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

                .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card-icon i {
            font-size: 1.25rem;
            line-height: 1;
        }

                .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .academic-icon {
            background: var(--primary-color);
        }

        .security-icon {
            background: var(--success-color);
        }

        .survey-icon {
            background: var(--warning-color);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .card-title-progress {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            
        }


        .card-content {
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 1rem;
            box-shadow: 0 4px 8px rgba(66, 153, 225, 0.3);
        }

        .status-not-started {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-started {
            background: #ecfdf5;
            color: #059669;
        }

        .status-closed {
            background: #f3f4f6;
            color: #4b5563;
        }

        .evaluate-btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .evaluate-btn:hover {
            background: var(--secondary-color);
        }

        .deadline-text {
            margin-top: 0.75rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .feature-list {
            list-style: none;
            margin-top: 1rem;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .feature-list li i {
            color: var(--success-color);
        }


        .evaluation-progress {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.progress-bar {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: #4299e1;
    transition: width 0.3s ease;
}

.completed-list {
    max-height: 300px;
    overflow-y: auto;
}

.completed-item {
    border-left: 4px solid #48bb78;
    padding: 1rem;
    margin-bottom: 0.5rem;
}

.completed-item:last-child {
    margin-bottom: 0;
}

@keyframes semesterChangeSwipe {
    0% {
        transform: translateX(0) scale(1);
        opacity: 1;
        background: var(--card-background);
    }
    15% {
        transform: translateX(0) scale(1.02);
        opacity: 1;
    }
    35% {
        transform: translateX(-120%);
        opacity: 0;
    }
    50% {
        transform: translateX(120%);
        opacity: 0;
    }
    65% {
        transform: translateX(0) scale(1.02);
        opacity: 1;
    }
    80% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
    90% {
        transform: translateX(0) scale(1.01);
        opacity: 1;
    }
    100% {
        transform: translateX(0) scale(1);
        opacity: 1;
        background: var(--card-background);
    }
}

@keyframes glowPulse {
    0% {
        box-shadow: 0 0 5px rgba(37, 99, 235, 0.1);
    }
    20% {
        box-shadow: 0 0 30px rgba(37, 99, 235, 0.4),
                    0 0 60px rgba(37, 99, 235, 0.2);
    }
    40% {
        box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
    }
    60% {
        box-shadow: 0 0 40px rgba(37, 99, 235, 0.5),
                    0 0 80px rgba(37, 99, 235, 0.3);
    }
    80% {
        box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
    }
    100% {
        box-shadow: 0 0 5px rgba(37, 99, 235, 0.1);
    }
}

.semester-change #academic-year {
    display: inline-block;
    animation: semesterChangeSwipe 4s ease-in-out;
}

.semester-change {
    animation: glowPulse 4s ease-in-out;
    position: relative;
    overflow: hidden;
}

.semester-change::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(37, 99, 235, 0.2),
        rgba(37, 99, 235, 0.3),
        rgba(37, 99, 235, 0.2),
        transparent
    );
    animation: shimmer 4s ease-in-out infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
        opacity: 0;
    }
    25% {
        opacity: 1;
    }
    50% {
        left: 100%;
        opacity: 1;
    }
    75% {
        opacity: 0;
    }
    100% {
        left: -100%;
        opacity: 0;
    }
}

/* Add a highlight effect for better visibility */
@keyframes highlightBackground {
    0% {
        background: var(--card-background);
    }
    30% {
        background: rgba(37, 99, 235, 0.05);
    }
    70% {
        background: rgba(37, 99, 235, 0.08);
    }
    100% {
        background: var(--card-background);
    }
}

.semester-change {
    font-family: 'Montserrat', sans-serif; /* Use your primary font */
    font-weight: 600;
    font-size: 1.2em;
    padding: 0.5em 1em;
    color: #2a3f5a; /* Adjust to your desired color */
    position: relative;
    display: inline-block;
    overflow: hidden;
    border-radius: 5px;
}

/* Create a gradient background that only appears behind the text */
.semester-change::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, rgba(37, 99, 235, 0.1), rgba(37, 99, 235, 0.3));
    z-index: -1;
    opacity: 0.8;
    transition: opacity 0.4s ease-in-out;
    border-radius: 5px;
}

/* Optional: Add a pulsing animation to the background */
@keyframes pulseBackground {
    0%, 100% {
        opacity: 0.8;
    }
    50% {
        opacity: 1;
    }
}

.semester-change:hover::before {
    animation: pulseBackground 2s infinite ease-in-out;
}


.evaluation-progress-container {
    margin: 2rem auto;
    max-width: 1200px;
    padding: 0 1rem;
}

/* Progress Summary Styles */
.progress-summary {
    margin-bottom: 2rem;
}

.progress-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
}

.stat-label {
    display: block;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #212529;
}

/* Progress Bar Styles */
.progress-bar-container {
    margin-bottom: 2rem;
}

.progress-bar {
    background: #e9ecef;
    border-radius: 1rem;
    height: 1.5rem;
    overflow: hidden;
}

.progress-fill {
    background: #28a745;
    height: 100%;
    border-radius: 1rem;
    transition: width 0.5s ease-in-out;
    position: relative;
    min-width: 2rem;
}

.progress-text {
    position: absolute;
    right: 0.5rem;
    color: white;
    font-size: 0.875rem;
    line-height: 1.5rem;
}

/* Completed Evaluations List Styles */
.completed-evaluations {
    margin-top: 2rem;
}

.completed-evaluations h3 {
    margin-bottom: 1rem;
    font-size: 1.25rem;
    color: #212529;
}

.evaluations-list {
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.evaluation-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.evaluation-item:last-child {
    border-bottom: none;
}

.eval-icon {
    margin-right: 1rem;
    color: #28a745;
    font-size: 1.5rem;
}

.eval-details {
    flex: 1;
}

.eval-faculty {
    font-weight: bold;
    color: #212529;
}

.eval-subject {
    color: #6c757d;
    font-size: 0.875rem;
    margin: 0.25rem 0;
}

.eval-date {
    color: #6c757d;
    font-size: 0.875rem;
}

.eval-date i {
    margin-right: 0.5rem;
}

.no-evaluations {
    padding: 2rem;
    text-align: center;
    color: #6c757d;
}

.no-evaluations i {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

/* Responsive Design */
@media (max-width: 768px) {
    .progress-stats {
        grid-template-columns: 1fr;
    }

    .stat-item {
        padding: 0.75rem;
    }

    .eval-faculty {
        font-size: 0.9rem;
    }

    .eval-subject {
        font-size: 0.8rem;
    }

    .eval-date {
        font-size: 0.8rem;
    }
}





        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .grid-container {
                grid-template-columns: 1fr;
            }

            .welcome-container {
                flex-direction: column;
                text-align: center;
            }
        }

        .update-highlight {
            animation: highlight 1s ease;
        }

        @keyframes highlight {
            0% { background-color: #fef3c7; }
            100% { background-color: var(--card-background); }
        }

        .progress-icon {
    background: var(--progress-color);  /* Using the progress-color variable you already defined */
}
.location {
    text-align: center;
    margin: 1.5rem 0;
    color: var(--text-primary);
}

.location p {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 600;
}

.location i {
    color: teal;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(5px);
    }
}
    </style>

    
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-avatar">
            <i class="fas fa-user"></i>
        </div>
        <p class="welcome-message">
            Welcome back, <span id="welcome-text"></span>!
        </p>
    </div>


  

    <div class="grid-container">
        <!-- Academic Status Card -->
        <div class="card" id="academic-info">
            <div class="card-header">
                <div class="card-icon academic-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h2 class="card-title">Academic Period</h2>
            </div>
            <div class="card-content">
                <p><strong>Academic Year:</strong>
                    <span id="academic-year">
                        <?php echo $_SESSION['academic']['year'] . ' ' . ordinal_suffix($_SESSION['academic']['semester']); ?> Semester
                    </span>
                </p>
                <div class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $astat[$_SESSION['academic']['status']])); ?>">
                    <?php echo $astat[$_SESSION['academic']['status']]; ?>
                </div>
                <div class="location">
    <p>Evaluation Progress Below <i class="fas fa-arrow-down"></i></p>
</div>
            </div>
        </div>

   

        <!-- Security Info Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon security-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="card-title">Data Security</h2>
            </div>
            <div class="card-content">
                <p>Your privacy is our top priority. All evaluations are:</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Completely confidential</li>
                    <li><i class="fas fa-check-circle"></i> Securely encrypted</li>
                    <li><i class="fas fa-check-circle"></i> Anonymously processed</li>
                </ul>
            </div>
        </div>

        <!-- Survey Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon survey-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h2 class="card-title">Teacher Assessment</h2>
            </div>
            <div class="card-content">
                <p>Help us improve education quality by sharing your valuable feedback.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Enhance teaching methods</li>
                    <li><i class="fas fa-check-circle"></i> Improve classroom experience</li>
                    <li><i class="fas fa-check-circle"></i> Support teacher development</li>
                </ul>
                <button class="evaluate-btn" onclick="window.location.href='index.php?page=evaluate';">
                    Begin Evaluation
                </button>
                <p class="deadline-text">
                    <i class="fas fa-clock"></i> Please complete by the end of the semester
                </p>
            </div>
        </div>
    </div>

           <!-- Survey Progress Card -->



    <div class="card">
        <div class="card-header">
            <div class="card-icon progress-icon">
            <i class="fas fa-chart-line"></i>
            </div>
            <h2 class="card-title-progress">Evaluation Progress</h2>
        </div>
        <div class="card-content">
            <?php
            // Get evaluation progress data
            $progress_query = $conn->query("SELECT 
                (SELECT COUNT(*) 
                FROM restriction_list 
                WHERE academic_id = {$_SESSION['academic']['id']} 
                AND class_id = {$_SESSION['login_class_id']}) as total_surveys,
                
                (SELECT COUNT(*) 
                FROM evaluation_list 
                WHERE academic_id = {$_SESSION['academic']['id']} 
                AND student_id = {$_SESSION['login_id']}) as completed_surveys");
            
            $progress = $progress_query->fetch_assoc();
            $total = $progress['total_surveys'];
            $completed = $progress['completed_surveys'];
            $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
            ?>
            
            <!-- Progress Summary -->
            <div class="progress-summary">
                <div class="progress-stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Evaluations</span>
                        <span class="stat-value"><?php echo $total; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Completed</span>
                        <span class="stat-value"><?php echo $completed; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Remaining</span>
                        <span class="stat-value"><?php echo $total - $completed; ?></span>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-bar-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%;">
                        <span class="progress-text"><?php echo $percentage; ?>%</span>
                    </div>
                </div>
            </div>

            <!-- Completed Evaluations List -->
            <div class="completed-evaluations">
                <h3>Recently Completed Evaluations</h3>
                <div class="evaluations-list">
                    <?php
                    $completed_query = $conn->query("SELECT 
                        e.date_taken,
                        CONCAT(f.firstname, ' ', f.lastname) as faculty_name,
                        s.code as subject_code,
                        s.subject as subject_name
                    FROM evaluation_list e
                    INNER JOIN restriction_list r ON e.restriction_id = r.id
                    INNER JOIN faculty_list f ON r.faculty_id = f.id
                    INNER JOIN subject_list s ON r.subject_id = s.id
                    WHERE e.academic_id = {$_SESSION['academic']['id']} 
                    AND e.student_id = {$_SESSION['login_id']}
                    ORDER BY e.date_taken DESC
                    LIMIT 5");

                    if($completed_query->num_rows > 0):
                        while($row = $completed_query->fetch_assoc()):
                    ?>
                        <div class="evaluation-item">
                            <div class="eval-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="eval-details">
                                <div class="eval-faculty"><?php echo htmlspecialchars($row['faculty_name']); ?></div>
                                <div class="eval-subject">
                                    <?php echo htmlspecialchars($row['subject_code']); ?> - 
                                    <?php echo htmlspecialchars($row['subject_name']); ?>
                                </div>
                                <div class="eval-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('M d, Y h:i A', strtotime($row['date_taken'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="no-evaluations">
                            <i class="fas fa-info-circle"></i>
                            No completed evaluations yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const text = '<?php echo $login_name; ?>';
    const speed = 75;
    let i = 0;

    function typeWriter() {
        if (i < text.length) {
            document.getElementById('welcome-text').innerHTML += text.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }

    window.onload = function() {
        typeWriter();
        updateAcademicInfo();
        updateSurveyProgress();
    };

    function updateAcademicInfo() {
        $.ajax({
            url: 'ajax.php?action=check_academic_year',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response) {
                    const currentYear = $('#academic-year').text().trim();
                    const currentStatus = $('.status-badge').text().trim();
                    
                    const newSemester = ordinalSuffix(response.semester);
                    const newYear = `${response.year} ${newSemester} Semester`;
                    const statusTexts = ["Not Yet Started", "Started", "Closed"];
                    const newStatus = statusTexts[response.status];
                    
                    if (currentYear !== newYear || currentStatus !== newStatus) {
                        const academicCard = $('#academic-info');
                        
                        // Remove existing animation classes
                        academicCard.removeClass('semester-change');
                        
                        // Force reflow to restart animation
                        void academicCard[0].offsetWidth;
                        
                        // Add animation class
                        academicCard.addClass('semester-change');
                        
                        // Update content with a slight delay to sync with animation
                        setTimeout(() => {
                            $('#academic-year').text(newYear);
                            $('.status-badge')
                                .removeClass()
                                .addClass(`status-badge status-${newStatus.toLowerCase().replace(' ', '-')}`)
                                .text(newStatus);
                        }, 2000);
                        
                        // Remove animation class after completion
                        setTimeout(() => {
                            academicCard.removeClass('semester-change');
                        }, 4000);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error checking academic year:", error);
            }
        });
    }

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

    setInterval(updateAcademicInfo, 50000);

    function updateEvaluationProgress() {
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: { action: 'get_student_surveys' },
        dataType: 'json',
        success: function(response) {
            if (response && !response.error) {
                // Update progress bar
                const percentage = response.total_surveys > 0 
                    ? Math.round((response.completed_surveys / response.total_surveys) * 100) 
                    : 0;
                $('.progress-fill').css('width', percentage + '%');
                $('.progress-text').text(percentage + '%');

                // Update stats
                $('.stat-value').eq(0).text(response.total_surveys);
                $('.stat-value').eq(1).text(response.completed_surveys);
                $('.stat-value').eq(2).text(response.total_surveys - response.completed_surveys);

                // Update completed list
                const evaluationsList = $('.evaluations-list');
                evaluationsList.empty();

                if (response.completed_list && response.completed_list.length > 0) {
                    response.completed_list.forEach(survey => {
                        const evaluationItem = `
                            <div class="evaluation-item">
                                <div class="eval-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="eval-details">
                                    <div class="eval-faculty">${survey.faculty}</div>
                                    <div class="eval-subject">
                                        ${survey.code} - ${survey.subject}
                                    </div>
                                    <div class="eval-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        ${survey.date}
                                    </div>
                                </div>
                            </div>
                        `;
                        evaluationsList.append(evaluationItem);
                    });
                } else {
                    evaluationsList.html(`
                        <div class="no-evaluations">
                            <i class="fas fa-info-circle"></i>
                            No completed evaluations yet.
                        </div>
                    `);
                }
            }
        }
    });
}

// Update progress every 5 minutes
setInterval(updateEvaluationProgress, 300000);

// Update progress when an evaluation is submitted
$(document).on('evaluationSubmitted', updateEvaluationProgress);
</script>

</body>
</html>