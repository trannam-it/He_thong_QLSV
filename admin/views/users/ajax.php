<?php
// ajax.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/audit_log.php';

authCheck(['super_admin']);

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ========= GET USER =========
if ($action === 'getuser') {
    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $stmt = $conn->prepare("
                SELECT r.code FROM roles r
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = ? LIMIT 1
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $role = $stmt->get_result()->fetch_assoc();

            unset($user['password_hash']);
            $user['role_code'] = $role['code'] ?? '';

            echo json_encode($user);
            exit;
        }
    }

    echo json_encode(['error' => 'User not found']);
    exit;
}

// ========= GET ACTIVITY =========
if ($action === 'getactivity') {
    $userId = intval($_GET['user_id'] ?? 0);

    if ($userId > 0) {
        $stmt = $conn->prepare("
            SELECT * FROM audit_logs
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        exit;
    }

    echo json_encode([]);
    exit;
}

// ========= GET ROLE PERMISSIONS =========
if ($action === 'getrolepermissions') {
    $roleId = intval($_GET['role_id'] ?? 0);

    if ($roleId > 0) {
        $permissions = $conn->query("SELECT * FROM permissions ORDER BY code")->fetch_all(MYSQLI_ASSOC);

        $stmt = $conn->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $assigned = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'permissions' => $permissions,
            'assigned'    => $assigned
        ]);
        exit;
    }

    echo json_encode(['permissions' => [], 'assigned' => []]);
    exit;
}

// ========= SAVE ROLE PERMISSIONS =========
if ($action === 'saveRolePermissions' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $roleId = intval($_POST['role_id'] ?? 0);
    $permissions = json_decode($_POST['permissions'] ?? '[]', true);

    if ($roleId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Role không hợp lệ']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
    $stmt->bind_param('i', $roleId);
    $stmt->execute();

    if (!empty($permissions)) {
        $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($permissions as $pid) {
            $pid = intval($pid);
            $stmt->bind_param('ii', $roleId, $pid);
            $stmt->execute();
        }
    }

    writeAuditLog(
        $conn,
        $_SESSION['user_id'] ?? 0,
        $_SESSION['username'] ?? 'ADMIN',
        'ASSIGN_PERMISSIONS',
        'role_permissions',
        $roleId,
        null,
        ['count' => count($permissions)]
    );

    echo json_encode(['success' => true]);
    exit;
}
// Nếu không có action hợp lệ
echo json_encode(['error' => 'Invalid action']);;
exit;
?>

<?php
// includes/audit_log.php   
// ========== AJAX: GET USER ==========
// if ($action === 'getuser') {
//     header('Content-Type: application/json');
//     $id = intval($_GET['id'] ?? 0);
    
//     if ($id > 0) {
//         $stmt = $conn->prepare("SELECT u.* FROM users u WHERE u.id = ?");
//         $stmt->bind_param('i', $id);
//         $stmt->execute();
//         $user = $stmt->get_result()->fetch_assoc();
        
//         if ($user) {
//             $stmt = $conn->prepare("SELECT r.code FROM roles r 
//                                    JOIN user_roles ur ON r.id = ur.role_id 
//                                    WHERE ur.user_id = ? LIMIT 1");
//             $stmt->bind_param('i', $id);
//             $stmt->execute();
//             $role_result = $stmt->get_result()->fetch_assoc();
            
//             $user['role_code'] = $role_result['code'] ?? '';
//             unset($user['password_hash']);
//             echo json_encode($user);
//             exit;
//         }
//     }
//     echo json_encode(['error' => 'User not found']);
//     exit;
// }


// // ========== AJAX: GET ACTIVITY ==========
// if ($action === 'getactivity') {
//     header('Content-Type: application/json');
//     $userId = intval($_GET['user_id'] ?? 0);
    
//     if ($userId > 0) {
//         $stmt = $conn->prepare("SELECT * FROM audit_logs 
//                                WHERE user_id = ? 
//                                ORDER BY created_at DESC 
//                                LIMIT 50");
//         $stmt->bind_param('i', $userId);
//         $stmt->execute();
//         $logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//         echo json_encode($logs);
//         exit;
//     }
//     echo json_encode([]);
//     exit;
// }


// // ========== AJAX: GET ROLE PERMISSIONS ==========
// if ($action === 'getrolepermissions') {
//     header('Content-Type: application/json');
//     $roleId = intval($_GET['role_id'] ?? 0);
    
//     if ($roleId > 0) {
//         $stmt = $conn->prepare("SELECT * FROM permissions ORDER BY code");
//         $stmt->execute();
//         $permissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
//         $stmt = $conn->prepare("SELECT rp.permission_id FROM role_permissions rp WHERE rp.role_id = ?");
//         $stmt->bind_param('i', $roleId);
//         $stmt->execute();
//         $assigned = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
//         echo json_encode([
//             'permissions' => $permissions,
//             'assigned' => $assigned
//         ]);
//         exit;
//     }
//     echo json_encode(['permissions' => [], 'assigned' => []]);
//     exit;
// }


// // ========== SAVE ROLE PERMISSIONS ==========
// if ($action === 'saveRolePermissions' && $_SERVER['REQUEST_METHOD'] === 'POST') {
//     header('Content-Type: application/json');

//     $roleId = intval($_POST['role_id'] ?? 0);
//     $permissions = json_decode($_POST['permissions'] ?? '[]', true);

//     if ($roleId > 0) {

//         // Xóa quyền cũ
//         $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
//         $stmt->bind_param('i', $roleId);
//         $stmt->execute();

//         // Thêm quyền mới
//         if (!empty($permissions)) {
//             $stmt = $conn->prepare(
//                 "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)"
//             );

//             foreach ($permissions as $perm_id) {
//                 $perm_id = intval($perm_id);
//                 $stmt->bind_param('ii', $roleId, $perm_id);
//                 $stmt->execute();
//             }
//         }

//         // ✅ GHI AUDIT LOG
//         writeAuditLog(
//             $conn,
//             $_SESSION['user_id'] ?? 0,
//             $_SESSION['username'] ?? 'ADMIN',
//             'ASSIGN_PERMISSIONS',
//             'role_permissions',
//             $roleId,
//             null,
//             ['count' => count($permissions)]
//         );

//         echo json_encode([
//             'success' => true,
//             'message' => 'Cập nhật quyền thành công'
//         ]);
//         exit;
//     }

//     echo json_encode([
//         'success' => false,
//         'message' => 'Role ID không hợp lệ'
//     ]);
//     exit;
// }


?>