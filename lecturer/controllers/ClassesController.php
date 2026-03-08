<?php
/**
 * ClassesController - Danh sách lớp đang dạy
 */

class ClassesController extends BaseLecturerController
{
    public function index(): void
    {
        $lid = $this->lecturerId;

        // Filters
        $search     = trim($_GET['search']   ?? '');
        $filterSem  = $_GET['semester']       ?? '';
        $filterYear = (int)($_GET['year']     ?? 0);

        // Dữ liệu
        $classes  = $this->model->getClasses($lid, $search, $filterSem, $filterYear);
        $stats    = $this->model->getClassesStats($lid);
        $yearList = $this->model->getYearList($lid);

        // Flash
        $flashSuccess = $this->getFlash('success');
        $flashError   = $this->getFlash('error');

        $this->render('classes/index.php', compact(
            'classes', 'stats', 'yearList',
            'search', 'filterSem', 'filterYear',
            'flashSuccess', 'flashError'
        ));
    }
}
