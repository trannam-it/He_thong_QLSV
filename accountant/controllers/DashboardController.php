<?php
class AccountantDashboardController extends BaseAccountantController
{
    public function index(): void
    {
        $stats          = $this->model->getDashboardStats();
        $recentInvoices = $this->model->getRecentInvoices(10);
        $this->render('dashboard/index.php', [
            'stats'          => $stats,
            'recentInvoices' => $recentInvoices,
            'success'        => $this->getFlash('success'),
            'error'          => $this->getFlash('error'),
        ]);
    }
}
