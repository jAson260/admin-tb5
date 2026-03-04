<?php

session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    // Total TORs
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM tor_records");
    $total = $totalStmt->fetch()['total'] ?? 0;
    
    // Competent students
    $competentStmt = $pdo->query("
        SELECT COUNT(DISTINCT t.EnrollmentId) as competent 
        FROM tor_records t
        WHERE t.Remarks = 'Competent'
    ");
    $competent = $competentStmt->fetch()['competent'] ?? 0;
    
    // This month TORs
    $thisMonthStmt = $pdo->query("
        SELECT COUNT(*) as this_month 
        FROM tor_records 
        WHERE MONTH(DateEncoded) = MONTH(CURRENT_DATE())
        AND YEAR(DateEncoded) = YEAR(CURRENT_DATE())
    ");
    $thisMonth = $thisMonthStmt->fetch()['this_month'] ?? 0;
    
    // Total downloads (you may need to add a download tracking field)
    $downloads = 0;
    
    echo json_encode([
        'total' => (int)$total,
        'competent' => (int)$competent,
        'this_month' => (int)$thisMonth,
        'downloads' => (int)$downloads
    ]);
    
} catch (PDOException $e) {
    error_log('Get TOR Statistics Error: ' . $e->getMessage());
    echo json_encode([
        'total' => 0,
        'competent' => 0,
        'this_month' => 0,
        'downloads' => 0
    ]);
}
?>