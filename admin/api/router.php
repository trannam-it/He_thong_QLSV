<?php
/**
 * API Router - Dynamic RBAC System
 * Định tuyến các API request đến Controller tương ứng
 */

// Start output buffering to prevent accidental output
ob_start();
ob_implicit_flush(false);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear any previous output and set headers before includes
ob_clean();

// Load dependencies
$base = dirname(__DIR__, 2);

// Try to load dependencies with error checking
$files_to_load = [
    $base . '/config/config.php',
    $base . '/core/AppRouter.php',
    $base . '/includes/auth_check.php',
    $base . '/admin/libs/Database.php',
    $base . '/admin/libs/DatabaseHelper.php',
    $base . '/admin/libs/Response.php',
    $base . '/admin/libs/Validator.php',
    $base . '/admin/libs/PermissionManager.php',
    $base . '/admin/libs/Auth.php',
    $base . '/admin/libs/ExportHelper.php',
    $base . '/admin/controllers/BaseController.php',
];

foreach ($files_to_load as $file) {
    if (!file_exists($file)) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'File not found: ' . $file]);
        exit;
    }
    require_once $file;
}

// Clean any output from includes
$buffered = ob_get_clean();
if (!empty(trim($buffered))) {
    error_log('Warning: Unexpected output from includes: ' . substr($buffered, 0, 200));
}

// Restart output buffer for API responses
ob_start();
ob_implicit_flush(false);

// Header
header('Content-Type: application/json; charset=UTF-8');

// Set error handler to capture fatal errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi máy chủ',
        'error' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
    exit;
}, E_ALL);

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi máy chủ (Fatal)',
            'error' => $error['message']
        ]);
    }
});

// Auth instance
$auth = new Auth($conn);

// Routing
$resource = $_GET['resource'] ?? '';
$action   = $_GET['action']   ?? '';
$method   = $_SERVER['REQUEST_METHOD'];

