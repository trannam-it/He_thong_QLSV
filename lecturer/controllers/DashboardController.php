<?php
/**
 * DashboardController - Trang chủ Dashboard Giảng viên
 */

class DashboardController extends BaseLecturerController
{
    public function index(): void
    {
        $lid = $this->lecturerId;

        // Thống kê
        $totalClasses    = $this->model->countCurrentYearClasses($lid);
        $totalStudents   = $this->model->countCurrentStudents($lid);
        $totalSubjects   = $this->model->countDistinctSubjects($lid);
        $totalClassesAll = $this->model->countAllClasses($lid);
        $pendingGrades   = $this->model->countPendingGrades($lid);

        // Danh sách lớp mới nhất
        $classes = $this->model->getRecentClasses($lid, 20);

        // Flash messages
        $flashSuccess = $this->getFlash('success');
        $flashError   = $this->getFlash('error');
        $lecturer = $this->lecturerInfo;

        $this->render('dashboard/index.php', compact(
            'totalClasses', 'totalStudents', 'totalSubjects',
            'totalClassesAll', 'pendingGrades', 'classes',
            'flashSuccess', 'flashError', 'lecturer'
        ));
    }
}
