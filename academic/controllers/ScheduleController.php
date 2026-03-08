<?php
/**
 * AcademicScheduleController - Thời khóa biểu
 */
class AcademicScheduleController extends BaseAcademicController
{
    public function index(): void
    {
        $semesterId  = (int)($_GET['semester_id'] ?? 0);
        $currentSem  = $this->model->getCurrentSemester();
        if ($semesterId === 0 && $currentSem) {
            $semesterId = (int)$currentSem['semester_id'];
        }

        $schedule  = $this->model->getSchedule($semesterId);
        $semesters = $this->model->getAllSemesters();

        // Group by day_of_week
        $grouped = [];
        foreach ($schedule as $row) {
            $grouped[$row['day_of_week']][] = $row;
        }

        $this->render('schedule/index.php', [
            'schedule'   => $schedule,
            'grouped'    => $grouped,
            'semesters'  => $semesters,
            'semesterId' => $semesterId,
            'currentSem' => $currentSem,
            'success'    => $this->getFlash('success'),
            'error'      => $this->getFlash('error'),
        ]);
    }
}
