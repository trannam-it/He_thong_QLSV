<?php
/**
 * ScholarshipController - Học bổng sinh viên
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'scholarship.view'
 *   [L2] Controller: $this->requirePermission('scholarship.view')
 *   [L3] Actions: 'scholarship.apply' / 'scholarship.cancel'
 */
class ScholarshipController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('scholarship.view');

        $msg   = $this->getFlash('success') ?? '';
        $error = $this->getFlash('error')   ?? '';

        $myGpa = $this->model->getCompletedGPA($this->studentId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['apply_scholarship'])) {
                [$msg, $error] = $this->handleApply($myGpa);
            } elseif (isset($_POST['cancel_application'])) {
                [$msg, $error] = $this->handleCancel();
            }
        }

        $available = $this->model->getAvailableScholarships($this->studentId);
        $myApps    = $this->model->getMyScholarshipApplications($this->studentId);

        $this->render('scholarship/index.php', [
            'pageTitle'  => 'Học bổng',
            'myGpa'      => $myGpa,
            'available'  => $available,
            'myApps'     => $myApps,
            'msg'        => $msg,
            'error'      => $error,
            // Quyền cho view
            'canApply'   => $this->auth->hasPermission('scholarship.apply'),
            'canCancel'  => $this->auth->hasPermission('scholarship.cancel'),
        ]);
    }

    private function handleApply(?float $myGpa): array
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('scholarship.apply')) {
            return ['', 'Bạn không có quyền đăng ký học bổng.'];
        }

        $schId = (int)($_POST['scholarship_id'] ?? 0);

        if ($schId <= 0) return ['', 'Học bổng không hợp lệ.'];

        $sch = $this->model->getScholarshipById($schId);
        if (!$sch) return ['', 'Học bổng không tồn tại hoặc đã đóng.'];
        if ($sch['deadline'] && date('Y-m-d') > $sch['deadline'])
            return ['', 'Đã hết hạn đăng ký học bổng này.'];

        if ($sch['min_gpa'] !== null && ($myGpa === null || $myGpa < $sch['min_gpa']))
            return ['', 'Điểm GPA của bạn không đáp ứng yêu cầu tối thiểu (' . $sch['min_gpa'] . ').'];
        if ($sch['max_gpa'] !== null && $myGpa !== null && $myGpa > $sch['max_gpa'])
            return ['', 'Điểm GPA của bạn vượt quá mức tối đa cho học bổng này.'];

        if ($this->model->hasAppliedScholarship($this->studentId, $schId))
            return ['', 'Bạn đã đăng ký học bổng này rồi.'];

        if ($sch['quantity'] !== null) {
            $count = $this->model->countScholarshipApplicants($schId);
            if ($count >= (int)$sch['quantity'])
                return ['', 'Học bổng này đã hết chỉ tiêu.'];
        }

        if ($this->model->applyScholarship($this->studentId, $schId)) {
            return ['Đã gửi đơn đăng ký học bổng "' . htmlspecialchars($sch['name']) . '" thành công!', ''];
        }

        return ['', 'Lỗi khi đăng ký: ' . $this->conn->error];
    }

    private function handleCancel(): array
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('scholarship.cancel')) {
            return ['', 'Bạn không có quyền hủy đơn học bổng.'];
        }

        $appId = (int)($_POST['application_id'] ?? 0);

        if ($this->model->cancelScholarshipApplication($appId, $this->studentId)) {
            return ['Đã hủy đơn đăng ký học bổng.', ''];
        }
        return ['', 'Không thể hủy (đơn không ở trạng thái Đang chờ).'];
    }
}
