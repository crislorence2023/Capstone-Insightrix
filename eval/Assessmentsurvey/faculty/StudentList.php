<?php
include('db_connect.php');

// Helper function for ordinal suffix
function ordinal_suffix($num) {
    $num = $num % 100;
    if ($num < 11 || $num > 13) {
        switch ($num % 10) {
            case 1: return $num . 'st';
            case 2: return $num . 'nd';
            case 3: return $num . 'rd';
        }
    }
    return $num . 'th';
}

// Session variables
$faculty_id = $_SESSION['login_id'];
$login_name = $_SESSION['login_name'];

// Evaluation status
$astat = ["Not Yet Started", "Started", "Closed"];

// Fetch subjects taught by the specific instructor (without duplicates)
$query = "SELECT DISTINCT sl.code, sl.subject 
          FROM subject_list sl
          JOIN faculty_assignments fa ON sl.id = fa.subject_id
          WHERE fa.faculty_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);
$total_subjects = count($subjects);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Evaluation System - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
    --primary-blue: #2563eb;
    --primary-blue-light: rgba(37, 99, 235, 0.1);
    --green: #22c55e;
    --green-light: rgba(34, 197, 94, 0.1);
    --teal: #008080;
    --teal-light: rgba(0, 128, 128, 0.1);
    --sage: #4d8076;
    --sage-light: rgba(77, 128, 118, 0.1);
    --white: #ffffff;
    --light-gray: #f8fafc;
    --gray-100: #f0f0f0;
    --gray-200: #e5e7eb;
    --gray-500: #6b7280;
    --gray-700: #1f2937;
    --gray-800: #111827;
    --transition-base: all 0.3s ease;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Montserrat', sans-serif;
    background-color: #f5f6fa;
    color: var(--gray-700);
}
.table-info{
   background-color: #fff;
}
/* Card Styles */
.dashboard-card {
    background: var(--white);
    border-radius: 12px;
   
    padding: 1rem;
    margin-bottom: 1rem;
    transition: var(--transition-base);
}

.card-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 1rem;
    justify-content: center;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0;
    text-align: center;
}

.card-content {
    text-align: center;
    padding: 1rem;
}

.card-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-800);
}

/* Dashboard Container */
.dashboard-container {
    padding: 1.5rem;
    max-width: 100%;
    margin: 0 auto;
    align-items: center;
    justify-content: center;
}

/* Responsive Grid */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: -0.75rem;
}

.col-md-4, .col-md-3, .col-md-2 {
    padding: 0.75rem;
}

@media (min-width: 768px) {
    .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
    .col-md-3 { flex: 0 0 25%; max-width: 25%; }
    .col-md-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
}

@media (max-width: 768px) {
    .col-md-4, .col-md-3, .col-md-2 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .dashboard-container {
        padding: 1rem;
    }
}

/* Accordion */
.accordion-item {
    background: var(--white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    margin-bottom: 2rem;
    border: 1px solid var(--gray-200);
}

.accordion-header {
    background-color: var(--white);
    border-bottom: 1px solid var(--gray-200);
}

.accordion-button {
    width: 100%;
    padding: 1.25rem 1.5rem;
    border: none;
    background: var(--white);
    text-align: left;
    display: flex;
    align-items: center;
    font-weight: 600;
    color: var(--gray-700);
    font-size: 1.1rem;
}

.accordion-body {
    padding: 1rem;
    background: var(--white);
}

/* Form Controls */
.form-control, .form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.95rem;
    transition: var(--transition-base);
    color: var(--gray-700);
    background-color: var(--white);
    height: 45px;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px var(--primary-blue-light);
}

.search-input {
    padding-left: 2.75rem;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
    font-size: 0.9rem;
}

/* Button Styles */
.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    transition: var(--transition-base);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-primary {
    background-color: teal;
    color: var(--white);
    border: none;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
}

.btn-outline-primary {
    border: 1px solid teal;
    color: teal;
    background: transparent;
}

.btn-outline-primary:hover {
    background-color: var(--primary-blue);
    color: var(--white);
    transform: translateY(-1px);
}

/* Alerts */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.95rem;
    line-height: 1.5;
    border: 1px solid transparent;
}

.alert i {
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert-info {
    background-color: var(--teal-light);
    color: var(--teal);
    border-color: rgba(0, 128, 128, 0.2);
}

.alert-danger {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: rgba(220, 38, 38, 0.2);
}

.alert-success {
    background-color: var(--green-light);
    color: var(--green);
    border-color: rgba(34, 197, 94, 0.2);
}

/* Add these new styles */
.stats-card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid var(--gray-200);
}



