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
$rid='';
$faculty_id='';
$subject_id='';
if(isset($_GET['rid'])) $rid = $_GET['rid'];
if(isset($_GET['fid'])) $faculty_id = $_GET['fid'];
if(isset($_GET['sid'])) $subject_id = $_GET['sid'];
$restriction = $conn->query("SELECT r.id,s.id as sid,f.id as fid,concat(f.firstname,' ',f.lastname) as faculty,s.code,s.subject FROM restriction_list r inner join faculty_list f on f.id = r.faculty_id inner join subject_list s on s.id = r.subject_id where academic_id ={$_SESSION['academic']['id']} and class_id = {$_SESSION['login_class_id']} and r.id not in (SELECT restriction_id from evaluation_list where academic_id ={$_SESSION['academic']['id']} and student_id = {$_SESSION['login_id']} ) ");
?>

<style>
.evaluation-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    background: #f8f9fa;
    padding: 20px;
    min-height: 100vh;
}

.evaluation-form {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 28px;
    transition: box-shadow 0.3s;
}

.evaluation-form:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.form-header {
    margin-bottom: 24px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 16px;
    font-weight: bold;
}

.form-title {
    font-size: 24px;
    color: #212529;
    margin: 0 0 8px 0;
    font-weight: bold;
}

.academic-year {
    color: teal;
    font-size: 14px;
    margin: 0;
}

.rating-scale {
    display: flex;
    justify-content: space-between;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #495057;
}

.criteria-section {
    margin-bottom: 32px;
}

.criteria-title {
    font-size: 18px;
    color: #212529;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
    font-weight: bold;
}

.question-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 16px;
    transition: border-color 0.3s ease;
}

.question-item.error {
    border-color: #dc3545;
    background: #fff8f8;
}

.question-text {
    margin-bottom: 12px;
    color: #212529;
}

.rating-options {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
}

.rating-input {
    display: none;
}

.rating-label {
    display: block;
    text-align: center;
    padding: 8px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    color: #495057;
    transition: all 0.2s ease;
}

.rating-label:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

.rating-input:checked + .rating-label {
    background: teal;
    color: white;
    border-color: teal;
    font-weight: 500;
}

.comment-section {
    margin-bottom: 24px;
}

.comment-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    resize: vertical;
    transition: border-color 0.3s ease;
}

.comment-textarea:focus {
    outline: none;
    border-color: teal;
    box-shadow: 0 0 0 2px rgba(0, 128, 128, 0.1);
}

.error-message {
    color: #dc3545;
    font-size: 12px;
    margin-top: 8px;
    display: none;
}

.error-message.visible {
    display: block;
}

.submit-btn {
    background: teal;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 8px;
    cursor: pointer;
    float: right;
    font-size: 16px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

.submit-btn:hover {
    background: #006666;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 128, 128, 0.2);
}

.submit-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(0, 128, 128, 0.2);
}

.submit-btn:disabled {
    background: #cccccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.submit-btn::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: -100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    transition: 0.5s;
}

.submit-btn:hover::after {
    left: 100%;
}

.submit-btn i {
    margin-right: 8px;
}

.instructor-select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background-color: white;
    font-size: 16px;
    color: #495057;
}

.instructor-select:focus {
    outline: none;
    border-color: teal;
    box-shadow: 0 0 0 2px rgba(0, 128, 128, 0.1);
}

@media (max-width: 768px) {
    .evaluation-wrapper {
        padding: 10px;
    }

    .evaluation-form {
        padding: 20px;
    }

    .form-title {
        font-size: 20px;
    }

    .rating-scale {
        flex-direction: column;
        align-items: flex-start;
    }

    .rating-scale span {
        margin-bottom: 5px;
    }

    .rating-options {
        grid-template-columns: repeat(5, 1fr);
    }

    .rating-label {
        padding: 6px;
        font-size: 14px;
    }

    .submit-btn {
        width: 100%;
        margin-top: 20px;
    }
}
</style>

