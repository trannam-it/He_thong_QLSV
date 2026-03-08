<?php
/**
 * RoleController - Quản lý vai trò và gán quyền (Dynamic RBAC)
 */
class RoleController
{
    private mysqli $conn;
    private Auth   $auth;

    public function __construct(mysqli $conn, Auth $auth)
    {
        $this->conn = $conn;
        $this->auth = $auth;
    }

    // ─────────────────────────────────────────
    // INDEX - Danh sách roles
    // ─────────────────────────────────────────
    public function index(): void
    {
        $this->auth->requirePermission('roles.view');

        $stmt = $this->conn->prepare("
            SELECT r.*,
                   COUNT(DISTINCT ur.user_id) AS user_count,
                   COUNT(DISTINCT rp.permission_id) AS perm_count
            FROM roles r
            LEFT JOIN user_roles ur ON ur.role_id = r.id
            LEFT JOIN role_permissions rp ON rp.role_id = r.id
            GROUP BY r.id
            ORDER BY r.id
        ");
        $stmt->execute();
        $roles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success' => true, 'data' => $roles]);
    }

    // ─────────────────────────────────────────
    // SHOW - Chi tiết 1 role
    // ─────────────────────────────────────────
    public function show(): void
    {
        $this->auth->requirePermission('roles.view');

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { echo json_encode(['success'=>false,'message'=>'Thiếu ID']); return; }

        $role = $this->getRoleById($id);
        if (!$role) { echo json_encode(['success'=>false,'message'=>'Không tìm thấy']); return; }

        // Lấy permissions đã gán
        $assigned = $this->getAssignedPermIds($id);

        echo json_encode(['success'=>true,'data'=>$role,'assigned_permissions'=>$assigned]);
    }

    // ─────────────────────────────────────────
    // CREATE - Tạo role mới
    // ─────────────────────────────────────────
    public function store(): void
    {
        $this->auth->requirePermission('roles.create');

        $code        = trim($_POST['code']        ?? '');
        $name        = trim($_POST['name']        ?? '');
        $description = trim($_POST['description'] ?? '');
        $color       = trim($_POST['color']       ?? '#6c757d');

        if ($code === '' || $name === '') {
            echo json_encode(['success'=>false,'message'=>'Vui lòng nhập mã và tên vai trò.']);
            return;
        }

        if (!preg_match('/^[a-z][a-z0-9_]+$/', $code)) {
            echo json_encode(['success'=>false,'message'=>'Mã vai trò chỉ được chứa chữ thường, số và gạch dưới.']);
            return;
        }

        // Check duplicate
        $dup = $this->conn->prepare("SELECT id FROM roles WHERE code = ?");
        $dup->bind_param('s', $code);
        $dup->execute();
        if ($dup->get_result()->num_rows > 0) {
            echo json_encode(['success'=>false,'message'=>'Mã vai trò đã tồn tại.']);
            return;
        }

        $stmt = $this->conn->prepare("
            INSERT INTO roles (code, name, description, color) VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('ssss', $code, $name, $description, $color);

        if ($stmt->execute()) {
            $newId = (int)$this->conn->insert_id;
            $this->logAudit('CREATE', 'roles', $newId, null, compact('code','name'));
            echo json_encode(['success'=>true,'message'=>'Tạo vai trò thành công.','id'=>$newId]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Lỗi khi tạo vai trò.']);
        }
    }

    // ─────────────────────────────────────────
    // UPDATE - Cập nhật role
    // ─────────────────────────────────────────
    public function update(): void
    {
        $this->auth->requirePermission('roles.edit');

        $id          = (int)($_POST['id']          ?? 0);
        $code        = trim($_POST['code']        ?? '');
        $name        = trim($_POST['name']        ?? '');
        $description = trim($_POST['description'] ?? '');
        $color       = trim($_POST['color']       ?? '#6c757d');

        if (!$id || $code === '' || $name === '') {
            echo json_encode(['success'=>false,'message'=>'Thiếu thông tin bắt buộc.']);
            return;
        }

        $role = $this->getRoleById($id);
        if (!$role) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy vai trò.']);
            return;
        }

        if ($role['is_system'] && $role['code'] !== $code) {
            echo json_encode(['success'=>false,'message'=>'Không thể thay đổi mã của vai trò hệ thống.']);
            return;
        }

        $stmt = $this->conn->prepare("
            UPDATE roles SET code=?, name=?, description=?, color=? WHERE id=?
        ");
        $stmt->bind_param('ssssi', $code, $name, $description, $color, $id);

        if ($stmt->execute()) {
            $this->logAudit('UPDATE', 'roles', $id, $role, compact('code','name'));
            echo json_encode(['success'=>true,'message'=>'Cập nhật vai trò thành công.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Lỗi khi cập nhật.']);
        }
    }

    // ─────────────────────────────────────────
    // DELETE - Xóa role
    // ─────────────────────────────────────────
    public function delete(): void
    {
        $this->auth->requirePermission('roles.delete');

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success'=>false,'message'=>'Thiếu ID.']);
            return;
        }

        $role = $this->getRoleById($id);
        if (!$role) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy vai trò.']);
            return;
        }

        if ($role['is_system']) {
            echo json_encode(['success'=>false,'message'=>'Không thể xóa vai trò hệ thống.']);
            return;
        }

        // Kiểm tra có user đang dùng role này không
        $check = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM user_roles WHERE role_id = ?");
        $check->bind_param('i', $id);
        $check->execute();
        $cnt = $check->get_result()->fetch_assoc()['cnt'];

        if ($cnt > 0) {
            echo json_encode(['success'=>false,'message'=>"Không thể xóa: có {$cnt} tài khoản đang sử dụng vai trò này."]);
            return;
        }

        $stmt = $this->conn->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            clearPermissionCache();
            $this->logAudit('DELETE', 'roles', $id, $role, null);
            echo json_encode(['success'=>true,'message'=>'Xóa vai trò thành công.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Lỗi khi xóa vai trò.']);
        }
    }

