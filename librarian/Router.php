<?php
/**
 * Librarian Router
 */
class LibrarianRouter
{
    private array $routes = [
        ''         => ['LibrarianDashboardController', 'dashboard/index.php'],
        'dashboard'=> ['LibrarianDashboardController', 'dashboard/index.php'],
        'books'    => ['LibrarianBooksController',     'books/index.php'],
        'borrows'  => ['LibrarianBorrowsController',   'borrows/index.php'],
        'members'  => ['LibrarianMembersController',   'members/index.php'],
        'reports'  => ['LibrarianReportsController',   'reports/index.php'],
        'profile'  => ['LibrarianProfileController',   'profile/index.php'],
    ];

    private array $controllerFiles = [
        'LibrarianDashboardController' => 'DashboardController.php',
        'LibrarianBooksController'     => 'BooksController.php',
        'LibrarianBorrowsController'   => 'BorrowsController.php',
        'LibrarianMembersController'   => 'MembersController.php',
        'LibrarianReportsController'   => 'ReportsController.php',
        'LibrarianProfileController'   => 'ProfileController.php',
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

        require_once $this->baseDir . '/librarian/controllers/BaseLibrarianController.php';
        require_once $this->baseDir . '/librarian/models/LibrarianModel.php';

        $ctrlPath = $this->baseDir . '/librarian/controllers/' . $controllerFile;
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
            ''         => 'librarian',
            'dashboard'=> 'librarian',
            'books'    => 'librarian_books',
            'borrows'  => 'librarian_borrows',
            'members'  => 'librarian_members',
            'reports'  => 'librarian_reports',
            'profile'  => 'librarian_profile',
        ];
        return $map[$page] ?? 'librarian';
    }

    public static function url(string $page = ''): string
    {
        $base = BASE_URL . '/librarian/';
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
            . '<a href="' . BASE_URL . '/librarian/" style="color:#20c997;">← Quay về Dashboard</a>'
            . '</div>';
        exit;
    }
}
