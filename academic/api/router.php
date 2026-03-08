<?php

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * Academic Admin API Router
 *
 * Flow phân quyền chuẩn:
 *   [L1] Router: AppRouter::guardModule + RBACMiddleware::check
 *   [L2] Service check trong action (write operations)
 *
 * URL: /web_QLSV/academic/api/router.php?resource=xxx&action=yyy
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__, 2);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';
require_once $base . '/includes/auth_check.php';
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../models/AcademicModel.php';

header('Content-Type: application/json; charset=UTF-8');

// [LAYER 1] Guard: chỉ academic_admin được gọi API này
AppRouter::guardModule(['academic_admin']);

$resource = strtolower(trim($_GET['resource'] ?? ''));
$action   = strtolower(trim($_GET['action']   ?? ''));
$method   = $_SERVER['REQUEST_METHOD'];

$model = new AcademicModel($conn);

// [LAYER 1] RBAC: kiểm tra quyền cho endpoint này
require_once $base . '/core/RBACMiddleware.php';
require_once $base . '/admin/libs/Auth.php';
$auth = new Auth($conn);
RBACMiddleware::check($conn, $auth, 'academic', $resource, $action);

function apiOk(array $data = [], string $msg = 'OK'): void {
    echo json_encode(['success' => true, 'message' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function apiFail(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─────────────────────────────────────────
// STUDENTS
// ─────────────────────────────────────────
if ($resource === 'students') {
    switch ($action) {
        case 'list':
            $search    = trim($_GET['search']    ?? '');
            $status    = trim($_GET['status']    ?? '');
            $facultyId = (int)($_GET['faculty_id'] ?? 0);
            apiOk($model->getAllStudents($search, $status, $facultyId));

        case 'detail':
            $id = (int)($_GET['id'] ?? 0);
            $student = $model->getStudentById($id);
            if (!$student) apiFail('Không tìm thấy sinh viên.', 404);
            $grades = $model->getStudentGrades($id);
            apiOk(['student' => $student, 'grades' => $grades]);

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// SUBJECTS
// ─────────────────────────────────────────
if ($resource === 'subjects') {
    switch ($action) {
        case 'list':
            $search = trim($_GET['search'] ?? '');
            apiOk($model->getAllSubjects($search));

        case 'create':
            // [L2] Extra check: subjects.create
            if (!$auth->hasPermission('subjects.create')) apiFail('Bạn không có quyền thêm học phần.', 403);
            if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $data = [
                'subject_code'    => trim($input['subject_code'] ?? ''),
                'subject_name'    => trim($input['subject_name'] ?? ''),
                'credits'         => (int)($input['credits'] ?? 3),
                'faculty_id'      => (int)($input['faculty_id'] ?? 0) ?: null,
                'description'     => trim($input['description'] ?? ''),
                'prerequisite_id' => (int)($input['prerequisite_id'] ?? 0) ?: null,
            ];
            if (!$data['subject_code'] || !$data['subject_name']) apiFail('Thiếu thông tin bắt buộc.');
            if ($model->createSubject($data)) apiOk([], 'Thêm học phần thành công!');
            apiFail('Lỗi khi thêm học phần.');

        case 'update':
            // [L2] Extra check: subjects.edit
            if (!$auth->hasPermission('subjects.edit')) apiFail('Bạn không có quyền sửa học phần.', 403);
            if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['subject_id'] ?? 0);
            $data = [
                'subject_code'    => trim($input['subject_code'] ?? ''),
                'subject_name'    => trim($input['subject_name'] ?? ''),
                'credits'         => (int)($input['credits'] ?? 3),
                'faculty_id'      => (int)($input['faculty_id'] ?? 0) ?: null,
                'description'     => trim($input['description'] ?? ''),
                'prerequisite_id' => (int)($input['prerequisite_id'] ?? 0) ?: null,
            ];
            if ($model->updateSubject($id, $data)) apiOk([], 'Cập nhật thành công!');
            apiFail('Lỗi khi cập nhật.');

        case 'delete':
            // [L2] Extra check: subjects.delete
            if (!$auth->hasPermission('subjects.delete')) apiFail('Bạn không có quyền xóa học phần.', 403);
            if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['subject_id'] ?? 0);
            if ($model->deleteSubject($id)) apiOk([], 'Xóa thành công!');
            apiFail('Không thể xóa (đang được sử dụng).');

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// CLASSES
// ─────────────────────────────────────────
if ($resource === 'classes') {
    switch ($action) {
        case 'list':
            $search     = trim($_GET['search'] ?? '');
            $semesterId = (int)($_GET['semester_id'] ?? 0);
            apiOk($model->getAllClasses($search, $semesterId));

        case 'students':
            $classId = (int)($_GET['class_id'] ?? 0);
            apiOk($model->getClassStudents($classId));

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// SEMESTERS
// ─────────────────────────────────────────
if ($resource === 'semesters') {
    switch ($action) {
        case 'list':
            apiOk($model->getAllSemesters());

        case 'set_current':
            // [L2] Extra check: semesters.edit
            if (!$auth->hasPermission('semesters.edit')) apiFail('Bạn không có quyền cập nhật học kỳ.', 403);
            if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['semester_id'] ?? 0);
            if ($model->setCurrentSemester($id)) apiOk([], 'Cập nhật học kỳ hiện tại!');
            apiFail('Lỗi cập nhật.');

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// ENROLLMENTS
// ─────────────────────────────────────────
if ($resource === 'enrollments') {
    switch ($action) {
        case 'list':
            $semesterId = (int)($_GET['semester_id'] ?? 0);
            $status     = trim($_GET['status'] ?? '');
            apiOk($model->getAllEnrollments($semesterId, $status));
            break;

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// GRADES
// ─────────────────────────────────────────
if ($resource === 'grades') {
    switch ($action) {
        case 'by_class':
            $classId = (int)($_GET['class_id'] ?? 0);
            $grades  = $model->getGradesByClass($classId);
            $stats   = $model->getGradeStatsByClass($classId);
            apiOk(['grades' => $grades, 'stats' => $stats]);

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// REPORTS
// ─────────────────────────────────────────
if ($resource === 'reports') {
    switch ($action) {
        case 'semester_grades':
            $semesterId = (int)($_GET['semester_id'] ?? 0);
            apiOk($model->getGradeReportBySemester($semesterId));

        case 'faculty_stats':
            apiOk($model->getEnrollmentStatsByFaculty());

        case 'dashboard':
            apiOk($model->getDashboardStats());

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
}

// ─────────────────────────────────────────
// ENROLLMENT PERIODS
// ─────────────────────────────────────────
if ($resource === 'enrollment_periods') {
    require_once __DIR__ . '/../controllers/EnrollmentController.php';
    // $controller = new EnrollmentController($conn, null);
    $controller = new EnrollmentController($conn, $auth);

    switch ($action) {
        // case 'list':
        //     $periods = $controller->getPeriods();
        //     apiOk($periods, 'Danh sách kỳ đăng ký');
        //     break;

       case 'list':
            if (!$auth->hasPermission('enrollment.manage'))
                apiFail('Bạn không có quyền xem kỳ đăng ký.', 403);

            $controller->getPeriods();
            break;

        // case 'store':
        //     // [L2] Extra check: enrollments.manage
        //     if (!$auth->hasPermission('enrollments.view')) apiFail('Bạn không có quyền quản lý kỳ đăng ký.', 403);
        //     if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
        //     $controller->store();
        //     break;

        // case 'toggle_active':
        //     // [L2] Extra check
        //     if (!$auth->hasPermission('enrollments.view')) apiFail('Bạn không có quyền thay đổi trạng thái.', 403);
        //     if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
        //     $controller->toggleActive();
        //     break;

        case 'store':
            // if (!$auth->hasPermission('enrollment.manage_period'))
            if (!$auth->hasPermission('enrollment.manage'))
                apiFail('Bạn không có quyền quản lý kỳ đăng ký.', 403);
            if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
            $controller->store();
            break;

        case 'toggle_active':
            // if (!$auth->hasPermission('enrollment.manage_period'))
            if (!$auth->hasPermission('enrollment.manage'))
                apiFail('Bạn không có quyền thay đổi trạng thái.', 403);
            if ($method !== 'POST') apiFail('Method không hợp lệ.', 405);
            $controller->toggleActive();
            break;

        default:
            apiFail("Action '{$action}' không tồn tại.");
    }
    exit; 
}

apiFail("Resource '{$resource}' không tồn tại.", 404);