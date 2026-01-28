<?php
session_start();

// if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
//     $_SESSION['error'] = "⚠️ Please login to access this page.";
//     header("Location: ../public/index.php"); // adjust path if needed
//     exit();
// }


/**
 * Kiểm tra đăng nhập + phân quyền
 * @param array $allowedRoles Danh sách role được phép truy cập
 */
function authCheck(array $allowedRoles = [])
{
    // 1. Chưa đăng nhập
    if (
        !isset($_SESSION['authenticated']) ||
        $_SESSION['authenticated'] !== true
    ) {
        $_SESSION['error'] = "⚠️ Vui lòng đăng nhập để tiếp tục.";
        header("Location: /public/index.php");
        exit;
    }

    // 2. Kiểm tra role nếu có khai báo
    if (!empty($allowedRoles)) {

        if (
            !isset($_SESSION['role']) ||
            !in_array($_SESSION['role'], $allowedRoles)
        ) {
            $_SESSION['error'] = "⛔ Bạn không có quyền truy cập trang này.";
            header("Location: ../public/index.php");
            exit;
        }
    }
}

?>