<?php

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    die('Invalid TOR ID.');
}

try {
    // ── Fetch TOR record ──────────────────────────────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            t.*,
            CONCAT(s.FirstName, ' ', s.LastName) AS StudentName,
            s.ULI,
            s.Email,
            c.CourseName,
            c.CourseCode,
            c.School
        FROM tor_records t
        INNER JOIN studentinfos s ON s.Id = t.StudentId
        INNER JOIN courses      c ON c.Id = t.CourseId
        WHERE t.Id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $tor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tor) {
        http_response_code(404);
        die('TOR record not found.');
    }

    // ── Fetch subject grades for this TOR ─────────────────────────────────────
    $gradeStmt = $pdo->prepare("
        SELECT
            tg.SubjectCode,
            tg.SubjectName,
            tg.Hours,
            tg.TheoreticalGrade,
            tg.PracticalGrade,
            tg.FinalGrade,
            tg.Remarks
        FROM tor_grades tg
        WHERE tg.TORId = ?
        ORDER BY tg.Id ASC
    ");
    $gradeStmt->execute([$id]);
    $grades = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);

    // ── Build filename ────────────────────────────────────────────────────────
    $safeName   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $tor['StudentName']);
    $safeCode   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $tor['CourseCode']);
    $filename   = "TOR_{$safeName}_{$safeCode}_" . date('Ymd') . ".csv";

    // ── Output CSV headers ────────────────────────────────────────────────────
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');

    // ── Header section ────────────────────────────────────────────────────────
    fputcsv($out, ['THE BIG FIVE TRAINING AND ASSESSMENT CENTER INC.']);
    fputcsv($out, ['TRANSCRIPT OF RECORDS']);
    fputcsv($out, []);

    // ── Student info ──────────────────────────────────────────────────────────
    fputcsv($out, ['Student Name',    $tor['StudentName']]);
    fputcsv($out, ['ULI',             $tor['ULI']]);
    fputcsv($out, ['Email',           $tor['Email']]);
    fputcsv($out, ['Course',          $tor['CourseName'] . ' (' . $tor['CourseCode'] . ')']);
    fputcsv($out, ['School',          $tor['School']]);
    fputcsv($out, ['Date Graduated',  $tor['GraduationDate']]);
    fputcsv($out, ['SO Number',       $tor['ISONumber']]);
    fputcsv($out, ['Date Encoded',    $tor['DateEncoded']]);
    fputcsv($out, []);

    // ── Grades table header ───────────────────────────────────────────────────
    fputcsv($out, [
        'Subject Code',
        'Subject / Competency',
        'Hours',
        'Theoretical (30%)',
        'Practical (70%)',
        'Final Grade',
        'Remarks'
    ]);

    // ── Grades rows ───────────────────────────────────────────────────────────
    if (!empty($grades)) {
        foreach ($grades as $g) {
            fputcsv($out, [
                $g['SubjectCode'],
                $g['SubjectName'],
                $g['Hours'] . ' hrs',
                $g['TheoreticalGrade'],
                $g['PracticalGrade'],
                $g['FinalGrade'],
                $g['Remarks']
            ]);
        }
    } else {
        fputcsv($out, ['—', 'No subject grades found', '', '', '', '', '']);
    }

    fputcsv($out, []);

    // ── Overall summary ───────────────────────────────────────────────────────
    fputcsv($out, ['', '', 'OVERALL TOTALS']);
    fputcsv($out, ['', 'Overall Theoretical',  '', $tor['TheoreticalGrade'] . '%']);
    fputcsv($out, ['', 'Overall Practical',    '', '', $tor['PracticalGrade'] . '%']);
    fputcsv($out, ['', 'Average Grade',        '', '', '', $tor['AverageGrade']]);
    fputcsv($out, ['', 'Final Grade',          '', '', '', $tor['FinalGrade']]);
    fputcsv($out, ['', 'Overall Remarks',      '', '', '', '', $tor['Remarks']]);

    fclose($out);
    exit;

} catch (PDOException $e) {
    error_log('download-tor-csv.php: ' . $e->getMessage());
    http_response_code(500);
    die('Database error. Please try again.');
}
?>