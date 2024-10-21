<?php

use function vehiclecheck\service\php\erp1c\get_transport_erp1c_ut;
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

            //----------------------------------------------------------------

            //----------------------------------------------------------------

            require_once APP_BASE_PATH . '/service/php/bitrix.php';

            includeD7();// Проверить авторизацию

            //----------------------------------------------------------------

            require_once APP_BASE_PATH . '/service/php/erp1c.php';

            //----------------------------------------------------------------

            $ERP_1C_UT_TRANSPORT_LIST = get_transport_erp1c_ut();

            //----------------------------------------------------------------

            return [
                'result' => [
                    'status' => 'ok',
                    'transportList' => $ERP_1C_UT_TRANSPORT_LIST,
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