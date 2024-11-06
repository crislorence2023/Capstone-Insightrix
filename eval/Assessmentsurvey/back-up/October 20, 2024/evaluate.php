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
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

.evaluation-container {
    max-width: 1200px;
    margin: 0 auto;
    font-family: 'Poppins', sans-serif;
}

.form-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 24px;
    color: white;
    position: relative;
    overflow: hidden;
}

.form-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(100px, -150px);
}

.form-title {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 12px;
    position: relative;
}

.form-subtitle {
    font-size: 16px;
    opacity: 0.9;
    position: relative;
}

.criteria-section {
    background: white;
    border-radius: 16px;
    margin-bottom: 24px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.3s ease;
}

.criteria-section:hover {
    transform: translateY(-2px);
}

.criteria-title {
    font-size: 22px;
    color: #4f46e5;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
    font-weight: 600;
}

.question-item {
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 16px;
    background: #f9fafb;
    transition: all 0.3s ease;
}

.question-item:hover {
    background: #f3f4f6;
    transform: scale(1.01);
}

.question-text {
    font-size: 16px;
    color: #374151;
    margin-bottom: 16px;
    line-height: 1.5;
}

.rating-group {
    display: flex;
    gap: 12px;
    justify-content: space-between;
    max-width: 600px;
    margin: 0 auto;
}

.rating-option {
    position: relative;
    flex: 1;
}

.rating-radio {
    display: none;
}

.rating-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
    cursor: pointer;
    border-radius: 12px;
    background: white;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
}

.rating-label:hover {
    border-color: #818cf8;
    background: #eef2ff;
}

.rating-value {
    font-size: 18px;
    font-weight: 600;
    color: #4f46e5;
    margin-bottom: 4px;
}

.rating-text {
    font-size: 12px;
    color: #6b7280;
    text-align: center;
}

.rating-radio:checked + .rating-label {
    background: #4f46e5;
    border-color: #4f46e5;
}

.rating-radio:checked + .rating-label .rating-value,
.rating-radio:checked + .rating-label .rating-text {
    color: white;
}

