<?php
/**
 * ProfileController - Thông tin cá nhân & đổi mật khẩu
 */

class ProfileController extends BaseLecturerController
{
    public function index(): void
    {
        $lid    = $this->lecturerId;
        $userId = $this->userId;

        // Xử lý POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'update_info';

            if ($action === 'update_info') {
                $this->handleUpdateInfo($lid);
            } elseif ($action === 'change_password') {
                $this->handleChangePassword($userId);
            }
        }

        $activeTab    = $_GET['tab'] ?? ($_SESSION['active_tab'] ?? 'info');
        unset($_SESSION['active_tab']);

        $flashSuccess = $this->getFlash('success');
        $flashError   = $this->getFlash('error');

        // Reload lecturer sau khi cập nhật
        $this->lecturer = $this->model->getOverviewByUserId($userId);

        $this->render('profile/index.php', compact(
            'activeTab', 'flashSuccess', 'flashError'
        ));
    }

    private function handleUpdateInfo(int $lecturerId): void
    {
        $phone  = trim($_POST['phone'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors[] = 'Số điện thoại phải là 10 chữ số';
        }

        if (empty($errors)) {
            if ($this->model->updateContact($lecturerId, $phone, $email)) {
                $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi khi cập nhật.';
            }
        } else {
            $_SESSION['error'] = implode('. ', $errors);
        }

        header('Location: ' . LecturerRouter::url('profile'));
        exit;
    }

    private function handleChangePassword(int $userId): void
    {
        $curPwd  = $_POST['current_password'] ?? '';
        $newPwd  = $_POST['new_password']     ?? '';
        $confPwd = $_POST['confirm_password'] ?? '';
        $errors  = [];

        $currentHash = $this->model->getPasswordHash($userId);
        if (!$currentHash || !password_verify($curPwd, $currentHash)) {
            $errors[] = 'Mật khẩu hiện tại không đúng';
        }
        if (strlen($newPwd) < 6) {
            $errors[] = 'Mật khẩu mới ít nhất 6 ký tự';
        }
        if ($newPwd !== $confPwd) {
            $errors[] = 'Xác nhận mật khẩu không khớp';
        }

        if (empty($errors)) {
            $hash = password_hash($newPwd, PASSWORD_DEFAULT);
            if ($this->model->changePassword($userId, $hash)) {
                $_SESSION['success']    = 'Đổi mật khẩu thành công!';
                $_SESSION['active_tab'] = 'password';
            } else {
                $_SESSION['error']      = 'Có lỗi khi đổi mật khẩu.';
                $_SESSION['active_tab'] = 'password';
            }
        } else {
            $_SESSION['error']      = implode('. ', $errors);
            $_SESSION['active_tab'] = 'password';
        }

        header('Location: ' . LecturerRouter::url('profile'));
        exit;
    }
}
