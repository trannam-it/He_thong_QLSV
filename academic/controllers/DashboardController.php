<?php
/**
 * AcademicDashboardController - Dashboard Quản lý Đào tạo
 */
class AcademicDashboardController extends BaseAcademicController
{
    public function index(): void
    {
        $stats         = $this->model->getDashboardStats();
        $recentClasses = $this->model->getRecentClasses(5);
        $currentSem    = $this->model->getCurrentSemester();

        $this->render('dashboard/index.php', [
            'stats'         => $stats,
            'recentClasses' => $recentClasses,
            'currentSem'    => $currentSem,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
