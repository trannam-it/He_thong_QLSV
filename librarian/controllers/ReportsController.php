<?php
class LibrarianReportsController extends BaseLibrarianController
{
    public function index(): void
    {
        $stats         = $this->model->getDashboardStats();
        $topBooks      = $this->model->getTopBorrowedBooks(10);
        $monthlyStats  = $this->model->getMonthlyStats();
        $categoryStats = $this->model->getCategoryStats();
        $totalFines    = $this->model->getTotalFines();

        $this->render('reports/index.php', [
            'stats'         => $stats,
            'topBooks'      => $topBooks,
            'monthlyStats'  => $monthlyStats,
            'categoryStats' => $categoryStats,
            'totalFines'    => $totalFines,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
