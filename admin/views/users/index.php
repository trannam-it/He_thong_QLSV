<?php
/**
 * QUẢN LÝ NGƯỜI DÙNG - PHP Backend Hoàn Chỉnh
 * Có đầy đủ xử lý AJAX + Form + Database
 */

// Session & Config
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

function setFlash($msg, $type = 'success') {
    $_SESSION['flash_message'] = $msg;
    $_SESSION['flash_type'] = $type;
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/audit_log.php';
// require_once __DIR__ . '/../../includes/audit_log.php';

// Auth check
authCheck(['super_admin']);

// === XỬ LÝ FORM & AJAX ===
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ========== THÊM USER ==========
if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if (empty($username) || strlen($username) < 3) {
        $message = 'Username phải có ít nhất 3 ký tự';
        $messageType = 'danger';

    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email không hợp lệ';
        $messageType = 'danger';

    } elseif (empty($password) || strlen($password) < 6) {
        $message = 'Mật khẩu phải có ít nhất 6 ký tự';
        $messageType = 'danger';

    } elseif (empty($role)) {
        $message = 'Vui lòng chọn vai trò';
        $messageType = 'danger';

    } else {
        // Check trùng username/email
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $message = 'Username hoặc Email đã tồn tại';
            $messageType = 'danger';

        } else {
            // Insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password_hash, is_active, failed_attempts)
                 VALUES (?, ?, ?, 1, 0)"
            );
            $stmt->bind_param('sss', $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $user_id = $conn->insert_id;

                // Gán role
                $stmt = $conn->prepare("SELECT id FROM roles WHERE code = ?");
                $stmt->bind_param('s', $role);
                $stmt->execute();
                $role_result = $stmt->get_result();

                if ($role_result->num_rows > 0) {
                    $role_row = $role_result->fetch_assoc();
                    $stmt = $conn->prepare(
                        "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
                    );
                    $stmt->bind_param('ii', $user_id, $role_row['id']);
                    $stmt->execute();
                }

                // ✅ GHI AUDIT LOG
                writeAuditLog(
                    $conn,
                    $_SESSION['user_id'] ?? 0,
                    $_SESSION['username'] ?? 'ADMIN',
                    'CREATE',
                    'users',
                    $user_id,
                    null,
                    [
                        'username' => $username,
                        'email'    => $email
                    ]
                );

                // ✅ TẠO PROFILE ĐỒNG BỘ THEO ROLE
                $first_name = trim($_POST['first_name'] ?? '') ?: 'Chưa cập nhật';
                $last_name  = trim($_POST['last_name']  ?? '') ?: '';
                $faculty_id = intval($_POST['faculty_id'] ?? 1);

                if ($role === 'student') {
                    $maxRow  = $conn->query("SELECT MAX(CAST(SUBSTRING(student_code,3) AS UNSIGNED)) as mx FROM students")->fetch_assoc();
                    $newCode = 'SV' . str_pad(((int)($maxRow['mx'] ?? 0)) + 1, 3, '0', STR_PAD_LEFT);
                    $gender     = $_POST['gender']     ?? 'Other';
                    $birth_date = $_POST['birth_date'] ?? date('Y-01-01', strtotime('-20 years'));
                    $sp = $conn->prepare("INSERT INTO students (user_id, student_code, first_name, last_name, gender, birth_date, email, faculty_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Studying')");
                    $sp->bind_param('issssssi', $user_id, $newCode, $first_name, $last_name, $gender, $birth_date, $email, $faculty_id);
                    $sp->execute();
                } elseif ($role === 'teacher') {
                    $maxRow  = $conn->query("SELECT MAX(CAST(SUBSTRING(lecturer_code,3) AS UNSIGNED)) as mx FROM lecturers")->fetch_assoc();
                    $newCode = 'GV' . str_pad(((int)($maxRow['mx'] ?? 0)) + 1, 2, '0', STR_PAD_LEFT);
                    $degree  = $_POST['degree'] ?? 'Bachelor';
                    $sp = $conn->prepare("INSERT INTO lecturers (user_id, lecturer_code, first_name, last_name, email, faculty_id, degree) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $sp->bind_param('issssis', $user_id, $newCode, $first_name, $last_name, $email, $faculty_id, $degree);
                    $sp->execute();
                }

                setFlash('Thêm user thành công!', 'success');

            } else {
                setFlash('Lỗi: ' . $conn->error, 'danger');
            }
        }
    }
     redirectBack();
}

