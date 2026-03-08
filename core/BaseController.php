<?php
/**
 * BaseController - Controller cơ sở thống nhất cho toàn bộ hệ thống
 * 
 * Tất cả các module controller đều kế thừa từ class này.
 * Cung cấp: render(), json(), redirect(), flash message, pagination, audit log.
 */

require_once __DIR__ . '/../includes/auth_check.php';

class BaseController
{
    protected mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ──────────────────────────────────────────────────────────
    // RENDER / JSON / REDIRECT
    // ──────────────────────────────────────────────────────────

    /**
     * Render view với layout header + footer
     *
     * @param string $layoutHeaderPath  Đường dẫn tuyệt đối đến header.php
     * @param string $layoutFooterPath  Đường dẫn tuyệt đối đến footer.php
     * @param string $viewFullPath      Đường dẫn tuyệt đối đến view.php
     * @param array  $data              Dữ liệu truyền vào view
     */
    protected function renderView(
        string $layoutHeaderPath,
        string $layoutFooterPath,
        string $viewFullPath,
        array  $data = []
    ): void {
        extract($data);

        if (!file_exists($viewFullPath)) {
            $this->abortError("View không tìm thấy: {$viewFullPath}");
        }

        require $layoutHeaderPath;
        require $viewFullPath;
        require $layoutFooterPath;
    }

    /**
     * Trả về JSON (dùng cho AJAX / API)
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
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
     * Redirect đơn giản
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // FLASH MESSAGE
    // ──────────────────────────────────────────────────────────

    /**
     * Lấy flash message và xóa khỏi session
     */
    protected function getFlash(string $key): ?string
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

    // ──────────────────────────────────────────────────────────
    // PAGINATION
    // ──────────────────────────────────────────────────────────

    /**
     * Lấy thông tin phân trang từ query string
     */
    protected function getPagination(): array
    {
        $page  = max(1, (int)($_GET['page']  ?? 1));
        $limit = (int)($_GET['limit'] ?? 20);
        if ($limit < 1)   $limit = 20;
        if ($limit > 100) $limit = 100;

        return [
            'page'   => $page,
            'limit'  => $limit,
            'offset' => ($page - 1) * $limit
        ];
    }

    // ──────────────────────────────────────────────────────────
    // AUDIT LOG
    // ──────────────────────────────────────────────────────────

    /**
     * Ghi audit log vào database
     */
    protected function logAudit(
        string  $action,
        string  $table,
        int     $recordId,
        ?array  $old,
        ?array  $new
    ): void {
        $userId   = $_SESSION['user_id']  ?? 0;
        $username = $_SESSION['username'] ?? 'system';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $oldJson  = $old ? json_encode($old,  JSON_UNESCAPED_UNICODE) : null;
        $newJson  = $new ? json_encode($new,  JSON_UNESCAPED_UNICODE) : null;

        $stmt = $this->conn->prepare("
            INSERT INTO audit_logs
                (user_id, username, action, table_name, record_id, old_data, new_data, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt) {
            $stmt->bind_param('isssisss', $userId, $username, $action, $table, $recordId, $oldJson, $newJson, $ip);
            $stmt->execute();
        }
    }

    // ──────────────────────────────────────────────────────────
    // ERROR
    // ──────────────────────────────────────────────────────────

    /**
     * Dừng thực thi và hiển thị lỗi
     */
    protected function abortError(string $message, string $backUrl = ''): void
    {
        $back = $backUrl ?: (BASE_URL . '/public/index.php');
        http_response_code(403);
        echo '<div style="font-family:sans-serif;padding:60px;text-align:center;">'
            . '<h2 style="color:#e74c3c;">⚠ Lỗi</h2>'
            . '<p>' . htmlspecialchars($message) . '</p>'
            . '<a href="' . htmlspecialchars($back) . '" style="color:#0d6efd;">← Quay lại</a>'
            . '</div>';
        exit;
    }
}
