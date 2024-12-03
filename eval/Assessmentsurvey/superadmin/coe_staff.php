<?php
// Database connection and initial setup
include './db_connect.php';
$department = 'COE';

// Fetch existing staff from COE staff table
$staff_query = "SELECT * FROM coe_staff ORDER BY lastname ASC";
$staff_result = $conn->query($staff_query);
?>

<div class="container py-4 rounded-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-light bg-gradient text-white py-3">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 flex-grow-1 text-bold">Manage <?php echo $department; ?> Staff</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="location.href='index.php?page=new_coe-staff'">
                    Add New Staff
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        while($row = $staff_result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-staff" 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-firstname="<?php echo $row['firstname']; ?>"
                                    data-lastname="<?php echo $row['lastname']; ?>"
                                    data-email="<?php echo $row['email']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-staff" 
                                    data-id="<?php echo $row['id']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Replace Bootstrap modal with custom modal -->
<div class="custom-modal" id="addStaffModal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="modal-title">Add New Staff</h5>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <form id="staffForm" class="needs-validation" novalidate>
            <div class="custom-modal-body">
                <input type="hidden" name="id" id="staff_id">
                <input type="hidden" name="department" value="<?php echo $department; ?>">
                
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="firstname" required>
                    <div class="invalid-feedback">Please enter first name</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lastname" required>
                    <div class="invalid-feedback">Please enter last name</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                    <div class="invalid-feedback">Please enter a valid email</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                    <div class="invalid-feedback">Please enter password</div>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm close-modal">Close</button>
                <button type="submit" class="btn btn-primary">Update Staff</button>
            </div>
        </form>
    </div>
</div>

<!-- Add the custom modal styles -->
<style>
.btn-secondary.btn-sm {
    font-size: .9rem;
    padding: 4px;
}

.btn-secondary.btn-sm:hover {
    background-color: #0056b3;
    color: #fff;
}

.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 9999;
    overflow-y: auto;
}

.custom-modal-content {
    position: relative;
    background-color: #fff;
    margin: 2rem auto;
    padding: 0;
    width: 50%;
    max-width: 500px;
    border-radius: 5px;
    box-shadow: 0 3px 7px rgba(0,0,0,0.3);
    max-height: calc(100vh - 4rem);
    display: flex;
    flex-direction: column;
    z-index: 10000;
}

.custom-modal-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-modal-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
}

.custom-modal-footer {
    padding: 1rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    color: #666;
}

.close-modal:hover {
    color: #000;
}
</style>

<script>
$(document).ready(function() {
    // Modal functions
    function showModal(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function hideModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal events
    $('.close-modal').click(function() {
        hideModal(document.getElementById('addStaffModal'));
    });

    $(window).click(function(event) {
        if (event.target.className === 'custom-modal') {
            hideModal(event.target);
        }
    });

    // Edit staff - update to use custom modal
    $('.edit-staff').click(function() {
        var modal = document.getElementById('addStaffModal');
        modal.querySelector('.modal-title').textContent = 'Edit Staff';
        modal.querySelector('[name="id"]').value = $(this).data('id');
        modal.querySelector('[name="firstname"]').value = $(this).data('firstname');
        modal.querySelector('[name="lastname"]').value = $(this).data('lastname');
        modal.querySelector('[name="email"]').value = $(this).data('email');
        modal.querySelector('[name="password"]').required = false;
        showModal(modal);
    });

    // Update addNewStaff function
    window.addNewStaff = function() {
        var modal = document.getElementById('addStaffModal');
        modal.querySelector('.modal-title').textContent = 'Add New Staff';
        modal.querySelector('form').reset();
        modal.querySelector('[name="id"]').value = '';
        modal.querySelector('[name="password"]').required = true;
        showModal(modal);
    };

    // Form submission for updates only
    $('#staffForm').submit(function(e) {
        e.preventDefault();
        if (this.checkValidity()) {
            $.ajax({
                url: 'ajax.php?action=update_staff_<?php echo strtolower($department); ?>',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast('Staff successfully updated', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 750);
                    } else if (resp == 2) {
                        alert_toast('Email already exists', 'warning');
                    }
                }
            });
        }
        $(this).addClass('was-validated');
    });

    // Delete staff
    $('.delete-staff').click(function() {
        if (confirm('Are you sure you want to delete this staff member?')) {
            $.ajax({
                url: 'ajax.php?action=delete_staff_<?php echo strtolower($department); ?>',
                method: 'POST',
                data: {id: $(this).data('id')},
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast('Staff successfully deleted', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 750);
                    }
                }
            });
        }
    });
});
</script>