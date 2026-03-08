<?php
/**
 * GradeController - Kết quả học tập sinh viên
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'grades.view'
 *   [L2] Controller: $this->requirePermission('grades.view')
 */
class GradeController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('grades.view');

        $allRows = $this->model->getAllGrades($this->studentId);

        $totalEnrolled  = count($allRows);
        $totalCompleted = 0;
        $totalFailed    = 0;
        $totalCredits   = 0;
        $scoreSum       = 0;
        $scoreCount     = 0;
        $gradeDist      = ['A+'=>0,'A'=>0,'B+'=>0,'B'=>0,'C+'=>0,'C'=>0,'D+'=>0,'D'=>0,'F'=>0];
        $bySemester     = [];

        foreach ($allRows as $row) {
            $semKey = $row['year'] . '_' . $row['semester'];
            if (!isset($bySemester[$semKey])) {
                $bySemester[$semKey] = [
                    'year'     => $row['year'],
                    'semester' => $row['semester'],
                    'rows'     => [],
                    'credits'  => 0,
                    'scoreSum' => 0,
                    'scoreN'   => 0,
                    'gpa'      => null,
                ];
            }
            $bySemester[$semKey]['rows'][] = $row;

            if ($row['enroll_status'] === 'Completed' && $row['score'] !== null) {
                $totalCompleted++;
                $totalCredits += $row['credit_hours'];
                $scoreSum     += $row['score'];
                $scoreCount++;
                $bySemester[$semKey]['scoreSum'] += $row['score'];
                $bySemester[$semKey]['scoreN']++;
                $bySemester[$semKey]['credits']  += $row['credit_hours'];
                $gl = $row['grade_letter'] ?? '';
                if (isset($gradeDist[$gl])) $gradeDist[$gl]++;
            }
            if ($row['enroll_status'] === 'Failed') $totalFailed++;
        }

        foreach ($bySemester as &$sem) {
            $sem['gpa'] = $sem['scoreN'] > 0 ? round($sem['scoreSum'] / $sem['scoreN'], 2) : null;
        }
        unset($sem);

        $overallGPA = $scoreCount > 0 ? round($scoreSum / $scoreCount, 2) : null;

        $chartLabels = [];
        $chartScores = [];
        foreach (array_reverse($bySemester) as $sd) {
            $label = match($sd['semester']) {
                'Spring' => 'HK1', 'Summer' => 'HKHè', 'Fall' => 'HK2', default => $sd['semester']
            } . ' ' . $sd['year'];
            $chartLabels[] = $label;
            $chartScores[] = $sd['gpa'] ?? 0;
        }

        $filterSem    = $_GET['semester'] ?? '';
        $filterYear   = (int)($_GET['year']   ?? 0);
        $filterStatus = $_GET['status']  ?? '';
        $displayRows  = $allRows;
        if ($filterSem)    $displayRows = array_filter($displayRows, fn($r) => $r['semester'] === $filterSem);
        if ($filterYear)   $displayRows = array_filter($displayRows, fn($r) => (int)$r['year'] === $filterYear);
        if ($filterStatus) $displayRows = array_filter($displayRows, fn($r) => $r['enroll_status'] === $filterStatus);
        $years = array_unique(array_column($allRows, 'year'));
        rsort($years);

        $this->render('grades/index.php', [
            'pageTitle'     => 'Kết quả học tập',
            'allRows'       => $allRows,
            'displayRows'   => $displayRows,
            'bySemester'    => $bySemester,
            'totalEnrolled' => $totalEnrolled,
            'totalCompleted'=> $totalCompleted,
            'totalFailed'   => $totalFailed,
            'totalCredits'  => $totalCredits,
            'overallGPA'    => $overallGPA,
            'gradeDist'     => $gradeDist,
            'chartLabels'   => $chartLabels,
            'chartScores'   => $chartScores,
            'filterSem'     => $filterSem,
            'filterYear'    => $filterYear,
            'filterStatus'  => $filterStatus,
            'years'         => $years,
        ]);
    }
}
