<?php
class LibrarianDashboardController extends BaseLibrarianController
{
    public function index(): void
    {
        $this->model->updateOverdueStatuses();
        $stats        = $this->model->getDashboardStats();
        $recentBorrows = $this->model->getRecentBorrows(8);

        $this->render('dashboard/index.php', [
            'stats'         => $stats,
            'recentBorrows' => $recentBorrows,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
