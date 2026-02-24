<?php
/**
 * User Controller
 * Quản lý người dùng
 */
// class UserController extends BaseController
// {
//     /**
//      * Get all users
//      */
//     public function index()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $pagination = $this->getPagination();
//         $total = $this->db->count('users');
        
//         $users = $this->db->query(
//             "SELECT u.*, GROUP_CONCAT(r.name SEPARATOR ', ') as roles 
//              FROM users u
//              LEFT JOIN user_roles ur ON u.id = ur.user_id
//              LEFT JOIN roles r ON ur.role_id = r.id
//              GROUP BY u.id
//              ORDER BY u.id DESC
//              LIMIT ? OFFSET ?",
//             [$pagination['limit'], $pagination['offset']]
//         )->fetch_all(MYSQLI_ASSOC);

//         return Response::paginate($users, $total, $pagination['page'], $pagination['limit']);
//     }

//     /**
//      * Get single user
//      */
//     public function show()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_GET['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         // Get roles
//         $roles = $this->db->query(
//             "SELECT r.* FROM roles r
//              JOIN user_roles ur ON r.id = ur.role_id
//              WHERE ur.user_id = ?",
//             [$id]
//         )->fetch_all(MYSQLI_ASSOC);

//         $user['roles'] = $roles;
//         unset($user['password_hash']);
        
//         return Response::success($user);
//     }

//     /**
//      * Create user
//      */
//     public function store()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         // Validate input
//         $rules = [
//             'username' => 'required|min:3|max:50',
//             'email' => 'required|email',
//             'password' => 'required|min:6',
//             'role' => 'required'
//         ];

//         if (!$this->validator->validate($_POST, $rules)) {
//             return Response::error('Validation failed', 400, $this->validator->getErrors());
//         }

//         // Check duplicate
//         if ($this->db->count('users', 'username = ?', [$_POST['username']]) > 0) {
//             return Response::error('Username already exists', 400);
//         }

//         if ($this->db->count('users', 'email = ?', [$_POST['email']]) > 0) {
//             return Response::error('Email already exists', 400);
//         }

//         // Create user
//         $userId = $this->db->insert('users', [
//             'username' => $_POST['username'],
//             'email' => $_POST['email'],
//             'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
//             'is_active' => 1
//         ]);

//         // Assign role
//         if ($userId) {
//             $role = $this->db->selectOne('roles', 'code = ?', [$_POST['role']]);
//             if ($role) {
//                 $this->db->insert('user_roles', [
//                     'user_id' => $userId,
//                     'role_id' => $role['id']
//                 ]);
//             }

//             $this->logAudit('CREATE', 'users', $userId, null, $_POST);
//             return Response::success(['id' => $userId], 'User created successfully', 201);
//         }

//         return Response::error('Failed to create user', 500);
//     }

//     /**
//      * Update user
//      */
//     public function update()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? $_GET['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         // Validate
//         $rules = [
//             'username' => 'required|min:3|max:50',
//             'email' => 'required|email'
//         ];

//         if (!$this->validator->validate($_POST, $rules)) {
//             return Response::error('Validation failed', 400, $this->validator->getErrors());
//         }

//         // Check duplicates (excluding current user)
//         if ($_POST['username'] != $user['username']) {
//             if ($this->db->count('users', 'username = ?', [$_POST['username']]) > 0) {
//                 return Response::error('Username already exists', 400);
//             }
//         }

//         // Update
//         $updateData = [
//             'username' => $_POST['username'],
//             'email' => $_POST['email']
//         ];

//         if (!empty($_POST['password'])) {
//             $updateData['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
//         }

//         $this->db->update('users', $updateData, 'id = ?', [$id]);

//         // Update role
//         if (isset($_POST['role'])) {
//             $role = $this->db->selectOne('roles', 'code = ?', [$_POST['role']]);
//             if ($role) {
//                 $this->db->delete('user_roles', 'user_id = ?', [$id]);
//                 $this->db->insert('user_roles', [
//                     'user_id' => $id,
//                     'role_id' => $role['id']
//                 ]);
//             }
//         }

//         $this->logAudit('UPDATE', 'users', $id, $user, $updateData);
//         return Response::success(null, 'User updated successfully');
//     }

