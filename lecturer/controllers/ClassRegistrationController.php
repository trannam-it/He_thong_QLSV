<?php
/**
 * ClassRegistrationController - Quản lý đăng ký dạy lớp học phần
 */
class ClassRegistrationController extends BaseLecturerController
{
    public function index(): void
    {
        // Check permission (web context)
        $this->auth->requirePermissionWeb('classes.register');

        $success = '';
        $error   = '';

        // Handle registration via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classId = (int)($_POST['class_id'] ?? 0);
            
            if ($classId <= 0) {
                $error = 'Mã lớp không hợp lệ.';
            } else {
                // Check if already teaches this class
                $stmt = $this->conn->prepare("
                    SELECT class_id FROM classes 
                    WHERE class_id = ? AND lecturer_id = ?
                ");
                $stmt->bind_param('ii', $classId, $this->lecturerId);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    $error = 'Bạn đã đăng ký dạy lớp này rồi.';
                } else {
                    // Assign this lecturer to the class
                    $update = $this->conn->prepare("
                        UPDATE classes SET lecturer_id = ? WHERE class_id = ?
                    ");
                    $update->bind_param('ii', $this->lecturerId, $classId);
                    
                    if ($update->execute()) {
                        $success = 'Đăng ký dạy lớp thành công!';
                    } else {
                        $error = 'Lỗi khi đăng ký: ' . $this->conn->error;
                    }
                }
            }
        }

        // Get available classes for this lecturer
        $classes = $this->model->getAvailableClassesForRegistration($this->lecturerId);
        $lecturer = [
            'full_name'    => $this->lecturerInfo['full_name'] ?? 'N/A',
            'faculty_name' => $this->lecturerInfo['faculty_name'] ?? 'Chưa xác định',
        ];

        // Render view
        $this->render('class_registration.php', [
            'classes'   => $classes,
            'lecturer'  => $lecturer,
            'success'   => $success,
            'error'     => $error,
        ]);
    }
}
