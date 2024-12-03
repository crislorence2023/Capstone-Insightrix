<?php
// manage_class_subject.php
include('./db_connect.php');

$class_subject = null;
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT cs.*, cl.curriculum, cl.level, cl.section 
                           FROM class_subject cs 
                           INNER JOIN class_list cl ON cs.class_id = cl.id 
                           WHERE cs.id = '$id'");
    if($result->num_rows > 0) {
        $class_subject = $result->fetch_assoc();
    }
}
?>

<div class="container-fluid">
    <form action="" id="manage-class-subject">
        <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
        
        <div class="form-group">
            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" class="form-control select2" required>
                <option value=""></option>
                <?php
                $classes = $conn->query("SELECT id, concat(curriculum,' ',level,' - ',section) as class FROM class_list ORDER BY curriculum ASC, level ASC, section ASC");
                while($row = $classes->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" 
                    <?php echo isset($class_subject) && $class_subject['class_id'] == $row['id'] ? 'selected' : '' ?>>
                    <?php echo $row['class'] ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-control select2" required>
                <option value=""></option>
                <?php
                $subjects = $conn->query("SELECT * FROM subject_list ORDER BY subject ASC");
                while($row = $subjects->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" 
                    data-code="<?php echo $row['code'] ?>"
                    data-subject="<?php echo $row['subject'] ?>"
                    <?php echo isset($class_subject) && $class_subject['subject_id'] == $row['id'] ? 'selected' : '' ?>>
                    <?php echo $row['code'].' - '.$row['subject'] ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Added div for showing messages -->
        <div id="msg"></div>
        
        <!-- Added Save Button -->
        <div class="form-group text-center">
            <button class="btn btn-primary mr-2" type="submit">Save</button>
            <button class="btn btn-secondary" type="button" onclick="uni_modal_close()">Cancel</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('.select2').select2({
            placeholder: "Please select here",
            width: "100%"
        });

        $('#manage-class-subject').submit(function(e){
            e.preventDefault();
            start_load();
            $('#msg').html('');
            
            var formData = new FormData($(this)[0]);
            var subject = $('#subject_id option:selected');
            formData.append('subject_code', subject.data('code'));
            formData.append('subject_name', subject.data('subject'));
            
            $.ajax({
                url: 'ajax.php?action=save_class_subject',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success:function(resp){
                    if(resp == 1){
                        alert_toast("Data successfully saved",'success');
                        setTimeout(function(){
                            location.reload();
                        },1500);
                    }else if(resp == 2){
                        $('#msg').html("<div class='alert alert-danger'>Subject is already assigned to the selected class.</div>");
                        end_load();
                    }else{
                        $('#msg').html("<div class='alert alert-danger'>An error occurred.</div>");
                        end_load();
                    }
                }
            });
        });
    });
</script>