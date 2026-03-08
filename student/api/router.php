<?php
/**
 * Student API Router
 * Entry point cho các AJAX request từ Student Portal.
 * URL: /web_QLSV/student/api/router.php?resource=xxx&action=yyy
 *
 * Flow phân quyền chuẩn:
 *   [L1] Router: AppRouter::guardModule + RBACMiddleware::check
 *   [L2] Controller service check trong từng action (nếu có)
 *
 * Các resource: enrollment, grades, schedule, tuition, scholarship, dormitory, library, profile
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__, 2);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';
require_once $base . '/includes/auth_check.php';
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../controllers/BaseStudentController.php';

header('Content-Type: application/json; charset=UTF-8');

// [LAYER 1] Guard: chỉ student được gọi API này
AppRouter::guardModule(['student']);

$resource = strtolower(trim($_GET['resource'] ?? ''));
$action   = strtolower(trim($_GET['action']   ?? ''));
// var_dump($resource, $action);
// exit;
$method   = $_SERVER['REQUEST_METHOD'];

/* =============================
   MAP ACTION TRƯỚC KHI CHECK RBAC
============================= */

$checkAction = $action;

if ($resource === 'enrollment' && $action === 'current_period') {
    $checkAction = 'view';
}

/* ============================= */

// [LAYER 1] RBAC Middleware check tại Router
require_once $base . '/core/RBACMiddleware.php';
require_once $base . '/admin/libs/Auth.php';
$auth = new Auth($conn);
RBACMiddleware::check($conn, $auth, 'student', $resource, $action);

// Helper response
function apiSuccess(array $data = [], string $message = 'OK'): void {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function apiError(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper lấy student ID
function getStudentId(StudentModel $model, int $userId): int {
    $info = $model->getOverviewByUserId($userId);
    return (int)($info['student_id'] ?? 0);
}

// ─────────────────────────────────────────
// ENROLLMENT - Đăng ký / hủy học phần
// ─────────────────────────────────────────
if ($resource === 'enrollment') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'list':
            // [L2] Service check đã được L1 xử lý (enrollment.view)
            $enrollments = $model->getMyEnrollments($studentId);
            apiSuccess($enrollments);

        case 'available':
            $fromYear = (int)($_GET['from_year'] ?? date('Y'));
            $available = $model->getAvailableClasses($studentId, $fromYear);
            apiSuccess($available);

        case 'register':
            // [L2] Extra check: enrollment.register
            if (!$auth->hasPermission('enrollment.register')) apiError('Bạn không có quyền đăng ký học phần.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input   = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $classId = (int)($input['class_id'] ?? 0);

            if ($classId <= 0)                                apiError('Mã lớp không hợp lệ.');
            if (!$model->classExists($classId))               apiError('Lớp không tồn tại.');
            if ($model->isAlreadyEnrolled($studentId, $classId)) apiError('Bạn đã đăng ký lớp này rồi.');
            if ($model->registerClass($studentId, $classId))  apiSuccess([], 'Đăng ký thành công!');
            apiError('Lỗi khi đăng ký: ' . $conn->error);

        case 'cancel':
            // [L2] Extra check: enrollment.cancel
            if (!$auth->hasPermission('enrollment.cancel')) apiError('Bạn không có quyền hủy đăng ký.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input        = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $enrollmentId = (int)($input['enrollment_id'] ?? 0);

            if ($model->cancelEnrollment($enrollmentId, $studentId)) apiSuccess([], 'Hủy đăng ký thành công.');
            apiError('Không thể hủy. Học phần không ở trạng thái "Đang học".');

        case 'current_period':
            // FIX: Sửa lỗi SQL - dùng prepare đúng cú pháp
            $semester = $_GET['semester'] ?? '';
            $year = (int)($_GET['year'] ?? date('Y'));

            // Xác định học kỳ mặc định nếu không truyền
            if (empty($semester)) {
                $month = (int)date('n');
                if ($month >= 1 && $month <= 5) {
                    $semester = 'Spring';
                } elseif ($month >= 6 && $month <= 8) {
                    $semester = 'Summer';
                } else {
                    $semester = 'Fall';
                }
            }

            $query = $conn->prepare(
                "SELECT * FROM enrollment_registration_periods
                 WHERE semester = ? AND year = ? AND is_active = 1
                 ORDER BY created_at DESC LIMIT 1"
            );
            if (!$query) apiError('Lỗi truy vấn: ' . $conn->error);

            $query->bind_param('si', $semester, $year);
            $query->execute();
            $result = $query->get_result();
            $period = $result->fetch_assoc();

            if (!$period) apiError('Không có kỳ đăng ký nào đang mở hiện tại.');
            apiSuccess($period, 'Thông tin kỳ đăng ký');

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'enrollment'.");
    }
}

// ─────────────────────────────────────────
// GRADES - Kết quả học tập
// ─────────────────────────────────────────
if ($resource === 'grades') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'all':
            $grades = $model->getAllGrades($studentId);
            apiSuccess($grades);

        case 'gpa':
            $gpa = $model->calculateGPA($studentId);
            apiSuccess($gpa);

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'grades'.");
    }
}

