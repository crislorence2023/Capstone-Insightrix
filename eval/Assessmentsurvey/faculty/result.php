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

// Check if current academic year is closed before showing interface
$check_status_query = "SELECT status, year, semester FROM academic_list WHERE id = ?";
$stmt = $conn->prepare($check_status_query);
$stmt->bind_param("i", $academic_id);
$stmt->execute();
$result = $stmt->get_result();
$academic_data = $result->fetch_assoc();

if ($academic_data['status'] != 2) {
    echo "
    <div class='container-fluid py-4'>
        <div class='card shadow-sm border-0 rounded-lg'>
            <div class='card-body p-5'>
                <div class='text-center mb-5'>
                    <div class='status-icon mb-4'>
                        <i class='fas fa-clock fa-3x text-primary opacity-75'></i>
                    </div>
                    <h3 class='fw-bold text-dark text-bold mb-3'>Evaluation Period in Progress</h3>
                    <div class='text-muted mb-4' style='font-size: 1.1rem;'>
                        Results will be available once the evaluation period ends
                    </div>
                </div>

                <div class='row justify-content-center'>
                    <div class='col-md-6'>
                        <div class='bg- rounded-lg p-4 mb-4' style='background: rgba(0,0,0,.02)'>
                            <div class='d-flex justify-content-between align-items-center mb-3'>
                                <span class='text-muted'>Academic Year</span>
                                <span class='fw-bold text-dark'>{$academic_data['year']}</span>
                            </div>
                            <div class='d-flex justify-content-between align-items-center'>
                                <span class='text-muted'>Semester</span>
                                <span class='fw-bold text-dark'>" . ordinal_suffix($academic_data['semester']) . " Semester</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
    .status-icon {
        width: 120px;  /* Increased from 80px */
        height: 120px; /* Increased from 80px */
        background: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .status-icon i {
        font-size: 4.5rem !important; /* Increased from 3x */
        color: #0d6efd;
        opacity: 0.75;
    }

    .card {
        
        border-radius: 20px !important;
        background: #fff;
        border: none;
        transition: all 0.3s ease;
    }
    .card:hover {
       
        box-shadow: 0 10px 20px rgba(0,0,0,.05) !important;
    }
    .border-start {
        border-left-width: 4px !important;
    }
    </style>";
    exit;
}

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
GROUP BY r.subject_id
HAVING average_rating IS NOT NULL";

$result = $conn->query($query);

$perf_distribution = array(
    'low' => 0,
    'moderate' => 0,
    'high' => 0
);

$total_subjects = 0;
while($row = $result->fetch_assoc()) {
    if($row['average_rating'] !== null) {
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
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

* {
    font-family: 'Montserrat', sans-serif;
}

.historical {
    margin-top: 2rem;
   
    padding-left: 20px;
    padding-top: 3px;
    padding-bottom: 3px;
    width: auto;
    color: black;
    
    border-radius: 5px;
}

.performancedis {
    margin-top: 2rem;
    font-size: 18px !important;
    padding-left: 20px;
    padding-top: 3px;
    padding-bottom: 3px;
    width: auto;
    color: black;
   
    border-radius: 5px;
}

.row {
    border-radius: 15px;
}

.evaluation-container {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100% !important;
}

.sticky-accordion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.accordion-wrapper {
    position: relative;
    z-index: 1;
}

@media screen and (max-width: 768px) {
    .sticky-accordion {
        position: relative;
    }
}

/* Enhance accordion visibility */
.accordion-item {
    background: #fff;
    margin-bottom: 2px;
    border-radius: 4px;
    overflow: hidden;
}

.accordion-header {
    position: relative;
    z-index: 2;
}

.accordion-content {
    position: relative;
    z-index: 1;
}
.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.report-meta {
    text-align: center;
    width: auto;
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
}

.report-meta .badge {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr;  /* Changed from repeat(2, 1fr) to single column */
    gap: 1rem;
    margin-bottom: 2rem;
}

.info-card {
    background-color: white !important;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    text-align: center;
}

.info-card-header {
    display: flex;
    align-items: center;
    justify-content: center; /* Center the header content */
    gap: 0.75rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.info-card-header i {
    font-size: 1.25rem;
    color: #0d6efd;
    stroke-width: 2px;
}

.info-card-header h2 {
    margin: 0;
    font-size: 1.25rem;
    color: #495057;
}

.info-card-body {
    padding: 0.5rem 0;
    text-align: center; /* Center align text */
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
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-align: center;
}

.stat-value {
    font-size: 2.5rem !important;
    font-weight: 700;
    color: #212529 !important;
    line-height: 1;
    text-align: center;
}

.stat-value small {
    font-size: 1.2rem;
    color: #212529 !important;
    margin-left: 2px;
}

/* Ensure proper vertical alignment for the /5 */
.stat-value {
    display: flex;
    align-items: baseline;
    justify-content: center;
}

.rating-section {
    margin-top: 2rem;
}

.progress-container {
    background-color: white !important;
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
    font-size: 1.2rem;
    color: #333333;
    font-weight: 600;
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

.list-group {
    max-width: 100%;
    margin: 0;
    padding: 0.5rem;
    width: 100% !important;
}

.list-group-item {
    padding: 1rem;
    margin-bottom: 0.5rem;
    border: 1px solid #e9ecef;
    border-radius: 8px !important;
    transition: all 0.2s ease;
    background: white;
    width: 100% !important;
    overflow: hidden;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 0.5rem !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    color: #333333;
}

.list-group-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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

.performance-badge {
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-size: 0.875em;
    font-weight: 600;
    margin-top: 0.5rem;
}

.performance-badge.low {
    background-color: #dc3545;
    color: white;
}

.performance-badge.moderate {
    background-color: #ffc107;
    color: black;
}

.performance-badge.high {
    background-color: #198754;
    color: white;
}

.mobile-subject-dropdown {
    display: none;
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
    align-self: flex-start !important;
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

.gap-2 {
    gap: 0.5rem !important;
}

.me-2 {
    margin-right: 0.5rem !important;
}

.d-flex {
    margin-left: 2rem;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table.wborder tr,
table.wborder td,
table.wborder th {
    border: 1px solid gray;
    padding: 3px;
}

table.wborder thead tr {
    background: #6c757d linear-gradient(180deg,#828a91,#6c757d) repeat-x!important;
    color: #fff;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.text-left {
    text-align: left;
}

.button-container {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-right: 1rem;
    flex-wrap: wrap;
}

.button-container .btn {
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
}

.button-container .btn i {
    width: 16px;
    height: 16px;
    margin-right: 0.25rem;
    vertical-align: text-bottom;
}

/* Accordion Styles */
#class-list {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

#class-list .accordion-item {
    border: none;
    background: #fff;
    margin-bottom: 2px;
}

.accordion-toggle {
    position: absolute;
    opacity: 0;
    z-index: -1;
}

.accordion-header {
    position: relative;
    padding: 15px 20px;
    background: #f8f9fa;
    font-weight: 500;
    font-size: 1.1rem;
    color: #333;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    user-select: none;
}

.accordion-header:hover {
    background: #e9ecef;
}

.accordion-icon {
    width: 24px;
    height: 24px;
    position: relative;
    transition: transform 0.3s ease;
    pointer-events: none;
}

.accordion-icon::before,
.accordion-icon::after {
    content: '';
    position: absolute;
    background-color: #333;
    border-radius: 1px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.accordion-icon::before {
    width: 2px;
    height: 12px;
    transition: transform 0.3s ease;
}

.accordion-icon::after {
    width: 12px;
    height: 2px;
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: none;
}

.accordion-content.animating {
    transition: max-height 0.3s ease-in-out;
}

.accordion-toggle:not(:checked) ~ .accordion-content {
    max-height: 0;
}

.accordion-toggle:checked ~ .accordion-content {
    max-height: 1000px;
}

.accordion-toggle:checked ~ .accordion-header .accordion-icon::before {
    transform: translate(-50%, -50%) rotate(90deg);
}


.col-md-3 {
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    padding: 1rem;
    z-index: 100;
}

/* Ensure bg-white takes full height */
.col-md-3 .bg-white {
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Maintain sticky header while allowing list to scroll */
#class-list {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.evaluation-container {
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Keep the count header sticky */
.sticky-accordion {
    position: sticky;
    top: 0;
    background: #fff;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    z-index: 2;
}

/* Make accordion wrapper scrollable */
.accordion-wrapper {
    flex: 1;
    overflow-y: auto;
    padding-right: 0.5rem;
}

/* Scrollbar styling for better visibility */
.accordion-wrapper::-webkit-scrollbar {
    width: 6px;
}

.accordion-wrapper::-webkit-scrollbar-track {
    background: #f8f9fa; /* Light gray background */
    border-radius: 3px;
}

.accordion-wrapper::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1); /* Subtle gray thumb */
    border-radius: 3px;
}

.accordion-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2); /* Slightly darker on hover */
}

/* Ensure content below sticky element flows properly */
.row {
    display: flex;
    align-items: flex-start;
}


/* Responsive Media Queries */
@media screen and (max-width: 768px) {
    .col-md-3 {
        position: relative;
        height: auto;
        overflow-y: visible;
        padding: 0.5rem;
    }
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0 0.5rem;
    }

    .report-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .report-meta .badge {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        margin: 0.25rem;
        display: inline-block;
    }

    .col-md-3, .col-md-9 {
        width: 100%;
        padding: 0 0.5rem;
    }

    .button-container {
        justify-content: center;
        padding: 0.5rem;
    }

    table.wborder {
        font-size: 0.9rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }

    .historical {
        width: 100%;
        font-size: 16px !important;
        padding: 10px;
        margin-top: 1rem;
    }

    .evaluation-container {
        padding: 0.5rem;
    }

    .list-group {
        margin: 0.5rem 0;
    }

    .list-group-item {
        padding: 0.75rem;
    }

    .card.border-info {
        margin: 0.5rem 0;
    }

    .card-body .d-flex {
        flex-wrap: wrap;
        justify-content: flex-start;
        margin-left: 0;
    }

    .card-body .d-flex div {
        width: 50%;
        padding: 0.25rem;
    }
}

@media screen and (max-width: 480px) {
    .col-md-3 {
        position: relative;
        height: auto;
        overflow-y: visible;
        padding: 0.5rem;
    }
    .faculty-title {
        font-size: 24px !important;
    }

    .info-card {
        padding: 1rem;
    }

    .info-card-header h2 {
        font-size: 1rem;
    }

    .info-card-header i {
        font-size: 1.1rem;
    }

    .stat-value {
        font-size: 1.25rem;
    }

    .stat-label {
        font-size: 0.8rem;
    }

    .progress-container {
        padding: 1rem;
    }

    .progress-header h3 {
        font-size: 1rem;
    }

    table.wborder {
        font-size: 0.8rem;
    }

    table.wborder td, 
    table.wborder th {
        padding: 2px;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .detail-item label {
        width: 80px;
        font-size: 0.9rem;
    }

    .detail-item .detail-value {
        font-size: 0.9rem;
    }

    .button-container .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
}

@media screen and (max-width: 320px) {
    .col-md-3 {
        position: relative;
        height: auto;
        overflow-y: visible;
        padding: 0.5rem;
    }
    .faculty-title {
        font-size: 20px !important;
    }

    .info-card-header h2 {
        font-size: 0.9rem;
    }

    .stat-value {
        font-size: 1.1rem;
    }

    .report-meta .badge {
        display: block;
        width: 100%;
        margin: 0.2rem 0;
        text-align: center;
    }

    table.wborder {
        font-size: 0.75rem;
    }

    .list-group-item {
        padding: 0.5rem;
    }
}

.rating-indicator {
    display: flex;
    align-items: center;
    position: relative;
}

.tooltip-text {
    visibility: hidden;
    background-color: #333;
    color: white;
    text-align: center;
    padding: 5px;
    border-radius: 6px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
    white-space: nowrap;
    font-size: 12px;
}

.rating-indicator:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

/* Rating Legend Card Styles */
.card-header.bg-info {
    background: #20b2aa !important; /* Teal color */
    border-color: #20b2aa;
}

/* Table Styles */
table.wborder thead tr {
    background: #20b2aa !important; /* Changed from gray to teal */
    color: #fff;
}

table.wborder tr,
table.wborder td,
table.wborder th {
    border: 1px solid #e9ecef; /* Lighter border color */
    padding: 8px; /* Increased padding for better readability */
}

/* Rating Legend Content */
.card-body.text-secondary {
    background-color: #fff;
    border: 1px solid #e9ecef;
}

.card-body.text-secondary .fw-bold.text-dark {
    color: #20b2aa !important; /* Teal color for numbers */
}

/* Update the HTML for the Rating Legend section */
<div class="card border-info shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i data-feather="star" class="me-2"></i>Rating Legend
        </h5>
    </div>
    <div class="card-body text-secondary">
        <div class="d-flex flex-wrap justify-content-around text-center gap-3">
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

/* Additional styles for hover effects and consistency */
.card {
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(32, 178, 170, 0.1) !important;
}

table.wborder tbody tr:hover {
    background-color: rgba(32, 178, 170, 0.05);
    transition: background-color 0.3s ease;
}

/* Ensure consistent spacing */
.table-responsive {
    margin-bottom: 2rem;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.carousel-item {
    min-height: 200px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5);
    padding: 15px;
    border-radius: 50%;
}

.carousel-indicators li {
    background-color: #666;
}

.carousel-indicators .active {
    background-color: #000;
}

.comments-grid {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}

.comments-grid::-webkit-scrollbar {
    width: 6px;
}

.comments-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.comments-grid::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.comments-grid::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.comment-box {
    background-color: #fff;
    transition: all 0.3s ease;
}

.comment-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.carousel-item {
    min-height: 150px;
    transition: transform 0.6s ease-in-out;
}

.comment-box {
    background-color: #fff;
    border: 1px solid rgba(0,0,0,0.1) !important;
    transition: all 0.3s ease;
    min-height: 150px;
    display: flex;
    flex-direction: column;
}

.comment-text {
    font-size: 1rem;
    line-height: 1.5;
    color: #333;
    flex-grow: 1;
    overflow-y: auto;
    max-height: 150px;
}

.carousel-control-prev,
.carousel-control-next {
    width: 40px;
    height: 40px;
    background-color: rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.carousel-control-prev {
    left: 10px;
}

.carousel-control-next {
    right: 10px;
}

#commentsCarousel:hover .carousel-control-prev,
#commentsCarousel:hover .carousel-control-next {
    opacity: 1;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 20px;
    height: 20px;
}

/* Custom scrollbar for comment text */
.comment-text::-webkit-scrollbar {
    width: 4px;
}

.comment-text::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.comment-text::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.comment-text::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Fade animation for carousel */
.carousel-item {
    opacity: 0;
    transition: opacity 0.6s ease-in-out;
}

.carousel-item.active {
    opacity: 1;
}

/* Modern Comments Section Styling */
.comment-container {
    padding: 2rem;
    background: #fff;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.comment-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.comment-avatar i {
    font-size: 24px;
    color: #6c757d;
}


.comment-meta {
    flex: 1;
}

.comment-author {
    font-weight: 600;
    color: #1a73e8;
    margin-bottom: 0.25rem;
}

.comment-date {
    font-size: 0.875rem;
    color: #6c757d;
}

.comment-body {
    flex: 1;
    display: flex;
    align-items: center;
}

.comment-text {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
    max-height: 150px;
    overflow-y: auto;
    padding-right: 1rem;
}

/* Modern Carousel Controls */
.carousel-control {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10; /* Add this to ensure controls are above content */
}

.carousel-control-prev {
    left: 10px; /* Update from -20px to 10px */
}

.carousel-control-next {
    right: 10px; /* Update from -20px to 10px */
}

/* Update hover states */
.carousel-control:hover {
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    opacity: 1;
}

/* Make controls always visible on mobile */
@media (max-width: 768px) {
    .carousel-control {
        opacity: 1;
    }
    
    .carousel-control-prev {
        left: 5px;
    }
    
    .carousel-control-next {
        right: 5px;
    }
}

/* Ensure comment container doesn't overlap controls */
.comment-container {
    padding: 2rem 3rem; /* Increase horizontal padding to make room for controls */
}

.carousel-control i {
    font-size: 16px;
    color: #1a73e8;
}

.carousel-control-prev:hover {
    left: -22px;
}

.carousel-control-next:hover {
    right: -22px;
}

/* Smooth Transitions */
.carousel-item {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.carousel-item.active {
    opacity: 1;
}

/* Custom Scrollbar */
.comment-text::-webkit-scrollbar {
    width: 4px;
}

.comment-text::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.comment-text::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}

.comment-text::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Card Styling */
.card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid #eef0f2;
}

.badge {
    padding: 0.5em 0.8em;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .comment-container {
        padding: 1.5rem;
    }
    
    .comment-avatar {
        width: 40px;
        height: 40px;
    }
    
    .comment-text {
        font-size: 0.95rem;
    }
    
    .carousel-control {
        width: 36px;
        height: 36px;
    }
}

.gap-3 {
    gap: 1rem !important;
}

.fs-4 {
    font-size: 1.5rem !important;
}

/* Modal Styles */
.modal-comment-box {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid #eef0f2;
    transition: all 0.3s ease;
}

.modal-comment-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.comments-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 0.5rem;
}

.modal-dialog-scrollable .modal-content {
    max-height: 85vh;
}

.btn-outline-primary {
    border-color: #1a73e8;
    color: #1a73e8;
}

.btn-outline-primary:hover {
    background-color: #1a73e8;
    color: white;
}

/* Enhance modal appearance */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eef0f2;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-close {
    background-color: transparent;
    opacity: 0.5;
    transition: opacity 0.3s ease;
}

.btn-close:hover {
    opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-comment-box {
        padding: 1rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .comments-grid {
        padding: 0.25rem;
    }
}

/* Custom Modal Styles */
.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050; /* Increase z-index */
    opacity: 0;
    transition: opacity 0.3s ease;
    justify-content: center;
    align-items: center;
}

.custom-modal.show {
    opacity: 1;
}

.custom-modal-content {
    background: white;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    transform: translateY(-20px);
    transition: transform 0.3s ease;
    position: relative; /* Add this */
}

.custom-modal.show .custom-modal-content {
    transform: translateY(0);
}

/* Add these new styles */
body.modal-open {
    overflow: hidden;
    padding-right: 17px; /* Prevent layout shift */
}

.modal-close {
    position: absolute;
    right: 1rem;
    top: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    z-index: 1;
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eef0f2;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0.5rem;
    transition: color 0.3s ease;
}

.modal-close:hover {
    color: #000;
}

.custom-modal-body {
    padding: 1rem;
    overflow-y: auto;
    max-height: calc(90vh - 100px);
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden;
}

/* Update the carousel control styles */
.carousel-control {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer; /* Add cursor pointer */
    z-index: 100; /* Increase z-index to ensure visibility */
}

/* Update positioning */
.carousel-control-prev {
    left: 20px; /* Increase distance from edge */
}

.carousel-control-next {
    right: 20px; /* Increase distance from edge */
}

/* Make controls visible by default */
.carousel-control {
    opacity: 0.8;
}

/* Update hover state */
.carousel-control:hover {
    opacity: 1;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Style the arrows */
.carousel-control i {
    font-size: 20px;
    color: #333;
}

/* Ensure container doesn't hide controls */
.comment-container {
    padding: 2rem 4rem; /* Increase horizontal padding */
    position: relative; /* Add relative positioning */
}

/* Remove any overflow hidden that might be hiding controls */
#commentsCarousel {
    overflow: visible;
    position: relative;
}

/* Remove or hide the carousel controls */
.carousel-control {
    display: none !important; /* Hide all carousel controls */
}

/* Alternative: you can also remove these specific controls */
.carousel-control-prev,
.carousel-control-next {
    display: none !important;
}

.privacy-notice {
    font-size: .9rem;
    color: #495057;
    text-align: left; /* Align text to the left for readability */
    margin: 0 auto;
    max-width: 80%; /* Limit width for better alignment */
}

.privacy-list {
    list-style: none;
    padding-left: 0;
    margin: 0.5rem 0;
    text-align: left; /* Align list items to the left */
}

.privacy-list li {
    padding: 0.25rem 0;
    padding-left: 1.5rem;
    position: relative;
}

.privacy-list li:before {
    content: "•";
    color: #20b2aa;
    position: absolute;
    left: 0;
}

.privacy-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 0.5rem;
    text-align: center; /* Center align footer text */
}

#subject-count {
    margin-left: 0.75rem;  /* Adds specific margin if needed */
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    background-color: #20b2aa;
    color: white;
    border-radius: 0.25rem;
    display: inline-block;
    text-align: center;
    min-width: 30px;
}

