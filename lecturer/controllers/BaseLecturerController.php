<?php
/**
 * BaseLecturerController - Controller cơ sở cho vai trò Giảng viên
 */

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dashboard_helper.php';
require_once __DIR__ . '/../../core/AppRouter.php';
require_once __DIR__ . '/../Router.php';

class BaseLecturerController
{
    protected mysqli        $conn;
    protected LecturerModel $model;
    protected ?Auth         $auth;       // RBAC Auth instance
    protected array         $lecturer;   // thông tin giảng viên hiện tại
    protected int           $lecturerId;
    protected int           $userId;
    protected $lecturerInfo = [];

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;

        // Load Auth for RBAC
        require_once __DIR__ . '/../../admin/libs/Auth.php';
        $this->auth = new Auth($conn);

        // Xác thực phải là teacher (dùng AppRouter)
        AppRouter::guardModule(['teacher']);

        // Load model
        require_once __DIR__ . '/../models/LecturerModel.php';
        $this->model = new LecturerModel($conn);

        // Lấy thông tin giảng viên
        $this->userId   = (int)$_SESSION['user_id'];
        $this->lecturer = $this->model->getOverviewByUserId($this->userId);

        if (!$this->lecturer || empty($this->lecturer['lecturer_id'])) {
            $this->abortError('Không tìm thấy thông tin giảng viên. (user_id=' . $this->userId . ')');
        }

        $this->lecturerId = (int)$this->lecturer['lecturer_id'];
    }

    /**
     * Render view và truyền data
     *
     * @param string $viewPath  Đường dẫn tương đối từ lecturer/views/
     * @param array  $data      Dữ liệu truyền vào view
     */
    protected function render(string $viewPath, array $data = []): void
    {
        // Biến luôn có sẵn trong view
        $data['lecturer']     = $this->lecturer;
        $data['lecturerId']   = $this->lecturerId;
        $data['currentPage']  = LecturerRouter::getPageName();

        extract($data);

        $fullPath = __DIR__ . '/../views/' . $viewPath;
        if (!file_exists($fullPath)) {
            $this->abortError("View không tìm thấy: {$viewPath}");
        }

        // Render layout
        require __DIR__ . '/../views/layout/header.php';
        require $fullPath;
        require __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Redirect kèm flash message
     */
    protected function redirectWithMessage(string $url, string $type, string $message): void
    {
        $_SESSION[$type] = $message;
        header("Location: {$url}");
        exit;
    }

    /**
     * Trả JSON (dùng cho AJAX)
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lấy flash message và xóa khỏi session
     */
    protected function getFlash(string $key): ?string
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

    /**
     * Hiển thị lỗi và dừng
     */
    protected function abortError(string $message): void
    {
        http_response_code(403);
        echo '<div style="font-family:sans-serif;padding:60px;text-align:center;">'
            . '<h2 style="color:#e74c3c;">⚠ Lỗi</h2>'
            . '<p>' . htmlspecialchars($message) . '</p>'
            . '<a href="' . BASE_URL . '/lecturer/" style="color:#0f766e;">← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
