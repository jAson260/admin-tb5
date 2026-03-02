<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\documents-approval\approve-document.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

error_log("Approve request: " . print_r($data, true));

if (!isset($data['id']) || !isset($data['type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$documentId = $data['id'];
$documentType = $data['type'];

// Map document type to status field
$statusFieldMap = [
    'PSA Birth Certificate' => 'PSAStatus',
    'Transcript of Records' => 'TORStatus',
    'Diploma' => 'DiplomaStatus',
    'Form 137' => 'Form137Status',
    'ALS Certificate' => 'ALSCertificateStatus',
    'Marriage Certificate' => 'MarriageCertificateStatus',
    'Barangay Indigency' => 'BarangayIndigencyStatus',
    'Certificate of Residency' => 'CertificateOfResidencyStatus'
];

if (!isset($statusFieldMap[$documentType])) {
    echo json_encode(['success' => false, 'message' => 'Invalid document type']);
    exit;
}

$statusField = $statusFieldMap[$documentType];

try {
    // Update the specific document status to 'approved'
    $sql = "UPDATE documents SET {$statusField} = 'approved' WHERE Id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$documentId]);
    
    if ($result) {
        error_log("Document approved successfully: ID={$documentId}, Type={$documentType}");
        echo json_encode([
            'success' => true,
            'message' => 'Document approved successfully'
        ]);
    } else {
        error_log("Failed to approve document: ID={$documentId}");
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update document status'
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Approve Document Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>