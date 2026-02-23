<?php
// filepath: c:\laragon\www\admin-tb5\enrollment\upload-documents-handler.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('../db-connect.php');

// Set header for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Define upload directory
$uploadDir = '../uploads/documents/';

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Allowed file types
$allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
$allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Function to validate and upload file
function uploadFile($file, $uploadDir, $allowedTypes, $allowedExtensions, $maxFileSize, $prefix) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No file uploaded (optional fields)
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Upload error for $prefix: " . $file['error']);
    }
    
    // Check file size
    if ($file['size'] > $maxFileSize) {
        throw new Exception("File $prefix is too large. Maximum size is 5MB.");
    }
    
    // Check file type (removed finfo_close - it's automatic in PHP 8.5+)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    // finfo_close() removed - deprecated in PHP 8.5
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception("Invalid file type for $prefix. Only PDF, JPG, and PNG are allowed.");
    }
    
    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception("Invalid file extension for $prefix.");
    }
    
    // Generate unique filename
    $fileName = $prefix . '_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception("Failed to move uploaded file $prefix.");
    }
    
    return $fileName;
}

try {
    // Check if student already has documents
    $checkStmt = $pdo->prepare("SELECT Id FROM documents WHERE StudentInfoId = ?");
    $checkStmt->execute([$_SESSION['user_id']]);
    $existingDoc = $checkStmt->fetch();
    
    if ($existingDoc) {
        echo json_encode(['success' => false, 'message' => 'You have already uploaded documents. Contact admin to update.']);
        exit;
    }
    
    // Upload files
    $psaPath = uploadFile($_FILES['psa_file'] ?? null, $uploadDir, $allowedTypes, $allowedExtensions, $maxFileSize, 'psa');
    $torPath = uploadFile($_FILES['tor_file'] ?? null, $uploadDir, $allowedTypes, $allowedExtensions, $maxFileSize, 'tor');
    $diplomaPath = uploadFile($_FILES['diploma_file'] ?? null, $uploadDir, $allowedTypes, $allowedExtensions, $maxFileSize, 'diploma');
    $marriageCertPath = uploadFile($_FILES['marriage_cert'] ?? null, $uploadDir, $allowedTypes, $allowedExtensions, $maxFileSize, 'marriage');
    
    // Validate required files
    if (!$psaPath || !$torPath || !$diplomaPath) {
        throw new Exception("PSA Birth Certificate, TOR, and Diploma are required.");
    }
    
    // Insert into database
    $sql = "INSERT INTO documents (
        PSAPath, TORPath, DiplomaPath, MarriageCertificatePath, 
        StudentInfoId, PSAStatus, Form137Status, UploadedAt
    ) VALUES (?, ?, ?, ?, ?, 'pending', 'pending', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $psaPath,
        $torPath,
        $diplomaPath,
        $marriageCertPath,
        $_SESSION['user_id']
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Documents uploaded successfully'
        ]);
    } else {
        throw new Exception("Failed to save document information to database.");
    }
    
} catch(Exception $e) {
    // Clean up uploaded files on error
    if (isset($psaPath) && file_exists($uploadDir . $psaPath)) {
        unlink($uploadDir . $psaPath);
    }
    if (isset($torPath) && file_exists($uploadDir . $torPath)) {
        unlink($uploadDir . $torPath);
    }
    if (isset($diplomaPath) && file_exists($uploadDir . $diplomaPath)) {
        unlink($uploadDir . $diplomaPath);
    }
    if (isset($marriageCertPath) && file_exists($uploadDir . $marriageCertPath)) {
        unlink($uploadDir . $marriageCertPath);
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>