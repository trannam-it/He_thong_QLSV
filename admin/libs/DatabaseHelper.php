<?php
/**
 * DatabaseHelper - Lớp trợ giúp cơ sở dữ liệu
 * Cung cấp các hàm CRUD cơ bản cho tất cả các bảng
 */

class DatabaseHelper
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    /**
     * Lấy tất cả dữ liệu từ một bảng
     */
    public function getAll($table, $limit = null, $offset = 0, $where = '', $params = [])
    {
        $query = "SELECT * FROM $table";
        
        if (!empty($where)) {
            $query .= " WHERE $where";
        }
        
        if (!is_null($limit)) {
            $query .= " LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy một bản ghi theo ID
     */
    public function getById($table, $id, $primaryKey = 'id')
    {
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE $primaryKey = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Thêm dữ liệu mới
     */
    public function insert($table, $data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $values = array_values($data);

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($query);

        $types = $this->getParamTypes($values);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        return $this->conn->insert_id;
    }

    /**
     * Cập nhật dữ liệu
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $set = implode(',', array_map(fn($k) => "$k = ?", array_keys($data)));
        $values = array_values($data);
        $allValues = array_merge($values, $whereParams);

        $query = "UPDATE $table SET $set WHERE $where";
        $stmt = $this->conn->prepare($query);

        $types = $this->getParamTypes($allValues);
        $stmt->bind_param($types, ...$allValues);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    /**
     * Xóa dữ liệu
     */
    public function delete($table, $where, $whereParams = [])
    {
        $query = "DELETE FROM $table WHERE $where";
        $stmt = $this->conn->prepare($query);

        if (!empty($whereParams)) {
            $types = $this->getParamTypes($whereParams);
            $stmt->bind_param($types, ...$whereParams);
        }

        $stmt->execute();
        return $stmt->affected_rows;
    }

    /**
     * Đếm số bản ghi
     */
    public function count($table, $where = '', $params = [])
    {
        $query = "SELECT COUNT(*) as total FROM $table";
        
        if (!empty($where)) {
            $query .= " WHERE $where";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    /**
     * Xác định loại tham số cho bind_param
     */
    private function getParamTypes($params)
    {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }
}
?>
