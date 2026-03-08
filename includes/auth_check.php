<?php
/**
 * Auth Check - Middleware cho các trang web
 * Kiểm tra xác thực và phân quyền (Dynamic RBAC)
 * Tích hợp với AppRouter để redirect đúng module theo role
 */

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Tự động load AppRouter nếu chưa load
if (!class_exists('AppRouter')) {
    $__appRouterPath = __DIR__ . '/../core/AppRouter.php';
    if (file_exists($__appRouterPath)) {
        require_once $__appRouterPath;
    }
}

/**
 * Kiểm tra đăng nhập + role (dùng cho các page web, redirect nếu sai)
 * Khi role không khớp, redirect về đúng module của role hiện tại thay vì về login
 */
function authCheck(array $allowedRoles = []): void
{
    $loginUrl = (defined('BASE_URL') ? BASE_URL : '/web_QLSV') . '/public/index.php';

    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
        header('Location: ' . $loginUrl);
        exit;
    }

    if (!empty($allowedRoles)) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
            // Nếu đã đăng nhập nhưng sai role -> redirect về đúng module
            $currentRole = $_SESSION['role'] ?? '';
            if ($currentRole && class_exists('AppRouter') && AppRouter::isValidRole($currentRole)) {
                header('Location: ' . AppRouter::getModuleUrl($currentRole));
            } else {
                $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
                header('Location: ' . $loginUrl);
            }
            exit;
        }
    }
}

/**
 * Kiểm tra quyền hạn cụ thể (Dynamic RBAC)
 * @param string $permission  Mã quyền, vd: 'students.view'
 * @param mysqli $conn        Kết nối DB
 * @return bool
 */
function checkPermission(string $permission, ?mysqli $conn = null): bool
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    // Super Admin luôn có quyền
    if (($_SESSION['role'] ?? '') === 'super_admin') {
        return true;
    }

    if ($conn === null) {
        return false;
    }

    // Kiểm tra cache session trước
    $userId = (int)$_SESSION['user_id'];
    $now    = time();
    $ttl    = 300; // 5 phút

    if (isset($_SESSION['perm_cache'][$userId]) &&
        isset($_SESSION['perm_cache_time'][$userId]) &&
        ($now - $_SESSION['perm_cache_time'][$userId]) < $ttl) {
        return in_array($permission, $_SESSION['perm_cache'][$userId]);
    }

    // Load từ DB
    $stmt = $conn->prepare("
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

    // Lưu cache
    $_SESSION['perm_cache'][$userId]      = $perms;
    $_SESSION['perm_cache_time'][$userId] = $now;

    return in_array($permission, $perms);
}

/**
 * Kiểm tra quyền và redirect nếu không có
 */
function requirePermission(string $permission, mysqli $conn, string $redirect = ''): void
{
    authCheck();

    if (!checkPermission($permission, $conn)) {
        $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
        $back = $redirect ?: '/web_QLSV/admin/Dashboard.php';
        header("Location: {$back}");
        exit;
    }
}

/**
 * Lấy toàn bộ danh sách permissions của user hiện tại
 */
function getUserPermissions(?mysqli $conn = null): array
{
    if (!isset($_SESSION['user_id'])) return [];
    if (($_SESSION['role'] ?? '') === 'super_admin') return ['*'];
    if ($conn === null) return [];

    $userId = (int)$_SESSION['user_id'];
    $now    = time();
    $ttl    = 300;

    if (isset($_SESSION['perm_cache'][$userId]) &&
        isset($_SESSION['perm_cache_time'][$userId]) &&
        ($now - $_SESSION['perm_cache_time'][$userId]) < $ttl) {
        return $_SESSION['perm_cache'][$userId];
    }

    $stmt = $conn->prepare("
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

    $_SESSION['perm_cache'][$userId]      = $perms;
    $_SESSION['perm_cache_time'][$userId] = $now;

    return $perms;
}

/**
 * Xóa cache permission của user (gọi khi admin thay đổi quyền)
 */
function clearPermissionCache(?int $userId = null): void
{
    if ($userId) {
        unset($_SESSION['perm_cache'][$userId]);
        unset($_SESSION['perm_cache_time'][$userId]);
    } else {
        unset($_SESSION['perm_cache']);
        unset($_SESSION['perm_cache_time']);
    }
}