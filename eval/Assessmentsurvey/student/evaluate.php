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

// Check academic status first
$academic_status = $_SESSION['academic']['status']; // 0 = Not Yet Started, 1 = Started, 2 = Closed

// Only proceed with fetching evaluation data if status is "Started" (1)
$rid = '';
$faculty_id = '';
$subject_id = '';
$restriction = null;
$has_pending_evaluations = false;

if($academic_status == 1) {
    if(isset($_GET['rid']))
        $rid = $_GET['rid'];
    if(isset($_GET['fid']))
        $faculty_id = $_GET['fid'];
    if(isset($_GET['sid']))
        $subject_id = $_GET['sid'];

    $restriction = $conn->query("SELECT r.id,s.id as sid,f.id as fid,concat(f.firstname,' ',f.lastname) as faculty,s.code,s.subject 
    FROM restriction_list r 
    INNER JOIN faculty_list f on f.id = r.faculty_id 
    INNER JOIN subject_list s on s.id = r.subject_id 
    WHERE academic_id = {$_SESSION['academic']['id']} 
    AND class_id = {$_SESSION['login_class_id']} 
    AND r.id NOT IN (
        SELECT restriction_id 
        FROM evaluation_list 
        WHERE academic_id = {$_SESSION['academic']['id']} 
        AND student_id = {$_SESSION['login_id']}
    ) 
    ORDER BY f.lastname ASC, f.firstname ASC");

    // Check if there are any pending evaluations
    $has_pending_evaluations = $restriction && $restriction->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.0.3/src/bold/style.min.css" rel="stylesheet">
    <style>


*{
    margin: 0;
    padding: 0;
}
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            
        }

        
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 10px 10px 10px 10px;
            border-radius: 15px;
            
        }
        .cardtitle {
           
            padding: 10px 10px 10px 10px;
            border-top-right-radius: 15px !important;
            border-top-left-radius: 15px !important;
            
        }
        .card-header {
            padding: 15px;
            
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important; 
            overflow: hidden; /* Ensure the background image doesn't spill out */
            position: relative;
           
        }
        
       

        .card-header .opacity-10 {
            opacity: 0.1; /* Makes the background image very subtle */
        }

        .card-header .position-relative {
            z-index: 1; /* Ensures text stays on top */
        }
      


        .list-group-item.active:hover {
          
    background-color: white;
        }

        .btn-primary {
            background-color: teal !important;  /* Changed to teal to match theme */
            border-color: teal !important;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 40px;  /* Increased padding for better presence */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);  /* Added subtle shadow */
            transition: all 0.3s ease;  /* Smooth transition for hover effects */
        }

        .btn-primary:hover {
            background-color: #006d6d !important;  /* Darker teal on hover */
            border-color: #006d6d !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);  /* Larger shadow on hover */
            transform: translateY(-1px);  /* Slight lift effect on hover */
        }
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
            width: 10px;
        }
        .rating-legend {
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: bold;
            color: #495057;
        }
        .rating-details {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            margin-bottom: 25px;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            justify-content: center;
        }
        .rating-item {
            display: inline-block;
            padding: 5px 10px;
            text-align: center;
        }
        .header-text {
            color: teal;
            font-weight: bold;
            font-size: 25px;
        }
        .criteria-header {
            background-color: white;
            color: black;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 20px;
        }
        .no-evaluation-message {
            text-align: center;
            padding: 50px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .no-evaluation-message i {
            font-size: 48px;
            color: teal;
            margin-bottom: 20px;
        }
        .no-evaluation-message h4 {
            color: #333;
            margin-bottom: 15px;
        }
        .no-evaluation-message p {
            color: #666;
            font-size: 16px;
        }
        .question-container {
            margin-bottom: 15px;
        }
        .criteria-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 1.5rem !important;
            transition: background-color 0.2s ease;
        }


        /* Style for error state */
        .mb-4.has-error {
            border: 2px solid #dc3545;
            background-color: #fff8f8;
        }

		.form-label {
    font-weight: normal !important; /* This ensures the question text is not bold */
}

		.form-check-inline {
			margin-right: 3rem !important; /* Reduce the space between the radio buttons */
			margin-top: 1rem; /* Add margin to the top */
			margin-bottom: 1rem; /* Add margin to the bottom */
		}

		.d-flex{
			margin-right: 0.5rem !important;
			justify-content: center;
		}


        .form-check-input[type=radio] {
    border-radius: 50%;
    width: 20px;
    height: 20px;
        }



        .mobile-select-wrapper {
    display: none;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Mobile Faculty Select Styles */
.mobile-faculty-select {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background-color: #fff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.mobile-faculty-select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Loading Spinner */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.formheader {
    font-weight: 700;
    font-size: 2rem;
    letter-spacing: 0.5px;
    text-align: center;
    color: #1a5f7a;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-family: 'Montserrat', sans-serif;
    position: relative;
    padding-bottom: 15px;
    margin-bottom: 5rem;
}

.formheader:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background-color: #1a5f7a;
    border-radius: 2px;
}

