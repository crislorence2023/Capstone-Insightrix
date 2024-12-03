<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM academic_list where id = ".$_GET['id'])->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
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
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4" id="form-section" style="position: sticky; top: 20px; height: calc(100vh - 40px); overflow-y: auto;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b></i>Criteria and Question Form</b>
                </div>
                <div class="card-body">
                    <form action="" id="manage-criteria">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label for="">Criteria</label>
                            <input type="text" name="criteria" class="form-control form-control-sm">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-primary" form="manage-criteria">
                                <i class="fas fa-save mr-2"></i>Save Criteria
                            </button>
                            <button class="btn btn-secondary" form="manage-criteria" type="reset">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                        </div>
                    </form>
                    <hr>
                    <form action="" id="manage-question">
                        <input type="hidden" name="academic_id" value="<?php echo isset($id) ? $id : '' ?>">
                        <input type="hidden" name="id" value="">
                        <div class="form-group">
                            <label for="">Criteria</label>
                            <select name="criteria_id" id="criteria_id" class="custom-select custom-select-sm select2">
                                <option value=""></option>
                                <?php 
                                    $criteria = $conn->query("SELECT * FROM criteria_list order by abs(order_by) asc ");
                                    while($row = $criteria->fetch_assoc()):
                                ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['criteria'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Question</label>
                            <textarea name="question" id="question" cols="30" rows="4" class="form-control" required=""></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-primary btn-flat bg-gradient-primary mx-1" form="manage-question">Save Question</button>
                            <button class="btn btn-sm btn-flat btn-secondary bg-gradient-secondary mx-1" form="manage-question" type="reset">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>Evaluation Questionnaire for Academic: <?php echo $year.' '.(ordinal_suffix($semester)) ?></b>
                    <div class="card-tools">
                        <button class="btn btn-primary" id="eval_restrict" type="button">
                            Assign Evaluation
                        </button>
                        <button class="btn btn-success" id="save-order">
                            <i class="fas fa-save mr-2"></i>Save Order
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <fieldset class="border border-info p-2 w-100">
                        <legend class="w-auto text-bold">Rating Legend</legend>
                        <p>5 = Strongly Agree, 4 = Agree, 3 = Uncertain, 2 = Disagree, 1 = Strongly Disagree</p>
                    </fieldset>
                    <form id="order-question">
                        <div class="clear-fix mt-2"></div>
                        <div id="criteria-list">
                            <?php 
                                $q_arr = array();
                                $criteria = $conn->query("SELECT * FROM criteria_list order by abs(order_by) asc ");
                                while($crow = $criteria->fetch_assoc()):
                            ?>
                            <div class="criteria-item" data-id="<?php echo $crow['id']; ?>">
                                <div class="criteria-group">
                                    <div class="criteria-header d-flex justify-content-between align-items-center">
                                        <b class="text-bold"><?php echo $crow['criteria'] ?></b>
                                        <div class="criteria-actions">
                                            <button class="btn btn-sm btn-link edit-criteria" data-id="<?php echo $crow['id'] ?>">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger delete-criteria" data-id="<?php echo $crow['id'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="questions-container">
                                        <table class="table table-condensed criteria-table">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th class="text-center" width="5%"><i class="fa fa-bars"></i></th>
                                                    <th width="40%">Question</th>
                                                    <th class="text-center">1</th>
                                                    <th class="text-center">2</th>
                                                    <th class="text-center">3</th>
                                                    <th class="text-center">4</th>
                                                    <th class="text-center">5</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tr-sortable">
                                                <?php 
                                                $questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = $id order by abs(order_by) asc ");
                                                while($row=$questions->fetch_assoc()):
                                                $q_arr[$row['id']] = $row;
                                                ?>
                                                <tr class="bg-white">
                                                    <td class="p-1 text-center" width="5px">
                                                        <span class="btn-group dropright">
                                                          <span type="button" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                           <i class="fa fa-ellipsis-v"></i>
                                                          </span>
                                                          <div class="dropdown-menu">
                                                             <a class="dropdown-item edit_question" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Edit</a>
                                                              <div class="dropdown-divider"></div>
                                                             <a class="dropdown-item delete_question" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                                                          </div>
                                                        </span>
                                                    </td>
                                                    <td class="p-1" width="40%">
                                                        <?php echo $row['question'] ?>
                                                        <input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
                                                    </td>
                                                    <?php for($c=0;$c<5;$c++): ?>
                                                    <td class="text-center">
                                                        <div class="icheck-success d-inline">
                                                            <input type="radio" name="rate[<?php echo $row['id'] ?>]" id="qradio<?php echo $row['id'].'_'.$c ?>" value="<?php echo 5-$c ?>">
                                                            <label for="qradio<?php echo $row['id'].'_'.$c ?>">
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <?php endfor; ?>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2({
        placeholder: "Please select here",
        width: "100%"
    });

    // Make criteria list sortable
    $('#criteria-list').sortable({
        handle: '.fa-bars',
        update: function(event, ui) {
            saveCriteriaOrder();
        }
    });

	

    // Make questions sortable within each criteria
    $('.tr-sortable').sortable();

    // Edit question
    $(document).on('click', '.edit_question', function(e){
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).attr('data-id');
        var question = <?php echo json_encode($q_arr) ?>;
        $('#manage-question').find("[name='id']").val(question[id].id);
        $('#manage-question').find("[name='question']").val(question[id].question);
        $('#manage-question').find("[name='criteria_id']").val(question[id].criteria_id).trigger('change');
        scrollToQuestion(id);
    });

    // Delete question
    $(document).on('click', '.delete_question', function(e){
        e.preventDefault();
        e.stopPropagation();
        var questionId = $(this).attr('data-id');
        confirmDeleteQuestion(questionId);
    });

	var originalCriteria = ''; // Variable to store the original criteria value

    // Edit criteria
    $(document).on('click', '.edit-criteria', function(e){
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).attr('data-id');
        $.ajax({
            url: 'ajax.php?action=get_criteria',
            method: 'POST',
            data: {id: id},
            dataType: 'json',
            success: function(resp){
                if(resp && !resp.error){
                    $('#manage-criteria').find("[name='id']").val(resp.id);
                    $('#manage-criteria').find("[name='criteria']").val(resp.criteria);
                    // Scroll to the form section for user convenience
                    $('html, body').animate({
                        scrollTop: $("#form-section").offset().top - 100
                    }, 500);
                    // Change the button text to indicate editing
                    $('#manage-criteria button[type="submit"]').text('Update Criteria');
                } else {
                    alert_toast(resp.error || "Error fetching criteria data", 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast("Error fetching criteria data", 'error');
            }
        });
    });

    // Delete criteria
    $(document).on('click', '.delete-criteria', function(e){
        e.preventDefault();
        e.stopPropagation();
        var criteriaId = $(this).attr('data-id');
        confirmDeleteCriteria(criteriaId);
    });

    // Evaluation restriction
    $('#eval_restrict').click(function(){
        uni_modal("Manage Evaluation Restrictions","<?php echo $_SESSION['login_view_folder'] ?>manage_restriction.php?id=<?php echo $id ?>","mid-large");
    });

    // Save/Update criteria
    $('#manage-criteria').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url:'ajax.php?action=save_criteria',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    alert_toast('Criteria successfully saved',"success");
                    setTimeout(function(){
                        location.reload();
                    },1500);
                } else if(resp == 2) {
                    alert_toast('Criteria already exists',"error");
                } else {
                    alert_toast('An error occurred while saving criteria',"error");
                }
                end_load();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast('An error occurred while saving criteria',"error");
                end_load();
            }
        });
    });

    // Reset criteria form after submission or cancellation
    $('#manage-criteria button[type="reset"]').click(function(){
        $('#manage-criteria').find("[name='id']").val('');
        $('#manage-criteria').find("[name='criteria']").val('');
        $('#manage-criteria button[type="submit"]').text('Save Criteria');
    });

    // Save question
    $('#manage-question').submit(function(e){
        e.preventDefault();
        start_load();
        if($('#question').val() == ''){
            alert_toast("Please fill the question description first",'error');
            end_load();
            return false;
        }
        $.ajax({
            url:'ajax.php?action=save_question',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    alert_toast('Question successfully saved',"success");
                    setTimeout(function(){
                        location.reload();
                    },1500);
                } else {
                    alert_toast('An error occurred while saving question',"error");
                }
                end_load();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast('An error occurred while saving question',"error");
                end_load();
            }
        });
    });

    // Save order (both criteria and questions)
    $('#save-order').click(function(e) {
        e.preventDefault();
        saveCriteriaOrder();
        saveQuestionOrder();
    });

    // Helper functions
    function saveCriteriaOrder() {
        var criteriaOrder = [];
        $('.criteria-item').each(function(index) {
            criteriaOrder.push({
                id: $(this).data('id'),
                order: index
            });
        });

        $.ajax({
            url: 'ajax.php?action=save_criteria_order',
            method: 'POST',
            data: {order: JSON.stringify(criteriaOrder)},
            success: function(resp) {
                if(resp == 1) {
                    alert_toast('Criteria order saved successfully', 'success');
                } else {
                    alert_toast('Error saving criteria order', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast('Error saving criteria order', 'error');
            }
        });
    }

    function saveQuestionOrder() {
        var orderData = [];
        $('.criteria-table').each(function() {
            var criteriaId = $(this).closest('.criteria-item').data('id');
            $(this).find('input[name="qid[]"]').each(function(index) {
                orderData.push({
                    id: $(this).val(),
                    order: index,
                    criteria_id: criteriaId
                });
            });
        });

        $.ajax({
            url: 'ajax.php?action=save_question_order',
            data: JSON.stringify(orderData),
            contentType: 'application/json',
            processData: false,
            method: 'POST',
            success: function(resp) {
                try {
                    var response = JSON.parse(resp);
                    if (response.status === 'success') {
                        alert_toast(response.message, "success");
                    } else {
                        alert_toast(response.message, "error");
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    alert_toast('Error saving question order', "error");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast('Error saving question order', "error");
            }
        });
    }

    function confirmDeleteQuestion(id) {
        if (confirm("Are you sure you want to delete this question?")) {
            deleteQuestion(id);
        }
    }

    function deleteQuestion(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_question',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Question successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                } else {
                    alert_toast("Error deleting question", 'error');
                }
                end_load();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast("Error deleting question", 'error');
                end_load();
            }
        });
    }

    function confirmDeleteCriteria(id) {
        if (confirm("Are you sure you want to delete this criteria? This will also delete all associated questions.")) {
            deleteCriteria(id);
        }
    }

    function deleteCriteria(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_criteria',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Criteria successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                } else {
                    alert_toast("Error deleting criteria", 'error');
                }
                end_load();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert_toast("Error deleting criteria", 'error');
                end_load();
            }
        });
    }

    function scrollToQuestion(questionId) {
        var questionElement = $('input[name="qid[]"][value="' + questionId + '"]').closest('tr');
        if (questionElement.length) {
            $('html, body').animate({
                scrollTop: questionElement.offset().top - 100
            }, 1000);
        }
    }

    function handleResponsive() {
        var formSection = $('#form-section');
        if (window.innerWidth <= 768) {  // Typical breakpoint for mobile devices
            formSection.removeClass('sticky-top');
        } else {
            formSection.addClass('sticky-top');
        }
    }

    // Initial call to handleResponsive
    handleResponsive();

    // On window resize
    $(window).resize(function() {
        handleResponsive();
    });
});
</script>

