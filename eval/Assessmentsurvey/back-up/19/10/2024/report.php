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
?>
<div class="col-lg-12">
    <div class="callout callout-info">
        <div class="d-flex w-100 justify-content-center align-items-center">
            <label for="faculty">Select Faculty</label>
            <div class=" mx-2 col-md-4">
            <select name="" id="faculty_id" class="form-control form-control-sm select2">
                <option value=""></option>
                <?php 
                $faculty = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
                $f_arr = array();
                $fname = array();
                while($row=$faculty->fetch_assoc()):
                    $f_arr[$row['id']]= $row;
                    $fname[$row['id']]= ucwords($row['name']);
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-1">
            <div class="d-flex justify-content-end w-100">
                <button class="btn btn-sm btn-success bg-gradient-success" style="display:none" id="print-btn"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>

    <div class="col-md-12 mb-1">
    <div class="d-flex justify-content-end w-100">
        <button class="btn btn-sm btn-info bg-gradient-info" id="loadPreviousDataBtn"><i class="fa fa-history"></i> Load Previous Semester Data</button>
        <button class="btn btn-sm btn-success bg-gradient-success" style="display:none" id="print-btn"><i class="fa fa-print"></i> Print</button>
    </div>
</div>
    <div class="row">
        <div class="col-md-3">
            <div class="callout callout-info">
                <div class="list-group" id="class-list">
                    
                </div>
            </div>
        </div>
        <div class="col-md-9">
    <div class="callout callout-info" id="printable">
        <div>
            <h3 class="text-center">Evaluation Report</h3>
            <hr>
            <table width="100%">
                <tr>
                    <td width="50%"><p><b>Faculty: <span id="fname"></span></b></p></td>
                    <td width="50%"><p><b>Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></b></p></td>
                </tr>
                <tr>
                    <td width="50%"><p><b>Class: <span id="classField"></span></b></p></td>
                    <td width="50%"><p><b>Subject: <span id="subjectField"></span></b></p></td>
                </tr>
            </table>
            <p class=""><b>Total Student Evaluated: <span id="tse"></span></b></p>
            <p class=""><b>Overall Average Rating: <span id="overallRating"></span> / 5</b></p> <!-- Add this to display overall average -->
        </div>
        <div class="progress">
  <div id="ratingProgressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="5">0%</div>
</div>
<div id="performanceMessage" class="mt-2"></div>



        <fieldset class="border border-info p-2 w-100">
            <legend class="w-auto">Rating Legend</legend>
            <p>5 = Strongly Agree, 4 = Agree, 3 = Uncertain, 2 = Disagree, 1 = Strongly Disagree</p>
        </fieldset>

        <!-- Container for Chart -->
        
         <div class="container mt-4">
            <h4>Historical Performance</h4>
            <canvas id="historicalChart" width="100%" height="40"></canvas>
        </div>


       


        <!-- Table for Evaluation Data -->
        <?php 
        $q_arr = array();
        $criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
        while($crow = $criteria->fetch_assoc()):
        ?>
        <table class="table table-condensed wborder">
            <thead>
                <tr class="bg-gradient-secondary">
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
        
    </div>
</div>
    </div>

    <div class="container">
         <canvas id="evaluationChart" width="100%" height="40"></canvas>
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






<style>
    .list-group-item:hover{
        color: black !important;
        font-weight: 700 !important;
    }
</style>
<noscript>
    <style>
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
    </style>
</noscript>
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

    function loadPreviousSemesterData(){
        start_load()
        $.ajax({
            url: "ajax.php?action=get_previous_semester_data",
            method: "POST",
            data: {faculty_id: $('#faculty_id').val()},
            error: function(xhr, status, error){
                console.error("AJAX Error:", status, error);
                console.log("Response Text:", xhr.responseText);
                alert_toast("An error occurred", 'error')
                end_load()
            },
            success: function(resp){
                console.log("Raw response:", resp);
                try {
                    var data = JSON.parse(resp)
                    console.log("Parsed data:", data);
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
                    console.error("Error parsing JSON:", e);
                    alert_toast("Error parsing data", 'error')
                }
            },
            complete: function(){
                end_load()
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