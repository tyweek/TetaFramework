<?php

namespace TetaFramework\Validation;

class Validator
{
    protected $data;
    protected $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function make($data, $rules)
    {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate($rules)
    {
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            foreach ($rulesArray as $rule) {
                if (strpos($rule, ':')) {
                    list($rule, $parameter) = explode(':', $rule);
                } else {
                    $parameter = null;
                }

                $method = "validate" . ucfirst($rule);
                if (method_exists($this, $method)) {
                    print($this->{$method}($field));
                }
            }
        }
    }

    protected function validateRequired($field)
    {
        if (empty($this->data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field is required.';
        }
    }

    protected function validateEmail($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a valid email address.';
        }
    }

    protected function validateInt($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = 'The ' . $field . ' field must be an integer.';
        }
    }

    protected function validateDecimal($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a decimal.';
        }
    }
    protected function validateMax($field, $max)
    {
        if (isset($this->data[$field])) {
            $value = $this->data[$field];
            if (is_numeric($value) && $value > $max) {
                $this->errors[$field][] = 'The ' . $field . ' field must be less than or equal to ' . $max . '.';
            } elseif (is_string($value) && strlen($value) > $max) {
                $this->errors[$field][] = 'The ' . $field . ' field must be less than or equal to ' . $max . ' characters.';
            }
        }
    }
    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }
}
