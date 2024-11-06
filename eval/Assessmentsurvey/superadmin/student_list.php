<?php include 'db_connect.php' ?>

<style>
.department-divider {
    border: 0;
    height: 1px;
    background: #333;
    background-image: linear-gradient(to right, #ccc, #333, #ccc);
    margin: 20px 0;
}
</style>

<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_student"><i class="fa fa-plus"></i> Add New Student</a>
			</div>
		</div>
		<div class="card-body">
			<?php
			// Fetch all departments and students
			$departments = array();
			$students = array();
			
			$class_query = $conn->query("SELECT id, concat(curriculum,' ',level,' - ',section) as `class`, department FROM class_list");
			while($class_row = $class_query->fetch_assoc()) {
				// Store class details in the array
				$departments[$class_row['id']] = [
					'class' => $class_row['class'],
					'department' => $class_row['department']
				];
			}
			
			$student_query = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM student_list ORDER BY class_id, concat(firstname,' ',lastname) ASC");
			while($student_row = $student_query->fetch_assoc()) {
				$dept = isset($departments[$student_row['class_id']]) ? $departments[$student_row['class_id']]['department'] : 'Unidentified';
				$students[$dept][] = $student_row;
			}
			
			// Function to generate table for a department
			function generate_department_table($dept_name, $dept_students) {
				global $departments;
				$i = 1;
				?>
				<div class="department-container mb-4">
					<h3><?php echo $dept_name != 'Unidentified' ? $dept_name . ' Department' : 'Unidentified Department'; ?></h3>
					
					<table class="table table-hover table-bordered">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th>School ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>Current Class</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($dept_students as $row): ?>
							<tr>
								<th class="text-center"><?php echo $i++; ?></th>
								<td><b><?php echo $row['school_id']; ?></b></td>
								<td><b><?php echo ucwords($row['name']); ?></b></td>
								<td><b><?php echo $row['email']; ?></b></td>
								<td><b>
									<?php 
									// Display the class name from the departments array
									echo isset($departments[$row['class_id']]) ? $departments[$row['class_id']]['class'] : "N/A"; 
									?>
								</b></td>
								<td class="text-center">
									<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
										Action
									</button>
									<div class="dropdown-menu" style="">
										<a class="dropdown-item view_student" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">View</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="./index.php?page=edit_student&id=<?php echo $row['id']; ?>">
											<i class="fa fa-edit"></i> Edit
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_student" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<hr class="department-divider"> <!-- Add a horizontal line after each table -->
			<?php
			}
			
			// Generate tables for each department, excluding 'Unidentified'
			foreach($students as $dept => $dept_students) {
				if ($dept != 'Unidentified') {
					generate_department_table($dept, $dept_students);
				}
			}
			
			// Generate table for 'Unidentified' department if it exists
			if (isset($students['Unidentified'])) {
				generate_department_table('Unidentified', $students['Unidentified']);
			}
			?>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.view_student').click(function(){
			uni_modal("<i class='fa fa-id-card'></i> Student Details","<?php echo $_SESSION['login_view_folder'] ?>view_student.php?id="+$(this).attr('data-id'))
		})
		$('.delete_student').click(function(){
			_conf("Are you sure to delete this student?","delete_student",[$(this).attr('data-id')])
		})
		$('.table').dataTable()
	})
	function delete_student($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_student',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>