<div class="evaluation-wrapper">
    <div class="evaluation-form">
        <div class="form-header">
            <h1 class="form-title">Faculty Evaluation Form</h1>
            <p class="academic-year">Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</p>
        </div>

        <select id="instructor-select" class="instructor-select" onchange="loadEvaluation()">
            <option value="">Select an instructor to evaluate</option>
            <?php 
            while($row = $restriction->fetch_array()):
            ?>
            <option value="<?php echo $row['id'] ?>" data-sid="<?php echo $row['sid'] ?>" data-fid="<?php echo $row['fid'] ?>">
                <?php echo ucwords($row['faculty']).' - ('.$row["code"].') '.$row['subject'] ?>
            </option>
            <?php endwhile; ?>
        </select>

        <div id="evaluation-content" style="display: none;">
            <div class="rating-scale">
                <span>5 - Strongly Agree</span>
                <span>4 - Agree</span>
                <span>3 - Uncertain</span>
                <span>2 - Disagree</span>
                <span>1 - Strongly Disagree</span>
            </div>

            <form id="manage-evaluation">
                <input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
                <input type="hidden" name="faculty_id" id="faculty_id">
                <input type="hidden" name="restriction_id" id="restriction_id">
                <input type="hidden" name="subject_id" id="subject_id">
                <input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">

                <?php 
                $criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
                while($crow = $criteria->fetch_assoc()):
                ?>
                <div class="criteria-section">
                    <h2 class="criteria-title"><?php echo $crow['criteria'] ?></h2>
                    <?php 
                    $questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
                    while($row=$questions->fetch_assoc()):
                    ?>
                    <div class="question-item">
                        <div class="question-text"><?php echo $row['question'] ?></div>
                        <input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
                        <div class="rating-options">
                            <?php for($c=1;$c<=5;$c++): ?>
                            <div>
                                <input type="radio" 
                                       class="rating-input" 
                                       name="rate[<?php echo $row['id'] ?>]" 
                                       id="rate_<?php echo $row['id'].'_'.$c ?>" 
                                       value="<?php echo $c ?>">
                                <label class="rating-label" for="rate_<?php echo $row['id'].'_'.$c ?>">
                                    <?php echo $c ?>
                                </label>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <div class="error-message">Please select a rating for this question.</div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endwhile; ?>

                <div class="comment-section">
                    <h2 class="criteria-title">Additional Comments</h2>
                    <textarea name="comment" 
                              class="comment-textarea" 
                              placeholder="Your feedback helps us improve (Optional)"></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Submit Evaluation
                </button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Check academic status
    if('<?php echo $_SESSION['academic']['status'] ?>' == 0){
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>not_started.php")
    } else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
    }
});

function loadEvaluation() {
    var select = document.getElementById('instructor-select');
    var selectedOption = select.options[select.selectedIndex];
    var rid = select.value;
    var sid = selectedOption.getAttribute('data-sid');
    var fid = selectedOption.getAttribute('data-fid');

    if (rid) {
        document.getElementById('faculty_id').value = fid;
        document.getElementById('restriction_id').value = rid;
        document.getElementById('subject_id').value = sid;
        document.getElementById('evaluation-content').style.display = 'block';
        
        // Clear previous ratings and comment
        $('input[type=radio]').prop('checked', false);
        $('textarea[name=comment]').val('');
        $('.question-item').removeClass('error');
        $('.error-message').removeClass('visible');
        
        // Load saved data if available
        loadSavedRatings(rid);
    } else {
        document.getElementById('evaluation-content').style.display = 'none';
    }
}

function loadSavedRatings(rid) {
    const storageKey = `evaluation_${rid}`;
    const savedData = localStorage.getItem(storageKey);
    if (savedData) {
        const data = JSON.parse(savedData);
        
        // Restore ratings
        Object.keys(data.ratings).forEach(questionId => {
            $(`input[name="rate[${questionId}]"][value="${data.ratings[questionId]}"]`).prop('checked', true);
        });
        
        // Restore comment
        $('textarea[name="comment"]').val(data.comment);
    }
}

function saveFormState() {
    const rid = $('#restriction_id').val();
    const storageKey = `evaluation_${rid}`;
    const formData = {
        ratings: {},
        comment: $('textarea[name="comment"]').val()
    };

    // Collect all ratings
    $('input[name^="rate["]:checked').each(function() {
        const questionId = $(this).attr('name').match(/\[(\d+)\]/)[1];
        formData.ratings[questionId] = $(this).val();
    });

    localStorage.setItem(storageKey, JSON.stringify(formData));
}

// Save form state when ratings change
// Save form state when ratings change
$(document).on('change', '.rating-input', function() {
    $(this).closest('.question-item').removeClass('error')
        .find('.error-message').removeClass('visible');
    saveFormState();
});

// Save comment as it's being typed
$(document).on('input', 'textarea[name="comment"]', function() {
    saveFormState();
});

// Form submission
$('#manage-evaluation').submit(function(e){
    e.preventDefault();
    
    // Validate that all questions have been answered
    let valid = true;
    $('.question-item').each(function() {
        const questionId = $(this).find('input[type="hidden"]').val();
        if (!$(`input[name="rate[${questionId}]"]:checked`).length) {
            $(this).addClass('error')
                .find('.error-message').addClass('visible');
            valid = false;
        }
    });

    if (!valid) {
        $('html, body').animate({
            scrollTop: $('.error').first().offset().top - 100
        }, 500);
        alert_toast("Please rate all questions before submitting.", "error");
        return;
    }

    // Disable submit button and show loading state
    const $submitBtn = $(this).find('.submit-btn');
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
    
    start_load();
    
    $.ajax({
        url: 'ajax.php?action=save_evaluation',
        method: 'POST',
        data: $(this).serialize(),
        success: function(resp){
            if(resp.status == 1){
                // Clear stored data after successful submission
                localStorage.removeItem(`evaluation_${$('#restriction_id').val()}`);
                alert_toast("Evaluation successfully submitted.", "success");
                setTimeout(function(){
                    location.reload()    
                }, 1500);
            } else {
                $submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Evaluation');
                alert_toast("An error occurred. Please try again.", "error");
            }
        },
        error: function() {
            $submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Evaluation');
            alert_toast("An error occurred. Please try again.", "error");
        }
    });
});
</script>