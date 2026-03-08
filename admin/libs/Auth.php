<?php
/**
 * Auth - Middleware xác thực và kiểm tra quyền (Dynamic RBAC)
 * Tích hợp với PermissionManager để kiểm tra quyền từ database
 */
class Auth
{
    private mysqli $conn;
    private ?int   $userId;
    private ?string $userRole;
    private ?string $username;
    private ?PermissionManager $permManager = null;

    public function __construct(mysqli $conn)
    {
        $this->conn     = $conn;
        $this->userId   = $_SESSION['user_id']  ?? null;
        $this->userRole = $_SESSION['role']      ?? null;
        $this->username = $_SESSION['username']  ?? null;
    }

    // ─────────────────────────────────────────
    // Getters
    // ─────────────────────────────────────────
    public function getId():       ?int    { return $this->userId;   }
    public function getRole():     ?string { return $this->userRole; }
    public function getUsername(): ?string { return $this->username; }

    // ─────────────────────────────────────────
    // Trạng thái xác thực
    // ─────────────────────────────────────────
    public function isAuthenticated(): bool
    {
        return !is_null($this->userId) && !is_null($this->userRole);
    }

    public function isSuperAdmin(): bool
    {
        return $this->userRole === 'super_admin';
    }

    // ─────────────────────────────────────────
    // Kiểm tra ROLE
    // ─────────────────────────────────────────
    public function hasRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->userRole, $roles);
    }

    // ─────────────────────────────────────────
    // DYNAMIC PERMISSION CHECK (qua DB)
    // ─────────────────────────────────────────

    /**
     * Lấy PermissionManager (lazy init)
     */
    private function getPermManager(): PermissionManager
    {
        if ($this->permManager === null) {
            require_once __DIR__ . '/PermissionManager.php';
            $this->permManager = PermissionManager::getInstance($this->conn);
        }
        return $this->permManager;
    }

    /**
     * Kiểm tra 1 quyền
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->userId) return false;
        if ($this->isSuperAdmin()) return true;
        return $this->getPermManager()->hasPermission($this->userId, $permission);
    }

    /**
     * Kiểm tra nhiều quyền (AND)
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $p) {
            if (!$this->hasPermission($p)) return false;
        }
        return true;
    }

    /**
     * Kiểm tra bất kỳ quyền nào (OR)
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $p) {
            if ($this->hasPermission($p)) return true;
        }
        return false;
    }

    /**
     * Lấy tất cả permissions của user hiện tại
     */
    public function getUserPermissions(): array
    {
        if (!$this->userId) return [];
        if ($this->isSuperAdmin()) return ['*']; // Super admin flag
        return $this->getPermManager()->getUserPermissions($this->userId);
    }

    /**
     * Xóa cache permission (gọi sau khi thay đổi quyền)
     */
    public function clearPermissionCache(): void
    {
        if ($this->userId) {
            $this->getPermManager()->clearCache($this->userId);
        }
    }

    // ─────────────────────────────────────────
    // REQUIRE METHODS - Dùng cho API (trả JSON)
    // ─────────────────────────────────────────

    /**
     * Yêu cầu đã đăng nhập (trả JSON nếu không)
     */
    public function requireAuthAPI(): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Chưa xác thực. Vui lòng đăng nhập.']);
            exit;
        }
    }

    /**
     * Yêu cầu quyền cụ thể (trả JSON nếu không có quyền)
     */
    public function requirePermission(string $permission): void
    {
        $this->requireAuthAPI();

        if (!$this->hasPermission($permission)) {
            http_response_code(403);
            echo json_encode([
                'success'    => false,
                'message'    => 'Bạn không có quyền thực hiện thao tác này.',
                'permission' => $permission
            ]);
            exit;
        }
    }

    /**
     * Yêu cầu 1 trong các quyền (OR)
     */
    public function requireAnyPermission(array $permissions): void
    {
        $this->requireAuthAPI();

        if (!$this->hasAnyPermission($permissions)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ]);
            exit;
        }
    }

    /**
     * Yêu cầu role
     */
    public function requireRole($roles): void
    {
        $this->requireAuthAPI();

        if (!$this->hasRole($roles)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Vai trò không hợp lệ.']);
            exit;
        }
    }

    // ─────────────────────────────────────────
    // REQUIRE METHODS - Dùng cho Web (redirect)
    // ─────────────────────────────────────────

    /**
     * Yêu cầu đăng nhập (redirect nếu không)
     */
    public function requireAuthWeb(string $redirectTo = '/web_QLSV/public/index.php'): void
    {
        if (!$this->isAuthenticated()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Yêu cầu quyền (redirect nếu không có)
     */
    public function requirePermissionWeb(string $permission, string $redirectTo = ''): void
    {
        $this->requireAuthWeb();

        if (!$this->hasPermission($permission)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            $back = $redirectTo ?: ($_SERVER['HTTP_REFERER'] ?? '/web_QLSV/admin/Dashboard.php');
            header("Location: {$back}");
            exit;
        }
    }

    /**
     * Alias để tương thích ngược với code cũ
     */
    public function requirePermissionAPI(string $permission): void
    {
        $this->requirePermission($permission);
    }

    public function requireRoleAPI($roles): void
    {
        $this->requireRole($roles);
    }
}