//     /**
//      * Delete user
//      */
//     public function delete()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? $_GET['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         $this->db->delete('users', 'id = ?', [$id]);
//         $this->logAudit('DELETE', 'users', $id, $user, null);
        
//         return Response::success(null, 'User deleted successfully');
//     }

//     /**
//      * Toggle user status
//      */
//     public function toggleStatus()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         $newStatus = (int)!$user['is_active'];
//         $this->db->update('users', ['is_active' => $newStatus], 'id = ?', [$id]);
        
//         $this->logAudit('TOGGLE', 'users', $id, $user, ['is_active' => $newStatus]);
//         return Response::success(['status' => $newStatus], 'Status updated');
//     }
// }
?>

<?php
/**
 * Enhanced User Controller
 * Quản lý người dùng + Reset password + Lock/Unlock
 */
// class UserController extends BaseController
// {
//     /**
//      * Get all users with pagination & search
//      */
//     public function index()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $pagination = $this->getPagination();
//         $search = isset($_GET['search']) ? '%' . trim($_GET['search']) . '%' : null;
//         $role = isset($_GET['role']) ? $_GET['role'] : null;
        
//         $where = '';
//         $params = [];
        
//         if ($search) {
//             $where = '(u.username LIKE ? OR u.email LIKE ?)';
//             $params = [$search, $search];
//         }
        
//         if ($role) {
//             if ($where) $where .= ' AND ';
//             $where .= 'r.code = ?';
//             $params[] = $role;
//         }
        
//         $query = "SELECT u.*, GROUP_CONCAT(r.code SEPARATOR ', ') as roles, GROUP_CONCAT(r.name SEPARATOR ', ') as role_names 
//                   FROM users u
//                   LEFT JOIN user_roles ur ON u.id = ur.user_id
//                   LEFT JOIN roles r ON ur.role_id = r.id";
        
//         if ($where) $query .= " WHERE $where";
        
//         $query .= " GROUP BY u.id ORDER BY u.id DESC LIMIT ? OFFSET ?";
        
//         $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
//         $users = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
//         // Count
//         $countQuery = "SELECT COUNT(DISTINCT u.id) as total FROM users u
//                        LEFT JOIN user_roles ur ON u.id = ur.user_id
//                        LEFT JOIN roles r ON ur.role_id = r.id";
        
//         if ($where) $countQuery .= " WHERE $where";
        
//         $countParams = array_values($params);
//         $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

//         return Response::paginate($users, $total, $pagination['page'], $pagination['limit']);
//     }

//     /**
//      * Get single user
//      */
//     public function show()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_GET['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         // Get roles
//         $roles = $this->db->query(
//             "SELECT r.id, r.code, r.name FROM roles r
//              JOIN user_roles ur ON r.id = ur.role_id
//              WHERE ur.user_id = ?",
//             [$id]
//         )->fetch_all(MYSQLI_ASSOC);

//         $user['roles'] = $roles;
//         unset($user['password_hash']);
        
//         return Response::success($user);
//     }

//     /**
//      * Create new user
//      */
//     public function store()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         // Validate input
//         $rules = [
//             'username' => 'required|min:3|max:50',
//             'email' => 'required|email',
//             'password' => 'required|min:6',
//             'role' => 'required'
//         ];

//         if (!$this->validator->validate($_POST, $rules)) {
//             return Response::error('Validation failed', 400, $this->validator->getErrors());
//         }

//         // Check duplicate
//         if ($this->db->count('users', 'username = ?', [$_POST['username']]) > 0) {
//             return Response::error('Username đã tồn tại', 400);
//         }

//         if ($this->db->count('users', 'email = ?', [$_POST['email']]) > 0) {
//             return Response::error('Email đã tồn tại', 400);
//         }

//         // Create user
//         $userId = $this->db->insert('users', [
//             'username' => $_POST['username'],
//             'email' => $_POST['email'],
//             'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
//             'is_active' => 1,
//             'failed_attempts' => 0
//         ]);

