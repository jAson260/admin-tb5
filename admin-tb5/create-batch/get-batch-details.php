<?php


session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$id = intval($_GET['id'] ?? $_GET['batch_id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid batch ID.']);
    exit;
}

try {
    // ── Fetch batch info ──────────────────────────────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            b.Id,
            b.BatchCode,
            b.BatchName,
            b.School,
            b.CourseId,
            b.StartDate,
            b.EndDate,
            b.Status,
            b.Description,
            b.MaxStudents,
            c.CourseName,
            c.CourseCode,
            c.Duration,
            c.DurationHours,
            (
                SELECT COUNT(*) FROM enrollments e
                WHERE e.BatchId = b.Id AND e.Status IN ('Enrolled','Ongoing')
            ) AS CurrentStudents
        FROM batches b
        LEFT JOIN courses c ON c.Id = b.CourseId
        WHERE b.Id = ?
    ");
    $stmt->execute([$id]);
    $batch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$batch) {
        echo json_encode(['success' => false, 'message' => 'Batch not found.']);
        exit;
    }

    // ── Fetch students actively enrolled in this batch ────────────────────────
    $stmtStudents = $pdo->prepare("
        SELECT
            s.Id                                        AS StudentId,
            CONCAT(s.FirstName, ' ', s.LastName)        AS FullName,
            s.Email,
            s.ContactNo                                 AS ContactNumber,
            e.Status                                    AS EnrollmentStatus,
            DATE_FORMAT(e.EnrolledAt, '%b %d, %Y')      AS EnrolledDate
        FROM enrollments e
        JOIN studentinfos s ON s.Id = e.StudentId
        WHERE e.BatchId = ? AND e.Status IN ('Enrolled','Ongoing')
        ORDER BY s.LastName ASC, s.FirstName ASC
    ");
    $stmtStudents->execute([$id]);
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'  => true,
        'batch'    => $batch,
        'students' => $students
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>