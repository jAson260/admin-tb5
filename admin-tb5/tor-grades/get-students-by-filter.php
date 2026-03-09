<?php
session_start();
require_once('../../db-connect.php');
header('Content-Type: application/json');

$mode     = $_GET['mode']      ?? 'batch';
$batchId  = intval($_GET['batch_id']  ?? 0);
$courseId = intval($_GET['course_id'] ?? 0);
$search   = trim($_GET['search']      ?? '');

try {
    $params = [];

    $sql = "
        SELECT
            s.Id,
            CONCAT(s.LastName, ', ', s.FirstName,
                   IF(s.MiddleName IS NOT NULL AND s.MiddleName != '',
                      CONCAT(' ', LEFT(s.MiddleName, 1), '.'), '')) AS FullName,
            s.Email,
            s.ULI,
            s.Status,
            s.ProfilePicture,
            e.BatchId,
            b.BatchCode,
            b.BatchName,
            e.CourseId,
            c.CourseName,
            c.CourseCode
        FROM studentinfos s
        JOIN enrollments e ON e.StudentId = s.Id
        JOIN batches b     ON b.Id = e.BatchId
        JOIN courses c     ON c.Id = e.CourseId
        WHERE s.Status = 'Approved'
    ";

    if ($mode === 'batch' && $batchId) {
        // ── By Batch ─────────────────────────────────────────────────────────
        $sql     .= " AND e.BatchId = ?";
        $params[] = $batchId;

    } elseif ($mode === 'course' && $courseId) {
        // ── By Course ────────────────────────────────────────────────────────
        $sql     .= " AND e.CourseId = ?";
        $params[] = $courseId;

    } elseif ($mode === 'student') {
        // ── By Student — search if provided, else return ALL approved ────────
        if ($search !== '') {
            $like     = '%' . $search . '%';
            $sql     .= " AND (
                s.FirstName LIKE ? OR
                s.LastName  LIKE ? OR
                CONCAT(s.LastName, ', ', s.FirstName) LIKE ? OR
                s.ULI   LIKE ? OR
                s.Email LIKE ?
            )";
            $params = array_merge($params, [$like, $like, $like, $like, $like]);
        }
        // no search = no extra WHERE → returns all approved students
    }

    $sql .= " ORDER BY s.LastName, s.FirstName";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'students' => $students]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'students' => []]);
}
?>