.sticky-accordion {
    background: #fff;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}




/* Add these button styles */
.btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 8px 16px !important;
}

.btn i {
    font-size: 20px !important;
    line-height: 1 !important;
}

/* Specific styles for the two buttons */
.btn-info {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: white !important;
}

.btn-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
}

/* Optional: Add hover effects */
.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    transition: all 0.2s ease;
}


</style>





<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12">
        <div class="button-container">
    <button class="btn btn-sm btn-info bg-gradient-info" id="loadPreviousDataBtn">
        <i class="ri-time-line"></i> Load Previous Semester Data
    </button>
    <button class="btn btn-sm btn-success bg-gradient-success" id="exportExcelBtn">
        <i class="ri-file-excel-2-line"></i> Export to Excel
    </button>
</div>

        </div>
    </div>
    <div class="row" >
        <div class=" col-md-3">
            <div class="bg-white">
                <div class="list-group" style="background-color: white" id="class-list">
                    
                </div>
            </div>
        </div>

    
        <div class="mobile-subject-dropdown"></div>
        <div class="col-md-9">
            <div id="printable">
                <div>
                <div class="evaluation-header mb-4">
                    <p class="faculty-title" style="font-weight: bold; font-size: 30px; color: teal">Evaluation Report</p>
                    <hr>
                    

                    <div class="evaluation-container">



    <!-- Header Section -->
    <div class="report-header">
       
        <div class="report-meta">
            <span class="badge">Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></span>
        </div>
    </div>

    <!-- Main Info Cards -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-header">
                <i class="ri-bar-chart-2-line"></i>  <!-- Changed from data-feather="bar-chart-2" -->
                <h2>Evaluation Statistics</h2>
            </div>
            <div class="info-card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Total Evaluations</div>
                        <div class="stat-value" id="tse" style="font-size: 2.5rem; font-weight: 700;">-</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Average Rating</div>
                        <div class="stat-value" style="font-size: 2.5rem; font-weight: 700;">
                            <span id="overallRating">-</span><small style="font-size: 1.2rem;">/5</small>
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
        <div class="container mt-4 mb-3">
            <p class="historical mb-3 bg-muted b-2 font-weight-bold "style="color: #333333; font-size: 1.2rem">Historical Performance</p>
            <canvas id="historicalChart" width="100%" height="50"></canvas>
        </div>
    </div>
