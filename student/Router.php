<?php
/**
 * Student Router - Điều hướng URL theo chuẩn MVC
 *
 * Cách hoạt động:
 *   URL: /web_QLSV/student/               → DashboardController::index()
 *   URL: /web_QLSV/student/?page=grades   → GradeController::index()
 *   URL: /web_QLSV/student/?page=profile  → ProfileController::index()
 *   ...v.v.
 *
 * Tất cả request đều đi qua student/index.php (Front Controller),
 * Router lấy param ?page= và dispatch đến Controller phù hợp.
 */

class StudentRouter
{
    /** Bảng ánh xạ: tên route => [file controller, tên class] */
    private array $routes = [
        ''            => ['DashboardController',  'dashboard/index.php'],
        'dashboard'   => ['DashboardController',  'dashboard/index.php'],
        'profile'     => ['ProfileController',    'profile/index.php'],
        'grades'      => ['GradeController',      'grades/index.php'],
        'enrollment'  => ['EnrollmentController', 'enrollment/index.php'],
        'schedule'    => ['ScheduleController',   'schedule/index.php'],
        'tuition'     => ['TuitionController',    'tuition/index.php'],
        'scholarship' => ['ScholarshipController','scholarship/index.php'],
        'dormitory'   => ['DormitoryController',  'dormitory/index.php'],
        'library'     => ['LibraryController',    'library/index.php'],
        'attendance'  => ['AttendanceController', 'attendance/index.php'],
    ];

    /** Tên file controller tương ứng */
    private array $controllerFiles = [
        'DashboardController'  => 'DashboardController.php',
        'ProfileController'    => 'ProfileController.php',
        'GradeController'      => 'GradeController.php',
        'EnrollmentController' => 'EnrollmentController.php',
        'ScheduleController'   => 'ScheduleController.php',
        'TuitionController'    => 'TuitionController.php',
        'ScholarshipController'=> 'ScholarshipController.php',
        'DormitoryController'  => 'DormitoryController.php',
        'LibraryController'    => 'LibraryController.php',
        'AttendanceController' => 'AttendanceController.php',
    ];

    private string $baseDir;

    public function __construct(string $baseDir)
    {
        $this->baseDir = rtrim($baseDir, '/');
    }

    /**
     * Lấy tên page hiện tại từ query string
     */
    public function getCurrentPage(): string
    {
        return strtolower(trim($_GET['page'] ?? ''));
    }

    /**
     * Dispatch request đến controller phù hợp
     */
    public function dispatch(): void
    {
        $page = $this->getCurrentPage();

        // Kiểm tra route có tồn tại không
        if (!isset($this->routes[$page])) {
            $this->handle404($page);
            return;
        }

        [$controllerClass, $viewPath] = $this->routes[$page];
        $controllerFile = $this->controllerFiles[$controllerClass];

        // Load BaseStudentController trước
        require_once $this->baseDir . '/student/controllers/BaseStudentController.php';
        require_once $this->baseDir . '/student/models/StudentModel.php';

        // Load controller cụ thể
        $ctrlPath = $this->baseDir . '/student/controllers/' . $controllerFile;
        if (!file_exists($ctrlPath)) {
            $this->handle404("Controller không tồn tại: {$controllerFile}");
            return;
        }

        require_once $ctrlPath;

        // Kiểm tra class có tồn tại không
        if (!class_exists($controllerClass)) {
            $this->handle404("Class không tìm thấy: {$controllerClass}");
            return;
        }

        // Khởi tạo controller với $conn (global)
        global $conn;
        $controller = new $controllerClass($conn);

        // Gọi action index()
        $controller->index();
    }

    /**
     * Lấy tên page (dùng cho header để highlight menu active)
     * Map page-name → tên $currentPage trong header cũ để tương thích
     */
    public static function getPageName(): string
    {
        $page = strtolower(trim($_GET['page'] ?? ''));

        $pageNameMap = [
            ''            => 'student',
            'dashboard'   => 'student',
            'profile'     => 'student_profile',
            'grades'      => 'student_grades',
            'enrollment'  => 'student_enrollment',
            'schedule'    => 'student_schedule',
            'tuition'     => 'student_tuition',
            'scholarship' => 'student_scholarship',
            'dormitory'   => 'student_dormitory',
            'library'     => 'student_library',
            'attendance'  => 'student_attendance',
        ];

        return $pageNameMap[$page] ?? 'student';
    }

    /**
     * Tạo URL cho một page
     */
    public static function url(string $page = ''): string
    {
        $base = BASE_URL . '/student/';
        if ($page === '' || $page === 'dashboard') {
            return $base;
        }
        return $base . '?page=' . urlencode($page);
    }

    /**
     * Xử lý 404
     */
    private function handle404(string $message = ''): void
    {
        http_response_code(404);
        echo '<div style="font-family:sans-serif;padding:60px;text-align:center;">'
            . '<h2 style="color:#e74c3c;">⚠ Trang không tồn tại</h2>'
            . '<p style="color:#666;">' . htmlspecialchars($message) . '</p>'
            . '<a href="' . BASE_URL . '/student/" '
            .    'style="color:#4e73df;text-decoration:none;">'
            .    '← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
