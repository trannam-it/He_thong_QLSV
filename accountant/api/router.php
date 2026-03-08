<?php
/**
 * Accountant API Router
 * URL: /web_QLSV/accountant/api/router.php?resource=xxx&action=yyy
 */

if (session_status() === PHP_SESSION_NONE) session_start();

$base = dirname(__DIR__, 2);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';
require_once $base . '/includes/auth_check.php';
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../models/AccountantModel.php';

header('Content-Type: application/json; charset=UTF-8');
AppRouter::guardModule(['accountant']);

$resource = strtolower(trim($_GET['resource'] ?? ''));
$action   = strtolower(trim($_GET['action']   ?? ''));
$method   = $_SERVER['REQUEST_METHOD'];

$model = new AccountantModel($conn);

// RBAC: kiểm tra quyền cho endpoint này
require_once __DIR__ . '/../../core/RBACMiddleware.php';
require_once __DIR__ . '/../../admin/libs/Auth.php';
$auth = new Auth($conn);
RBACMiddleware::check($conn, $auth, 'accountant', $resource, $action);

function accOk(array $data = [], string $msg = 'OK'): void {
    echo json_encode(['success' => true, 'message' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function accFail(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─────────────────────────────────────────
// TUITION
// ─────────────────────────────────────────
if ($resource === 'tuition') {
    switch ($action) {
        case 'list':
            $status   = trim($_GET['status']   ?? '');
            $search   = trim($_GET['search']   ?? '');
            $semester = trim($_GET['semester'] ?? '');
            $year     = (int)($_GET['year'] ?? 0);
            accOk($model->getAllInvoices($status, $search, $semester, $year));

        case 'pay':
            if ($method !== 'POST') accFail('Method không hợp lệ.', 405);
            $input     = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $invoiceId = (int)($input['invoice_id'] ?? 0);
            $amount    = (float)($input['amount'] ?? 0);
            if ($amount <= 0) accFail('Số tiền phải lớn hơn 0.');
            if ($model->recordPayment($invoiceId, $amount)) accOk([], 'Ghi nhận thanh toán thành công!');
            accFail('Lỗi ghi nhận.');

        case 'update_status':
            if ($method !== 'POST') accFail('Method không hợp lệ.', 405);
            $input     = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $invoiceId = (int)($input['invoice_id'] ?? 0);
            $status    = $input['status'] ?? '';
            $note      = trim($input['note'] ?? '');
            if ($model->updateInvoiceStatus($invoiceId, $status, $note)) accOk([], 'Cập nhật thành công!');
            accFail('Lỗi cập nhật.');

        case 'settings':
            accOk($model->getTuitionSettings());

        default:
            accFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// SCHOLARSHIPS
// ─────────────────────────────────────────
if ($resource === 'scholarships') {
    switch ($action) {
        case 'list':
            accOk($model->getAllScholarships());

        case 'applications':
            $scholarshipId = (int)($_GET['scholarship_id'] ?? 0);
            $status        = trim($_GET['status'] ?? '');
            accOk($model->getScholarshipApplications($scholarshipId, $status));

        case 'review':
            if ($method !== 'POST') accFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $appId  = (int)($input['application_id'] ?? 0);
            $status = $input['status'] ?? 'Pending';
            $note   = trim($input['note'] ?? '');
            if ($model->reviewApplication($appId, $status, $note)) accOk([], 'Duyệt đơn thành công!');
            accFail('Lỗi duyệt đơn.');

        default:
            accFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// STUDENTS
// ─────────────────────────────────────────
if ($resource === 'students') {
    switch ($action) {
        case 'list':
            $search = trim($_GET['search'] ?? '');
            $status = trim($_GET['status'] ?? '');
            accOk($model->getStudentsWithTuition($search, $status));

        case 'invoices':
            $studentId = (int)($_GET['student_id'] ?? 0);
            accOk($model->getStudentInvoices($studentId));

        default:
            accFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// REPORTS
// ─────────────────────────────────────────
if ($resource === 'reports') {
    switch ($action) {
        case 'tuition_by_semester':
            accOk($model->getTuitionReportBySemester());

        case 'scholarship_summary':
            accOk($model->getScholarshipFinancialSummary());

        case 'dashboard':
            accOk($model->getDashboardStats());

        default:
            accFail("Action '{$action}' không tồn tại.");
    }
}

accFail("Resource '{$resource}' không tồn tại.", 404);
