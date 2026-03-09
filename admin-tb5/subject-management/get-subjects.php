<?php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $search     = trim($_POST['search']     ?? '');
    $school     = trim($_POST['school']     ?? '');
    $courseId   = (int)($_POST['courseId']  ?? 0);
    $status     = $_POST['status']          ?? '';
    $competency = trim($_POST['competency'] ?? '');

    $where  = ['1=1'];
    $params = [];

    if (!empty($search)) {
        $where[]  = '(s.SubjectCode LIKE ? OR s.SubjectName LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($school)) {
        $where[]  = 's.School = ?';
        $params[] = $school;
    }

    if ($courseId > 0) {
        $where[]  = 's.CourseId = ?';
        $params[] = $courseId;
    }

    if ($status !== '') {
        $where[]  = 's.IsActive = ?';
        $params[] = (int)$status;
    }

    if (!empty($competency)) {
        $where[]  = 's.Competency = ?';
        $params[] = $competency;
    }

    $whereStr = implode(' AND ', $where);

    $stmt = $pdo->prepare("
        SELECT
            s.Id, s.SubjectCode, s.SubjectName, s.School,
            s.Competency, s.Hours, s.Days, s.IsActive,
            c.CourseName, c.CourseCode
        FROM subjects s
        LEFT JOIN courses c ON c.Id = s.CourseId
        WHERE $whereStr
        ORDER BY
            FIELD(s.Competency, 'Basic', 'Common', 'Core'),
            s.SubjectCode ASC
    ");
    $stmt->execute($params);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Stats (unfiltered)
    $stats = $pdo->query("
        SELECT
            COUNT(*)                                          AS total,
            SUM(CASE WHEN IsActive = 1 THEN 1 ELSE 0 END)   AS active,
            SUM(CASE WHEN School = 'TB5' THEN 1 ELSE 0 END) AS tb5,
            SUM(CASE WHEN School = 'BBI' THEN 1 ELSE 0 END) AS bbi
        FROM subjects
    ")->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'  => true,
        'subjects' => $subjects,
        'stats'    => $stats
    ]);

} catch (PDOException $e) {
    error_log("get-subjects.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'subjects' => []]);
}