    // ─────────────────────────────────────────
    // ASSIGN PERMISSIONS - Gán quyền cho role
    // ─────────────────────────────────────────
    public function assignPermissions(): void
    {
        // --- Debug logging (temporary) -------------------------------------------------
        $debugFile = __DIR__ . '/../storage/rbac_debug.log';
        $debugDir  = dirname($debugFile);
        if (!is_dir($debugDir)) mkdir($debugDir, 0755, true);
        
        $logEntry = [
            'time' => date('c'),
            'step' => 'start',
            'user_id' => $this->auth->getId(),
            'username' => $this->auth->getUsername(),
            'session_role' => $this->auth->getRole(),
            'post' => $_POST,
            'get'  => $_GET,
        ];
        file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
        // ------------------------------------------------------------------------------

        try {
            $this->auth->requirePermission('roles.assign_perm');
            $logEntry['step'] = 'permission_ok';
            file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
        } catch (Exception $e) {
            $logEntry['step'] = 'permission_denied';
            $logEntry['error'] = $e->getMessage();
            file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
            echo json_encode(['success'=>false,'message'=>'Bạn không có quyền cấu hình phân quyền.']);
            return;
        }

        $roleId     = (int)($_POST['role_id'] ?? 0);
        $permIds    = isset($_POST['permission_ids']) ? (array)$_POST['permission_ids'] : [];
        $permIds    = array_values(array_filter(array_map('intval', $permIds)));

        if (!$roleId) {
            echo json_encode(['success'=>false,'message'=>'Thiếu role ID.']);
            return;
        }

        $role = $this->getRoleById($roleId);
        if (!$role) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy vai trò.']);
            return;
        }

        $logEntry['step'] = 'role_found';
        $logEntry['role_id'] = $roleId;
        $logEntry['role_code'] = $role['code'];
        $logEntry['perm_count'] = count($permIds);
        file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

        // Không cho thay đổi permissions của super_admin qua đây
        if ($role['code'] === 'super_admin') {
            echo json_encode(['success'=>false,'message'=>'Super Admin luôn có toàn quyền, không cần cấu hình.']);
            return;
        }

        // Xóa quyền cũ
        $del = $this->conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $del->bind_param('i', $roleId);
        if (!$del->execute()) {
            $logEntry['step'] = 'delete_failed';
            $logEntry['error'] = $del->error;
            file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
            echo json_encode(['success'=>false,'message'=>'Lỗi khi xóa quyền cũ.']);
            return;
        }

        $logEntry['step'] = 'delete_ok';
        $logEntry['deleted_rows'] = $del->affected_rows;
        file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

        $grantedBy = $this->auth->getId() ?? 0;
        $insertCount = 0;

        if (!empty($permIds)) {
            $ins = $this->conn->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by) VALUES (?,?,?)");
            if (!$ins) {
                $logEntry['step'] = 'insert_prepare_failed';
                $logEntry['error'] = $this->conn->error;
                file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
                echo json_encode(['success'=>false,'message'=>'Lỗi chuẩn bị câu lệnh INSERT.']);
                return;
            }

            foreach ($permIds as $permId) {
                $ins->bind_param('iii', $roleId, $permId, $grantedBy);
                if ($ins->execute()) {
                    $insertCount++;
                } else {
                    $logEntry['step'] = 'insert_failed';
                    $logEntry['permission_id'] = $permId;
                    $logEntry['error'] = $ins->error;
                    file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
                }
            }

