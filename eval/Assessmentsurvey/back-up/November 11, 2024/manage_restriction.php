<?php
include '../db_connect.php';
?>
<div class="container-fluid">
	<form action="" id="manage-restriction">
		<div class="row">
			<div class="col-md-4 border-right">
				<input type="hidden" name="academic_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
				<div id="msg" class="form-group"></div>
				<div class="form-group">
					<label for="" class="control-label">Faculty</label>
					<select name="" id="faculty_id" class="form-control form-control-sm select2">
						<option value=""></option>
						<?php 
						$faculty = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
						$f_arr = array();
						while($row=$faculty->fetch_assoc()):
							$f_arr[$row['id']]= $row;
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Class</label>
					<select name="" id="class_id" class="form-control form-control-sm select2">
						<option value=""></option>
						<?php 
						$classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as class FROM class_list");
						$c_arr = array();
						while($row=$classes->fetch_assoc()):
							$c_arr[$row['id']]= $row;
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($class_id) && $class_id == $row['id'] ? "selected" : "" ?>><?php echo $row['class'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Subject</label>
					<select name="" id="subject_id" class="form-control form-control-sm select2">
						<option value=""></option>
						<?php 
						$subject = $conn->query("SELECT id,concat(code,' - ',subject,' (',department,')') as subj, department FROM subject_list ORDER BY department, subject");
						$s_arr = array();
						while($row=$subject->fetch_assoc()):
							$s_arr[$row['id']]= $row;
						?>
						<option value="<?php echo $row['id'] ?>" data-department="<?php echo $row['department'] ?>" <?php echo isset($subject_id) && $subject_id == $row['id'] ? "selected" : "" ?>><?php echo $row['subj'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<div class="d-flex w-100 justify-content-center">
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary" id="add_to_list" type="button">Add to List</button>
						<button class="btn btn-sm btn-flat btn-success bg-gradient-success mx-1" id="send_to_all" type="button">Send to All Students</button>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div id="department-lists">
					<?php
					// Get unique departments
					$dept_query = $conn->query("SELECT DISTINCT department FROM subject_list ORDER BY department");
					while($dept = $dept_query->fetch_assoc()):
						$department = $dept['department'];
					?>
					<div class="department-section mb-4" id="dept-<?php echo str_replace(' ', '-', strtolower($department)) ?>">
						<h4 class="department-header bg-gradient-secondary text-white p-2 rounded"><?php echo $department ?></h4>
						<table class="table table-condensed r-list">
							<thead>
								<tr>
									<th>Faculty</th>
									<th>Class</th>
									<th>Subject</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$restriction = $conn->query("SELECT r.*, s.code, s.subject, s.department 
															FROM restriction_list r 
															LEFT JOIN subject_list s ON r.subject_id = s.id 
															WHERE r.academic_id = {$_GET['id']} 
															AND s.department = '$department'
															ORDER BY s.subject ASC");
								while($row=$restriction->fetch_assoc()):
								?>
								<tr>
									<td>
										<b><?php echo isset($f_arr[$row['faculty_id']]) ? $f_arr[$row['faculty_id']]['name'] : '' ?></b>
										<input type="hidden" name="rid[]" value="<?php echo $row['id'] ?>">
										<input type="hidden" name="faculty_id[]" value="<?php echo $row['faculty_id'] ?>">
									</td>
									<td>
										<b><?php echo isset($c_arr[$row['class_id']]) ? $c_arr[$row['class_id']]['class'] : '' ?></b>
										<input type="hidden" name="class_id[]" value="<?php echo $row['class_id'] ?>">
									</td>
									<td>
										<b><?php echo $row['code'].' - '.$row['subject'] ?></b>
										<input type="hidden" name="subject_id[]" value="<?php echo $row['subject_id'] ?>">
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-outline-danger" onclick="$(this).closest('tr').remove()" type="button"><i class="fa fa-trash"></i></button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function(){
		$('.select2').select2({
			placeholder:"Please select here",
			width: "100%"
		});

		$('#manage-restriction').submit(function(e){
			e.preventDefault();
			start_load()
			$('#msg').html('')
			$.ajax({
				url:'ajax.php?action=save_restriction',
				method:'POST',
				data:$(this).serialize(),
				success:function(resp){
					if(resp == 1){
						alert_toast("Data successfully saved.","success");
						setTimeout(function(){
							location.reload()	
						},1750)
					}else if(resp == 2){
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Class already exist.</div>')
						end_load()
					}
				}
			})
		})

		$('#add_to_list').click(function(){
			start_load()
			var frm = $('#manage-restriction')
			var cid = frm.find('#class_id').val()
			var fid = frm.find('#faculty_id').val()
			var sid = frm.find('#subject_id').val()
			
			if(!cid || !fid || !sid) {
				alert_toast("Please select all fields", "warning");
				end_load();
				return;
			}

			var f_arr = <?php echo json_encode($f_arr) ?>;
			var c_arr = <?php echo json_encode($c_arr) ?>;
			var s_arr = <?php echo json_encode($s_arr) ?>;

			var department = $('#subject_id option:selected').data('department');
			var deptId = 'dept-' + department.toLowerCase().replace(/ /g, '-');

			var tr = $("<tr></tr>")
			tr.append('<td><b>'+f_arr[fid].name+'</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="'+fid+'"></td>')
			tr.append('<td><b>'+c_arr[cid].class+'</b><input type="hidden" name="class_id[]" value="'+cid+'"></td>')
			tr.append('<td><b>'+s_arr[sid].subj.split(' (')[0]+'</b><input type="hidden" name="subject_id[]" value="'+sid+'"></td>')
			tr.append('<td class="text-center"><span class="btn btn-sm btn-outline-danger" onclick="$(this).closest(\'tr\').remove()" type="button"><i class="fa fa-trash"></i></span></td>')
			
			$('#' + deptId + ' table tbody').append(tr)

			frm.find('#class_id').val('').trigger('change')
			frm.find('#faculty_id').val('').trigger('change')
			frm.find('#subject_id').val('').trigger('change')
			end_load()
		})

		$('#send_to_all').click(function(){
			if(confirm("This will create evaluation forms for all students. This may take a moment. Continue?")){
				start_load();
				
				$.ajax({
					url: 'ajax.php?action=send_evaluation_to_all',
					method: 'POST',
					data: {
						academic_id: $('[name="academic_id"]').val()
					},
					success: function(resp){
						if(resp == 1){
							alert_toast("Evaluations have been set up successfully for all students.", "success");
							setTimeout(function(){
								location.reload()
							}, 1750);
						} else if(resp == 2){
							alert_toast("An error occurred while setting up evaluations.", "error");
						} else if(resp == 3){
							alert_toast("No classes, faculty, or subjects found to process.", "warning");
						}
						end_load();
					},
					error: function(xhr, status, error){
						alert_toast("Error occurred while processing request.", "error");
						console.error(error);
						end_load();
					}
				});
			}
		});
	})

	function start_load(){
		$('body').prepend('<div id="preloader2"></div>')
	}
	function end_load(){
		$('#preloader2').fadeOut('fast', function() {
			$(this).remove();
		})
	}
</script>

<style>
#preloader2 {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 9999;
	overflow: hidden;
	background: #ffffff8c;
}
#preloader2:before {
	content: "";
	position: fixed;
	top: calc(50% - 30px);
	left: calc(50% - 30px);
	border: 6px solid #ddd;
	border-top: 6px solid #28a745;
	border-radius: 50%;
	width: 60px;
	height: 60px;
	animation: rotate 1s linear infinite;
}
@keyframes rotate {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}

.department-header {
	margin-bottom: 0;
	font-size: 1.2rem;
}

.department-section {
	border: 1px solid #ddd;
	border-radius: 5px;
	overflow: hidden;
}

.department-section table {
	margin-bottom: 0;
}

.department-section:empty {
	display: none;
}
</style>