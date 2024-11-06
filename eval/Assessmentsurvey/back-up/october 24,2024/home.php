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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            max-height: 100vh;
            overflow: none;
        }

        .welcome-message {
            font-size: 20px;
            color: black;
            font-weight: 600;
        }

        .background {
            border: 1px solid white !important;
        }

        h6 {
            color: teal;
        }

        /* Message Styles */
        .message-container {
            padding: 20px;
            margin-top: 20px;
        
        }

        .message-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
           border: 1px solid teal;
            max-width: 100%;
            margin: 0 auto;
        }

        .message-icon {
            font-size: 2.5rem;
            color: #FFD700;
            margin-bottom: 20px;
        }

        .message-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .message-content {
            text-align: left;
            padding: 0 20px;
        }

        .message-content p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .highlight-text {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px !important;
        }

        .message-content ul {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 25px;
        }

        .message-content ul li {
            color: #555;
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }

        .message-content ul li:before {
            content: "✓";
            color: #4CAF50;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .action-section {
            text-align: center;
            margin-top: 30px;
        }

        .evaluate-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
            font-weight: 500;
        }

        .evaluate-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .deadline-text {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
            font-style: italic;
        }

        /* Academic info update animation */
        @keyframes highlight {
            0% { background-color: transparent; }
            50% { background-color: rgba(76, 175, 80, 0.1); }
            100% { background-color: transparent; }
        }

        .update-highlight {
            animation: highlight 1s ease-in-out;
        }

        @media (max-width: 768px) {
            .message-card {
                padding: 20px;
                margin: 0 10px;
            }
            
            .message-content {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    <p class="welcome-message">
        Welcome <span id="welcome-text"></span>!
    </p>

    <div class="background" style="background-image: url('./logo/ctu-background.png') !important; background-size: cover; background-repeat: no-repeat; background-position: center; width: 100%; min-height: 29vh; border-radius: 10px;">
        <div class="main-container">
            <div class="card-body" style="background-color: rgba(255, 255, 255, 0.5); padding: 20px;">
                <div class="col-md-6 mt-4">
                    <div id="academic-info" class="callout callout-info p-3 rounded">
                        <h5><b>Academic Year: 
                            <span id="academic-year">
                                <?php echo $_SESSION['academic']['year'] . ' ' . ordinal_suffix($_SESSION['academic']['semester']); ?> Semester
                            </span>
                        </b></h5>
                        <h6><b>Evaluation Status: 
                            <span id="academic-status">
                                <?php echo $astat[$_SESSION['academic']['status']]; ?>
                            </span>
                        </b></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Survey Message Container -->
    <div class="message-container">
        <div class="message-card">
            <div class="message-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3>Teacher Evaluation Survey</h3>
            <div class="message-content">
                <p>Your voice matters! Help us improve our educational experience by participating in our teacher evaluation survey.</p>
                <p class="highlight-text">Your feedback helps us:</p>
                <ul>
                    <li>Enhance teaching methods</li>
                    <li>Improve classroom experience</li>
                    <li>Support teacher development</li>
                </ul>
                <div class="action-section">
                    <button class="btn btn-primary evaluate-btn" onclick="window.location.href='index.php?page=evaluate';">
                        Begin Evaluation
                    </button>
                    <p class="deadline-text">Please complete by the end of the semester</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Typing Animation Script -->
    <script>
        const text = '<?php echo $login_name; ?>';
        const speed = 100;
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
            // Initial academic info check
            updateAcademicInfo();
            checkAcademicStatus();
        };

        // Function to update academic year information
        function updateAcademicInfo() {
            $.ajax({
                url: 'ajax.php?action=check_academic_year',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        // Get current values
                        const currentYear = $('#academic-year').text().trim();
                        const currentStatus = $('#academic-status').text().trim();
                        
                        // Create new values
                        const newSemester = ordinalSuffix(response.semester);
                        const newYear = `${response.year} ${newSemester} Semester`;
                        const statusTexts = ["Not Yet Started", "Started", "Closed"];
                        const newStatus = statusTexts[response.status];
                        
                        // Update only if there are changes
                        if (currentYear !== newYear) {
                            $('#academic-year').text(newYear);
                            $('#academic-info').addClass('update-highlight');
                        }
                        
                        if (currentStatus !== newStatus) {
                            $('#academic-status').text(newStatus);
                            $('#academic-info').addClass('update-highlight');
                        }
                        
                        // Remove highlight animation after it completes
                        setTimeout(() => {
                            $('#academic-info').removeClass('update-highlight');
                        }, 1000);
                    }
                    checkAcademicStatus();
                },
                error: function(xhr, status, error) {
                    console.log("Error checking academic year:", error);
                }
            });
        }

        // Helper function for ordinal suffixes
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

        // Check for updates every 30 seconds
        setInterval(updateAcademicInfo, 30000);
    </script>
</body>
</html>