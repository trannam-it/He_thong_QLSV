<?php
class Role
{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function all()
    {
        return $this->conn
            ->query("SELECT * FROM roles ORDER BY id ASC")
            ->fetch_all(MYSQLI_ASSOC);
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
}
