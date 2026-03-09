<?php

// ONLY ONE opening tag - remove any duplicate <?php at the top

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');

checkAdmin();

try {
    $draw             = isset($_POST['draw'])               ? intval($_POST['draw'])               : 1;
    $start            = isset($_POST['start'])              ? intval($_POST['start'])              : 0;
    $length           = isset($_POST['length'])             ? intval($_POST['length'])             : 10;
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDir         = in_array(strtoupper($_POST['order'][0]['dir'] ?? ''), ['ASC','DESC'])
                            ? strtoupper($_POST['order'][0]['dir'])
                            : 'DESC';

    $roleFilter   = isset($_POST['roleFilter'])   ? trim($_POST['roleFilter'])   : '';
    $statusFilter = isset($_POST['statusFilter']) ? trim($_POST['statusFilter']) : '';

    // ── FIX: read BOTH sources and prefer the custom searchKeyword ────────────
    $searchValue = '';
    if (!empty($_POST['searchKeyword'])) {
        $searchValue = trim($_POST['searchKeyword']);
    } elseif (!empty($_POST['search']['value'])) {
        $searchValue = trim($_POST['search']['value']);
    }

    $columns     = ['Id', 'FullName', 'Email', 'AccountType', 'Status', 'LastLogin'];
    $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'Id';

    // ── STUDENT WHERE ─────────────────────────────────────────────────────────
    $whereConditions = [];
    $params          = [];

    if ($searchValue !== '') {
        $whereConditions[] = "(CONCAT(s.FirstName, ' ', s.LastName) LIKE ? OR s.Email LIKE ?)";
        $params[]          = "%$searchValue%";
        $params[]          = "%$searchValue%";
    }
    if ($statusFilter !== '' && $roleFilter !== 'admin') {
        $whereConditions[] = "s.Status = ?";
        $params[]          = ucfirst($statusFilter);
    }
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

    // ── ADMIN WHERE ───────────────────────────────────────────────────────────
    $adminWhereConditions = [];
    $adminParams          = [];

    if ($searchValue !== '') {
        $adminWhereConditions[] = "(CONCAT(FirstName, ' ', LastName) LIKE ? OR Email LIKE ?)";
        $adminParams[]          = "%$searchValue%";
        $adminParams[]          = "%$searchValue%";
    }
    if ($statusFilter !== '' && $roleFilter === 'admin') {
        $adminWhereConditions[] = "Status = ?";
        $adminParams[]          = ucfirst($statusFilter);
    }
    $adminWhereClause = !empty($adminWhereConditions)
        ? "WHERE " . implode(" AND ", $adminWhereConditions)
        : "";

    // ── QUERIES ───────────────────────────────────────────────────────────────
    if ($roleFilter === 'admin') {

        $dataQuery = "
            SELECT
                Id,
                CONCAT(FirstName, ' ', LastName) AS FullName,
                Email,
                'admin' AS AccountType,
                Role,
                Status,
                LastLogin,
                CreatedAt,
                NULL AS School,
                NULL AS CourseCode,
                NULL AS CourseName,
                NULL AS CourseId,
                NULL AS BatchCode,
                NULL AS BatchName,
                NULL AS BatchId
            FROM admins
            $adminWhereClause
            ORDER BY $orderColumn $orderDir
            LIMIT $start, $length
        ";
        $countQuery = "SELECT COUNT(*) AS total FROM admins $adminWhereClause";
        $totalQuery = "SELECT COUNT(*) AS total FROM admins";
        $execParams = $adminParams;

    } elseif ($roleFilter === 'student') {

        $dataQuery = "
            SELECT
                s.Id,
                CONCAT(s.FirstName, ' ', s.LastName) AS FullName,
                s.Email,
                'student' AS AccountType,
                NULL      AS Role,
                s.Status,
                s.LastLogin,
                s.EntryDate AS CreatedAt,
                e.School,
                c.CourseCode,
                c.CourseName,
                c.Id   AS CourseId,
                b.BatchCode,
                b.BatchName,
                b.Id   AS BatchId
            FROM studentinfos s
            LEFT JOIN enrollments e
                ON e.StudentId = s.Id
                AND e.Status NOT IN ('Dropped','Failed','Completed')
            LEFT JOIN courses c ON c.Id = e.CourseId
            LEFT JOIN batches b ON b.Id = e.BatchId
            $whereClause
            ORDER BY $orderColumn $orderDir
            LIMIT $start, $length
        ";
        $countQuery = "
            SELECT COUNT(*) AS total
            FROM studentinfos s
            LEFT JOIN enrollments e
                ON e.StudentId = s.Id
                AND e.Status NOT IN ('Dropped','Failed','Completed')
            LEFT JOIN courses c ON c.Id = e.CourseId
            LEFT JOIN batches b ON b.Id = e.BatchId
            $whereClause
        ";
        $totalQuery = "SELECT COUNT(*) AS total FROM studentinfos";
        $execParams = $params;

    } else {

        $adminUnionWhere  = $searchValue !== ''
            ? "WHERE (CONCAT(FirstName, ' ', LastName) LIKE ? OR Email LIKE ?)"
            : "";
        $adminUnionParams = $searchValue !== ''
            ? ["%$searchValue%", "%$searchValue%"]
            : [];

        // Status filter per side
        $adminStatusWhere  = '';
        $adminStatusParams = [];
        $studStatusWhere   = '';
        $studStatusParams  = [];

        if ($statusFilter !== '') {
            $statusVal = ucfirst($statusFilter);

            $adminStatusWhere  = $adminUnionWhere !== '' ? " AND Status = ?" : "WHERE Status = ?";
            $adminStatusParams = [$statusVal];

            $studStatusWhere  = $whereClause !== '' ? " AND s.Status = ?" : "WHERE s.Status = ?";
            $studStatusParams = [$statusVal];
        }

        $finalAdminWhere  = $adminUnionWhere . $adminStatusWhere;
        $finalAdminParams = array_merge($adminUnionParams, $adminStatusParams);
        $finalStudWhere   = $whereClause . $studStatusWhere;
        $finalStudParams  = array_merge($params, $studStatusParams);
        $execParams       = array_merge($finalAdminParams, $finalStudParams);

        $dataQuery = "
            SELECT
                Id,
                CONCAT(FirstName, ' ', LastName) AS FullName,
                Email,
                'admin' AS AccountType,
                Role,
                Status,
                LastLogin,
                CreatedAt,
                NULL AS School,
                NULL AS CourseCode,
                NULL AS CourseName,
                NULL AS CourseId,
                NULL AS BatchCode,
                NULL AS BatchName,
                NULL AS BatchId
            FROM admins
            $finalAdminWhere

            UNION ALL

            SELECT
                s.Id,
                CONCAT(s.FirstName, ' ', s.LastName) AS FullName,
                s.Email,
                'student' AS AccountType,
                NULL      AS Role,
                s.Status,
                s.LastLogin,
                s.EntryDate AS CreatedAt,
                e.School,
                c.CourseCode,
                c.CourseName,
                c.Id   AS CourseId,
                b.BatchCode,
                b.BatchName,
                b.Id   AS BatchId
            FROM studentinfos s
            LEFT JOIN enrollments e
                ON e.StudentId = s.Id
                AND e.Status NOT IN ('Dropped','Failed','Completed')
            LEFT JOIN courses c ON c.Id = e.CourseId
            LEFT JOIN batches b ON b.Id = e.BatchId
            $finalStudWhere

            ORDER BY $orderColumn $orderDir
            LIMIT $start, $length
        ";

        $countQuery = "
            SELECT COUNT(*) AS total FROM (
                SELECT Id FROM admins $finalAdminWhere
                UNION ALL
                SELECT s.Id
                FROM studentinfos s
                LEFT JOIN enrollments e
                    ON e.StudentId = s.Id
                    AND e.Status NOT IN ('Dropped','Failed','Completed')
                LEFT JOIN courses c ON c.Id = e.CourseId
                LEFT JOIN batches b ON b.Id = e.BatchId
                $finalStudWhere
            ) AS combined
        ";

        $totalQuery = "
            SELECT (SELECT COUNT(*) FROM admins) +
                   (SELECT COUNT(*) FROM studentinfos) AS total
        ";
    }

    // ── EXECUTE ───────────────────────────────────────────────────────────────
    $stmt = $pdo->prepare($dataQuery);
    $stmt->execute($execParams);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtCount = $pdo->prepare($countQuery);
    $stmtCount->execute($execParams);
    $recordsFiltered = $stmtCount->fetch()['total'];

    $stmtTotal    = $pdo->query($totalQuery);
    $recordsTotal = $stmtTotal->fetch()['total'];

    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => intval($recordsTotal),
        'recordsFiltered' => intval($recordsFiltered),
        'data'            => $data
    ]);

} catch (PDOException $e) {
    error_log("get-accounts.php DB error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'draw'            => 1,
        'recordsTotal'    => 0,
        'recordsFiltered' => 0,
        'data'            => [],
        'error'           => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("get-accounts.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'draw'            => 1,
        'recordsTotal'    => 0,
        'recordsFiltered' => 0,
        'data'            => [],
        'error'           => $e->getMessage()
    ]);
}
?>