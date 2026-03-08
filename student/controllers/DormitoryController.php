<?php
/**
 * DormitoryController - Ký túc xá sinh viên
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'dormitory.view'
 *   [L2] Controller: $this->requirePermission('dormitory.view')
 *   [L3] Actions: 'dormitory.register' / 'dormitory.cancel'
 */
class DormitoryController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('dormitory.view');

        $msg   = $this->getFlash('success') ?? '';
        $error = $this->getFlash('error')   ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['register_room'])) {
                [$msg, $error] = $this->handleRegister();
            } elseif (isset($_POST['cancel_registration'])) {
                [$msg, $error] = $this->handleCancel();
            }
        }

        $availableRooms  = $this->model->getAvailableDormRooms();
        $myRegistrations = $this->model->getDormRegistrations($this->studentId);

        $this->render('dormitory/index.php', [
            'pageTitle'       => 'Ký túc xá',
            'availableRooms'  => $availableRooms,
            'myRegistrations' => $myRegistrations,
            'msg'             => $msg,
            'error'           => $error,
            // Quyền cho view
            'canRegister'     => $this->auth->hasPermission('dormitory.register'),
            'canCancel'       => $this->auth->hasPermission('dormitory.cancel'),
        ]);
    }

    private function handleRegister(): array
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('dormitory.register')) {
            return ['', 'Bạn không có quyền đăng ký ký túc xá.'];
        }

        $roomId    = (int)($_POST['room_id']    ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate   = trim($_POST['end_date']   ?? '');

        if ($roomId <= 0 || !$startDate || !$endDate)
            return ['', 'Vui lòng điền đầy đủ thông tin.'];
        if ($startDate >= $endDate)
            return ['', 'Ngày kết thúc phải sau ngày bắt đầu.'];
        if ($this->model->hasActiveDormRegistration($this->studentId))
            return ['', 'Bạn đang có đơn đăng ký ký túc xá đang hoạt động. Vui lòng hủy trước khi đăng ký mới.'];

        $room = $this->model->getDormRoomById($roomId);
        if (!$room) return ['', 'Phòng không tồn tại hoặc không hoạt động.'];
        if ((int)$room['available_beds'] <= 0) return ['', 'Phòng này đã hết chỗ trống.'];

        if ($this->model->registerDormRoom($this->studentId, $roomId, $startDate, $endDate)) {
            return ['Đăng ký ký túc xá thành công! Vui lòng chờ phê duyệt.', ''];
        }
        return ['', 'Lỗi khi đăng ký: ' . $this->conn->error];
    }

    private function handleCancel(): array
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('dormitory.cancel')) {
            return ['', 'Bạn không có quyền hủy đăng ký ký túc xá.'];
        }

        $registrationId = (int)($_POST['registration_id'] ?? 0);

        if ($this->model->cancelDormRegistration($registrationId, $this->studentId)) {
            return ['Hủy đăng ký ký túc xá thành công.', ''];
        }
        return ['', 'Không thể hủy đăng ký này.'];
    }
}