// ========== SỬA USER ==========
// ========== UPDATE USER ==========
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($id <= 0) {
        $message = 'User không hợp lệ';
        $messageType = 'danger';

    } elseif (empty($username) || strlen($username) < 3) {
        $message = 'Username phải có ít nhất 3 ký tự';
        $messageType = 'danger';

    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email không hợp lệ';
        $messageType = 'danger';

    } else {
        // Check trùng username/email
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?"
        );
        $stmt->bind_param('ssi', $username, $email, $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $message = 'Username hoặc Email đã được sử dụng';
            $messageType = 'danger';

        } else {
            // Update user
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "UPDATE users SET username = ?, email = ?, password_hash = ? WHERE id = ?"
                );
                $stmt->bind_param('sssi', $username, $email, $hashed_password, $id);
            } else {
                $stmt = $conn->prepare(
                    "UPDATE users SET username = ?, email = ? WHERE id = ?"
                );
                $stmt->bind_param('ssi', $username, $email, $id);
            }

            if ($stmt->execute()) {

                // Update role nếu có
                if (!empty($role)) {
                    $stmt = $conn->prepare("SELECT id FROM roles WHERE code = ?");
                    $stmt->bind_param('s', $role);
                    $stmt->execute();
                    $role_result = $stmt->get_result();

                    if ($role_result->num_rows > 0) {
                        $role_row = $role_result->fetch_assoc();

                        $stmt = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
                        $stmt->bind_param('i', $id);
                        $stmt->execute();

                        $stmt = $conn->prepare(
                            "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
                        );
                        $stmt->bind_param('ii', $id, $role_row['id']);
                        $stmt->execute();
                    }
                }

                // ✅ GHI AUDIT LOG (ĐÚNG HÀM)
                writeAuditLog(
                    $conn,
                    $_SESSION['user_id'] ?? 0,
                    $_SESSION['username'] ?? 'ADMIN',
                    'UPDATE',
                    'users',
                    $id,
                    null,
                    [
                        'username' => $username,
                        'email'    => $email
                    ]
                );
                setFlash('Cập nhật user thành công!', 'success');

            } else {
                setFlash('Lỗi: ' . $conn->error, 'danger');
            }
        }
    }
     redirectBack();
}


// ========== XÓA USER ==========
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {

        // Xóa role trước
        $stmt = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // Xóa user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {

            // ✅ GHI AUDIT LOG
            writeAuditLog(
                $conn,
                $_SESSION['user_id'] ?? 0,
                $_SESSION['username'] ?? 'ADMIN',
                'DELETE',
                'users',
                $id,
                null,
                null
            );

            setFlash('Xóa user thành công!', 'success');

        } else {
           setFlash('Lỗi: ' . $conn->error, 'danger');
        }
    }
        redirectBack();
}


// ========== TOGGLE STATUS ==========
if ($action === 'toggleStatus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        // Lấy trạng thái cũ
        $stmt = $conn->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $newStatus = 1 - (int)$user['is_active'];

            // Cập nhật trạng thái
            $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->bind_param('ii', $newStatus, $id);

            if ($stmt->execute()) {

                // ✅ GHI AUDIT LOG (đúng hàm – đúng tham số)
                writeAuditLog(
                    $conn,
                    $_SESSION['user_id'] ?? 0,
                    $_SESSION['username'] ?? 'ADMIN',
                    'TOGGLE_STATUS',
                    'users',
                    $id,
                    ['is_active' => $user['is_active']],
                    ['is_active' => $newStatus]
                );

                setFlash(($newStatus ? 'Mở khóa' : 'Khóa') . ' user thành công!','success'
            );
            }
        }
    }
     redirectBack();
}

// ========== RESET PASSWORD ==========
// ========== RESET PASSWORD ==========
// if ($action === 'resetPassword' && $_SERVER['REQUEST_METHOD'] === 'POST') {
//     $id = intval($_POST['id'] ?? 0);

