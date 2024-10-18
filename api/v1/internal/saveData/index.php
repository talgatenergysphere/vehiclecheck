<?php

use function vehiclecheck\service\php\sanitize\getSanitizedValues;
use function vehiclecheck\service\php\bitrix\includeD7;

// Определяем базовый путь к проекту
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__, 4));
}

// Функция setError используется для формирования результата с ошибкой
if (!function_exists('setError')) {
    function setError($msg)
    {
        // Установка HTTP-кода ответа 500 (Внутренняя ошибка сервера)
        http_response_code(500);
        // Возвращение данных с информацией об ошибке
        return [
            'result' => [
                'status' => 'error',
                'message' => $msg
            ]
        ];
    }
}

// Основная логика вынесена в функцию getResult
if (!function_exists('getResult')) {
    function getResult()
    {
        try {
            //----------------------------------------------------------------

            require_once APP_BASE_PATH . '/service/php/sanitize.php';

            [
                $save_data,
                $data_type,
            ] = getSanitizedValues([
                'save_data', 
                'data_type',
            ]);

            if( empty($save_data) || empty($data_type) ){
                return setError("Указаны некорректные данные");
            }

            //----------------------------------------------------------------

            require_once APP_BASE_PATH . '/service/php/bitrix.php';

            includeD7();// Проверить авторизацию

            //----------------------------------------------------------------
            
            $jsonContent = json_encode($save_data, JSON_UNESCAPED_UNICODE);

            if ($jsonContent === false) {
                return setError("Ошибка кодирования JSON: " . json_last_error_msg());
            }

            $url = APP_BASE_PATH . ( strpos(__DIR__, "dev") ? "/public/storage/{$data_type}_dev.json" : "/public/storage/{$data_type}.json" );

            file_put_contents($url, $jsonContent);

            //----------------------------------------------------------------

            return [
                'result' => [
                    'status' => 'ok',
                ]
            ];
        } catch (\Throwable $th) {
            // Обработка исключений и возврат результата с ошибкой
            return setError($th->getMessage());
        }
    }
}

// Вызов функции getResult для получения результата
$result = getResult();

// Вывод результата в формате JSON
header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE);