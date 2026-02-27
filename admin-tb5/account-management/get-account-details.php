<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\get-account-details.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');
checkAdmin();

try {
    if (!isset($_GET['id']) || !isset($_GET['type'])) {
        throw new Exception('Missing required parameters');
    }
    
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    
    if ($type === 'admin') {
        // Get admin account details
        $stmt = $pdo->prepare("
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                FirstName,
                LastName,
                Email,
                Role,
                Status,
                LastLogin,
                CreatedAt
            FROM admins
            WHERE Id = ?
        ");
        
        $stmt->execute([$id]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } else if ($type === 'student') {
        // Get student account details from studentinfos table
        $stmt = $pdo->prepare("
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                FirstName,
                LastName,
                MiddleName,
                ExtensionName,
                Email,
                ULI,
                Nationality,
                BirthDate,
                Sex,
                CivilStatus,
                Employment,
                Age,
                BirthPlace,
                ContactNo,
                Street,
                BarangayName,
                CityName,
                ProvinceName,
                RegionName,
                District,
                SecondarySchool,
                SecondaryYearCompleted,
                TertiarySchool,
                TertiaryYearCompleted,
                Status,
                Role,
                LastLogin,
                EntryDate as CreatedAt,
                EmailVerified,
                ProfilePicture
            FROM studentinfos
            WHERE Id = ?
        ");
        
        $stmt->execute([$id]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } else {
        throw new Exception('Invalid account type');
    }
    
    if (!$account) {
        throw new Exception('Account not found');
    }
    
    echo json_encode([
        'success' => true,
        'account' => $account
    ]);
    
} catch (Exception $e) {
    error_log("Get account details error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>