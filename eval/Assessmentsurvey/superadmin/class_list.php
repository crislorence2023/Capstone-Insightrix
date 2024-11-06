<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_class" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<?php
			// Fetch all departments and classes
			$departments = array();
			$classes = array();
			
			$class_query = $conn->query("SELECT *, concat(curriculum,' ',level,'-',section) as `class` FROM class_list ORDER BY department ASC, curriculum ASC, level ASC, section ASC");
			while($class_row = $class_query->fetch_assoc()) {
				$dept = $class_row['department'];
				if (!in_array($dept, $departments)) {
					$departments[] = $dept;
				}
				$classes[$dept][] = $class_row;
			}
			
			// Function to generate table for a department
			function generate_department_table($dept_name, $dept_classes) {
				$i = 1;
				?>
				<div class="department-container mb-4">
					<h3><?php echo $dept_name != '' ? $dept_name . ' Department' : 'Unassigned Department'; ?></h3>
					
					<table class="table table-hover table-bordered class-table">
						<colgroup>
							<col width="5%">
							<col width="20%">
							<col width="55%">
							<col width="20%">
						</colgroup>
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th>Department</th>
								<th>Class</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($dept_classes as $row): ?>
							<tr>
								<th class="text-center"><?php echo $i++ ?></th>
								<td><b><?php echo $row['department'] ?></b></td>
								<td><b><?php echo $row['class'] ?></b></td>
								<td class="text-center">
									<div class="btn-group">
										<a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_class">
											<i class="fas fa-edit"></i>
										</a>
										<button type="button" class="btn btn-danger btn-flat delete_class" data-id="<?php echo $row['id'] ?>">
											<i class="fas fa-trash"></i>
										</button>
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
				if (isset($classes[$dept])) {
					generate_department_table($dept, $classes[$dept]);
				}
			}
			?>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.class-table').dataTable()
		$('.new_class').click(function(){
			uni_modal("New class","<?php echo $_SESSION['login_view_folder'] ?>manage_class.php")
		})
		$('.manage_class').click(function(){
			uni_modal("Manage class","<?php echo $_SESSION['login_view_folder'] ?>manage_class.php?id="+$(this).attr('data-id'))
		})
		$('.delete_class').click(function(){
			_conf("Are you sure to delete this class?","delete_class",[$(this).attr('data-id')])
		})
	})
	function delete_class($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_class',
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