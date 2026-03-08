<?php
/**
 * LibraryController - Thư viện sinh viên
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'library.view'
 *   [L2] Controller: $this->requirePermission('library.view')
 *   [L3] Actions: 'library.borrow'
 */
class LibraryController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('library.view');

        // Tự động cập nhật sách quá hạn
        $this->model->updateOverdueBooks();

        $msg   = $this->getFlash('success') ?? '';
        $error = $this->getFlash('error')   ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['borrow_book'])) {
                [$msg, $error] = $this->handleBorrow();
            } elseif (isset($_POST['return_book'])) {
                [$msg, $error] = $this->handleReturn();
            }
        }

        $keyword   = trim($_GET['q'] ?? '');
        $books     = $this->model->searchBooks($keyword);
        $history   = $this->model->getBorrowHistory($this->studentId);

        $activeBorrows = $this->model->countActiveBorrows($this->studentId);
        $totalBorrowed = count($history);
        $overdueCount  = count(array_filter($history, fn($b) => $b['status'] === 'Overdue'));
        $totalFine     = array_sum(array_column($history, 'fine_amount'));

        $this->render('library/index.php', [
            'pageTitle'     => 'Thư viện',
            'books'         => $books,
            'history'       => $history,
            'keyword'       => $keyword,
            'activeBorrows' => $activeBorrows,
            'totalBorrowed' => $totalBorrowed,
            'overdueCount'  => $overdueCount,
            'totalFine'     => $totalFine,
            'msg'           => $msg,
            'error'         => $error,
            // Quyền cho view
            'canBorrow'     => $this->auth->hasPermission('library.borrow'),
        ]);
    }

    private function handleBorrow(): array
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('library.borrow')) {
            return ['', 'Bạn không có quyền mượn sách.'];
        }

        $bookId  = (int)($_POST['book_id']  ?? 0);
        $dueDays = (int)($_POST['due_days'] ?? 14);
        if ($dueDays < 1 || $dueDays > 30) $dueDays = 14;

        if ($bookId <= 0) return ['', 'Sách không hợp lệ.'];

        $activeBorrows = $this->model->countActiveBorrows($this->studentId);
        if ($activeBorrows >= 3)
            return ['', 'Bạn đang mượn tối đa 3 quyển sách. Vui lòng trả sách trước khi mượn thêm.'];
        if ($this->model->isBookBorrowedByStudent($this->studentId, $bookId))
            return ['', 'Bạn đang mượn cuốn sách này rồi.'];

        $book = $this->model->getBookById($bookId);
        if (!$book) return ['', 'Sách không tồn tại hoặc đã hết bản sao.'];

        if ($this->model->borrowBook($this->studentId, $bookId, $dueDays)) {
            return ['Mượn sách "' . htmlspecialchars($book['title']) . '" thành công! Hạn trả: '
                . date('d/m/Y', strtotime("+{$dueDays} days")), ''];
        }
        return ['', 'Lỗi khi mượn sách: ' . $this->conn->error];
    }

    private function handleReturn(): array
    {
        $borrowId = (int)($_POST['borrow_id'] ?? 0);

        if ($this->model->returnBook($borrowId, $this->studentId)) {
            return ['Trả sách thành công!', ''];
        }
        return ['', 'Không thể trả sách này. Vui lòng kiểm tra lại.'];
    }
}
