<?php
/**
 * Lecturer Router - Điều hướng URL theo chuẩn MVC
 *
 * URL Pattern:
 *   /web_QLSV/lecturer/                  → DashboardController::index()
 *   /web_QLSV/lecturer/?page=profile     → ProfileController::index()
 *   /web_QLSV/lecturer/?page=classes     → ClassesController::index()
 *   /web_QLSV/lecturer/?page=grades      → GradesController::index()
 *   /web_QLSV/lecturer/?page=attendance  → AttendanceController::index()
 *   /web_QLSV/lecturer/?page=register    → RegisterController::index()
 */

class LecturerRouter
{
    /** Bảng ánh xạ: tên route => [ControllerClass, viewFolder] */
    private array $routes = [
        ''                    => ['DashboardController',      'dashboard/index.php'],
        'dashboard'           => ['DashboardController',      'dashboard/index.php'],
        'profile'             => ['ProfileController',        'profile/index.php'],
        'classes'             => ['ClassesController',        'classes/index.php'],
        'grades'              => ['GradesController',         'grades/index.php'],
        'attendance'          => ['AttendanceController',     'attendance/index.php'],
        'register'            => ['RegisterController',       'register/index.php'],
        'class_registration'  => ['ClassRegistrationController', 'class_registration.php'],
    ];

    /** Map ControllerClass → file PHP */
    private array $controllerFiles = [
        'DashboardController'             => 'DashboardController.php',
        'ProfileController'               => 'ProfileController.php',
        'ClassesController'               => 'ClassesController.php',
        'GradesController'                => 'GradesController.php',
        'AttendanceController'            => 'AttendanceController.php',
        'RegisterController'              => 'RegisterController.php',
        'ClassRegistrationController'     => 'ClassRegistrationController.php',
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
        require_once $this->baseDir . '/lecturer/controllers/BaseLecturerController.php';
        require_once $this->baseDir . '/lecturer/models/LecturerModel.php';

        // Load specific controller
        $ctrlPath = $this->baseDir . '/lecturer/controllers/' . $controllerFile;
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
            ''           => 'teacher',
            'dashboard'  => 'teacher',
            'profile'    => 'teacher_profile',
            'classes'    => 'teacher_classes',
            'grades'     => 'teacher_grades',
            'attendance' => 'teacher_attendance',
            'register'   => 'teacher_register',
        ];
        return $map[$page] ?? 'teacher';
    }

    /**
     * Tạo URL cho một page
     */
    public static function url(string $page = ''): string
    {
        $base = BASE_URL . '/lecturer/';
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
            . '<a href="' . BASE_URL . '/lecturer/" '
            .    'style="color:#0f766e;text-decoration:none;">'
            .    '← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
