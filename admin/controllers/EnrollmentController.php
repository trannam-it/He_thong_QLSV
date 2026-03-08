<?php
class EnrollmentController extends BaseController{
        // API lấy sinh viên trong lớp (kèm enrollment_id)
    public function studentsByClass()
    {
        $this->auth->requirePermission('manage_grades');

        $classId = $_GET['class_id'] ?? null;
        if (!$classId) return Response::error('class_id required', 400);

        $sql = "SELECT 
                    e.enrollment_id,
                    s.student_code,
                    CONCAT(s.last_name, ' ', s.first_name) AS full_name
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                WHERE e.class_id = ?
                  AND e.status IN ('Registered','Completed')";

        return Response::success(
            $this->db->query($sql, [$classId])->fetch_all(MYSQLI_ASSOC)
        );
    }






}