try {

// ─────────────────────────────────────────
// PERMISSIONS
// ─────────────────────────────────────────
if ($resource === 'permissions') {
    require_once $base . '/admin/controllers/PermissionController.php';
    $ctrl = new PermissionController($conn, $auth);

    switch ($action) {
        case 'index':       $ctrl->index();       break;
        case 'show':        $ctrl->show();        break;
        case 'store':       $ctrl->store();       break;
        case 'update':      $ctrl->update();      break;
        case 'delete':      $ctrl->delete();      break;
        case 'listGroups':  $ctrl->listGroups();  break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// ROLES
// ─────────────────────────────────────────
if ($resource === 'roles') {
    require_once $base . '/admin/controllers/RoleController.php';
    $ctrl = new RoleController($conn, $auth);

    switch ($action) {
        case 'index':                $ctrl->index();                break;
        case 'show':                 $ctrl->show();                 break;
        case 'store':                $ctrl->store();                break;
        case 'update':               $ctrl->update();               break;
        case 'delete':               $ctrl->delete();               break;
        case 'assign_permissions':   $ctrl->assignPermissions();    break;
        case 'get_permissions':      $ctrl->getPermissionsForAssign(); break;
        case 'assign_user':          $ctrl->assignUser();           break;
        case 'search_users':         $ctrl->searchUsers();          break;
        case 'users_by_role':        $ctrl->getUsersByRole();       break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// USERS
// ─────────────────────────────────────────
if ($resource === 'users') {
    require_once $base . '/admin/controllers/UserController.php';
    $ctrl = new UserController($conn, $auth);

    switch ($action) {
        case 'index':          $ctrl->index();          break;
        case 'show':           $ctrl->show();           break;
        case 'store':          $ctrl->store();          break;
        case 'update':         $ctrl->update();         break;
        case 'delete':         $ctrl->delete();         break;
        case 'toggleStatus':   $ctrl->toggleStatus();   break;
        case 'resetPassword':  $ctrl->resetPassword();  break;
        case 'unlockAccount':  $ctrl->unlockAccount();  break;
        case 'getActivity':    $ctrl->getActivity();    break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// STUDENTS
// ─────────────────────────────────────────
if ($resource === 'students') {
    require_once $base . '/admin/controllers/StudentController.php';
    $ctrl = new StudentController($conn, $auth);

    switch ($action) {
        case 'index':           $ctrl->index();           break;
        case 'show':            $ctrl->show();            break;
        case 'store':           $ctrl->store();           break;
        case 'update':          $ctrl->update();          break;
        case 'delete':          $ctrl->delete();          break;
        case 'changeStatus':    $ctrl->changeStatus();    break;
        case 'createAccount':   $ctrl->createAccount();   break;
        case 'resetPassword':   $ctrl->resetPassword();   break;
        case 'lockAccount':     $ctrl->lockAccount();     break;
        case 'getTranscript':   $ctrl->getTranscript();   break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// LECTURERS
// ─────────────────────────────────────────
if ($resource === 'lecturers') {
    require_once $base . '/admin/controllers/LecturerController.php';
    $ctrl = new LecturerController($conn, $auth);

    switch ($action) {
        case 'index':              $ctrl->index();              break;
        case 'show':               $ctrl->show();               break;
        case 'store':              $ctrl->store();              break;
        case 'update':             $ctrl->update();             break;
        case 'delete':             $ctrl->delete();             break;
        case 'nextCode':           $ctrl->nextCode();           break;
        case 'assignClasses':      $ctrl->assignClasses();      break;
        case 'getLecturerClasses': $ctrl->getLecturerClasses(); break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// FACULTIES
// ─────────────────────────────────────────
if ($resource === 'faculties') {
    require_once $base . '/admin/controllers/FacultyController.php';
    $ctrl = new FacultyController($conn, $auth);

    switch ($action) {
        case 'index':      $ctrl->index();      break;
        case 'show':       $ctrl->show();       break;
        case 'store':      $ctrl->store();      break;
        case 'update':     $ctrl->update();     break;
        case 'delete':     $ctrl->delete();     break;
        case 'stats':      $ctrl->stats();      break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// SUBJECTS
// ─────────────────────────────────────────
if ($resource === 'subjects') {
    require_once $base . '/admin/controllers/SubjectController.php';
    $ctrl = new SubjectController($conn, $auth);

    switch ($action) {
        case 'index':  $ctrl->index();  break;
        case 'store':  $ctrl->store();  break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// CLASSES
// ─────────────────────────────────────────
if ($resource === 'classes') {
    require_once $base . '/admin/controllers/ClassController.php';
    $ctrl = new ClassController($conn, $auth);

    switch ($action) {
        case 'index':  $ctrl->index();  break;
        case 'show':   $ctrl->show();   break;
        case 'store':  $ctrl->store();  break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// GRADES
// ─────────────────────────────────────────
if ($resource === 'grades') {
    require_once $base . '/admin/controllers/GradeController.php';
    $ctrl = new GradeController($conn, $auth);

    switch ($action) {
        case 'index':   $ctrl->index();   break;
        case 'store':   $ctrl->store();   break;
        case 'update':  $ctrl->update();  break;
        case 'export':  $ctrl->export();  break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// ENROLLMENTS
// ─────────────────────────────────────────
if ($resource === 'enrollments') {
    require_once $base . '/admin/controllers/EnrollmentController.php';
    $ctrl = new EnrollmentController($conn, $auth);

    switch ($action) {
        case 'index':    $ctrl->index();    break;
        case 'store':    $ctrl->store();    break;
        case 'cancel':   $ctrl->cancel();   break;
        case 'approve':  $ctrl->approve();  break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// AUDIT LOGS
// ─────────────────────────────────────────
if ($resource === 'audit_logs') {
    require_once $base . '/admin/controllers/AuditLogController.php';
    $ctrl = new AuditLogController($conn, $auth);

    switch ($action) {
        case 'index':  $ctrl->index();  break;
        case 'export': $ctrl->export(); break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// ─────────────────────────────────────────
// SEMESTERS
// ─────────────────────────────────────────
if ($resource === 'semesters') {
    require_once $base . '/admin/controllers/SemesterController.php';
    $ctrl = new SemesterController($conn, $auth);

    switch ($action) {
        case 'index':  $ctrl->index();  break;
        case 'store':  $ctrl->store();  break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        default:
            echo json_encode(['success'=>false,'message'=>'Action không hợp lệ']);
    }
    exit;
}

// Not found
echo json_encode(['success' => false, 'message' => "Resource '{$resource}' không tồn tại."]);

} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi máy chủ: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'line' => $e->getLine()
    ]);
}

ob_end_flush();
?>
;