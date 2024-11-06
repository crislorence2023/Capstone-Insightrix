<?php
// Include your database connection file here
include 'db_connect.php';

// Check if the function is not already defined
if (!function_exists('ordinal_suffix')) {
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
}

// Query to get all academic years and semesters
$query = "SELECT * FROM academic_list ORDER BY year DESC, semester DESC";
$result = $conn->query($query);

// Check if query was successful
if ($result) {
    $academic_years = array();
    while ($row = $result->fetch_assoc()) {
        $academic_years[] = array(
            'id' => $row['id'],
            'year' => $row['year'],
            'semester' => $row['semester'],
            'is_default' => $row['is_default'],
            'status' => $row['status']
        );
    }

    // Generate the dropdown HTML
    echo '<select id="academic_year" name="academic_year" class="form-control">';
    echo '<option value="">Select Academic Year</option>';
    foreach ($academic_years as $ay) {
        $selected = $ay['is_default'] ? 'selected' : '';
        echo '<option value="' . $ay['id'] . '" ' . $selected . '>';
        echo $ay['year'] . ' - ' . ordinal_suffix($ay['semester']) . ' Semester';
        echo '</option>';
    }
    echo '</select>';
} else {
    echo "Error retrieving academic years: " . $conn->error;
}

// Close the database connection
$conn->close();
?>