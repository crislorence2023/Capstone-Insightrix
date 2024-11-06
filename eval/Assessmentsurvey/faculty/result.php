<?php $faculty_id = $_SESSION['login_id'] ?>
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

$faculty_id = $_SESSION['login_id'];
$academic_id = $_SESSION['academic']['id'];

// Get all subjects for this instructor in current semester
$query = "SELECT 
    r.subject_id,
    s.code,
    s.subject,
    (SELECT AVG(ea.rate)
    FROM evaluation_answers ea 
    INNER JOIN evaluation_list el ON ea.evaluation_id = el.evaluation_id
    WHERE el.faculty_id = r.faculty_id 
    AND el.subject_id = r.subject_id
    AND el.academic_id = r.academic_id) as average_rating
FROM restriction_list r
INNER JOIN subject_list s ON r.subject_id = s.id
WHERE r.faculty_id = {$faculty_id} 
AND r.academic_id = {$academic_id}
GROUP BY r.subject_id";

$result = $conn->query($query);

$perf_distribution = array(
    'low' => 0,
    'moderate' => 0,
    'high' => 0
);

$total_subjects = 0;
while($row = $result->fetch_assoc()) {
    $total_subjects++;
    $rating = floatval($row['average_rating']);
    
    if($rating <= 2) {
        $perf_distribution['low']++;
    } else if($rating > 2 && $rating <= 4) {
        $perf_distribution['moderate']++;
    } else {
        $perf_distribution['high']++;
    }
}
?>
<style>
  .historical{
    margin-top: 2rem;
    font-size: 18px !important;
    padding-left: 20px;
    padding-top: 2px;
    padding-bottom: 2px;
    width: 60%;
    color: black;
    font-weight: 600;
    
    border-radius:5px;
}
.row{
    border-radius: 15px;
}

