<?php
/**
 * Base Controller - Dynamic RBAC
 * Parent class for all API controllers
 */
require_once __DIR__ . '/../../includes/auth_check.php';

if (file_exists(__DIR__ . '/../../includes/audit_log.php')) {
    require_once __DIR__ . '/../../includes/audit_log.php';
}

class BaseController
{
    protected DatabaseHelper $db;
    protected Auth           $auth;
    protected Validator      $validator;
    protected mysqli         $conn;

    public function __construct(mysqli $connection, ?Auth $authInstance = null)
    {
        $this->conn      = $connection;
        $this->db        = new DatabaseHelper($connection);
        $this->auth      = $authInstance ?? new Auth($connection);
        $this->validator = new Validator();
    }

    /**
     * Gọi action an toàn
     */
    public function call(string $action, array $params = [])
    {
        if (method_exists($this, $action)) {
            return call_user_func_array([$this, $action], $params);
        }
        return Response::error('Action not found', 404);
    }

    /**
     * Phân trang
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

    /**
     * Ghi audit log
     */
    protected function logAudit(string $action, string $table, int $recordId, ?array $old, ?array $new): void
    {
        $userId   = $this->auth->getId()       ?? 0;
        $username = $this->auth->getUsername() ?? 'system';
        $ip       = $_SERVER['REMOTE_ADDR']    ?? '0.0.0.0';
        $oldJson  = $old ? json_encode($old,  JSON_UNESCAPED_UNICODE) : null;
        $newJson  = $new ? json_encode($new,  JSON_UNESCAPED_UNICODE) : null;

        $stmt = $this->conn->prepare("
            INSERT INTO audit_logs
                (user_id, username, action, table_name, record_id, old_data, new_data, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('isssisss', $userId, $username, $action, $table, $recordId, $oldJson, $newJson, $ip);
        $stmt->execute();
    }
}