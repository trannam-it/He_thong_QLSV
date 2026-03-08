<?php
/**
 * RBACMiddleware - enforce permission checks tại Router layer
 *
 * Flow chuẩn:
 *   Request → Router (guardModule + RBACMiddleware::check)
 *           → Controller (requirePermissionWeb / requirePermission)
 *           → Service/Model (business logic)
 *
 * Sử dụng:
 *   require_once $base . '/core/RBACMiddleware.php';
 *   AppRouter::guardModule(['student']);
 *   RBACMiddleware::check($conn, $auth, 'student', $resource, $action);
 */

require_once __DIR__ . '/PermissionMap.php';

class RBACMiddleware
{
    /**
     * Kiểm tra quyền tại Router layer (cho API calls)
     * Nếu permission code không tìm thấy trong map => trả 403
     * Nếu user không có quyền => trả 403
     */
    public static function check(mysqli $conn, $auth, string $module, string $resource, string $action): void
    {
        // Super admin bypass tất cả
        if ($auth->isSuperAdmin()) {
            return;
        }

        $map = PermissionMap::get($conn);

        if (!isset($map[$module][$resource][$action])) {
            http_response_code(403);
            echo json_encode([
                'success'  => false,
                'message'  => 'Không xác định được quyền cho endpoint này.',
                'endpoint' => "{$module}/{$resource}/{$action}"
            ]);
            exit;
        }

        $code = $map[$module][$resource][$action];

        if (!$auth->hasPermission($code)) {
            http_response_code(403);
            echo json_encode([
                'success'    => false,
                'message'    => 'Bạn không có quyền thực hiện thao tác này.',
                'permission' => $code
            ]);
            exit;
        }
    }

    /**
     * Kiểm tra quyền tại Router layer cho Web pages (redirect thay vì trả JSON)
     * Dùng trong Controller index.php để guard web page
     */
    public static function checkWeb(mysqli $conn, $auth, string $permissionCode, string $redirectUrl = ''): void
    {
        if ($auth->isSuperAdmin()) {
            return;
        }

        if (!$auth->hasPermission($permissionCode)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            $back = $redirectUrl ?: (defined('BASE_URL') ? BASE_URL : '/web_QLSV') . '/public/index.php';
            header("Location: {$back}");
            exit;
        }
    }

    /**
     * Lấy permission code từ map (dùng trong Controller để check lại)
     */
    public static function getPermCode(mysqli $conn, string $module, string $resource, string $action): ?string
    {
        $map = PermissionMap::get($conn);
        return $map[$module][$resource][$action] ?? null;
    }
}
