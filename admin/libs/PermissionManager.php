<?php
/**
 * PermissionManager - Dynamic RBAC Core Engine
 * Quản lý toàn bộ việc kiểm tra quyền theo cơ chế động
 */
class PermissionManager
{
    private static ?PermissionManager $instance = null;
    private mysqli $conn;
    private array $cache = [];
    private int $cacheTtl = 300; // 5 phút

    private function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public static function getInstance(mysqli $conn): self
    {
        if (self::$instance === null) {
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * Kiểm tra user có quyền cụ thể không
     * Super Admin luôn trả về true
     */
    public function hasPermission(int $userId, string $permissionCode): bool
    {
        // Super Admin có tất cả quyền
        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        $permissions = $this->getUserPermissions($userId);
        return in_array($permissionCode, $permissions);
    }

    /**
     * Kiểm tra nhiều quyền cùng lúc (AND logic)
     */
    public function hasAllPermissions(int $userId, array $permCodes): bool
    {
        foreach ($permCodes as $code) {
            if (!$this->hasPermission($userId, $code)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Kiểm tra có ít nhất 1 trong các quyền (OR logic)
     */
    public function hasAnyPermission(int $userId, array $permCodes): bool
    {
        foreach ($permCodes as $code) {
            if ($this->hasPermission($userId, $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Lấy tất cả quyền của user (từ cache session hoặc DB)
     */
    public function getUserPermissions(int $userId): array
    {
        $cacheKey = "user_perms_{$userId}";
        $now = time();

        // Kiểm tra cache trong memory
        if (isset($this->cache[$cacheKey]) &&
            ($now - $this->cache[$cacheKey]['time']) < $this->cacheTtl) {
            return $this->cache[$cacheKey]['perms'];
        }

        // Kiểm tra cache trong session
        if (isset($_SESSION['perm_cache'][$userId]) &&
            isset($_SESSION['perm_cache_time'][$userId]) &&
            ($now - $_SESSION['perm_cache_time'][$userId]) < $this->cacheTtl) {
            $perms = $_SESSION['perm_cache'][$userId];
            $this->cache[$cacheKey] = ['perms' => $perms, 'time' => $now];
            return $perms;
        }

        // Load từ DB
        $perms = $this->loadPermissionsFromDB($userId);

        // Lưu vào cache
        $this->cache[$cacheKey] = ['perms' => $perms, 'time' => $now];
        $_SESSION['perm_cache'][$userId] = $perms;
        $_SESSION['perm_cache_time'][$userId] = $now;

        return $perms;
    }

    /**
     * Load permissions từ database
     */
    private function loadPermissionsFromDB(int $userId): array
    {
        $stmt = $this->conn->prepare("
            SELECT DISTINCT p.code
            FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            INNER JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $perms = [];
        while ($row = $result->fetch_assoc()) {
            $perms[] = $row['code'];
        }
        return $perms;
    }

    /**
     * Xóa cache permission của user (gọi khi thay đổi quyền)
     */
    public function clearCache(int $userId): void
    {
        $cacheKey = "user_perms_{$userId}";
        unset($this->cache[$cacheKey]);
        unset($_SESSION['perm_cache'][$userId]);
        unset($_SESSION['perm_cache_time'][$userId]);
    }

    /**
     * Xóa cache toàn bộ
     */
    public function clearAllCache(): void
    {
        $this->cache = [];
        unset($_SESSION['perm_cache']);
        unset($_SESSION['perm_cache_time']);
    }

    /**
     * Kiểm tra user có phải Super Admin không
     */
    private function isSuperAdmin(int $userId): bool
    {
        $cacheKey = "is_super_{$userId}";
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $stmt = $this->conn->prepare("
            SELECT 1 FROM user_roles ur
            INNER JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = ? AND r.code = 'super_admin'
            LIMIT 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->num_rows > 0;
        $this->cache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Lấy role của user
     */
    public function getUserRoles(int $userId): array
    {
        $stmt = $this->conn->prepare("
            SELECT r.id, r.code, r.name, r.color
            FROM roles r
            INNER JOIN user_roles ur ON ur.role_id = r.id
            WHERE ur.user_id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy danh sách permission có group info
     */
    public function getAllPermissionsGrouped(): array
    {
        $result = $this->conn->query("
            SELECT p.*, pg.name AS group_name, pg.icon AS group_icon
            FROM permissions p
            INNER JOIN permission_groups pg ON pg.id = p.group_id
            ORDER BY pg.sort_order, p.id
        ");
        $groups = [];
        while ($row = $result->fetch_assoc()) {
            $groupName = $row['group_name'];
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = [
                    'icon'  => $row['group_icon'],
                    'perms' => []
                ];
            }
            $groups[$groupName]['perms'][] = $row;
        }
        return $groups;
    }

    /**
     * Lấy permissions đã gán cho 1 role
     */
    public function getRolePermissions(int $roleId): array
    {
        $stmt = $this->conn->prepare("
            SELECT permission_id FROM role_permissions WHERE role_id = ?
        ");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'permission_id');
    }

    /**
     * Gán permissions cho role (admin thực hiện)
     */
    public function setRolePermissions(int $roleId, array $permIds, int $grantedBy): bool
    {
        // Xóa quyền cũ
        $stmt = $this->conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();

        if (empty($permIds)) {
            $this->clearAllCache();
            return true;
        }

        $insert = $this->conn->prepare("
            INSERT INTO role_permissions (role_id, permission_id, granted_by)
            VALUES (?, ?, ?)
        ");

        foreach ($permIds as $permId) {
            $permId = (int)$permId;
            $insert->bind_param('iii', $roleId, $permId, $grantedBy);
            $insert->execute();
        }

        // Xóa cache để reload
        $this->clearAllCache();
        return true;
    }

    /**
     * Thêm 1 quyền vào role
     */
    public function grantPermission(int $roleId, int $permId, int $grantedBy): bool
    {
        $stmt = $this->conn->prepare("
            INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param('iii', $roleId, $permId, $grantedBy);
        $ok = $stmt->execute();
        $this->clearAllCache();
        return $ok;
    }

    /**
     * Thu hồi 1 quyền khỏi role
     */
    public function revokePermission(int $roleId, int $permId): bool
    {
        $stmt = $this->conn->prepare("
            DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?
        ");
        $stmt->bind_param('ii', $roleId, $permId);
        $ok = $stmt->execute();
        $this->clearAllCache();
        return $ok;
    }
}