//     if ($id > 0) {
//         // Tạo mật khẩu tạm
//         $temp_password   = bin2hex(random_bytes(6));
//         $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

//         // Update DB
//         $stmt = $conn->prepare("
//             UPDATE users 
//             SET password_hash = ?, failed_attempts = 0, locked_until = NULL 
//             WHERE id = ?
//         ");
//         $stmt->bind_param('si', $hashed_password, $id);

//         if ($stmt->execute()) {

//             // ✅ GHI AUDIT LOG (đúng hàm)
//             writeAuditLog(
//                 $conn,
//                 $_SESSION['user_id'] ?? 0,
//                 $_SESSION['username'] ?? 'ADMIN',
//                 'RESET_PASSWORD',
//                 'users',
//                 $id,
//                 null,
//                 null
//             );

//             $_SESSION['temp_password'] = $temp_password;

//             $message = 'Mật khẩu tạm thời: <strong>' . htmlspecialchars($temp_password) . '</strong>';
//             $messageType = 'success';
//         }
//     }
// }

// ========== RESET PASSWORD ==========
if ($action === 'resetPassword' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        // Mật khẩu mặc định
        $default_password = '123456';
        $hashed_password  = password_hash($default_password, PASSWORD_DEFAULT);

        // Update DB
        $stmt = $conn->prepare("
            UPDATE users 
            SET password_hash = ?, failed_attempts = 0, locked_until = NULL 
            WHERE id = ?
        ");
        $stmt->bind_param('si', $hashed_password, $id);

        if ($stmt->execute()) {

            // ✅ GHI AUDIT LOG
            writeAuditLog(
                $conn,
                $_SESSION['user_id'] ?? 0,
                $_SESSION['username'] ?? 'ADMIN',
                'RESET_PASSWORD',
                'users',
                $id,
                null,
                null
            );

            setFlash('Đã reset mật khẩu về 123456', 'success');
        }
    }
     redirectBack();
}

    function redirectBack() {
        header('Location: index.php');
        exit;
    }



// ========== LẤY DỮ LIỆU HIỂN THỊ ==========
// $search = $_GET['search'] ?? '';
// $role_filter = $_GET['role'] ?? '';
// $page = intval($_GET['page'] ?? 1);
// $limit = 20;
// $offset = ($page - 1) * $limit;

// $where = '1=1';
// $params = [];

// if (!empty($search)) {
//     $search_param = "%$search%";
//     $where .= " AND (u.username LIKE ? OR u.email LIKE ?)";
//     $params[] = $search_param;
//     $params[] = $search_param;
// }

// if (!empty($role_filter)) {
//     $where .= " AND r.code = ?";
//     $params[] = $role_filter;
// }

// $query = "SELECT u.*, GROUP_CONCAT(r.code SEPARATOR ', ') as roles, GROUP_CONCAT(r.name SEPARATOR ', ') as role_names 
//           FROM users u
//           LEFT JOIN user_roles ur ON u.id = ur.user_id
//           LEFT JOIN roles r ON ur.role_id = r.id
//           WHERE $where
//           GROUP BY u.id
//           ORDER BY u.id DESC
//           LIMIT ? OFFSET ?";

// $stmt = $conn->prepare($query);
// if (!empty($params)) {
//     $types = str_repeat('s', count($params)) . 'ii';
//     $stmt->bind_param($types, ...$params, $limit, $offset);
// } else {
//     $stmt->bind_param('ii', $limit, $offset);
// }
// $stmt->execute();
// $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// $count_query = "SELECT COUNT(DISTINCT u.id) as total FROM users u
//                 LEFT JOIN user_roles ur ON u.id = ur.user_id
//                 LEFT JOIN roles r ON ur.role_id = r.id
//                 WHERE $where";

// $stmt = $conn->prepare($count_query);
// if (!empty($params)) {
//     $count_params = array_slice($params, 0, count($params) - 2);
//     if (!empty($count_params)) {
//         $types = str_repeat('s', count($count_params));
//         $stmt->bind_param($types, ...$count_params);
//     }
// }
// $stmt->execute();
// $count_result = $stmt->get_result()->fetch_assoc();
// $total = intval($count_result['total']);
// $totalPages = ceil($total / $limit);

