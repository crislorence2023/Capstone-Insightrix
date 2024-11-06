

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

if($academic_status == 1) { // Only fetch data if status is "Started"
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
    WHERE academic_id ={$_SESSION['academic']['id']} 
    AND class_id = {$_SESSION['login_class_id']} 
    AND r.id NOT IN (SELECT restriction_id FROM evaluation_list WHERE academic_id ={$_SESSION['academic']['id']} AND student_id = {$_SESSION['login_id']} ) ");
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
    <style>

        *{
            margin: 0 !important;
            padding: 0 !important;
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
        .card-header {
            padding: 15px;
            margin-bottom: 10px;
            background-color: white;
        }
        .list-group-item.active {
            background-color: teal;
            border-color: #007bff;
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-size: 15px;
            font-weight: bold;
        }
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        .rating-legend {
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: bold;
            color: #495057;
        }
        .rating-details {
            display: flex;
            gap: 10px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .rating-item {
            display: inline-block;
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
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #fefefe;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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


        @media (max-width: 767.98px) {
            .evaluation-list {
                max-height: none;
            }
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
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card sticky-top">
                        <div class="card-header text-black">
                            <h5 class="mb-0">Evaluation List</h5>
                        </div>
                        <div class="list-group list-group-flush evaluation-list">
                            <?php 
                            if($restriction):
                                $restriction->data_seek(0);
                                while($row = $restriction->fetch_assoc()):
                            ?>
                            <a class="list-group-item list-group-item-action <?php echo $rid == $row['id'] ? 'active' : '' ?>" 
                               href="./index.php?page=evaluate&rid=<?php echo $row['id'] ?>&sid=<?php echo $row['sid'] ?>&fid=<?php echo $row['fid'] ?>">
                                <strong><?php echo ucwords($row['faculty']) ?></strong>
                                <br>
                                <small>(<?php echo $row["code"] ?>) <?php echo $row['subject'] ?></small>
                            </a>
                            <?php 
                                endwhile; 
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <?php if(empty($rid)): ?>
                        <div class="no-evaluation-message">
                            <i class="fas fa-clipboard-list"></i>
                            <h4>No Evaluation Selected</h4>
                            <p>Please select an evaluation from the list on the left to begin the assessment.</p>
                        </div>
                        <?php else: ?>
                        <div class="card-header text-black">
                            <h5>Evaluation Questionnaire</h5>
                            <small>Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?></small>
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
                                    <h6><?php echo $crow['criteria'] ?></h6>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        validateEvaluationAccess();

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

        $('#manage-evaluation').on('submit', function(e) {
            e.preventDefault();

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
                    submitEvaluation();
                }
            });
        });

        function submitEvaluation() {
            let currentRid = <?php echo !empty($rid) ? $rid : 'null' ?>;
            
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
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        validateEvaluationAccess();
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
                                window.location.reload();
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
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while saving the evaluation.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
    </script>
</body>
</html>