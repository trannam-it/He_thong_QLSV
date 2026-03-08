<?php
/**
 * PermissionController - Quản lý quyền hạn (Dynamic RBAC)
 * CRUD permissions + permission groups
 */
class PermissionController
{
    private mysqli $conn;
    private Auth $auth;

    public function __construct(mysqli $conn, Auth $auth)
    {
        $this->conn = $conn;
        $this->auth = $auth;
    }

    // ─────────────────────────────────────────
    // PERMISSION GROUPS
    // ─────────────────────────────────────────

    public function listGroups(): void
    {
        $this->auth->requirePermission('permissions.view');
        $groups = $this->conn->query("SELECT * FROM permission_groups ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'data' => $groups]);
    }

    // ─────────────────────────────────────────
    // PERMISSIONS - CRUD
    // ─────────────────────────────────────────

    /**
     * Lấy danh sách permissions (grouped)
     */
    public function index(): void
    {
        $this->auth->requirePermission('permissions.view');

        $search   = $_GET['search'] ?? '';
        $groupId  = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

        $conditions = [];
        $params     = [];
        $types      = '';

        if ($search !== '') {
            $conditions[] = '(p.code LIKE ? OR p.name LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types   .= 'ss';
        }

        if ($groupId > 0) {
            $conditions[] = 'p.group_id = ?';
            $params[]     = $groupId;
            $types       .= 'i';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $stmt = $this->conn->prepare("
            SELECT p.*, pg.name AS group_name, pg.icon AS group_icon, pg.code AS group_code
            FROM permissions p
            INNER JOIN permission_groups pg ON pg.id = p.group_id
            $where
            ORDER BY pg.sort_order, p.id
        ");

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $perms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Lấy groups cho filter
        $groups = $this->conn->query("SELECT * FROM permission_groups ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success' => true, 'data' => $perms, 'groups' => $groups, 'total' => count($perms)]);
    }

    /**
     * Lấy 1 permission
     */
    public function show(): void
    {
        $this->auth->requirePermission('permissions.view');
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { echo json_encode(['success'=>false,'message'=>'Thiếu ID']); return; }

        $stmt = $this->conn->prepare("
            SELECT p.*, pg.name AS group_name FROM permissions p
            INNER JOIN permission_groups pg ON pg.id = p.group_id
            WHERE p.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $perm = $stmt->get_result()->fetch_assoc();

        if (!$perm) { echo json_encode(['success'=>false,'message'=>'Không tìm thấy']); return; }
        echo json_encode(['success'=>true,'data'=>$perm]);
    }

    /**
     * Tạo mới permission
     */
    public function store(): void
    {
        $this->auth->requirePermission('permissions.create');

        $groupId     = (int)($_POST['group_id']    ?? 0);
        $code        = trim($_POST['code']         ?? '');
        $name        = trim($_POST['name']         ?? '');
        $description = trim($_POST['description']  ?? '');

        if (!$groupId || $code === '' || $name === '') {
            echo json_encode(['success'=>false,'message'=>'Vui lòng điền đầy đủ thông tin.']);
            return;
        }

        // Validate code format
        if (!preg_match('/^[a-z][a-z0-9_.]+$/', $code)) {
            echo json_encode(['success'=>false,'message'=>'Mã quyền chỉ được chứa chữ thường, số, dấu chấm và gạch dưới.']);
            return;
        }

        // Check duplicate
        $dup = $this->conn->prepare("SELECT id FROM permissions WHERE code = ?");
        $dup->bind_param('s', $code);
        $dup->execute();
        if ($dup->get_result()->num_rows > 0) {
            echo json_encode(['success'=>false,'message'=>'Mã quyền đã tồn tại.']);
            return;
        }

        $stmt = $this->conn->prepare("
            INSERT INTO permissions (group_id, code, name, description)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('isss', $groupId, $code, $name, $description);

        if ($stmt->execute()) {
            $this->logAudit('CREATE', 'permissions', (int)$this->conn->insert_id, null, compact('code','name'));
            echo json_encode(['success'=>true,'message'=>'Tạo quyền hạn thành công.','id'=>$this->conn->insert_id]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Lỗi khi tạo quyền hạn.']);
        }
    }

    /**
     * Cập nhật permission
     */
    public function update(): void
    {
        $this->auth->requirePermission('permissions.edit');

        $id          = (int)($_POST['id']          ?? 0);
        $groupId     = (int)($_POST['group_id']    ?? 0);
        $code        = trim($_POST['code']         ?? '');
        $name        = trim($_POST['name']         ?? '');
        $description = trim($_POST['description']  ?? '');

        if (!$id || !$groupId || $code === '' || $name === '') {
            echo json_encode(['success'=>false,'message'=>'Thiếu thông tin.']);
            return;
        }

        $existing = $this->getPermById($id);
        if (!$existing) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy quyền hạn.']);
            return;
        }

        // Không cho sửa code của system permission
        if ($existing['is_system'] && $existing['code'] !== $code) {
            echo json_encode(['success'=>false,'message'=>'Không thể thay đổi mã của quyền hệ thống.']);
            return;
        }

        $stmt = $this->conn->prepare("
            UPDATE permissions SET group_id=?, code=?, name=?, description=? WHERE id=?
        ");
        $stmt->bind_param('isssi', $groupId, $code, $name, $description, $id);

        if ($stmt->execute()) {
            clearPermissionCache(); // Xóa cache toàn bộ
            $this->logAudit('UPDATE', 'permissions', $id, $existing, compact('code','name'));
            echo json_encode(['success'=>true,'message'=>'Cập nhật thành công.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Lỗi khi cập nhật.']);
        }
    }

    /**
     * Xóa permission
     */
    public function delete(): void
    {
        $this->auth->requirePermission('permissions.delete');

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success'=>false,'message'=>'Thiếu ID.']);
            return;
        }

        $perm = $this->getPermById($id);
        if (!$perm) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy.']);
            return;
        }

        if ($perm['is_system']) {
            echo json_encode(['success'=>false,'message'=>'Không thể xóa quyền hệ thống.']);
            return;
        }

        $stmt = $this->conn->prepare("DELETE FROM permissions WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            clearPermissionCache();
            $this->logAudit('DELETE', 'permissions', $id, $perm, null);
            echo json_encode(['success'=>true,'message'=>'Xóa quyền hạn thành công.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Lỗi khi xóa.']);
        }
    }

    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function getPermById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM permissions WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    private function logAudit(string $action, string $table, int $recordId, ?array $old, ?array $new): void
    {
        $userId   = $this->auth->getId() ?? 0;
        $username = $this->auth->getUsername() ?? 'system';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $oldJson  = $old  ? json_encode($old,  JSON_UNESCAPED_UNICODE) : null;
        $newJson  = $new  ? json_encode($new,  JSON_UNESCAPED_UNICODE) : null;

        $stmt = $this->conn->prepare("
            INSERT INTO audit_logs (user_id, username, action, table_name, record_id, old_data, new_data, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('isssisss', $userId, $username, $action, $table, $recordId, $oldJson, $newJson, $ip);
        $stmt->execute();
    }
}