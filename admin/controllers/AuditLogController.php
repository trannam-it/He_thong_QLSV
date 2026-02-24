<?php
/**
 * Audit Log Controller
 * Quản lý nhật ký
 */
class AuditLogController extends BaseController
{
    public function index()
    {
        $this->auth->requirePermission('view_audit_logs');
        
        $pagination = $this->getPagination();
        $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        
        $where = '';
        $params = [];
        
        if ($search) {
            $where = '(username LIKE ? OR action LIKE ? OR table_name LIKE ?)';
            $params = [$search, $search, $search];
        }
        
        if ($action) {
            if ($where) $where .= ' AND ';
            $where .= 'action = ?';
            $params[] = $action;
        }
        
        $query = "SELECT * FROM audit_logs";
        if ($where) $query .= " WHERE $where";
        $query .= " ORDER BY audit_id DESC LIMIT ? OFFSET ?";
        
        $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
        $logs = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
        $countQuery = "SELECT COUNT(*) as total FROM audit_logs";
        if ($where) $countQuery .= " WHERE $where";
        
        $countParams = array_values($params);
        $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

        return Response::paginate($logs, $total, $pagination['page'], $pagination['limit']);
    }

    public function show()
    {
        $this->auth->requirePermission('view_audit_logs');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Audit ID required', 400);

        $log = $this->db->selectOne('audit_logs', 'audit_id = ?', [$id]);
        if (!$log) return Response::error('Audit log not found', 404);

        // Decode JSON data
        if ($log['old_data']) $log['old_data'] = json_decode($log['old_data'], true);
        if ($log['new_data']) $log['new_data'] = json_decode($log['new_data'], true);

        return Response::success($log);
    }

    /**
     * Export audit logs
     */
    public function export()
    {
        $this->auth->requirePermission('view_audit_logs');
        
        $logs = $this->db->select('audit_logs', '', [], 10000);

        $filename = 'audit_logs_' . date('Y-m-d_His');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        fputcsv($output, ['ID', 'User ID', 'Username', 'Action', 'Table', 'Record ID', 'IP', 'Timestamp'], ';');
        
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['audit_id'],
                $log['user_id'],
                $log['username'],
                $log['action'],
                $log['table_name'],
                $log['record_id'],
                $log['ip_address'],
                $log['created_at']
            ], ';');
        }

        fclose($output);
        exit;
    }
}
?>