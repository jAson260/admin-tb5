<?php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

/**
 * Admin Dashboard Controller
 * Handles all data fetching and processing for the admin dashboard
 */
class DashboardController {
    private $pdo;
    private $stats = [];
    private $recentData = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadDashboardData();
    }
    
    /**
     * Load all dashboard data
     */
    private function loadDashboardData() {
        try {
            $this->stats = [
                'students' => $this->getStudentStats(),
                'courses' => $this->getCourseStats(),
                'batches' => $this->getBatchStats(),
                'enrollments' => $this->getEnrollmentStats(),
                'documents' => $this->getDocumentStats(),
                'schools' => $this->getSchoolDistribution()
            ];
            
            $this->recentData = [
                'logs' => $this->fetchRecentLogs(),
                'documents' => $this->fetchDocumentManagement(),
                'enrollments' => $this->fetchRecentEnrollments()
            ];
            
            $this->logDebugInfo();
            
        } catch (PDOException $e) {
            error_log('Dashboard Error: ' . $e->getMessage());
            $this->setDefaultStats();
        }
    }
    
    /**
     * Get student statistics
     */
    private function getStudentStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN Status = 'Approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN Status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN Status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN Status = 'Suspended' THEN 1 ELSE 0 END) as suspended
                FROM studentinfos";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: $this->getEmptyStudentStats();
    }
    
    /**
     * Get course statistics
     */
    private function getCourseStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN IsActive = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN School = 'TB5' AND IsActive = 1 THEN 1 ELSE 0 END) as tb5_active,
                    SUM(CASE WHEN School = 'BBI' AND IsActive = 1 THEN 1 ELSE 0 END) as bbi_active
                FROM courses";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: $this->getEmptyCourseStats();
    }
    
    /**
     * Get batch statistics
     */
    private function getBatchStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN Status = 'Active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN Status = 'Completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN Status = 'Pending' THEN 1 ELSE 0 END) as pending
                FROM batches";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: $this->getEmptyBatchStats();
    }
    
    /**
     * Get enrollment statistics
     */
    private function getEnrollmentStats() {
        $sql = "SELECT 
                    COUNT(*) as total_enrollments,
                    SUM(CASE WHEN Status = 'Enrolled' THEN 1 ELSE 0 END) as enrolled,
                    SUM(CASE WHEN Status = 'Ongoing' THEN 1 ELSE 0 END) as ongoing,
                    SUM(CASE WHEN Status = 'Completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN Status IN ('Dropped', 'Failed') THEN 1 ELSE 0 END) as dropped
                FROM enrollments";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: $this->getEmptyEnrollmentStats();
    }
    
    /**
     * Get document statistics - UPDATED to exclude Form137, ALS, Barangay, Residency
     */
    private function getDocumentStats() {
        $sql = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN PSAStatus = 'approved' THEN 1 ELSE 0 END) as psa_approved,
                    SUM(CASE WHEN TORStatus = 'approved' THEN 1 ELSE 0 END) as tor_approved,
                    SUM(CASE WHEN DiplomaStatus = 'approved' THEN 1 ELSE 0 END) as diploma_approved,
                    SUM(CASE WHEN MarriageCertificateStatus = 'approved' THEN 1 ELSE 0 END) as marriage_approved,
                    SUM(CASE WHEN PSAStatus = 'pending' THEN 1 ELSE 0 END) as psa_pending,
                    SUM(CASE WHEN TORStatus = 'pending' THEN 1 ELSE 0 END) as tor_pending,
                    SUM(CASE WHEN DiplomaStatus = 'pending' THEN 1 ELSE 0 END) as diploma_pending,
                    SUM(CASE WHEN MarriageCertificateStatus = 'pending' THEN 1 ELSE 0 END) as marriage_pending
                FROM documents";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: $this->getEmptyDocumentStats();
    }
    
    /**
     * Get school distribution
     */
    private function getSchoolDistribution() {
        $sql = "SELECT 
                    e.School,
                    COUNT(DISTINCT e.StudentId) as student_count
                FROM enrollments e
                WHERE e.Status IN ('Enrolled', 'Ongoing')
                GROUP BY e.School";
        
        $stmt = $this->pdo->query($sql);
        $distribution = ['TB5' => 0, 'BBI' => 0];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $distribution[$row['School']] = (int)$row['student_count'];
        }
        
        return $distribution;
    }
    
    /**
     * Fetch recent activity logs
     */
    private function fetchRecentLogs($limit = 5) {
        $sql = "
            SELECT * FROM (
                -- Student approvals
                SELECT 
                    CONCAT('APPROVED_', si.Id, '_', UNIX_TIMESTAMP(si.UpdatedAt)) as Id,
                    'student_approved' as LogType,
                    si.Id as TargetId,
                    'Student Approved' as Action,
                    CONCAT(
                        'Student ', si.FirstName, ' ', si.LastName,
                        ' (', si.Email, ') has been APPROVED'
                    ) as Description,
                    si.UpdatedAt as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Student Approval' as Category,
                    'success' as Status,
                    si.FirstName as StudentFirstName,
                    si.LastName as StudentLastName,
                    si.Email as StudentEmail,
                    si.Status as StudentStatus
                FROM studentinfos si
                WHERE si.Status = 'Approved'
                AND si.UpdatedAt != si.EntryDate
                
                UNION ALL
                
                -- Student rejections
                SELECT 
                    CONCAT('REJECTED_', si.Id, '_', UNIX_TIMESTAMP(si.UpdatedAt)) as Id,
                    'student_rejected' as LogType,
                    si.Id as TargetId,
                    'Student Rejected' as Action,
                    CONCAT(
                        'Student ', si.FirstName, ' ', si.LastName,
                        ' (', si.Email, ') has been REJECTED'
                    ) as Description,
                    si.UpdatedAt as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Student Rejection' as Category,
                    'danger' as Status,
                    si.FirstName as StudentFirstName,
                    si.LastName as StudentLastName,
                    si.Email as StudentEmail,
                    si.Status as StudentStatus
                FROM studentinfos si
                WHERE si.Status = 'Rejected'
                AND si.UpdatedAt != si.EntryDate
                
                UNION ALL
                
                -- Student registrations
                SELECT 
                    CONCAT('STUDENT_', si.Id, '_', UNIX_TIMESTAMP(si.EntryDate)) as Id,
                    'student_creation' as LogType,
                    si.Id as TargetId,
                    'Student Registered' as Action,
                    CONCAT(
                        'New student registered: ', si.FirstName, ' ', si.LastName,
                        ' (', si.Email, ')'
                    ) as Description,
                    si.EntryDate as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Student Registration' as Category,
                    CASE 
                        WHEN si.Status = 'Approved' THEN 'success'
                        WHEN si.Status = 'Pending' THEN 'warning'
                        ELSE 'secondary'
                    END as Status,
                    si.FirstName as StudentFirstName,
                    si.LastName as StudentLastName,
                    si.Email as StudentEmail,
                    si.Status as StudentStatus
                FROM studentinfos si
                
                UNION ALL
                
                -- Document approvals - PSA
                SELECT 
                    CONCAT('DOC_APPROVED_PSA_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_approved' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Approved' as Action,
                    CONCAT(
                        'PSA Birth Certificate approved for ', s.FirstName, ' ', s.LastName
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Approval' as Category,
                    'success' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.PSAStatus = 'approved'
                
                UNION ALL
                
                -- Document approvals - TOR
                SELECT 
                    CONCAT('DOC_APPROVED_TOR_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_approved' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Approved' as Action,
                    CONCAT(
                        'Transcript of Records approved for ', s.FirstName, ' ', s.LastName
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Approval' as Category,
                    'success' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.TORStatus = 'approved'
                
                UNION ALL
                
                -- Document approvals - Diploma
                SELECT 
                    CONCAT('DOC_APPROVED_DIPLOMA_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_approved' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Approved' as Action,
                    CONCAT(
                        'Diploma approved for ', s.FirstName, ' ', s.LastName
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Approval' as Category,
                    'success' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.DiplomaStatus = 'approved'
                
                UNION ALL
                
                -- Document approvals - Marriage Certificate
                SELECT 
                    CONCAT('DOC_APPROVED_MARRIAGE_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_approved' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Approved' as Action,
                    CONCAT(
                        'Marriage Certificate approved for ', s.FirstName, ' ', s.LastName
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Approval' as Category,
                    'success' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.MarriageCertificateStatus = 'approved'
                
                UNION ALL
                
                -- Document rejections - PSA
                SELECT 
                    CONCAT('DOC_REJECTED_PSA_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_rejected' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Rejected' as Action,
                    CONCAT(
                        'PSA Birth Certificate rejected for ', s.FirstName, ' ', s.LastName,
                        IF(d.Remarks IS NOT NULL AND d.Remarks != '', CONCAT(' - Reason: ', d.Remarks), '')
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Rejection' as Category,
                    'danger' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.PSAStatus = 'rejected'
                
                UNION ALL
                
                -- Document rejections - TOR
                SELECT 
                    CONCAT('DOC_REJECTED_TOR_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_rejected' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Rejected' as Action,
                    CONCAT(
                        'Transcript of Records rejected for ', s.FirstName, ' ', s.LastName,
                        IF(d.Remarks IS NOT NULL AND d.Remarks != '', CONCAT(' - Reason: ', d.Remarks), '')
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Rejection' as Category,
                    'danger' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.TORStatus = 'rejected'
                
                UNION ALL
                
                -- Document rejections - Diploma
                SELECT 
                    CONCAT('DOC_REJECTED_DIPLOMA_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_rejected' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Rejected' as Action,
                    CONCAT(
                        'Diploma rejected for ', s.FirstName, ' ', s.LastName,
                        IF(d.Remarks IS NOT NULL AND d.Remarks != '', CONCAT(' - Reason: ', d.Remarks), '')
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Rejection' as Category,
                    'danger' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.DiplomaStatus = 'rejected'
                
                UNION ALL
                
                -- Document rejections - Marriage Certificate
                SELECT 
                    CONCAT('DOC_REJECTED_MARRIAGE_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
                    'document_rejected' as LogType,
                    d.StudentInfoId as TargetId,
                    'Document Rejected' as Action,
                    CONCAT(
                        'Marriage Certificate rejected for ', s.FirstName, ' ', s.LastName,
                        IF(d.Remarks IS NOT NULL AND d.Remarks != '', CONCAT(' - Reason: ', d.Remarks), '')
                    ) as Description,
                    NOW() as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Document Rejection' as Category,
                    'danger' as Status,
                    s.FirstName as StudentFirstName,
                    s.LastName as StudentLastName,
                    s.Email as StudentEmail,
                    s.Status as StudentStatus
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                WHERE d.MarriageCertificateStatus = 'rejected'
                
                UNION ALL
                
                -- Admin logins
                SELECT 
                    CONCAT('ADMINLOGIN_', a.Id, '_', UNIX_TIMESTAMP(a.LastLogin)) as Id,
                    'admin_login' as LogType,
                    a.Id as TargetId,
                    'Admin Login' as Action,
                    CONCAT('Admin ', a.Username, ' logged in') as Description,
                    a.LastLogin as CreatedAt,
                    a.Id as AdminId,
                    a.Username as AdminName,
                    'Admin Login' as Category,
                    'info' as Status,
                    NULL as StudentFirstName,
                    NULL as StudentLastName,
                    NULL as StudentEmail,
                    NULL as StudentStatus
                FROM admins a
                WHERE a.LastLogin IS NOT NULL
                
                UNION ALL
                
                -- Course creations
                SELECT 
                    CONCAT('COURSE_', c.Id, '_', UNIX_TIMESTAMP(c.CreatedAt)) as Id,
                    'course_change' as LogType,
                    c.Id as TargetId,
                    'Course Created' as Action,
                    CONCAT('New course created: ', c.CourseCode, ' - ', c.CourseName) as Description,
                    c.CreatedAt as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Course Management' as Category,
                    'success' as Status,
                    NULL as StudentFirstName,
                    NULL as StudentLastName,
                    NULL as StudentEmail,
                    NULL as StudentStatus
                FROM courses c
                WHERE c.CreatedAt IS NOT NULL
                
                UNION ALL
                
                -- Batch creations
                SELECT 
                    CONCAT('BATCH_', b.Id, '_', UNIX_TIMESTAMP(b.CreatedAt)) as Id,
                    'batch_change' as LogType,
                    b.Id as TargetId,
                    'Batch Created' as Action,
                    CONCAT('New batch created: ', b.BatchName, ' (', b.BatchCode, ')') as Description,
                    b.CreatedAt as CreatedAt,
                    NULL as AdminId,
                    NULL as AdminName,
                    'Batch Management' as Category,
                    'success' as Status,
                    NULL as StudentFirstName,
                    NULL as StudentLastName,
                    NULL as StudentEmail,
                    NULL as StudentStatus
                FROM batches b
                WHERE b.CreatedAt IS NOT NULL
            ) combined_logs
            ORDER BY CreatedAt DESC
            LIMIT :limit
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Fetch document management data - UPDATED to exclude Form137, ALS, Barangay, Residency
     */
    private function fetchDocumentManagement($limit = 5) {
        $sql = "SELECT 
                    s.Id,
                    CONCAT(s.FirstName, ' ', s.LastName) as StudentName,
                    s.ULI,
                    s.Email,
                    d.Id as DocumentId,
                    d.UploadedAt as SubmissionDate,
                    -- Count of pending documents for this student (excluding Form137, ALS, Barangay, Residency)
                    (SELECT COUNT(*) FROM (
                        SELECT 'PSA' as doc_type WHERE d.PSAStatus = 'pending'
                        UNION ALL SELECT 'TOR' WHERE d.TORStatus = 'pending'
                        UNION ALL SELECT 'Diploma' WHERE d.DiplomaStatus = 'pending'
                        UNION ALL SELECT 'Marriage' WHERE d.MarriageCertificateStatus = 'pending'
                    ) as pending) as pending_count,
                    -- Count of approved documents (excluding Form137, ALS, Barangay, Residency)
                    (SELECT COUNT(*) FROM (
                        SELECT 'PSA' WHERE d.PSAStatus = 'approved'
                        UNION ALL SELECT 'TOR' WHERE d.TORStatus = 'approved'
                        UNION ALL SELECT 'Diploma' WHERE d.DiplomaStatus = 'approved'
                        UNION ALL SELECT 'Marriage' WHERE d.MarriageCertificateStatus = 'approved'
                    ) as approved) as approved_count,
                    -- Count of rejected documents (excluding Form137, ALS, Barangay, Residency)
                    (SELECT COUNT(*) FROM (
                        SELECT 'PSA' WHERE d.PSAStatus = 'rejected'
                        UNION ALL SELECT 'TOR' WHERE d.TORStatus = 'rejected'
                        UNION ALL SELECT 'Diploma' WHERE d.DiplomaStatus = 'rejected'
                        UNION ALL SELECT 'Marriage' WHERE d.MarriageCertificateStatus = 'rejected'
                    ) as rejected) as rejected_count
                FROM documents d
                INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
                ORDER BY d.UploadedAt DESC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Fetch recent enrollments
     */
    private function fetchRecentEnrollments($limit = 5) {
        $sql = "SELECT 
                    e.Id,
                    CONCAT(s.FirstName, ' ', s.LastName) as StudentName,
                    c.CourseName,
                    b.BatchName,
                    b.BatchCode,
                    e.School,
                    e.Status,
                    e.EnrolledAt
                FROM enrollments e
                INNER JOIN studentinfos s ON e.StudentId = s.Id
                INNER JOIN courses c ON e.CourseId = c.Id
                INNER JOIN batches b ON e.BatchId = b.Id
                ORDER BY e.EnrolledAt DESC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get pending approvals count - UPDATED to exclude Form137, ALS, Barangay, Residency
     */
    public function getPendingApprovalsCount() {
        $sql = "SELECT COUNT(DISTINCT d.StudentInfoId) as total 
                FROM documents d
                WHERE d.PSAStatus = 'pending' 
                   OR d.TORStatus = 'pending' 
                   OR d.DiplomaStatus = 'pending' 
                   OR d.MarriageCertificateStatus = 'pending'";
        
        $stmt = $this->pdo->query($sql);
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }
    
    /**
     * Get total approved documents - UPDATED to exclude Form137, ALS, Barangay, Residency
     */
    public function getTotalApprovedDocs() {
        $docStats = $this->stats['documents'];
        return ($docStats['psa_approved'] ?? 0) + 
               ($docStats['tor_approved'] ?? 0) + 
               ($docStats['diploma_approved'] ?? 0) + 
               ($docStats['marriage_approved'] ?? 0);
    }
    
    /**
     * Get total pending documents - UPDATED to exclude Form137, ALS, Barangay, Residency
     */
    public function getTotalPendingDocs() {
        $docStats = $this->stats['documents'];
        return ($docStats['psa_pending'] ?? 0) + 
               ($docStats['tor_pending'] ?? 0) + 
               ($docStats['diploma_pending'] ?? 0) + 
               ($docStats['marriage_pending'] ?? 0);
    }
    
    /**
     * Get time ago string
     */
    public function getTimeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
        return date('M d', $time);
    }
    
    /**
     * Get action icon based on action type
     */
    public function getActionIcon($action) {
        $actionLower = strtolower($action);
        
        if (strpos($actionLower, 'approve') !== false) return 'check-circle';
        if (strpos($actionLower, 'reject') !== false) return 'x-circle';
        if (strpos($actionLower, 'create') !== false || strpos($actionLower, 'registered') !== false) return 'plus-circle';
        if (strpos($actionLower, 'login') !== false) return 'box-arrow-in-right';
        if (strpos($actionLower, 'document') !== false) return 'file-earmark-text';
        if (strpos($actionLower, 'course') !== false) return 'book';
        if (strpos($actionLower, 'batch') !== false) return 'collection';
        
        return 'info-circle';
    }
    
    /**
     * Get action color based on action type
     */
    public function getActionColor($action) {
        $actionLower = strtolower($action);
        
        if (strpos($actionLower, 'approve') !== false) return 'success';
        if (strpos($actionLower, 'reject') !== false) return 'danger';
        if (strpos($actionLower, 'create') !== false || strpos($actionLower, 'registered') !== false) return 'primary';
        if (strpos($actionLower, 'login') !== false) return 'info';
        if (strpos($actionLower, 'course') !== false) return 'info';
        if (strpos($actionLower, 'batch') !== false) return 'secondary';
        
        return 'secondary';
    }
    
    /**
     * Get quick actions with badges
     */
    public function getQuickActions() {
        return [
            [
                'url' => '../course-creation/course-creation.php',
                'icon' => 'plus-circle',
                'label' => 'Create Course',
                'color' => 'primary',
                'badge' => null
            ],
            [
                'url' => '../create-batch/create-batch.php',
                'icon' => 'plus-circle',
                'label' => 'Create Batch',
                'color' => 'success',
                'badge' => null
            ],
            [
                'url' => '../documents-approval/documents-approval.php',
                'icon' => 'file-earmark-text',
                'label' => 'Document Management',
                'color' => 'warning',
                'badge' => $this->getPendingApprovalsCount()
            ],
            [
                'url' => '../account-management/account-management.php',
                'icon' => 'person-check',
                'label' => 'Manage Students',
                'color' => 'info',
                'badge' => $this->stats['students']['pending'] ?? 0
            ]
        ];
    }
    
    /**
     * Get summary statistics for bottom section - UPDATED
     */
    public function getSummaryStats() {
        return [
            ['icon' => 'file-check', 'color' => 'success', 'label' => 'Documents Approved', 'value' => $this->getTotalApprovedDocs()],
            ['icon' => 'file-earmark', 'color' => 'warning', 'label' => 'Pending Documents', 'value' => $this->getTotalPendingDocs()],
            ['icon' => 'check-circle', 'color' => 'info', 'label' => 'Completed Enrollments', 'value' => $this->stats['enrollments']['completed'] ?? 0],
            ['icon' => 'people', 'color' => 'primary', 'label' => 'Active Students', 'value' => $this->stats['students']['approved'] ?? 0]
        ];
    }
    
    /**
     * Log debug information
     */
    private function logDebugInfo() {
        error_log("=== DASHBOARD DATA LOADED ===");
        error_log("Students: " . json_encode($this->stats['students']));
        error_log("Courses: " . json_encode($this->stats['courses']));
        error_log("Batches: " . json_encode($this->stats['batches']));
        error_log("Enrollments: " . json_encode($this->stats['enrollments']));
    }
    
    /**
     * Set default empty stats on error
     */
    private function setDefaultStats() {
        $this->stats = [
            'students' => $this->getEmptyStudentStats(),
            'courses' => $this->getEmptyCourseStats(),
            'batches' => $this->getEmptyBatchStats(),
            'enrollments' => $this->getEmptyEnrollmentStats(),
            'documents' => $this->getEmptyDocumentStats(),
            'schools' => ['TB5' => 0, 'BBI' => 0]
        ];
        
        $this->recentData = [
            'logs' => [],
            'documents' => [],
            'enrollments' => []
        ];
    }
    
    /**
     * Empty stats templates - UPDATED to exclude removed document types
     */
    private function getEmptyStudentStats() {
        return ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'suspended' => 0];
    }
    
    private function getEmptyCourseStats() {
        return ['total' => 0, 'active' => 0, 'tb5_active' => 0, 'bbi_active' => 0];
    }
    
    private function getEmptyBatchStats() {
        return ['total' => 0, 'active' => 0, 'completed' => 0, 'pending' => 0];
    }
    
    private function getEmptyEnrollmentStats() {
        return ['total_enrollments' => 0, 'enrolled' => 0, 'ongoing' => 0, 'completed' => 0, 'dropped' => 0];
    }
    
    private function getEmptyDocumentStats() {
        return [
            'total_documents' => 0,
            'psa_approved' => 0, 'tor_approved' => 0, 'diploma_approved' => 0,
            'marriage_approved' => 0,
            'psa_pending' => 0, 'tor_pending' => 0, 'diploma_pending' => 0,
            'marriage_pending' => 0
        ];
    }
    
    /**
     * Getters for template access
     */
    public function getStats() { return $this->stats; }
    public function getRecentLogs() { return $this->recentData['logs']; }
    public function getDocumentManagement() { return $this->recentData['documents']; }
    public function getRecentEnrollments() { return $this->recentData['enrollments']; }
}

// Initialize dashboard controller
$dashboard = new DashboardController($pdo);
$stats = $dashboard->getStats();
$recentLogs = $dashboard->getRecentLogs();
$documentManagement = $dashboard->getDocumentManagement();
$recentEnrollments = $dashboard->getRecentEnrollments();
$quickActions = $dashboard->getQuickActions();
$summaryStats = $dashboard->getSummaryStats();

// Include layout files
include('../header/header.php');
include('../sidebar/sidebar.php');
?>

<style>
/* Dashboard Layout */
.content-wrapper {
    padding: 20px;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.main-content {
    max-width: 1400px;
    margin: 0 auto;
}

/* Card Styles */
.card {
    border-radius: 10px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}

/* Stat Cards */
.stat-card {
    position: relative;
    overflow: hidden;
}

.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: transform 0.3s;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1);
}

/* Quick Action Buttons */
.quick-action {
    position: relative;
    padding: 1.5rem 1rem;
    transition: all 0.3s;
    text-decoration: none;
    border: 1px solid rgba(0,0,0,0.1);
    display: block;
    text-align: center;
}

.quick-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.quick-action .badge {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.7rem;
    padding: 0.35rem 0.5rem;
}

/* List Group Items */
.list-group-item {
    transition: background-color 0.2s;
    border-left: 3px solid transparent;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item.document-item {
    border-left-color: #ffc107;
}

.list-group-item.log-item {
    border-left-color: #0d6efd;
}

/* Log Entry Styles */
.log-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    flex-shrink: 0;
}

.log-content {
    flex: 1;
    min-width: 0;
}

.log-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.log-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Document Status Badges */
.doc-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
    margin-right: 0.25rem;
}

.doc-status-approved {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.doc-status-pending {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.doc-status-rejected {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

/* Time Ago Text */
.time-ago {
    font-size: 0.7rem;
    color: #6c757d;
    white-space: nowrap;
    flex-shrink: 0;
}

/* School Badges */
.school-badge {
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.school-tb5 {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
}

.school-bbi {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
}

.status-enrolled { background: rgba(25, 135, 84, 0.1); color: #198754; }
.status-ongoing { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
.status-completed { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
.status-dropped { background: rgba(255, 193, 7, 0.1); color: #ffc107; }

/* Header Gradient */
.gradient-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* View All Link */
.view-all-link {
    font-size: 0.85rem;
    text-decoration: none;
}

.view-all-link:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .content-wrapper {
        padding: 15px;
    }
    
    .quick-action {
        padding: 1rem;
    }
}
</style>

<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Header -->
        <div class="card gradient-header border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-2">
                            <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                        </h2>
                        <p class="text-white-50 mb-0">
                            <i class="bi bi-calendar3 me-2"></i><?php echo date('l, F j, Y'); ?>
                            <i class="bi bi-clock ms-3 me-2"></i><span id="currentTime"></span>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="../logs/logs.php" class="btn btn-light btn-sm">
                            <i class="bi bi-clock-history me-1"></i>View Activity Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Stats Row -->
        <div class="row g-4 mb-4">
            <?php 
            $primaryStats = [
                ['icon' => 'people', 'color' => 'primary', 'label' => 'Total Students', 'value' => $stats['students']['total'], 'sub' => $stats['students']['approved'] . ' Approved'],
                ['icon' => 'book', 'color' => 'success', 'label' => 'Active Courses', 'value' => $stats['courses']['active'], 'sub' => 'TB5: ' . $stats['courses']['tb5_active'] . ' | BBI: ' . $stats['courses']['bbi_active']],
                ['icon' => 'collection', 'color' => 'info', 'label' => 'Active Batches', 'value' => $stats['batches']['active'], 'sub' => 'Total: ' . $stats['batches']['total'] . ' batches'],
                ['icon' => 'person-check', 'color' => 'warning', 'label' => 'Enrollments', 'value' => $stats['enrollments']['total_enrollments'], 'sub' => $stats['enrollments']['ongoing'] . ' Ongoing']
            ];
            
            foreach ($primaryStats as $stat): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-<?php echo $stat['color']; ?> bg-opacity-10 me-3">
                                <i class="bi bi-<?php echo $stat['icon']; ?> text-<?php echo $stat['color']; ?>" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1"><?php echo $stat['label']; ?></h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format($stat['value']); ?></h3>
                                <small class="text-<?php echo $stat['color']; ?>"><?php echo $stat['sub']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Secondary Stats Row -->
        <div class="row g-4 mb-4">
            <?php 
            $secondaryStats = [
                ['label' => 'Pending Students', 'icon' => 'person-clock', 'color' => 'warning', 'value' => $stats['students']['pending']],
                ['label' => 'Approved Students', 'icon' => 'person-check-fill', 'color' => 'success', 'value' => $stats['students']['approved']],
                ['label' => 'Students with Documents', 'icon' => 'file-earmark-text', 'color' => 'info', 'value' => $stats['documents']['total_documents']],
                ['label' => 'Completed Batches', 'icon' => 'check-all', 'color' => 'secondary', 'value' => $stats['batches']['completed']]
            ];
            
            foreach ($secondaryStats as $stat): ?>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-<?php echo $stat['color']; ?> bg-opacity-10 text-<?php echo $stat['color']; ?> mb-2">
                                    <?php echo $stat['label']; ?>
                                </span>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($stat['value']); ?></h4>
                            </div>
                            <i class="bi bi-<?php echo $stat['icon']; ?> text-<?php echo $stat['color']; ?>" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- School Distribution -->
        <div class="row g-4 mb-4">
            <?php foreach (['TB5' => 'primary', 'BBI' => 'danger'] as $school => $color): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <img src="../assets/img/<?php echo strtolower($school); ?>-logo.png" 
                                 alt="<?php echo $school; ?>"
                                 class="rounded-circle"
                                 style="width:60px;height:60px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,.15);"
                                 onerror="this.src='../assets/img/default-school.png'">
                            <div class="flex-grow-1">
                                <h5 class="mb-1"><?php echo $school; ?> Students</h5>
                                <h2 class="fw-bold text-<?php echo $color; ?> mb-0">
                                    <?php echo number_format($stats['schools'][$school] ?? 0); ?>
                                </h2>
                                <small class="text-muted">Currently enrolled</small>
                            </div>
                            <div class="text-end">
                                <h6>Courses</h6>
                                <span class="badge bg-<?php echo $color; ?>">
                                    <?php echo $stats['courses'][strtolower($school) . '_active'] ?? 0; ?> Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($quickActions as $action): ?>
                            <div class="col-md-3">
                                <a href="<?php echo $action['url']; ?>" 
                                   class="quick-action btn btn-outline-<?php echo $action['color']; ?> w-100 py-3">
                                    <i class="bi bi-<?php echo $action['icon']; ?> d-block mb-2" style="font-size: 1.5rem;"></i>
                                    <span><?php echo $action['label']; ?></span>
                                    <?php if ($action['badge'] > 0): ?>
                                    <span class="badge rounded-pill bg-danger">
                                        <?php echo $action['badge']; ?>
                                    </span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Document Management -->
        <div class="row g-4">
            <!-- Recent Activity -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-clock-history me-2"></i>Recent Activity
                        </h5>
                        <a href="../logs/logs.php" class="view-all-link text-primary">View All <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (!empty($recentLogs)): ?>
                                <?php foreach ($recentLogs as $log): ?>
                                <div class="list-group-item log-item border-0 border-bottom py-3">
                                    <div class="d-flex w-100 align-items-start gap-2">
                                        <div class="log-icon bg-<?php echo $dashboard->getActionColor($log['Action']); ?> bg-opacity-10">
                                            <i class="bi bi-<?php echo $dashboard->getActionIcon($log['Action']); ?> text-<?php echo $dashboard->getActionColor($log['Action']); ?>"></i>
                                        </div>
                                        <div class="log-content">
                                            <div class="log-title"><?php echo htmlspecialchars($log['Action']); ?></div>
                                            <div class="log-description"><?php echo htmlspecialchars($log['Description']); ?></div>
                                        </div>
                                        <small class="time-ago"><?php echo $dashboard->getTimeAgo($log['CreatedAt']); ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="list-group-item text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No recent activity
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Management - Shows only PSA, TOR, Diploma, Marriage -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-earmark-text me-2"></i>Document Management
                        </h5>
                        <a href="../documents-approval/documents-approval.php" class="view-all-link text-primary">Manage All <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (!empty($documentManagement)): ?>
                                <?php foreach ($documentManagement as $doc): ?>
                                <div class="list-group-item document-item border-0 border-bottom py-3">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?php echo htmlspecialchars($doc['StudentName']); ?></h6>
                                            <div class="mb-1">
                                                <?php if ($doc['pending_count'] > 0): ?>
                                                <span class="doc-status-badge doc-status-pending">
                                                    <i class="bi bi-clock me-1"></i><?php echo $doc['pending_count']; ?> Pending
                                                </span>
                                                <?php endif; ?>
                                                <?php if ($doc['approved_count'] > 0): ?>
                                                <span class="doc-status-badge doc-status-approved">
                                                    <i class="bi bi-check-circle me-1"></i><?php echo $doc['approved_count']; ?> Approved
                                                </span>
                                                <?php endif; ?>
                                                <?php if ($doc['rejected_count'] > 0): ?>
                                                <span class="doc-status-badge doc-status-rejected">
                                                    <i class="bi bi-x-circle me-1"></i><?php echo $doc['rejected_count']; ?> Rejected
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                Updated: <?php echo date('M d, Y', strtotime($doc['SubmissionDate'])); ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">Total</small>
                                            <span class="fw-bold"><?php echo $doc['pending_count'] + $doc['approved_count'] + $doc['rejected_count']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="list-group-item text-center py-4 text-muted">
                                    <i class="bi bi-file-earmark-text text-info fs-1 d-block mb-2"></i>
                                    No document records found
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-plus me-2"></i>Recent Enrollments
                        </h5>
                        <a href="../enrollments/enrollments.php" class="view-all-link text-primary">View All <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Student</th>
                                        <th>Course</th>
                                        <th>Batch</th>
                                        <th>School</th>
                                        <th>Status</th>
                                        <th>Enrolled Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentEnrollments)): ?>
                                        <?php foreach ($recentEnrollments as $enroll): ?>
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-person-fill text-primary"></i>
                                                    </div>
                                                    <span class="fw-semibold"><?php echo htmlspecialchars($enroll['StudentName']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($enroll['CourseName']); ?></td>
                                            <td>
                                                <small class="text-muted"><?php echo htmlspecialchars($enroll['BatchCode']); ?></small>
                                                <br>
                                                <small><?php echo htmlspecialchars($enroll['BatchName']); ?></small>
                                            </td>
                                            <td>
                                                <span class="school-badge school-<?php echo strtolower($enroll['School']); ?>">
                                                    <?php echo $enroll['School']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($enroll['Status']); ?>">
                                                    <?php echo $enroll['Status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($enroll['EnrolledAt'])); ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                No recent enrollments
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row text-center">
                            <?php foreach ($summaryStats as $stat): ?>
                            <div class="col-md-3 col-6 mb-3 mb-md-0">
                                <div class="p-3">
                                    <i class="bi bi-<?php echo $stat['icon']; ?> text-<?php echo $stat['color']; ?> fs-2"></i>
                                    <h5 class="mt-2 mb-0"><?php echo number_format($stat['value']); ?></h5>
                                    <small class="text-muted"><?php echo $stat['label']; ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Info (only in debug mode) -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="alert alert-info mt-4">
            <h6>Debug Info:</h6>
            <pre><?php print_r($stats); ?></pre>
            <pre><?php print_r($documentManagement); ?></pre>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Live time update
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    });
    document.getElementById('currentTime').textContent = timeString;
}
updateTime();
setInterval(updateTime, 1000);
</script>

<?php include('../footer/footer.php'); ?>