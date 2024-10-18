<?php

namespace vehiclecheck\service\php\sanitize;

use Exception;

if (!function_exists('getSanitizedValues')) {
    function getSanitizedValues(array $keys)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $sanitized_values = [];

        foreach ($keys as $key) {
            $value = $_GET[$key] ?? $data[$key] ?? null;

            if (empty($value)) {
                $sanitized_values[] = null;
                continue;
            }

            // Фильтрация и обработка значений в зависимости от типа
            $sanitized_values[] = sanitizeValue($value, $key);
        }

        return $sanitized_values;
    }
}

if (!function_exists('sanitizeValue')) {
    function sanitizeValue($value, $key)
    {
        if (is_array($value)) {
            // Рекурсивная обработка массивов
            return sanitizeArray($value, $key);
        } elseif (is_string($value)) {
            // Фильтрация строки
            $sanitizedValue = filter_var($value, FILTER_SANITIZE_STRING );
    
            // Проверка на пустую строку после фильтрации
            if (trim($sanitizedValue) === '') {
                throw new Exception("Некорректное текстовое значение для ключа $key");
            }
    
            return $value;
        } elseif (is_numeric($value)) {
            // Обработка целых чисел и чисел с плавающей запятой
            $intValue = filter_var($value, FILTER_VALIDATE_INT);
            if ($intValue !== false) {
                return $intValue;
            }
            $floatValue = filter_var($value, FILTER_VALIDATE_FLOAT);
            if ($floatValue !== false) {
                return $floatValue;
            }
            throw new Exception("Некорректное цифровое значение для ключа $key");
        } elseif (is_bool($value)) {
            $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($boolValue === null) {
                throw new Exception("Некорректное булевое значение для ключа $key");
            }
            return $boolValue;
        } else {
            throw new Exception("Неподдерживаемый тип значения для ключа $key");
        }
    }
}

if (!function_exists('sanitizeArray')) {
    function sanitizeArray($data, $key_parent)
    {
        foreach ($data as $key => $value) {
            $data[$key] = (is_array($value)) 
                ? sanitizeArray($value, "$key_parent:$key") 
                : sanitizeValue($value, "$key_parent:$key");
        }
        return $data;
    }
}
