<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\documents-approval\get-documents.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    $sql = "SELECT 
                d.Id,
                d.PSAPath,
                d.TORPath,
                d.DiplomaPath,
                d.MarriageCertificatePath,
                d.PSAStatus,
                d.Form137Status,
                d.MarriageCertificateStatus,
                d.UploadedAt,
                d.Remarks,
                s.Id as StudentId,
                s.FirstName,
                s.MiddleName,
                s.LastName,
                s.ULI,
                s.Email
            FROM documents d
            INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
            ORDER BY d.UploadedAt DESC";
    
    $stmt = $pdo->query($sql);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $processedDocuments = [];
    $uploadDir = '../../uploads/documents/';
    
    foreach ($documents as $doc) {
        $fullName = trim($doc['FirstName'] . ' ' . ($doc['MiddleName'] ? $doc['MiddleName'] . ' ' : '') . $doc['LastName']);
        
        $formatFileSize = function($bytes) {
            if ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 1) . ' MB';
            } elseif ($bytes >= 1024) {
                return number_format($bytes / 1024, 1) . ' KB';
            } else {
                return $bytes . ' B';
            }
        };
        
        $psaSize = $doc['PSAPath'] && file_exists($uploadDir . $doc['PSAPath']) ? filesize($uploadDir . $doc['PSAPath']) : 0;
        $torSize = $doc['TORPath'] && file_exists($uploadDir . $doc['TORPath']) ? filesize($uploadDir . $doc['TORPath']) : 0;
        $diplomaSize = $doc['DiplomaPath'] && file_exists($uploadDir . $doc['DiplomaPath']) ? filesize($uploadDir . $doc['DiplomaPath']) : 0;
        $marriageSize = $doc['MarriageCertificatePath'] && file_exists($uploadDir . $doc['MarriageCertificatePath']) 
            ? filesize($uploadDir . $doc['MarriageCertificatePath']) : 0;
        
        if ($doc['PSAPath']) {
            $processedDocuments[] = [
                'id' => $doc['Id'],
                'studentId' => $doc['StudentId'],
                'studentName' => $fullName,
                'uli' => $doc['ULI'],
                'email' => $doc['Email'],
                'documentType' => 'PSA Birth Certificate',
                'documentPath' => $doc['PSAPath'],
                'submissionDate' => date('M d, Y', strtotime($doc['UploadedAt'])),
                'fileSize' => $formatFileSize($psaSize),
                'status' => $doc['PSAStatus'] ?: 'pending',
                'course' => '-',
                'batch' => '-',
                'school' => '-',
                'remarks' => $doc['Remarks']
            ];
        }
        
        if ($doc['TORPath']) {
            $processedDocuments[] = [
                'id' => $doc['Id'],
                'studentId' => $doc['StudentId'],
                'studentName' => $fullName,
                'uli' => $doc['ULI'],
                'email' => $doc['Email'],
                'documentType' => 'Transcript of Records',
                'documentPath' => $doc['TORPath'],
                'submissionDate' => date('M d, Y', strtotime($doc['UploadedAt'])),
                'fileSize' => $formatFileSize($torSize),
                'status' => $doc['Form137Status'] ?: 'pending',
                'course' => '-',
                'batch' => '-',
                'school' => '-',
                'remarks' => $doc['Remarks']
            ];
        }
        
        if ($doc['DiplomaPath']) {
            $processedDocuments[] = [
                'id' => $doc['Id'],
                'studentId' => $doc['StudentId'],
                'studentName' => $fullName,
                'uli' => $doc['ULI'],
                'email' => $doc['Email'],
                'documentType' => 'Diploma',
                'documentPath' => $doc['DiplomaPath'],
                'submissionDate' => date('M d, Y', strtotime($doc['UploadedAt'])),
                'fileSize' => $formatFileSize($diplomaSize),
                'status' => $doc['PSAStatus'] ?: 'pending',
                'course' => '-',
                'batch' => '-',
                'school' => '-',
                'remarks' => $doc['Remarks']
            ];
        }
        
        if ($doc['MarriageCertificatePath']) {
            $processedDocuments[] = [
                'id' => $doc['Id'],
                'studentId' => $doc['StudentId'],
                'studentName' => $fullName,
                'uli' => $doc['ULI'],
                'email' => $doc['Email'],
                'documentType' => 'Marriage Certificate',
                'documentPath' => $doc['MarriageCertificatePath'],
                'submissionDate' => date('M d, Y', strtotime($doc['UploadedAt'])),
                'fileSize' => $formatFileSize($marriageSize),
                'status' => $doc['MarriageCertificateStatus'] ?: 'pending',
                'course' => '-',
                'batch' => '-',
                'school' => '-',
                'remarks' => $doc['Remarks']
            ];
        }
    }
    
    $stats = [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'total' => count($processedDocuments)
    ];
    
    foreach ($processedDocuments as $doc) {
        if (isset($stats[$doc['status']])) {
            $stats[$doc['status']]++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'documents' => $processedDocuments,
        'statistics' => $stats
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>