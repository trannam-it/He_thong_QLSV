<?php
/**
 * AccountantTuitionController - Quản lý Học phí (Kế toán)
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'tuition.view'
 *   [L2] Controller: $this->requirePermission('tuition.view')
 *   [L3] Pay: 'tuition.pay' | Edit: 'tuition.edit'
 */
class AccountantTuitionController extends BaseAccountantController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('tuition.view');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'record_payment') {
                // [LAYER 3]
                $this->requirePermission('tuition.pay');
                $invoiceId = (int)($_POST['invoice_id'] ?? 0);
                $amount    = (float)str_replace(',', '', $_POST['amount'] ?? '0');
                if ($amount <= 0) {
                    $this->redirectWithMessage(AccountantRouter::url('tuition'), 'error', 'Số tiền phải lớn hơn 0.');
                }
                if ($this->model->recordPayment($invoiceId, $amount)) {
                    $this->redirectWithMessage(AccountantRouter::url('tuition'), 'success', 'Ghi nhận thanh toán thành công!');
                }
                $this->redirectWithMessage(AccountantRouter::url('tuition'), 'error', 'Lỗi ghi nhận.');
            }

            if ($action === 'update_status') {
                // [LAYER 3]
                $this->requirePermission('tuition.edit');
                $invoiceId = (int)($_POST['invoice_id'] ?? 0);
                $status    = $_POST['status'] ?? '';
                $note      = trim($_POST['note'] ?? '');
                if ($this->model->updateInvoiceStatus($invoiceId, $status, $note)) {
                    $this->redirectWithMessage(AccountantRouter::url('tuition'), 'success', 'Cập nhật trạng thái thành công!');
                }
                $this->redirectWithMessage(AccountantRouter::url('tuition'), 'error', 'Lỗi cập nhật.');
            }

            if ($action === 'update_price') {
                // [LAYER 3]
                $this->requirePermission('tuition.edit');
                $semester = $_POST['semester'] ?? '';
                $year     = (int)($_POST['year'] ?? date('Y'));
                $price    = (float)str_replace(',', '', $_POST['price'] ?? '0');
                $note     = trim($_POST['note'] ?? '');
                if ($this->model->updateTuitionSetting($semester, $year, $price, $note)) {
                    $this->redirectWithMessage(AccountantRouter::url('tuition'), 'success', 'Cập nhật đơn giá thành công!');
                }
                $this->redirectWithMessage(AccountantRouter::url('tuition'), 'error', 'Lỗi cập nhật đơn giá.');
            }
        }

        $status   = $_GET['status']   ?? '';
        $search   = trim($_GET['search'] ?? '');
        $semester = $_GET['semester'] ?? '';
        $year     = (int)($_GET['year'] ?? 0);

        $invoices = $this->model->getAllInvoices($status, $search, $semester, $year);
        $settings = $this->model->getTuitionSettings();
        $years    = $this->model->getAvailableYears();

        $this->render('tuition/index.php', [
            'pageTitle'    => 'Quản lý Học phí',
            'invoices'     => $invoices,
            'settings'     => $settings,
            'years'        => $years,
            'filterStatus' => $status,
            'search'       => $search,
            'semester'     => $semester,
            'year'         => $year,
            'success'      => $this->getFlash('success'),
            'error'        => $this->getFlash('error'),
            // Quyền cho view
            'canPay'       => $this->auth->hasPermission('tuition.pay'),
            'canEdit'      => $this->auth->hasPermission('tuition.edit'),
        ]);
    }
}
