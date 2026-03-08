<?php
/**
 * AcademicGradesController - Xem và quản lý điểm
 */
class AcademicGradesController extends BaseAcademicController
{
    public function index(): void
    {
        $semesterId = (int)($_GET['semester_id'] ?? 0);
        $classId    = (int)($_GET['class_id'] ?? 0);

        $semesters = $this->model->getAllSemesters();
        $classes   = $this->model->getAllClasses('', $semesterId);
        $grades    = [];
        $gradeStats = [];
        $selectedClass = null;

        if ($classId > 0) {
            $grades        = $this->model->getGradesByClass($classId);
            $gradeStats    = $this->model->getGradeStatsByClass($classId);
            $selectedClass = $this->model->getClassById($classId);
        }

        $this->render('grades/index.php', [
            'semesters'     => $semesters,
            'classes'       => $classes,
            'grades'        => $grades,
            'gradeStats'    => $gradeStats,
            'selectedClass' => $selectedClass,
            'semesterId'    => $semesterId,
            'classId'       => $classId,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