.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: opacity 0.3s ease;
    opacity: 0;
}

.stats-card.students-card::before {
    background-color: var(--teal);
}

.stats-card.classes-card::before {
    background-color: var(--sage);
}

.card-header {
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.card-icon {
    width: 56px;
    height: 56px;
    font-size: 1.75rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    transition: var(--transition-base);
}

.card-title {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-content {
    padding-top: 1rem;
}

.card-value {
    font-size: 2.25rem;
    margin-bottom: 0.5rem;
    color: var(--gray-800);
    font-weight: 700;
}

.card-trend {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.card-trend i {
    margin-right: 0.25rem;
}

.trend-up {
    color: var(--green);
}

.trend-down {
    color: #ef4444;
}

/* Update Button Styles */
.btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    transition: var(--transition-base);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

/* Specific styles for small buttons */
.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.8125rem;
}

/* Update icon size in buttons */
.btn i {
    font-size: 0.875rem;
}

/* Specific styles for the Apply Filters button */
#applyFilters {
    width: 100%;
    height: 45px;
    background-color: teal;
    border: none;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
}

#applyFilters:hover {
    background-color: #015050;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.justify-content-center {
    justify-content: center !important;
}

/* Row spacing in accordion */
.accordion-body .row {
    margin: -0.5rem;
}

.accordion-body .col-md-4,
.accordion-body .col-md-3,
.accordion-body .col-md-2 {
    padding: 0.5rem;
}

/* Add these pagination styles */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
    padding: 1rem;
}

