<?php


session_start();
require_once('../../db-connect.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType; // ← add this

// ─── WRITE TOR ROWS ───────────────────────────────────────────────────────────
function writeStudentTOR($sheet, $student, $course, $subjectRows, $gradeMap,
                          $graduationDate, $soNumber, $theoreticalGrade,
                          $practicalGrade, $averageGrade, $finalGrade, $remarks,
                          $writeHeader = false, $rowIndex = 2) {

    $mi       = !empty($student['MiddleName'])
        ? strtoupper(substr($student['MiddleName'], 0, 1)) . '.' : '';
    $ext      = !empty($student['ExtensionName'])
        ? ' ' . strtoupper($student['ExtensionName']) : '';
    $fullName = strtoupper($student['LastName']) . ', '
              . strtoupper($student['FirstName']) . ' ' . $mi . $ext;

    $coord = function($col, $row) {
        return Coordinate::stringFromColumnIndex($col) . $row;
    };

    $subjectHeaders = [];
    $subjectValues  = [];
    $totalHrs       = 0;
    $subjectNum     = 1;

    foreach ($subjectRows as $sub) {
        $code      = $sub['SubjectCode'];
        $g         = $gradeMap[$code] ?? [];
        $totalHrs += intval($sub['Hours']);

        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Code';
        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Name';
        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Hours';
        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Theoretical (30%)';
        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Practical (70%)';
        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Final Grade';
        $subjectHeaders[] = 'Subject ' . $subjectNum . ' - Remarks';

        $subjectValues[]  = $code;
        $subjectValues[]  = $sub['SubjectName'];
        $subjectValues[]  = $sub['Hours'] . ' hours';
        $subjectValues[]  = !empty($g['theoretical']) ? $g['theoretical'] . '%' : '';
        $subjectValues[]  = !empty($g['practical'])   ? $g['practical']   . '%' : '';
        $subjectValues[]  = !empty($g['final'])        ? $g['final']       . '%' : '';
        $subjectValues[]  = $g['remarks'] ?? '';

        $subjectNum++;
    }

    if ($writeHeader) {
        $headers = [
            'Course/Qualification',
            'Date of Graduation',
            'Full Name',
            'ULI',
            'SO Number',
            'Sex',
            'Date of Birth',
            'Place of Birth',
            'Secondary School',
            'Secondary Year Graduated',
            'College/Vocational',
            'Tertiary Year Graduated',
            'Entrance Date',
            ...$subjectHeaders,
            'Total Hours',
            'Theoretical Grade (30%)',
            'Practical Grade (70%)',
            'Average Grade',
            'Final Grade',
            'Remarks',
        ];

        $col = 1;
        foreach ($headers as $header) {
            $sheet->getCell($coord($col, 1))->setValue($header);
            $col++;
        }

        $sheet->freezePane('A2');
    }

    $dataRow = [
        strtoupper($course['CourseName']),
        !empty($graduationDate) ? date('m/d/Y', strtotime($graduationDate)) : '',
        $fullName,
        $student['ULI']        ?? 'N/A',
        $soNumber,                                      // ← SO Number (will force as string below)
        strtoupper($student['Sex']         ?? ''),
        !empty($student['BirthDate'])      ? date('m/d/Y', strtotime($student['BirthDate']))   : '',
        strtoupper($student['BirthPlace']  ?? ''),
        $student['SecondarySchool']        ?? '',
        $student['SecondaryYearCompleted'] ?? '',
        $student['TertiarySchool']         ?? '',
        $student['TertiaryYearCompleted']  ?? '',
        !empty($student['EntryDate'])      ? date('F d, Y', strtotime($student['EntryDate'])) : '',
        ...$subjectValues,
        $totalHrs . ' hours',
        $theoreticalGrade . '%',
        $practicalGrade   . '%',
        $averageGrade     . '%',
        $finalGrade       . '%',
        $remarks,
    ];

    $col = 1;
    foreach ($dataRow as $value) {
        $c = $coord($col, $rowIndex);

        // ── Force SO Number column (col 5) to be plain text ──────────────────
        if ($col === 5) {
            $sheet->getCell($c)->setValueExplicit((string) $value, DataType::TYPE_STRING);
        } else {
            $sheet->getCell($c)->setValue($value);
        }

        $col++;
    }

    return $rowIndex + 1;
}

// ─── SAVE TOR TO DB ───────────────────────────────────────────────────────────
function saveTORRecord($pdo, $studentId, $courseId, $theoretical, $practical,
                        $average, $final, $remarks, $soNumber, $graduationDate, $fileName) {
    $stmt = $pdo->prepare("
        INSERT INTO tor_records (
            StudentId, CourseId, TheoreticalGrade, PracticalGrade,
            AverageGrade, FinalGrade, Remarks, ISONumber,
            GraduationDate, FileName, FileSize, CreatedBy
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)
        ON DUPLICATE KEY UPDATE
            TheoreticalGrade = VALUES(TheoreticalGrade),
            PracticalGrade   = VALUES(PracticalGrade),
            AverageGrade     = VALUES(AverageGrade),
            FinalGrade       = VALUES(FinalGrade),
            Remarks          = VALUES(Remarks),
            ISONumber        = VALUES(ISONumber),
            GraduationDate   = VALUES(GraduationDate),
            FileName         = VALUES(FileName)
    ");
    $stmt->execute([
        $studentId, $courseId ?: null,
        $theoretical, $practical, $average, $final,
        $remarks, $soNumber, $graduationDate,
        $fileName, $_SESSION['admin_id'] ?? null
    ]);
}

// ─── MAIN ────────────────────────────────────────────────────────────────────
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

if (!empty($data['student_ids']) && is_array($data['student_ids'])) {
    $studentIds = $data['student_ids'];
} elseif (!empty($data['student_id'])) {
    $studentIds = [$data['student_id']];
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No student_ids provided']);
    exit;
}

$courseId         = $data['course_id']                  ?? null;
$batchId          = $data['batch_id']                   ?? null;
$graduationDate   = $data['graduation_date']            ?? '';
$soNumber         = $data['so_number']                  ?? '';
$theoreticalGrade = floatval($data['theoretical_grade'] ?? 0);
$practicalGrade   = floatval($data['practical_grade']   ?? 0);
$averageGrade     = floatval($data['average_grade']     ?? 0);
$finalGrade       = floatval($data['final_grade']       ?? 0);
$remarks          = $data['remarks']                    ?? '';

// ── FIX: always use per_student_grades — ignore subject_grades entirely ───────
$perStudentGrades = $data['per_student_grades'] ?? [];

// ── FIX: re-index so numeric $index always matches $perStudentGrades key ──────
$studentIds = array_values($studentIds);

try {
    $course = null;
    if ($courseId) {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE Id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if (!$course) {
        $course = ['CourseName' => 'N/A', 'CourseCode' => 'N/A'];
    }

    $subjectRows = [];
    if ($courseId) {
        $stmt = $pdo->prepare("
            SELECT SubjectCode, SubjectName, Competency, Hours, SubjectType
            FROM subjects
            WHERE CourseId = ? AND IsActive = 1
            ORDER BY FIELD(Competency, 'Basic', 'Common', 'Core'), SubjectName
        ");
        $stmt->execute([$courseId]);
        $subjectRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('TOR Records');

    $fileName  = count($studentIds) === 1
        ? 'TOR_' . preg_replace('/[^A-Za-z0-9_\-]/', '_',
            (function() use (&$pdo, &$studentIds) {
                $s = $pdo->prepare("SELECT LastName, FirstName FROM studentinfos WHERE Id = ?");
                $s->execute([$studentIds[0]]);
                $r = $s->fetch(PDO::FETCH_ASSOC);
                return ($r['LastName'] ?? 'Unknown') . '_' . ($r['FirstName'] ?? '');
            })()
          ) . '_' . date('Y-m-d') . '.xlsx'
        : 'TOR_Batch_' . date('Y-m-d_His') . '.xlsx';

    $first    = true;
    $rowIndex = 2;

    foreach ($studentIds as $index => $sid) {
        $stmt = $pdo->prepare("SELECT * FROM studentinfos WHERE Id = ?");
        $stmt->execute([$sid]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) continue;

        // ── FIX: always use per_student_grades[$index] ────────────────────────
        $studentGradeMap    = [];
        $studentTheoretical = $theoreticalGrade;
        $studentPractical   = $practicalGrade;
        $studentAverage     = $averageGrade;
        $studentFinal       = $finalGrade;
        $studentRemarks     = $remarks;

        if (!empty($perStudentGrades[$index])) {
            foreach ($perStudentGrades[$index] as $sg) {
                if (!empty($sg['code'])) {
                    $studentGradeMap[$sg['code']] = $sg;
                }
            }
            $thVals = array_column($perStudentGrades[$index], 'theoretical');
            $prVals = array_column($perStudentGrades[$index], 'practical');
            $thVals = array_filter($thVals, fn($v) => $v > 0);
            $prVals = array_filter($prVals, fn($v) => $v > 0);

            if (count($thVals) && count($prVals)) {
                $avgTh              = round(array_sum($thVals) / count($thVals));
                $avgPr              = round(array_sum($prVals) / count($prVals));
                $avgFin             = round(($avgTh * 0.3) + ($avgPr * 0.7));
                $studentTheoretical = $avgTh;
                $studentPractical   = $avgPr;
                $studentAverage     = $avgFin;
                $studentFinal       = $avgFin;
                $studentRemarks     = $avgFin >= 85 ? 'Competent' : 'Not Yet Competent';
            }
        }

        $rowIndex = writeStudentTOR(
            $sheet, $student, $course, $subjectRows, $studentGradeMap,
            $graduationDate, $soNumber,
            $studentTheoretical, $studentPractical,
            $studentAverage, $studentFinal, $studentRemarks,
            $first, $rowIndex
        );

        saveTORRecord(
            $pdo, $sid, $courseId,
            $studentTheoretical, $studentPractical,
            $studentAverage, $studentFinal, $studentRemarks,
            $soNumber, $graduationDate, $fileName
        );

        $first = false;
    }

    foreach ($sheet->getColumnIterator() as $column) {
        $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
    }

    if (ob_get_level()) ob_end_clean();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>