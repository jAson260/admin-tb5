<?php

session_start();
require_once('../../db-connect.php');
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT Id,
               CONCAT(LastName, ', ', FirstName, ' ', COALESCE(MiddleName,'')) AS FullName,
               Email, ULI, Status, ProfilePicture
        FROM studentinfos
        WHERE Status = 'Approved'
        ORDER BY LastName, FirstName
    ");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'students' => $students]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>