<?php


session_start();
require_once('../../db-connect.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

use Mpdf\Mpdf;

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
$graduationDate   = $data['graduation_date']            ?? '';
$soNumber         = $data['so_number']                  ?? '';
$theoreticalGrade = floatval($data['theoretical_grade'] ?? 0);
$practicalGrade   = floatval($data['practical_grade']   ?? 0);
$averageGrade     = floatval($data['average_grade']     ?? 0);
$finalGrade       = floatval($data['final_grade']       ?? 0);
$remarks          = $data['remarks']                    ?? '';

// ── FIX: always use per_student_grades — ignore subject_grades entirely ───────
$perStudentGrades = $data['per_student_grades'] ?? [];

try {
    // Fetch course
    $course = null;
    if ($courseId) {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE Id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if (!$course) {
        $course = ['CourseName' => 'N/A', 'CourseCode' => 'N/A'];
    }

    // Fetch subjects
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

    $mpdf = new Mpdf([
        'margin_top'    => 10,
        'margin_bottom' => 10,
        'margin_left'   => 15,
        'margin_right'  => 15,
        'format'        => 'A4',
    ]);

    $isFirst = true;

    // ── FIX: use numeric re-indexed loop so $index always matches array key ───
    $studentIds = array_values($studentIds);

    foreach ($studentIds as $index => $sid) {
        $stmt = $pdo->prepare("SELECT * FROM studentinfos WHERE Id = ?");
        $stmt->execute([$sid]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) continue;

        if (!$isFirst) {
            $mpdf->AddPage();
        }

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

        $html = buildTORHtml(
            $student, $course, $subjectRows, $studentGradeMap,
            $graduationDate, $soNumber,
            $studentTheoretical, $studentPractical,
            $studentAverage, $studentFinal, $studentRemarks
        );

        $mpdf->WriteHTML($html);
        $isFirst = false;
    }

    if (ob_get_level()) ob_end_clean();

    $fileName = count($studentIds) === 1
        ? 'TOR_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $student['LastName'])
            . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $student['FirstName'])
            . '_' . date('Y-m-d') . '.pdf'
        : 'TOR_Batch_' . date('Y-m-d_His') . '.pdf';

    $mpdf->Output($fileName, 'D');
    exit;

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// ─── BUILD TOR HTML PER STUDENT ───────────────────────────────────────────────
function buildTORHtml($student, $course, $subjectRows, $gradeMap,
                       $graduationDate, $soNumber, $theoreticalGrade,
                       $practicalGrade, $averageGrade, $finalGrade, $remarks) {

    $mi       = !empty($student['MiddleName'])
        ? strtoupper(substr($student['MiddleName'], 0, 1)) . '.' : '';
    $ext      = !empty($student['ExtensionName'])
        ? ' ' . strtoupper($student['ExtensionName']) : '';
    $fullName = strtoupper($student['LastName']) . ', '
              . strtoupper($student['FirstName']) . ' ' . $mi . $ext;

    $gradDate   = !empty($graduationDate) ? date('m/d/Y', strtotime($graduationDate))  : '';
    $birthDate  = !empty($student['BirthDate']) ? date('m/d/Y', strtotime($student['BirthDate'])) : '';
    $entryDate  = !empty($student['EntryDate'])  ? date('F d, Y', strtotime($student['EntryDate'])) : '';

    // ── SUBJECTS ROWS ─────────────────────────────────────────────────────────
    $subjectHtml = '';
    $totalHrs    = 0;
    $lastComp    = '';

    foreach ($subjectRows as $sub) {
        $code  = $sub['SubjectCode'];
        $g     = $gradeMap[$code] ?? [];
        $hours = intval($sub['Hours']);
        $totalHrs += $hours;

        $th  = !empty($g['theoretical']) ? $g['theoretical'] : '—';
        $pr  = !empty($g['practical'])   ? $g['practical']   : '—';
        $fin = !empty($g['final'])        ? $g['final']       : '—';
        $rem = $g['remarks']             ?? '—';

        if ($sub['Competency'] !== $lastComp) {
            $subjectHtml .= '
                <tr>
                    <td colspan="6" style="background:#f0f0f0;font-weight:bold;font-size:9px;padding:3px 5px;">
                        ' . htmlspecialchars($sub['Competency']) . ' Competencies
                    </td>
                </tr>';
            $lastComp = $sub['Competency'];
        }

        $subjectHtml .= '
            <tr>
                <td style="font-size:8px;padding:2px 4px;">' . htmlspecialchars($code) . '</td>
                <td style="font-size:8px;padding:2px 4px;">' . htmlspecialchars($sub['SubjectName']) . '</td>
                <td style="text-align:center;font-size:8px;padding:2px 4px;">' . $hours . ' hours</td>
                <td style="text-align:center;font-size:8px;padding:2px 4px;">' . $th . '</td>
                <td style="text-align:center;font-size:8px;padding:2px 4px;">' . $pr . '</td>
                <td style="text-align:center;font-size:8px;padding:2px 4px;font-weight:bold;">' . $fin . '</td>
                <td style="text-align:center;font-size:8px;padding:2px 4px;">' . htmlspecialchars($rem) . '</td>
            </tr>';
    }

    // ── FULL HTML ─────────────────────────────────────────────────────────────
    return '
    <style>
        body        { font-family: Arial, sans-serif; font-size: 10px; }
        table       { border-collapse: collapse; width: 100%; }
        td, th      { border: 1px solid #999; padding: 3px 5px; }
        .no-border td, .no-border th { border: none; }
        .header-title { text-align: center; font-size: 11px; font-weight: bold; }
        .school-name  { text-align: center; font-size: 13px; font-weight: bold; color: #1a3a6b; }
        .school-addr  { text-align: center; font-size: 8px; color: #555; }
        .section-title { text-align: center; font-weight: bold; font-size: 9px;
                         background: #ddd; padding: 3px; letter-spacing: 1px; }
        .form-label   { font-size: 8px; color: #555; }
        .form-value   { font-size: 9px; font-weight: bold; }
        .tfoot-row td { background: #f5f5f5; font-weight: bold; font-size: 9px; }
    </style>

    <!-- FORM IX LABEL -->
    <p style="text-align:right;font-size:8px;margin:0;">FORM IX</p>

     <!-- HEADER -->
    <table class="no-border" style="margin-bottom:4px;">
        <tr>
            <td style="width:60px;text-align:center;vertical-align:middle;">
                <img src="' . __DIR__ . '/../assets/img/tb5-logo.png" style="width:55px;height:55px;" />
            </td>
            <td style="text-align:center;vertical-align:middle;">
                <div style="font-size:11px;font-weight:bold;text-align:center;">Records of Candidates for Graduation from TVET Courses</div>
                <div style="font-size:13px;font-weight:bold;color:#1a3a6b;text-align:center;">The Big Five Training and Assessment Center Inc.</div>
                <div style="font-size:8px;color:#555;text-align:center;">4th Floor Vitra Bldg, 123 P. Alcantara St. Brgy. VII-B, San Pablo City, Laguna</div>
            </td>
        </tr>
    </table>

    <!-- COURSE & DATE -->
    <table style="margin-bottom:2px;">
        <tr>
            <td style="width:70%;font-size:9px;">
                <strong>Course / Qualification:</strong>
                ' . htmlspecialchars(strtoupper($course['CourseName'])) . '
            </td>
            <td style="font-size:9px;">
                <strong>Date of Graduation:</strong> ' . $gradDate . '
            </td>
        </tr>
    </table>

    <!-- PERSONAL RECORDS -->
    <div class="section-title">PERSONAL RECORDS</div>
    <table style="margin-bottom:2px;">
        <tr>
            <td style="width:50%;">
                <table class="no-border">
                    <tr>
                        <td class="form-label" style="width:100px;">ULI :</td>
                        <td class="form-value">' . htmlspecialchars($student['ULI'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Name of Trainee :</td>
                        <td class="form-value">' . htmlspecialchars($fullName) . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Sex :</td>
                        <td class="form-value">' . htmlspecialchars(strtoupper($student['Sex'] ?? '')) . '</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table class="no-border">
                    <tr>
                        <td class="form-label" style="width:100px;">SO Number :</td>
                        <td class="form-value">' . htmlspecialchars($soNumber) . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Date of Birth :</td>
                        <td class="form-value">' . $birthDate . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Place of Birth :</td>
                        <td class="form-value">' . htmlspecialchars(strtoupper($student['BirthPlace'] ?? '')) . '</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- RECORD OF CANDIDATES -->
    <div class="section-title">RECORD OF CANDIDATES FOR GRADUATION</div>
    <table style="margin-bottom:2px;">
        <tr>
            <td style="width:50%;">
                <table class="no-border">
                    <tr>
                        <td class="form-label" style="width:130px;">Elementary Completed :</td>
                        <td class="form-value">' . htmlspecialchars($student['ElementarySchool'] ?? '') . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Secondary Completed :</td>
                        <td class="form-value">' . htmlspecialchars($student['SecondarySchool'] ?? '') . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">College / Vocational School :</td>
                        <td class="form-value">' . htmlspecialchars($student['TertiarySchool'] ?? '') . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Entrance Date :</td>
                        <td class="form-value">' . $entryDate . '</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table class="no-border">
                    <tr>
                        <td class="form-label" style="width:130px;">Year Graduated :</td>
                        <td class="form-value">' . htmlspecialchars($student['ElementaryYearCompleted'] ?? '') . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Year Graduated :</td>
                        <td class="form-value">' . htmlspecialchars($student['SecondaryYearCompleted'] ?? '') . '</td>
                    </tr>
                    <tr>
                        <td class="form-label">Year Graduated :</td>
                        <td class="form-value">' . htmlspecialchars($student['TertiaryYearCompleted'] ?? '') . '</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ACADEMIC RECORD -->
    <div class="section-title">ACADEMIC RECORD</div>
    <table style="margin-bottom:2px;">
         <thead>
            <tr style="background:#d9d9d9;color:#000;">
                <th style="width:80px;font-size:8px;text-align:center;">Code Number</th>
                <th style="font-size:8px;">Descriptive Title of Subjects</th>
                <th style="width:55px;font-size:8px;text-align:center;">Nominal Duration</th>
                <th style="width:45px;font-size:8px;text-align:center;">Theoretical 30%</th>
                <th style="width:45px;font-size:8px;text-align:center;">Actual 70%</th>
                <th style="width:45px;font-size:8px;text-align:center;">Final Grade</th>
                <th style="width:65px;font-size:8px;text-align:center;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            ' . $subjectHtml . '
        </tbody>
        <tfoot>
            <tr class="tfoot-row">
                <td colspan="2" style="text-align:right;font-size:9px;">Total Hours</td>
                <td style="text-align:center;">' . $totalHrs . ' hours</td>
                <td style="text-align:center;">' . $theoreticalGrade . '</td>
                <td style="text-align:center;">' . $practicalGrade . '</td>
                <td style="text-align:center;font-size:11px;">' . $finalGrade . '</td>
                <td style="text-align:center;">' . htmlspecialchars($remarks) . '</td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align:center;font-size:8px;margin:2px 0;">*** End of the Record***</p>

    <!-- GRADING SYSTEM -->
    <table class="no-border" style="margin-bottom:4px;">
        <tr>
            <td style="text-align:center;vertical-align:top;">
                <div style="font-weight:bold;font-size:9px;text-align:center;margin-bottom:4px;">GRADING SYSTEM</div>
                <table style="margin:0 auto;border-collapse:collapse;" class="no-border">
                    <tr>
                        <td style="font-size:8px;padding:1px 10px;text-align:right;">97% and above</td>
                        <td style="font-size:8px;padding:1px 10px;text-align:left;">Excellent</td>
                        <td style="font-size:8px;padding:1px 20px;text-align:left;">Competent</td>
                    </tr>
                    <tr>
                        <td style="font-size:8px;padding:1px 10px;text-align:right;">94% – 96%</td>
                        <td style="font-size:8px;padding:1px 10px;text-align:left;">Very Good</td>
                        <td style="font-size:8px;padding:1px 20px;text-align:left;">Competent</td>
                    </tr>
                    <tr>
                        <td style="font-size:8px;padding:1px 10px;text-align:right;">91% – 93%</td>
                        <td style="font-size:8px;padding:1px 10px;text-align:left;">Good</td>
                        <td style="font-size:8px;padding:1px 20px;text-align:left;">Competent</td>
                    </tr>
                    <tr>
                        <td style="font-size:8px;padding:1px 10px;text-align:right;">88% – 90%</td>
                        <td style="font-size:8px;padding:1px 10px;text-align:left;">Fair</td>
                        <td style="font-size:8px;padding:1px 20px;text-align:left;">Competent</td>
                    </tr>
                    <tr>
                        <td style="font-size:8px;padding:1px 10px;text-align:right;">85% – 87%</td>
                        <td style="font-size:8px;padding:1px 10px;text-align:left;">Passed</td>
                        <td style="font-size:8px;padding:1px 20px;text-align:left;">Competent</td>
                    </tr>
                    <tr>
                        <td style="font-size:8px;padding:1px 10px;text-align:right;">Below 85%</td>
                        <td style="font-size:8px;padding:1px 10px;text-align:left;">Failed</td>
                        <td style="font-size:8px;padding:1px 20px;text-align:left;">Not Yet Competent</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- CERTIFICATION -->
    <div style="font-weight:bold;font-size:10px;text-align:center;margin:4px 0;">C E R T I F I C A T I O N</div>
    <p style="font-size:8px;text-align:center;margin:2px 0;">
        I certify that the following records of
        <strong style="color:#1a3a6b;">' . htmlspecialchars($fullName) . ',</strong>
        a candidate for graduation in this institution have<br>
        been verified by me, and that the true copies of the official record sustaining the same are kept in this file of our school.
        I do certify<br>
        that this student enrolled in this institution on
        <strong style="color:#1a3a6b;">' . $entryDate . '</strong>
        to <strong style="color:#1a3a6b;">' . $gradDate . '</strong>.
    </p>

     <!-- SIGNATORIES -->
    <table class="no-border" style="margin-top:20px;">
        <tr>
            <td style="width:50%;text-align:left;padding-left:20px;">
                <span style="font-size:8px;">Verified by: </span>
                <strong style="font-size:9px;">TRINA M. VILLAMERO</strong>
                <br>
                <span style="font-size:8px;padding-left:65px;">Registrar</span>
            </td>
            <td style="width:50%;text-align:right;padding-right:20px;">
                <span style="font-size:8px;">Approved by: </span>
                <strong style="font-size:9px;">CHRISTOPHER M. SANTOS</strong>
                <br>
                <span style="font-size:8px;">TVI President</span>
            </td>
        </tr>
    </table>

    ';
}
?>