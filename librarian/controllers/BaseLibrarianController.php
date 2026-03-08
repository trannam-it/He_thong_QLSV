<?php
/**
 * BaseLibrarianController - Controller cơ sở cho vai trò Thủ thư
 */
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dashboard_helper.php';
require_once __DIR__ . '/../../core/AppRouter.php';
require_once __DIR__ . '/../Router.php';

class BaseLibrarianController
{
    protected mysqli         $conn;
    protected LibrarianModel $model;
    protected ?Auth          $auth;       // RBAC Auth instance
    protected array          $user;
    protected int            $userId;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;

        // Load Auth for RBAC
        require_once __DIR__ . '/../../admin/libs/Auth.php';
        $this->auth = new Auth($conn);

        // Xác thực phải là librarian (dùng AppRouter)
        AppRouter::guardModule(['librarian']);

        require_once __DIR__ . '/../models/LibrarianModel.php';
        $this->model = new LibrarianModel($conn);

        $this->userId = (int)$_SESSION['user_id'];
        $this->user   = $this->model->getUserInfo($this->userId);
    }

    protected function render(string $viewPath, array $data = []): void
    {
        $data['user']        = $this->user;
        $data['userId']      = $this->userId;
        $data['currentPage'] = LibrarianRouter::getPageName();

        extract($data);

        $fullPath = __DIR__ . '/../views/' . $viewPath;
        if (!file_exists($fullPath)) {
            $this->abortError("View không tìm thấy: {$viewPath}");
        }

        require __DIR__ . '/../views/layout/header.php';
        require $fullPath;
        require __DIR__ . '/../views/layout/footer.php';
    }

    protected function redirectWithMessage(string $url, string $type, string $message): void
    {
        $_SESSION[$type] = $message;
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function getFlash(string $key): ?string
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

    protected function abortError(string $message): void
    {
        http_response_code(403);
        echo '<div style="font-family:sans-serif;padding:60px;text-align:center;">'
            . '<h2 style="color:#e74c3c;">⚠ Lỗi</h2>'
            . '<p>' . htmlspecialchars($message) . '</p>'
            . '<a href="' . BASE_URL . '/librarian/" style="color:#20c997;">← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
