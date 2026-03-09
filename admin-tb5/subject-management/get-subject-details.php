<?php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid subject ID.']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT
            s.Id,
            s.School,
            s.SubjectCode,
            s.SubjectName,
            s.Description,
            s.LearningOutcomes,
            s.SubjectType,
            s.Competency,
            s.Hours,
            s.Days,
            s.PassingGrade,
            s.SubjectOrder,
            s.IsActive,
            s.CreatedAt,
            s.UpdatedAt,
            c.Id       AS CourseId,
            c.CourseCode,
            c.CourseName
        FROM subjects s
        INNER JOIN courses c ON s.CourseId = c.Id
        WHERE s.Id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $subject = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subject) {
        echo json_encode(['success' => false, 'message' => 'Subject not found.']);
        exit;
    }

    echo json_encode(['success' => true, 'subject' => $subject]);

} catch (PDOException $e) {
    error_log("get-subject-details.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}