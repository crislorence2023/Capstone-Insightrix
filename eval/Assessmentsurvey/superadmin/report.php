<?php $faculty_id = isset($_GET['fid']) ? $_GET['fid'] : '' ; ?>
<?php 
function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}

function get_historical_data($conn, $faculty_id, $subject_id, $class_id) {
    $query = "SELECT a.year, a.semester, AVG(r.rate) as average_rating 
              FROM evaluation_list e 
              JOIN academic_year a ON e.academic_id = a.id
              JOIN restriction_list r ON e.id = r.evaluation_id
              WHERE e.faculty_id = ? AND e.subject_id = ? AND e.class_id = ?
              GROUP BY a.id
              ORDER BY a.year ASC, a.semester ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $faculty_id, $subject_id, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $historical_data = array();
    while ($row = $result->fetch_assoc()) {
        $historical_data[] = array(
            'semester' => $row['year'] . ' - ' . ordinal_suffix($row['semester']) . ' Semester',
            'average_rating' => $row['average_rating']
        );
    }
    
    return $historical_data;
}

function getRatingColor($rating) {
    if ($rating <= 2) return 'danger';
    if ($rating <= 4) return 'warning';
    return 'success';
}

// Helper function for performance text
function getPerformanceText($rating) {
    if ($rating <= 2) return 'Needs Improvement';
    if ($rating <= 4) return 'Satisfactory';
    return 'Excellent';
}
?>


<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Modern Color Scheme and Variables */
:root {
  --primary: #4361ee;
  --primary-light: #4895ef;
  --secondary: #3f37c9;
  --success: #4cc9f0;
  --info: #4895ef;
  --warning: #f72585;
  --danger: #e63946;
  --light: #f8f9fa;
  --dark: #212529;
  --teal: #14b8a6;
  --teal-light: #5eead4;
  --teal-dark: #0d9488;
  --gray-100: #f8f9fa;
  --gray-200: #e9ecef;
  --gray-300: #dee2e6;
  --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
  --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
  --radius-sm: 0.375rem;
  --radius-md: 15px;
  --radius-lg: 0.75rem;
}

/* Class List Section Enhancements */
#class-list {
  
  overflow: hidden;
  padding: 1rem;
  margin-botton: 1rem;
  
  
}

#class-list .list-group-item:first-child {
  border-top: none;
}

#class-list .list-group-item:last-child {
  border-radius: var(--radius-md);
}

#class-list .list-group-item {
    border-radius: var(--radius-md);
  padding: 1rem 1.25rem;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
  margin-top: 5px;
}

#class-list .list-group-item:hover {
  background-color: teal;
  color: white;
}

#class-list .list-group-item:focus {
  outline: none;
  background-color: teal;
  color: var(--dark);
  
}

#class-list .list-group-item.active {
  background-color: teal;
  border: none;
  
  color: white;
}

#class-list .list-group-item.active:hover {
  background-color: var(--teal-dark);
}

/* Add subtle hover animation */
#class-list .list-group-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  
  background-color: var(--teal);
  transform: scaleY(0);
  transition: transform 0.2s ease;
}

#class-list .list-group-item:hover::before,
#class-list .list-group-item.active::before {
  transform: scaleY(1);
}

/* Card Header for Class List */
.card .card-header:first-child {
    border-radius: 15px 15px 0px 0px;
}

/* Ensure consistent border radius for the entire card */
.card {
  border-radius: var(--radius-md);
  overflow: hidden;
}

/* Base Container Styles */
.container-fluid {
  
  max-width: 1440px;
  margin: 0 auto;
}

/* Card Enhancements */
.card {
  border: none;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-md);
  transition: all 0.2s ease;
  background: white;
  margin-bottom: 1.5rem;

}

.card:hover {
  box-shadow: var(--shadow-lg);
}

.card-header {
  background: none;
  border-bottom: 1px solid var(--gray-200);
  
  
}

.card-body {
  padding: 1.5rem;
}

/* Faculty Selection Section */
.faculty-selection .card {
  background: linear-gradient(45deg, var(--primary-light), var(--primary));
}

.form-select, .form-control {
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-sm);
  padding: 0.625rem 1rem;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.form-select:focus, .form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.1);
}

