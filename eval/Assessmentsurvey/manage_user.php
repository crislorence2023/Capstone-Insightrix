<?php 
include('db_connect.php');
session_start();
if(isset($_GET['id'])){
$type = array("","users","faculty_list","student_list");
$user = $conn->query("SELECT * FROM {$type[$_SESSION['login_type']]} where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container">
	<div id="msg"></div>
	
	<form action="" id="manage-user">	
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">First Name</label>
			<input type="text" name="firstname" id="firstname" class="input-field" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="name">Last Name</label>
			<input type="text" name="lastname" id="lastname" class="input-field" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="text" name="email" id="email" class="input-field" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" autocomplete="off">
			<small><i>Leave this blank to keep your current email address.</i></small>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="input-field" value="" autocomplete="off">
			<small><i>Leave this blank if you don't want to change the password.</i></small>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Avatar</label>
			<div class="file-input">
              <input type="file" id="customFile" name="img" onchange="displayImg(this,$(this))">
              <label for="customFile">Choose file</label>
            </div>
		</div>
		<div class="form-group center">
			<img src="<?php echo isset($meta['avatar']) ? 'assets/uploads/'.$meta['avatar'] :'' ?>" alt="" id="cimg" class="img-thumbnail">
		</div>
	</form>
</div>
<style>
	.container {
		max-width: 600px;
		margin: 0 auto;
		padding: 20px;
	}

	.form-group {
		margin-bottom: 15px;
	}

	.input-field {
		width: 100%;
		padding: 10px;
		margin-top: 5px;
		border: 1px solid #ccc;
		border-radius: 4px;
	}

	.file-input {
		position: relative;
	}

	.file-input input[type="file"] {
		width: 100%;
		padding: 10px;
		border: 1px solid #ccc;
		border-radius: 4px;
	}

	.file-input label {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: #f8f9fa;
		border: 1px solid #ccc;
		border-radius: 4px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
	}

	.center {
		display: flex;
		justify-content: center;
	}

	.img-thumbnail {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 50%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=update_user',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp ==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}else{
					$('#msg').html('<div class="alert alert-danger">Email address already exists</div>')
					end_load()
				}
			}
		})
	})

</script>