<?php
/**
 * AccountantScholarshipsController - Quản lý Học bổng (Kế toán)
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'scholarships.view'
 *   [L2] Controller: $this->requirePermission('scholarships.view')
 *   [L3] Edit/Review: 'scholarships.edit'
 */
class AccountantScholarshipsController extends BaseAccountantController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('scholarships.view');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'review') {
                // [LAYER 3]
                $this->requirePermission('scholarships.edit');
                $appId  = (int)($_POST['application_id'] ?? 0);
                $status = $_POST['status'] ?? 'Pending';
                $note   = trim($_POST['note'] ?? '');
                if ($this->model->reviewApplication($appId, $status, $note)) {
                    $this->redirectWithMessage(AccountantRouter::url('scholarships'), 'success', 'Đã duyệt đơn học bổng!');
                }
                $this->redirectWithMessage(AccountantRouter::url('scholarships'), 'error', 'Lỗi duyệt đơn.');
            }

            if ($action === 'create_scholarship') {
                // [LAYER 3]
                $this->requirePermission('scholarships.edit');
                $data = [
                    'name'        => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'value'       => (float)str_replace(',', '', $_POST['value'] ?? '0'),
                    'min_gpa'     => trim($_POST['min_gpa'] ?? '') !== '' ? (float)$_POST['min_gpa'] : null,
                    'max_gpa'     => trim($_POST['max_gpa'] ?? '') !== '' ? (float)$_POST['max_gpa'] : null,
                    'semester'    => $_POST['semester'] ?? 'Spring',
                    'year'        => (int)($_POST['year'] ?? date('Y')),
                    'quantity'    => trim($_POST['quantity'] ?? '') !== '' ? (int)$_POST['quantity'] : null,
                    'deadline'    => trim($_POST['deadline'] ?? '') ?: null,
                    'is_active'   => isset($_POST['is_active']) ? 1 : 0,
                ];
                if ($this->model->createScholarship($data)) {
                    $this->redirectWithMessage(AccountantRouter::url('scholarships'), 'success', 'Tạo học bổng thành công!');
                }
                $this->redirectWithMessage(AccountantRouter::url('scholarships'), 'error', 'Lỗi tạo học bổng.');
            }

            if ($action === 'toggle_active') {
                // [LAYER 3]
                $this->requirePermission('scholarships.edit');
                $id = (int)($_POST['scholarship_id'] ?? 0);
                $this->model->toggleScholarshipActive($id);
                $this->redirectWithMessage(AccountantRouter::url('scholarships'), 'success', 'Cập nhật trạng thái học bổng!');
            }
        }

        $scholarshipId = (int)($_GET['scholarship_id'] ?? 0);
        $appStatus     = $_GET['app_status'] ?? '';
        $scholarships  = $this->model->getAllScholarships();
        $applications  = $this->model->getScholarshipApplications($scholarshipId, $appStatus);

        $this->render('scholarships/index.php', [
            'pageTitle'     => 'Quản lý Học bổng',
            'scholarships'  => $scholarships,
            'applications'  => $applications,
            'scholarshipId' => $scholarshipId,
            'appStatus'     => $appStatus,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
            // Quyền cho view
            'canEdit'       => $this->auth->hasPermission('scholarships.edit'),
        ]);
    }
}