/* Buttons */
.btn {
  padding: 0.625rem 1.25rem;
  border-radius: var(--radius-sm);
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-info {
  background: var(--info);
  border: none;
  color: white;
}

.btn-info:hover {
  background: var(--primary);
  transform: translateY(-1px);
}

/* Rating Statistics Card */
.bg-light {
  background: var(--gray-100) !important;
}

.progress {
  height: 0.75rem;
  border-radius: var(--radius-lg);
  background: var(--gray-200);
}

.progress-bar {
  border-radius: var(--radius-lg);
  background: linear-gradient(45deg, var(--success), var(--primary-light));
}

/* Rating Legend */
.alert-info {
  background: none;
  border: 1px solid rgba(72, 149, 239, 0.2);
  border-radius: var(--radius-md);
  color: var(--dark);
  font-size: 1.2rem;
}

/* Tables */
.table {
  margin-bottom: 0;
}

.table thead th {
  background: var(--gray-100);
  border-bottom: 2px solid var(--gray-200);
  font-weight: 600;
  color: var(--dark);
  padding: 1rem;
}

.table td {
  padding: 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--gray-200);
}

/* Charts Section */
.charts-section {
  background: white;
  border-radius: var(--radius-lg);
  padding: 1.5rem;
}

canvas {
  max-width: 100%;
}

/* Modal Enhancements */
.modal-content {
  border: none;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
}

.modal-header {
  border-bottom: 1px solid var(--gray-200);
  padding: 1.25rem;
}

.modal-body {
  padding: 1.5rem;
}

/* Search Input */
#searchInput {
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-sm);
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .container-fluid {
    padding: 1rem;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .table-responsive {
    margin: 0 -1rem;
  }
  
  .col-md-3, .col-md-9 {
    padding: 0;
  }
}

/* Print Styles */
@media print {
  .card {
    box-shadow: none;
    border: 1px solid var(--gray-300);
  }
  
  .container-fluid {
    padding: 0;
  }
  
  .no-print {
    display: none !important;
  }
}

/* Statistics Cards */
.statistics-card {
  background: white;
  border-radius: var(--radius-md);
  padding: 1.5rem;
}

.statistics-card h4 {
  color: var(--primary);
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.statistics-card h6 {
  color: var(--gray-600);
  font-size: 0.875rem;
  font-weight: 500;
}

.print-btn{
    margin-left: auto;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
}
.text-bold{
    font-size: 2rem;
}

/* Performance Message */
#performanceMessage {
  font-size: 0.875rem;
  font-weight: 500;
  margin-top: 0.75rem;
}

/* Responsive utilities */
.g-3 {
    --bs-gutter-y: 1rem;
    --bs-gutter-x: 1rem;
}

@media (min-width: 768px) {
    .w-md-auto {
        width: auto !important;
    }
    
    .text-md-end {
        text-align: right !important;
    }
}

/* Button Styles */
#loadPreviousDataBtn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: var(--radius-sm);
}

/* Adjust text-muted color for better contrast */
.text-muted {
    color: #6c757d !important;
    font-size: 0.9rem;
    display: inline-block;
}

strong {
    font-size: 0.95rem;
    font-weight: 600;
}

.d-flex {
    display: flex !important;
    align-items: center;
}

.ms-2 {
    margin-left: 0.5rem !important;
}
    </style>
