<?php
/**
 * Authentication Helper
 * Check permissions, roles, etc.
 */
// class Auth
// {
//     private $conn;
//     private $userId;
//     private $userRole;

//     public function __construct($connection)
//     {
//         $this->conn = $connection;
//         $this->userId = $_SESSION['user_id'] ?? null;
//         $this->userRole = $_SESSION['role'] ?? null;
//     }

//     /**
//      * Check if user is authenticated
//      */
//     public function isAuthenticated()
//     {
//         return !is_null($this->userId) && !is_null($this->userRole);
//     }

//     /**
//      * Check if user has specific role
//      */
//     public function hasRole($roles)
//     {
//         $roles = is_array($roles) ? $roles : [$roles];
//         return in_array($this->userRole, $roles);
//     }

//     /**
//      * Check if user has specific permission
//      */
//     public function hasPermission($permission)
//     {
//         $db = new Database($this->conn);
//         $result = $db->query(
//             "SELECT 1 FROM role_permissions rp
//              JOIN roles r ON rp.role_id = r.id
//              JOIN user_roles ur ON r.id = ur.role_id
//              JOIN permissions p ON rp.permission_id = p.id
//              WHERE ur.user_id = ? AND p.code = ?
//              LIMIT 1",
//             [$this->userId, $permission]
//         );
        
//         return $result->num_rows > 0;
//     }

//     /**
//      * Require authentication
//      */
//     public function requireAuth()
//     {
//         if (!$this->isAuthenticated()) {
//             $_SESSION['error'] = 'Vui lòng đăng nhập';
//             header('Location: /web_QLSV/public/login.php');
//             exit;
//         }
//     }

//     /**
//      * Require specific role
//      */
//     public function requireRole($roles)
//     {
//         $this->requireAuth();
        
//         if (!$this->hasRole($roles)) {
//             http_response_code(403);
//             die('Unauthorized');
//         }
//     }

//     /**
//      * Require permission
//      */
//     public function requirePermission($permission)
//     {
//         $this->requireAuth();
        
//         if (!$this->hasPermission($permission)) {
//             http_response_code(403);
//             die('Unauthorized');
//         }
//     }

//     /**
//      * Get current user ID
//      */
//     public function getId()
//     {
//         return $this->userId;
//     }

//     /**
//      * Get current user role
//      */
//     public function getRole()
//     {
//         return $this->userRole;
//     }
// }
?>

<?php
/**
 * Authentication Helper - CHỈ dùng cho API (trả JSON, không redirect)
 * Kiểm tra auth, role, permission
 */
class Auth
{
    private $conn;
    private $userId;
    private $userRole;
    private $username; 

    public function __construct($connection)
    {
        $this->conn = $connection;
        $this->userId = $_SESSION['user_id'] ?? null;
        $this->userRole = $_SESSION['role'] ?? null;
        $this->username = $_SESSION['username'] ?? null;  
    }

    /**
     * Check nếu authenticated
     */
    public function isAuthenticated()
    {
        return !is_null($this->userId) && !is_null($this->userRole);
    }

    /**
     * Check role
     */
    public function hasRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->userRole, $roles);
    }

    /**
     * Check permission
     */
    public function hasPermission($permission)
    {
        // Super admin có toàn quyền
        if ($this->userRole === 'super_admin') {
            return true;
        }

        $db = new Database($this->conn);
        $result = $db->query(
            "SELECT 1 FROM role_permissions rp
             JOIN roles r ON rp.role_id = r.id
             JOIN user_roles ur ON r.id = ur.role_id
             JOIN permissions p ON rp.permission_id = p.id
             WHERE ur.user_id = ? AND p.code = ?
             LIMIT 1",
            [$this->userId, $permission]
        );
        
        return $result->num_rows > 0;
    }

    /**
     * Get user ID
     */
    public function getId()
    {
        return $this->userId;
    }

    /**
     * Get user role
     */
    public function getRole()
    {
        return $this->userRole;
    }

    /**
     * ✅ CHỈ DÙNG CHO API - Trả JSON, không redirect
     */
    public function requirePermissionAPI($permission)
    {
        // Fallback: Nếu chưa setup session (development mode)
        if (!$this->isAuthenticated()) {
            // Cho phép nếu đang ở localhost (development)
            $isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', 'localhost', '::1']);
            if (!$isLocalhost) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Chưa xác thực'
                ]);
                exit;
            }
            // Nếu localhost, cho qua để development
            return;
        }

        // Check permission - Super admin có toàn quyền
        if ($this->userRole === 'super_admin') {
            return;
        }

        if (!$this->hasPermission($permission)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ]);
            exit;
        }
    }

    /**
     * Backwards-compatible alias used by controllers.
     * Controllers call requirePermission(...); forward to API variant.
     */
    public function requirePermission($permission)
    {
        $this->requirePermissionAPI($permission);
    }

    /**
     * Kiểm tra role cho API
     */    public function requireRoleAPI($roles)
    {
        // Fallback: Nếu chưa setup session (development mode)
        if (!$this->isAuthenticated()) {
            // Cho phép nếu đang ở localhost (development)
            $isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', 'localhost', '::1']);
            if (!$isLocalhost) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Chưa xác thực'
                ]);
                exit;
            }
            // Nếu localhost, cho qua để development
            return;
        }

        // Check role
        if (!$this->hasRole($roles)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ]);
            exit;
        }
    }


    /**
     * Get username
     */
    // public function getUsername()
    // {
    //     return $_SESSION['username'] ?? null;
    // }
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Backwards-compatible alias used by controllers.
     * Controllers call requireRole(...); forward to API variant.
     */
    public function requireRole($roles)
    {
        $this->requireRoleAPI($roles);
    }

}
?>