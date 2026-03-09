<?php
session_start();
require_once('../db-connect.php'); // Path to your PDO/DB connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $targetDir = "../uploads/documents/";
    
    if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }

    // Helper for safe uploads
    function processFile($fileInput, $prefix, $dir, $uid) {
        if (!empty($fileInput['name'])) {
            $name = $prefix . "_" . $uid . "_" . time() . "." . pathinfo($fileInput['name'], PATHINFO_EXTENSION);
            move_uploaded_file($fileInput["tmp_name"], $dir . $name);
            return $name;
        }
        return null;
    }

    $psa = processFile($_FILES['psa_file'] ?? null, 'PSA', $targetDir, $user_id);
    $tor = processFile($_FILES['tor_file'] ?? null, 'TOR', $targetDir, $user_id);
    $diploma = processFile($_FILES['diploma_file'] ?? null, 'DIPLOMA', $targetDir, $user_id);
    $marriage = processFile($_FILES['marriage_cert'] ?? null, 'MARRIAGE', $targetDir, $user_id);

    try {
        // CHECK: Does the document row exist?
        $check = $pdo->prepare("SELECT Id FROM documents WHERE StudentInfoId = ? LIMIT 1");
        $check->execute([$user_id]);
        $row = $check->fetch();

        if ($row) {
            // UPDATE: If the row exists, only update the files that were actually sent now
            $updateFields = [];
            $params = [];

            if ($psa) { $updateFields[] = "PSAPath = ?"; $params[] = $psa; }
            if ($tor) { $updateFields[] = "TORPath = ?"; $params[] = $tor; }
            if ($diploma) { $updateFields[] = "DiplomaPath = ?"; $params[] = $diploma; }
            if ($marriage) { $updateFields[] = "MarriageCertificatePath = ?"; $params[] = $marriage; }

            if (!empty($updateFields)) {
                $params[] = $user_id;
                $sql = "UPDATE documents SET " . implode(", ", $updateFields) . " WHERE StudentInfoId = ?";
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Records updated successfully.']);

        } else {
            // INSERT: New record if none exists
            $sql = "INSERT INTO documents (StudentInfoId, PSAPath, TORPath, DiplomaPath, MarriageCertificatePath) VALUES (?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$user_id, $psa, $tor, $diploma, $marriage]);
            echo json_encode(['success' => true, 'message' => 'Documents saved.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}