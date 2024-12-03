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

<div class="container py-4 rounded-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-light bg-gradient text-white py-3">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 flex-grow-1 text-bold"><?php echo isset($id) ? 'Edit Student' : 'Add New Student'; ?></h5>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.href = 'staff_cme_index.php?page=student_list'">
                    Back to List
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="" id="manage_student" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6 border-end">
                       
                        <div class="form-group mb-3">
                            <label for="school_id" class="form-label fw-semibold">School ID</label>
                            <input type="text" name="school_id" id="school_id" class="form-control" required value="<?php echo isset($school_id) ? $school_id : '' ?>" placeholder="Enter school ID">
                            <div class="invalid-feedback">Please provide a School ID.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="firstname" class="form-label fw-semibold">First Name</label>
                            <input type="text" name="firstname" class="form-control" required value="<?php echo isset($firstname) ? $firstname : '' ?>" placeholder="Enter first name">
                            <div class="invalid-feedback">Please provide a First Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="lastname" class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="lastname" class="form-control" required value="<?php echo isset($lastname) ? $lastname : '' ?>" placeholder="Enter last name">
                            <div class="invalid-feedback">Please provide a Last Name.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department" class="form-label fw-semibold">Department</label>
                            <input type="hidden" name="department" id="department" value="CME">
                            <input type="text" class="form-control" value="CME" readonly disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="class_id" class="form-label fw-semibold">Class</label>
                            <select name="class_id" id="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                            </select>
                            <div class="invalid-feedback">Please select a Class.</div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-6">
                
                        <div class="form-group mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="<?php echo isset($email) ? $email : '' ?>" 
                                   placeholder="Enter email address (optional)">
                            <div class="invalid-feedback">Please provide a valid Email address if entering one.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" id="password" class="form-control" readonly>
                            <small class="form-text text-muted">Password is automatically set to the Student ID</small>
                        </div>
                        <div class="form-group mb-4">
                            <label for="cpass" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="cpass" id="cpass" class="form-control" readonly>
                            <small class="form-text text-muted">Password is automatically set to the Student ID</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label fw-semibold">Profile Picture (Optional)</label>
                            <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-3">
                                <div class="avatar-preview">
                                    <img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar : 'assets/img/default-avatar.png' ?>" 
                                         alt="Avatar" id="cimg" class="rounded shadow-sm" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="avatar-upload flex-grow-1">
                                    <input type="file" class="form-control d-none" id="customFile" name="img" accept="image/*" onchange="displayImg(this,$(this))">
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="$('#customFile').click()">
                                        <i data-lucide="upload-cloud" class="me-2"></i>Choose Photo
                                    </button>
                                    <div class="form-text text-center">Square image, 200x200px or larger</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-light me-2" onclick="location.href = 'staff_cme_index.php?page=student_list'">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #007bff;
    --primary-hover: #0056b3;
}

.card {
    transition: box-shadow 0.3s ease;
}

.avatar-preview {
    flex-shrink: 0;
    padding: 0.5rem;
}

.avatar-preview img {
    border: 3px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.avatar-upload {
    padding: 0.5rem;
}

.avatar-preview img:hover {
    border-color: var(--primary-color);
}

.form-control, .form-select {
    border: 1px solid #dee2e6;
    padding: 0.6rem 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
}

.invalid-feedback {
    font-size: 0.85rem;
}

.form-select {
    width: 100%;
    border-radius: 5px;
}

@media (max-width: 768px) {
    .border-end {
        border: none !important;
    }
}
</style>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Bootstrap form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()

    // Auto-fill password fields when school ID changes
    $('#school_id').on('input', function() {
        var schoolId = $(this).val();
        $('#password').val(schoolId);
        $('#cpass').val(schoolId);
    });

    // Image preview with fade effect
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').fadeOut(200, function() {
                    $(this).attr('src', e.target.result).fadeIn(200);
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // File input handling
        $('#customFile').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('.avatar-upload .form-text').text('Selected: ' + fileName);
            } else {
                $('.avatar-upload .form-text').text('Square image, 200x200px or larger');
            }
        });

        // Form submission
        $('#manage_student').submit(function(e){
            e.preventDefault();
            if (this.checkValidity()) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=save_student',
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function(resp){
                        if(resp == 1){
                            alert_toast('Student successfully added!', "success");
                            setTimeout(function(){
                                location.replace('staff_cme_index.php?page=student_list')
                            },750)
                        } else if(resp == 2){
                            $('#email').addClass("is-invalid");
                            $('#email').next('.invalid-feedback').text('This email address is already registered.');
                            end_load();
                        } else if(resp == 3){
                            $('#school_id').addClass("is-invalid");
                            $('#school_id').next('.invalid-feedback').text('This School ID is already in use.');
                            end_load();
                        }
                    }
                });
            }
        });

        // Load CME classes on page load
        function loadCMEClasses() {
            var classSelect = $('#class_id');
            classSelect.html('<option value="">Loading...</option>');
            
            $.ajax({
                url: 'ajax.php?action=get_classes_by_department_modified',
                method: 'POST',
                data: {department: 'CME'},
                dataType: 'json',
                success: function(response){
                    classSelect.html('<option value="">Select Class</option>');
                    
                    if(response.status === 'success' && response.classes.length > 0) {
                        response.classes.forEach(function(classItem){
                            var displayText = classItem.curriculum + ' ' + classItem.level + ' - ' + classItem.section;
                            classSelect.append('<option value="' + classItem.id + '">' + displayText + '</option>');
                        });
                        
                        <?php if(isset($class_id)): ?>
                        classSelect.val('<?php echo $class_id ?>');
                        <?php endif; ?>
                    } else {
                        classSelect.append('<option value="">No classes found</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    classSelect.html('<option value="">Error loading classes</option>');
                }
            });
        }

        // Load classes immediately when page loads
        loadCMEClasses();
    });
</script>