.academic-year {
    color: #505050;
    text-align: center;
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    margin-top: 15px;
}

.card-header {
    background: linear-gradient(to right, #ffffff, #f8f9fa);
    border-bottom: none;
    padding: 2rem 1.5rem;
}

.background-cover {
    position: relative;
    overflow: hidden;
    border-radius: 15px 15px 0 0;
}

.background-cover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%);
    z-index: 1;
}

.position-relative {
    position: relative;
    z-index: 2;
}

.criteria-title{
    font-weight: bold;
    font-size: 1rem;
    
    
    
}




@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}





        @media (max-width: 767.98px) {
    /* Rating Legend Mobile Layout */
    .rating-details {
        flex-direction: column;
        gap: 8px;
        padding: 15px;
    }

    .rating-item {
        display: block;
        width: 100%;
        padding: 8px 10px;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
        margin: 0;
    }

    .rating-item:last-child {
        border-bottom: none;
    }

    /* Radio Button Mobile Layout */
    .d-flex {
        flex-direction: column;
        align-items: flex-start;
        margin-left: 1rem;
    }
    
    .form-check-inline {
        margin-right: 0 !important;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }
    
    .form-check-input[type=radio] {
        margin-right: 0.5rem;
    }
    
    .form-check-label {
        margin-left: 0.5rem;
    }

    /* Faculty List Mobile Styles */
    .col-md-3 {
        margin-bottom: 1rem;
    }

    .evaluation-list {
        display: none !important; /* Force hide the original list */
    }

    .mobile-select-wrapper {
        display: block !important; /* Force show the wrapper */
    }

    .mobile-faculty-select {
        display: block !important; /* Force show the select element */
    }

    .card.sticky-top {
        position: relative !important;
    }

    /* Add these new styles for mobile header */
    .formheader {
        font-size: 1.5rem;  /* Smaller font size for mobile */
        padding-bottom: 10px;
    }

    .academic-year {
        font-size: 0.9rem;  /* Smaller font size for academic year text */
    }

    .formheader:after {
        width: 60px;  /* Smaller underline width */
        height: 2px;  /* Slightly thinner line */
    }
}

/* Hide mobile select on desktop */
.mobile-faculty-select {
    display: none;
}
.teal-header {
    background-color: #009688; /* Teal color */
    height: 8px; /* Adjust height to match the thickness of the teal bar */
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.list-group-item {
    display: flex !important;
    align-items: center !important;
    padding: 12px 16px !important;
    gap: 12px;
    text-decoration: none !important;
    border: none !important;
    margin-bottom: 0 !important;
}

.list-group-item.active {
    border-right: 3px solid teal !important;
    background-color: white;
    color: teal;
    font-weight: bold;
    margin-bottom: 0 !important;
}

.list-group-item.active strong {
    color: teal;  /* Add this to make instructor name teal when active */
}

.initial-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #f0f2f5;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 500;
    flex-shrink: 0;
    letter-spacing: 0.5px;  /* Added letter spacing for initial */
}