</div>



                
            

                

        <div class="card border-info shadow-sm mb-4">
    <div class="container mt-4 mb-3 ">
        <p class="performancedis mb-3 bg-muted b-2" style="color: #333333; font-weight: 600;">Subject Performance Distribution (Current Semester)</p>
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
$criteria = $conn->query("SELECT * FROM criteria_list WHERE id IN (SELECT criteria_id FROM question_list WHERE academic_id = {$_SESSION['academic']['id']}) ORDER BY ABS(order_by) ASC");
while ($crow = $criteria->fetch_assoc()):
?>
<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
            <tr class="bg-info text-white rounded-3">
                <th class="p-2 rounded-start"><b><?php echo $crow['criteria']; ?></b></th>
                <th width="5%" class="text-center">1</th>
                <th width="5%" class="text-center">2</th>
                <th width="5%" class="text-center">3</th>
                <th width="5%" class="text-center">4</th>
                <th width="5%" class="text-center">5</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $questions = $conn->query("SELECT * FROM question_list WHERE criteria_id = {$crow['id']} AND academic_id = {$_SESSION['academic']['id']} ORDER BY ABS(order_by) ASC");
            while ($row = $questions->fetch_assoc()):
                $q_arr[$row['id']] = $row;
            ?>
            <tr class="bg-white align-middle">
                <td class="p-2" width="40%">
                    <?php echo $row['question']; ?>
                </td>
                <?php for ($c = 1; $c <= 5; $c++): ?>
                <td class="text-center">
                    <span class="rate_<?php echo $c . '_' . $row['id']; ?> rates"></span>
                </td>
                <?php endfor; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endwhile; ?>
