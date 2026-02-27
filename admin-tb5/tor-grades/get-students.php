<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\tor-grades\get-students.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    // Get all students with Status = 'Approved'
    $stmt = $pdo->query("
        SELECT 
            Id as id, 
            CONCAT(
                FirstName, 
                ' ', 
                COALESCE(CONCAT(SUBSTRING(MiddleName, 1, 1), '. '), ''), 
                LastName
            ) as name, 
            ULI as uli,
            Email as email,
            ContactNo as contact
        FROM studentinfos 
        WHERE Status = 'Approved'
        ORDER BY LastName, FirstName
    ");
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log for debugging
    error_log('Students found: ' . count($students));
    
    echo json_encode($students);
    
} catch (PDOException $e) {
    error_log('Get Students Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}