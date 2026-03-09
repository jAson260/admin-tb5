<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $raw   = file_get_contents('php://input');
    $input = json_decode($raw, true);

    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
        exit;
    }

    $school   = trim($input['school']   ?? '');
    $courseId = (int)($input['courseId'] ?? 0);
    $subjects = $input['subjects']       ?? [];

    if (!in_array($school, ['TB5', 'BBI'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid school.']);
        exit;
    }

    if ($courseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid course.']);
        exit;
    }

    if (empty($subjects)) {
        echo json_encode(['success' => false, 'message' => 'No subjects provided.']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO subjects (
            School, CourseId, SubjectCode, SubjectName,
            Competency, Hours, Days,
            IsActive, CreatedAt, UpdatedAt
        ) VALUES (
            :school, :courseId, :subjectCode, :subjectName,
            :competency, :hours, :days,
            1, NOW(), NOW()
        )
    ");

    $pdo->beginTransaction();

    foreach ($subjects as $s) {
        $code       = strtoupper(trim($s['code']       ?? ''));
        $name       = trim($s['name']       ?? '');
        $competency = trim($s['competency'] ?? '');
        $hours      = (int)($s['hours']     ?? 0);
        $days       = $hours > 0 ? (int)ceil($hours / 8) : null;

        if (empty($code) || empty($name) || $hours <= 0) continue;

        if (!in_array($competency, ['Basic', 'Common', 'Core'])) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => "Invalid competency for subject '{$code}'."]);
            exit;
        }

        // Check duplicate code
        $chk = $pdo->prepare("SELECT Id FROM subjects WHERE SubjectCode = ?");
        $chk->execute([$code]);
        if ($chk->fetch()) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => "Subject code '{$code}' already exists."]);
            exit;
        }

        $stmt->execute([
            ':school'      => $school,
            ':courseId'    => $courseId,
            ':subjectCode' => $code,
            ':subjectName' => $name,
            ':competency'  => $competency,
            ':hours'       => $hours,
            ':days'        => $days
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Subjects saved successfully.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("save-subjects-multi.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}