<div class="card-body">
            <div id="comments-section" class="mb-3">
                <!-- Comments will be loaded here -->
            </div>
        </div>
              
                
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
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search subjects...">
                </div>
                <div id="previousSemesterData" class="table-responsive">
                    <!-- Data will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>
// Replace the existing exportExcelBtn click handler with this updated version
document.getElementById('exportExcelBtn').addEventListener('click', function() {
    // Create a new workbook
    var wb = XLSX.utils.book_new();
    
    // Get evaluation summary data
    var summaryData = [
        ['Evaluation Summary'],
        ['Total Evaluations', document.getElementById('tse').textContent || '0'],
        ['Average Rating', (document.getElementById('overallRating').textContent || '0') + '/5'],
        ['Performance', document.getElementById('performanceMessage').textContent.replace(/<[^>]*>/g, '').trim() || 'N/A'],
        [] // Empty row for spacing
    ];
    
    // Create summary worksheet
    var summaryWS = XLSX.utils.aoa_to_sheet(summaryData);
    XLSX.utils.book_append_sheet(wb, summaryWS, "Summary");

    // Get all tables with criteria data
    var tables = document.querySelectorAll('.table-responsive table.table-bordered');
    
    tables.forEach((table, index) => {
        try {
            // Get table header
            var header = [];
            table.querySelectorAll('thead th').forEach(th => {
                header.push(th.textContent.trim());
            });

            // Get table data
            var data = [];
            table.querySelectorAll('tbody tr').forEach(tr => {
                var row = [];
                tr.querySelectorAll('td').forEach(td => {
                    // Remove any HTML tags and get only the text
                    var cellText = td.textContent.trim();
                    // Add percentage symbol if the cell contains a number followed by %
                    if (/^\d+%$/.test(cellText)) {
                        row.push(cellText); // Keep the percentage symbol
                    } else {
                        row.push(cellText);
                    }
                });
                if (row.length > 0) { // Only add non-empty rows
                    data.push(row);
                }
            });

            if (header.length > 0 && data.length > 0) {
                // Combine header and data
                var fullData = [header, ...data];

                // Create worksheet
                var ws = XLSX.utils.aoa_to_sheet(fullData);
                
                // Add worksheet to workbook with a unique name
                XLSX.utils.book_append_sheet(wb, ws, `Criteria_${index + 1}`);
            }
        } catch (error) {
            console.error('Error processing table:', error);
        }
    });

    // Get historical data
    try {
        var historicalCanvas = document.getElementById('historicalChart');
        if (historicalCanvas && historicalCanvas.chart) {
            var historicalData = [
                ['Historical Performance'],
                ['Semester', 'Rating'],
                ...historicalCanvas.chart.data.labels.map((label, i) => [
                    label,
                    historicalCanvas.chart.data.datasets[0].data[i]
                ])
            ];
            var historicalWS = XLSX.utils.aoa_to_sheet(historicalData);
            XLSX.utils.book_append_sheet(wb, historicalWS, "Historical_Data");
        }
    } catch (error) {
        console.error('Error processing historical data:', error);
    }

    // Get performance distribution data
    try {
        var distChart = document.getElementById('performanceDistChart');
        if (distChart && distChart.chart) {
            var performanceData = [
                ['Performance Distribution'],
                ['Category', 'Number of Subjects'],
                ...distChart.chart.data.labels.map((label, i) => [
                    label,
                    distChart.chart.data.datasets[0].data[i]
                ])
            ];
            var performanceWS = XLSX.utils.aoa_to_sheet(performanceData);
            XLSX.utils.book_append_sheet(wb, performanceWS, "Performance_Distribution");
        }
    } catch (error) {
        console.error('Error processing performance distribution:', error);
    }

    // Get comments
    try {
        var commentsSection = document.getElementById('comments-section');
        if (commentsSection) {
            var comments = [];
            comments.push(['Student Comments']);
            comments.push(['Comment', 'Date']);
            
            commentsSection.querySelectorAll('.comment-box').forEach(box => {
                var comment = box.querySelector('.comment-text')?.textContent.trim() || '';
                var date = box.querySelector('.comment-date')?.textContent.trim() || '';
                if (comment || date) {
                    comments.push([comment, date]);
                }
            });

            if (comments.length > 2) { // Only add if there are actual comments
                var commentsWS = XLSX.utils.aoa_to_sheet(comments);
                XLSX.utils.book_append_sheet(wb, commentsWS, "Comments");
            }
        }
    } catch (error) {
        console.error('Error processing comments:', error);
    }

    try {
        // Generate filename with current date
        var date = new Date();
        var filename = `Evaluation_Report_${date.getFullYear()}_${date.getMonth()+1}_${date.getDate()}.xlsx`;

        // Save the file
        XLSX.writeFile(wb, filename);
    } catch (error) {
        console.error('Error saving file:', error);
        alert('Error exporting to Excel. Please try again.');
    }
});