.faculty-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    width: 100%;
}

.faculty-info strong,
.faculty-info small {
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    width: 100%;
    line-height: 1.2;
}

/* Adjust padding to accommodate wrapped text */
.list-group-item {
    padding: 12px 16px !important;
    min-height: 72px;
}

/* Keep responsive font sizes */
@media (max-width: 1400px) {
    .faculty-info strong {
        font-size: 14px;
    }
    
    .faculty-info small {
        font-size: 14px;
    }
}

@media (max-width: 1200px) {
    .faculty-info strong {
        font-size: 13px;
    }
    
    .faculty-info small {
        font-size: 13px;
    }
}

.faculty-info strong {
    font-size: 16px;
    line-height: 1.3;
    letter-spacing: 1px;
    color: #333;  /* Dark color for faculty name */
}

.faculty-info small {
    font-size: 16px;  /* Same size as faculty name */
    line-height: 1.3;
    color: #666;      /* Lighter color for subject info - creates hierarchy */
    letter-spacing: 0.6px;
}

.list-group-item.active .initial-circle {
    background-color: #e8f0f1;
    color: teal;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

@media (min-width: 1200px) {
    .initial-circle {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .faculty-info strong {
        font-size: 15px;
        letter-spacing: 0.25px;
    }
    
    .faculty-info small {
        font-size: 15px;
        color: #666;  /* Maintaining lighter color for larger screens */
        letter-spacing: 0.15px;
    }
}

.card-header.text-muted.text-center {
    border-bottom: 1px solid #dee2e6 !important;
    padding-bottom: 15px !important;
    margin-bottom: 10px !important;
}

.list-group.list-group-flush.evaluation-list {
    max-height: calc(100vh - 200px); /* Adjust height to leave space for header */
    overflow-y: auto;
    scrollbar-width: thin; /* For Firefox */
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent; /* For Firefox */
}

/* Webkit scrollbar styling (Chrome, Safari, newer Edge) */
.list-group.list-group-flush.evaluation-list::-webkit-scrollbar {
    width: 6px;
}

.list-group.list-group-flush.evaluation-list::-webkit-scrollbar-track {
    background: transparent;
}

.list-group.list-group-flush.evaluation-list::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

.list-group.list-group-flush.evaluation-list::-webkit-scrollbar-thumb:hover {
    background-color: rgba(0, 0, 0, 0.3);
}

/* Ensure the sticky behavior works properly with scrolling */
.card.sticky-top {
    top: 1rem;
    z-index: 1020;
}

    </style>
</head>
<body>
<div class="container-fluid py-4">
    <?php if($academic_status != 1): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas <?php echo ($academic_status == 0) ? 'fa-clock' : 'fa-calendar-times'; ?> fa-4x mb-4"></i>
                        <h3 class="mb-4">
                            <?php echo ($academic_status == 0) ? 'Evaluation Period Not Yet Started' : 'Evaluation Period Closed'; ?>
                        </h3>
                        <p class="lead mb-4">
                            <?php if($academic_status == 0): ?>
                                The evaluation period has not begun yet. Please check back later when the evaluation period starts.
                            <?php else: ?>
                                The evaluation period has ended. Thank you for your participation.
                            <?php endif; ?>
                        </p>
                        <div class="text-muted">
                            Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php if(!$has_pending_evaluations): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-check-circle fa-4x mb-4" style="color: #28a745;"></i>
                            <h3 class="mb-4">All Evaluations Completed</h3>
                            <p class="lead mb-4">
                                Thank you for completing all your evaluations for this semester.
                            </p>
                            <div class="text-muted">
                                Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card sticky-top">
                        <div class="card-header text-muted text-center" style="background: none;">
                            <h5 class="">Evaluation List</h5>
                        </div>
                        <div class="list-group list-group-flush evaluation-list">
                            <?php 
                            while($row = $restriction->fetch_assoc()):
                                $firstInitial = substr(ucwords($row['faculty']), 0, 1);
                            ?>
                            <a class="list-group-item list-group-item-action <?php echo $rid == $row['id'] ? 'active' : '' ?>" 
                               href="./index.php?page=evaluate&rid=<?php echo $row['id'] ?>&sid=<?php echo $row['sid'] ?>&fid=<?php echo $row['fid'] ?>">
                                <div class="initial-circle"><?php echo $firstInitial ?></div>
                                <div class="faculty-info">
                                    <strong><?php echo ucwords($row['faculty']) ?></strong>
                                    <small><?php echo $row["code"] ?></small>
                                    <small><?php echo $row['subject'] ?></small>
                                </div>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <?php if(empty($rid)): ?>
                            <div class="no-evaluation-message">
                                <i class="ph ph-clipboard-text ph-bold"></i>
                                <h4>No Evaluation Selected</h4>
                                <p>Please select an evaluation from the list on the left to begin the assessment.</p>
                            </div>
                        <?php else: ?>
                            <div class="cardtitle">
    <div class="card-header bg-body position-relative background-cover">
        <div class="position-absolute top-0 end-0 opacity-10">
            <img src="./logo/ctu-building3.png" alt="Background Logo" />
        </div>
        <div class="position-relative">
            <h4 class="formheader mb-2">Evaluation Questionnaire</h4>
            <p class="academic-year mb-0"><strong>Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])). ' Semester' ?></strong></p>
        </div>
    </div>
