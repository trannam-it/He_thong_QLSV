<?php
class AccountantStudentsController extends BaseAccountantController
{
    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';
        $students = $this->model->getStudentsWithTuition($search, $status);

        $studentDetail  = null;
        $studentInvoices = [];
        if (!empty($_GET['detail'])) {
            $sid = (int)$_GET['detail'];
            $studentInvoices = $this->model->getStudentInvoices($sid);
            foreach ($students as $sv) {
                if ((int)$sv['student_id'] === $sid) { $studentDetail = $sv; break; }
            }
        }

        $this->render('students/index.php', [
            'students'       => $students,
            'search'         => $search,
            'filterStatus'   => $status,
            'studentDetail'  => $studentDetail,
            'studentInvoices'=> $studentInvoices,
            'success'        => $this->getFlash('success'),
            'error'          => $this->getFlash('error'),
        ]);
    }
}
