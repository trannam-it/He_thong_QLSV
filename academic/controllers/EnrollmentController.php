<?php
/**
 * EnrollmentController (Academic) - Quản lý kỳ đăng ký học phần
 */
require_once __DIR__ . '/BaseAcademicController.php';
class EnrollmentController extends BaseAcademicController
{
    /**
     * Danh sách kỳ đăng ký
     */
    public function index(): void
    {
        // require permission for web browsing (redirect if not)
        // $this->auth->requirePermissionWeb('enrollment.manage_period');
        $this->auth->requirePermissionWeb('enrollment.manage');

        $stmt = $this->conn->prepare("
            SELECT * FROM enrollment_registration_periods
            ORDER BY year DESC, FIELD(semester, 'Spring', 'Summer', 'Fall')
        ");
        $stmt->execute();
        $periods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->conn->prepare("SELECT id FROM roles WHERE code = 'academic_admin' LIMIT 1");
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $academicRoleId = (int)($r['id'] ?? 0);

        $stmt = $this->conn->prepare("SELECT id FROM permissions WHERE code = 'enrollment.manage' LIMIT 1");
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $enrollPermId = (int)($r['id'] ?? 0);

        // $this->render('enrollment_periods/periods.php', ['periods' => $periods]);
        $this->render('enrollment_periods/periods.php', [
            'periods' => $periods,
            'academicRoleId' => $academicRoleId,
            'enrollPermId' => $enrollPermId
        ]);
    }

    /**
     * Tạo kỳ đăng ký mới
     */
    public function store(): void
    {
        // $this->auth->requirePermission('enrollment.manage_period');
        $this->auth->requirePermission('enrollment.manage');

        $semester = trim($_POST['semester'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $enrollment_open = trim($_POST['enrollment_open'] ?? '');
        $enrollment_close = trim($_POST['enrollment_close'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if (!$semester || !$year || !$enrollment_open || !$enrollment_close) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền tất cả thông tin bắt buộc']);
            return;
        }

        // Parse datetime
        $enroll_open_dt = DateTime::createFromFormat('Y-m-d\TH:i', $enrollment_open);
        $enroll_close_dt = DateTime::createFromFormat('Y-m-d\TH:i', $enrollment_close);

        if (!$enroll_open_dt || !$enroll_close_dt) {
            echo json_encode(['success' => false, 'message' => 'Định dạng ngày giờ không hợp lệ']);
            return;
        }

        if ($enroll_open_dt >= $enroll_close_dt) {
            echo json_encode(['success' => false, 'message' => 'Thời gian mở phải trước thời gian đóng']);
            return;
        }

        $open_str = $enroll_open_dt->format('Y-m-d H:i:s');
        $close_str = $enroll_close_dt->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("
            INSERT INTO enrollment_registration_periods
                (semester, year, enrollment_open, enrollment_close, is_active, note, created_by)
            VALUES (?, ?, ?, ?, 1, ?, ?)
            ON DUPLICATE KEY UPDATE
                enrollment_open = VALUES(enrollment_open),
                enrollment_close = VALUES(enrollment_close),
                note = VALUES(note)
        ");
        $userId = (int)$_SESSION['user_id'];
        $stmt->bind_param('sisssi', $semester, $year, $open_str, $close_str, $note, $userId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật kỳ đăng ký thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $stmt->error]);
        }
    }

    /**
     * Kích hoạt/Vô hiệu hóa kỳ đăng ký
     */
    public function toggleActive(): void
    {
        // $this->auth->requirePermission('enrollment.manage_period');
        $this->auth->requirePermission('enrollment.manage');

        $period_id = (int)($_POST['period_id'] ?? 0);
        $is_active = (int)($_POST['is_active'] ?? 0);

        if (!$period_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu period_id']);
            return;
        }

        // Disable other periods nếu activate sớm
        if ($is_active) {
            $stmt = $this->conn->prepare("
                UPDATE enrollment_registration_periods
                SET is_active = 0
                WHERE is_active = 1 AND period_id != ?
            ");
            $stmt->bind_param('i', $period_id);
            $stmt->execute();
        }

        $stmt = $this->conn->prepare("
            UPDATE enrollment_registration_periods
            SET is_active = ?
            WHERE period_id = ?
        ");
        $stmt->bind_param('ii', $is_active, $period_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật kỳ đăng ký thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $stmt->error]);
        }
    }

    /**
     * API: Lấy dữ liệu kỳ đăng ký (AJAX)
     */
    // public function getPeriods(): void
    // {
    //     // $this->auth->requirePermission('enrollment.manage_period');
    //     $this->auth->requirePermission('enrollment.manage');

    //     $stmt = $this->conn->prepare("
    //         SELECT * FROM enrollment_registration_periods
    //         ORDER BY year DESC, FIELD(semester, 'Spring', 'Summer', 'Fall')
    //     ");
    //     $stmt->execute();
    //     $periods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    //     echo json_encode(['success' => true, 'data' => $periods]);
    // }

    public function getPeriods(): void
    {
        // $this->auth->requirePermission('enrollment.view');
        $this->auth->requirePermission('enrollment.manage');

        $stmt = $this->conn->prepare("
            SELECT * FROM enrollment_registration_periods
            ORDER BY year DESC, FIELD(semester, 'Spring', 'Summer', 'Fall')
        ");
        $stmt->execute();
        $periods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success' => true, 'data' => $periods]);
    }
}
?>
