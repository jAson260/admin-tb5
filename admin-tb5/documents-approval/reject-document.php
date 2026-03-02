<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\documents-approval\reject-document.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

error_log("Reject request: " . print_r($data, true));

if (!isset($data['id']) || !isset($data['type']) || !isset($data['reason'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$documentId = $data['id'];
$documentType = $data['type'];
$reason = $data['reason'];

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
    // Update the specific document status to 'rejected' and add remarks
    $sql = "UPDATE documents SET {$statusField} = 'rejected', Remarks = ? WHERE Id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$reason, $documentId]);
    
    if ($result) {
        error_log("Document rejected successfully: ID={$documentId}, Type={$documentType}");
        echo json_encode([
            'success' => true,
            'message' => 'Document rejected successfully'
        ]);
    } else {
        error_log("Failed to reject document: ID={$documentId}");
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update document status'
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Reject Document Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>