</div>
                            <div class="card-body">
                                <div class="rating-legend">Rating Legend:</div>
                                <div class="rating-details">
                                    <div class="rating-item">5 = Strongly Agree</div>
                                    <div class="rating-item">4 = Agree</div>
                                    <div class="rating-item">3 = Uncertain</div>
                                    <div class="rating-item">2 = Disagree</div>
                                    <div class="rating-item">1 = Strongly Disagree</div>
                                </div>
                                <form id="manage-evaluation">
                                    <input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
                                    <input type="hidden" name="faculty_id" value="<?php echo $faculty_id ?>">
                                    <input type="hidden" name="restriction_id" value="<?php echo $rid ?>">
                                    <input type="hidden" name="subject_id" value="<?php echo $subject_id ?>">
                                    <input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">

                                    <?php 
                                    $question_num = 1;
                                    $criteria = $conn->query("SELECT * FROM criteria_list WHERE id IN (SELECT criteria_id FROM question_list WHERE academic_id = {$_SESSION['academic']['id']}) ORDER BY ABS(order_by) ASC");
                                    while($crow = $criteria->fetch_assoc()):
                                    ?>
                                    <div class="criteria-container">
                                        <h6 class="criteria-title mb-4"><?php echo $crow['criteria'] ?></h6>
                                        <hr style="color: teal;">
                                        <?php 
                                        $questions = $conn->query("SELECT * FROM question_list WHERE criteria_id = {$crow['id']} AND academic_id = {$_SESSION['academic']['id']} ORDER BY ABS(order_by) ASC");
                                        while($row = $questions->fetch_assoc()):
                                        ?>
                                        <div class="mb-4">
                                            <label class="form-label"><?php echo $question_num . ". " . $row['question'] ?></label>
                                            <input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
                                            <div class="d-flex">
                                                <?php for($c=1; $c<=5; $c++): ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rate[<?php echo $row['id'] ?>]" id="qradio<?php echo $row['id'].'_'.$c ?>" value="<?php echo $c ?>" required>
                                                    <label class="form-check-label" for="qradio<?php echo $row['id'].'_'.$c ?>"><?php echo $c ?></label>
                                                </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <?php 
                                        $question_num++;
                                        endwhile; 
                                        ?>
                                    </div>
                                    <?php endwhile; ?>

                                    <div class="form-group mt-4">
                                        <label for="comment">Additional Comments (Optional)</label>
                                        <textarea name="comment" id="comment" rows="4" class="form-control"></textarea>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">Submit Evaluation</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
   $(document).ready(function() {
    // Create mobile faculty dropdown
    function createMobileFacultyDropdown() {
        console.log('Creating mobile dropdown...');
        if ($('.evaluation-list').length && !$('.mobile-faculty-select').length) {
            // Create select element with Bootstrap styling
            let select = $('<select>', {
                class: 'form-select mobile-faculty-select',
                'aria-label': 'Select Faculty'
            });

            // Create and add the wrapper div
            let wrapper = $('<div>', {
                class: 'mobile-select-wrapper mb-3'
            });

            // Add descriptive label
            let label = $('<label>', {
                class: 'form-label fw-bold mb-2',
                text: 'Select Faculty and Subject'
            });

            // Add default option
            select.append($('<option>', {
                value: '',
                text: 'Choose a faculty member...',
                selected: true,
                disabled: true
            }));

            // Convert list items to options with better formatting
            $('.evaluation-list .list-group-item-action').each(function() {
                let href = $(this).attr('href');
                let facultyName = $(this).find('strong').text().trim();
                let subjectCode = $(this).find('small:first').text().trim();
                let subjectName = $(this).find('small:last').text().trim();
                let isSelected = $(this).hasClass('active');
                
                // Format the option text with line breaks
                let optionText = `${facultyName}\n${subjectCode} - ${subjectName}`;
                
                let option = $('<option>', {
                    value: href,
                    text: optionText,
                    selected: isSelected
                });
                
                select.append(option);
            });

            // Assemble the wrapper with label and select
            wrapper.append(label).append(select);

            // Insert before the original list
            wrapper.insertBefore('.evaluation-list');

            // Handle change event with loading state
            select.on('change', function() {
                let selectedUrl = $(this).val();
                if (selectedUrl) {
                    // Show loading state
                    $(this).prop('disabled', true);
                    $('<div class="loading-spinner ms-2"></div>').insertAfter(this);
                    
                    // Navigate to selected faculty
                    window.location.href = selectedUrl;
                }
            });

            // Handle responsive behavior
            function handleResponsiveDisplay() {
                if (window.innerWidth <= 767.98) {
                    $('.evaluation-list').addClass('mobile-hidden');
                    wrapper.show();
                } else {
                    $('.evaluation-list').removeClass('mobile-hidden');
                    wrapper.hide();
                }
            }

            // Initial check and event listener for resize
            handleResponsiveDisplay();
            $(window).on('resize', handleResponsiveDisplay);
        }
    }

    // Validate evaluation access
    function validateEvaluationAccess() {
        $.ajax({
            url: 'ajax.php?action=check_academic_year',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response) {
                    const statusTexts = ["Not Yet Started", "Started", "Closed"];
                    const currentStatus = statusTexts[response.status];
                    
                    if (currentStatus !== 'Started') {
                        const message = currentStatus === 'Closed' 
                            ? 'The evaluation period has ended.'
                            : 'The evaluation period has not begun yet.';
                            
                        Swal.fire({
                            title: `Evaluation ${currentStatus}`,
                            text: message,
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php';
                            }
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error checking academic status:", error);
                Swal.fire({
                    title: 'Error',
                    text: 'Unable to verify evaluation access. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            }
        });
    }

    // Get next evaluation
    function getNextEvaluation(currentRid) {
        let evaluationLinks = $('.evaluation-list .list-group-item-action');
        let currentIndex = -1;
        
        evaluationLinks.each(function(index) {
            let href = $(this).attr('href');
            if (href.includes('rid=' + currentRid)) {
                currentIndex = index;
                return false;
            }
        });

        if (currentIndex < evaluationLinks.length - 1) {
            return evaluationLinks.eq(currentIndex + 1).attr('href');
        }
        return null;
    }

    // Submit evaluation function
    function submitEvaluation() {
        let currentRid = $('input[name="restriction_id"]').val();
        
        Swal.fire({
            title: 'Submitting...',
            text: 'Please wait while we save your evaluation.',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'ajax.php?action=save_evaluation',
            method: 'POST',
            data: $('#manage-evaluation').serialize(),
            success: function(response) {
                // Parse response if it's a string
                let resp = response;
                try {
                    if (typeof response === 'string') {
                        resp = JSON.parse(response);
                    }
                } catch (e) {
                    console.log("Response parsing error:", e);
                }

                // Check if submission was successful (1 indicates success)
                if (resp == 1 || resp.status == 'success') {
                    $(document).trigger('evaluationSubmitted');
                    
                    let nextEvaluationLink = getNextEvaluation(currentRid);

                    if (nextEvaluationLink) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Evaluation saved successfully. Proceeding to next evaluation...',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = nextEvaluationLink;
                        });
                    } else {
                        Swal.fire({
                            title: 'All Done!',
                            text: 'You have completed all available evaluations.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'index.php?page=evaluate';
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: resp.message || 'An error occurred while saving the evaluation.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", status, error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while saving the evaluation. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Function to save form data to localStorage
    function saveFormData() {
        const formData = {};
        $('#manage-evaluation input[type="radio"]:checked').each(function() {
            formData[$(this).attr('name')] = $(this).val();
        });
        
        // Save comment as well
        formData['comment'] = $('#comment').val();
        
        // Save to localStorage with a unique key based on the evaluation ID
        const rid = $('input[name="restriction_id"]').val();
        localStorage.setItem(`evaluation_${rid}`, JSON.stringify(formData));
    }

    // Function to load saved form data
    function loadFormData() {
        const rid = $('input[name="restriction_id"]').val();
        const savedData = localStorage.getItem(`evaluation_${rid}`);
        
        if (savedData) {
            const formData = JSON.parse(savedData);
            
            // Restore radio button selections
            Object.entries(formData).forEach(([key, value]) => {
                if (key === 'comment') {
                    $('#comment').val(value);
                } else {
                    $(`input[name="${key}"][value="${value}"]`).prop('checked', true);
                }
            });
        }
    }

    // Save form data when radio buttons change or comment is updated
    $(document).on('change', 'input[type="radio"]', saveFormData);
    $(document).on('input', '#comment', saveFormData);

    // Clear saved data after successful submission
    $(document).on('evaluationSubmitted', function() {
        const rid = $('input[name="restriction_id"]').val();
        localStorage.removeItem(`evaluation_${rid}`);
    });

    // Load saved data when page loads
    loadFormData();

    // Form submission handler
    $('#manage-evaluation').on('submit', function(e) {
        e.preventDefault();
        let isValid = true;
        
        // Check all radio button groups
        $(this).find('input[type="radio"]').each(function() {
            let name = $(this).attr('name');
            if (!$('input[name="' + name + '"]:checked').length) {
                isValid = false;
                // Find the closest question container and add error styling
                $(this).closest('.mb-4').addClass('has-error');
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Error!',
                text: 'Please answer all questions before submitting.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to change your responses after submission.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear saved data before submitting
                const rid = $('input[name="restriction_id"]').val();
                localStorage.removeItem(`evaluation_${rid}`);
                submitEvaluation();
            }
        });
    });

    // Initialize
    createMobileFacultyDropdown();
    validateEvaluationAccess();

    // Clear error styling on radio button selection
    $(document).on('change', 'input[type="radio"]', function() {
        $(this).closest('.mb-4').removeClass('has-error');
    });
});
    </script>
</body>
</html>