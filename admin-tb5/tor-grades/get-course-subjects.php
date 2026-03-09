<?php

session_start();
require_once('../../db-connect.php');
header('Content-Type: application/json');

$courseId = intval($_GET['course_id'] ?? 0);
if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'No course ID provided']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            SubjectCode,
            SubjectName,
            Competency,
            SubjectType,
            Hours,
            COALESCE(SubjectOrder, 999) AS SubjectOrder
        FROM subjects
        WHERE CourseId = ?
          AND IsActive = 1
        ORDER BY
            FIELD(Competency, 'Basic', 'Common', 'Core'),
            SubjectOrder,
            SubjectName
    ");
    $stmt->execute([$courseId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'subjects' => $subjects]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>