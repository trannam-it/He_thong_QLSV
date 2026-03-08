<?php

/**
 * AcademicClassesController - Quản lý Lớp học
 */
// class AcademicClassesController extends BaseAcademicController
// {
//     public function index(): void
//     {
//         $search     = trim($_GET['search'] ?? '');
//         $semesterId = (int)($_GET['semester_id'] ?? 0);

//         $classes   = $this->model->getAllClasses($search, $semesterId);
//         $semesters = $this->model->getAllSemesters();

//         $classDetail  = null;
//         $classStudents = [];
//         if (!empty($_GET['detail'])) {
//             $detailId     = (int)$_GET['detail'];
//             $classDetail  = $this->model->getClassById($detailId);
//             if ($classDetail) {
//                 $classStudents = $this->model->getClassStudents($detailId);
//             }
//         }

//         $this->render('classes/index.php', [
//             'classes'       => $classes,
//             'semesters'     => $semesters,
//             'search'        => $search,
//             'semesterId'    => $semesterId,
//             'classDetail'   => $classDetail,
//             'classStudents' => $classStudents,
//             'success'       => $this->getFlash('success'),
//             'error'         => $this->getFlash('error'),
//         ]);
//     }
// }

/**
 * AcademicClassesController - Quản lý Lớp học
 */
// require_once __DIR__ . '/../../core/RBACMiddleware.php';

// class AcademicClassesController extends BaseAcademicController
// {
//     public function index(): void
//     {
//         // 🔐 Check permission xem danh sách lớp
//         RBACMiddleware::checkWeb(
//             $this->conn,
//             $this->auth,
//             'classes.view'
//         );

//         // ====== Filter ======
//         $search     = isset($_GET['search']) ? trim($_GET['search']) : '';
//         $semesterId = isset($_GET['semester_id']) ? (int)$_GET['semester_id'] : 0;

//         // ====== Lấy dữ liệu chính ======
//         $classes   = $this->model->getAllClasses($search, $semesterId);
//         $semesters = $this->model->getAllSemesters();
//         $subjects   = $this->model->getAllSubjects();
//         $lecturers  = $this->model->getAllLecturers();

//         // ====== Xem chi tiết lớp ======
//         $classDetail   = null;
//         $classStudents = [];
//         $canCreate = $this->auth->hasPermission('classes.create');

//         if (isset($_GET['detail']) && is_numeric($_GET['detail'])) {

//             $detailId = (int)$_GET['detail'];

//             if ($detailId > 0) {

//                 // 🔐 Check permission xem chi tiết
//                 RBACMiddleware::checkWeb(
//                     $this->conn,
//                     $this->auth,
//                     'classes.view'
//                 );

//                 $classDetail = $this->model->getClassById($detailId);

//                 if ($classDetail) {
//                     $classStudents = $this->model->getClassStudents($detailId);
//                 } else {
//                     $this->setFlash('error', 'Lớp học không tồn tại.');
//                 }
//             }
//         }

//         // ====== Render ======
//         $this->render('classes/index.php', [
//             'classes'       => $classes,
//             'semesters'     => $semesters,
//             'search'        => $search,
//             'subjects'      => $subjects,     
//             'lecturers'     => $lecturers,    
//             'semesterId'    => $semesterId,
//             'classDetail'   => $classDetail,
//             'classStudents' => $classStudents,
//             'canCreate'     => $canCreate,
//             'success'       => $this->getFlash('success'),
//             'error'         => $this->getFlash('error'),
//         ]);
//     }
// }

require_once __DIR__ . '/../../core/RBACMiddleware.php';

class AcademicClassesController extends BaseAcademicController
{
    public function index(): void
    {
        // 🔐 Check permission xem danh sách
        RBACMiddleware::checkWeb(
            $this->conn,
            $this->auth,
            'classes.view'
        );

        // ===============================
        // ✅ XỬ LÝ TẠO LỚP (POST)
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            RBACMiddleware::checkWeb(
                $this->conn,
                $this->auth,
                'classes.create'
            );

            $data = [
                'class_code'   => trim($_POST['class_code'] ?? ''),
                'subject_id'   => (int)($_POST['subject_id'] ?? 0),
                'semester_id'  => (int)($_POST['semester_id'] ?? 0),
                'lecturer_id'  => (int)($_POST['lecturer_id'] ?? 0),
                'max_students' => (int)($_POST['max_students'] ?? 50),
                // 'status' => $_POST['status']
                'status' => $_POST['status'] ?? 'open'
            ];

            if (!$data['class_code'] || !$data['subject_id'] || !$data['semester_id']) {
                $this->setFlash('error', 'Thiếu thông tin bắt buộc.');
                // header("Location: " . aUrl('classes'));
                header("Location: " . AcademicRouter::url('classes'));
                exit;
            }

           $result = $this->model->createClass($data);

            if ($result === true) {
                $this->setFlash('success', 'Tạo lớp thành công!');
            } elseif ($result === 'duplicate') {
                $this->setFlash('error', 'Mã lớp đã tồn tại!');
            } else {
                $this->setFlash('error', 'Lỗi khi tạo lớp.');
            }

            // header("Location: " . aUrl('classes'));
                header("Location: " . AcademicRouter::url('classes'));
            exit;
        }

        // ===============================
        // LOAD DATA
        // ===============================
        $search     = trim($_GET['search'] ?? '');
        $semesterId = (int)($_GET['semester_id'] ?? 0);

        $classes    = $this->model->getAllClasses($search, $semesterId);
        $semesters  = $this->model->getAllSemesters();
        $subjects   = $this->model->getAllSubjects();
        $lecturers  = $this->model->getAllLecturers();

        $classDetail   = null;
        $classStudents = [];
        $canCreate     = $this->auth->hasPermission('classes.create');

        if (isset($_GET['detail']) && is_numeric($_GET['detail'])) {
            $detailId = (int)$_GET['detail'];

            if ($detailId > 0) {
                $classDetail = $this->model->getClassById($detailId);
                if ($classDetail) {
                    $classStudents = $this->model->getClassStudents($detailId);
                }
            }
        }

        $this->render('classes/index.php', [
            'classes'       => $classes,
            'semesters'     => $semesters,
            'subjects'      => $subjects,
            'lecturers'     => $lecturers,
            'search'        => $search,
            'semesterId'    => $semesterId,
            'classDetail'   => $classDetail,
            'classStudents' => $classStudents,
            'canCreate'     => $canCreate,
            'success'       => $this->getFlash('success'),
            'error'         => $this->getFlash('error'),
        ]);
    }
}
 
