<?php
/**
 * TuitionController - Học phí sinh viên
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'tuition.view'
 *   [L2] Controller: $this->requirePermission('tuition.view')
 *   [L3] Action pay: $this->requirePermission('tuition.pay')
 */
class TuitionController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('tuition.view');

        // Tự động đồng bộ hoá đơn trước khi hiển thị
        $this->syncInvoices();

        $msg   = $this->getFlash('success') ?? '';
        $error = $this->getFlash('error')   ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_invoice'])) {
            [$msg, $error] = $this->handlePayment();
        }

        $invoices = $this->model->getAllTuitionInvoices($this->studentId);

        $totalDue    = array_sum(array_column($invoices, 'amount_due'));
        $totalPaid   = array_sum(array_column($invoices, 'amount_paid'));
        $totalDebt   = $totalDue - $totalPaid;
        $unpaidCount = count(array_filter(
            $invoices,
            fn($i) => in_array($i['status'], ['Unpaid', 'Partial', 'Overdue'])
        ));

        $this->render('tuition/index.php', [
            'pageTitle'   => 'Học phí',
            'invoices'    => $invoices,
            'totalDue'    => $totalDue,
            'totalPaid'   => $totalPaid,
            'totalDebt'   => $totalDebt,
            'unpaidCount' => $unpaidCount,
            'msg'         => $msg,
            'error'       => $error,
            // Quyền cho view
            'canPay'      => $this->auth->hasPermission('tuition.pay'),
        ]);
    }

    private function syncInvoices(): void
    {
        $semRows = $this->model->getEnrolledSemesterCredits($this->studentId);

        foreach ($semRows as $sr) {
            // $sem    = $sr['semester'];
            $sem = $sr['semester_name'];
            $yr     = (int)$sr['year'];
            $cred   = (int)$sr['total_credits'];
            $price  = $this->model->getTuitionPricePerCredit($sem, $yr);
            $amount = $cred * $price;

            $existing = $this->model->getTuitionInvoice($this->studentId, $sem, $yr);

            if (!$existing) {
                $this->model->createTuitionInvoice($this->studentId, $sem, $yr, $cred, $amount);
            } elseif (in_array($existing['status'], ['Unpaid', 'Partial'])) {
                $newStatus = 'Unpaid';
                if ((float)$existing['amount_paid'] >= $amount) {
                    $newStatus = 'Paid';
                } elseif ((float)$existing['amount_paid'] > 0) {
                    $newStatus = 'Partial';
                }
                $this->model->updateTuitionInvoice($existing['invoice_id'], $cred, $amount, $newStatus);
            }
        }
    }

    private function handlePayment(): array
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('tuition.pay')) {
            return ['', 'Bạn không có quyền nộp học phí.'];
        }

        $invId  = (int)($_POST['invoice_id'] ?? 0);
        $amount = (float)str_replace(',', '', $_POST['amount'] ?? '0');

        if ($amount <= 0) {
            return ['', 'Số tiền không hợp lệ.'];
        }

        $inv = $this->model->getTuitionInvoiceById($invId, $this->studentId);
        if (!$inv) {
            return ['', 'Hoá đơn không tồn tại.'];
        }
        if (in_array($inv['status'], ['Paid', 'Exempted'])) {
            return ['', 'Hoá đơn này đã được thanh toán/miễn giảm.'];
        }

        if ($this->model->payTuition($invId, (float)$inv['amount_paid'], (float)$inv['amount_due'], $amount)) {
            $formatted = number_format($amount, 0, ',', '.');
            return ["Nộp học phí thành công! Đã nộp: {$formatted} VNĐ", ''];
        }

        return ['', 'Lỗi khi cập nhật: ' . $this->conn->error];
    }
}
