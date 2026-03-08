<?php
class LibrarianBooksController extends BaseLibrarianController
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $data = [
                    'title'           => trim($_POST['title'] ?? ''),
                    'author'          => trim($_POST['author'] ?? ''),
                    'isbn'            => trim($_POST['isbn'] ?? '') ?: null,
                    'category'        => trim($_POST['category'] ?? '') ?: null,
                    'publisher'       => trim($_POST['publisher'] ?? '') ?: null,
                    'published_year'  => trim($_POST['published_year'] ?? '') ?: null,
                    'total_copies'    => (int)($_POST['total_copies'] ?? 1),
                    'available_copies'=> (int)($_POST['available_copies'] ?? 1),
                    'description'     => trim($_POST['description'] ?? '') ?: null,
                ];
                if ($this->model->createBook($data)) {
                    $this->redirectWithMessage(LibrarianRouter::url('books'), 'success', 'Thêm sách thành công!');
                } else {
                    $this->redirectWithMessage(LibrarianRouter::url('books'), 'error', 'Lỗi khi thêm sách.');
                }
            }

            if ($action === 'update') {
                $id   = (int)($_POST['book_id'] ?? 0);
                $data = [
                    'title'           => trim($_POST['title'] ?? ''),
                    'author'          => trim($_POST['author'] ?? ''),
                    'isbn'            => trim($_POST['isbn'] ?? '') ?: null,
                    'category'        => trim($_POST['category'] ?? '') ?: null,
                    'publisher'       => trim($_POST['publisher'] ?? '') ?: null,
                    'published_year'  => trim($_POST['published_year'] ?? '') ?: null,
                    'total_copies'    => (int)($_POST['total_copies'] ?? 1),
                    'available_copies'=> (int)($_POST['available_copies'] ?? 1),
                    'description'     => trim($_POST['description'] ?? '') ?: null,
                ];
                if ($this->model->updateBook($id, $data)) {
                    $this->redirectWithMessage(LibrarianRouter::url('books'), 'success', 'Cập nhật sách thành công!');
                } else {
                    $this->redirectWithMessage(LibrarianRouter::url('books'), 'error', 'Lỗi cập nhật.');
                }
            }

            if ($action === 'delete') {
                $id = (int)($_POST['book_id'] ?? 0);
                if ($this->model->deleteBook($id)) {
                    $this->redirectWithMessage(LibrarianRouter::url('books'), 'success', 'Xóa sách thành công!');
                } else {
                    $this->redirectWithMessage(LibrarianRouter::url('books'), 'error', 'Không thể xóa sách đang được mượn.');
                }
            }
        }

        $search   = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $books     = $this->model->getAllBooks($search, $category);
        $categories = $this->model->getAllCategories();

        $editBook = null;
        $bookHistory = [];
        if (!empty($_GET['edit'])) {
            $editBook = $this->model->getBookById((int)$_GET['edit']);
        }
        if (!empty($_GET['history'])) {
            $bookHistory = $this->model->getBookBorrowHistory((int)$_GET['history']);
            $editBook    = $this->model->getBookById((int)$_GET['history']);
        }

        $this->render('books/index.php', [
            'books'       => $books,
            'categories'  => $categories,
            'search'      => $search,
            'category'    => $category,
            'editBook'    => $editBook,
            'bookHistory' => $bookHistory,
            'showHistory' => !empty($_GET['history']),
            'success'     => $this->getFlash('success'),
            'error'       => $this->getFlash('error'),
        ]);
    }
}
