<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\tor-grades\generate-tor.php
session_start();
require_once('../../db-connect.php');
require_once('../../vendor/autoload.php');

header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Get JSON input
$input = file_get_contents('php://input');
error_log('TOR Generation - Input received: ' . $input);

$data = json_decode($input, true);

// Check if data was received
if (!$data) {
    error_log('TOR Generation - No data received or JSON decode failed');
    echo json_encode(['success' => false, 'message' => 'No data received or invalid JSON']);
    exit;
}

// Check required fields
$requiredFields = ['student_id', 'theoretical_grade', 'practical_grade', 'graduation_date', 'iso_number', 'remarks'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    $message = 'Missing required fields: ' . implode(', ', $missingFields);
    error_log('TOR Generation - ' . $message);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
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

error_log("TOR Generation - Processing for Student ID: {$studentId}, Course ID: " . ($courseId ?: 'Generic'));

try {
    // Fetch student data
    $studentStmt = $pdo->prepare("SELECT * FROM studentinfos WHERE Id = ?");
    $studentStmt->execute([$studentId]);
    $student = $studentStmt->fetch();

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    // Get course if selected
    $course = null;
    if ($courseId) {
        $courseStmt = $pdo->prepare("SELECT * FROM courses WHERE Id = ?");
        $courseStmt->execute([$courseId]);
        $course = $courseStmt->fetch();
        
        if (!$course) {
            echo json_encode(['success' => false, 'message' => 'Selected course not found']);
            exit;
        }
    } else {
        // Generic course if none selected
        $course = [
            'Id' => 0,
            'CourseName' => 'General TVET Training',
            'CourseCode' => 'N/A',
            'Category' => 'NC II'
        ];
    }

    error_log("Processing course: " . $course['CourseName']);

    // Create custom TCPDF class
    class TVETRecord extends TCPDF {
        public function Header() {
            // Left logo - TB5
            if (file_exists('../../assets/img/tb5-logo.png')) {
                $this->Image('../../assets/img/tb5-logo.png', 15, 10, 18, '', 'PNG');
            }
            
            // Header text
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(40, 10);
            $this->Cell(0, 4, 'Records of Candidates for Graduation from TVET Courses', 0, 1, 'L');
            
            $this->SetFont('helvetica', 'B', 9);
            $this->SetX(40);
            $this->Cell(0, 4, 'The Big Five Training and Assessment Center Inc.', 0, 1, 'L');
            
            $this->SetFont('helvetica', '', 7);
            $this->SetX(40);
            $this->Cell(0, 3, '4th Floor Vilco Bldg. 123 P. Alcantara St. Brgy. VII-B, San Pablo City, Laguna', 0, 1, 'L');
            
            // FORM IX text (top right)
            $this->SetXY(175, 10);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(20, 5, 'FORM IX', 0, 0, 'R');
        }
        
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, '*** End of the Record***', 0, false, 'C');
        }
    }

    // Create PDF
    $pdf = new TVETRecord('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('The Big Five Training Center');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('TVET Graduation Record - ' . $student['FirstName'] . ' ' . $student['LastName']);
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();
    
    // Course and Date of Graduation
    $pdf->SetY(28);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(120, 6, 'Course / Qualification: ' . strtoupper($course['CourseName'] . ' ' . ($course['Category'] ?? '')), 1, 0, 'L');
    $pdf->Cell(60, 6, 'Date of Graduation: ' . date('m/d/Y', strtotime($graduationDate)), 1, 1, 'L');
    
    // PERSONAL RECORDS Section
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, 5, 'PERSONAL RECORDS', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', '', 8);
    
    // ULI and ISO Number
    $uli = $student['ULI'] ?? 'N/A';
    $pdf->Cell(40, 5, 'ULI :', 1, 0, 'L');
    $pdf->Cell(80, 5, $uli, 1, 0, 'L');
    $pdf->Cell(20, 5, 'ISO Number', 1, 0, 'L');
    $pdf->Cell(40, 5, $isoNumber, 1, 1, 'L');
    
    // Name of Trainee
    $middleInitial = !empty($student['MiddleName']) ? strtoupper(substr($student['MiddleName'], 0, 1)) . '.' : '';
    $extension = !empty($student['ExtensionName']) ? ' ' . strtoupper($student['ExtensionName']) : '';
    $fullName = strtoupper($student['LastName']) . ', ' . strtoupper($student['FirstName']) . ' ' . $middleInitial . $extension;
    
    $pdf->Cell(40, 5, 'Name of Trainee', 1, 0, 'L');
    $pdf->Cell(140, 5, $fullName, 1, 1, 'L');
    
    // Gender
    $pdf->Cell(40, 5, '', 1, 0, 'L');
    $pdf->Cell(140, 5, strtoupper($student['Sex']), 1, 1, 'L');
    
    // Date and Place of Birth
    $dob = isset($student['BirthDate']) ? date('m/d/Y', strtotime($student['BirthDate'])) : 'N/A';
    $pob = $student['BirthPlace'] ?? 'N/A';
    
    $pdf->Cell(40, 5, 'Date of Birth:', 1, 0, 'L');
    $pdf->Cell(50, 5, $dob, 1, 0, 'L');
    $pdf->Cell(30, 5, 'Place of Birth:', 1, 0, 'L');
    $pdf->Cell(60, 5, $pob, 1, 1, 'L');
    
    // RECORD OF CANDIDATES FOR GRADUATION Section
    $pdf->Ln(1);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, 5, 'RECORD OF CANDIDATES FOR GRADUATION', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', '', 8);
    
    // Secondary Completed
    $secondary = $student['SecondarySchool'] ?? 'N/A';
    $secYear = $student['SecondaryYearCompleted'] ?? 'N/A';
    $pdf->Cell(40, 5, 'Secondary Completed', 1, 0, 'L');
    $pdf->Cell(90, 5, $secondary, 1, 0, 'L');
    $pdf->Cell(25, 5, 'Year Graduated', 1, 0, 'L');
    $pdf->Cell(25, 5, $secYear, 1, 1, 'C');
    
    // College/Vocational School
    $college = $student['TertiarySchool'] ?? 'N/A';
    $colYear = $student['TertiaryYearCompleted'] ?? 'N/A';
    $pdf->Cell(40, 5, 'College/Vocational School', 1, 0, 'L');
    $pdf->Cell(90, 5, $college, 1, 0, 'L');
    $pdf->Cell(25, 5, 'Year Graduated', 1, 0, 'L');
    $pdf->Cell(25, 5, $colYear, 1, 1, 'C');
    
    // Entrance Date
    $entranceDate = isset($student['EntryDate']) ? date('m/d/Y', strtotime($student['EntryDate'])) : 'N/A';
    $pdf->Cell(40, 5, 'Entrance Date', 1, 0, 'L');
    $pdf->Cell(140, 5, $entranceDate, 1, 1, 'L');
    
    // ACADEMIC RECORD Section
    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(180, 6, 'ACADEMIC RECORD', 1, 1, 'C', true);
    
    // Table Headers
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(15, 10, 'Code Number', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Descriptive Title of Subjects', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Nominal Duration', 1, 0, 'C');
    
    // Grades header with sub-headers
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->Cell(55, 5, 'Grades', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Remarks', 1, 1, 'C');
    
    // Sub-headers for Grades
    $pdf->SetXY($x, $y + 5);
    $pdf->Cell(15, 5, 'THEORE TICAL 30%', 1, 0, 'C');
    $pdf->Cell(15, 5, 'ACTUAL 70%', 1, 0, 'C');
    $pdf->Cell(25, 5, 'FINAL GRADE', 1, 0, 'C');
    $pdf->Ln();
    
    // Sample Academic Records (Basic Competencies)
    $pdf->SetFont('helvetica', '', 7);
    
    $academics = [
        ['50G311105', 'Participate in workplace communications', '4 hours', $theoreticalGrade, $practicalGrade, number_format($averageGrade, 0), $remarks],
        ['50G311106', 'Work in team environments', '4 hours', $theoreticalGrade, $practicalGrade, number_format($averageGrade, 0), $remarks],
        ['50G311107', 'Practice career professionalism', '6 hours', $theoreticalGrade, $practicalGrade, number_format($averageGrade, 0), $remarks],
        ['50G311108', 'Practice occupational health and safety procedures', '4 hours', $theoreticalGrade, $practicalGrade, number_format($averageGrade, 0), $remarks],
    ];
    
    // Add Basic Competencies header
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(180, 5, 'Basic Competencies', 1, 1, 'L', true);
    
    $pdf->SetFont('helvetica', '', 7);
    foreach ($academics as $record) {
        $pdf->Cell(15, 5, $record[0], 1, 0, 'C');
        $pdf->Cell(50, 5, $record[1], 1, 0, 'L');
        $pdf->Cell(20, 5, $record[2], 1, 0, 'C');
        $pdf->Cell(15, 5, $record[3], 1, 0, 'C');
        $pdf->Cell(15, 5, $record[4], 1, 0, 'C');
        $pdf->Cell(25, 5, $record[5], 1, 0, 'C');
        $pdf->Cell(40, 5, $record[6], 1, 1, 'C');
    }
    
    // Common Competencies
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(180, 5, 'Common Competencies', 1, 1, 'L', true);
    
    $pdf->SetFont('helvetica', '', 7);
    $commonCompetencies = [
        ['TRS311201', 'Develop and update industry knowledge', '2 hours'],
        ['TRS311202', 'Observe workplace hygiene procedures', '4 hours'],
        ['TRS311203', 'Perform computer operations', '4 hours'],
        ['TRS311204', 'Perform workplace and safety practices', '4 hours'],
        ['TRS311205', 'Provide effective customer service', '4 hours'],
    ];
    
    foreach ($commonCompetencies as $record) {
        $pdf->Cell(15, 5, $record[0], 1, 0, 'C');
        $pdf->Cell(50, 5, $record[1], 1, 0, 'L');
        $pdf->Cell(20, 5, $record[2], 1, 0, 'C');
        $pdf->Cell(15, 5, $theoreticalGrade, 1, 0, 'C');
        $pdf->Cell(15, 5, $practicalGrade, 1, 0, 'C');
        $pdf->Cell(25, 5, number_format($averageGrade, 0), 1, 0, 'C');
        $pdf->Cell(40, 5, $remarks, 1, 1, 'C');
    }
    
    // Core Competencies (if course specific)
    if ($courseId && $course['CourseName'] != 'General TVET Training') {
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(180, 5, 'Core Competencies', 1, 1, 'L', true);
        
        $pdf->SetFont('helvetica', '', 7);
        // Add course-specific competencies here
        $pdf->Cell(15, 5, 'TRS741379', 1, 0, 'C');
        $pdf->Cell(50, 5, 'Prepare and produce bakery products', 1, 0, 'L');
        $pdf->Cell(20, 5, '24 hours', 1, 0, 'C');
        $pdf->Cell(15, 5, $theoreticalGrade, 1, 0, 'C');
        $pdf->Cell(15, 5, $practicalGrade, 1, 0, 'C');
        $pdf->Cell(25, 5, number_format($averageGrade, 0), 1, 0, 'C');
        $pdf->Cell(40, 5, $remarks, 1, 1, 'C');
    }
    
    // Totals
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(65, 5, 'On-The-Job Training', 1, 0, 'L');
    $pdf->Cell(20, 5, '40 hours', 1, 0, 'C');
    $pdf->Cell(15, 5, '', 1, 0, 'C');
    $pdf->Cell(15, 5, '', 1, 0, 'C');
    $pdf->Cell(25, 5, '', 1, 0, 'C');
    $pdf->Cell(40, 5, $remarks, 1, 1, 'C');
    
    $pdf->Cell(65, 5, 'Total Hours', 1, 0, 'L');
    $totalHours = $course['DurationHours'] ?? 181;
    $pdf->Cell(20, 5, $totalHours, 1, 0, 'C');
    $pdf->Cell(15, 5, '', 1, 0, 'C');
    $pdf->Cell(15, 5, '', 1, 0, 'C');
    $pdf->Cell(25, 5, number_format($finalGrade, 0), 1, 0, 'C');
    $pdf->Cell(40, 5, '', 1, 1, 'C');
    
    // Grading System
    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(0, 5, 'GRADING SYSTEM', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(60, 4, '97% and above  Excellent', 0, 0, 'L');
    $pdf->Cell(60, 4, 'Competent', 0, 1, 'L');
    
    $pdf->Cell(60, 4, '96% - 95%  Very Good', 0, 0, 'L');
    $pdf->Cell(60, 4, 'Competent', 0, 1, 'L');
    
    $pdf->Cell(60, 4, '93% - 92%  Good', 0, 0, 'L');
    $pdf->Cell(60, 4, 'Competent', 0, 1, 'L');
    
    $pdf->Cell(60, 4, '90% - 87%  Satisfactory', 0, 0, 'L');
    $pdf->Cell(60, 4, 'Competent', 0, 1, 'L');
    
    $pdf->Cell(60, 4, '85% - 87%  Passed', 0, 0, 'L');
    $pdf->Cell(60, 4, 'Competent', 0, 1, 'L');
    
    $pdf->Cell(60, 4, 'Below 85% Failed', 0, 0, 'L');
    $pdf->Cell(60, 4, 'Not Yet Competent', 0, 1, 'L');
    
    // Certification
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(0, 5, 'CERTIFICATION', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 8);
    $certText = "I certify that the following records of {$fullName}, a candidate for graduation in this institution have been verified by me, and that the true copies of the official record sustaining the same are kept in this file of our school. I do certify that this student enrolled in this institution on " . date('F d, Y', strtotime($student['EntryDate'])) . " to " . date('F d, Y', strtotime($graduationDate)) . ".";
    $pdf->MultiCell(0, 4, $certText, 0, 'J');
    
    // Signatures
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(90, 5, 'Verified by: TRINA M. VILLAMERO', 0, 0, 'L');
    $pdf->Cell(90, 5, 'Approved by: CHRISTOPHER M. SANTOS', 0, 1, 'R');
    
    $pdf->SetFont('helvetica', 'I', 7);
    $pdf->Cell(90, 4, 'Registrar', 0, 0, 'L');
    $pdf->Cell(90, 4, 'TVI President', 0, 1, 'R');
    
    // Save PDF with absolute path
    $courseIdForFile = $course['Id'] > 0 ? $course['Id'] : 'none';
    $fileName = 'TOR_' . $student['Id'] . '_' . $courseIdForFile . '_' . time() . '.pdf';
    
    // Use absolute Windows path
    $baseDir = 'C:/laragon/www/admin-tb5';
    $uploadDir = $baseDir . '/uploads/tor/';
    $filePath = $uploadDir . $fileName;
    
    error_log("Attempting to save PDF to: " . $filePath);
    
    // Output PDF
    try {
        $pdf->Output($filePath, 'F');
        error_log("PDF saved successfully: " . $filePath);
    } catch (Exception $e) {
        error_log("PDF Output error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to save PDF: ' . $e->getMessage()
        ]);
        exit;
    }
    
    // Verify file was created
    if (!file_exists($filePath)) {
        error_log("PDF file was not created at: " . $filePath);
        echo json_encode([
            'success' => false, 
            'message' => 'PDF file was not created'
        ]);
        exit;
    }
    
    $fileSize = filesize($filePath);
    error_log("PDF file size: " . $fileSize . " bytes");
    
    // Save to database
    try {
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
            $fileName,
            $fileSize,
            $_SESSION['admin_id'] ?? NULL
        ]);
        error_log("Database record saved - TOR ID: " . $pdo->lastInsertId());
    } catch (PDOException $e) {
        error_log("Database save error: " . $e->getMessage());
        // Continue anyway - PDF is already created
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'TOR generated successfully',
        'pdf_url' => '/uploads/tor/' . $fileName
    ]);
    
} catch (Exception $e) {
    error_log('TOR Generation Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Error generating TOR: ' . $e->getMessage()
    ]);
}
?>