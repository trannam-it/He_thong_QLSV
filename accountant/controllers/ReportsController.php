<?php
class AccountantReportsController extends BaseAccountantController
{
    public function index(): void
    {
        $stats             = $this->model->getDashboardStats();
        $tuitionReport     = $this->model->getTuitionReportBySemester();
        $scholarshipReport = $this->model->getScholarshipFinancialSummary();

        $this->render('reports/index.php', [
            'stats'             => $stats,
            'tuitionReport'     => $tuitionReport,
            'scholarshipReport' => $scholarshipReport,
            'success'           => $this->getFlash('success'),
            'error'             => $this->getFlash('error'),
        ]);
    }
}
