<?php
class Permission
{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function all()
    {
        return $this->conn
            ->query("SELECT * FROM permissions ORDER BY id ASC")
            ->fetch_all(MYSQLI_ASSOC);
    }
}
