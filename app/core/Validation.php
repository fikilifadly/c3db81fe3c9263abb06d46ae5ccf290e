<?php

class Validation
{
    const DEFAULT_VALIDATION_ERRORS = [
        'required' => 'Data %s harus diisi',
        'email' => '%s tidak valid',
        'min' => '%s harus lebih dari %d karakter',
        'max' => '%s harus kurang dari %d karakter',
        'between' => '%s harus diantara %d and %d karakter',
        'same' => '%s and %s tidak sama',
        'alphanumeric' => '%s harus diisi huruf dan angka',
        'secure' => '%s jumalah diantara 8 and 64 characters and ada angka, huruf besar, huruf kecil and dan karakter spesial',
        'unique' => '%s sudah ada',
    ];

    public function validate(array $data, array $fields, array $messages = []): array
    {
        $split = fn ($str, $sparator) => array_map('trim', explode($sparator, $str));

        //get the message rules
        $rule_messages = array_filter($messages, fn ($massage) => is_string($massage));
        //overtite default message
        $validation_errors = array_merge(self::DEFAULT_VALIDATION_ERRORS, $rule_messages);
        $errors = [];
        foreach ($fields as $field => $option) {
            $rules = $split($option, '|');
            foreach ($rules as $rule) {
                $params = [];
                if (strpos($rule, ':')) {
                    [$rule_name, $param_str] = $split($rule, ':');
                    $params = $split($param_str, ',');
                } else {
                    $rule_name = trim($rule);
                }

                $fn = 'is_' . $rule_name;
                if (method_exists(new Validation(), $fn)) {
                    $pass = $this->$fn($data, $field, ...$params);
                    if (!$pass) {
                        array_push(
                            $errors,
                            sprintf(
                                $messages[$field][$rule_name] ?? $validation_errors[$rule_name],
                                str_replace("_", " ", $field),
                                ...$params
                            )
                        );
                    }
                }
            }
        }

        return $errors;
    }

    public function is_required(array $data, string $field): bool
    {
        return isset($data[$field]) && $data[$field] !== '';
    }

    public function is_email(array $data, string $field): bool
    {
        if (empty($data[$field])) {
            return true;
        }

        return filter_var($data[$field], FILTER_VALIDATE_EMAIL);
    }

    public function is_min(array $data, string $field, int $min): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) >= $min;
    }

    public function is_same(array $data, string $field, string $other): bool
    {
        if (isset($data[$field], $data[$other])) {
            return $data[$field] === $data[$other];
        }

        if (!isset($data[$field]) && !isset($data[$other])) {
            return true;
        }

        return false;
    }
}
