<?php
/**
 * ValidationHelper - Xác thực input
 */

class ValidationHelper
{
    private static $errors = [];

    public static function validate($data, $rules)
    {
        self::$errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $fieldRules) as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $ruleValue] = explode(':', $rule);
                } else {
                    $ruleName = $rule;
                    $ruleValue = null;
                }

                self::applyRule($field, $value, $ruleName, $ruleValue);
            }
        }

        return empty(self::$errors);
    }

    private static function applyRule($field, $value, $rule, $ruleValue)
    {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    self::$errors[$field][] = "$field là bắt buộc";
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    self::$errors[$field][] = "$field không hợp lệ";
                }
                break;

            case 'min':
                if (strlen($value) < $ruleValue) {
                    self::$errors[$field][] = "$field phải tối thiểu $ruleValue ký tự";
                }
                break;

            case 'max':
                if (strlen($value) > $ruleValue) {
                    self::$errors[$field][] = "$field tối đa $ruleValue ký tự";
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    self::$errors[$field][] = "$field phải là số";
                }
                break;

            case 'date':
                if (!strtotime($value)) {
                    self::$errors[$field][] = "$field không phải là ngày hợp lệ";
                }
                break;

            case 'unique':
                // Sẽ implement động
                break;
        }
    }

    public static function getErrors()
    {
        return self::$errors;
    }

    public static function getErrorMessage($field)
    {
        return self::$errors[$field][0] ?? '';
    }
}
?>

        $types = str_repeat('s', count($values));
        $stmt = $this->conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Cập nhật dữ liệu
     */
    public function update($table, $id, $data, $primaryKey = 'id')
    {
        $setClause = implode(',', array_map(fn($col) => "$col = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $types = str_repeat('s', count($data)) . 'i';
        $stmt = $this->conn->prepare("UPDATE $table SET $setClause WHERE $primaryKey = ?");
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Xóa dữ liệu
     */
    public function delete($table, $id, $primaryKey = 'id')
    {
        $stmt = $this->conn->prepare("DELETE FROM $table WHERE $primaryKey = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
    }