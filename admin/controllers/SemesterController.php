<?php

class SemesterController
{
    protected $db;
    protected $auth;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->auth = Auth::getInstance();
    }

    // 🔒 Chốt điểm học kỳ
    public function lockSemester()
    {
        $this->auth->requirePermission('lock_grades');

        $semesterId = $_POST['semester_id'] ?? null;

        if (!$semesterId) {
            return Response::error('Thiếu semester_id');
        }

        // Kiểm tra học kỳ tồn tại
        $semester = $this->db->query(
            "SELECT * FROM semesters WHERE semester_id = ?",
            [$semesterId]
        )->fetch_assoc();

        if (!$semester) {
            return Response::error('Học kỳ không tồn tại');
        }

        // Chốt điểm
        $this->db->update(
            'semesters',
            ['is_locked' => 1],
            'semester_id = ?',
            [$semesterId]
        );

        return Response::success(null, 'Đã chốt điểm học kỳ');
    }
}

?>
<?php
// // Chốt điểm học kỳ
// class SemesterController extends BaseController
// {
//     public function lockSemester()
//     {
//         $this->auth->requirePermission('lock_grades');

//         $semesterId = $_POST['semester_id'];
//         $this->db->update('semesters', ['is_locked' => 1], 'semester_id = ?', [$semesterId]);

//         return Response::success(null, 'Đã chốt điểm học kỳ');
//     }
// }

