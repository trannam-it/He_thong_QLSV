<?php
class LibrarianProfileController extends BaseLibrarianController
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'update_email') {
                $email = trim($_POST['email'] ?? '');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->redirectWithMessage(LibrarianRouter::url('profile'), 'error', 'Email không hợp lệ.');
                }
                if ($this->model->updateUserProfile($this->userId, $email)) {
                    $this->redirectWithMessage(LibrarianRouter::url('profile'), 'success', 'Cập nhật email thành công!');
                }
            }
            if ($action === 'change_password') {
                $oldPw  = $_POST['old_password'] ?? '';
                $newPw  = $_POST['new_password'] ?? '';
                $confPw = $_POST['confirm_password'] ?? '';
                $hash   = $this->model->getPasswordHash($this->userId);
                if (!password_verify($oldPw, $hash)) {
                    $this->redirectWithMessage(LibrarianRouter::url('profile'), 'error', 'Mật khẩu cũ không đúng.');
                }
                if ($newPw !== $confPw) {
                    $this->redirectWithMessage(LibrarianRouter::url('profile'), 'error', 'Xác nhận mật khẩu không khớp.');
                }
                if (strlen($newPw) < 6) {
                    $this->redirectWithMessage(LibrarianRouter::url('profile'), 'error', 'Mật khẩu tối thiểu 6 ký tự.');
                }
                if ($this->model->changePassword($this->userId, password_hash($newPw, PASSWORD_DEFAULT))) {
                    $this->redirectWithMessage(LibrarianRouter::url('profile'), 'success', 'Đổi mật khẩu thành công!');
                }
            }
        }
        $this->render('profile/index.php', [
            'success' => $this->getFlash('success'),
            'error'   => $this->getFlash('error'),
        ]);
    }
}
