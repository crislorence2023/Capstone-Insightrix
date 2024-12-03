<?php include('./db_connect.php');?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="./index.php?page=manage_class_subject" class="btn btn-primary btn-sm btn-block col-md-3 float-right">
                        <i class="fa fa-plus"></i> Assign New Subject
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="class-subject-list">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Subject Code</th>
                                <th>Subject</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $qry = $conn->query("SELECT cs.*, 
                                concat(cl.curriculum,' ',cl.level,' - ',cl.section) as class 
                                FROM class_subject cs 
                                INNER JOIN class_list cl ON cs.class_id = cl.id 
                                ORDER BY cl.curriculum ASC, cl.level ASC, cl.section ASC");
                            while($row = $qry->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $row['class'] ?></td>
                                <td><?php echo $row['subject_code'] ?></td>
                                <td><?php echo $row['subject_name'] ?></td>
                                <td class="text-center">
                                    <a href="./index.php?page=manage_class_subject&id=<?php echo $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger delete_class_subject" data-id="<?php echo $row['id'] ?>">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Initialize DataTable
    $('#class-subject-list').DataTable();
    
    // Handle Delete Class Subject button click
    $('.delete_class_subject').click(function(){
        _conf("Are you sure to delete this class subject?","delete_class_subject",[$(this).attr('data-id')]);
    });
});

// Function to handle deletion
function delete_class_subject($id){
    start_load();
    $.ajax({
        url:'ajax.php?action=delete_class_subject',
        method:'POST',
        data:{id:$id},
        success:function(resp){
            if(resp==1){
                alert_toast("Data successfully deleted",'success');
                setTimeout(function(){
                    location.reload();
                },1500);
            }
        }
    });
}
</script>