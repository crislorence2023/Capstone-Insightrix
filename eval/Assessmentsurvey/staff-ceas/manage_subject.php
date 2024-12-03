<?php
include '../db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM subject_list where id={$_GET['id']}")->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
$dept_qry = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
?>
<div class="container-fluid">
	<form action="" id="manage-subject">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div id="msg" class="form-group"></div>
		<div class="form-group">
			<label for="subject" class="control-label">Subject Code</label>
			<input type="text" class="form-control form-control-sm" name="code" id="code" value="<?php echo isset($code) ? $code : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="subject" class="control-label">Subject</label>
			<input type="text" class="form-control form-control-sm" name="subject" id="subject" value="<?php echo isset($subject) ? $subject : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="description" class="control-label">Description</label>
			<textarea name="description" id="description" cols="30" rows="4" class="form-control" required><?php echo isset($description) ? $description : '' ?></textarea>
		</div>
		<div class="form-group">
			<label for="department" class="control-label">Department</label>
			<select name="department" id="department" class="form-control" required>
				<option value="CME" <?php echo isset($department) && $department == "CME" ? "selected" : '' ?>>CME</option>
			</select>
		</div>
	</form>
</div>
<script>
	$(document).ready(function(){
		$('#manage-subject').submit(function(e){
			e.preventDefault();
			start_load()
			$('#msg').html('')
			$.ajax({
				url:'ajax.php?action=save_subject',
				method:'POST',
				data:$(this).serialize(),
				success:function(resp){
					if(resp == 1){
						alert_toast("Data successfully saved.","success");
						setTimeout(function(){
							location.reload()	
						},1750)
					}else if(resp == 2){
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Subject Code already exists.</div>')
						end_load()
					}
				}
			})
			if($.fn.select2){
            $('#department').select2({
                placeholder: "Select Department",
                width: '100%'
            });
        }
    
		})
		
	})
	
</script>