<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\course-creation\save-course.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');
checkAdmin();

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Validate required fields
    if (empty($data['courseCode']) || empty($data['courseName']) || empty($data['school']) || empty($data['category']) || empty($data['duration'])) {
        throw new Exception('Required fields are missing');
    }
    
    $adminId = $_SESSION['user_id'] ?? null;
    
    if (!empty($data['id'])) {
        // Update existing course
        $stmt = $pdo->prepare("
            UPDATE courses SET
                CourseCode = ?,
                CourseName = ?,
                Description = ?,
                School = ?,
                Duration = ?,
                DurationHours = ?,
                Category = ?,
                MaxStudents = ?,
                Prerequisites = ?,
                Tuition = ?,
                IsActive = ?
            WHERE Id = ?
        ");
        
        $stmt->execute([
            $data['courseCode'],
            $data['courseName'],
            $data['description'],
            $data['school'],
            $data['duration'],
            $data['durationHours'],
            $data['category'],
            $data['maxStudents'],
            $data['prerequisites'],
            $data['tuition'],
            $data['isActive'],
            $data['id']
        ]);
        
        $message = 'Course updated successfully';
        
    } else {
        // Create new course
        $stmt = $pdo->prepare("
            INSERT INTO courses 
            (CourseCode, CourseName, Description, School, Duration, DurationHours, 
             Category, MaxStudents, Prerequisites, Tuition, IsActive, CreatedBy)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['courseCode'],
            $data['courseName'],
            $data['description'],
            $data['school'],
            $data['duration'],
            $data['durationHours'],
            $data['category'],
            $data['maxStudents'],
            $data['prerequisites'],
            $data['tuition'],
            $data['isActive'],
            $adminId
        ]);
        
        $message = 'Course created successfully';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
    
} catch (PDOException $e) {
    error_log("Save course error: " . $e->getMessage());
    
    // Check for duplicate course code
    if ($e->getCode() == 23000) {
        $message = 'Course code already exists';
    } else {
        $message = 'Database error: ' . $e->getMessage();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
} catch (Exception $e) {
    error_log("Save course error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>