// ─────────────────────────────────────────
// SCHEDULE - Lịch học
// ─────────────────────────────────────────
if ($resource === 'schedule') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'list':
            $semester  = $_GET['semester'] ?? null;
            $year      = isset($_GET['year']) ? (int)$_GET['year'] : null;
            $schedule  = $model->getSchedule($studentId, $semester, $year);
            apiSuccess($schedule);

        case 'semesters':
            $semesters = $model->getEnrolledSemesters($studentId);
            apiSuccess($semesters);

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'schedule'.");
    }
}

// ─────────────────────────────────────────
// TUITION - Học phí
// ─────────────────────────────────────────
if ($resource === 'tuition') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'invoices':
            $invoices = $model->getAllTuitionInvoices($studentId);
            apiSuccess($invoices);

        case 'pay':
            // [L2] Extra check: tuition.pay
            if (!$auth->hasPermission('tuition.pay')) apiError('Bạn không có quyền nộp học phí.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input   = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $invId   = (int)($input['invoice_id'] ?? 0);
            $amount  = (float)str_replace(',', '', $input['amount'] ?? '0');

            if ($amount <= 0) apiError('Số tiền không hợp lệ.');
            $inv = $model->getTuitionInvoiceById($invId, $studentId);
            if (!$inv) apiError('Hoá đơn không tồn tại.', 404);
            if (in_array($inv['status'], ['Paid','Exempted'])) apiError('Hoá đơn đã thanh toán.');
            if ($model->payTuition($invId, (float)$inv['amount_paid'], (float)$inv['amount_due'], $amount))
                apiSuccess([], 'Nộp học phí thành công!');
            apiError('Lỗi khi cập nhật: ' . $conn->error);

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'tuition'.");
    }
}

// ─────────────────────────────────────────
// SCHOLARSHIP - Học bổng
// ─────────────────────────────────────────
if ($resource === 'scholarship') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'available':
            $list = $model->getAvailableScholarships($studentId);
            apiSuccess($list);

        case 'my_applications':
            $apps = $model->getMyScholarshipApplications($studentId);
            apiSuccess($apps);

        case 'apply':
            // [L2] Extra check: scholarship.apply
            if (!$auth->hasPermission('scholarship.apply')) apiError('Bạn không có quyền đăng ký học bổng.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $schId = (int)($input['scholarship_id'] ?? 0);

            if ($schId <= 0)                                          apiError('Học bổng không hợp lệ.');
            $sch = $model->getScholarshipById($schId);
            if (!$sch)                                                apiError('Học bổng không tồn tại hoặc đã đóng.');
            if ($sch['deadline'] && date('Y-m-d') > $sch['deadline']) apiError('Đã hết hạn đăng ký.');
            $myGpa = $model->getCompletedGPA($studentId);
            if ($sch['min_gpa'] !== null && ($myGpa === null || $myGpa < $sch['min_gpa']))
                apiError('GPA không đáp ứng yêu cầu tối thiểu (' . $sch['min_gpa'] . ').');
            if ($model->hasAppliedScholarship($studentId, $schId))   apiError('Bạn đã đăng ký học bổng này rồi.');
            if ($sch['quantity'] !== null && $model->countScholarshipApplicants($schId) >= (int)$sch['quantity'])
                apiError('Học bổng đã hết chỉ tiêu.');
            if ($model->applyScholarship($studentId, $schId))        apiSuccess([], 'Đã gửi đơn thành công!');
            apiError('Lỗi khi đăng ký: ' . $conn->error);

        case 'cancel':
            // [L2] Extra check: scholarship.cancel
            if (!$auth->hasPermission('scholarship.cancel')) apiError('Bạn không có quyền hủy đơn học bổng.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $appId = (int)($input['application_id'] ?? 0);
            if ($model->cancelScholarshipApplication($appId, $studentId)) apiSuccess([], 'Đã hủy đơn đăng ký.');
            apiError('Không thể hủy (đơn không ở trạng thái Đang chờ).');

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'scholarship'.");
    }
}

