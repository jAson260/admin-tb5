<?php

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

header('Content-Type: application/json');

$data      = json_decode(file_get_contents('php://input'), true);
$studentId = intval($data['id']     ?? 0);
$fieldType = trim($data['type']     ?? '');  // e.g. "PSA Birth Certificate"
$reason    = trim($data['reason']   ?? 'Document rejected by admin');

// ── Map document type label → column name ─────────────────────────────────────
$fieldMap = [
    'PSA Birth Certificate'        => 'PSA',
    'Diploma'                      => 'Diploma',
    'Form 137'                     => 'Form137',
    'Marriage Certificate'         => 'MarriageCertificate',
    'ALS Certificate'              => 'ALSCertificate',
    'Barangay Indigency'           => 'BarangayIndigency',
    'Certificate of Residency'     => 'CertificateOfResidency',
    'Transcript of Records (TOR)'  => 'TOR',
];

if (!$studentId || !isset($fieldMap[$fieldType])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request. Type: ' . $fieldType]);
    exit;
}

$col          = $fieldMap[$fieldType];
$statusField  = "{$col}Status";   // e.g. PSAStatus
$remarksField = "{$col}Remarks";  // e.g. PSARemarks

try {
    $stmt = $pdo->prepare("
        UPDATE documents
        SET `{$statusField}`  = 'rejected',
            `{$remarksField}` = ?
        WHERE StudentInfoId = ?
    ");
    $stmt->execute([$reason, $studentId]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'No document record found for this student.']);
        exit;
    }

    // ── Log the rejection ─────────────────────────────────────────────────────
    $logStmt = $pdo->prepare("
        INSERT INTO activity_logs (UserId, UserType, Action, ActionDetails, CreatedAt)
        VALUES (?, 'admin', 'reject_document', ?, NOW())
    ");
    $logStmt->execute([
        $_SESSION['user_id'],
        "Rejected {$fieldType} for StudentInfoId {$studentId}. Reason: {$reason}"
    ]);

    echo json_encode(['success' => true, 'message' => 'Document rejected successfully.']);

} catch (PDOException $e) {
    error_log('reject-document.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>