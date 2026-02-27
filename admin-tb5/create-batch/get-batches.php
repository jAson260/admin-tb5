<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-batches.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$schoolFilter = isset($_POST['school']) ? $_POST['school'] : '';
$statusFilter = isset($_POST['status']) ? $_POST['status'] : '';

try {
    // Build WHERE clause
    $whereClause = "WHERE 1=1";
    $params = [];
    
    if (!empty($searchValue)) {
        $whereClause .= " AND (b.BatchCode LIKE ? OR b.BatchName LIKE ? OR c.CourseName LIKE ?)";
        $searchParam = "%{$searchValue}%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
    }
    
    if (!empty($schoolFilter)) {
        $whereClause .= " AND b.School = ?";
        $params[] = strtoupper($schoolFilter);
    }
    
    if (!empty($statusFilter)) {
        $whereClause .= " AND b.Status = ?";
        $params[] = ucfirst($statusFilter);
    }
    
    // Count total records
    $countQuery = "SELECT COUNT(*) as total FROM batches b {$whereClause}";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch()['total'] ?? 0;
    
    // Get records with pagination
    $query = "
        SELECT 
            b.Id,
            b.BatchCode,
            b.BatchName,
            b.School,
            c.CourseName,
            c.CourseCode,
            b.CurrentStudents,
            b.MaxStudents,
            DATE_FORMAT(b.StartDate, '%b %d, %Y') as StartDate,
            DATE_FORMAT(b.EndDate, '%b %d, %Y') as EndDate,
            b.Status,
            b.Description
        FROM batches b
        LEFT JOIN courses c ON b.CourseId = c.Id
        {$whereClause}
        ORDER BY b.Id DESC
        LIMIT {$start}, {$length}
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => (int)$totalRecords,
        'recordsFiltered' => (int)$totalRecords,
        'data' => $records
    ]);
    
} catch (PDOException $e) {
    error_log('Get Batches Error: ' . $e->getMessage());
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>