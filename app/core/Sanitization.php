<?php
class Sanitization
{
    const FILTERS = [
        'string' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
    ];

    private function array_trim(array $items)
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return trim($item);
            } elseif (is_array($item)) {
                return $this->array_trim($item);
            } else
                return $item;
        }, $items);
    }

    public function sanitize(array $inputs, array $fields = [], int $default_filter = FILTER_SANITIZE_SPECIAL_CHARS, array $filters = self::FILTERS, bool $trim = true)
    {
        if ($fields) {
            foreach ($fields as $key => $field) {
                if ($field == "string" && isset($inputs[$key])) {
                    $tempvar = strip_tags($inputs[$key]);
                    $inputs[$key] = $tempvar;
                }
            }
            $options = array_map(fn ($field) => $filters[trim($field)], $fields);
            $data = filter_var_array($inputs, $options);
        } else {
            $data = filter_var_array($inputs, $default_filter);
        }

        return $trim ? $this->array_trim($data) : $data;
    }
}
