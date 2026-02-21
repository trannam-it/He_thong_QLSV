<?php
class User
{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function all()
    {
        return $this->conn
            ->query("SELECT id, username, email FROM users ORDER BY id DESC")
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function assignRole($userId, $roleId)
    {
        // Mỗi user chỉ có 1 role (theo thiết kế hiện tại)
        $this->conn->query("DELETE FROM user_roles WHERE user_id = $userId");

        $stmt = $this->conn->prepare(
            "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $userId, $roleId);
        return $stmt->execute();
    }
}