// ─────────────────────────────────────────
// DORMITORY - Ký túc xá
// ─────────────────────────────────────────
if ($resource === 'dormitory') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'available_rooms':
            $rooms = $model->getAvailableDormRooms();
            apiSuccess($rooms);

        case 'my_registrations':
            $regs = $model->getDormRegistrations($studentId);
            apiSuccess($regs);

        case 'register':
            // [L2] Extra check: dormitory.register
            if (!$auth->hasPermission('dormitory.register')) apiError('Bạn không có quyền đăng ký ký túc xá.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input     = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $roomId    = (int)($input['room_id']    ?? 0);
            $startDate = trim($input['start_date'] ?? '');
            $endDate   = trim($input['end_date']   ?? '');

            if ($roomId <= 0 || !$startDate || !$endDate) apiError('Vui lòng điền đầy đủ thông tin.');
            if ($startDate >= $endDate)                    apiError('Ngày kết thúc phải sau ngày bắt đầu.');
            if ($model->hasActiveDormRegistration($studentId)) apiError('Bạn đang có đơn đăng ký đang hoạt động.');
            $room = $model->getDormRoomById($roomId);
            if (!$room)                                    apiError('Phòng không tồn tại.');
            if ((int)$room['available_beds'] <= 0)         apiError('Phòng đã hết chỗ trống.');
            if ($model->registerDormRoom($studentId, $roomId, $startDate, $endDate))
                apiSuccess([], 'Đăng ký ký túc xá thành công! Vui lòng chờ phê duyệt.');
            apiError('Lỗi khi đăng ký: ' . $conn->error);

        case 'cancel':
            // [L2] Extra check: dormitory.cancel
            if (!$auth->hasPermission('dormitory.cancel')) apiError('Bạn không có quyền hủy đăng ký ký túc xá.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input          = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $registrationId = (int)($input['registration_id'] ?? 0);
            if ($model->cancelDormRegistration($registrationId, $studentId)) apiSuccess([], 'Hủy đăng ký thành công.');
            apiError('Không thể hủy đăng ký này.');

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'dormitory'.");
    }
}

// ─────────────────────────────────────────
// LIBRARY - Thư viện
// ─────────────────────────────────────────
if ($resource === 'library') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentId = getStudentId($model, $userId);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'books':
            $keyword = trim($_GET['q'] ?? '');
            $books   = $model->searchBooks($keyword);
            apiSuccess($books);

        case 'history':
            $history = $model->getBorrowHistory($studentId);
            apiSuccess($history);

        case 'borrow':
            // [L2] Extra check: library.borrow
            if (!$auth->hasPermission('library.borrow')) apiError('Bạn không có quyền mượn sách.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input   = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $bookId  = (int)($input['book_id']  ?? 0);
            $dueDays = (int)($input['due_days'] ?? 14);
            if ($dueDays < 1 || $dueDays > 30) $dueDays = 14;

            $model->updateOverdueBooks();
            if ($bookId <= 0)                                         apiError('Sách không hợp lệ.');
            if ($model->countActiveBorrows($studentId) >= 3)          apiError('Bạn đang mượn tối đa 3 quyển sách.');
            if ($model->isBookBorrowedByStudent($studentId, $bookId)) apiError('Bạn đang mượn cuốn này rồi.');
            $book = $model->getBookById($bookId);
            if (!$book)                                               apiError('Sách không tồn tại hoặc hết bản sao.');
            if ($model->borrowBook($studentId, $bookId, $dueDays))
                apiSuccess([], 'Mượn sách "' . htmlspecialchars($book['title']) . '" thành công!');
            apiError('Lỗi khi mượn sách: ' . $conn->error);

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'library'.");
    }
}

// ─────────────────────────────────────────
// PROFILE - Thông tin cá nhân
// ─────────────────────────────────────────
if ($resource === 'profile') {
    $model     = new StudentModel($conn);
    $userId    = (int)$_SESSION['user_id'];
    $studentInfo = $model->getOverviewByUserId($userId);
    $studentId = (int)($studentInfo['student_id'] ?? 0);

    if ($studentId === 0) apiError('Không tìm thấy thông tin sinh viên.', 404);

    switch ($action) {
        case 'info':
            apiSuccess($studentInfo);

        case 'update_contact':
            // [L2] Extra check: profile.edit
            if (!$auth->hasPermission('profile.edit')) apiError('Bạn không có quyền cập nhật thông tin.', 403);
            if ($method !== 'POST') apiError('Method không hợp lệ.', 405);
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $phone = trim($input['phone'] ?? '');
            $email = trim($input['email'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) apiError('Email không hợp lệ.');
            if (!preg_match('/^[0-9]{10}$/', $phone))       apiError('Số điện thoại phải là 10 chữ số.');
            if ($model->updateContactInfo($studentId, $phone, $email)) apiSuccess([], 'Cập nhật thành công!');
            apiError('Lỗi khi cập nhật.');

        default:
            apiError("Action '{$action}' không tồn tại cho resource 'profile'.");
    }
}

apiError("Resource '{$resource}' không tồn tại.", 404);
