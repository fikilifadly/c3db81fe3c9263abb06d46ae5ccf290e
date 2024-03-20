<?php

class Validation
{
    const RULES = [
        'required' => 'Data %s harus diisi!',
        'email' => '%s harus berupa email valid!',
        'min' => '%s minimal %d karakter!',
        'unique' => 'email harus unik!',
    ];

    public function validate(array $data, array $fields, array $messages = [])
    {
        $split = fn ($str, $separator) => array_map('trim', explode($separator, $str));
        $rule_message = array_filter($messages, fn ($key) => is_string($key));
        $validation_errors = array_merge($rule_message, self::RULES);
        foreach ($fields as $key => $opt) {
            $rules = $split($opt, '|');
            foreach ($rules as $rule) {
                $params = [];
                if (strpos($rule, ':')) {
                    [$rule, $params] = explode(':', $rule);
                    $params = $split($params, ',');
                } else {
                    $rule_name = trim($rule);
                }

                $fn = 'is_' . $rule_name;
                if (method_exists($this, $fn)) {
                    $pass = $this->$fn($data, $key, ...$params);
                    if (!$pass) {
                        array_push($errors, sprintf($messages[$rule_name] ?? $validation_errors[$rule_name], str_replace(
                            "_",
                            " ",
                            $key
                        ), ...$params));
                    }
                }
            }
        }

        return $errors;
    }

    public function is_required($data, $key)
    {
        return isset($data[$key]) && !empty($data[$key]);
    }

    public function is_email($data, $key)
    {
        return filter_var($data[$key], FILTER_VALIDATE_EMAIL);
    }

    public function is_min($data, $key, $min)
    {
        return strlen($data[$key]) >= $min;
    }
}
