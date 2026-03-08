<?php
/**
 * AcademicSemestersController - Quản lý Học kỳ
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'semesters.view'
 *   [L2] Controller: $this->requirePermission('semesters.view')
 *   [L3] Edit: 'semesters.edit'
 */
class AcademicSemestersController extends BaseAcademicController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('semesters.view');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                // [LAYER 3]
                $this->requirePermission('semesters.edit');
                $data = [
                    'semester_name' => trim($_POST['semester_name'] ?? ''),
                    'semester'      => $_POST['semester'] ?? 'Spring',
                    'year'          => (int)($_POST['year'] ?? date('Y')),
                    'start_date'    => $_POST['start_date'] ?? '',
                    'end_date'      => $_POST['end_date']   ?? '',
                    'is_current'    => isset($_POST['is_current']) ? 1 : 0,
                ];
                if ($this->model->createSemester($data)) {
                    $this->redirectWithMessage(AcademicRouter::url('semesters'), 'success', 'Thêm học kỳ thành công!');
                } else {
                    $this->redirectWithMessage(AcademicRouter::url('semesters'), 'error', 'Lỗi khi thêm học kỳ.');
                }
            }

            if ($action === 'update') {
                // [LAYER 3]
                $this->requirePermission('semesters.edit');
                $id   = (int)($_POST['semester_id'] ?? 0);
                $data = [
                    'semester_name' => trim($_POST['semester_name'] ?? ''),
                    'semester'      => $_POST['semester'] ?? 'Spring',
                    'year'          => (int)($_POST['year'] ?? date('Y')),
                    'start_date'    => $_POST['start_date'] ?? '',
                    'end_date'      => $_POST['end_date']   ?? '',
                    'is_current'    => isset($_POST['is_current']) ? 1 : 0,
                ];
                if ($this->model->updateSemester($id, $data)) {
                    $this->redirectWithMessage(AcademicRouter::url('semesters'), 'success', 'Cập nhật học kỳ thành công!');
                } else {
                    $this->redirectWithMessage(AcademicRouter::url('semesters'), 'error', 'Lỗi khi cập nhật học kỳ.');
                }
            }

            if ($action === 'set_current') {
                // [LAYER 3]
                $this->requirePermission('semesters.edit');
                $id = (int)($_POST['semester_id'] ?? 0);
                if ($this->model->setCurrentSemester($id)) {
                    $this->redirectWithMessage(AcademicRouter::url('semesters'), 'success', 'Đã đặt làm học kỳ hiện tại!');
                } else {
                    $this->redirectWithMessage(AcademicRouter::url('semesters'), 'error', 'Lỗi khi cập nhật.');
                }
            }
        }

        $semesters   = $this->model->getAllSemesters();
        $editSemester = null;
        if (!empty($_GET['edit'])) {
            $editSemester = $this->model->getSemesterById((int)$_GET['edit']);
        }

        $this->render('semesters/index.php', [
            'pageTitle'    => 'Quản lý Học kỳ',
            'semesters'    => $semesters,
            'editSemester' => $editSemester,
            'success'      => $this->getFlash('success'),
            'error'        => $this->getFlash('error'),
            // Quyền cho view
            'canEdit'      => $this->auth->hasPermission('semesters.edit'),
        ]);
    }
}