<style>
/* Modern Color Scheme and Variables */
:root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary: #64748b;
    --success: #059669;
    --danger: #dc2626;
    --background: #f8fafc;
    --surface: #ffffff;
    --text: #1e293b;
    --text-light: #64748b;
    --border: #e2e8f0;
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

/* Global Resets and Base Styles */
body {
    background-color: var(--background);
    color: var(--text);
    line-height: 1.5;
}

/* Card Styling */
.card {
    
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
    border-radius: 15px;
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    font-weight: 600;
    color:#1F2D3D;
            font-size: 20px;
            font-weight: 600;
   
}

.card-body {
    padding: 1.5rem;
}

/* Form Controls */
.form-control, .custom-select {
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.form-control:focus, .custom-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    outline: none;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text);
}

/* Button Styles */
.btn {

  
            font-size: 14px;
            font-weight: 400;


    padding: 10px;
    
    
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
}

.btn i {
    font-size: 1rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover);
}

.btn-secondary {
    background: var(--secondary);
    color: white;
}

.btn-success {
    background: var(--success);
    color: white;
}

.p-1{
  
}

/* Table Styling */
.criteria-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 1rem;
}

.criteria-table th {
    background: var(--background);
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text);
    text-align: left;
}

.criteria-table td {
    padding: 1rem;
    border-top: 1px solid var(--border);
    font-size: 0.875rem;
    vertical-align: middle;
}