// ========== LẤY DỮ LIỆU HIỂN THỊ ==========
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

$where = '1=1';
$params = [];
$types = '';

if (!empty($search)) {
    $search_param = "%$search%";
    $where .= " AND (u.username LIKE ? OR u.email LIKE ?)";
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($role_filter)) {
    $where .= " AND r.code = ?";
    $params[] = $role_filter;
}

// Thêm limit & offset vào params
$params[] = $limit;
$params[] = $offset;
$types = str_repeat('s', count($params) - 2) . 'ii';

$query = "SELECT u.*, GROUP_CONCAT(r.code SEPARATOR ', ') as roles, GROUP_CONCAT(r.name SEPARATOR ', ') as role_names 
          FROM users u
          LEFT JOIN user_roles ur ON u.id = ur.user_id
          LEFT JOIN roles r ON ur.role_id = r.id
          WHERE $where
          GROUP BY u.id
          ORDER BY u.id DESC
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count total
$count_query = "SELECT COUNT(DISTINCT u.id) as total FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE $where";

$count_params = array_slice($params, 0, count($params) - 2);
$count_types = str_repeat('s', count($count_params));

$stmt = $conn->prepare($count_query);
if (!empty($count_params)) {
    $stmt->bind_param($count_types, ...$count_params);
}
$stmt->execute();
$count_result = $stmt->get_result()->fetch_assoc();
$total = intval($count_result['total']);
$totalPages = ceil($total / $limit);


$stmt = $conn->prepare("SELECT * FROM roles ORDER BY name");
$stmt->execute();
$all_roles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT * FROM permissions ORDER BY code");
$stmt->execute();
$all_permissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT faculty_id, faculty_name FROM faculties ORDER BY faculty_name");
$stmt->execute();
$all_faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../layout/header.php';
?>

<div class="topbar d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-people"></i> Quản Lý Người Dùng</h2>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
            <i class="bi bi-plus"></i> Thêm Mới User
        </button>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#roleModal">
            <i class="bi bi-lock"></i> Gán Quyền cho Role
        </button>
    </div>
</div>

<!-- Messages -->
<?php if (!empty($_SESSION['flash_message'])): ?>
<div class="alert alert-<?= $_SESSION['flash_type'] ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['flash_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['flash_message'], $_SESSION['flash_type']); 
endif; ?>

<!-- Search & Filter -->
<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Tìm username hoặc email..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <div class="col-md-6">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">-- Lọc theo vai trò --</option>
                <option value="super_admin" <?= $role_filter === 'super_admin' ? 'selected' : '' ?>>Admin Cấp Cao</option>
                <option value="content_admin" <?= $role_filter === 'content_admin' ? 'selected' : '' ?>>Admin Nội Dung</option>
                <option value="teacher" <?= $role_filter === 'teacher' ? 'selected' : '' ?>>Giảng Viên</option>
                <option value="student" <?= $role_filter === 'student' ? 'selected' : '' ?>>Sinh Viên</option>
            </select>
        </form>
    </div>
</div>

