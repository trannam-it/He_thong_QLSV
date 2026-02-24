<?php
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/Permission.php';

class RoleController
{
    private $conn;
    private $role;
    private $permission;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->role = new Role($conn);
        $this->permission = new Permission($conn);
    }

    // Danh sách role
    public function index()
    {
        $roles = $this->role->all();
        include __DIR__ . '/../views/roles/index.php';
    }

    // Tạo role
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->role->create($_POST['code'], $_POST['name']);
            header('Location: roles.php');
            exit;
        }
        include __DIR__ . '/../views/roles/create.php';
    }

    // Gán quyền cho role
    public function assignPermissions($roleId)
    {
        $permissions = $this->permission->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->conn->query(
                "DELETE FROM role_permissions WHERE role_id = $roleId"
            );

            foreach ($_POST['permissions'] ?? [] as $pid) {
                $stmt = $this->conn->prepare(
                    "INSERT INTO role_permissions (role_id, permission_id)
                     VALUES (?, ?)"
                );
                $stmt->bind_param("ii", $roleId, $pid);
                $stmt->execute();
            }

            header('Location: roles.php');
            exit;
        }

        include __DIR__ . '/../views/roles/assign.php';
    }
}
