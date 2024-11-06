<!DOCTYPE html>
<html>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container-fluid {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .report-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .report-header h1 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .report-date {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .report-content {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }
        .report-section {
            flex: 1;
            min-width: 300px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .info-card h2 {
            color: #2980b9;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3498db;
        }
        .rating-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .rating-label {
            font-weight: 500;
        }
        .rating-value {
            font-size: 1.1rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .performance-average {
            background-color: #3498db;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-top: 1rem;
        }
        .performance-average h2 {
            margin: 0;
            color: white;
            border: none;
        }
        .modal-footer {
            text-align: right;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .icon {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <?php 
    include 'db_connect.php';
    if(isset($_GET['id'])){
        $qry = $conn->query("SELECT r.*,concat(e.lastname,', ',e.firstname,' ',e.middlename) as name,t.task,concat(ev.lastname,', ',ev.firstname,' ',ev.middlename) as ename,((((r.efficiency + r.timeliness + r.quality + r.accuracy)/4)/5) * 100) as pa FROM ratings r inner join employee_list e on e.id = r.employee_id inner join task_list t on t.id = r.task_id inner join evaluator_list ev on ev.id = r.evaluator_id  where r.id = ".$_GET['id'])->fetch_array();
        foreach($qry as $k => $v){
            $$k = $v;
        }
    }
    ?>
    <div class="container-fluid">
        <div class="report-header">
            <h1>Performance Evaluation Report</h1>
            <div class="report-date">
                <i class="fas fa-calendar-alt icon"></i>
                <?php echo date("F d, Y", strtotime($date_created)) ?>
            </div>
        </div>
        <div class="report-content">
            <div class="report-section">
                <div class="info-card">
                    <h2><i class="fas fa-tasks icon"></i>Task Details</h2>
                    <p><strong>Task:</strong> <?php echo ucwords($task) ?></p>
                    <p><strong>Assigned To:</strong> <?php echo ucwords($name) ?></p>
                    <p><strong>Evaluator:</strong> <?php echo ucwords($ename) ?></p>
                </div>
                <div class="info-card">
                    <h2><i class="fas fa-comment icon"></i>Remarks</h2>
                    <p><?php echo $remarks ?></p>
                </div>
            </div>
            <div class="report-section">
                <div class="info-card">
                    <h2><i class="fas fa-star icon"></i>Performance Ratings</h2>
                    <div class="rating-item">
                        <span class="rating-label">Efficiency</span>
                        <span class="rating-value"><?php echo $efficiency ?>/5</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-label">Timeliness</span>
                        <span class="rating-value"><?php echo $timeliness ?>/5</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-label">Quality</span>
                        <span class="rating-value"><?php echo $quality ?>/5</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-label">Accuracy</span>
                        <span class="rating-value"><?php echo $accuracy ?>/5</span>
                    </div>
                </div>
                <div class="performance-average">
                    <h2>Performance Average</h2>
                    <div style="font-size: 2rem; font-weight: bold;">
                        <?php echo number_format($pa,2).'%' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times icon"></i>Close
        </button>
    </div>
</body>
</html>