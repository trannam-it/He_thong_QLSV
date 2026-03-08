<?php
/**
 * AcademicReportsController - Báo cáo thống kê
 */
class AcademicReportsController extends BaseAcademicController
{
    public function index(): void
    {
        $semesterId  = (int)($_GET['semester_id'] ?? 0);
        $currentSem  = $this->model->getCurrentSemester();
        if ($semesterId === 0 && $currentSem) {
            $semesterId = (int)$currentSem['semester_id'];
        }

        $semesters       = $this->model->getAllSemesters();
        $gradeReport     = $semesterId > 0 ? $this->model->getGradeReportBySemester($semesterId) : [];
        $enrollmentStats = $this->model->getEnrollmentStatsByFaculty();
        $dashboardStats  = $this->model->getDashboardStats();

        $this->render('reports/index.php', [
            'semesters'       => $semesters,
            'semesterId'      => $semesterId,
            'gradeReport'     => $gradeReport,
            'enrollmentStats' => $enrollmentStats,
            'dashboardStats'  => $dashboardStats,
            'currentSem'      => $currentSem,
            'success'         => $this->getFlash('success'),
            'error'           => $this->getFlash('error'),
        ]);
    }
}
