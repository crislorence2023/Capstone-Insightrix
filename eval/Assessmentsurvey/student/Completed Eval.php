<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Evaluations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .modal-content {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            justify-content: center;
            align-items: center;
        }

        .modal-header .btn-close {
            font-size: 1.25rem;
            padding: 1rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
            opacity: 0.5;
        }

        .rating-legend {
            margin: 1rem 0 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .rating-legend .badge {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            font-weight: 500;
            
        }

        .question-item {
            padding: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
           
        }

        .rating-options {
            display: flex;
            justify-content: space-between;
            max-width: 400px;
            margin: 1rem auto 0;
           
        }

        .form-check {
            margin: 0 1rem;
        }

        .form-check-input {
            border-color: black !important;
        }

        .form-check-input:checked {
            background-color: black !important;
            border-color: black !important;
        }

        .form-check-input:disabled {
            opacity: 1;
        }

        .form-check-label {
            color: black !important;
            font-weight: 500;
        }

        .modal-footer .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        .btn-close-modal {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1a202c;
            transition: all 0.2s;
            font-weight: 600;
        }

        .btn-close-modal:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            color: #e53e3e;
        }

        .criteria-section {
            padding: 1.5rem;
            transition: all 0.2s ease;
            margin-bottom: 1.5rem;
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #edf2f7;
            color: black;
            margin: 1rem 1.5rem;
        }

        .criteria-section:hover {
         
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05);
        }

        .criteria-header {
           
            color: #2d3748 !important;
            padding: 1rem 1.5rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-bottom: 1px solid #edf2f7;
        }

        .criteria-content {
            padding: 1.5rem !important;
            border: none !important;
        }

        .question-item {
            transition: background-color 0.2s ease;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #edf2f7;
        }

        .question-item:last-child {
            border-bottom: none;
        }

        .question-item:hover {
            background-color: #f8fafc;
        }

        .badge {
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: #f8f9fa;
            color: #4a5568;
            border: 1px solid #edf2f7;
        }

        .evaluation-header {
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 1;
            padding: 1.5rem 0;
            border-bottom: 1px solid #edf2f7;
            
        }

        
        .comment-section {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #edf2f7;
            
             
            width: auto;
        }

        .comment-section h6 {
            color: #2d3748;
            font-weight: 600;
        }

        .modal-title {
            color: #1a5f7a;
            font-weight: 600;
           font-size: 1rem;
            width: 100%;

        }

        .academic-year {
            font-weight: 700;
            color: #2d3748;
            font-size: 1.2rem;
        }

        .comment-section {
            padding: 20px;
            margin: 0 20px 20px;
        }

        .comment-section h6 {
            font-size: 1rem;
            font-weight: 600;
        }

        .comment-section .form-control {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
        }
        .text-muted {
            color: #6c757d !important;
            font-weight: 500 !important;
        }

        .search-container {
            position: relative;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            margin: auto;
        }

        .search-input:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
        }

        .select-container {
            position: relative;
            max-width: 250px;
        }

        .custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23718096' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }

        /* For IE */
        .custom-select::-ms-expand {
            display: none;
        }

        .custom-select:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
            outline: none;
        }

        .select-arrow {
            display: none;
        }

        /* Updated card styles */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.08);
            margin: 2rem 0;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            padding: 1.5rem;
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
        }

        /* Updated table styles */
        .table-responsive {
            border-radius: 0.75rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f8fafc;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .table {
            min-width: 800px;
            width: 100%;
        }

        .table td, 
        .table th {
            border: none;
            border-bottom: 1px solid #edf2f7;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table td,
        .table th {
            border-right: 1px solid #edf2f7;
        }

        .table td:last-child,
        .table th:last-child {
            border-right: none;
        }

        .table thead th {
            background: #f8fafc;
            color: #1a202c;
            font-weight: 600;
            padding: 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #4a5568;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
            transition: background-color 0.2s ease;
        }

        /* Updated filter controls */
        .select-container {
            max-width: 200px;
        }

        .custom-select {
            height: 42px;
            border-color: #e2e8f0;
            background-color: #ffffff;
            font-size: 0.875rem;
            padding: 0.5rem 2rem 0.5rem 1rem;
        }

        .search-input {
            height: 42px;
            border-color: #e2e8f0;
            padding-left: 2.5rem;
        }

        /* Updated button styles */
        .btn-primary {
            background-color: #3182ce;
            border-color: #3182ce;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2c5282;
            border-color: #2c5282;
            transform: translateY(-1px);
        }

        .btn-primary i {
            margin-right: 0.5rem;
        }

        .col-md-4{
            margin: auto;
        }


        .text-muted {
   color: #4a5568 !important;  /* Darker gray instead of #6c757d */
   font-weight: 500 !important;
        }

.table tbody td {
   padding: 1rem;
   vertical-align: middle;
   color: #2d3748;  /* Darker color instead of #4a5568 */
}

.badge {
   font-weight: 500;
   font-size: 0.875rem;
   padding: 0.5rem 1rem;
   border-radius: 0.5rem;
   background: #f8f9fa;
   color: #2d3748;  /* Darker color instead of #4a5568 */
   border: 1px solid #edf2f7;
}

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-header .row {
                gap: 1rem;
            }
            
            .select-container, .search-container {
                max-width: 100%;
            }
            
            .table-responsive {
                border-radius: 0.75rem;
            }
        }

        .custom-gap > div {
            margin-left: 1rem; /* Adjust the value as needed for the desired gap */
        }

        .custom-gap > div:first-child {
            margin-left: 0; /* Remove margin from the first item */
        }

        /* Also update the options within the select */
        .custom-select option {
            font-size: 1rem;
            padding: 0.5rem;
        }

        /* Modern Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #ffffff;
            border-top: 1px solid #edf2f7;
        }

        .pagination-info {
            color: #4a5568;
            font-size: 0.875rem;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-button {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #2d3748;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pagination-button:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        .pagination-button.active {
            background: #3182ce;
            color: #ffffff;
            border-color: #3182ce;
        }

        .pagination-button:disabled {
            background: #f7fafc;
            color: #a0aec0;
            cursor: not-allowed;
        }

        .pagination-button.nav-button {
            padding: 0.5rem 0.75rem;
        }
        .title{
            color: teal;
            font-size: 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        
        <div class="col-lg-12">
        <p class="title">Completed Evaluations</p>
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center justify-content-end">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-end custom-gap">
                                <div class="select-container">
                                    <select id="semester-filter" class="custom-select">
                                        <option value="">All Semesters</option>
                                        <option value="1">1st Semester</option>
                                        <option value="2">2nd Semester</option>
                                        <option value="3">Summer</option>
                                    </select>
                                </div>
                                <div class="search-container">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="search-input" id="search-input" placeholder="Search evaluations...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="completed-evaluations-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Faculty</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Academic Year</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add these variables at the top of your script
    const ITEMS_PER_PAGE = 10;
    let currentPage = 1;
    let totalItems = 0;
    let currentData = [];

    $(document).ready(function(){
        loadCompletedEvaluations();
        
        // Add event listener for semester filter
        $('#semester-filter').change(function() {
            loadCompletedEvaluations();
        });
    });

    function loadCompletedEvaluations() {
        const semester = $('#semester-filter').val();
        
        $.ajax({
            url: 'ajax.php?action=get_completed_evaluations',
            method: 'GET',
            data: { semester: semester },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if(result.status === 'success') {
                        displayEvaluations(result.data);
                    } else {
                        alert(result.message || 'Failed to load evaluations');
                    }
                } catch(e) {
                    console.error('Error parsing response:', e);
                    alert('Failed to load evaluations');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to load evaluations');
            }
        });
    }

    function displayEvaluations(evaluations) {
        currentData = evaluations;
        totalItems = evaluations.length;
        displayPage(currentPage);
        renderPagination();
    }

    function displayPage(page) {
        const tbody = $('#completed-evaluations-table tbody');
        tbody.empty();
        
        const startIndex = (page - 1) * ITEMS_PER_PAGE;
        const endIndex = startIndex + ITEMS_PER_PAGE;
        const pageData = currentData.slice(startIndex, endIndex);
        
        if(pageData.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <p class="mb-0">No completed evaluations found</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }
        
        pageData.forEach(eval => {
            tbody.append(`
                <tr>
                    <td>${formatDate(eval.date_taken)}</td>
                    <td>
                        <div class="fw-semibold">${eval.faculty_name}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">${eval.subject_code}</div>
                        <small class="text-muted">${eval.subject_name}</small>
                    </td>
                    <td>${eval.class}</td>
                    <td>${eval.academic_period}</td>
                    <td>
                        ${eval.comment ? 
                            `<span class="text-truncate d-inline-block" style="max-width: 200px;">${eval.comment}</span>` 
                            : '<span class="text-muted">-</span>'}
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm view-evaluation" data-evaluation-id="${eval.evaluation_id}">
                            View
                        </button>
                    </td>
                </tr>
            `);
        });

        // Reattach event handlers
        $('.view-evaluation').click(function() {
            const evaluationId = $(this).data('evaluation-id');
            showEvaluationDetails(evaluationId);
        });
    }

    function renderPagination() {
        const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
        
        // Add pagination container after the table
        if ($('.pagination-container').length === 0) {
            $('.table-responsive').after(`
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing <span class="font-medium">${((currentPage - 1) * ITEMS_PER_PAGE) + 1}</span>
                        to <span class="font-medium">${Math.min(currentPage * ITEMS_PER_PAGE, totalItems)}</span>
                        of <span class="font-medium">${totalItems}</span> results
                    </div>
                    <div class="pagination"></div>
                </div>
            `);
        }

        const pagination = $('.pagination');
        pagination.empty();

        // Previous button
        pagination.append(`
            <button class="pagination-button nav-button" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>
        `);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
                pagination.append(`
                    <button class="pagination-button ${i === currentPage ? 'active' : ''}" 
                        onclick="changePage(${i})">${i}</button>
                `);
            } else if (
                i === currentPage - 2 || 
                i === currentPage + 2
            ) {
                pagination.append(`<span class="px-2">...</span>`);
            }
        }

        // Next button
        pagination.append(`
            <button class="pagination-button nav-button" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>
        `);
    }

    function changePage(page) {
        if (page < 1 || page > Math.ceil(totalItems / ITEMS_PER_PAGE)) return;
        currentPage = page;
        displayPage(currentPage);
        renderPagination();
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function showEvaluationDetails(evaluationId) {
        $.ajax({
            url: 'ajax.php?action=get_evaluation_details',
            method: 'POST',
            data: { evaluation_id: evaluationId },
            success: function(response) {
                console.log('Raw response:', response); // Debug line
                try {
                    const result = JSON.parse(response);
                    if(result.status === 'success') {
                        displayEvaluationModal(result.data);
                    } else {
                        console.error('Error response:', result); // Debug line
                        alert(result.message || 'Failed to load evaluation details');
                    }
                } catch(e) {
                    console.error('Error parsing response:', e);
                    console.error('Raw response:', response); // Debug line
                    alert('Failed to load evaluation details');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                }); // Enhanced error logging
                alert('Failed to load evaluation details');
            }
        });
    }

    function displayEvaluationModal(details) {
        const modal = `
            <div class="modal" id="evaluationDetailsModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="ri-file-list-3-line me-2"></i> Evaluation Questionnaires
                            </h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <h5 class="text-center mb-5 mt-3 academic-year">Academic Year: ${details.academic_period}</h5>
                            
                            <div class="rating-legend">
                                <span class="badge">5 - Strongly Agree</span>
                                <span class="badge">4 - Agree</span>
                                <span class="badge">3 - Uncertain</span>
                                <span class="badge">2 - Disagree</span>
                                <span class="badge">1 - Strongly Disagree</span>
                            </div>

                            <div class="evaluation-details ">
                                ${formatEvaluationDetails(details)}
                            </div>

                            ${details.comment ? `
                                <div class="comment-section mt-4">
                                    <h6 class="text-muted mb-3">Additional Comments (Optional)</h6>
                                    <div class="form-control" style="min-height: 100px; resize: none;" readonly>
                                        ${details.comment}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-close-modal" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#evaluationDetailsModal').remove();
        
        // Add new modal to body
        $('body').append(modal);
        
        // Show the modal using jQuery
        $('#evaluationDetailsModal').modal('show');

        // Add event listener for the close buttons
        $('.close, .btn-close-modal').on('click', function() {
            $('#evaluationDetailsModal').modal('hide');
        });
    }

    function formatEvaluationDetails(details) {
        let html = '';
        
        // Group questions by criteria
        const questionsByCriteria = {};
        details.questions.forEach(question => {
            if (!questionsByCriteria[question.criteria]) {
                questionsByCriteria[question.criteria] = [];
            }
            questionsByCriteria[question.criteria].push(question);
        });
        
        // Format questions by criteria
        Object.entries(questionsByCriteria).forEach(([criteria, questions]) => {
            html += `
                <div class="criteria-section mb-4">
                    <h6 class="fw-bold">${criteria}</h6>
                    ${questions.map((question, index) => `
                        <div class="question-item">
                            <div class="mb-2">${index + 1}. ${question.question}</div>
                            <div class="rating-options">
                                ${[1, 2, 3, 4, 5].map(rating => `
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                            name="question_${question.id}" 
                                            value="${rating}" 
                                            ${rating === parseInt(question.answer) ? 'checked' : ''} 
                                            disabled>
                                        <label class="form-check-label">${rating}</label>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        });
        
        return html;
    }

    $('#search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        const filteredData = currentData.filter(eval => {
            return Object.values(eval).some(value => 
                String(value).toLowerCase().includes(searchTerm)
            );
        });
        currentPage = 1;
        displayEvaluations(filteredData);
    });
    </script>
</body>
</html>