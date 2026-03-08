<?php
/**
 * DashboardController - Dashboard tổng quan sinh viên
 */
class DashboardController extends BaseStudentController
{
    public function index(): void
    {
        $gpaData  = $this->model->calculateGPA($this->studentId);
        $schedule = $this->model->getDashboardSchedule($this->studentId, 5);
        $grades   = $this->model->getRecentGrades($this->studentId, 10);

        $this->render('dashboard/index.php', [
            'gpaData'  => $gpaData,
            'schedule' => $schedule,
            'grades'   => $grades,
            'success'  => $this->getFlash('success'),
            'error'    => $this->getFlash('error'),
        ]);
    }
}
