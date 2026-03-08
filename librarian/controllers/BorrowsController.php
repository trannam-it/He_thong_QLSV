<?php
class LibrarianBorrowsController extends BaseLibrarianController
{
    public function index(): void
    {
        $this->model->updateOverdueStatuses();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'borrow') {
                $studentId = (int)($_POST['student_id'] ?? 0);
                $bookId    = (int)($_POST['book_id'] ?? 0);
                $dueDays   = (int)($_POST['due_days'] ?? 14);
                if ($this->model->createBorrow($studentId, $bookId, $dueDays)) {
                    $this->redirectWithMessage(LibrarianRouter::url('borrows'), 'success', 'Cho mượn sách thành công!');
                } else {
                    $this->redirectWithMessage(LibrarianRouter::url('borrows'), 'error', 'Lỗi: sách không còn bản sao khả dụng.');
                }
            }

            if ($action === 'return') {
                $borrowId = (int)($_POST['borrow_id'] ?? 0);
                $fine     = (float)str_replace(',', '', $_POST['fine_amount'] ?? '0');
                if ($this->model->returnBook($borrowId, $fine)) {
                    $this->redirectWithMessage(LibrarianRouter::url('borrows'), 'success', 'Trả sách thành công!');
                } else {
                    $this->redirectWithMessage(LibrarianRouter::url('borrows'), 'error', 'Lỗi khi trả sách.');
                }
            }

            if ($action === 'mark_lost') {
                $borrowId = (int)($_POST['borrow_id'] ?? 0);
                if ($this->model->markLost($borrowId)) {
                    $this->redirectWithMessage(LibrarianRouter::url('borrows'), 'success', 'Đánh dấu mất sách thành công.');
                }
            }
        }

        $status  = $_GET['status'] ?? '';
        $search  = trim($_GET['search'] ?? '');
        $borrows = $this->model->getAllBorrows($status, $search);
        $books   = $this->model->getAllBooks();

        $this->render('borrows/index.php', [
            'borrows'      => $borrows,
            'books'        => $books,
            'filterStatus' => $status,
            'search'       => $search,
            'success'      => $this->getFlash('success'),
            'error'        => $this->getFlash('error'),
        ]);
    }
}