.rating-legend {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.legend-title {
    font-size: 18px;
    color: #4f46e5;
    margin-bottom: 16px;
    font-weight: 600;
}

.legend-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.legend-number {
    width: 24px;
    height: 24px;
    background: #4f46e5;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.comment-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.comment-textarea {
    width: 100%;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    resize: vertical;
    min-height: 120px;
    margin-top: 12px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
}

.comment-textarea:focus {
    border-color: #4f46e5;
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.submit-button {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 16px 32px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    float: left;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}

.submit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: #e5e7eb;
    z-index: 1000;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    width: 0%;
    transition: width 0.3s ease;
}

/* Animation classes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease forwards;
}

.completion-status {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: white;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 1000;
    font-size: 14px;
    color: #4f46e5;
    border: 2px solid #e5e7eb;
}

.completion-status.complete {
    background: #4f46e5;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .rating-group {
        flex-direction: column;
        gap: 8px;
    }

    .rating-label {
        flex-direction: row;
        justify-content: space-between;
        padding: 12px 16px;
    }

    .form-header {
        padding: 24px;
    }

    .form-title {
        font-size: 24px;
    }
}
</style>

<div class="progress-bar">
    <div class="progress-fill" id="progressFill"></div>
</div>

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
                <div class="form-header animate-fade-in">
                    <h1 class="form-title">Faculty Evaluation Form</h1>
                    <p class="form-subtitle">Academic Year: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?></p>
                </div>

                <div class="rating-legend animate-fade-in">
                    <h3 class="legend-title">Rating Guide</h3>
                    <div class="legend-grid">
                        <div class="legend-item">
                            <span class="legend-number">5</span>
                            <span>Strongly Agree</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-number">4</span>
                            <span>Agree</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-number">3</span>
                            <span>Uncertain</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-number">2</span>
                            <span>Disagree</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-number">1</span>
                            <span>Strongly Disagree</span>
                        </div>
                    </div>
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
                    <div class="criteria-section animate-fade-in">
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
                                           value="<?php echo $c ?>">
                                    <label class="rating-label" for="qradio<?php echo $row['id'].'_'.$c ?>">
                                        <span class="rating-value"><?php echo $c ?></span>
                                        <span class="rating-text">
                                            <?php 
                                            switch($c) {
                                                case 5: echo "Strongly Agree"; break;
												case 4: echo "Agree"; break;
                                                case 3: echo "Uncertain"; break;
                                                case 2: echo "Disagree"; break;
                                                case 1: echo "Strongly Disagree"; break;
                                            }
                                            ?>
                                        </span>
                                    </label>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php endwhile; ?>

                    <div class="comment-section animate-fade-in">
                        <h2 class="criteria-title">Share Your Thoughts</h2>
                        <textarea name="comment" 
                                  class="comment-textarea" 
                                  placeholder="Your feedback helps us improve. Feel free to share any additional comments or suggestions..."></textarea>
                    </div>

                    <button type="submit" class="submit-button">Submit Evaluation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="completion-status" id="completionStatus">
    Completed: <span id="completedQuestions">0</span>/<span id="totalQuestions">0</span>
</div>

<!-- Previous HTML and CSS code remains the same -->

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

    // Function to save ratings to localStorage
    function saveRatings() {
        const ratings = {};
        $('input[type="radio"]:checked').each(function() {
            const questionId = $(this).attr('name').replace('rate[', '').replace(']', '');
            ratings[questionId] = $(this).val();
        });
        localStorage.setItem('evaluationRatings_<?php echo $rid ?>', JSON.stringify(ratings));
        
        // Also save comment if exists
        const comment = $('textarea[name="comment"]').val();
        if (comment) {
            localStorage.setItem('evaluationComment_<?php echo $rid ?>', comment);
        }
    }

    // Function to load ratings from localStorage
    function loadRatings() {
        const savedRatings = localStorage.getItem('evaluationRatings_<?php echo $rid ?>');
        if (savedRatings) {
            const ratings = JSON.parse(savedRatings);
            Object.keys(ratings).forEach(questionId => {
                $(`input[name="rate[${questionId}]"][value="${ratings[questionId]}"]`).prop('checked', true);
            });
        }
        
        // Load saved comment if exists
        const savedComment = localStorage.getItem('evaluationComment_<?php echo $rid ?>');
        if (savedComment) {
            $('textarea[name="comment"]').val(savedComment);
        }
    }

    // Progress tracking
    const totalQuestions = $('.question-item').length;
    $('#totalQuestions').text(totalQuestions);
    
    function updateProgress() {
        const answeredQuestions = $('input[type="radio"]:checked').length;
        const percentage = (answeredQuestions / totalQuestions) * 100;
        
        $('#progressFill').css('width', percentage + '%');
        $('#completedQuestions').text(answeredQuestions);
        
        if (answeredQuestions === totalQuestions) {
            $('#completionStatus').addClass('complete');
        } else {
            $('#completionStatus').removeClass('complete');
        }
    }

    // Smooth scroll to sections
    $('.criteria-title').click(function() {
        const section = $(this).closest('.criteria-section');
        $('html, body').animate({
            scrollTop: section.offset().top - 100
        }, 500);
    });

    // Animation on scroll
    function animateOnScroll() {
        $('.animate-fade-in').each(function() {
            const elementTop = $(this).offset().top;
            const elementBottom = elementTop + $(this).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();

            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).css('opacity', '1');
                $(this).css('transform', 'translateY(0)');
            }
        });
    }

    // Rating selection handling
    $('.rating-radio').change(function() {
        updateProgress();
        saveRatings(); // Save ratings when changed
        
        // Animate the selection
        const label = $(this).next('.rating-label');
        label.addClass('selected');
        setTimeout(() => label.removeClass('selected'), 300);
    });

    // Save comment when typing
    $('textarea[name="comment"]').on('input', function() {
        saveRatings();
    });

    // Enhanced form submission
    $('#manage-evaluation').submit(function(e){
        e.preventDefault();
        
        // Check if all questions are answered
        const unansweredCount = $('.question-item').find('input[type="radio"]:not(:checked)').length;
        if (unansweredCount > 0) {
            alert_toast("Please answer all questions before submitting.", "warning");
            
            // Scroll to first unanswered question
            const firstUnanswered = $('.question-item').find('input[type="radio"]:not(:checked)').first().closest('.question-item');
            $('html, body').animate({
                scrollTop: firstUnanswered.offset().top - 100
            }, 500);
            
            return false;
        }

        start_load();
        
        // Add loading animation to submit button
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
        
        $.ajax({
            url: 'ajax.php?action=save_evaluation',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if(resp.status === 'success'){
                    // Clear localStorage after successful submission
                    localStorage.removeItem('evaluationRatings_<?php echo $rid ?>');
                    localStorage.removeItem('evaluationComment_<?php echo $rid ?>');
                    
                    alert_toast("Thank you! Your evaluation has been submitted successfully.", "success");
                    setTimeout(function(){
                        location.reload()    
                    }, 1750)
                } else {
                    alert_toast("Error: " + resp.message, "error");
                    submitBtn.prop('disabled', false).html('Submit Evaluation');
                }
            },
            error: function(xhr, status, error) {
                alert_toast("An error occurred while saving the evaluation.", "error");
                submitBtn.prop('disabled', false).html('Submit Evaluation');
            },
            complete: function() {
                end_load()
            }
        });
    });

    // Initialize animations and tracking
    $(window).on('scroll', animateOnScroll);
    animateOnScroll();
    loadRatings(); // Load saved ratings when page loads
    updateProgress();
});
</script>