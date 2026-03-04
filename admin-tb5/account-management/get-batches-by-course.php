<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\get-batches-by-course.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$courseId = intval($_GET['courseId'] ?? 0);

if (!$courseId) {
    echo json_encode([
        'success' => false,
        'message' => 'Course ID is required',
        'batches' => []
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            b.Id,
            b.BatchCode,
            b.BatchName,
            b.School,
            b.StartDate,
            b.EndDate,
            b.MaxStudents,
            b.CurrentStudents,
            b.Status
        FROM batches b
        INNER JOIN courses c ON b.CourseId = c.Id
        WHERE b.CourseId = ?
        AND b.IsActive = 1
        ORDER BY b.StartDate DESC
    ");
    $stmt->execute([$courseId]);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($batches)) {
        echo json_encode([
            'success'  => false,
            'message'  => 'No active batches available for this course',
            'batches'  => []
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'batches' => $batches,
        'count'   => count($batches)
    ]);

} catch (PDOException $e) {
    error_log('Get Batches Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'batches' => []
    ]);
}
?>