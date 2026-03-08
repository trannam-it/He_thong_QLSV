<?php
/**
 * BaseStudentController - Controller cơ sở cho vai trò Sinh viên
 *
 * Flow phân quyền:
 *   Router (AppRouter::guardModule + RBACMiddleware::check)
 *     → Controller (requirePermission* tại mỗi action)
 *     → Model/Service (business logic)
 */
// var_dump($_SESSION['user_id']);
// exit;


require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/dashboard_helper.php';
require_once __DIR__ . '/../../core/AppRouter.php';
require_once __DIR__ . '/../../core/RBACMiddleware.php';

// Load Router để dùng url() helper trong controller
require_once __DIR__ . '/../Router.php';

class BaseStudentController
{

    protected mysqli       $conn;
    protected StudentModel $model;
    protected ?Auth        $auth;
    protected array        $student;
    protected int          $studentId;
    protected int          $userId;

    public function __construct(mysqli $conn)
    {
        //   var_dump($conn);
        //     exit;
        $this->conn = $conn;

        // Load Auth for RBAC
        require_once __DIR__ . '/../../admin/libs/Auth.php';
        $this->auth = new Auth($conn);

        // Load model
        require_once __DIR__ . '/../models/StudentModel.php';
        $this->model = new StudentModel($conn);

        // [LAYER 1] Router guard: chỉ student mới vào được module này
        AppRouter::guardModule(['student']);

        $this->userId  = (int)$_SESSION['user_id'];
        $this->student = $this->model->getOverviewByUserId($this->userId);
        $this->studentId = (int)$this->student['student_id'];

        if ($this->studentId === 0) {
            $this->abortNotFound('Không tìm thấy thông tin sinh viên. (user_id=' . $this->userId . ')');
        }
    }

    /**
     * [LAYER 2] Controller permission check - gọi trong từng action của controller
     * Kiểm tra quyền động từ DB, nếu không có sẽ redirect về dashboard
     */
    protected function requirePermission(string $permissionCode): void
    {
        if (!$this->auth->hasPermission($permissionCode)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này.';
            $back = defined('BASE_URL') ? BASE_URL . '/student/' : '/student/';
            header("Location: {$back}");
            exit;
        }
    }

    /**
     * [LAYER 2] Controller permission check cho API (trả JSON)
     */
    protected function requirePermissionAPI(string $permissionCode): void
    {
        if (!$this->auth->hasPermission($permissionCode)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success'    => false,
                'message'    => 'Bạn không có quyền thực hiện thao tác này.',
                'permission' => $permissionCode
            ]);
            exit;
        }
    }

    /**
     * Render view và truyền data
     */
    protected function render(string $viewPath, array $data = []): void
    {
        $data['student']     = $this->student;
        $data['studentId']   = $this->studentId;
        $data['currentPage'] = StudentRouter::getPageName();
        $data['auth']        = $this->auth;

        extract($data);

        $fullPath = __DIR__ . '/../views/' . $viewPath;
        if (!file_exists($fullPath)) {
            $this->abortNotFound("View không tìm thấy: {$viewPath}");
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

    protected function abortNotFound(string $message): void
    {
        http_response_code(404);
        echo '<div style="font-family:sans-serif;padding:60px;text-align:center;">'
            . '<h2 style="color:#e74c3c;">⚠ Lỗi</h2>'
            . '<p>' . htmlspecialchars($message) . '</p>'
            . '<a href="' . (defined('BASE_URL') ? BASE_URL : '') . '/student/" style="color:#4e73df;">← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }

    protected function getCurrentPage(): string
    {
        return StudentRouter::getPageName();
    }
}
