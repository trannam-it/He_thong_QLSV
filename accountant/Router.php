<?php
/**
 * Accountant Router
 */
class AccountantRouter
{
    private array $routes = [
        ''             => ['AccountantDashboardController', 'dashboard/index.php'],
        'dashboard'    => ['AccountantDashboardController', 'dashboard/index.php'],
        'tuition'      => ['AccountantTuitionController',   'tuition/index.php'],
        'scholarships' => ['AccountantScholarshipsController','scholarships/index.php'],
        'students'     => ['AccountantStudentsController',  'students/index.php'],
        'reports'      => ['AccountantReportsController',   'reports/index.php'],
        'profile'      => ['AccountantProfileController',   'profile/index.php'],
    ];

    private array $controllerFiles = [
        'AccountantDashboardController'    => 'DashboardController.php',
        'AccountantTuitionController'      => 'TuitionController.php',
        'AccountantScholarshipsController' => 'ScholarshipsController.php',
        'AccountantStudentsController'     => 'StudentsController.php',
        'AccountantReportsController'      => 'ReportsController.php',
        'AccountantProfileController'      => 'ProfileController.php',
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

        require_once $this->baseDir . '/accountant/controllers/BaseAccountantController.php';
        require_once $this->baseDir . '/accountant/models/AccountantModel.php';

        $ctrlPath = $this->baseDir . '/accountant/controllers/' . $controllerFile;
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

    public static function getPageName(): string
    {
        $page = strtolower(trim($_GET['page'] ?? ''));
        $map  = [
            ''             => 'accountant',
            'dashboard'    => 'accountant',
            'tuition'      => 'accountant_tuition',
            'scholarships' => 'accountant_scholarships',
            'students'     => 'accountant_students',
            'reports'      => 'accountant_reports',
            'profile'      => 'accountant_profile',
        ];
        return $map[$page] ?? 'accountant';
    }

    public static function url(string $page = ''): string
    {
        $base = BASE_URL . '/accountant/';
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
            . '<a href="' . BASE_URL . '/accountant/" style="color:#fd7e14;">← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
