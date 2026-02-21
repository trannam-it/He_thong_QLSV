<?php
/**
 * Base Controller
 * Parent class for all controllers
 */
// require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../includes/audit_log.php';
if (!function_exists('writeAuditLog')) {
    die('writeAuditLog NOT FOUND');
}

class BaseController
{
    protected $db;
    protected $auth;
    protected $validator;
    protected $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
        $this->db = new Database($connection);
        $this->auth = new Auth($connection);
        $this->validator = new Validator();
    }

    /**
     * Call method safely
     */
    public function call($action, $params = [])
    {
        if (method_exists($this, $action)) {
            return call_user_func_array([$this, $action], $params);
        }
        return Response::error('Action not found');
    }

    /**
     * Get pagination info
     */
    // protected function getPagination()
    // {
    //     $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    //     $limit = isset($_GET['limit']) ? min(100, (int)$_GET['limit']) : 20;
    //     $offset = ($page - 1) * $limit;
        
    //     return compact('page', 'limit', 'offset');
    // }

    protected function getPagination()
    {
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $limit = (int)($_GET['limit'] ?? 10);

        // 🔒 Giới hạn an toàn
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;

        $offset = ($page - 1) * $limit;

        return compact('page', 'limit', 'offset');
    }


    /**
     * Log audit
     */
    protected function logAudit($action, $table, $recordId, $oldData = null, $newData = null)
    {
        writeAuditLog(
            $this->conn,
            $this->auth->getId(),
            $this->auth->getUsername(),
            $action,
            $table,
            $recordId,
            $oldData ? json_encode($oldData) : null,
            $newData ? json_encode($newData) : null
        );
    }
}
?>