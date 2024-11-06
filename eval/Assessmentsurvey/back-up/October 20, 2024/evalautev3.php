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

.sidebar {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.faculty-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.faculty-item {
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
    text-decoration: none;
    display: block;
}

.faculty-item:last-child {
    border-bottom: none;
}

.faculty-item.active {
    background: #0d6efd;
    color: white;
}

.faculty-item:hover:not(.active) {
    background: #f8f9fa;
}

.evaluation-form {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    padding: 24px;
}

.form-header {
    margin-bottom: 24px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 16px;
}

.form-title {
    font-size: 24px;
    color: #212529;
    margin: 0 0 8px 0;
}

.academic-year {
    color: #6c757d;
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
}

.question-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 16px;
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
}

.rating-input:checked + .rating-label {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
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
}

.submit-btn {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    float: right;
}

.submit-btn:hover {
    background: #0b5ed7;
}
</style>

<div class="evaluation-wrapper">
    <div class="row">
        <!-- Faculty Sidebar -->
        <div class="col-md-3">
            <div class="sidebar">
                <div class="faculty-list">
                    <?php 
                    while($row=$restriction->fetch_array()):
                        if(empty($rid)){
                            $rid = $row['id'];
                            $faculty_id = $row['fid'];
                            $subject_id = $row['sid'];
                        }
                    ?>
                    <a class="faculty-item <?php echo isset($rid) && $rid == $row['id'] ? 'active' : '' ?>" 
                       href="./index.php?page=evaluate&rid=<?php echo $row['id'] ?>&sid=<?php echo $row['sid'] ?>&fid=<?php echo $row['fid'] ?>">
                        <?php echo ucwords($row['faculty']).' - ('.$row["code"].') '.$row['subject'] ?>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Evaluation Form -->
        <div class="col-md-9">
            <div class="evaluation-form">
                <div class="form-header">
                    <h1 class="form-title">Faculty Evaluation Form</h1>
                    <p class="academic-year">Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?></p>
                </div>

                <div class="rating-scale">
                    <span>5 - Strongly Agree</span>
                    <span>4 - Agree</span>
                    <span>3 - Uncertain</span>
                    <span>2 - Disagree</span>
                    <span>1 - Strongly Disagree</span>
                </div>

                <form id="manage-evaluation">
                    <input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
                    <input type="hidden" name="faculty_id" value="<?php echo $faculty_id?>">
                    <input type="hidden" name="restriction_id" value="<?php echo $rid ?>">
                    <input type="hidden" name="subject_id" value="<?php echo $subject_id ?>">
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
                                           value="<?php echo $c ?>" 
                                           <?php echo $c == 5 ? "checked" : '' ?>>
                                    <label class="rating-label" for="rate_<?php echo $row['id'].'_'.$c ?>">
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

                    <button type="submit" class="submit-btn">Submit Evaluation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    if('<?php echo $_SESSION['academic']['status'] ?>' == 0){
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>not_started.php")
    } else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
    }
    if(<?php echo empty($rid) ? 1 : 0 ?> == 1)
        uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>done.php")

    $('#manage-evaluation').submit(function(e){
        e.preventDefault();
        start_load()
        
        $.ajax({
            url: 'ajax.php?action=save_evaluation',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp){
                if(resp.status == 1){
                    alert_toast("Evaluation successfully submitted.", "success");
                    setTimeout(function(){
                        location.reload()    
                    }, 1500)
                }
            }
        })
    })
})
</script>