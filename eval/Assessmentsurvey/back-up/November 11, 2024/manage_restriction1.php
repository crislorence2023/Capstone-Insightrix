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
					$dept_query = $conn->query("SELECT DISTINCT department FROM subject_list ORDER BY department");
					while($dept = $dept_query->fetch_assoc()):
						$department = $dept['department'];
						$dept_id = str_replace(' ', '-', strtolower($department));
					?>
					<div class="department-section mb-4" id="dept-<?php echo $dept_id ?>">
						<div class="department-header bg-gradient-secondary text-white p-2 rounded d-flex justify-content-between align-items-center">
							<h4 class="m-0"><?php echo $department ?></h4>
							<div class="search-container d-flex align-items-center">
								<input type="text" class="form-control form-control-sm dept-search mr-2" 
									placeholder="Search <?php echo $department ?>..." 
									data-department="<?php echo $dept_id ?>">
								<button type="button" class="btn btn-sm btn-light clear-search" 
									data-department="<?php echo $dept_id ?>">
									<i class="fa fa-times"></i>
								</button>
							</div>
						</div>
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
								<tr class="searchable-row">
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
										<button class="btn btn-sm btn-outline-danger delete-restriction" type="button"><i class="fa fa-trash"></i></button>
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

<!-- Add SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
	$(document).ready(function(){
		$('.select2').select2({
			placeholder:"Please select here",
			width: "100%"
		});

		// Search functionality
		$('.dept-search').on('keyup', function() {
			var searchText = $(this).val().toLowerCase();
			var deptId = $(this).data('department');
			var $rows = $('#dept-' + deptId + ' .searchable-row');

			$rows.each(function() {
				var rowText = $(this).text().toLowerCase();
				$(this).toggle(rowText.indexOf(searchText) > -1);
			});
		});

		// Clear search
		$('.clear-search').on('click', function() {
			var deptId = $(this).data('department');
			$('#dept-' + deptId + ' .dept-search').val('');
			$('#dept-' + deptId + ' .searchable-row').show();
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

			// Check for duplicates
			var isDuplicate = false;
			$('.searchable-row').each(function() {
				var existingFid = $(this).find('input[name="faculty_id[]"]').val();
				var existingCid = $(this).find('input[name="class_id[]"]').val();
				var existingSid = $(this).find('input[name="subject_id[]"]').val();
				
				if (existingFid === fid && existingCid === cid && existingSid === sid) {
					isDuplicate = true;
					return false;
				}
			});

			if (isDuplicate) {
				alert_toast("This combination of Faculty, Class, and Subject already exists!", "warning");
				end_load();
				return;
			}

			// Additional validation for faculty-subject combination
			var facultySubjectDuplicate = false;
			$('.searchable-row').each(function() {
				var existingFid = $(this).find('input[name="faculty_id[]"]').val();
				var existingSid = $(this).find('input[name="subject_id[]"]').val();
				
				if (existingFid === fid && existingSid === sid) {
					facultySubjectDuplicate = true;
					return false;
				}
			});

			if (facultySubjectDuplicate) {
				if(!confirm("This faculty is already assigned to this subject in another class. Do you want to continue?")) {
					end_load();
					return;
				}
			}

			// Additional validation for class-subject combination
			var classSubjectDuplicate = false;
			$('.searchable-row').each(function() {
				var existingCid = $(this).find('input[name="class_id[]"]').val();
				var existingSid = $(this).find('input[name="subject_id[]"]').val();
				
				if (existingCid === cid && existingSid === sid) {
					classSubjectDuplicate = true;
					return false;
				}
			});

			if (classSubjectDuplicate) {
				if(!confirm("This subject is already assigned to this class with a different faculty. Do you want to continue?")) {
					end_load();
					return;
				}
			}

			var f_arr = <?php echo json_encode($f_arr) ?>;
			var c_arr = <?php echo json_encode($c_arr) ?>;
			var s_arr = <?php echo json_encode($s_arr) ?>;

			var department = $('#subject_id option:selected').data('department');
			var deptId = 'dept-' + department.toLowerCase().replace(/ /g, '-');

			var tr = $("<tr class='searchable-row'></tr>")
			tr.append('<td><b>'+f_arr[fid].name+'</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="'+fid+'"></td>')
			tr.append('<td><b>'+c_arr[cid].class+'</b><input type="hidden" name="class_id[]" value="'+cid+'"></td>')
			tr.append('<td><b>'+s_arr[sid].subj.split(' (')[0]+'</b><input type="hidden" name="subject_id[]" value="'+sid+'"></td>')
			tr.append('<td class="text-center"><button class="btn btn-sm btn-outline-danger delete-restriction" type="button"><i class="fa fa-trash"></i></button></td>')
			
			$('#' + deptId + ' table tbody').append(tr)

			frm.find('#class_id').val('').trigger('change')
			frm.find('#faculty_id').val('').trigger('change')
			frm.find('#subject_id').val('').trigger('change')
			end_load()
		})

		// Delete restriction with confirmation
		$(document).on('click', '.delete-restriction', function(e){
			e.preventDefault();
			var row = $(this).closest('tr');
			var facultyName = row.find('td:first b').text();
			var className = row.find('td:eq(1) b').text();
			var subjectName = row.find('td:eq(2) b').text();
			
			Swal.fire({
				title: 'Confirm Deletion',
				html: `Are you sure you want to delete this restriction?<br><br>
					<strong>Faculty:</strong> ${facultyName}<br>
					<strong>Class:</strong> ${className}<br>
					<strong>Subject:</strong> ${subjectName}`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					row.remove();
					Swal.fire(
						'Deleted!',
						'The restriction has been removed.',
						'success'
					)
				}
			})
		});

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

.search-container {
	width: 300px;
}

.search-container .form-control {
	height: 31px;
}

.clear-search {
	padding: 4px 8px;
	line-height: 1;
}
</style>