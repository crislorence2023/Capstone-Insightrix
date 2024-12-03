<?php include 'db_connect.php' ?>
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold ">Academic Years</h6>
            
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover border-bottom mb-0" id="list" style="width:100%">
                    <thead>
                        <tr class="bg-light">
                            <th class="py-3 px-4" style="width: 8%">#</th>
                            <th class="py-3 px-4" style="width: 25%">Academic Year</th>
                            <th class="py-3 px-4" style="width: 20%">Semester</th>
                            <th class="py-3 px-4 text-center" style="width: 15%">Questions</th>
                            <th class="py-3 px-4 text-center" style="width: 15%">Answered</th>
                            <th class="py-3 px-4" style="width: 17%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM academic_list order by abs(year) desc,abs(semester) desc ");
                        while($row= $qry->fetch_assoc()):
                            $questions = $conn->query("SELECT * FROM question_list where academic_id ={$row['id']} ")->num_rows;
                            $answers = $conn->query("SELECT * FROM evaluation_list where academic_id ={$row['id']} ")->num_rows;
                        ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo $i++ ?></td>
                            <td class="py-3 px-4 font-weight-medium"><?php echo $row['year'] ?></td>
                            <td class="py-3 px-4"><?php echo $row['semester'] ?></td>
                            <td class="py-3 px-4 text-center">
                                <span class="badge bg-info rounded-pill px-3"><?php echo number_format($questions) ?></span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="badge bg-success rounded-pill px-3"><?php echo number_format($answers) ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <a href="indexsuperadmin.php?page=manage_questionnaire&id=<?php echo $row['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm d-flex align-items-center" style="width: fit-content">
                                    <i class="fa fa-list-alt me-2"></i>
                                    <span>Manage Survey</span>
                                </a>
                            </td>
                        </tr>    
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table {
    width: 100% !important;
    margin-bottom: 0;
}
.dataTables_wrapper {
    padding: 0;
    width: 100%;
}
.dataTables_wrapper .row {
    margin: 0;
    padding: 1rem;
}
.dataTables_wrapper .row:not(:last-child) {
    border-bottom: 1px solid #dee2e6;
}
.card {
    border: none;
    border-radius: 0.5rem;
}
.badge {
    font-weight: 500;
    font-size: 0.875rem;
}
.btn-sm {
    padding: 0.4rem 1rem;
}
/* DataTables specific styling */
.dataTables_length select {
    min-width: 4.5rem;
    padding: 0.375rem 1.75rem 0.375rem 0.75rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}
.dataTables_filter input {
    min-width: 250px;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}
.dataTables_info {
    padding-top: 0 !important;
}
.dataTables_paginate {
    margin-top: 0 !important;
}
.paginate_button {
    padding: 0.375rem 0.75rem;
    margin: 0 0.2rem;
}

.m-0{
    margin: 0;
    color:#1F2D3D;
            font-size: 20px;
            font-weight: 600;
}
/* Responsive adjustments */
@media (min-width: 1200px) {
    .container-fluid {
        padding-left: 2.5rem;
        padding-right: 2.5rem;
    }
}
</style>

<script>
$(document).ready(function(){
    $('#list').DataTable({
        responsive: true,
        pageLength: 10,
        autoWidth: true,
        language: {
            search: "",
            searchPlaceholder: "Search records...",
            lengthMenu: "_MENU_ entries per page",
        },
        dom: `
            <'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>
            <'row'<'col-sm-12'tr>>
            <'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>
        `,
        initComplete: function() {
            // Adjust table width after initialization
            $(this).DataTable().columns.adjust();
        }
    });

    // Event handlers
    $('.new_academic').click(function(){
        uni_modal("New Academic Year","<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php");
    });

    // Adjust table columns on window resize
    $(window).resize(function() {
        $('#list').DataTable().columns.adjust();
    });
});

function delete_academic($id){
    start_load()
    $.ajax({
        url:'ajax.php?action=delete_academic',
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

function make_default($id){
    start_load()
    $.ajax({
        url:'ajax.php?action=make_default',
        method:'POST',
        data:{id:$id},
        success:function(resp){
            if(resp==1){
                alert_toast("Default Academic Year Updated",'success')
                setTimeout(function(){
                    location.reload()
                },1500)
            }
        }
    })
}
</script>