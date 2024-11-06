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
.evaluation-container {
    max-width: 1200px;
    margin: 0 auto;
    font-family: 'Roboto', sans-serif;
}

.form-header {
    background: #fff;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    border-top: 8px solid #4285f4;
}

.form-title {
    font-size: 32px;
    color: #202124;
    margin-bottom: 12px;
}

.form-subtitle {
    color: #5f6368;
    font-size: 14px;
}

.criteria-section {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 24px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}

.criteria-title {
    font-size: 20px;
    color: #202124;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e0e0e0;
}

.question-item {
    padding: 16px;
    border-bottom: 1px solid #e0e0e0;
    transition: background-color 0.3s;
}

.question-item:hover {
    background-color: #f8f9fa;
}

.question-text {
    font-size: 16px;
    color: #202124;
    margin-bottom: 12px;
}

.rating-group {
    display: flex;
    justify-content: space-between;
    max-width: 500px;
    margin: 12px 0;
}

.rating-option {
    text-align: center;
    flex: 1;
}

.rating-label {
    display: block;
    padding: 8px;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s;
}

.rating-label:hover {
    background-color: #e8f0fe;
}

.rating-radio {
    display: none;
}

.rating-radio:checked + .rating-label {
    background-color: #4285f4;
    color: white;
}

.comment-section {
    background: #fff;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}

.comment-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    resize: vertical;
    min-height: 100px;
    margin-top: 8px;
}

.submit-button {
    background-color: #4285f4;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    float: right;
    transition: background-color 0.3s;
}

.submit-button:hover {
    background-color: #3367d6;
}

.rating-legend {
    display: flex;
    justify-content: space-between;
    margin-bottom: 24px;
    padding: 12px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.legend-item {
    text-align: center;
    font-size: 14px;
    color: #5f6368;
}
</style>

<div class="col-lg-12">
    <div class="row">
        <!-- Keep the existing faculty list sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <?php 
                while($row=$restriction->fetch_array()):
                    if(empty($rid)){
                        $rid = $row['id'];
                        $faculty_id = $row['fid'];
                        $subject_id = $row['sid'];
                    }
                ?>
                <a class="list-group-item list-group-item-action <?php echo isset($rid) && $rid == $row['id'] ? 'active' : '' ?>" 
                   href="./index.php?page=evaluate&rid=<?php echo $row['id'] ?>&sid=<?php echo $row['sid'] ?>&fid=<?php echo $row['fid'] ?>">
                    <?php echo ucwords($row['faculty']).' - ('.$row["code"].') '.$row['subject'] ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Improved evaluation form -->
        <div class="col-md-9">
            <div class="evaluation-container">
                <div class="form-header">
                    <h1 class="form-title">Faculty Evaluation Form</h1>
                    <p class="form-subtitle">Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?></p>
                </div>

                <div class="rating-legend">
                    <div class="legend-item">5 - Strongly Agree</div>
                    <div class="legend-item">4 - Agree</div>
                    <div class="legend-item">3 - Uncertain</div>
                    <div class="legend-item">2 - Disagree</div>
                    <div class="legend-item">1 - Strongly Disagree</div>
                </div>

                <form id="manage-evaluation">
                    <input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
                    <input type="hidden" name="faculty_id" value="<?php echo $faculty_id?>">
                    <input type="hidden" name="restriction_id" value="<?php echo $rid ?>">
                    <input type="hidden" name="subject_id" value="<?php echo $subject_id ?>">
                    <input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">

                    <?php 
                    $q_arr = array();
                    $criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
                    while($crow = $criteria->fetch_assoc()):
                    ?>
                    <div class="criteria-section">
                        <h2 class="criteria-title"><?php echo $crow['criteria'] ?></h2>
                        <?php 
                        $questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
                        while($row=$questions->fetch_assoc()):
                        $q_arr[$row['id']] = $row;
                        ?>
                        <div class="question-item">
                            <div class="question-text"><?php echo $row['question'] ?></div>
                            <input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
                            <div class="rating-group">
                                <?php for($c=1;$c<=5;$c++): ?>
                                <div class="rating-option">
                                    <input type="radio" 
                                           class="rating-radio" 
                                           name="rate[<?php echo $row['id'] ?>]" 
                                           id="qradio<?php echo $row['id'].'_'.$c ?>" 
                                           value="<?php echo $c ?>" 
                                           <?php echo $c == 5 ? "checked" : '' ?>>
                                    <label class="rating-label" for="qradio<?php echo $row['id'].'_'.$c ?>">
                                        <?php echo $c ?>
                                    </label>
                                </div>
                                <?php endfor; ?>
                            </div>
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

                    <button type="submit" class="submit-button">Submit Evaluation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Existing modal checks
    if('<?php echo $_SESSION['academic']['status'] ?>' == 0){
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>not_started.php")
    } else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
    }
    if(<?php echo empty($rid) ? 1 : 0 ?> == 1)
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>done.php")

    // Smooth scroll to questions
    $('.criteria-title').click(function() {
        $(this).next('.question-item').find('.question-text').focus();
    });

    // Add animation to rating selection
    $('.rating-label').click(function() {
        $(this).closest('.rating-group').find('.rating-label').removeClass('selected');
        $(this).addClass('selected');
    });

    // Form submission with improved feedback
    $('#manage-evaluation').submit(function(e){
        e.preventDefault();
        start_load()
        
        $.ajax({
            url: 'ajax.php?action=save_evaluation',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if(resp.status === 'success'){
                    alert_toast("Thank you! Your evaluation has been submitted successfully.", "success");
                    setTimeout(function(){
                        location.reload()    
                    }, 1750)
                } else {
                    alert_toast("Error: " + resp.message, "error");
                }
            },
            error: function(xhr, status, error) {
                alert_toast("An error occurred while saving the evaluation.", "error");
            },
            complete: function() {
                end_load()
            }
        });
    });
});
</script>