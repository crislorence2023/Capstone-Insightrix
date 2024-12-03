<?php
?>
<style>
*{
	margin: 0;
	padding: 0;
}
.header{
	margin-left: 1rem;
	font-size: 1.5rem;
}



	</style>
<div class="col-lg-12">
<p class="header">Add New Student</p>
	<div class="card">
	
		<div class="card-body">
			<form action="" id="manage_student">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<div class="form-group">
							<label for="" class="control-label">School ID</label>
							<input type="text" name="school_id" class="form-control form-control-sm" required value="<?php echo isset($school_id) ? $school_id : '' ?>" id="school_id">
						</div>
						<div class="form-group">
							<label for="" class="control-label">First Name</label>
							<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Last Name</label>
							<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Class</label>
							<select name="class_id" id="class_id" class="form-control form-control-sm select2" required>
								<option value=""></option>
								<?php 
								$classes = $conn->query("SELECT id, concat(curriculum,' ',level,' - ',section) as class, department FROM class_list WHERE department = 'COT'");
								while($row=$classes->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" data-department="<?php echo $row['department'] ?>" <?php echo isset($class_id) && $class_id == $row['id'] ? "selected" : "" ?>><?php echo $row['class'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="" class="control-label">Department</label><br>
							<select name="department" id="department" class="form-control form-control-sm select2">
								<option value="COT">College of Technology (COT)</option>
								
							</select>
						</div>
						<div class="form-group">
							<label for="" class="control-label">Avatar</label>
							<div class="custom-file">
		                      <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
		                      <label class="custom-file-label" for="customFile">Choose file</label>
		                    </div>
						</div>
						<div class="form-group d-flex justify-content-center align-items-center">
							<img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar :'' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Email</label>
							<input type="email" class="form-control form-control-sm" name="email" value="<?php echo isset($email) ? $email : '' ?>">
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Password</label>
							<input type="password" class="form-control form-control-sm" name="password" id="password" <?php echo !isset($id) ? "required":'' ?> readonly>
							<small><i>Password is automatically set to the Student ID</i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">Confirm Password</label>
							<input type="password" class="form-control form-control-sm" name="cpass" id="cpass" <?php echo !isset($id) ? 'required' : '' ?> readonly>
							<small><i>Password is automatically set to the Student ID</i></small>
							<small id="pass_match" data-status=''></small>
						</div>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Save</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=student_list'">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	// Auto-fill password fields when school ID changes
	$('#school_id').on('input', function() {
		var schoolId = $(this).val();
		$('#password').val(schoolId);
		$('#cpass').val(schoolId);
		
		// Trigger password match check
		if(schoolId) {
			$('#pass_match').attr('data-status','1').html('<i class="text-success">Password Matched.</i>');
		} else {
			$('#pass_match').attr('data-status','').html('');
		}
	});

	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$('#class_id').change(function(){
		var department = $('#class_id option:selected').data('department');
		$('#department').val(department);
	});

	$('#manage_student').submit(function(e){
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		
		$.ajax({
			url:'ajax.php?action=save_student',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Data successfully saved.',"success");
					setTimeout(function(){
						location.replace('index.php?page=student_list')
					},750)
				}else if(resp == 2){
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>