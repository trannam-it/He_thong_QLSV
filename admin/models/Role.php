<?php
class Role
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function all()
    {
        $result = $this->conn->query("SELECT * FROM roles ORDER BY id ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($code, $name)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO roles (code, name) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $code, $name);
        return $stmt->execute();
    }

    public function update($id, $code, $name)
    {
        $stmt = $this->conn->prepare(
            "UPDATE roles SET code = ?, name = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $code, $name, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function setPermissions($roleId, array $permissionIds)
    {
        $stmt = $this->conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->bind_param("i", $roleId);
        $stmt->execute();

        if (empty($permissionIds)) {
            return true;
        }

        $insert = $this->conn->prepare(
            "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)"
        );

        foreach ($permissionIds as $permissionId) {
            $permissionId = (int)$permissionId;
            $insert->bind_param("ii", $roleId, $permissionId);
            if (!$insert->execute()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lấy danh sách user đang có role này
     */
    public function getUsersByRole(int $roleId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT u.id, u.username, u.email
             FROM users u
             JOIN user_roles ur ON u.id = ur.user_id
             WHERE ur.role_id = ?
             ORDER BY u.username ASC"
        );
        $stmt->bind_param("i", $roleId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Tìm kiếm user theo username hoặc email (không thuộc role này)
     */
    public function searchUsers(string $keyword, int $roleId): array
    {
        $like = '%' . $keyword . '%';
        $stmt = $this->conn->prepare(
            "SELECT u.id, u.username, u.email
             FROM users u
             WHERE (u.username LIKE ? OR u.email LIKE ?)
               AND u.id NOT IN (
                   SELECT user_id FROM user_roles WHERE role_id = ?
               )
             ORDER BY u.username ASC
             LIMIT 30"
        );
        $stmt->bind_param("ssi", $like, $like, $roleId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gán role cho user
     */
    public function assignUserRole(int $userId, int $roleId): bool
    {
        // Xóa role cũ của user (mỗi user 1 role)
        $del = $this->conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $del->bind_param("i", $userId);
        $del->execute();

        $stmt = $this->conn->prepare(
            "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $userId, $roleId);
        return $stmt->execute();
    }

    /**
     * Xóa role khỏi user
     */
    public function removeUserRole(int $userId, int $roleId): bool
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM user_roles WHERE user_id = ? AND role_id = ?"
        );
        $stmt->bind_param("ii", $userId, $roleId);
        return $stmt->execute();
    }
}