/* Radio Buttons */
.icheck-success {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.icheck-success input[type="radio"] {
    appearance: none;
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--border);
    border-radius: 50%;
    margin: 0;
    cursor: pointer;
    transition: all 0.2s ease;
}

.icheck-success input[type="radio"]:checked {
    border-color: var(--success);
    background-color: var(--success);
    box-shadow: inset 0 0 0 4px var(--surface);
}

/* Spacing and Layout */
.d-flex {
    gap: 0.75rem;
}

.card-tools {
    display: flex;
    gap: 0.75rem;
}

/* Rating Legend */
fieldset.border {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-md);
    padding: 1.25rem !important;
    margin-bottom: 1.5rem;
}

fieldset legend {
    padding: 0 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .card-header {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn {
        width: auto;
        padding: 0.5rem 0.875rem;
    }
    
    .d-flex {
        gap: 0.5rem;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(0.5rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.criteria-item {
    animation: fadeIn 0.3s ease-out;
   
}

.text-bold{
    color:#1F2D3D;
            font-size: 16px;
            font-weight: 600;
}

/* Toast Notification Improvements */
.toast {
    font-size: 1rem !important;  /* Base font size */
    max-width: 400px !important;
    opacity: 1 !important;
}

.toast-header {
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    padding: 0.75rem 1rem !important;
}

.toast-body {
    font-size: 0.95rem !important;
    padding: 1rem !important;
    line-height: 1.5 !important;
}

/* Toast variants */
.toast-success .toast-header {
    background-color: var(--success) !important;
    color: white !important;
}

.toast-error .toast-header {
    background-color: var(--danger) !important;
    color: white !important;
}

/* Toast animation */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast {
    animation: slideInRight 0.3s ease-out;
}

/* Ensure toast is above other elements */
.toast-container {
    z-index: 9999 !important;
}

/* Add new styles for criteria groups */
.criteria-group {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
}

.criteria-header {
    background: #f8fafc;
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1rem;
    border: 1px solid var(--border);
}

.criteria-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.criteria-actions .btn-link {
    padding: 0.25rem 0.5rem;
    color: var(--text);
    text-decoration: none;
    transition: all 0.2s ease;
}

.criteria-actions .btn-link:hover {
    transform: scale(1.1);
}

.criteria-actions .text-danger:hover {
    color: var(--danger) !important;
}

.questions-container {
    background: #ffffff;
    border-radius: var(--radius-sm);
    padding: 1rem;
    border: 1px solid var(--border);
}

/* Update table styles */
.criteria-table {
    margin-bottom: 0;
}

.criteria-table thead th {
    background: #f8fafc;
    border-bottom: 2px solid var(--border);
    padding: 0.75rem;
    font-weight: 600;
}

.criteria-table tbody td {
    padding: 0.75rem;
    border-bottom: 1px solid var(--border);
}

/* Add hover effect for better UX */
.criteria-group:hover {
    box-shadow: var(--shadow-md);
}
</style>