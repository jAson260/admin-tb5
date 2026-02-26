<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\course-creation\get-courses.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');
checkAdmin();

try {
    // Get filter parameters
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $school = isset($_POST['school']) ? $_POST['school'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Search filter
    if (!empty($search)) {
        $whereConditions[] = "(CourseCode LIKE ? OR CourseName LIKE ? OR Description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // School filter
    if (!empty($school)) {
        $whereConditions[] = "School = ?";
        $params[] = $school;
    }
    
    // Status filter
    if ($status !== '') {
        $whereConditions[] = "IsActive = ?";
        $params[] = intval($status);
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Get courses
    $query = "
        SELECT 
            Id, CourseCode, CourseName, Description, School, 
            Duration, DurationHours, Category, MaxStudents, 
            Prerequisites, Tuition, IsActive, CreatedAt, UpdatedAt
        FROM courses
        $whereClause
        ORDER BY School ASC, CourseName ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $statsQuery = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN IsActive = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN School = 'TB5' THEN 1 ELSE 0 END) as tb5,
            SUM(CASE WHEN School = 'BBI' THEN 1 ELSE 0 END) as bbi
        FROM courses
    ");
    $stats = $statsQuery->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'courses' => $courses,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    error_log("Get courses error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>