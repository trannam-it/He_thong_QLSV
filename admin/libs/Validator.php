<?php
/**
 * Validator Class
 * Input validation & sanitization
 */
class Validator
{
    private $errors = [];

    public function validate($data, $rules)
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            $ruleArray = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleArray as $rule) {
                $this->checkRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function checkRule($field, $value, $rule)
    {
        $rule = trim($rule);
        
        if (strpos($rule, ':') !== false) {
            [$ruleName, $ruleParam] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $ruleParam = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, ucfirst($field) . ' là bắt buộc');
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, ucfirst($field) . ' không hợp lệ');
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleParam) {
                    $this->addError($field, ucfirst($field) . " tối thiểu {$ruleParam} ký tự");
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleParam) {
                    $this->addError($field, ucfirst($field) . " tối đa {$ruleParam} ký tự");
                }
                break;

            case 'minval':
                if (!empty($value) && is_numeric($value) && (float)$value < (float)$ruleParam) {
                    $this->addError($field, ucfirst($field) . " phải ≥ {$ruleParam}");
                }
                break;

            case 'maxval':
                if (!empty($value) && is_numeric($value) && (float)$value > (float)$ruleParam) {
                    $this->addError($field, ucfirst($field) . " phải ≤ {$ruleParam}");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, ucfirst($field) . ' phải là số');
                }
                break;

            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, ucfirst($field) . ' không hợp lệ');
                }
                break;

            case 'unique':
                // Được xử lý trong controller
                break;

            case 'phone':
                if (!empty($value) && !preg_match('/^[0-9]{10,11}$/', str_replace(['-', ' '], '', $value))) {
                    $this->addError($field, ucfirst($field) . ' không hợp lệ');
                }
                break;
        }
    }

    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Sanitize input
     */
    public static function sanitize($data)
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_array($value) ? self::sanitize($value) : htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }
}
?>