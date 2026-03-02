<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\documents-approval\get-documents.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    // Get all documents with student information
    $stmt = $pdo->query("
        SELECT 
            d.*,
            s.FirstName,
            s.MiddleName,
            s.LastName,
            s.Id as StudentInfoId
        FROM documents d
        INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
        ORDER BY d.UploadedAt DESC
    ");
    
    $documentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform data into individual document entries
    $documents = [];
    $statistics = [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'total' => 0
    ];
    
    // Document field mapping
    $documentFields = [
        'PSAPath' => ['type' => 'PSA Birth Certificate', 'status' => 'PSAStatus'],
        'TORPath' => ['type' => 'Transcript of Records', 'status' => 'TORStatus'],
        'DiplomaPath' => ['type' => 'Diploma', 'status' => 'DiplomaStatus'],
        'Form137Path' => ['type' => 'Form 137', 'status' => 'Form137Status'],
        'ALSCertificatePath' => ['type' => 'ALS Certificate', 'status' => 'ALSCertificateStatus'],
        'MarriageCertificatePath' => ['type' => 'Marriage Certificate', 'status' => 'MarriageCertificateStatus'],
        'BarangayIndigencyPath' => ['type' => 'Barangay Indigency', 'status' => 'BarangayIndigencyStatus'],
        'CertificateOfResidencyPath' => ['type' => 'Certificate of Residency', 'status' => 'CertificateOfResidencyStatus']
    ];
    
    foreach ($documentsData as $doc) {
        $studentName = trim($doc['FirstName'] . ' ' . $doc['MiddleName'] . ' ' . $doc['LastName']);
        $studentId = $doc['StudentInfoId'];
        
        // Check each document field
        foreach ($documentFields as $pathField => $info) {
            if (!empty($doc[$pathField])) {
                $filePath = $doc[$pathField];
                $statusField = $info['status'];
                $status = strtolower($doc[$statusField] ?? 'pending');
                
                // Get file size
                $fullPath = '../../uploads/documents/' . $filePath;
                $fileSize = file_exists($fullPath) ? formatFileSize(filesize($fullPath)) : 'Unknown';
                
                // Create document entry
                $docEntry = [
                    'id' => $doc['Id'],
                    'studentId' => $studentId,
                    'studentName' => $studentName,
                    'documentType' => $info['type'],
                    'documentField' => $pathField,
                    'statusField' => $statusField,
                    'documentPath' => $filePath,
                    'status' => $status,
                    'submissionDate' => date('M d, Y', strtotime($doc['UploadedAt'])),
                    'fileSize' => $fileSize,
                    'remarks' => $doc['Remarks'],
                    'course' => '-',  // You can add course info if available
                    'school' => '-',  // You can add school info if available
                    'batch' => '-'    // You can add batch info if available
                ];
                
                $documents[] = $docEntry;
                
                // Update statistics
                $statistics['total']++;
                if ($status === 'pending') {
                    $statistics['pending']++;
                } elseif ($status === 'approved') {
                    $statistics['approved']++;
                } elseif ($status === 'rejected') {
                    $statistics['rejected']++;
                }
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'documents' => $documents,
        'statistics' => $statistics
    ]);
    
} catch (PDOException $e) {
    error_log('Get Documents Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'documents' => [],
        'statistics' => ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0]
    ]);
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>