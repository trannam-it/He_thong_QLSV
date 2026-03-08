<?php
/**
 * AttendanceController - Điểm danh sinh viên
 *
 * Route: /web_QLSV/student/?page=attendance
 * Trạng thái: Đang phát triển (stub)
 */
class AttendanceController extends BaseStudentController
{
    public function index(): void
    {
        // TODO: Khi bảng attendance đã được tạo trong DB,
        //       triển khai đầy đủ ở đây.

        $this->render('attendance/index.php', [
            'pageTitle' => 'Điểm danh',
            'success'   => $this->getFlash('success'),
            'error'     => $this->getFlash('error'),
        ]);
    }
}
