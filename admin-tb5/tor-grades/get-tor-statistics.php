<?php

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

header('Content-Type: application/json');

try {
    // ── Total TORs ────────────────────────────────────────────────────────────
    $total = $pdo->query("
        SELECT COUNT(*) FROM tor_records
    ")->fetchColumn();

    // ── Competent ─────────────────────────────────────────────────────────────
    $competent = $pdo->query("
        SELECT COUNT(*) FROM tor_records
        WHERE Remarks = 'Competent'
    ")->fetchColumn();

    // ── Not Yet Competent ─────────────────────────────────────────────────────
    $notYetCompetent = $pdo->query("
        SELECT COUNT(*) FROM tor_records
        WHERE Remarks = 'Not Yet Competent'
    ")->fetchColumn();

    // ── This Month — uses DateEncoded (not CreatedAt) ─────────────────────────
    $thisMonth = $pdo->query("
        SELECT COUNT(*) FROM tor_records
        WHERE MONTH(DateEncoded) = MONTH(NOW())
        AND   YEAR(DateEncoded)  = YEAR(NOW())
    ")->fetchColumn();

    // ── This Week — uses DateEncoded ──────────────────────────────────────────
    $thisWeek = $pdo->query("
        SELECT COUNT(*) FROM tor_records
        WHERE DateEncoded >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ")->fetchColumn();

    // ── Unique students with TOR ───────────────────────────────────────────────
    $uniqueStudents = $pdo->query("
        SELECT COUNT(DISTINCT StudentId) FROM tor_records
    ")->fetchColumn();

    echo json_encode([
        'success'           => true,
        'total'             => (int) $total,
        'competent'         => (int) $competent,
        'not_yet_competent' => (int) $notYetCompetent,
        'this_month'        => (int) $thisMonth,
        'this_week'         => (int) $thisWeek,
        'downloads'         => (int) $uniqueStudents, // no DownloadCount col — use unique students
    ]);

} catch (PDOException $e) {
    error_log('get-tor-statistics.php error: ' . $e->getMessage());
    echo json_encode([
        'success'           => false,
        'message'           => $e->getMessage(), // show real error during dev
        'total'             => 0,
        'competent'         => 0,
        'not_yet_competent' => 0,
        'this_month'        => 0,
        'this_week'         => 0,
        'downloads'         => 0,
    ]);
}
?>