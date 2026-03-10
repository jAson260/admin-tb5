<?php

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$id     = intval($data['id']     ?? 0);
$type   = trim($data['type']     ?? '');
$reason = trim($data['reason']   ?? '');

if (!$id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Missing document ID or type.']);
    exit;
}

if (!$reason) {
    echo json_encode(['success' => false, 'message' => 'Rejection reason is required.']);
    exit;
}

// ── Exact match from get-documents.php $documentFields ───────────────────────
$fieldMap = [
    'PSA Birth Certificate'    => 'PSAStatus',
    'Transcript of Records'    => 'TORStatus',
    'Diploma'                  => 'DiplomaStatus',
    'Form 137'                 => 'Form137Status',
    'ALS Certificate'          => 'ALSCertificateStatus',
    'Marriage Certificate'     => 'MarriageCertificateStatus',
    'Barangay Indigency'       => 'BarangayIndigencyStatus',
    'Certificate of Residency' => 'CertificateOfResidencyStatus',
];

$remarksMap = [
    'PSA Birth Certificate'    => 'PSARemarks',
    'Transcript of Records'    => 'Remarks',
    'Diploma'                  => 'DiplomaRemarks',
    'Form 137'                 => 'Form137Remarks',
    'ALS Certificate'          => 'ALSCertificateRemarks',
    'Marriage Certificate'     => 'MarriageCertificateRemarks',
    'Barangay Indigency'       => 'BarangayIndigencyRemarks',
    'Certificate of Residency' => 'CertificateOfResidencyRemarks',
];

if (!isset($fieldMap[$type])) {
    echo json_encode(['success' => false, 'message' => 'Invalid document type: ' . $type]);
    exit;
}

$statusCol  = $fieldMap[$type];
$remarksCol = $remarksMap[$type];

try {
    $stmt = $pdo->prepare("
        UPDATE documents
        SET `{$statusCol}`  = 'rejected',
            `{$remarksCol}` = ?
        WHERE Id = ?
    ");
    $stmt->execute([$reason, $id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Document not found.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => $type . ' rejected successfully.']);

} catch (PDOException $e) {
    error_log('reject-document.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>