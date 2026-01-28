<!-- For Login.php Backend code -->
<?php
// session_start();
// require '../config/config.php';

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $username = $_POST['username'];
//     $password = $_POST['password'];

//     // Prepare and execute the SQL statement
//     $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
//     $stmt->bind_param("s", $username);
//     $stmt->execute();
//     $stmt->bind_result($storedPassword);

//     // Check if the user exists and verify the password
//     if ($stmt->fetch() && $password === $storedPassword) {
//         $_SESSION['authenticated'] = true;
//         header("Location: home.php");
//     } else {
//         $_SESSION['error'] = "Invalid username or password.";
//         header("Location: index.php");
//     }

//     $stmt->close();
//     $conn->close();
// }


// Sau khi người dùng nhập username + password
// $user = getUserFromDB($username);

// if ($user['locked_until'] > date('Y-m-d H:i:s')) {
//     echo "Tài khoản bị khóa đến " . $user['locked_until'];
// } else {
//     if (password_verify($password, $user['password_hash'])) {
//         // Đăng nhập thành công
//         updateUser($user['id'], [
//             'failed_attempts' => 0,
//             'last_login' => date('Y-m-d H:i:s')
//         ]);
//         echo "Đăng nhập thành công!";
//     } else {
//         // Sai mật khẩu
//         $attempts = $user['failed_attempts'] + 1;
//         if ($attempts >= 5) {
//             $lockedUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
//             updateUser($user['id'], [
//                 'failed_attempts' => $attempts,
//                 'locked_until' => $lockedUntil
//             ]);
//             echo "Sai mật khẩu quá nhiều lần. Tài khoản bị khóa 15 phút.";
//         } else {
//             updateUser($user['id'], ['failed_attempts' => $attempts]);
//             echo "Sai mật khẩu. Bạn còn " . (5 - $attempts) . " lần thử.";
//         }
//     }
// }



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
   KIỂM TRA MẬT KHẨU (HASH)
================================ */
if (!password_verify($password, $user['password_hash'])) {

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

// Reset failed_attempts + update last_login
$reset = $conn->prepare("
    UPDATE users 
    SET failed_attempts = 0, locked_until = NULL, last_login = NOW()
    WHERE id = ?
");
$reset->bind_param("i", $user['id']);
$reset->execute();

$_SESSION['authenticated'] = true;
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role_code']; // super_admin | content_admin | teacher | student


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
        header("Location: home.php");
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


