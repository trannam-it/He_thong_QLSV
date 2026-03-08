<?php
/**
 * Lecturer API Router
 *
 * Entry point cho các AJAX/API request từ Lecturer Portal.
 * URL: /web_QLSV/lecturer/api/router.php?resource=xxx&action=yyy
 *
 * Các resource:
 *   - grades:     lưu điểm, lấy danh sách điểm
 *   - attendance: lưu điểm danh, lịch sử
 *   - classes:    danh sách lớp
 *   - profile:    thông tin cá nhân
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__, 2);  // thư mục gốc web_QLSV
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';
require_once $base . '/includes/auth_check.php';
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../models/LecturerModel.php';
require_once __DIR__ . '/../controllers/BaseLecturerController.php';

// JSON only
header('Content-Type: application/json; charset=UTF-8');

// Guard: chỉ teacher được gọi API này
AppRouter::guardModule(['teacher']);

$resource = strtolower(trim($_GET['resource'] ?? ''));
$action   = strtolower(trim($_GET['action']   ?? ''));
$method   = $_SERVER['REQUEST_METHOD'];

// Helper responses
function apiSuccess(array $data = [], string $message = 'OK'): void
{
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function apiError(string $message, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// Init model
$model     = new LecturerModel($conn);
$userId    = (int)$_SESSION['user_id'];
$lecturer  = $model->getOverviewByUserId($userId);
$lecturerId = (int)($lecturer['lecturer_id'] ?? 0);

// RBAC: kiểm tra quyền theo PermissionMap
require_once __DIR__ . '/../../core/RBACMiddleware.php';
require_once __DIR__ . '/../../admin/libs/Auth.php';
$auth = new Auth($conn);
RBACMiddleware::check($conn, $auth, 'lecturer', $resource, $action);

if ($lecturerId === 0) {
    apiError('Không tìm thấy thông tin giảng viên.', 404);
}

// ─────────────────────────────────────────
// GRADES - Nhập / xem điểm
// ─────────────────────────────────────────
if ($resource === 'grades') {
    switch ($action) {
        case 'list':
            $classId   = (int)($_GET['class_id'] ?? 0);
            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);
            $students = $model->getStudentsWithGrades($classId);
            apiSuccess($students);

        case 'stats':
            $classId   = (int)($_GET['class_id'] ?? 0);
            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);
            $stats = $model->getGradeStats($classId);
            apiSuccess($stats);

        case 'save':
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input   = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $classId = (int)($input['class_id'] ?? 0);
            $scores  = $input['scores'] ?? [];  // [enrollment_id => score]

            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);

            $saved = 0;
            foreach ($scores as $enrollId => $scoreRaw) {
                $enrollId = (int)$enrollId;
                $score    = ($scoreRaw === '' || $scoreRaw === null) ? null : floatval($scoreRaw);
                if ($score !== null && ($score < 0 || $score > 100)) continue;
                $model->saveGrade($enrollId, $score);
                $saved++;
            }
            apiSuccess(['saved' => $saved], "Đã lưu điểm cho $saved sinh viên!");

        default:
            apiError("Action '$action' không tồn tại cho resource 'grades'.");
    }
}

// ─────────────────────────────────────────
// ATTENDANCE - Điểm danh
// ─────────────────────────────────────────
if ($resource === 'attendance') {
    switch ($action) {
        case 'history':
            $classId   = (int)($_GET['class_id'] ?? 0);
            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);
            $history = $model->getAttendanceHistory($classId);
            apiSuccess($history);

        case 'by_date':
            $classId   = (int)($_GET['class_id'] ?? 0);
            $date      = trim($_GET['date'] ?? '');
            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);
            if (!$date)      apiError('Thiếu tham số date.');
            $att = $model->getAttendanceByDate($classId, $date);
            apiSuccess($att);

        case 'save':
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $classId  = (int)($input['class_id']  ?? 0);
            $attDate  = trim($input['att_date']   ?? '');
            $statuses = $input['statuses']         ?? [];
            $notes    = $input['notes']            ?? [];
            $allowed  = ['Present', 'Absent', 'Late', 'Excused'];

            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);
            if (!$attDate)   apiError('Thiếu ngày điểm danh.');

            $saved = 0;
            foreach ($statuses as $sid => $status) {
                $sid    = (int)$sid;
                $status = in_array($status, $allowed) ? $status : 'Present';
                $note   = ($notes[$sid] ?? '') === '' ? null : trim($notes[$sid]);
                if (!$model->isStudentEnrolled($sid, $classId)) continue;
                $model->saveAttendance($classId, $sid, $attDate, $status, $note);
                $saved++;
            }
            apiSuccess(['saved' => $saved], "Đã lưu điểm danh $saved sinh viên!");

        case 'summary':
            $classId   = (int)($_GET['class_id'] ?? 0);
            $classInfo = $classId ? $model->getClassOfLecturer($classId, $lecturerId) : null;
            if (!$classInfo) apiError('Lớp không hợp lệ.', 403);
            $summary = $model->getStudentAttendanceSummary($classId);
            apiSuccess($summary);

        default:
            apiError("Action '$action' không tồn tại cho resource 'attendance'.");
    }
}

// ─────────────────────────────────────────
// CLASSES - Danh sách lớp
// ─────────────────────────────────────────
if ($resource === 'classes') {
    switch ($action) {
        case 'list':
            $classes = $model->getAllLecturerClasses($lecturerId);
            apiSuccess($classes);

        case 'stats':
            $stats = $model->getClassesStats($lecturerId);
            apiSuccess($stats);

        default:
            apiError("Action '$action' không tồn tại cho resource 'classes'.");
    }
}

// ─────────────────────────────────────────
// PROFILE - Thông tin giảng viên
// ─────────────────────────────────────────
if ($resource === 'profile') {
    switch ($action) {
        case 'info':
            apiSuccess($lecturer);

        case 'update_contact':
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $phone = trim($input['phone'] ?? '');
            $email = trim($input['email'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) apiError('Email không hợp lệ.');
            if (!preg_match('/^[0-9]{10}$/', $phone))       apiError('Số điện thoại phải là 10 chữ số.');
            if ($model->updateContact($lecturerId, $phone, $email)) apiSuccess([], 'Cập nhật thành công!');
            apiError('Lỗi khi cập nhật.');

        default:
            apiError("Action '$action' không tồn tại cho resource 'profile'.");
    }
}

// ─────────────────────────────────────────
// CLASS REGISTRATION - Đăng ký dạy lớp
// ─────────────────────────────────────────
if ($resource === 'class_registration') {
    switch ($action) {
        case 'available':
            // Get available classes for lecturer to register
            $classes = $model->getAvailableClassesForRegistration($lecturerId);
            apiSuccess($classes, 'Danh sách lớp có thể đăng ký');

        case 'register':
            // Register lecturer for a class
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input   = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $classId = (int)($input['class_id'] ?? 0);

            if ($classId <= 0) apiError('Mã lớp không hợp lệ.');

            // Check if lecturer already teaches this class
            $stmt = $conn->prepare("SELECT class_id FROM classes WHERE class_id = ? AND lecturer_id = ?");
            $stmt->bind_param('ii', $classId, $lecturerId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                apiError('Bạn đã đăng ký dạy lớp này rồi.');
            }

            // Update class to assign this lecturer
            $stmt = $conn->prepare("UPDATE classes SET lecturer_id = ? WHERE class_id = ?");
            $stmt->bind_param('ii', $lecturerId, $classId);
            if ($stmt->execute()) {
                apiSuccess([], 'Đăng ký dạy lớp thành công!');
            }
            apiError('Lỗi khi đăng ký: ' . $conn->error);

        default:
            apiError("Action '$action' không tồn tại cho resource 'class_registration'.");
    }
}

// ─────────────────────────────────────────
// Not found
// ─────────────────────────────────────────
apiError("Resource '$resource' không tồn tại.", 404);