<div class="container-fluid py-2">
    <!-- Faculty Selection Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center g-4">
                <div class="col-md-3 col-12 mb-2 mb-md-0">
                    <label for="faculty_id" class="form-label fw-bold">Select Faculty</label>
                </div>
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <select id="faculty_id" class="form-select select2">
                        <option value=""></option>
                        <?php 
                        $faculty = $conn->query("SELECT *, concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
                        $f_arr = array();
                        $fname = array();
                        while($row=$faculty->fetch_assoc()):
                            $f_arr[$row['id']]= $row;
                            $fname[$row['id']]= ucwords($row['name']);
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>>
                            <?php echo ucwords($row['name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3 col-12 text-md-end text-center">
                    <button class="btn btn-success w-100 w-md-auto" id="loadPreviousDataBtn">
                        <i class="fa fa-history"></i> Load Previous Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row">
        <!-- Class List Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-light text-white">
                    <h5 class="card-title mb-0">Class List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="class-list">
                        <!-- Classes will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluation Report Section -->
        <div class="col-md-9">
            <div class="card shadow-sm" id="printable">
                <div class="card-header  text-black d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-bold mb-0">Evaluation Report</h5>
                    <button class="btn btn-light btn-sm print-btn" id="print-btn">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
                <div class="card-body">
                    <!-- Report Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-2 d-flex">
                                <span class="text-muted" style="width: 80px;">Faculty:</span>
                                <strong class="ms-2" id="fname"></strong>
                            </div>
                            <div class="mb-2 d-flex">
                                <span class="text-muted" style="width: 80px;">Class:</span>
                                <strong class="ms-2" id="classField"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 d-flex">
                                <span class="text-muted" style="width: 120px;">Academic Year:</span>
                                <strong class="ms-2" id="ay"><?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</strong>
                            </div>
                            <div class="mb-2 d-flex">
                                <span class="text-muted" style="width: 120px;">Subject:</span>
                                <strong class="ms-2" id="subjectField"></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Statistics -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2">Total Students Evaluated</h6>
                                    <h4 class="mb-0" id="tse">0</h4>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2">Overall Average Rating</h6>
                                    <h4 class="mb-0"><span id="overallRating">0</span> / 5</h4>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 20px;">
                                    <div id="ratingProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5">0%</div>
                                </div>
                                <div id="performanceMessage" class="mt-2 text-center"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0 text-bold">Historical Performance</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="historicalChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Legend -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">Rating Legend</h6>
                        <div class="row">
                            <div class="col"><small>5 = Strongly Agree</small></div>
                            <div class="col"><small>4 = Agree</small></div>
                            <div class="col"><small>3 = Uncertain</small></div>
                            <div class="col"><small>2 = Disagree</small></div>
                            <div class="col"><small>1 = Strongly Disagree</small></div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                   

                    <!-- Evaluation Questions -->
                    <?php 
                    $q_arr = array();
                    $criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
                    while($crow = $criteria->fetch_assoc()):
                    ?>
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><?php echo $crow['criteria'] ?></h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="w-50">Question</th>
                                            <th class="text-center">1</th>
                                            <th class="text-center">2</th>
                                            <th class="text-center">3</th>
                                            <th class="text-center">4</th>
                                            <th class="text-center">5</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tr-sortable">
                                        <?php 
                                        $questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
                                        while($row=$questions->fetch_assoc()):
                                        $q_arr[$row['id']] = $row;
                                        ?>
                                        <tr>
                                            <td><?php echo $row['question'] ?></td>
                                            <?php for($c=1;$c<=5;$c++): ?>
                                            <td class="text-center">
                                                <span class="rate_<?php echo $c.'_'.$row['id'] ?> rates"></span>
                                            </td>
                                            <?php endfor; ?>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>

                    <!-- Additional Chart -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 text-bold">Evaluation Summary</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="evaluationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Previous Semester Modal -->
<div class="modal fade" id="previousSemesterModal" tabindex="-1" role="dialog" aria-labelledby="previousSemesterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previousSemesterModalLabel">Previous Semester Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Search input for filtering table rows -->
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search for subjects..." onkeyup="searchTable()">
        <div id="previousSemesterData">
          <!-- Data will be populated here -->
        </div>
      </div>
    </div>
  </div>
</div>






<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function(){
    let evaluationChart = null;
    let historicalChart = null;

    $('#loadPreviousDataBtn').click(function(){
        loadPreviousSemesterData();
    });

    $('#faculty_id').change(function(){
        if($(this).val() > 0) {
            window.history.pushState({}, null, './index.php?page=report&fid='+$(this).val());
            load_class();
        } else {
            clearAllData();
        }
    });

    if($('#faculty_id').val() > 0)
        load_class();

    function load_class(){
        start_load();
        var fname = <?php echo json_encode($fname) ?>;
        $('#fname').text(fname[$('#faculty_id').val()]);
        $.ajax({
            url:"ajax.php?action=get_class",
            method:'POST',
            data:{fid:$('#faculty_id').val()},
            error:function(err){
                console.log(err);
                alert_toast("An error occured",'error');
                end_load();
            },
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp);
                    if(Object.keys(resp).length <= 0 ){
                        $('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to be display.</a>');
                        clearAllData();
                    } else {
                        $('#class-list').html('');
                        Object.keys(resp).map(k=>{
                        $('#class-list').append('<a href="javascript:void(0)" data-json=\''+JSON.stringify(resp[k])+'\' data-id="'+resp[k].id+'" class="list-group-item list-group-item-action show-result">'+resp[k].class+' - '+resp[k].subj+'</a>');
                        });
                    }
                }
            },
            complete:function(){
                end_load();
                anchor_func();
                if('<?php echo isset($_GET['rid']) ?>' == 1){
                    $('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]').trigger('click');
                }else{
                    $('.show-result').first().trigger('click');
                }
            }
        });
    }

   

    function getOrdinal(n) {
        var s = ["th", "st", "nd", "rd"];
        var v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    }

    function anchor_func(){
        $('.show-result').click(function(){
            var vars = [], hash;
            var data = $(this).attr('data-json');
                data = JSON.parse(data);
            var _href = location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < _href.length; i++)
                {
                    hash = _href[i].split('=');
                    vars[hash[0]] = hash[1];
                }
            window.history.pushState({}, null, './index.php?page=report&fid='+vars.fid+'&rid='+data.id);
            load_report(vars.fid, data.sid, data.id);
            $('#subjectField').text(data.subj);
            $('#classField').text(data.class);
            $('.show-result.active').removeClass('active');
            $(this).addClass('active');
        });
    }

    function clearAllData() {
        $('.rates').text('-');
        $('#tse').text('-');
        $('#print-btn').hide();
        $('#overallRating').text('-');
        $('#ratingProgressBar').css('width', '0%');
        $('#ratingProgressBar').attr('aria-valuenow', '0');
        $('#ratingProgressBar').text('0%');
        $('#performanceMessage').html('');
        $('#subjectField').text('-');
        $('#classField').text('-');

        if (evaluationChart) {
            evaluationChart.destroy();
            evaluationChart = null;
        }
        if (historicalChart) {
            historicalChart.destroy();
            historicalChart = null;
        }

        $('#previousSemesterData').html('');
    }

    function loadPreviousSemesterData(){
    start_load();
    $.ajax({
        url: "ajax.php?action=get_previous_semester_data",
        method: "POST",
        data: {faculty_id: $('#faculty_id').val()},
        success: function(resp){
            try {
                var html = '<style>' +
                          '.rating-indicator { position: relative; display: inline-block; }' +
                          '.rating-indicator .tooltip-text { visibility: hidden; background-color: #333;' +
                          'color: white; text-align: center; padding: 5px; border-radius: 6px;' +
                          'position: absolute; z-index: 1; bottom: 125%; left: 50%;' +
                          'margin-left: -60px; font-size: 12px; }' +
                          '.rating-indicator:hover .tooltip-text { visibility: visible; }' +
                          '</style>';

                var data = JSON.parse(resp);
                if(data.length === 0) {
                    $('#previousSemesterData').html('<p>No previous semester data available for this instructor.</p>');
                } else {
                    data.forEach(function(semester){
                        html += '<h4>' + semester.academic.year + ' - ' + getOrdinal(semester.academic.semester) + ' Semester</h4>';
                        html += '<table class="table table-bordered">';
                        html += '<thead><tr>' + 
                               '<th>Subject</th>' + 
                               '<th>Total Evaluations</th>' + 
                               '<th>Average Rating</th>' +
                               '</tr></thead>';
                        html += '<tbody>';
                        if(semester.subjects.length === 0) {
                            html += '<tr><td colspan="3">No subjects found for this semester.</td></tr>';
                        } else {
                            semester.subjects.forEach(function(subject){
                                var rating = parseFloat(subject.evaluation_summary.average_rating);
                                var indicatorColor;
                                var performanceText;
                                
                                // Determine indicator color and performance text
                                if (rating <= 2) {
                                    indicatorColor = '#ff4444';
                                    performanceText = 'Low Performance';
                                } else if (rating > 2 && rating <= 4) {
                                    indicatorColor = '#ffa500';
                                    performanceText = 'Moderate Performance';
                                } else {
                                    indicatorColor = '#00C851';
                                    performanceText = 'Excellent Performance';
                                }

                                html += '<tr>';
                                html += '<td>' + subject.code + ' - ' + subject.subject + '</td>';
                                html += '<td>' + subject.evaluation_summary.total_evaluations + '</td>';
                                html += '<td>' +
                                       '<div class="rating-indicator">' +
                                       '<div style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; ' +
                                       'background-color: ' + indicatorColor + '; margin-right: 5px;"></div>' +
                                       '<span class="tooltip-text">' + performanceText + '</span>' +
                                       rating.toFixed(2) +
                                       '</div></td>';
                                html += '</tr>';
                            });
                        }
                        html += '</tbody></table>';
                    });
                    $('#previousSemesterData').html(html);
                }
                $('#previousSemesterModal').modal('show');
            } catch (e) {
                console.error("Error parsing data:", e);
                alert_toast("Error parsing data", 'error');
            }
        },
        complete: function(){
            end_load();
        }
    });
}

    function load_report($faculty_id, $subject_id, $class_id){
        if($('#preloader2').length <= 0)
            start_load();
        
        $.ajax({
            url:'ajax.php?action=get_report',
            method:"POST",
            data:{faculty_id: $faculty_id, subject_id: $subject_id, class_id: $class_id},
            error:function(err){
                console.log(err);
                alert_toast("An Error Occured.","error");
                end_load();
            },
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp);
                    if(Object.keys(resp).length <= 0){
                        clearAllData();
                    } else {
                        $('#print-btn').show();
                        $('#tse').text(resp.tse);
                        $('.rates').text('-');
                        var data = resp.data;

                        var averages = [];
                        Object.keys(data).map(q => {
                            var totalResponses = 0;
                            var weightedSum = 0;
                            Object.keys(data[q]).map(r => {
                                var responseCount = data[q][r];
                                totalResponses += responseCount;
                                weightedSum += responseCount * r;
                            });
                            var average = weightedSum / totalResponses;
                            averages.push(average.toFixed(2));
                        });

                        var totalSum = 0;
                        for (var i = 0; i < averages.length; i++) {
                            totalSum += parseFloat(averages[i]);
                        }
                        var overallAverage = (totalSum / averages.length).toFixed(2);
                        $('#overallRating').text(overallAverage);

                        var progressPercentage = (overallAverage / 5) * 100;
                        $('#ratingProgressBar').css('width', progressPercentage + '%');
                        $('#ratingProgressBar').attr('aria-valuenow', overallAverage);
                        $('#ratingProgressBar').text(progressPercentage.toFixed(0) + '%');

                        var performanceMessage = '';
                        if (overallAverage <= 2) {
                            performanceMessage = '<span style="color: red;">Low Performance</span>';
                        } else if (overallAverage > 2 && overallAverage <= 4) {
                            performanceMessage = '<span style="color: orange;">Moderate Performance</span>';
                        } else if (overallAverage > 4 && overallAverage <= 5) {
                            performanceMessage = '<span style="color: blue;">High Performance</span>';
                        }
                        $('#performanceMessage').html(performanceMessage);

                        if (evaluationChart) {
                            evaluationChart.destroy();
                        }
                        var ctx = document.getElementById('evaluationChart').getContext('2d');
                        evaluationChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(data).map(q => 'Question ' + q),
                                datasets: [{
                                    label: 'Average Rating',
                                    data: averages,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 5
                                    }
                                }
                            }
                        });

                        Object.keys(data).map(q => {
                            Object.keys(data[q]).map(r => {
                                $('.rate_' + r + '_' + q).text(parseFloat(data[q][r]).toFixed(2) + '%');
                            });
                        });
                    }
                }
            },
            complete:function(){
                loadHistoricalData($faculty_id, $subject_id, $class_id);
            }
        });
    }

    function loadHistoricalData($faculty_id, $subject_id, $class_id) {
        $.ajax({
            url: 'get_historical_data.php',
            method: 'POST',
            data: {
                faculty_id: $faculty_id,
                subject_id: $subject_id,
                class_id: $class_id
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    var historicalData = result.data;
                    
                    var labels = historicalData.map(item => item.semester);
                    var ratings = historicalData.map(item => parseFloat(item.average_rating));

                    if (historicalChart) {
                        historicalChart.destroy();
                    }
                    var historicalCtx = document.getElementById('historicalChart').getContext('2d');
                    historicalChart = new Chart(historicalCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Average Rating per Semester',
                                data: ratings,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 5
                                }
                            }
                        }
                    });
                } else {
                    console.error("Error loading historical data:", result.message);
                    alert_toast("Error loading historical data", 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert_toast("Error loading historical data", 'error');
            },
            complete: function() {
                end_load();
            }
        });
    }









    $('#print-btn').click(function(){
        start_load();
        var ns =$('noscript').clone();
        var content = $('#printable').html();
        ns.append(content);
        var nw = window.open("Report","_blank","width=900,height=700");
        nw.document.write(ns.html());
        nw.document.close();
        nw.print();
        setTimeout(function(){
            nw.close();
            end_load();
        },750);
    });
});

function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toLowerCase();
    table = document.querySelector("#previousSemesterData table");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";

        td = tr[i].getElementsByTagName("td");
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}


function getOrdinal(n) {
    var s = ["th", "st", "nd", "rd"];
    var v = n % 100;
    return n + (s[(v - 20) % 10] || s[v] || s[0]);
}
</script>