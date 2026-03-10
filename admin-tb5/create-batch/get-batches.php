<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-batches.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $draw = intval($_POST['draw'] ?? 1);
    $start = intval($_POST['start'] ?? 0);
    $length = intval($_POST['length'] ?? 10);
    $searchValue = $_POST['search']['value'] ?? '';
    $orderColumnIndex = intval($_POST['order'][0]['column'] ?? 0);
    $orderDirection = $_POST['order'][0]['dir'] ?? 'desc';
    
    // Custom filters
    $schoolFilter = $_POST['schoolFilter'] ?? '';
    $statusFilter = $_POST['statusFilter'] ?? '';
    
    $columns = ['b.BatchCode', 'b.BatchName', 'b.School', 'courses.CourseName', 'b.CurrentStudents', 'b.StartDate', 'b.EndDate', 'b.Status'];
    $orderColumn = $columns[$orderColumnIndex] ?? 'b.Id';
    
    // Base query with proper JOIN
    $baseQuery = "
        FROM batches b
        LEFT JOIN courses ON b.CourseId = courses.Id
    ";
    
    // WHERE conditions
    $whereConditions = ['1=1'];
    $params = [];
    
    // Search filter
    if (!empty($searchValue)) {
        $whereConditions[] = "(
            b.BatchCode LIKE ? OR 
            b.BatchName LIKE ? OR 
            b.School LIKE ? OR
            courses.CourseName LIKE ? OR 
            courses.CourseCode LIKE ?
        )";
        $searchParam = "%$searchValue%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // School filter
    if (!empty($schoolFilter)) {
        $whereConditions[] = "b.School = ?";
        $params[] = strtoupper($schoolFilter);
    }
    
    // Status filter
    if (!empty($statusFilter)) {
        $whereConditions[] = "b.Status = ?";
        $params[] = ucfirst($statusFilter);
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Count total records (without filters)
    $totalQuery = "SELECT COUNT(*) as total FROM batches b";
    $totalStmt = $pdo->query($totalQuery);
    $totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Count filtered records
    $filteredQuery = "SELECT COUNT(*) as total $baseQuery WHERE $whereClause";
    $filteredStmt = $pdo->prepare($filteredQuery);
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Fetch data
    $dataQuery = "
        SELECT 
            b.Id,
            b.BatchCode,
            b.BatchName,
            b.School,
            b.CourseId,
            b.StartDate,
            b.EndDate,
            b.Status,
            b.MaxStudents,
            b.Description,
            courses.CourseCode,
            courses.CourseName,
            (
                SELECT COUNT(*) FROM enrollments e
                WHERE e.BatchId = b.Id AND e.Status IN ('Enrolled','Ongoing')
            ) AS CurrentStudents
        $baseQuery
        WHERE $whereClause
        ORDER BY $orderColumn $orderDirection
        LIMIT $start, $length
    ";
    
    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'draw' => 1,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>