.evaluation-container {
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.report-meta {
    text-align: right;
}

.report-meta .badge {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    margin-bottom: 2rem;
}

.info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.info-card-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.info-card-header i {
    font-size: 1.5rem;
    margin-right: 1rem;
    color: #0d6efd;
}

.info-card-header h2 {
    margin: 0;
    font-size: 1.25rem;
    color: #495057;
}

.info-card-body {
    padding: 0.5rem 0;
}

.detail-item {
    display: flex;
    margin-bottom: 0.75rem;
    align-items: center;
}

.detail-item label {
    font-weight: 600;
    width: 100px;
    margin: 0;
    color: #6c757d;
}

.detail-item .detail-value {
    color: #212529;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #0d6efd;
}

.rating-section {
    margin-top: 2rem;
}

.progress-container {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.progress-header h3 {
    margin: 0;
    font-size: 1.25rem;
    color: #495057;
}

.custom-progress {
    height: 1.5rem;
    background-color: #e9ecef;
    border-radius: 0.5rem;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(45deg, #0d6efd, #0a58ca);
    transition: width 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-label {
    color: white;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.performance-indicator {
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
}

.list-group-item.active {
    background-color: #e6fffd !important;
    border-left-color: #20b2aa !important;
    color: #000 !important;
    border-color: #20b2aa !important;
}

.list-group-item:focus {
    background-color: #e6fffd;
    border-left-color: #20b2aa;
    box-shadow: 0 0 0 0.2rem rgba(32, 178, 170, 0.25);
    outline: none;
}
.list-group-item {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 0.5rem !important;
    border-radius: 10px;
    margin: 5px 5px 5px 5px
}
.list-group{
    border-radius: 10px !important;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .evaluation-container {
        padding: 1rem;
    }
}

    </style>


<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12 mb-1">
            <div class="d-flex justify-content-end w-100 gap-3 mb-2">
                <button class="btn btn-sm btn-info bg-gradient-info me-2" id="loadPreviousDataBtn"><i class="fa fa-history"></i> Load Previous Semester Data</button>
              
            </div>
            <div class="d-flex justify-content-end w-100 gap-3 mb-2">
               
                <button class="btn btn-sm btn-success bg-gradient-success" style="display:none" id="print-btn"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="bg-white">
                <div class="list-group" style="background-color: white" id="class-list">
                    
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="callout" style="call-out" id="printable">
                <div>
                <div class="evaluation-header mb-4">
                    <p class="faculty-title" style="font-weight: bold; font-size: 30px">Evaluation Report</p>
                    <hr>
                    

                    <div class="evaluation-container">
    <!-- Header Section -->
    <div class="report-header">
       
        <div class="report-meta">
            <span class="callout badge bg-teal">Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></span>
        </div>
    </div>

    <!-- Main Info Cards -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-header">
                <i class="fas fa-chalkboard-teacher"></i>
                <h2>Class Details</h2>
            </div>
            <div class="info-card-body">
                <div class="detail-item">
                    <label>Class:</label>
                    <span id="classField" class="detail-value"></span>
                </div>
                <div class="detail-item">
                    <label>Subject:</label>
                    <span id="subjectField" class="detail-value"></span>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-header">
                <i class="fas fa-chart-bar"></i>
                <h2>Evaluation Statistics</h2>
            </div>
            <div class="info-card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Total Students</div>
                        <div class="stat-value" id="tse">-</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Average Rating</div>
                        <div class="stat-value"><span id="overallRating">-</span><small>/5</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Progress Section -->
    <div class="rating-section">
        <div class="progress-container">
            <div class="progress-header">
                <h3>Overall Performance</h3>
                <div id="performanceMessage" class="performance-indicator"></div>
            </div>
            <div class="custom-progress">
                <div id="ratingProgressBar" class="progress-bar" role="progressbar" 
                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="5">
                    <span class="progress-label">0%</span>
                </div>
            </div>
        </div>
    </div>

    <div id="performanceMessage" class="mb-4"></div>

    <div class="card border-info shadow-sm mb-4">
        <div class="container mt-4 mb-3 bg-light">
            <p class="historical callout mb-3 bg-teal">Historical Performance</p>
            <canvas id="historicalChart" width="100%" height="50"></canvas>
        </div>
    </div>
</div>



                
            

                <div class="card-body">
            <div id="comments-section" class="mb-3">
                <!-- Comments will be loaded here -->
            </div>
        </div>

        <div class="card border-info shadow-sm mb-4">
    <div class="container mt-4 mb-3 bg-light">
        <p class="callout mb-3 bg-teal">Subject Performance Distribution (Current Semester)</p>
        <canvas id="performanceDistChart" width="100%" height="50"></canvas>
    </div>
</div>

    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-star-fill me-2"></i>Rating Legend</h5>
    </div>
    <div class="card-body text-secondary">
        <div class="d-flex flex-wrap justify-content-around text-center">
            <div class="p-2">
                <span class="fw-bold text-dark">5</span> = Strongly Agree
            </div>
            <div class="p-2">
                <span class="fw-bold text-dark">4</span> = Agree
            </div>
            <div class="p-2">
                <span class="fw-bold text-dark">3</span> = Uncertain
            </div>
            <div class="p-2">
                <span class="fw-bold text-dark">2</span> = Disagree
            </div>
            <div class="p-2">
                <span class="fw-bold text-dark">1</span> = Strongly Disagree
            </div>
        </div>
    </div>



</div>


                <?php 
                $q_arr = array();
                $criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
                while($crow = $criteria->fetch_assoc()):
                ?>
                <table class="table table-condensed wborder">
                    <thead>
                        <tr class="bg-info">
                            <th class=" p-1"><b><?php echo $crow['criteria'] ?></b></th>
                            <th width="5%" class="text-center">1</th>
                            <th width="5%" class="text-center">2</th>
                            <th width="5%" class="text-center">3</th>
                            <th width="5%" class="text-center">4</th>
                            <th width="5%" class="text-center">5</th>
                        </tr>
                    </thead>
                    <tbody class="tr-sortable">
                        <?php 
                        $questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
                        while($row=$questions->fetch_assoc()):
                        $q_arr[$row['id']] = $row;
                        ?>
                        <tr class="bg-white">
                            <td class="p-1" width="40%">
                                <?php echo $row['question'] ?>
                            </td>
                            <?php for($c=1;$c<=5;$c++): ?>
                            <td class="text-center">
                                <span class="rate_<?php echo $c.'_'.$row['id'] ?> rates"></span>
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

              
                <?php endwhile; ?>
                <div class="container mt-4">
    <div class="card">
       
         
        </div>
                            </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="previousSemesterModal" tabindex="-1" role="dialog" aria-labelledby="previousSemesterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previousSemesterModalLabel">Previous Semester Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <!--  <div class="modal-body">
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search for subjects..." onkeyup="searchTable()">
                <div id="previousSemesterData">
                    Data will be populated here 
                </div>
            </div>-->
        </div>
    </div>
</div>





<noscript>
<style>
        *{
            font-family: 'Montserrat', sans-serif;
        }

        
        table{
            width:100%;
            border-collapse: collapse;
        }
        table.wborder tr,table.wborder td,table.wborder th{
            border:1px solid gray;
            padding: 3px
        }
        table.wborder thead tr{
            background: #6c757d linear-gradient(180deg,#828a91,#6c757d) repeat-x!important;
            color: #fff;
        }
        .text-center{
            text-align:center;
        } 
        .text-right{
            text-align:right;
        } 
        .text-left{
            text-align:left;
        } 
        .comment-box {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.comment-box:hover {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
    border-radius: 0.25rem;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: black !important;
}

.bg-success {
    background-color: #198754 !important;
    color: white !important;
}


.subject-text {
    width: 100% !important;
}

.badge {
    align-self: flex-start !important;
}

.gap-2 {
    gap: 0.5rem !important;
}

.me-2 {
    margin-right: 0.5rem !important;
}
.d-flex{
    margin-left: 2rem;
}

</style>






   
</noscript>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('performanceDistChart').getContext('2d');
    var performanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Low Performance\n(≤ 2.0)', 'Moderate Performance\n(2.1 - 4.0)', 'High Performance\n(> 4.0)'],
            datasets: [{
                label: 'Number of Subjects',
                data: [
                    <?php echo $perf_distribution['low']; ?>,
                    <?php echo $perf_distribution['moderate']; ?>,
                    <?php echo $perf_distribution['high']; ?>
                ],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.8)',  // red for low
                    'rgba(255, 193, 7, 0.8)',  // yellow for moderate
                    'rgba(13, 110, 253, 0.8)'  // blue for high
                ],
                borderColor: [
                    'rgba(220, 53, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(13, 110, 253, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Number of Subjects'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = <?php echo $total_subjects; ?>;
                            const value = context.raw;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `Subjects: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
  $(document).ready(function(){
    let historicalChart = null;
    let currentClassId, currentSubjectId;

    $('#loadPreviousDataBtn').click(function(){
        loadPreviousSemesterData();
    });

    load_class();

    function load_class(){
    start_load();
    $.ajax({
        url:"ajax.php?action=get_class",
        method:'POST',
        data:{fid:<?php echo $faculty_id ?>},
        error:function(err){
            console.log(err);
            alert_toast("An error occurred",'error');
            end_load();
        },
        success:function(resp){
            if(resp){
                resp = JSON.parse(resp);
                if(Object.keys(resp).length <= 0 ){
                    $('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to display.</a>');
                }else{
                    $('#class-list').html('');
                    Object.keys(resp).map(k=>{
                        let ratingClass = '';
                        let ratingBadge = '';
                        
                        if (resp[k].average_rating !== null) {
                            const rating = parseFloat(resp[k].average_rating);
                            if (rating <= 2) {
                                ratingClass = 'text-black';
                                ratingBadge = '<span class="badge bg-danger">Low Performance</span>';
                            } else if (rating > 2 && rating <= 4) {
                                ratingClass = 'text-black';
                                ratingBadge = '<span class="badge bg-warning">Moderate Performance</span>';
                            } else {
                                ratingClass = 'text-black';
                                ratingBadge = '<span class="badge bg-success">High Performance</span>';
                            }
                        }
                        
                        $('#class-list').append(
                            `<a href="javascript:void(0)" 
                                data-json='${JSON.stringify(resp[k])}' 
                                data-id="${resp[k].id}" 
                                class="list-group-item list-group-item-action show-result ${ratingClass}">
                                <div class="subject-text">${resp[k].class} - ${resp[k].subj}</div>
                                ${ratingBadge}
                            </a>`
                        );
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












    function anchor_func(){
        $('.show-result').click(function(){
            var vars = [], hash;
            var data = $(this).attr('data-json');
            data = JSON.parse(data);
            var _href = location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < _href.length; i++) {
                hash = _href[i].split('=');
                vars[hash[0]] = hash[1];
            }
            window.history.pushState({}, null, './index.php?page=result&rid='+data.id);
            load_report(<?php echo $faculty_id ?>, data.sid, data.id);
            $('#subjectField').text(data.subj);
            $('#classField').text(data.class);
            $('.show-result.active').removeClass('active');
            $(this).addClass('active');
        });
    }

    function load_report($faculty_id, $subject_id, $class_id){
        if($('#preloader2').length <= 0)
            start_load();
        $.ajax({
            url:'ajax.php?action=get_report',
            method:"POST",
            data:{faculty_id: $faculty_id, subject_id:$subject_id, class_id:$class_id},
            error:function(err){
                console.log(err);
                alert_toast("An Error Occured.","error");
                end_load();
            },
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp);
                    if(Object.keys(resp).length <= 0){
                        $('.rates').text('');
                        $('#tse').text('');
                        $('#print-btn').hide();
                        $('#overallRating').text('-');
                        $('#ratingProgressBar').css('width', '0%');
                        $('#ratingProgressBar').attr('aria-valuenow', '0');
                        $('#ratingProgressBar').text('0%');
                        $('#performanceMessage').html('');
                    }else{
                        $('#print-btn').show();
                        $('#tse').text(resp.tse);
                        $('.rates').text('-');
                        var data = resp.data;
                        var totalSum = 0;
                        var questionCount = 0;

                        Object.keys(data).map(q=>{
                            var questionSum = 0;
                            var responseCount = 0;
                            Object.keys(data[q]).map(r=>{
                                $('.rate_'+r+'_'+q).text(data[q][r]+'%');
                                questionSum += r * data[q][r];
                                responseCount += data[q][r];
                            });
                            var questionAverage = questionSum / responseCount;
                            totalSum += questionAverage;
                            questionCount++;
                        });

                        var overallAverage = (totalSum / questionCount).toFixed(2);
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
                    }
                }
            },
            complete:function(){
                loadHistoricalData($faculty_id, $subject_id, $class_id);
                loadComments($faculty_id, $subject_id, $class_id);
                currentClassId = $class_id;
                currentSubjectId = $subject_id;
                end_load();
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
            }
        });
    }

    function loadComments($faculty_id, $subject_id, $class_id) {
    $.ajax({
        url: 'ajax.php?action=load_comments',
        method: 'POST',
        data: {
            faculty_id: $faculty_id,
            subject_id: $subject_id,
            class_id: $class_id
        },
        success: function(resp) {
            try {
                var comments = JSON.parse(resp);
                var html = '';
                
                if (comments.length === 0) {
                    html = '<div class="alert alert-info">No comments available for this evaluation.</div>';
                } else {
                    html = '<div class="card">';
                    html += '<div class="card-header">';
                    html += '<h4>Student Comments (' + comments.length + ')</h4>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    
                    comments.forEach(function(comment, index) {
                        html += '<div class="comment-box mb-3 p-3 border rounded">';
                        html += '<div class="d-flex justify-content-between">';
                        html += '<strong class="text-primary">Student ' + (index + 1) + '</strong>';
                        html += '<small class="text-muted">' + comment.date_taken + '</small>';
                        html += '</div>';
                        html += '<p class="mt-2 mb-0">' + comment.comment + '</p>';
                        html += '</div>';
                    });
                    
                    html += '</div></div>';
                }
                
                $('#comments-section').html(html);
                
            } catch(e) {
                console.error("Error parsing comments:", e);
                $('#comments-section').html('<div class="alert alert-danger">Error loading comments</div>');
            }
        },
        error: function(err) {
            console.error("AJAX error:", err);
            $('#comments-section').html('<div class="alert alert-danger">Error loading comments</div>');
        }
    });
}



    $('#print-btn').click(function(){
        start_load();
        var ns = $('noscript').clone();
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

    function loadPreviousSemesterData(){
        start_load();
        $.ajax({
            url: "ajax.php?action=get_previous_semester_data",
            method: "POST",
            data: {faculty_id: <?php echo $faculty_id ?>},
            success: function(resp){
                try {
                    var data = JSON.parse(resp);
                    if(data.length === 0) {
                        $('#previousSemesterData').html('<p>No previous semester data available for this instructor.</p>');
                    } else {
                        var html = '';
                        data.forEach(function(semester){
                            html += '<h4>' + semester.academic.year + ' - ' + getOrdinal(semester.academic.semester) + ' Semester</h4>';
                            html += '<table class="table table-bordered">';
                            html += '<thead><tr><th>Subject</th><th>Total Evaluations</th><th>Average Rating</th></tr></thead>';
                            html += '<tbody>';
                            if(semester.subjects.length === 0) {
                                html += '<tr><td colspan="3">No subjects found for this semester.</td></tr>';
                            } else {
                                semester.subjects.forEach(function(subject){
                                    html += '<tr>';
                                    html += '<td>' + subject.code + ' - ' + subject.subject + '</td>';
                                    html += '<td>' + subject.evaluation_summary.total_evaluations + '</td>';
                                    html += '<td>' + parseFloat(subject.evaluation_summary.average_rating).toFixed(2) + '</td>';
                                    html += '</tr>';
                                });
                            }
                            html += '</tbody></table>';
                        });
                        $('#previousSemesterData').html(html);
                    }
                    $('#previousSemesterModal').modal('show');
                } catch (e) {
                    alert_toast("Error parsing data", 'error');
                }
            },
            complete: function(){
                end_load();
            }
        });
    }

    function getOrdinal(n) {
        var s = ["th", "st", "nd", "rd"];
        var v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    }

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

    // Set up automatic refresh every 30 seconds for comments
   
});
</script>