//         // Assign role
//         if ($userId) {
//             $role = $this->db->selectOne('roles', 'code = ?', [$_POST['role']]);
//             if ($role) {
//                 $this->db->insert('user_roles', [
//                     'user_id' => $userId,
//                     'role_id' => $role['id']
//                 ]);
//             }

//             $this->logAudit('CREATE', 'users', $userId, null, $_POST);
//             return Response::success(['id' => $userId], 'User created successfully', 201);
//         }

//         return Response::error('Failed to create user', 500);
//     }

//     /**
//      * Update user
//      */
//     public function update()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? $_GET['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         // Validate
//         $rules = [
//             'username' => 'required|min:3|max:50',
//             'email' => 'required|email'
//         ];

//         if (!$this->validator->validate($_POST, $rules)) {
//             return Response::error('Validation failed', 400, $this->validator->getErrors());
//         }

//         // Check duplicates (excluding current user)
//         if ($_POST['username'] != $user['username']) {
//             if ($this->db->count('users', 'username = ?', [$_POST['username']]) > 0) {
//                 return Response::error('Username đã tồn tại', 400);
//             }
//         }

//         // Update
//         $updateData = [
//             'username' => $_POST['username'],
//             'email' => $_POST['email']
//         ];

//         if (!empty($_POST['password'])) {
//             $updateData['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
//         }

//         $this->db->update('users', $updateData, 'id = ?', [$id]);

//         // Update role
//         if (isset($_POST['role'])) {
//             $role = $this->db->selectOne('roles', 'code = ?', [$_POST['role']]);
//             if ($role) {
//                 $this->db->delete('user_roles', 'user_id = ?', [$id]);
//                 $this->db->insert('user_roles', [
//                     'user_id' => $id,
//                     'role_id' => $role['id']
//                 ]);
//             }
//         }

//         $this->logAudit('UPDATE', 'users', $id, $user, $updateData);
//         return Response::success(null, 'User updated successfully');
//     }

//     /**
//      * Delete user
//      */
//     public function delete()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? $_GET['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         $this->db->delete('users', 'id = ?', [$id]);
//         $this->logAudit('DELETE', 'users', $id, $user, null);
        
//         return Response::success(null, 'User deleted successfully');
//     }

//     /**
//      * Toggle user status (Active/Locked)
//      */
//     public function toggleStatus()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         $newStatus = (int)!$user['is_active'];
//         $this->db->update('users', ['is_active' => $newStatus], 'id = ?', [$id]);
        
//         $this->logAudit('TOGGLE_STATUS', 'users', $id, ['is_active' => $user['is_active']], ['is_active' => $newStatus]);
//         return Response::success(['status' => $newStatus], 'Status updated');
//     }

//     /**
//      * RESET PASSWORD - Admin reset user password
//      */
//     public function resetPassword()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         // Generate temporary password
//         $tempPassword = $this->generateTemporaryPassword();
//         $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

//         // Update password
//         $this->db->update('users', [
//             'password_hash' => $hashedPassword,
//             'failed_attempts' => 0,
//             'locked_until' => null
//         ], 'id = ?', [$id]);

//         $this->logAudit('RESET_PASSWORD', 'users', $id, null, ['user_id' => $id, 'new_password_temp' => $tempPassword]);

//         return Response::success([
//             'temp_password' => $tempPassword,
//             'message' => 'Mật khẩu đã được đặt lại. Hãy gửi mật khẩu tạm thời này cho user.'
//         ], 'Password reset successfully');
//     }

//     /**
//      * UNLOCK ACCOUNT - Mở khóa tài khoản bị khóa
//      */
//     public function unlockAccount()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $id = $_POST['id'] ?? null;
//         if (!$id) return Response::error('User ID required', 400);

//         $user = $this->db->selectOne('users', 'id = ?', [$id]);
//         if (!$user) return Response::error('User not found', 404);

//         // Reset failed attempts & unlock
//         $this->db->update('users', [
//             'failed_attempts' => 0,
//             'locked_until' => null
//         ], 'id = ?', [$id]);

//         $this->logAudit('UNLOCK_ACCOUNT', 'users', $id, $user, ['failed_attempts' => 0, 'locked_until' => null]);
        
//         return Response::success(null, 'Account unlocked successfully');
//     }

