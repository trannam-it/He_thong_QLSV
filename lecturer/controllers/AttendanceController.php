<?php
/**
 * AttendanceController - Điểm danh sinh viên
 */

class AttendanceController extends BaseLecturerController
{
    public function index(): void
    {
        $lid     = $this->lecturerId;
        $classId = (int)($_GET['class_id'] ?? $_POST['class_id'] ?? 0);
        $selDate = $_GET['att_date'] ?? date('Y-m-d');
        $viewTab = $_GET['tab']     ?? 'take';   // take | history | summary

        // Xử lý POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action  = $_POST['action']   ?? '';
            $classId = (int)($_POST['class_id'] ?? 0);
            $attDate = $_POST['att_date'] ?? date('Y-m-d');

            // Xác minh lớp
            $classInfo = $classId ? $this->model->getClassOfLecturer($classId, $lid) : null;
            if (!$classInfo) {
                $_SESSION['att_error'] = 'Lớp không hợp lệ hoặc không có quyền truy cập.';
                header('Location: ' . LecturerRouter::url('attendance') . '&class_id=' . $classId);
                exit;
            }

            if ($action === 'save_attendance') {
                $this->handleSaveAttendance($classId, $attDate);
            } elseif ($action === 'delete_attendance_day') {
                $delDate = $_POST['del_date'] ?? '';
                if ($delDate) {
                    $rows = $this->model->deleteAttendanceByDate($classId, $delDate);
                    $_SESSION['att_success'] = "Đã xóa buổi điểm danh ngày $delDate ($rows bản ghi).";
                }
                header('Location: ' . LecturerRouter::url('attendance') . '&class_id=' . $classId . '&tab=history');
                exit;
            }
        }

        // Flash
        $flashSuccess = $this->getFlash('att_success');
        $flashError   = $this->getFlash('att_error');

        // Load dữ liệu
        $myClasses    = $this->model->getAllLecturerClasses($lid);
        $classInfo    = $classId ? $this->model->getClassOfLecturer($classId, $lid) : null;
        $students     = [];
        $existingAtt  = [];
        $historyDates = [];
        $historyDetail = [];
        $historyDay   = $_GET['hist_date'] ?? '';
        $studentSummary = [];
        $totalSessions  = 0;

        if ($classInfo) {
            $students      = $this->model->getEnrolledStudents($classId);
            $existingAtt   = $this->model->getAttendanceByDate($classId, $selDate);
            $totalSessions = $this->model->getTotalSessionsHeld($classId);

            if ($viewTab === 'history') {
                $historyDates = $this->model->getAttendanceHistory($classId);
                if ($historyDay) {
                    $historyDetail = $this->model->getAttendanceDetail($classId, $historyDay);
                }
            } elseif ($viewTab === 'summary') {
                $studentSummary = $this->model->getStudentAttendanceSummary($classId);
            }
        }

        $this->render('attendance/index.php', compact(
            'myClasses', 'classInfo', 'classId', 'selDate', 'viewTab',
            'students', 'existingAtt', 'historyDates', 'historyDetail',
            'historyDay', 'studentSummary', 'totalSessions',
            'flashSuccess', 'flashError'
        ));
    }

    private function handleSaveAttendance(int $classId, string $attDate): void
    {
        $statuses = $_POST['statuses'] ?? [];
        $notes    = $_POST['notes']    ?? [];
        $allowed  = ['Present', 'Absent', 'Late', 'Excused'];
        $saved    = 0;

        foreach ($statuses as $sid => $status) {
            $sid    = (int)$sid;
            $status = in_array($status, $allowed) ? $status : 'Present';
            $note   = ($notes[$sid] ?? '') === '' ? null : trim($notes[$sid]);

            if (!$this->model->isStudentEnrolled($sid, $classId)) {
                continue;
            }

            $this->model->saveAttendance($classId, $sid, $attDate, $status, $note);
            $saved++;
        }

        $_SESSION['att_success'] = "Đã lưu điểm danh $saved sinh viên cho ngày $attDate!";
        header('Location: ' . LecturerRouter::url('attendance') . '&class_id=' . $classId . '&att_date=' . $attDate . '&tab=take');
        exit;
    }
}
