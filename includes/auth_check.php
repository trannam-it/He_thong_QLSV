<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
//     $_SESSION['error'] = "⚠️ Please login to access this page.";
//     header("Location: ../public/index.php"); // adjust path if needed
//     exit();
// }


/**
 */
// function authCheck(array $allowedRoles = [])
// {
//     // 1. Chưa đăng nhập
//     if (
//         !isset($_SESSION['authenticated']) ||
//         $_SESSION['authenticated'] !== true
//     ) {
//         $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục.";
//         header("Location: /public/index.php");
//         exit;
//     }

//     // 2. Kiểm tra role nếu có khai báo
//     if (!empty($allowedRoles)) {

//         if (
//             !isset($_SESSION['role']) ||
//             !in_array($_SESSION['role'], $allowedRoles)
//         ) {
//             $_SESSION['error'] = "Bạn không có quyền truy cập trang này.";
//             header("Location: ../public/index.php");
//             exit;
//         }
//     }
// }

?>

<?php
/**
 * Auth Check - Kiểm tra xác thực người dùng
 * PHẢI được gọi TRƯỚC bất kỳ output nào (headers, HTML, echo, etc)
 */

// Khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

/**
 * Kiểm tra đăng nhập + phân quyền
 * @param array $allowedRoles Danh sách role được phép truy cập
 */
function authCheck(array $allowedRoles = [])
{
    // 1. Chưa đăng nhập
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục.";
        header("Location: ../public/index.php");
        exit;
    }

    // 2. Kiểm tra role nếu có khai báo
    if (!empty($allowedRoles)) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này.";
            header("Location: ../public/index.php");
            exit;
        }
    }
}

/**
 * Check Permission - Kiểm tra quyền hạn cụ thể
 */
function checkPermission($permission, $conn = null)
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    // Nếu là super_admin, cho phép tất cả
    if ($_SESSION['role'] === 'super_admin') {
        return true;
    }

    // Kiểm tra quyền trong database
    if ($conn) {
        $stmt = $conn->prepare(
            "SELECT 1 FROM role_permissions rp
             JOIN roles r ON rp.role_id = r.id
             JOIN user_roles ur ON r.id = ur.role_id
             JOIN permissions p ON rp.permission_id = p.id
             WHERE ur.user_id = ? AND p.code = ?
             LIMIT 1"
        );
        if ($stmt) {
            $stmt->bind_param('is', $_SESSION['user_id'], $permission);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0;
        }
    }

    return false;
}

?>