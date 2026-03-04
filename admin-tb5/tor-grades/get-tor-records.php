<?php

session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

try {
    // Build WHERE clause
    $whereClause = "WHERE 1=1";
    $params = [];
    
    if (!empty($searchValue)) {
        $whereClause .= " AND (
            s.FirstName LIKE ? OR 
            s.LastName LIKE ? OR 
            s.ULI LIKE ? OR
            c.CourseName LIKE ? OR
            t.ISONumber LIKE ?
        )";
        $searchParam = "%{$searchValue}%";
        $params = array_fill(0, 5, $searchParam);
    }
    
    // Count total records
    $countQuery = "SELECT COUNT(*) as total FROM tor_records {$whereClause}";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch()['total'] ?? 0;
    
    // Get records with pagination
    $query = "
        SELECT 
            t.Id as id,
            s.ULI as student_id,
            CONCAT(s.FirstName, ' ', s.LastName) as student_name,
            COALESCE(c.CourseName, 'N/A') as course_name,
            DATE_FORMAT(t.GraduationDate, '%m/%d/%Y') as graduation_date,
            t.ISONumber as iso_number,
            t.Remarks as status,
            t.FileName as file_name
        FROM tor_records t
        LEFT JOIN studentinfos s ON t.StudentId = s.Id
        LEFT JOIN courses c ON t.CourseId = c.Id
        {$whereClause}
        ORDER BY t.Id DESC
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
    error_log('Get TOR Records Error: ' . $e->getMessage());
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
    ]);
}
?>