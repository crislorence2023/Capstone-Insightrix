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
            <div class="card card-info card-primary">
                <div class="card-header">
                    <b>Criteria and Question Form</b>
                </div>
                <div class="card-body">
                    <form action="" id="manage-criteria">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label for="">Criteria</label>
                            <input type="text" name="criteria" class="form-control form-control-sm">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-primary btn-flat bg-gradient-primary mx-1" form="manage-criteria">Save Criteria</button>
                            <button class="btn btn-sm btn-flat btn-secondary bg-gradient-secondary mx-1" form="manage-criteria" type="reset">Cancel</button>
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
            <div class="card card-outline card-info">
                <div class="card-header">
                    <b>Evaluation Questionnaire for Academic: <?php echo $year.' '.(ordinal_suffix($semester)) ?> </b>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-flat btn-primary bg-gradient-primary mx-1" id="eval_restrict" type="button">Assign Evaluation</button>
                        <button class="btn btn-sm btn-flat btn-success bg-gradient-success mx-1" id="save-order">Save Order</button>
                    </div>
                </div>
                <div class="card-body">
                    <fieldset class="border border-info p-2 w-100">
                        <legend class="w-auto">Rating Legend</legend>
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
                                <table class="table table-condensed criteria-table">
                                    <thead>
                                        <tr class="bg-gradient-secondary">
                                            <th class="text-center" width="5%"><i class="fa fa-bars"></i></th>
                                            <th width="30%">
                                                <b><?php echo $crow['criteria'] ?></b>
                                                <span class="float-right">
                                                    <a href="javascript:void(0)" class="edit-criteria" data-id="<?php echo $crow['id'] ?>"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:void(0)" class="delete-criteria" data-id="<?php echo $crow['id'] ?>"><i class="fa fa-trash"></i></a>
                                                </span>
                                            </th>
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
    $(document).on('click', '.edit_question', function(){
        var id = $(this).attr('data-id');
        var question = <?php echo json_encode($q_arr) ?>;
        $('#manage-question').find("[name='id']").val(question[id].id);
        $('#manage-question').find("[name='question']").val(question[id].question);
        $('#manage-question').find("[name='criteria_id']").val(question[id].criteria_id).trigger('change');
        scrollToQuestion(id);
    });

    // Delete question
    $(document).on('click', '.delete_question', function(){
        var questionId = $(this).attr('data-id');
        confirmDeleteQuestion(questionId);
    });

	var originalCriteria = ''; // Variable to store the original criteria value

    // Edit criteria
    $(document).on('click', '.edit-criteria', function(){
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
    $(document).on('click', '.delete-criteria', function(){
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
.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 1020;
}

.highlight-question {
    animation: highlightFade 3s;
}

@keyframes highlightFade {
    0% { background-color: #fffacd; }
    100% { background-color: transparent; }
}

#criteria-list .fa-bars {
    cursor: move;
}

.criteria-item {
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.criteria-table {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    #form-section {
        position: static !important;
        height: auto !important;
        overflow-y: visible !important;
    }
}
</style>