.pagination-btn {
    padding: 0.25rem 0.5rem;
    min-width: 32px;
    height: 32px;
    border: 1px solid var(--gray-200);
    background: var(--white);
    color: var(--gray-700);
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pagination-btn:hover:not(.active, :disabled) {
    background: var(--gray-100);
    border-color: var(--gray-300);
}

.pagination-btn.active {
    background: teal;
    color: white;
    border-color: teal;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-info {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin: 0 1rem;
}

/* Add these table styles */
.table {
    width: 100%;
    margin-bottom: 1rem;
    background-color: var(--white);
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-200);
}

.table thead th {
    background-color: var(--gray-50);
    font-weight: 600;
    color: var(--gray-700);
    white-space: nowrap;
}

.table tbody tr:hover {
    background-color: var(--gray-50);
}

.text-center {
    text-align: center;
}

/* Add/update these styles */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* Enhanced Stats Cards */
.stats-card {
    background: var(--white);
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    padding: 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
   
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
}

.stats-card.students-card {
   
}

.stats-card.classes-card {
   
}

.card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.students-card .card-icon {
    background-color: var(--teal-light);
    color: var(--teal);
}

.classes-card .card-icon {
    background-color: var(--sage-light);
    color: var(--sage);
}

/* Enhanced Accordion */
.accordion-item {
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.accordion-button {
    padding: 1.5rem;
    font-size: 1.1rem;
    border-radius: 16px;
}

/* Enhanced Table */
.table-container {
    background: var(--white);
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    padding: 1.5rem;
    margin-top: 2rem;
}

.table thead th {
    background-color: var(--gray-100);
    padding: 1rem 1.5rem;
    font-weight: 600;
}

.table tbody td {
    padding: 1rem 1.5rem;
    vertical-align: middle;
}

/* Enhanced Buttons */
.btn-outline-primary {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background-color: var(--teal);
    border-color: var(--teal);
}

/* Enhanced Pagination */
.pagination {
    background: var(--white);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.pagination-btn {
    min-width: 40px;
    height: 40px;
    border-radius: 8px;
}

.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
}

.custom-modal .modal-content {
    position: relative;
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    width: 90%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.custom-modal .close-modal {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    background: none;
}

.custom-modal .modal-header {
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.custom-modal .modal-footer {
    padding-top: 15px;
    border-top: 1px solid #eee;
    margin-top: 15px;
    text-align: right;
}

/* Update the student avatar styles in the modal */
#studentAvatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--teal);
    padding: 3px;
    background-color: var(--white);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.student-info {
    margin-top: 1.5rem;
}

.table-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.show-entries {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.show-entries select {
    width: auto;
    display: inline-block;
    margin: 0 0.5rem;
    padding: 0.25rem 0.5rem;
    height: 32px;
    font-size: 0.875rem;
}

.table-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding: 0.5rem 0;
}

.showing-entries {
    color: var(--gray-500);
    font-size: 0.875rem;
}

.page-numbers {
    display: flex;
    gap: 0.25rem;
}

.page-numbers .pagination-btn {
    min-width: 36px;
}

/* Add these new styles for sticky filters */
.accordion-sticky {
    position: sticky;
    top: 0;
    z-index: 100;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 16px;
}

.accordion-sticky.is-sticky {
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(0);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Update accordion styles */
.accordion-item {
    background: var(--white);
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 1rem;
    transition: margin 0.3s ease;
}

.is-sticky .accordion-item {
    border-radius: 0 0 16px 16px;
    margin-bottom: 1rem;
}

.accordion-body {
    padding: 1rem;
}

.row.g-2 {
    margin: -0.5rem;
}

.row.g-2 > [class*="col-"] {
    padding: 0.5rem;
}

.btn-primary {
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-control, .form-select {
    height: 45px;
}

/* Update pagination styles for mobile responsiveness */
.pagination {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
    padding: 1rem;
}

/* Add these new mobile-specific styles */
@media (max-width: 768px) {
    .table-info {
        flex-direction: column;
        gap: 1rem;
    }
    
    .showing-entries {
        text-align: center;
        width: 100%;
    }
    
    .pagination {
        width: 100%;
        padding: 0.5rem;
    }
    
    .page-numbers {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.25rem;
    }
    
    .pagination-btn {
        min-width: 32px;
        height: 32px;
        padding: 0.25rem;
        font-size: 0.75rem;
    }
    
    /* Hide some page numbers on very small screens */
    @media (max-width: 380px) {
        .page-numbers .pagination-btn:not(.active) {
            display: none;
        }
        
        .page-numbers .pagination-btn.active {
            display: inline-flex;
        }
    }
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.card {
    background: var(--white);
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.card-icon {
    width: 40px;
    height: 40px;
    background: var(--sage-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sage);
}

.card-title {
    color: var(--sage);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a1a1a;
}

.subjects-table {
    background: var(--white);
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.subjects-table table {
    width: 100%;
    border-collapse: collapse;
}

.subjects-table th,
.subjects-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.subjects-table th {
    background: var(--sage-light);
    color: var(--sage);
    font-weight: 600;
}

.subjects-table tr:hover {
    background: var(--light-gray);
}

.stats-card.subjects-card::before {
    background-color: var(--teal);
}

.subjects-card .card-icon {
    background-color: var(--teal-light);
    color: var(--teal);
}

    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="row justify-content-center">
        <div class="col-md-4">
            <article class="dashboard-card stats-card students-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="card-title">Total Students</h2>
                </div>
                <div class="card-content">
                    <h3 id="totalStudents" class="card-value">0</h3>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="dashboard-card stats-card classes-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <h2 class="card-title">Total Classes</h2>
                </div>
                <div class="card-content">
                    <h3 id="totalClasses" class="card-value">0</h3>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="dashboard-card stats-card subjects-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h2 class="card-title">Total Subjects</h2>
                </div>
                <div class="card-content">
                    <h3 class="card-value"><?php echo $total_subjects; ?></h3>
                </div>
            </article>
        </div>
    </div>


    <div class="subjects-table table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subject['code']); ?></td>
                        <td><?php echo htmlspecialchars($subject['subject']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
                

    <!-- Filters & Search Accordion -->
    <div class="accordion">
        <div class="accordion-item">
            <div class="accordion-header">
                <button class="accordion-button">
                    <i class="fas fa-filter" style="margin-right: 0.75rem; color: var(--gray-500);"></i>
                    Filters & Search
                </button>
            </div>
            <div class="accordion-content">
                <div class="accordion-body">
                    <div class="row align-items-center g-2">
                        <div class="col-md-5">
                            <div style="position: relative;">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="form-control search-input" id="searchStudent" placeholder="Search students...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterClass">
                                <option value="">All Classes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" id="applyFilters">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student List Container -->
    <div class="table-container">
        <div class="table-controls">
            <div class="show-entries">
                <label>
                    Show 
                    <select id="entriesPerPage" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </label>
            </div>
        </div>
        
        <div id="student-list"></div>
        
        <div class="table-info">
            <div class="showing-entries">
                Showing <span id="startEntry">0</span> to <span id="endEntry">0</span> of <span id="totalEntries">0</span> entries
            </div>
            <div class="pagination">
                <button class="pagination-btn" onclick="changePage('first')" id="firstPage">
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="pagination-btn" onclick="changePage('prev')" id="prevPage">
                    <i class="fas fa-angle-left"></i>
                </button>
                <div id="pageNumbers" class="page-numbers"></div>
                <button class="pagination-btn" onclick="changePage('next')" id="nextPage">
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="pagination-btn" onclick="changePage('last')" id="lastPage">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Main Dashboard Functionality
        $(document).ready(function() {
            // Declare studentsData in the global scope
            window.studentsData = [];
            let currentPage = 1;
            
            function load_students() {
                $.ajax({
                    url: "ajax.php?action=get_students",
                    method: "POST",
                    data: { faculty_id: <?php echo $faculty_id; ?> },
                    success: function(response) {
                        try {
                            window.studentsData = JSON.parse(response);
                            updateStudentList(window.studentsData);
                            populateFilters(window.studentsData);
                            updateStatistics(window.studentsData);
                        } catch (error) {
                            console.error('Error parsing student data:', error);
                            showError();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        showError();
                    }
                });
            }

            // Update the changePage function to be globally accessible
            window.changePage = function(action) {
                const entriesPerPage = parseInt($('#entriesPerPage').val() || 10);
                const totalPages = Math.ceil(window.studentsData.length / entriesPerPage);
                
                switch(action) {
                    case 'first':
                        currentPage = 1;
                        break;
                    case 'prev':
                        currentPage = Math.max(1, currentPage - 1);
                        break;
                    case 'next':
                        currentPage = Math.min(totalPages, currentPage + 1);
                        break;
                    case 'last':
                        currentPage = totalPages;
                        break;
                    default:
                        if (typeof action === 'number') {
                            currentPage = action;
                        }
                }
                
                updateStudentList(window.studentsData);
            };

            function updateStudentList(students) {
                const entriesPerPage = parseInt($('#entriesPerPage').val() || 10);
                const totalPages = Math.ceil(students.length / entriesPerPage);
                
                // Adjust currentPage if it exceeds the new total pages
                if (currentPage > totalPages) {
                    currentPage = totalPages || 1;
                }
                
                const startIndex = (currentPage - 1) * entriesPerPage;
                const endIndex = Math.min(startIndex + entriesPerPage, students.length);
                const displayedStudents = students.slice(startIndex, endIndex);

                // Update table info
                $('#startEntry').text(students.length ? startIndex + 1 : 0);
                $('#endEntry').text(endIndex);
                $('#totalEntries').text(students.length);

                // Generate table HTML
                let html = '';
                if (students.length > 0) {
                    html = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    displayedStudents.forEach(student => {
                        html += `
                            <tr>
                                <td>${student.school_id}</td>
                                <td>${student.student_name}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary view-details" data-id="${student.school_id}">
                                        View
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No students found
                        </div>
                    `;
                }

                $('#student-list').html(html);
                updatePagination(totalPages);
            }

            function populateFilters(students) {
                const classes = [...new Set(students.map(s => s.class))];
                
                $('#filterClass').html('<option value="">All Classes</option>' + 
                    classes.map(c => `<option value="${c}">${c}</option>`).join(''));
            }

            function updateStatistics(students) {
                animateCounter('#totalStudents', students.length);
                animateCounter('#totalClasses', [...new Set(students.map(s => s.class))].length);
            }

            function animateCounter(elementId, endValue) {
                const duration = 1000;
                const startValue = parseInt($(elementId).text()) || 0;
                const step = (endValue - startValue) / (duration / 16);
                let current = startValue;
                
                const animate = () => {
                    current += step;
                    if ((step > 0 && current >= endValue) || (step < 0 && current <= endValue)) {
                        $(elementId).text(endValue);
                        return;
                    }
                    $(elementId).text(Math.round(current));
                    requestAnimationFrame(animate);
                };
                
                animate();
            }

            function showError() {
                $('#student-list').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Unable to load student data</strong>
                            <p style="margin-top: 0.25rem; opacity: 0.9">Please check your connection and try again. If the problem persists, contact support.</p>
                        </div>
                    </div>
                `);
            }

            // Initial load
            load_students();

            // Add event listener for entries per page change
            $('#entriesPerPage').change(function() {
                currentPage = 1; // Reset to first page when changing entries per page
                updateStudentList(window.studentsData);
            });

            // Update search handler
            $('#searchStudent').on('input', function() {
                currentPage = 1; // Reset to first page when searching
                const searchTerm = $(this).val().toLowerCase();
                const filteredStudents = window.studentsData.filter(student => 
                    student.student_name.toLowerCase().includes(searchTerm) ||
                    student.school_id.toLowerCase().includes(searchTerm)
                );
                updateStudentList(filteredStudents);
            });

            // Update filter handler
            $('#applyFilters').click(function() {
                currentPage = 1; // Reset to first page when filtering
                const classFilter = $('#filterClass').val();
                
                let filteredStudents = window.studentsData;
                
                if (classFilter) {
                    filteredStudents = filteredStudents.filter(s => s.class === classFilter);
                }
                
                updateStudentList(filteredStudents);
            });

            // Modal functions
            function openModal() {
                $('#studentModal').fadeIn(300);
            }

            function closeModal() {
                $('#studentModal').fadeOut(300);
            }

            // Close modal when clicking close button or outside
            $('.close-modal').click(closeModal);
            $(window).click(function(event) {
                if (event.target == document.getElementById('studentModal')) {
                    closeModal();
                }
            });

            // Update click handler for view button
            $(document).on('click', '.view-details', function() {
                const studentId = $(this).data('id');
                console.log('Fetching details for student:', studentId); // Debug line
                
                $.ajax({
                    url: 'ajax.php?action=get_student_details_faculty',
                    method: 'POST',
                    data: {student_id: studentId},
                    success: function(response) {
                        console.log('Raw response:', response); // Debug line
                        try {
                            const data = JSON.parse(response);
                            
                            if (data.status === 'success') {
                                const student = data.data;
                                
                                // Update modal content
                                $('#studentId').text(student.school_id);
                                $('#studentName').text(student.firstname + ' ' + student.lastname);
                                $('#studentClass').text(student.class);
                                
                                // Set default avatar if the path is empty or invalid
                                const avatarSrc = student.avatar_path || 'assets/img/no-image-available.png';
                                $('#studentAvatar').attr('src', '../' + avatarSrc);
                                
                                // Show modal
                                openModal();
                            } else {
                                alert(data.message || 'Failed to load student details');
                            }
                        } catch (error) {
                            console.error('Error parsing response:', error);
                            alert('Failed to load student details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Failed to fetch student details. Please try again.');
                    }
                });
            });

            // Add these new functions
            function updatePagination(totalPages) {
                const pageNumbers = $('#pageNumbers');
                pageNumbers.empty();

                // Calculate range of pages to show
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                
                // Adjust start page if we're near the end
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                // First page
                $('#firstPage').prop('disabled', currentPage === 1);
                $('#prevPage').prop('disabled', currentPage === 1);

                // Add ellipsis and first page if needed
                if (startPage > 1) {
                    pageNumbers.append(`
                        <button class="pagination-btn" onclick="changePage(1)">1</button>
                        ${startPage > 2 ? '<span class="pagination-ellipsis">...</span>' : ''}
                    `);
                }

                // Page numbers
                for (let i = startPage; i <= endPage; i++) {
                    pageNumbers.append(`
                        <button class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                                onclick="changePage(${i})">${i}</button>
                    `);
                }

                // Add ellipsis and last page if needed
                if (endPage < totalPages) {
                    pageNumbers.append(`
                        ${endPage < totalPages - 1 ? '<span class="pagination-ellipsis">...</span>' : ''}
                        <button class="pagination-btn" onclick="changePage(${totalPages})">${totalPages}</button>
                    `);
                }

                // Last page
                $('#nextPage').prop('disabled', currentPage === totalPages);
                $('#lastPage').prop('disabled', currentPage === totalPages);
            }

            // Add sticky functionality for filters
            const accordion = document.querySelector('.accordion');
            const accordionOffset = accordion.offsetTop;

            window.addEventListener('scroll', () => {
                if (window.pageYOffset > accordionOffset) {
                    accordion.classList.add('accordion-sticky', 'is-sticky');
                } else {
                    accordion.classList.remove('accordion-sticky', 'is-sticky');
                }
            });

            // Ensure table container doesn't jump when accordion becomes sticky
            const tableContainer = document.querySelector('.table-container');
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > accordionOffset) {
                    tableContainer.style.marginTop = accordion.offsetHeight + 'px';
                } else {
                    tableContainer.style.marginTop = '0';
                }
            });
        });
    </script>

    <!-- Replace the Bootstrap modal with this custom modal -->
    <div id="studentModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Details</h5>
                <button type="button" class="close-modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-clock fa-3x mb-3" style="color: var(--teal);"></i>
                <h4>Coming Soon!</h4>
                <p>Student details feature is under development.</p>
            </div>
        </div>
    </div>

    
    </div>

    <!-- Subjects Table -->
    
</body>
</html>