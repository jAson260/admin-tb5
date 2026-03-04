<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\tor-grades\generate-tor-csv.php
session_start();
require_once('../../db-connect.php');

// Suppress all error output to prevent corrupting CSV
ini_set('display_errors', 0);
error_reporting(0);

// Get data from POST or php://input
if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
} else {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
}

if (!$data || !isset($data['student_id'])) {
    die('No data received or invalid data format');
}

$studentId = $data['student_id'];
$courseId = $data['course_id'] ?? null;
$graduationDate = $data['graduation_date'];
$isoNumber = $data['iso_number'];
$theoreticalGrade = floatval($data['theoretical_grade']);
$practicalGrade = floatval($data['practical_grade']);
$averageGrade = floatval($data['average_grade']);
$finalGrade = floatval($data['final_grade']);
$remarks = $data['remarks'];

// Set headers for CSV download
$fileName = 'TOR_' . $studentId . '_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Fetch student data
    $studentStmt = $pdo->prepare("SELECT * FROM studentinfos WHERE Id = ?");
    $studentStmt->execute([$studentId]);
    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die('Student not found');
    }

    // Get course if selected
    $course = null;
    if ($courseId) {
        $courseStmt = $pdo->prepare("SELECT * FROM courses WHERE Id = ?");
        $courseStmt->execute([$courseId]);
        $course = $courseStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$course) {
        $course = [
            'CourseName' => 'BREAD AND PASTRY PRODUCTION NC II',
            'CourseCode' => 'BP',
            'Category' => 'NC II'
        ];
    }

    // Open output stream
    $output = fopen('php://output', 'w');

    // Set UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Define escape character for PHP 8.4+ compatibility
    $escape = "\\";

    // Header Section
    fputcsv($output, ['RECORDS OF CANDIDATES FOR GRADUATION FROM TVET COURSES'], ',', '"', $escape);
    fputcsv($output, ['The Big Five Training and Assessment Center Inc.'], ',', '"', $escape);
    fputcsv($output, ['4th Floor Vilco Bldg. 123 P. Alcantara St. Brgy. VII-B, San Pablo City, Laguna'], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);

    // Course and Graduation Date
    fputcsv($output, ['Course/Qualification:', strtoupper($course['CourseName']), '', 'Date of Graduation:', date('m/d/Y', strtotime($graduationDate))], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);

    // Personal Records Section
    fputcsv($output, ['=== PERSONAL RECORDS ==='], ',', '"', $escape);
    
    $middleInitial = !empty($student['MiddleName']) ? strtoupper(substr($student['MiddleName'], 0, 1)) . '.' : '';
    $extension = !empty($student['ExtensionName']) ? ' ' . strtoupper($student['ExtensionName']) : '';
    $fullName = strtoupper($student['LastName']) . ', ' . strtoupper($student['FirstName']) . ' ' . $middleInitial . $extension;
    
    fputcsv($output, ['ULI:', $student['ULI'] ?? 'N/A', '', 'SO Number:', ''], ',', '"', $escape);
    fputcsv($output, ['Name of Trainee:', $fullName], ',', '"', $escape);
    fputcsv($output, ['Sex:', strtoupper($student['Sex'])], ',', '"', $escape);
    
    $dob = isset($student['BirthDate']) ? date('m/d/Y', strtotime($student['BirthDate'])) : '';
    $pob = strtoupper($student['BirthPlace'] ?? '');
    fputcsv($output, ['Date of Birth:', $dob, 'Place of Birth:', $pob], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);

    // Educational Background
    fputcsv($output, ['=== RECORD OF CANDIDATES FOR GRADUATION ==='], ',', '"', $escape);
    fputcsv($output, ['Elementary Completed:', $student['ElementarySchool'] ?? '', 'Year Graduated:', $student['ElementaryYearCompleted'] ?? ''], ',', '"', $escape);
    fputcsv($output, ['Secondary Completed:', $student['SecondarySchool'] ?? '', 'Year Graduated:', $student['SecondaryYearCompleted'] ?? ''], ',', '"', $escape);
    fputcsv($output, ['College/Vocational School:', $student['TertiarySchool'] ?? '', 'Year Graduated:', $student['TertiaryYearCompleted'] ?? ''], ',', '"', $escape);
    fputcsv($output, ['Entrance Date:', isset($student['EntryDate']) ? date('F d, Y', strtotime($student['EntryDate'])) : ''], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);

    // Academic Record Section
    fputcsv($output, ['=== ACADEMIC RECORD ==='], ',', '"', $escape);
    fputcsv($output, ['Code Number', 'Descriptive Title of Subjects', 'Nominal Duration', 'Theoretical Grade (30%)', 'Practical Grade (70%)', 'Final Grade', 'Remarks'], ',', '"', $escape);
    
    // Basic Competencies
    fputcsv($output, ['BASIC COMPETENCIES'], ',', '"', $escape);
    $basicCompetencies = [
        ['50G311105', 'Participate in workplace communications', '4 hours', 92, 93, '', 'Competent'],
        ['50G311106', 'Work in team environments', '4 hours', 93, 93, '', 'Competent'],
        ['50G311107', 'Practice career professionalism', '6 hours', 97, 95, '', 'Competent'],
        ['50G311108', 'Practice occupational health and safety procedures', '4 hours', 93, 95, '', 'Competent'],
    ];
    foreach ($basicCompetencies as $record) {
        fputcsv($output, $record, ',', '"', $escape);
    }
    
    // Common Competencies
    fputcsv($output, ['COMMON COMPETENCIES'], ',', '"', $escape);
    $commonCompetencies = [
        ['TRS311201', 'Develop and update industry knowledge', '2 hours', 92, 93, '', 'Competent'],
        ['TRS311202', 'Observe workplace hygiene procedures', '4 hours', 97, 97, '', 'Competent'],
        ['TRS311203', 'Perform computer operations', '4 hours', 91, 93, '', 'Competent'],
        ['TRS311204', 'Perform workplace and safety practices', '4 hours', 95, 96, '', 'Competent'],
        ['TRS311205', 'Provide effective consumer service', '4 hours', 95, 95, '', 'Competent'],
    ];
    foreach ($commonCompetencies as $record) {
        fputcsv($output, $record, ',', '"', $escape);
    }
    
    // Core Competencies
    fputcsv($output, ['CORE COMPETENCIES'], ',', '"', $escape);
    $coreCompetencies = [
        ['TRS741379', 'Prepare and produce bakery products', '24 hours', 97, 95, '', 'Competent'],
        ['TRS741380', 'Prepare and produce pastry products', '24 hours', 96, 97, '', 'Competent'],
        ['TRS741342', 'Prepare and present gâteaux, tortes and cakes', '36 hours', 92, 97, '', 'Competent'],
        ['TRS741344', 'Prepare and display petits fours', '11 hours', 97, 97, '', 'Competent'],
        ['TRS741343', 'Present desserts', '10 hours', 92, 93, '', 'Competent'],
    ];
    foreach ($coreCompetencies as $record) {
        fputcsv($output, $record, ',', '"', $escape);
    }
    
    // Totals
    fputcsv($output, ['', 'On-The-Job Training', '40 hours', '95', '97', '', 'Competent'], ',', '"', $escape);
    fputcsv($output, ['', 'TOTAL HOURS', '181', '', '', '96', ''], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);

    // Final Grades Summary
    fputcsv($output, ['=== FINAL GRADES SUMMARY ==='], ',', '"', $escape);
    fputcsv($output, ['Theoretical Grade:', $theoreticalGrade . '%'], ',', '"', $escape);
    fputcsv($output, ['Practical Grade:', $practicalGrade . '%'], ',', '"', $escape);
    fputcsv($output, ['Average Grade:', $averageGrade . '%'], ',', '"', $escape);
    fputcsv($output, ['Final Grade:', $finalGrade . '%'], ',', '"', $escape);
    fputcsv($output, ['Remarks:', $remarks], ',', '"', $escape);
    fputcsv($output, ['ISO Number:', "'" . $isoNumber], ',', '"', $escape); // Prefix with ' to prevent Excel formatting
    fputcsv($output, [''], ',', '"', $escape);

    // Grading System
    fputcsv($output, ['=== GRADING SYSTEM ==='], ',', '"', $escape);
    fputcsv($output, ['Grade Range', 'Description', 'Competency Status'], ',', '"', $escape);
    $gradingSystem = [
        ['97% and above', 'Excellent', 'Competent'],
        ['96% - 95%', 'Very Good', 'Competent'],
        ['93% - 92%', 'Good', 'Competent'],
        ['90% - 87%', 'Satisfactory', 'Competent'],
        ['86% - 85%', 'Passed', 'Competent'],
        ['Below 85%', 'Failed', 'Not Yet Competent'],
    ];
    foreach ($gradingSystem as $grade) {
        fputcsv($output, $grade, ',', '"', $escape);
    }
    fputcsv($output, [''], ',', '"', $escape);

    // Certification
    fputcsv($output, ['=== CERTIFICATION ==='], ',', '"', $escape);
    $certText = "I certify that the following records of " . $fullName . ", a candidate for graduation in this institution have been verified by me, and that the true copies of the official record sustaining the same are kept in the file of our school.";
    fputcsv($output, [$certText], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);
    
    fputcsv($output, ['Verified by:', 'TRINA M. VILLAMERO', '', 'Approved by:', 'CHRISTOPHER M. SANTOS'], ',', '"', $escape);
    fputcsv($output, ['', 'Registrar', '', '', 'TVI President'], ',', '"', $escape);
    fputcsv($output, [''], ',', '"', $escape);
    fputcsv($output, ['*** END OF THE RECORD ***'], ',', '"', $escape);

    fclose($output);

    // Save to database
    $dbFileName = 'TOR_' . $student['Id'] . '_' . time() . '.csv';
    $insertStmt = $pdo->prepare("
        INSERT INTO tor_records (
            StudentId, CourseId, TheoreticalGrade, PracticalGrade, 
            AverageGrade, FinalGrade, Remarks, ISONumber, 
            GraduationDate, FileName, FileSize, CreatedBy
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insertStmt->execute([
        $studentId, 
        $courseId ?: NULL, 
        $theoreticalGrade, 
        $practicalGrade, 
        $averageGrade, 
        $finalGrade, 
        $remarks,
        $isoNumber,
        $graduationDate,
        $dbFileName,
        0,
        $_SESSION['admin_id'] ?? NULL
    ]);

    exit;
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>