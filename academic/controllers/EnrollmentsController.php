<?php
/**
 * AcademicEnrollmentsController - Quản lý Đăng ký học phần
 */
class AcademicEnrollmentsController extends BaseAcademicController
{
    public function index(): void
    {
        $semesterId = (int)($_GET['semester_id'] ?? 0);
        $status     = $_GET['status'] ?? '';

        $enrollments = $this->model->getAllEnrollments($semesterId, $status);
        $semesters   = $this->model->getAllSemesters();

        $this->render('enrollments/index.php', [
            'enrollments' => $enrollments,
            'semesters'   => $semesters,
            'semesterId'  => $semesterId,
            'filterStatus'=> $status,
            'success'     => $this->getFlash('success'),
            'error'       => $this->getFlash('error'),
        ]);
    }
}
