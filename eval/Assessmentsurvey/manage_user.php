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
<div class="container-fluid">
	<div id="msg"></div>
	
	<form action="" id="manage-user">	
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">First Name</label>
			<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="name">Last Name</label>
			<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="email" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" required autocomplete="off">
		</div>
		<div class="form-group">
    <label for="password">Password</label>
    <div class="input-group">
        <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fa fa-eye-slash" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <small><i>Leave this blank if you dont want to change the password.</i></small>
</div>
		<div class="form-group">
			<label for="" class="control-label">Avatar</label>
			<div class="custom-file">
              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
              <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
		</div>
		<div class="form-group d-flex justify-content-center">
			<img src="<?php echo isset($meta['avatar']) ? 'assets/uploads/'.$meta['avatar'] :'' ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
		</div>
	</form>

	
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
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
});

// Update the ajax success handling in your existing submit handler
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
            if(resp == 1){
                end_load()
                alert_toast("Profile Updated Successfully! Please logout and login again to see the changes.", 'success')
                setTimeout(function(){
                    location.reload()
                }, 3000)
            }else if(resp == 2){
                $('#msg').html(`
                    <div style="text-align: center; 
                               padding: 20px; 
                               background: #f8d7da; 
                               color: #721c24; 
                               border-radius: 5px; 
                               margin-bottom: 20px;">
                        <h4>Email already exists!</h4>
                    </div>
                `)
                end_load()
            }else if(resp == 3){
                $('#msg').html(`
                    <div style="text-align: center; 
                               padding: 10px; 
                               background: #f8d7da; 
                               color: #721c24; 
                               border-radius: 5px; 
                               margin-bottom: 10px;
                               font-size: 14px;">
                        <p>New password cannot be the same as current password!</p>
                    </div>
                `)
                end_load()
            }else{
                $('#msg').html(`
                    <div style="text-align: center; 
                               padding: 20px; 
                               background: #f8d7da; 
                               color: #721c24; 
                               border-radius: 5px; 
                               margin-bottom: 20px;">
                        <h4>An error occurred</h4>
                    </div>
                `)
                end_load()
            }
        }
    })
})
</script>