<?php

class Permission
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function all()
    {
        return $this->conn
            ->query("SELECT id, code, description AS name FROM permissions")
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function getByRole($roleId)
    {
        $stmt = $this->conn->prepare("
            SELECT permission_id FROM role_permissions WHERE role_id = ?
        ");
        $stmt->bind_param("i", $roleId);
        $stmt->execute();

        return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'permission_id');
    }
}