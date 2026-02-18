<?php
session_start();
require_once('../db-connect.php');

// Set header for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Sanitize and validate input
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Get form data
$firstName = sanitize_input($_POST['first_name'] ?? '');
$lastName = sanitize_input($_POST['surname'] ?? '');
$middleName = sanitize_input($_POST['mi'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$sex = sanitize_input($_POST['sex'] ?? '');
$dob = sanitize_input($_POST['dob'] ?? '');
$uli = sanitize_input($_POST['uli'] ?? '');
$region = sanitize_input($_POST['region'] ?? '');
$province = sanitize_input($_POST['province'] ?? '');
$city = sanitize_input($_POST['city'] ?? '');
$barangay = sanitize_input($_POST['barangay'] ?? '');
$secondarySchool = sanitize_input($_POST['secondary_school'] ?? '');
$secondaryYear = sanitize_input($_POST['secondary_year'] ?? '');
$tertiarySchool = sanitize_input($_POST['tertiary_school'] ?? '');
$tertiaryYear = sanitize_input($_POST['tertiary_year'] ?? '');
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

// Check if email already exists
$checkEmail = $conn->prepare("SELECT Id FROM studentinfos WHERE Email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$result = $checkEmail->get_result();

if ($result->num_rows > 0) {
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

// Insert into database
$sql = "INSERT INTO studentinfos (
    ULI, FirstName, LastName, MiddleName, Email, 
    Nationality, BirthDate, Sex, CivilStatus, Employment, 
    Age, EntryDate, BirthPlace, BarangayName, CityName, 
    ContactNo, District, ProvinceName, RegionName, Street, Password, Status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// Set default values for fields not in form
$nationality = 'Filipino'; // Default
$civilStatus = 'Single'; // Default
$employment = 'Student'; // Default
$birthPlace = $city . ', ' . $province; // Construct from address
$district = ''; // Not in form, set empty
$street = ''; // Not in form, set empty
$status = 'Pending';

$stmt->bind_param(
    "ssssssssssississsssssss",
    $uli, $firstName, $lastName, $middleName, $email,
    $nationality, $birthDateTime, $sex, $civilStatus, $employment,
    $age, $entryDate, $birthPlace, $barangay, $city,
    $phone, $district, $province, $region, $street, $hashedPassword, $status
);

if ($stmt->execute()) {
    $studentId = $stmt->insert_id;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Registration successful',
        'student_id' => $studentId
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>