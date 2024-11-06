<style>
.department-divider {
    border: 0;
    height: 1px;
    background: #333;
    background-image: linear-gradient(to right, #ccc, #333, #ccc);
    margin: 20px 0;
}

</style>



<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_faculty"><i class="fa fa-plus"></i> Add New Faculty</a>
			</div>
		</div>
		<div class="card-body">
			<?php
			// Fetch all departments and faculty
			$departments = array();
			$faculty = array();
			
			$faculty_query = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM faculty_list ORDER BY department, concat(firstname,' ',lastname) ASC");
			while($faculty_row = $faculty_query->fetch_assoc()) {
				$dept = $faculty_row['department'];
				if (!in_array($dept, $departments)) {
					$departments[] = $dept;
				}
				$faculty[$dept][] = $faculty_row;
			}
			
			// Function to generate table for a department
			function generate_department_table($dept_name, $dept_faculty) {
				$i = 1;
				?>
				<div class="department-container mb-4">
					<h3><?php echo $dept_name != '' ? $dept_name . ' Department' : 'Unassigned Department'; ?></h3>
					
					<table class="table table-hover table-bordered faculty-table">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th>School ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>Department</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($dept_faculty as $row): ?>
							<tr>
								<th class="text-center"><?php echo $i++; ?></th>
								<td><b><?php echo $row['school_id']; ?></b></td>
								<td><b><?php echo ucwords($row['name']); ?></b></td>
								<td><b><?php echo $row['email']; ?></b></td>
								<td><b><?php echo $row['department']; ?></b></td>
								<td class="text-center">
									<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
										Action
									</button>
									<div class="dropdown-menu" style="">
										<a class="dropdown-item view_faculty" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">View</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="./index.php?page=edit_faculty&id=<?php echo $row['id']; ?>">Edit</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_faculty" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<hr class="department-divider">
			<?php
			}
			
			// Generate tables for each department
			foreach($departments as $dept) {
				if (isset($faculty[$dept])) {
					generate_department_table($dept, $faculty[$dept]);
				}
			}
			?>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.view_faculty').click(function(){
			uni_modal("<i class='fa fa-id-card'></i> Faculty Details","<?php echo $_SESSION['login_view_folder'] ?>view_faculty.php?id="+$(this).attr('data-id'))
		})
		$('.delete_faculty').click(function(){
			_conf("Are you sure to delete this faculty?","delete_faculty",[$(this).attr('data-id')])
		})
		$('.faculty-table').dataTable()
	})
	function delete_faculty($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_faculty',
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