<?php
/**
 * AcademicStudentsController - Quản lý Sinh viên
 */ 
class AcademicStudentsController extends BaseAcademicController
{
    public function index(): void
    {
        $search    = trim($_GET['search'] ?? '');
        $status    = $_GET['status']     ?? '';
        $facultyId = (int)($_GET['faculty_id'] ?? 0);

        $students  = $this->model->getAllStudents($search, $status, $facultyId);
        $faculties = $this->model->getAllFaculties();

        // Xem chi tiết sinh viên
        $studentDetail = null;
        $studentGrades = [];
        if (!empty($_GET['detail'])) {
            $detailId      = (int)$_GET['detail'];
            $studentDetail = $this->model->getStudentById($detailId);
            if ($studentDetail) {
                $studentGrades = $this->model->getStudentGrades($detailId);
            }
        }

        $this->render('students/index.php', [
            'students'      => $students,
            'faculties'     => $faculties,
            'search'        => $search,
            'filterStatus'  => $status,
            'filterFaculty' => $facultyId,
            'studentDetail' => $studentDetail,
            'studentGrades' => $studentGrades,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
