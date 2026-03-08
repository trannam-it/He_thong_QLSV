<?php
/**
 * AcademicSubjectsController - Quản lý Học phần
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'subjects.view'
 *   [L2] Controller: $this->requirePermission('subjects.view')
 *   [L3] Create: 'subjects.create' | Update: 'subjects.edit' | Delete: 'subjects.delete'
 */
class AcademicSubjectsController extends BaseAcademicController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('subjects.view');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                // [LAYER 3]
                $this->requirePermission('subjects.create');
                $data = [
                    'subject_code'    => trim($_POST['subject_code'] ?? ''),
                    'subject_name'    => trim($_POST['subject_name'] ?? ''),
                    'credits'         => (int)($_POST['credits'] ?? 3),
                    'faculty_id'      => (int)($_POST['faculty_id'] ?? 0) ?: null,
                    'description'     => trim($_POST['description'] ?? ''),
                    'prerequisite_id' => (int)($_POST['prerequisite_id'] ?? 0) ?: null,
                ];
                if ($this->model->createSubject($data)) {
                    $this->redirectWithMessage(AcademicRouter::url('subjects'), 'success', 'Thêm học phần thành công!');
                } else {
                    $this->redirectWithMessage(AcademicRouter::url('subjects'), 'error', 'Lỗi khi thêm học phần.');
                }
            }

            if ($action === 'update') {
                // [LAYER 3]
                $this->requirePermission('subjects.edit');
                $id   = (int)($_POST['subject_id'] ?? 0);
                $data = [
                    'subject_code'    => trim($_POST['subject_code'] ?? ''),
                    'subject_name'    => trim($_POST['subject_name'] ?? ''),
                    'credits'         => (int)($_POST['credits'] ?? 3),
                    'faculty_id'      => (int)($_POST['faculty_id'] ?? 0) ?: null,
                    'description'     => trim($_POST['description'] ?? ''),
                    'prerequisite_id' => (int)($_POST['prerequisite_id'] ?? 0) ?: null,
                ];
                if ($this->model->updateSubject($id, $data)) {
                    $this->redirectWithMessage(AcademicRouter::url('subjects'), 'success', 'Cập nhật học phần thành công!');
                } else {
                    $this->redirectWithMessage(AcademicRouter::url('subjects'), 'error', 'Lỗi khi cập nhật.');
                }
            }

            if ($action === 'delete') {
                // [LAYER 3]
                $this->requirePermission('subjects.delete');
                $id = (int)($_POST['subject_id'] ?? 0);
                if ($this->model->deleteSubject($id)) {
                    $this->redirectWithMessage(AcademicRouter::url('subjects'), 'success', 'Xóa học phần thành công!');
                } else {
                    $this->redirectWithMessage(AcademicRouter::url('subjects'), 'error', 'Không thể xóa học phần (có thể đang được sử dụng).');
                }
            }
        }

        $search      = trim($_GET['search'] ?? '');
        $subjects    = $this->model->getAllSubjects($search);
        $faculties   = $this->model->getAllFaculties();
        $allSubjects = $this->model->getAllSubjects();

        $editSubject = null;
        if (!empty($_GET['edit'])) {
            $editSubject = $this->model->getSubjectById((int)$_GET['edit']);
        }

        $this->render('subjects/index.php', [
            'pageTitle'   => 'Quản lý Học phần',
            'subjects'    => $subjects,
            'faculties'   => $faculties,
            'allSubjects' => $allSubjects,
            'search'      => $search,
            'editSubject' => $editSubject,
            'success'     => $this->getFlash('success'),
            'error'       => $this->getFlash('error'),
            // Quyền cho view
            'canCreate'   => $this->auth->hasPermission('subjects.create'),
            'canEdit'     => $this->auth->hasPermission('subjects.edit'),
            'canDelete'   => $this->auth->hasPermission('subjects.delete'),
        ]);
    }
}
