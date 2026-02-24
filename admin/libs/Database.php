<?php
/**
 * Database wrapper to provide expected methods for controllers
 */
class Database
{
    private $conn;

    public function __construct($mysqli)
    {
        $this->conn = $mysqli;
    }

    /**
     * Execute a query. If $params provided, use prepared statement.
     * Returns mysqli_result on SELECT, or boolean for write queries.
     */
    public function query($sql, $params = [])
    {
        if (empty($params)) {
            return $this->conn->query($sql);
        }

        // prepare
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('DB prepare failed: ' . $this->conn->error);
        }

        // bind params as strings by default
        $types = '';
        $vals = [];
        foreach ($params as $p) {
            if (is_int($p)) $types .= 'i';
            elseif (is_double($p) || is_float($p)) $types .= 'd';
            else $types .= 's';
            $vals[] = $p;
        }

        if (!empty($vals)) {
            $stmt->bind_param($types, ...$vals);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        if ($res === false) {
            // could be write query
            return $stmt;
        }
        return $res;
    }

    public function selectOne($table, $where, $params = [])
    {
        $sql = "SELECT * FROM {$table} WHERE {$where} LIMIT 1";
        $res = $this->query($sql, $params);
        if ($res instanceof mysqli_result) return $res->fetch_assoc();
        return null;
    }

    public function count($table, $where = '', $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        if (!empty($where)) $sql .= " WHERE {$where}";
        $res = $this->query($sql, $params);
        if ($res instanceof mysqli_result) {
            $row = $res->fetch_assoc();
            return (int)$row['total'];
        }
        return 0;
    }

    public function insert($table, $data)
    {
        $cols = array_keys($data);
        $placeholders = implode(',', array_fill(0, count($cols), '?'));
        $colList = implode(',', $cols);
        $sql = "INSERT INTO {$table} ({$colList}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) throw new Exception('DB prepare failed: ' . $this->conn->error);

        $types = '';
        foreach (array_values($data) as $v) {
            if (is_int($v)) $types .= 'i';
            elseif (is_double($v) || is_float($v)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();
        return $this->conn->insert_id ?: false;
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $sets = implode(',', array_map(fn($c) => "$c = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$sets} WHERE {$where}";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) throw new Exception('DB prepare failed: ' . $this->conn->error);

        $values = array_values($data);
        $all = array_merge($values, $whereParams);
        $types = '';
        foreach ($all as $v) {
            if (is_int($v)) $types .= 'i';
            elseif (is_double($v) || is_float($v)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$all);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function delete($table, $where, $whereParams = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) throw new Exception('DB prepare failed: ' . $this->conn->error);
        if (!empty($whereParams)) {
            $types = '';
            foreach ($whereParams as $v) {
                if (is_int($v)) $types .= 'i';
                elseif (is_double($v) || is_float($v)) $types .= 'd';
                else $types .= 's';
            }
            $stmt->bind_param($types, ...$whereParams);
        }
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
