<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Evaluations</title>
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
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #edf2f7;
        }

        .comment-section h6 {
            color: #2d3748;
            font-weight: 600;
        }

        .modal-title {
            color: #1a5f7a;
            font-weight: 700;
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
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            background-color: #f8f9fa !important;
        }
        .text-muted {
            color: #6c757d !important;
            font-weight: 500 !important;
        }
    </style>
</head>
<body>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-bold">Completed Evaluations</h3>
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

    <script>
    $(document).ready(function(){
        loadCompletedEvaluations();
    });

    function loadCompletedEvaluations() {
        $.ajax({
            url: 'ajax.php?action=get_completed_evaluations',
            method: 'GET',
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
        const tbody = $('#completed-evaluations-table tbody');
        tbody.empty();
        
        if(evaluations.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="text-center">No completed evaluations found</td>
                </tr>
            `);
            return;
        }
        
        evaluations.forEach(eval => {
            tbody.append(`
                <tr>
                    <td>${eval.date_taken}</td>
                    <td>${eval.faculty_name}</td>
                    <td>${eval.subject_code} - ${eval.subject_name}</td>
                    <td>${eval.class}</td>
                    <td>${eval.academic_period}</td>
                    <td>${eval.comment || '-'}</td>
                    <td>
                        <button class="btn btn-primary btn-sm view-evaluation" data-evaluation-id="${eval.evaluation_id}">
                            <i class="fa fa-eye"></i> View
                        </button>
                    </td>
                </tr>
            `);
        });

        // Add click event handler for view buttons
        $('.view-evaluation').click(function() {
            const evaluationId = $(this).data('evaluation-id');
            showEvaluationDetails(evaluationId);
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
                            <h4 class="modal-title">Evaluation Details</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <h5 class="text-center mb-3 academic-year">Academic Year: ${details.academic_period}</h5>
                            
                            <div class="rating-legend">
                                <span class="badge">5 - Strongly Agree</span>
                                <span class="badge">4 - Agree</span>
                                <span class="badge">3 - Uncertain</span>
                                <span class="badge">2 - Disagree</span>
                                <span class="badge">1 - Strongly Disagree</span>
                            </div>

                            <div class="evaluation-details">
                                ${formatEvaluationDetails(details)}
                            </div>

                            ${details.comment ? `
                                <div class="comment-section mt-4">
                                    <h6 class="text-muted mb-3">Additional Comments (Optional)</h6>
                                    <div class="form-control bg-light" style="min-height: 100px; resize: none;" readonly>
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
    </script>
</body>
</html>