//     /**
//      * VIEW USER ACTIVITY HISTORY
//      */
//     public function getActivity()
//     {
//         $this->auth->requirePermission('manage_users');
        
//         $userId = $_GET['user_id'] ?? null;
//         if (!$userId) return Response::error('User ID required', 400);

//         $pagination = $this->getPagination();

//         $logs = $this->db->query(
//             "SELECT * FROM audit_logs 
//              WHERE user_id = ? 
//              ORDER BY created_at DESC 
//              LIMIT ? OFFSET ?",
//             [$userId, $pagination['limit'], $pagination['offset']]
//         )->fetch_all(MYSQLI_ASSOC);

//         $total = $this->db->count('audit_logs', 'user_id = ?', [$userId]);

//         return Response::paginate($logs, $total, $pagination['page'], $pagination['limit']);
//     }

//     /**
//      * Generate temporary password
//      */
//     private function generateTemporaryPassword($length = 12)
//     {
//         $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
//         $password = '';
//         for ($i = 0; $i < $length; $i++) {
//             $password .= $characters[random_int(0, strlen($characters) - 1)];
//         }
//         return $password;
//     }
// }
?>

<?php
/**
 * User Controller - Quản lý người dùng
 */
class UserController extends BaseController
{
    /**
     * Get all users
     */
    public function index()
    {
        // ✅ CHỈ DÙNG CHO API - trả JSON
        $this->auth->requirePermissionAPI('manage_users');
        
        $pagination = $this->getPagination();
        $search = isset($_GET['search']) ? '%' . trim($_GET['search']) . '%' : null;
        $role = isset($_GET['role']) ? $_GET['role'] : null;
        
        $where = '';
        $params = [];
        
        if ($search) {
            $where = '(u.username LIKE ? OR u.email LIKE ?)';
            $params = [$search, $search];
        }
        
        if ($role) {
            if ($where) $where .= ' AND ';
            $where .= 'r.code = ?';
            $params[] = $role;
        }
        
        $query = "SELECT u.*, GROUP_CONCAT(r.code SEPARATOR ', ') as roles, GROUP_CONCAT(r.name SEPARATOR ', ') as role_names 
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id = ur.user_id
                  LEFT JOIN roles r ON ur.role_id = r.id";
        
        if ($where) $query .= " WHERE $where";
        
        $query .= " GROUP BY u.id ORDER BY u.id DESC LIMIT ? OFFSET ?";
        
        $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
        $users = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
        // Count
        $countQuery = "SELECT COUNT(DISTINCT u.id) as total FROM users u
                       LEFT JOIN user_roles ur ON u.id = ur.user_id
                       LEFT JOIN roles r ON ur.role_id = r.id";
        
        if ($where) $countQuery .= " WHERE $where";
        
        $countParams = array_values($params);
        $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

        return Response::paginate($users, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Get single user
     */
    public function show()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('User ID required', 400);

        $user = $this->db->selectOne('users', 'id = ?', [$id]);
        if (!$user) return Response::error('User not found', 404);

        $roles = $this->db->query(
            "SELECT r.id, r.code, r.name FROM roles r
             JOIN user_roles ur ON r.id = ur.role_id
             WHERE ur.user_id = ?",
            [$id]
        )->fetch_all(MYSQLI_ASSOC);

        $user['roles'] = $roles;
        unset($user['password_hash']);
        
        return Response::success($user);
    }

    /**
     * Create user
     */
    public function store()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $rules = [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        if ($this->db->count('users', 'username = ?', [$_POST['username']]) > 0) {
            return Response::error('Username đã tồn tại', 400);
        }

        if ($this->db->count('users', 'email = ?', [$_POST['email']]) > 0) {
            return Response::error('Email đã tồn tại', 400);
        }

        $userId = $this->db->insert('users', [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'is_active' => 1,
            'failed_attempts' => 0
        ]);

        if ($userId) {
            $role = $this->db->selectOne('roles', 'code = ?', [$_POST['role']]);
            if ($role) {
                $this->db->insert('user_roles', [
                    'user_id' => $userId,
                    'role_id' => $role['id']
                ]);
            }

            $this->logAudit('CREATE', 'users', $userId, null, $_POST);
            return Response::success(['id' => $userId], 'User created', 201);
        }

        return Response::error('Failed to create user', 500);
    }

    /**
     * Update user
     */
    public function update()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('User ID required', 400);

        $user = $this->db->selectOne('users', 'id = ?', [$id]);
        if (!$user) return Response::error('User not found', 404);

        $rules = [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        if ($_POST['username'] != $user['username']) {
            if ($this->db->count('users', 'username = ?', [$_POST['username']]) > 0) {
                return Response::error('Username đã tồn tại', 400);
            }
        }

        $updateData = [
            'username' => $_POST['username'],
            'email' => $_POST['email']
        ];

        if (!empty($_POST['password'])) {
            $updateData['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $this->db->update('users', $updateData, 'id = ?', [$id]);

        if (isset($_POST['role'])) {
            $role = $this->db->selectOne('roles', 'code = ?', [$_POST['role']]);
            if ($role) {
                $this->db->delete('user_roles', 'user_id = ?', [$id]);
                $this->db->insert('user_roles', [
                    'user_id' => $id,
                    'role_id' => $role['id']
                ]);
            }
        }

        $this->logAudit('UPDATE', 'users', $id, $user, $updateData);
        return Response::success(null, 'User updated');
    }

    /**
     * Delete user
     */
    public function delete()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('User ID required', 400);

        $user = $this->db->selectOne('users', 'id = ?', [$id]);
        if (!$user) return Response::error('User not found', 404);

        $this->db->delete('users', 'id = ?', [$id]);
        $this->logAudit('DELETE', 'users', $id, $user, null);
        
        return Response::success(null, 'User deleted');
    }

    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $id = $_POST['id'] ?? null;
        if (!$id) return Response::error('User ID required', 400);

        $user = $this->db->selectOne('users', 'id = ?', [$id]);
        if (!$user) return Response::error('User not found', 404);

        $newStatus = (int)!$user['is_active'];
        $this->db->update('users', ['is_active' => $newStatus], 'id = ?', [$id]);
        
        $this->logAudit('TOGGLE_STATUS', 'users', $id, ['is_active' => $user['is_active']], ['is_active' => $newStatus]);
        return Response::success(['status' => $newStatus], 'Status updated');
    }

    /**
     * Reset password
     */
    public function resetPassword()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $id = $_POST['id'] ?? null;
        if (!$id) return Response::error('User ID required', 400);

        $user = $this->db->selectOne('users', 'id = ?', [$id]);
        if (!$user) return Response::error('User not found', 404);

        $tempPassword = $this->generateTemporaryPassword();
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        $this->db->update('users', [
            'password_hash' => $hashedPassword,
            'failed_attempts' => 0,
            'locked_until' => null
        ], 'id = ?', [$id]);

        $this->logAudit('RESET_PASSWORD', 'users', $id, null, ['password_reset' => true]);

        return Response::success([
            'temp_password' => $tempPassword
        ], 'Password reset successfully');
    }

    /**
     * Unlock account
     */
    public function unlockAccount()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $id = $_POST['id'] ?? null;
        if (!$id) return Response::error('User ID required', 400);

        $user = $this->db->selectOne('users', 'id = ?', [$id]);
        if (!$user) return Response::error('User not found', 404);

        $this->db->update('users', [
            'failed_attempts' => 0,
            'locked_until' => null
        ], 'id = ?', [$id]);

        $this->logAudit('UNLOCK_ACCOUNT', 'users', $id, $user, null);
        
        return Response::success(null, 'Account unlocked');
    }

    /**
     * Get user activity
     */
    public function getActivity()
    {
        $this->auth->requirePermissionAPI('manage_users');
        
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) return Response::error('User ID required', 400);

        $pagination = $this->getPagination();

        $logs = $this->db->query(
            "SELECT * FROM audit_logs 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?",
            [$userId, $pagination['limit'], $pagination['offset']]
        )->fetch_all(MYSQLI_ASSOC);

        $total = $this->db->count('audit_logs', 'user_id = ?', [$userId]);

        return Response::paginate($logs, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Generate temporary password
     */
    private function generateTemporaryPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }
}
?>