            $logEntry['step'] = 'insert_ok';
            $logEntry['inserted_rows'] = $insertCount;
            file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
        }

        // Xóa cache permission
        clearPermissionCache();

        $this->logAudit('ASSIGN_PERMISSIONS', 'roles', $roleId, null, ['permission_ids' => $permIds, 'count' => count($permIds)]);

        $logEntry['step'] = 'success';
        file_put_contents($debugFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật quyền thành công.',
            'granted_count' => count($permIds)
        ]);
    }

    /**
     * Lấy danh sách permissions (grouped) để render form gán quyền
     */
    public function getPermissionsForAssign(): void
    {
        $this->auth->requirePermission('roles.assign_perm');

        $roleId = (int)($_GET['role_id'] ?? 0);
        if (!$roleId) {
            echo json_encode(['success'=>false,'message'=>'Thiếu role_id']);
            return;
        }

        $role = $this->getRoleById($roleId);
        if (!$role) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy vai trò']);
            return;
        }

        // Lấy tất cả permissions (grouped)
        $result = $this->conn->query("
            SELECT p.*, pg.name AS group_name, pg.icon AS group_icon, pg.code AS group_code, pg.sort_order
            FROM permissions p
            INNER JOIN permission_groups pg ON pg.id = p.group_id
            ORDER BY pg.sort_order, p.id
        ");
        $all = $result->fetch_all(MYSQLI_ASSOC);

        // Lấy permissions đã được gán cho role này
        $assigned = $this->getAssignedPermIds($roleId);

        echo json_encode([
            'success'  => true,
            'role'     => $role,
            'permissions' => $all,
            'assigned' => $assigned
        ]);
    }

    // ─────────────────────────────────────────
    // ASSIGN USER - Gán user vào role
    // ─────────────────────────────────────────
    public function assignUser(): void
    {
        $this->auth->requirePermission('users.assign_role');

        $userId = (int)($_POST['user_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);
        $action = $_POST['action'] ?? 'add'; // add | remove

        if (!$userId || !$roleId) {
            echo json_encode(['success'=>false,'message'=>'Thiếu user_id hoặc role_id.']);
            return;
        }

        if ($action === 'add') {
            // Xóa role cũ rồi gán role mới (1 user 1 role)
            $del = $this->conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $del->bind_param('i', $userId);
            $del->execute();

            $assignedBy = $this->auth->getId() ?? 0;
            $ins = $this->conn->prepare("INSERT INTO user_roles (user_id, role_id, assigned_by) VALUES (?,?,?)");
            $ins->bind_param('iii', $userId, $roleId, $assignedBy);

            if ($ins->execute()) {
                clearPermissionCache($userId);
                $this->logAudit('ASSIGN_ROLE', 'user_roles', $userId, null, compact('userId','roleId'));
                echo json_encode(['success'=>true,'message'=>'Gán vai trò thành công.']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Lỗi khi gán vai trò.']);
            }
        } elseif ($action === 'remove') {
            $del = $this->conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ?");
            $del->bind_param('ii', $userId, $roleId);

            if ($del->execute()) {
                clearPermissionCache($userId);
                $this->logAudit('REMOVE_ROLE', 'user_roles', $userId, compact('userId','roleId'), null);
                echo json_encode(['success'=>true,'message'=>'Đã xóa vai trò khỏi tài khoản.']);
            } else {
                echo json_encode(['success'=>false,'message'=>'Lỗi khi xóa vai trò.']);
            }
        } else {
            echo json_encode(['success'=>false,'message'=>'Hành động không hợp lệ.']);
        }
    }

    /**
     * Tìm kiếm user (chưa có role)
     */
    public function searchUsers(): void
    {
        $this->auth->requirePermission('users.assign_role');

        $keyword = trim($_GET['q'] ?? '');
        $roleId  = (int)($_GET['role_id'] ?? 0);

        if ($keyword === '') {
            echo json_encode(['success'=>true,'data'=>[]]);
            return;
        }

        $like = '%' . $keyword . '%';
        $stmt = $this->conn->prepare("
            SELECT u.id, u.username, u.email,
                   r.name AS current_role
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            WHERE (u.username LIKE ? OR u.email LIKE ?)
            ORDER BY u.username LIMIT 20
        ");
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success'=>true,'data'=>$users]);
    }

    /**
     * Lấy danh sách users của 1 role
     */
    public function getUsersByRole(): void
    {
        $this->auth->requirePermission('roles.view');

        $roleId = (int)($_GET['role_id'] ?? 0);
        if (!$roleId) {
            echo json_encode(['success'=>false,'message'=>'Thiếu role_id']);
            return;
        }

        $stmt = $this->conn->prepare("
            SELECT u.id, u.username, u.email, u.is_active, ur.assigned_at
            FROM users u
            INNER JOIN user_roles ur ON ur.user_id = u.id
            WHERE ur.role_id = ?
            ORDER BY u.username
        ");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success'=>true,'data'=>$users]);
    }

    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function getRoleById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    private function getAssignedPermIds(int $roleId): array
    {
        $stmt = $this->conn->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'permission_id');
    }

    private function logAudit(string $action, string $table, int $recordId, ?array $old, ?array $new): void
    {
        $userId   = $this->auth->getId() ?? 0;
        $username = $this->auth->getUsername() ?? 'system';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $oldJson  = $old ? json_encode($old,  JSON_UNESCAPED_UNICODE) : null;
        $newJson  = $new ? json_encode($new,  JSON_UNESCAPED_UNICODE) : null;

        $stmt = $this->conn->prepare("
            INSERT INTO audit_logs (user_id, username, action, table_name, record_id, old_data, new_data, ip_address)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param('isssisss', $userId, $username, $action, $table, $recordId, $oldJson, $newJson, $ip);
        $stmt->execute();
    }
}