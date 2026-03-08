<?php
/**
 * Academic Router - Điều hướng URL theo chuẩn MVC
 *
 * Cách hoạt động:
 *   URL: /web_QLSV/academic/                  → DashboardController::index()
 *   URL: /web_QLSV/academic/?page=students    → StudentsController::index()
 *   URL: /web_QLSV/academic/?page=subjects    → SubjectsController::index()
 *   ...v.v.
 */

class AcademicRouter
{
    /** Bảng ánh xạ: tên route => [ControllerClass, viewFolder] */
    private array $routes = [
        ''                  => ['AcademicDashboardController', 'dashboard/index.php'],
        'dashboard'         => ['AcademicDashboardController', 'dashboard/index.php'],
        'students'          => ['AcademicStudentsController',  'students/index.php'],
        'subjects'          => ['AcademicSubjectsController',  'subjects/index.php'],
        'classes'           => ['AcademicClassesController',   'classes/index.php'],
        'semesters'         => ['AcademicSemestersController', 'semesters/index.php'],
        'enrollments'       => ['AcademicEnrollmentsController','enrollments/index.php'],
        'enrollment_periods'=> ['EnrollmentController',        'enrollment/periods.php'],
        'grades'            => ['AcademicGradesController',    'grades/index.php'],
        'schedule'          => ['AcademicScheduleController',  'schedule/index.php'],
        'reports'           => ['AcademicReportsController',   'reports/index.php'],
        'profile'           => ['AcademicProfileController',   'profile/index.php'],
    ];

    /** Map ControllerClass → file PHP */
    private array $controllerFiles = [
        'AcademicDashboardController'   => 'DashboardController.php',
        'AcademicStudentsController'    => 'StudentsController.php',
        'AcademicSubjectsController'    => 'SubjectsController.php',
        'AcademicClassesController'     => 'ClassesController.php',
        'AcademicSemestersController'   => 'SemestersController.php',
        'AcademicEnrollmentsController' => 'EnrollmentsController.php',
        'EnrollmentController'          => 'EnrollmentController.php',
        'AcademicGradesController'      => 'GradesController.php',
        'AcademicScheduleController'    => 'ScheduleController.php',
        'AcademicReportsController'     => 'ReportsController.php',
        'AcademicProfileController'     => 'ProfileController.php',
    ];

    private string $baseDir;

    public function __construct(string $baseDir)
    {
        $this->baseDir = rtrim($baseDir, '/');
    }

    public function getCurrentPage(): string
    {
        return strtolower(trim($_GET['page'] ?? ''));
    }

    public function dispatch(): void
    {
        $page = $this->getCurrentPage();

        if (!isset($this->routes[$page])) {
            $this->handle404($page);
            return;
        }

        [$controllerClass, $viewPath] = $this->routes[$page];
        $controllerFile = $this->controllerFiles[$controllerClass];

        // Load base dependencies
        require_once $this->baseDir . '/academic/controllers/BaseAcademicController.php';
        require_once $this->baseDir . '/academic/models/AcademicModel.php';

        // Load specific controller
        $ctrlPath = $this->baseDir . '/academic/controllers/' . $controllerFile;
        if (!file_exists($ctrlPath)) {
            $this->handle404("Controller không tồn tại: {$controllerFile}");
            return;
        }

        require_once $ctrlPath;

        if (!class_exists($controllerClass)) {
            $this->handle404("Class không tìm thấy: {$controllerClass}");
            return;
        }

        global $conn;
        $controller = new $controllerClass($conn);
        $controller->index();
    }

    /**
     * Trả về tên page chuẩn cho active menu
     */
    public static function getPageName(): string
    {
        $page = strtolower(trim($_GET['page'] ?? ''));
        $map  = [
            ''            => 'academic',
            'dashboard'   => 'academic',
            'students'    => 'academic_students',
            'subjects'    => 'academic_subjects',
            'classes'     => 'academic_classes',
            'semesters'   => 'academic_semesters',
            'enrollments' => 'academic_enrollments',
            'grades'      => 'academic_grades',
            'schedule'    => 'academic_schedule',
            'reports'     => 'academic_reports',
            'profile'           => 'academic_profile',
            'enrollment_periods' => 'academic_enroll_periods',
        ];
        return $map[$page] ?? 'academic';
    }

    /**
     * Tạo URL cho một page
     */
    public static function url(string $page = ''): string
    {
        $base = BASE_URL . '/academic/';
        if ($page === '' || $page === 'dashboard') {
            return $base;
        }
        return $base . '?page=' . urlencode($page);
    }

    private function handle404(string $message = ''): void
    {
        http_response_code(404);
        echo '<div style="font-family:sans-serif;padding:60px;text-align:center;">'
            . '<h2 style="color:#e74c3c;">⚠ Trang không tồn tại</h2>'
            . '<p style="color:#666;">' . htmlspecialchars($message) . '</p>'
            . '<a href="' . BASE_URL . '/academic/" '
            .    'style="color:#0d6efd;text-decoration:none;">'
            .    '← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