<!-- Table -->
<div class="table-responsive" style="background: white; border-radius: 8px;">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Vai Trò</th>
                <th>Lần Đăng Nhập Cuối</th>
                <th style="width: 110px;">Trạng Thái</th>
                <th style="width: 330px;">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><small><?= $user['id'] ?></small></td>
                    <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge bg-info">
                            <?= $user['role_names'] ?? 'N/A' ?>
                        </span>
                    </td>
                    <td>
                        <small>
                            <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '<em>Chưa đăng nhập</em>' ?>
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                            <i class="bi bi-<?= $user['is_active'] ? 'check-circle' : 'lock-fill' ?>"></i>
                            <?= $user['is_active'] ? 'Hoạt Động' : 'Khóa' ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#userModal" onclick="editUser(<?= $user['id'] ?>)" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#activityModal" onclick="showActivity(<?= $user['id'] ?>)" title="Hoạt động">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            <form method="POST" style="display: inline; margin: 0;">
                                <input type="hidden" name="action" value="toggleStatus">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-<?= $user['is_active'] ? 'danger' : 'success' ?>" title="<?= $user['is_active'] ? 'Khóa' : 'Mở khóa' ?>" onclick="return confirm('Xác nhận?')">
                                    <i class="bi bi-<?= $user['is_active'] ? 'lock-fill' : 'unlock' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" style="display: inline; margin: 0;">
                                <input type="hidden" name="action" value="resetPassword">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-secondary" title="Reset mật khẩu" onclick="return confirm('Đặt lại mật khẩu?')">
                                    <i class="bi bi-key"></i>
                                </button>
                            </form>
                            <form method="POST" style="display: inline; margin: 0;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa user này?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> Không có dữ liệu
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav aria-label="Page navigation" class="mt-3">
    <ul class="pagination">
        <?php if ($page > 1): ?>
        <li class="page-item">
            <a class="page-link" href="?page=1&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>">
                <i class="bi bi-chevron-double-left"></i>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>">
                Trước
            </a>
        </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
        <li class="page-item">
            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>">
                Tiếp
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>">
                <i class="bi bi-chevron-double-right"></i>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- Modal: Thêm/Sửa User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Thêm Người Dùng Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="store">
                    <input type="hidden" name="id" id="userId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" id="username" required minlength="3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật Khẩu <span class="text-danger" id="passwordRequired">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" minlength="6">
                            <small class="text-muted" id="passwordHint"><strong style="display: none;">Để trống sẽ không thay đổi khi sửa</strong></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vai Trò <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" id="role" required onchange="toggleProfileFields(this.value)">
                                <option value="">-- Chọn vai trò --</option>
                                <?php foreach ($all_roles as $r): ?>
                                <option value="<?= $r['code'] ?>"><?= $r['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Thông tin hồ sơ (hiện khi role = student hoặc teacher) -->
                    <div id="profileFields" style="display:none;">
                        <hr class="my-2">
                        <small class="text-muted d-block mb-2"><i class="bi bi-info-circle"></i> Hồ sơ sẽ được tạo tự động trong hệ thống</small>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ đệm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Nguyễn Văn">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="An">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                <select class="form-select" name="faculty_id" id="faculty_id">
                                    <?php foreach ($all_faculties as $f): ?>
                                    <option value="<?= $f['faculty_id'] ?>"><?= htmlspecialchars($f['faculty_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Student only -->
                            <div class="col-md-3 mb-3" id="genderField">
                                <label class="form-label">Giới tính</label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="Male">Nam</option>
                                    <option value="Female">Nữ</option>
                                    <option value="Other">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3" id="birthField">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" name="birth_date" id="birth_date" value="<?= date('Y-01-01', strtotime('-20 years')) ?>">
                            </div>
                            <!-- Teacher only -->
                            <div class="col-md-6 mb-3" id="degreeField" style="display:none;">
                                <label class="form-label">Học vị</label>
                                <select class="form-select" name="degree" id="degree">
                                    <option value="Bachelor">Cử nhân</option>
                                    <option value="Master">Thạc sĩ</option>
                                    <option value="PhD">Tiến sĩ</option>
                                    <option value="Professor">Giáo sư / PGS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Gán Quyền cho Role -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Gán Quyền cho Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label"><strong>Chọn Role</strong></label>
                    <select id="roleSelect" class="form-select" onchange="loadRolePermissions()">
                        <option value="">-- Chọn role --</option>
                        <?php foreach ($all_roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= $r['name'] ?> (<?= $r['code'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="permissionsContainer" class="d-none">
                    <h6>Danh sách Quyền hạn</h6>
                    <div id="permissionsList" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-info" onclick="saveRolePermissions()" id="savePermBtn" disabled>
                    <i class="bi bi-check"></i> Lưu Quyền
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Xem Hoạt Động -->
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Lịch Sử Hoạt Động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-striped">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Hành Động</th>
                            <th>Bảng</th>
                            <th style="width: 120px;">ID</th>
                            <th style="width: 160px;">Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody id="activityBody">
                        <tr><td colspan="4" class="text-center">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Hiện/ẩn extra fields theo role
function toggleProfileFields(role) {
    const pf = document.getElementById('profileFields');
    const df = document.getElementById('degreeField');
    const gf = document.getElementById('genderField');
    const bf = document.getElementById('birthField');
    const fn = document.getElementById('first_name');
    const ln = document.getElementById('last_name');

    if (role === 'student') {
        pf.style.display = '';
        gf.style.display = '';
        bf.style.display = '';
        df.style.display = 'none';
        fn.required = true; ln.required = true;
    } else if (role === 'teacher') {
        pf.style.display = '';
        gf.style.display = 'none';
        bf.style.display = 'none';
        df.style.display = '';
        fn.required = true; ln.required = true;
    } else {
        pf.style.display = 'none';
        fn.required = false; ln.required = false;
    }
}

// Reset form for new user
function resetForm() {
    document.getElementById('formAction').value = 'store';
    document.getElementById('userId').value = '';
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordRequired').textContent = '*';
    document.getElementById('passwordHint').querySelector('strong').style.display = 'none';
    document.getElementById('role').value = '';
    document.getElementById('modalTitle').textContent = 'Thêm Người Dùng Mới';
    // Reset profile section
    toggleProfileFields('');
    document.getElementById('first_name').value = '';
    document.getElementById('last_name').value  = '';
    document.getElementById('gender').value     = 'Male';
    document.getElementById('degree').value     = 'Bachelor';
}

// Edit user
function editUser(id) {
    fetch('ajax.php?action=getuser&id=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('formAction').value = 'update';
            document.getElementById('userId').value = id;
            document.getElementById('username').value = data.username;
            document.getElementById('email').value = data.email;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passwordRequired').textContent = '(Tuỳ chọn)';
            document.getElementById('passwordHint').querySelector('strong').style.display = 'block';
            document.getElementById('role').value = data.role_code || '';
            document.getElementById('modalTitle').textContent = 'Sửa: ' + data.username;
            // Ẩn profile fields khi sửa (chỉ dùng khi thêm mới)
            toggleProfileFields('');
        });
}

// Show activity history
function showActivity(userId) {
    fetch('ajax.php?action=getactivity&user_id=' + userId)

        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('activityBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Không có hoạt động</td></tr>';
            } else {
                tbody.innerHTML = data.map(log => `
                    <tr>
                        <td><span class="badge bg-secondary">${log.action}</span></td>
                        <td>${log.table_name}</td>
                        <td><strong>${log.record_id}</strong></td>
                        <td><small>${new Date(log.created_at).toLocaleString('vi-VN')}</small></td>
                    </tr>
                `).join('');
            }
            const modal = new bootstrap.Modal(document.getElementById('activityModal'));
            modal.show();
        });
}

// Load role permissions
function loadRolePermissions() {
    const roleId = document.getElementById('roleSelect').value;
    if (!roleId) {
        document.getElementById('permissionsContainer').classList.add('d-none');
        return;
    }

    fetch('ajax.php?action=getrolepermissions&role_id=' + roleId)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('permissionsList');
            let html = '';
            
            data.permissions.forEach(perm => {
                const checked = data.assigned.some(p => p.permission_id == perm.id) ? 'checked' : '';
                html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input perm-check" type="checkbox" name="permission_${perm.id}" 
                               value="${perm.id}" ${checked} id="perm_${perm.id}">
                        <label class="form-check-label" for="perm_${perm.id}">
                            <strong>${perm.code}</strong>
                            ${perm.description ? '<br><small class="text-muted">' + perm.description + '</small>' : ''}
                        </label>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            document.getElementById('permissionsContainer').classList.remove('d-none');
            document.getElementById('savePermBtn').disabled = false;
        });
}

// Save role permissions
function saveRolePermissions() {
    const roleId = document.getElementById('roleSelect').value;
    const permissions = Array.from(document.querySelectorAll('.perm-check:checked')).map(cb => cb.value);

    if (!roleId) {
        alert('Vui lòng chọn role');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'saveRolePermissions');
    formData.append('role_id', roleId);
    formData.append('permissions', JSON.stringify(permissions));

    fetch('ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✓ Cập nhật quyền thành công!');
            bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
        } else {
            alert('✗ Lỗi: ' + data.message);
        }
    });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>