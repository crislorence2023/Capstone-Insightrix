<?php
include 'db_connect.php';

function ordinal_suffix($num) {
    $num = $num % 100;
    if($num < 11 || $num > 13) {
        switch($num % 10) {
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}

if(isset($_POST['faculty_id']) && isset($_POST['subject_id']) && isset($_POST['class_id'])) {
    $faculty_id = $_POST['faculty_id'];
    $subject_id = $_POST['subject_id'];
    $class_id = $_POST['class_id'];
    
    try {
        $query = "SELECT a.year, a.semester, AVG(ea.rate) as average_rating 
                  FROM evaluation_list e 
                  JOIN academic_list a ON e.academic_id = a.id
                  JOIN evaluation_answers ea ON e.evaluation_id = ea.evaluation_id
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
                'average_rating' => number_format($row['average_rating'], 2)
            );
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $historical_data
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]);
}
?>