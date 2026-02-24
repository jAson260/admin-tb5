<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\get-statistics.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

checkAdmin();

header('Content-Type: application/json');

try {
    // Count all admins
    $stmtAllAdmins = $pdo->query("SELECT COUNT(*) as count FROM admins");
    $statsAdmins = $stmtAllAdmins->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count all students
    $stmtAllStudents = $pdo->query("SELECT COUNT(*) as count FROM studentinfos");
    $statsStudents = $stmtAllStudents->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total users
    $statsTotal = $statsAdmins + $statsStudents;
    
    // Count active accounts (Active admins + Approved students)
    $stmtActive = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM admins WHERE Status = 'Active') + 
            (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Approved') as count
    ");
    $statsActive = $stmtActive->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'statistics' => [
            'total' => (int)$statsTotal,
            'admins' => (int)$statsAdmins,
            'students' => (int)$statsStudents,
            'active' => (int)$statsActive
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>