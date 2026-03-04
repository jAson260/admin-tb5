<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\tor-grades\download-tor-csv.php
session_start();
require_once('../../db-connect.php');

$id = $_GET['id'] ?? 0;

try {
    // Get TOR record
    $stmt = $pdo->prepare("SELECT * FROM tor_records WHERE Id = ?");
    $stmt->execute([$id]);
    $tor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tor) {
        die('TOR record not found');
    }
    
    // Prepare data for CSV generation
    $data = [
        'student_id' => $tor['StudentId'],
        'course_id' => $tor['CourseId'],
        'graduation_date' => $tor['GraduationDate'],
        'iso_number' => $tor['ISONumber'],
        'theoretical_grade' => $tor['TheoreticalGrade'],
        'practical_grade' => $tor['PracticalGrade'],
        'average_grade' => $tor['AverageGrade'],
        'final_grade' => $tor['FinalGrade'],
        'remarks' => $tor['Remarks']
    ];
    
    // Redirect to generate-tor-csv.php with data
    $_POST['data'] = json_encode($data);
    include('generate-tor-csv.php');
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>