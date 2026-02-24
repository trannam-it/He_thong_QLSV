

<?php
/**
 * ✅ CORRECT API ROUTER
 * - KHÔNG gọi authCheck()
 * - KHÔNG dùng header redirect
 * - CHỈ trả JSON
 * - Controllers tự check quyền bằng $this->auth->requirePermissionAPI()
 */

// echo json_encode(['router' => 'alive']);     // test xem router chạy được không
// exit;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// register_shutdown_function(function () {
//     $error = error_get_last();
//     if ($error) {
//         echo json_encode([
//             'fatal' => true,
//             'type' => $error['type'],
//             'message' => $error['message'],
//             'file' => $error['file'],
//             'line' => $error['line'],
//         ]);
//     }
// });


// Đảm bảo output là JSON
header('Content-Type: application/json; charset=utf-8');

// Disable any error output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Custom error handler - but don't die, just log
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log the error but don't interrupt execution
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    // Return false to continue with PHP's error handling
    return false;
});

// Session (nếu cần check $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// KHÔNG gọi authCheck() ở đây!
// require_once __DIR__ . '/../../includes/auth_check.php';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../libs/Database.php';
require_once __DIR__ . '/../libs/Response.php';
require_once __DIR__ . '/../libs/Validator.php';
require_once __DIR__ . '/../libs/Auth.php';
require_once __DIR__ . '/../../includes/audit_log.php';
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/StudentController.php';
require_once __DIR__ . '/../controllers/LecturerController.php';
require_once __DIR__ . '/../controllers/FacultyController.php';
require_once __DIR__ . '/../controllers/SubjectController.php';
require_once __DIR__ . '/../controllers/ClassController.php';
require_once __DIR__ . '/../controllers/BaseClassController.php';
require_once __DIR__ . '/../controllers/GradeController.php';
require_once __DIR__ . '/../controllers/AuditLogController.php';

$module = $_GET['module'] ?? $_POST['module'] ?? null;
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

if (!$module) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Module required']));
}

$controllers = [
    'users' => 'UserController',
    'students' => 'StudentController',
    'lecturers' => 'LecturerController',
    'faculties' => 'FacultyController',
    'subjects' => 'SubjectController',
    'classes' => 'ClassController',
    'base_classes' => 'BaseClassController',
    'grades' => 'GradeController',
    'audit_logs' => 'AuditLogController'
];

if (!isset($controllers[$module])) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Invalid module']));
}

try {
    $controllerClass = $controllers[$module];
    $controller = new $controllerClass($conn);
    echo $controller->call($action);
} catch (Error $e) {
    http_response_code(500);
    error_log('API Error - ' . $module . '::' . $action . ' - ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'class' => get_class($e)
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('API Error - ' . $module . '::' . $action . ' - ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>