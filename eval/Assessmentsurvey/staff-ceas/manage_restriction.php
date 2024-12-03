<?php
include '../db_connect.php';

$records_per_page = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records for each department for pagination
$dept_counts = array();
$dept_query = $conn->query("SELECT department, COUNT(*) as count 
                           FROM restriction_list r 
                           LEFT JOIN subject_list s ON r.subject_id = s.id 
                           WHERE r.academic_id = {$_GET['id']}
                           GROUP BY department");
while($row = $dept_query->fetch_assoc()) {
    $dept_counts[$row['department']] = $row['count'];
}
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
						<div class="department-header bg-gradient-secondary text-white p-2 rounded">
							<h4 class="m-0"><?php echo $department ?></h4>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-striped dt-responsive nowrap restriction-table" width="100%">
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
											<button class="btn btn-sm btn-outline-danger delete-restriction" type="button"><i class="fa fa-trash"></i></button>
										</td>
									</tr>
									<?php endwhile; ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- Add required CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function(){
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Please select here",
        width: "100%"
    });

    // Initialize DataTables for each department
    $('.restriction-table').each(function(){
        $(this).DataTable({
            responsive: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            stateSave: true,
            language: {
                search: "Search in department:"
            },
            drawCallback: function(settings) {
                // Reinitialize delete buttons after draw
                initializeDeleteButtons($(this));
            }
        });
    });

    // Function to initialize delete buttons
    function initializeDeleteButtons($table) {
        $table.find('.delete-restriction').off('click').on('click', function() {
            var row = $(this).closest('tr');
            var table = row.closest('table').DataTable();
            
            Swal.fire({
                title: 'Confirm Deletion',
                text: "Are you sure you want to delete this restriction?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    table.row(row).remove().draw();
                    Swal.fire(
                        'Deleted!',
                        'The restriction has been removed.',
                        'success'
                    );
                }
            });
        });
    }

    // Add to list handler with validation
    $('#add_to_list').click(function(){
    start_load();
    var frm = $('#manage-restriction');
    var cid = frm.find('#class_id').val();
    var fid = frm.find('#faculty_id').val();
    var sid = frm.find('#subject_id').val();
    
    if(!cid || !fid || !sid) {
        alert_toast("Please select all fields", "warning");
        end_load();
        return;
    }

    // Get selected department
    var department = $('#subject_id option:selected').data('department');
    if (!department) {
        alert_toast("Unable to determine department", "warning");
        end_load();
        return;
    }

    var deptId = 'dept-' + department.toLowerCase().replace(/ /g, '-');
    var $table = $('#' + deptId + ' table');
    
    if ($table.length === 0) {
        alert_toast("Department table not found", "warning");
        end_load();
        return;
    }

    var table = $table.DataTable();
    
    // Check for existing combination
    var isDuplicate = false;
    table.rows().every(function() {
        var $row = $(this.node());
        var rowFid = $row.find('input[name="faculty_id[]"]').val();
        var rowCid = $row.find('input[name="class_id[]"]').val();
        var rowSid = $row.find('input[name="subject_id[]"]').val();
        
        if (rowFid == fid && rowCid == cid && rowSid == sid) {
            isDuplicate = true;
            return false;
        }
    });

    if (isDuplicate) {
        alert_toast("This combination already exists", "warning");
        end_load();
        return;
    }

    // Get display text for new row
    var facultyText = $('#faculty_id option:selected').text().trim();
    var classText = $('#class_id option:selected').text().trim();
    var subjectText = $('#subject_id option:selected').text().split(' (')[0].trim();

    // Create the new row HTML
    var newRowData = [
        '<b>' + facultyText + '</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="' + fid + '">',
        '<b>' + classText + '</b><input type="hidden" name="class_id[]" value="' + cid + '">',
        '<b>' + subjectText + '</b><input type="hidden" name="subject_id[]" value="' + sid + '">',
        '<button class="btn btn-sm btn-outline-danger delete-restriction" type="button"><i class="fa fa-trash"></i></button>'
    ];

    // Add new row and redraw
    var newRow = table.row.add(newRowData).draw(false);
    
    // Ensure delete button functionality is added to the new row
    var $newRow = $(newRow.node());
    $newRow.find('.delete-restriction').on('click', function() {
        var row = $(this).closest('tr');
        var table = row.closest('table').DataTable();
        
        Swal.fire({
            title: 'Confirm Deletion',
            text: "Are you sure you want to delete this restriction?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                table.row(row).remove().draw();
                Swal.fire(
                    'Deleted!',
                    'The restriction has been removed.',
                    'success'
                );
            }
        });
    });

    // Reset form fields
    frm.find('#class_id').val('').trigger('change');
    frm.find('#faculty_id').val('').trigger('change');
    frm.find('#subject_id').val('').trigger('change');
    
    end_load();
});

    // Form submission handler
    $('#manage-restriction').submit(function(e){
        e.preventDefault();
        start_load();
        $('#msg').html('');

        // Create FormData object
        var formData = new FormData();
        formData.append('academic_id', $('[name="academic_id"]').val());

        // Gather data from all department tables
        $('.restriction-table').each(function(){
            var table = $(this).DataTable();
            
            // Get ALL rows from this table
            table.rows().every(function(){
                var $row = $(this.node());
                
                // Get values from the row
                var rid = $row.find('input[name="rid[]"]').val();
                var facultyId = $row.find('input[name="faculty_id[]"]').val();
                var classId = $row.find('input[name="class_id[]"]').val();
                var subjectId = $row.find('input[name="subject_id[]"]').val();
                
                // Append to formData if all required values exist
                if (facultyId && classId && subjectId) {
                    formData.append('rid[]', rid || '');
                    formData.append('faculty_id[]', facultyId);
                    formData.append('class_id[]', classId);
                    formData.append('subject_id[]', subjectId);
                }
            });
        });

        // Submit form with all data
        $.ajax({
            url: 'ajax.php?action=save_restriction',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp){
                if(resp == 1){
                    alert_toast("Data successfully saved.", "success");
                    setTimeout(function(){
                        location.reload();
                    }, 1750);
                } else if(resp == 2){
                    $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Error saving data.</div>');
                    end_load();
                }
            },
            error: function(xhr, status, error) {
                $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> An error occurred: ' + error + '</div>');
                console.error(error);
                end_load();
            }
        });
    });

    // Send to all students handler
    $('#send_to_all').click(function(){
        Swal.fire({
            title: 'Send to All Students',
            text: "This will create evaluation forms for all students. This may take a moment. Continue?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                
                $.ajax({
                    url: 'ajax.php?action=send_evaluation_to_all',
                    method: 'POST',
                    data: {
                        academic_id: $('[name="academic_id"]').val()
                    },
                    success: function(resp){
                        if(resp == 1){
                            Swal.fire(
                                'Success!',
                                'Evaluations have been set up successfully for all students.',
                                'success'
                            );
                            setTimeout(function(){
                                location.reload();
                            }, 1750);
                        } else if(resp == 2){
                            Swal.fire(
                                'Error!',
                                'An error occurred while setting up evaluations.',
                                'error'
                            );
                        } else if(resp == 3){
                            Swal.fire(
                                'Warning!',
                                'No classes, faculty, or subjects found to process.',
                                'warning'
                            );
                        }
                        end_load();
                    },
                    error: function(xhr, status, error){
                        Swal.fire(
                            'Error!',
                            'Error occurred while processing request.',
                            'error'
                        );
                        console.error(error);
                        end_load();
                    }
                });
            }
        });
    });
});

// Loader functions
function start_load(){
    $('body').prepend('<div id="preloader2"></div>');
}

function end_load(){
    $('#preloader2').fadeOut('fast', function() {
        $(this).remove();
    });
}

function alert_toast(msg = 'TEST', type = 'success'){
    var bg = type === 'success' ? '#28a745' : type === 'danger' ? '#dc3545' : '#ffc107';
    var toast = $('<div class="alert_toast" style="position: fixed; top: 0; right: 0; z-index: 99999">' +
        '<div class="alert alert-' + type + ' text-white">' +
        '<span>' + msg + '</span>' +
        '</div>' +
    '</div>');

    $('body').append(toast);
    toast.show();

    setTimeout(function(){
        toast.hide('slow', function(){
            $(this).remove();
        });
    }, 2000);
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
	margin-bottom: 2rem;
}

.department-section:last-child {
	margin-bottom: 0;
}

.dataTables_wrapper {
	padding: 1rem;
}

.dataTables_filter {
	margin-bottom: 1rem;
}

.dataTables_length select {
	min-width: 4rem;
}
</style>