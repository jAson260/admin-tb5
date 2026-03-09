<?php

session_start();
require_once('../../db-connect.php');
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT
            b.Id,
            b.BatchCode,
            b.BatchName,
            b.School,
            b.Status,
            b.CourseId,
            COUNT(e.Id) AS StudentCount
        FROM batches b
        LEFT JOIN enrollments e ON e.BatchId = b.Id
        GROUP BY b.Id
        ORDER BY b.BatchCode
    ");
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'batches' => $batches]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>