// ... existing code ...
</script>

<script>




document.addEventListener('DOMContentLoaded', function() {
    // Create modal HTML if it doesn't exist

// Add this to your existing JavaScript
$('.accordion-header').on('click', function() {
    const content = $(this).siblings('.accordion-content');
    
    // Add animation class before the state changes
    content.addClass('animating');
    
    // Optional: Remove the animation class after transition completes
    setTimeout(() => {
        content.removeClass('animating');
    }, 300); // Match this with your CSS transition duration
});
    
    if (!document.querySelector('.chart-modal')) {
        const modalHTML = `
            <div class="chart-modal">
                <div class="chart-modal-content">
                  
                    <div class="chart-modal-body">
                        <canvas id="modalChart"></canvas>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    const modal = document.querySelector('.chart-modal');
    const modalClose = document.querySelector('.chart-modal-close');
    let modalChart = null;

    // Function to wrap charts in container with enlarge button
    function wrapChartsInContainer() {
        const charts = document.querySelectorAll('#historicalChart, #performanceDistChart');
        charts.forEach(canvas => {
            // Only wrap if not already wrapped
            if (!canvas.parentElement.classList.contains('chart-container')) {
                const container = document.createElement('div');
                container.className = 'chart-container';
                canvas.parentNode.insertBefore(container, canvas);
                container.appendChild(canvas);
            }
        });
    }

    

    // Function to create modal chart
    function createModalChart(sourceChart) {
        const modalCanvas = document.getElementById('modalChart');
        const modalCtx = modalCanvas.getContext('2d');
        
        // Deep clone the source chart configuration
        const modalConfig = JSON.parse(JSON.stringify(sourceChart.config));
        
        // Adjust options for modal display
        modalConfig.options = {
            ...modalConfig.options,
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                ...modalConfig.options.scales,
                x: {
                    ...modalConfig.options.scales?.x,
                    ticks: {
                        ...modalConfig.options.scales?.x?.ticks,
                        font: { size: 14 }
                    }
                },
                y: {
                    ...modalConfig.options.scales?.y,
                    ticks: {
                        ...modalConfig.options.scales?.y?.ticks,
                        font: { size: 14 }
                    }
                }
            },
            plugins: {
                ...modalConfig.options.plugins,
                legend: {
                    ...modalConfig.options.plugins?.legend,
                    labels: {
                        ...modalConfig.options.plugins?.legend?.labels,
                        font: { size: 14 }
                    }
                }
            }
        };

        return new Chart(modalCtx, modalConfig);
    }

    // Handle chart click
    function handleChartClick(event) {
        const canvas = event.target;
        const chart = Chart.getChart(canvas);
        if (!chart) return;

        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Create or update modal chart
        if (modalChart) {
            modalChart.destroy();
        }
        modalChart = createModalChart(chart);
    }

    // Close modal handler
    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        if (modalChart) {
            modalChart.destroy();
            modalChart = null;
        }
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Add click listeners to chart containers
        document.querySelectorAll('.chart-container').forEach(container => {
            const canvas = container.querySelector('canvas');
            if (canvas) {
                canvas.addEventListener('click', handleChartClick);
            }
        });

        // Modal close button
        modalClose.addEventListener('click', closeModal);

        // Click outside modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    }

    // Initialize everything
    wrapChartsInContainer();
    initializeEventListeners();

    // Handle dynamic chart creation
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                wrapChartsInContainer();
                initializeEventListeners();
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});



document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('performanceDistChart').getContext('2d');
    var performanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Low Performance\n( 2.0)', 'Moderate Performance\n(2.1 - 4.0)', 'High Performance\n(> 4.0)'],
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


   
   function searchPreviousSemesterData() {
    const searchInput = document.getElementById("searchInput");
    const filter = searchInput.value.toLowerCase();
    const tables = document.querySelectorAll("#previousSemesterData table");
    
    tables.forEach(table => {
        const rows = table.getElementsByTagName("tr");
        let hasVisibleRows = false;
        
        // Skip header row (i=0)
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName("td");
            let rowText = '';
            
            // Combine all cell text for searching
            for (let j = 0; j < cells.length; j++) {
                rowText += cells[j].textContent || cells[j].innerText;
            }
            
            if (rowText.toLowerCase().includes(filter)) {
                rows[i].style.display = "";
                hasVisibleRows = true;
            } else {
                rows[i].style.display = "none";
            }
        }
        
        // Show/hide semester headers based on whether there are visible rows
        const semesterHeader = table.previousElementSibling;
        if (semesterHeader && semesterHeader.tagName === 'H4') {
            semesterHeader.style.display = hasVisibleRows ? "" : "none";
        }
    });
}

// Update the loadPreviousSemesterData function to include the event listener
function loadPreviousSemesterData() {
    start_load();
    $.ajax({
        url: "ajax.php?action=get_previous_semester_data",
        method: "POST",
        data: {faculty_id: faculty_id},
        success: function(resp) {
            try {
                var data = JSON.parse(resp);
                if(data.length === 0) {
                    $('#previousSemesterData').html('<p>No previous semester data available for this instructor.</p>');
                } else {
                    var html = '';
                    data.forEach(function(semester) {
                        html += '<h4>' + semester.academic.year + ' - ' + getOrdinal(semester.academic.semester) + ' Semester</h4>';
                        html += '<table class="table table-bordered">';
                        html += '<thead><tr><th>Subject</th><th>Total Evaluations</th><th>Average Rating</th></tr></thead>';
                        html += '<tbody>';
                        if(semester.subjects.length === 0) {
                            html += '<tr><td colspan="3">No subjects found for this semester.</td></tr>';
                        } else {
                            semester.subjects.forEach(function(subject) {
                                // Calculate performance indicators
                                let indicatorColor = '';
                                let performanceText = '';
                                const rating = parseFloat(subject.evaluation_summary.average_rating);
                                
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
                
                // Add event listener to search input after content is loaded
                const searchInput = document.getElementById("searchInput");
                if (searchInput) {
                    // Remove any existing listeners first
                    searchInput.removeEventListener('input', searchPreviousSemesterData);
                    // Add new listener
                    searchInput.addEventListener('input', searchPreviousSemesterData);
                }
                
                $('#previousSemesterModal').modal('show');
            } catch (e) {
                alert_toast("Error parsing data", 'error');
            }
        },
        complete: function() {
            end_load();
        }
    });
}
    function createModalChart(sourceChart) {
        const modalCanvas = document.getElementById('modalChart');
        const modalCtx = modalCanvas.getContext('2d');
        
        // Copy the configuration from source chart
        const modalChart = new Chart(modalCtx, {
            type: sourceChart.config.type,
            data: JSON.parse(JSON.stringify(sourceChart.config.data)),
            options: {
                ...sourceChart.config.options,
                responsive: true,
                maintainAspectRatio: false
            }
        });

        return modalChart;
    }


    $('#loadPreviousDataBtn').click(function(){
        loadPreviousSemesterData();
    });

    load_class();


    // Add this to your existing JavaScript
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
                    $('.mobile-subject-dropdown').html('<select class="form-control" disabled><option>No data to display</option></select>');
                } else {
                    // Create wrapper structure for sticky behavior
                    const evaluationContainer = `
                        <div class="evaluation-container">
                            <div class="sticky-accordion">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Subject List</h6>
                                    <span class="badge bg-primary" id="subject-count">
                                        ${Object.keys(resp).length} <!-- Only the number -->
                                    </span>
                                </div>
                            </div>
                            <div class="accordion-wrapper">
                                <div class="list-group">
                                    ${Object.keys(resp).map(k => {
                                        const item = resp[k];
                                        let badgeClass = '';
                                        let ratingBadge = '';
                                        
                                        if (item.average_rating !== null) {
                                            const rating = parseFloat(item.average_rating);
                                            if (rating <= 2) {
                                                badgeClass = 'performance-badge low';
                                                ratingBadge = 'Low Performance';
                                            } else if (rating > 2 && rating <= 4) {
                                                badgeClass = 'performance-badge moderate';
                                                ratingBadge = 'Moderate Performance';
                                            } else {
                                                badgeClass = 'performance-badge high';
                                                ratingBadge = 'High Performance';
                                            }
                                        }
                                        
                                        return `
                                            <a href="javascript:void(0)" 
                                               data-json='${JSON.stringify(item)}' 
                                               data-id="${item.id}" 
                                               class="list-group-item show-result">
                                                <div class="subject-text">${item.subj}</div>
                                                ${ratingBadge ? `<span class="${badgeClass}">${ratingBadge}</span>` : ''}
                                            </a>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#class-list').empty().append(evaluationContainer);

                    // Create mobile dropdown
                    let mobileSelect = '<select class="form-control" id="mobileSubjectSelect">';
                    mobileSelect += '<option value="">Select Subject</option>';
                    
                    Object.keys(resp).forEach(k => {
                        mobileSelect += `<option value="${resp[k].id}" 
                            data-json='${JSON.stringify(resp[k])}'>${resp[k].subj}</option>`;
                    });
                    
                    mobileSelect += '</select>';
                    $('.mobile-subject-dropdown').html(mobileSelect);
                }
            }
        },
        complete:function(){
            end_load();
            
            // Add click handlers for showing results
            $('.show-result').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const id = $(this).data('id');
                anchor_func();
            });

            // Handle accordion header clicks
            $('.accordion-header').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const accordionToggle = $(this).prev('.accordion-toggle');
                const isChecked = accordionToggle.prop('checked');
                
                // Toggle the checkbox state
                accordionToggle.prop('checked', !isChecked);
            });

            // Initialize sticky behavior
            const stickyAccordion = document.querySelector('.sticky-accordion');
            if (stickyAccordion) {
                const observer = new IntersectionObserver(
                    ([e]) => e.target.classList.toggle('is-sticky', e.intersectionRatio < 1),
                    { threshold: [1] }
                );
                observer.observe(stickyAccordion);
            }

            // Modified initialization logic
            if('<?php echo isset($_GET['rid']) ?>' == 1){
                const initialItem = $('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]');
                if(initialItem.length) {
                    initialItem.trigger('click');
                    $('#mobileSubjectSelect').val('<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>');
                    
                    const departmentId = initialItem.closest('.accordion-item').data('department');
                    $(`#toggle-${departmentId}`).prop('checked', true);
                }
            } else {
                $('#mobileSubjectSelect').val('');
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

                        // Add color based on rating range
                        const ratingElement = $('#overallRating');
                        if (overallAverage <= 2) {
                            ratingElement.css('color', '#dc3545'); // Red for low performance
                        } else if (overallAverage > 2 && overallAverage <= 4) {
                            ratingElement.css('color', '#ffc107'); // Yellow/Orange for moderate performance
                        } else {
                            ratingElement.css('color', '#198754'); // Green for high performance
                        }

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

    // First, define the function in the global scope (outside any other functions)
    window.showAllComments = function() {
        const modal = document.getElementById('allCommentsModal');
        if (modal) {
            document.body.classList.add('modal-open');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }
    };

    window.hideAllComments = function() {
        const modal = document.getElementById('allCommentsModal');
        if (modal) {
            document.body.classList.remove('modal-open');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }
    };

    // Then, in your loadComments function, modify the button to use addEventListener instead of onclick
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
                        html = `
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="ri-chat-3-line text-primary fs-4"></i>  <!-- Changed from fas fa-comments -->
                                        <h5 class="mb-0">Student Feedback</h5>
                                        <span class="badge bg-primary">${comments.length} Comments</span>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm" id="viewAllCommentsBtn">
                                        <i class="ri-fullscreen-line me-2"></i>  <!-- Changed from fas fa-expand-alt -->
                                        View All
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="commentsCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        ${comments.map((comment, index) => `
                                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                                <div class="comment-container">
                                                    <div class="comment-header">
                                                        <div class="comment-avatar">
                                                            <i class="fas fa-user-circle"></i>
                                                        </div>
                                                        <div class="comment-meta">
                                                            <div class="comment-author">Student ${index + 1} of ${comments.length}</div>
                                                            <div class="comment-date">${comment.date_taken}</div>
                                                        </div>
                                                    </div>
                                                    <div class="comment-body">
                                                        <div class="comment-text">${comment.comment}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                    
                                    <!-- Update control buttons -->
                                    <button class="carousel-control carousel-control-prev" type="button" data-bs-target="#commentsCarousel" data-bs-slide="prev">
                                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control carousel-control-next" type="button" data-bs-target="#commentsCarousel" data-bs-slide="next">
                                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Modal for All Comments -->
                        <div id="allCommentsModal" class="custom-modal">
                            <div class="custom-modal-content">
                                <div class="custom-modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-comments text-primary me-2"></i>
                                        All Student Comments
                                    </h5>
                                    <button type="button" class="modal-close" onclick="hideAllComments()">&times;</button>
                                </div>
                                <div class="custom-modal-body">
                                    <div class="comments-grid">
                                        ${comments.map((comment, index) => `
                                            <div class="modal-comment-box">
                                                <div class="comment-header">
                                                    <div class="comment-avatar">
                                                        <i class="fas fa-user-circle"></i>
                                                    </div>
                                                    <div class="comment-meta">
                                                        <div class="comment-author">Student ${index + 1}</div>
                                                        <div class="comment-date">${comment.date_taken}</div>
                                                    </div>
                                                </div>
                                                <div class="comment-body mt-3">
                                                    <div class="comment-text">${comment.comment}</div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }
                    
                    $('#comments-section').html(html);
                    
                    // Add event listener after the HTML is inserted
                    if (comments.length > 0) {
                        document.getElementById('viewAllCommentsBtn').addEventListener('click', showAllComments);
                        
                        // Initialize carousel
                        const carousel = new bootstrap.Carousel(document.getElementById('commentsCarousel'), {
                            interval: 5000,
                            ride: 'carousel',
                            wrap: true,
                            touch: true,
                            keyboard: true // Enable keyboard controls
                        });

                        // Add click handlers for controls
                        document.querySelector('.carousel-control-prev').addEventListener('click', function(e) {
                            e.preventDefault();
                            carousel.prev();
                        });

                        document.querySelector('.carousel-control-next').addEventListener('click', function(e) {
                            e.preventDefault();
                            carousel.next();
                        });

                        // Optional: Pause on hover
                        $('#commentsCarousel').hover(
                            function() { carousel.pause(); },
                            function() { carousel.cycle(); }
                        );
                    }
                } catch(e) {
                    console.error("Error parsing comments:", e);
                    $('#comments-section').html('<div class="alert alert-danger">Error loading comments</div>');
                }
            }
        });
    }

    // Move these functions to global scope (outside of document.ready)
    function showAllComments() {
        const modal = document.getElementById('allCommentsModal');
        if (modal) {
            document.body.classList.add('modal-open');
            modal.style.display = 'flex'; // Add this line
            setTimeout(() => modal.classList.add('show'), 10); // Add small delay for transition
        }
    }

    function hideAllComments() {
        const modal = document.getElementById('allCommentsModal');
        if (modal) {
            document.body.classList.remove('modal-open');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300); // Hide after transition
        }
    }

    // Add click outside to close
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('allCommentsModal');
        if (event.target === modal) {
            hideAllComments();
        }
    });

    // Add escape key to close
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideAllComments();
        }
    });

    $('#print-btn').click(function(){
        start_load();
        var ns = $('noscript').clone();
        var content = $('#printable').html();
        
        // Modify the content to remove icons and unnecessary sections
        content = content.replace(/<i[^>]*><\/i>/g, ''); // Remove icons
        content = content.replace(/<div class="info-card">[\s\S]*?<\/div>/g, ''); // Remove Data Privacy Notice
        content = content.replace(/<div class="card border-info shadow-sm mb-4">[\s\S]*?<\/div>/g, ''); // Remove Historical Performance and Subject Performance Distribution
        content = content.replace(/<button[^>]*>[\s\S]*?<\/button>/g, ''); // Remove View All, Previous, and Next buttons
        content = content.replace(/<div id="allCommentsModal"[\s\S]*?<\/div>/g, ''); // Remove modal contents

        // Fix alignments if necessary
        content = content.replace(/class="d-flex justify-content-between align-items-center"/g, 'style="display: flex; justify-content: space-between; align-items: center;"');
        
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
                                    // Calculate performance indicators
                                    let indicatorColor = '';
                                    let performanceText = '';
                                    const rating = parseFloat(subject.evaluation_summary.average_rating);
                                    
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
    var input = document.getElementById("searchInput");
    var filter = input.value.toLowerCase();
    var table = document.querySelector("#previousSemesterData table");
    
    // Ensure table exists
    if (!table) return;

    var tr = table.getElementsByTagName("tr");

    // Loop through all table rows (except the header)
    for (var i = 1; i < tr.length; i++) {
        tr[i].style.display = "none"; // Hide the row by default
        var td = tr[i].getElementsByTagName("td");

        // Check each cell in the row
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                var txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Show the row if match is found
                    break;
                }
            }
        }
    }
}

    // Set up automatic refresh every 30 seconds for comments
    window.addEventListener('scroll', function() {
    const classListContainer = document.querySelector('.col-md-3');
    const scrollPosition = window.scrollY;
    
    // Add/remove sticky class based on scroll position
    if (scrollPosition > 100) { // Adjust this value based on when you want it to become sticky
        classListContainer.classList.add('is-sticky');
    } else {
        classListContainer.classList.remove('is-sticky');
    }
});

$('.accordion-button').click(function() {
    $(this).find('.dropdown-icon').toggleClass('rotate');
});

// Add resize event listener to handle viewport changes
window.addEventListener('resize', function() {
    const classListContainer = document.querySelector('.col-md-3');
    if (window.innerWidth <= 768) {
        classListContainer.classList.remove('is-sticky');
    }
});


document.addEventListener('scroll', () => {
    const classList = document.getElementById('class-list');
    if (window.scrollY > classList.offsetTop) {
        classList.classList.add('is-sticky');
    } else {
        classList.classList.remove('is-sticky');
    }
});
   
});
</script>