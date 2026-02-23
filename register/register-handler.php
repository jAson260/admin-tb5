<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('../db-connect.php');

// Set header for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data (no need for sanitize_input with PDO prepared statements)
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['surname'] ?? '');
$middleName = trim($_POST['mi'] ?? '');
$extension = trim($_POST['extension'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$sex = trim($_POST['sex'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$uli = trim($_POST['uli'] ?? '');
$region = trim($_POST['region'] ?? '');
$province = trim($_POST['province'] ?? '');
$city = trim($_POST['city'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$secondarySchool = trim($_POST['secondary_school'] ?? '');
$secondaryYear = trim($_POST['secondary_year'] ?? '');
$tertiarySchool = trim($_POST['tertiary_school'] ?? '');
$tertiaryYear = trim($_POST['tertiary_year'] ?? '');
$password = $_POST['password'] ?? '';

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
    empty($sex) || empty($dob) || empty($uli) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if email already exists
    $checkEmail = $pdo->prepare("SELECT Id FROM studentinfos WHERE Email = ?");
    $checkEmail->execute([$email]);
    
    if ($checkEmail->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Calculate age from date of birth
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    
    // Hash password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Current datetime for EntryDate
    $entryDate = date('Y-m-d H:i:s');
    
    // Convert date of birth to datetime format
    $birthDateTime = $dob . ' 00:00:00';
    
    // Set default values for fields not in form
    $nationality = 'Filipino';
    $civilStatus = 'Single';
    $employment = 'Student';
    $birthPlace = $city . ', ' . $province;
    $district = '';
    $street = '';
    $status = 'Pending';
    
    // Convert empty year values to NULL
    $secondaryYear = !empty($secondaryYear) ? (int)$secondaryYear : null;
    $tertiaryYear = !empty($tertiaryYear) ? (int)$tertiaryYear : null;
    
    // Insert into database
    $sql = "INSERT INTO studentinfos (
        ULI, FirstName, LastName, MiddleName, ExtensionName, Email, 
        Nationality, BirthDate, Sex, CivilStatus, Employment, 
        Age, EntryDate, BirthPlace, BarangayName, CityName, 
        ContactNo, District, ProvinceName, RegionName, Street, 
        SecondarySchool, SecondaryYearCompleted, 
        TertiarySchool, TertiaryYearCompleted,
        Password, Status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        $uli, $firstName, $lastName, $middleName, $extension, $email,
        $nationality, $birthDateTime, $sex, $civilStatus, $employment,
        $age, $entryDate, $birthPlace, $barangay, $city,
        $phone, $district, $province, $region, $street,
        $secondarySchool, $secondaryYear, 
        $tertiarySchool, $tertiaryYear,
        $hashedPassword, $status
    ]);
    
    if ($result) {
        $studentId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful',
            'student_id' => $studentId
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Registration failed'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>