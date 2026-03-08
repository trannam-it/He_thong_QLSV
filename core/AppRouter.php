<?php
/**
 * AppRouter - Router trung tâm điều hướng toàn bộ hệ thống theo Role
 *
 * Cấu trúc URL thống nhất:
 *   /web_QLSV/public/index.php          → Trang đăng nhập
 *   /web_QLSV/admin/                    → Dashboard Admin (super_admin, content_admin)
 *   /web_QLSV/academic/                 → Dashboard Quản lý Đào tạo (academic_admin)
 *   /web_QLSV/lecturer/                 → Dashboard Giảng viên (teacher)
 *   /web_QLSV/student/                  → Dashboard Sinh viên (student)
 *   /web_QLSV/accountant/               → Dashboard Kế toán (accountant)
 *   /web_QLSV/librarian/                → Dashboard Thủ thư (librarian)
 *
 * Mỗi module có index.php riêng gọi Router tương ứng:
 *   Module Router (AcademicRouter, LecturerRouter, ...) xử lý ?page= param
 *
 * API thống nhất đi qua:
 *   /web_QLSV/admin/api/router.php      → Admin API (RBAC)
 *   /web_QLSV/academic/api/router.php   → Academic API
 *   /web_QLSV/lecturer/api/router.php   → Lecturer API
 *   /web_QLSV/student/api/router.php    → Student API
 *   /web_QLSV/accountant/api/router.php → Accountant API
 *   /web_QLSV/librarian/api/router.php  → Librarian API
 */

class AppRouter
{
    /**
     * Map: role_code => entry module path
     */
    private static array $roleModuleMap = [
        'super_admin'    => '/admin/Dashboard.php',
        'content_admin'  => '/admin/Dashboard.php',
        'academic_admin' => '/academic/',       // quản lý đào tạo
        'teacher'        => '/lecturer/',       // giảng viên (teacher) để tránh nhầm với role 'lecturer' nếu có sau này
        'student'        => '/student/',        // sinh viên
        'accountant'     => '/accountant/',     // kế toán 
        'librarian'      => '/librarian/',      // thủ thư
    ];

    /**
     * Lấy đường dẫn vào module dựa theo role_code
     */
    public static function getModuleUrl(string $roleCode): string
    {
        $path = self::$roleModuleMap[$roleCode] ?? '/public/index.php';
        return BASE_URL . $path;
    }

    /**
     * Redirect người dùng về module tương ứng với role của họ
     */
    public static function redirectToModule(): void
    {
        $role = $_SESSION['role'] ?? '';
        $url  = self::getModuleUrl($role);
        header("Location: {$url}");
        exit;
    }

    /**
     * Kiểm tra xem role hiện tại có được phép truy cập module không
     * Dùng trong index.php của từng module để ngăn truy cập nhầm module
     *
     * @param array $allowedRoles  Danh sách role được phép
     */
    public static function guardModule(array $allowedRoles): void
    {
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
            header('Location: ' . BASE_URL . '/public/index.php');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, $allowedRoles)) {
            // Redirect về đúng module của role hiện tại
            self::redirectToModule();
        }
    }

    /**
     * Lấy tất cả role được hỗ trợ
     */
    public static function getSupportedRoles(): array
    {
        return array_keys(self::$roleModuleMap);
    }

    /**
     * Kiểm tra role có tồn tại trong hệ thống không
     */
    public static function isValidRole(string $role): bool
    {
        return array_key_exists($role, self::$roleModuleMap);
    }
}
