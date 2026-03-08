<?php
/**
 * Librarian API Router
 * URL: /web_QLSV/librarian/api/router.php?resource=xxx&action=yyy
 *
 * Resources: books, borrows, members
 */

if (session_status() === PHP_SESSION_NONE) session_start();

$base = dirname(__DIR__, 2);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';
require_once $base . '/includes/auth_check.php';
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../models/LibrarianModel.php';

header('Content-Type: application/json; charset=UTF-8');
AppRouter::guardModule(['librarian']);

$resource = strtolower(trim($_GET['resource'] ?? ''));
$action   = strtolower(trim($_GET['action']   ?? ''));
$method   = $_SERVER['REQUEST_METHOD'];

$model = new LibrarianModel($conn);

// RBAC: kiểm tra quyền cho endpoint này
require_once __DIR__ . '/../../core/RBACMiddleware.php';
require_once __DIR__ . '/../../admin/libs/Auth.php';
$auth = new Auth($conn);
RBACMiddleware::check($conn, $auth, 'librarian', $resource, $action);

function libOk(array $data = [], string $msg = 'OK'): void {
    echo json_encode(['success' => true, 'message' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function libFail(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─────────────────────────────────────────
// BOOKS
// ─────────────────────────────────────────
if ($resource === 'books') {
    switch ($action) {
        case 'list':
            $search   = trim($_GET['search'] ?? '');
            $category = trim($_GET['category'] ?? '');
            libOk($model->getAllBooks($search, $category));

        case 'create':
            if ($method !== 'POST') libFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $data = [
                'title'           => trim($input['title'] ?? ''),
                'author'          => trim($input['author'] ?? '') ?: null,
                'isbn'            => trim($input['isbn'] ?? '') ?: null,
                'category'        => trim($input['category'] ?? '') ?: null,
                'publisher'       => trim($input['publisher'] ?? '') ?: null,
                'published_year'  => trim($input['published_year'] ?? '') ?: null,
                'total_copies'    => (int)($input['total_copies'] ?? 1),
                'available_copies'=> (int)($input['available_copies'] ?? 1),
                'description'     => trim($input['description'] ?? '') ?: null,
            ];
            if (!$data['title']) libFail('Tên sách không được rỗng.');
            if ($model->createBook($data)) libOk([], 'Thêm sách thành công!');
            libFail('Lỗi khi thêm sách.');

        case 'update':
            if ($method !== 'POST') libFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['book_id'] ?? 0);
            $data = [
                'title'           => trim($input['title'] ?? ''),
                'author'          => trim($input['author'] ?? '') ?: null,
                'isbn'            => trim($input['isbn'] ?? '') ?: null,
                'category'        => trim($input['category'] ?? '') ?: null,
                'publisher'       => trim($input['publisher'] ?? '') ?: null,
                'published_year'  => trim($input['published_year'] ?? '') ?: null,
                'total_copies'    => (int)($input['total_copies'] ?? 1),
                'available_copies'=> (int)($input['available_copies'] ?? 1),
                'description'     => trim($input['description'] ?? '') ?: null,
            ];
            if ($model->updateBook($id, $data)) libOk([], 'Cập nhật thành công!');
            libFail('Lỗi cập nhật.');

        case 'delete':
            if ($method !== 'POST') libFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['book_id'] ?? 0);
            if ($model->deleteBook($id)) libOk([], 'Xóa thành công!');
            libFail('Không thể xóa sách đang được mượn.');

        default:
            libFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// BORROWS
// ─────────────────────────────────────────
if ($resource === 'borrows') {
    switch ($action) {
        case 'list':
            $status = trim($_GET['status'] ?? '');
            $search = trim($_GET['search'] ?? '');
            libOk($model->getAllBorrows($status, $search));

        case 'borrow':
            if ($method !== 'POST') libFail('Method không hợp lệ.', 405);
            $input     = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $studentId = (int)($input['student_id'] ?? 0);
            $bookId    = (int)($input['book_id']    ?? 0);
            $dueDays   = (int)($input['due_days']   ?? 14);
            if (!$studentId || !$bookId) libFail('Thiếu thông tin.');
            if ($model->createBorrow($studentId, $bookId, $dueDays)) libOk([], 'Cho mượn thành công!');
            libFail('Sách không còn bản sao khả dụng.');

        case 'return':
            if ($method !== 'POST') libFail('Method không hợp lệ.', 405);
            $input    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $borrowId = (int)($input['borrow_id'] ?? 0);
            $fine     = (float)($input['fine_amount'] ?? 0);
            if ($model->returnBook($borrowId, $fine)) libOk([], 'Trả sách thành công!');
            libFail('Lỗi khi trả sách.');

        case 'mark_lost':
            if ($method !== 'POST') libFail('Method không hợp lệ.', 405);
            $input    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $borrowId = (int)($input['borrow_id'] ?? 0);
            if ($model->markLost($borrowId)) libOk([], 'Đã đánh dấu mất sách.');
            libFail('Lỗi cập nhật.');

        default:
            libFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// MEMBERS
// ─────────────────────────────────────────
if ($resource === 'members') {
    switch ($action) {
        case 'list':
            libOk($model->getActiveMembers());

        case 'search':
            $keyword = trim($_GET['q'] ?? '');
            if (strlen($keyword) < 2) libFail('Từ khóa quá ngắn.');
            libOk($model->searchStudents($keyword));

        case 'history':
            $studentId = (int)($_GET['student_id'] ?? 0);
            libOk($model->getMemberBorrowHistory($studentId));

        default:
            libFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// STATS
// ─────────────────────────────────────────
if ($resource === 'stats') {
    libOk($model->getDashboardStats());
}

libFail("Resource '{$resource}' không tồn tại.", 404);
