<?php
/**
 * Login Handler - Xử lý đăng nhập hệ thống
 * Hỗ trợ RBAC (Role-Based Access Control)
 * Có tính năng: khóa tài khoản sau 5 lần nhập sai, audit log
 */

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/audit_log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

/* ===============================
   LẤY USER + ROLE (RBAC)
================================ */
$sql = "
    SELECT 
        u.id,
        u.username,
        u.password_hash,
        u.is_active,
        u.failed_attempts,
        u.locked_until,
        r.code AS role_code
    FROM users u
    JOIN user_roles ur ON u.id = ur.user_id
    JOIN roles r ON ur.role_id = r.id
    WHERE u.username = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$user = $result->fetch_assoc()) {
    // Không có user -> không thể tăng failed_attempts vì không biết id
    // Nhưng vẫn có thể ghi log hệ thống (user_id = 0)
    writeAuditLog(
        $conn,
        0,
        $username,
        'LOGIN_FAIL',
        'users',
        null,
        null,
        ['reason' => 'USER_NOT_FOUND']
    );

    $_SESSION['error'] = "Tài khoản không tồn tại.";
    header("Location: index.php");
    exit;
}

/* ===============================
   KIỂM TRA TRẠNG THÁI USER
================================ */
if ($user['is_active'] == 0) {
    $_SESSION['error'] = "Tài khoản đã bị vô hiệu hóa.";
    header("Location: index.php");
    exit;
}

if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
    $_SESSION['error'] = "Tài khoản bị khóa đến " . $user['locked_until'];
    header("Location: index.php");
    exit;
}

/* ===============================
   KIỂM TRA MẬT KHẨU (PLAIN TEXT)
   LƯU Ý: Đang dùng so sánh trực tiếp, không hash
================================ */
if ($password !== $user['password_hash']) {

    // Tăng failed_attempts
    $failed = (int)$user['failed_attempts'] + 1;
    $lockUntil = null;

    if ($failed >= 5) {
        $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    }

    $update = $conn->prepare("
        UPDATE users 
        SET failed_attempts = ?, locked_until = ?
        WHERE id = ?
    ");
    // locked_until có thể NULL => bind kiểu s vẫn OK với mysqli (NULL sẽ được gửi là NULL)
    $update->bind_param("isi", $failed, $lockUntil, $user['id']);
    $update->execute();

    // 🔐 AUDIT LOG – LOGIN FAIL
    writeAuditLog(
        $conn,
        (int)$user['id'],
        $user['username'],
        'LOGIN_FAIL',
        'users',
        (int)$user['id'],
        null,
        ['failed_attempts' => $failed, 'locked_until' => $lockUntil]
    );

    $_SESSION['error'] = "Mật khẩu không chính xác.";
    header("Location: index.php");
    exit;
}

/* ===============================
   ĐĂNG NHẬP THÀNH CÔNG
================================ */
session_regenerate_id(true);

// Reset trạng thái login fail
resetLoginFail($conn, $user['id']);

// Lấy profile user
$profile = getUserProfile($conn, $user['id']);

// Lưu session
$_SESSION['authenticated'] = true;
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $profile['role_code'];

$_SESSION['user'] = [
    'id'        => $profile['user_id'],
    'fullname'  => $profile['full_name'],
    'role_name' => $profile['role_name'],
    'class'     => $profile['class_name'] ?? null
];


/* ===============================
   ĐĂNG NHẬP THÀNH CÔNG
================================ */
// session_regenerate_id(true);

// // ===============================
// // LẤY PROFILE USER (VIEW)
// // ===============================
// $profileStmt = $conn->prepare("
//     SELECT *
//     FROM v_user_profile
//     WHERE user_id = ?
//     LIMIT 1
// ");
// $profileStmt->bind_param("i", $user['id']);
// $profileStmt->execute();
// $profile = $profileStmt->get_result()->fetch_assoc();


// // Reset failed_attempts + update last_login
// $reset = $conn->prepare("
//     UPDATE users 
//     SET failed_attempts = 0, locked_until = NULL, last_login = NOW()
//     WHERE id = ?
// ");
// $reset->bind_param("i", $user['id']);
// $reset->execute();

// $_SESSION['authenticated'] = true;
// $_SESSION['user_id']  = $user['id'];
// $_SESSION['username'] = $user['username'];
// $_SESSION['role']     = $user['role_code']; // super_admin | content_admin | teacher | student

// $_SESSION['user'] = [
//     'id'        => $profile['user_id'],
//     'fullname'  => $profile['full_name'],
//     'role_name' => $profile['role_name'],
//     'class'     => $profile['class_name'] ?? null
// ];


/* ===============================
   🔐 AUDIT LOG – LOGIN SUCCESS
================================ */
writeAuditLog(
    $conn,
    (int)$user['id'],
    $user['username'],
    'LOGIN_SUCCESS',
    'users',
    (int)$user['id'],
    null,
    ['role' => $user['role_code']]
);


/* ===============================
   PHÂN LUỒNG THEO ROLE
================================ */
switch ($user['role_code']) {

    case 'super_admin':
    case 'content_admin':
        header("Location: /web_QLSV/admin/Dashboard.php");
        break;


    case 'teacher':
        header("Location: teacher.php");
        break;

    case 'student':
        header("Location: student.php");
        break;

    default:
        header("Location: index.php");
}
exit;

function resetLoginFail(mysqli $conn, int $userId): void
{
    $stmt = $conn->prepare("
        UPDATE users
        SET failed_attempts = 0,
            locked_until = NULL,
            last_login = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

function getUserProfile(mysqli $conn, int $userId): array
{
    // Lấy thông tin user + role
    $stmt = $conn->prepare("
        SELECT 
            u.id as user_id,
            u.username,
            u.email,
            r.code as role_code,
            r.name as role_name
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    
    if (!$profile) {
        return [];
    }
    
    // Lấy full_name và class_name tùy theo role
    if ($profile['role_code'] === 'student') {
        $stmtStudent = $conn->prepare("
            SELECT 
                CONCAT(s.first_name, ' ', s.last_name) as full_name,
                bc.base_class_name as class_name
            FROM students s
            LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
            WHERE s.user_id = ?
            LIMIT 1
        ");
        $stmtStudent->bind_param("i", $userId);
        $stmtStudent->execute();
        $studentData = $stmtStudent->get_result()->fetch_assoc();
        
        if ($studentData) {
            $profile['full_name'] = $studentData['full_name'];
            $profile['class_name'] = $studentData['class_name'];
        } else {
            $profile['full_name'] = $profile['username'];
        }
    } elseif ($profile['role_code'] === 'teacher') {
        $stmtLecturer = $conn->prepare("
            SELECT 
                CONCAT(l.first_name, ' ', l.last_name) as full_name,
                l.degree
            FROM lecturers l
            WHERE l.user_id = ?
            LIMIT 1
        ");
        $stmtLecturer->bind_param("i", $userId);
        $stmtLecturer->execute();
        $lecturerData = $stmtLecturer->get_result()->fetch_assoc();
        
        if ($lecturerData) {
            $profile['full_name'] = $lecturerData['full_name'];
            $profile['degree'] = $lecturerData['degree'];
        } else {
            $profile['full_name'] = $profile['username'];
        }
    } else {
        // Admin - dùng username làm full_name
        $profile['full_name'] = $profile['username'];
    }
    
    return $profile;
}


