<?php

use function vehiclecheck\service\php\bitrix\isUserAdminOrHRmanager;
use function vehiclecheck\service\php\erp1c\get_banks_erp1c_bk;
use function vehiclecheck\service\php\erp1c\get_citizenship_erp1c_bk;
use function vehiclecheck\service\php\erp1c\get_contacts_erp1c_bk;
use function vehiclecheck\service\php\erp1c\get_passports_erp1c_bk;
use function vehiclecheck\service\php\sanitize\getSanitizedValues;
use function vehiclecheck\service\php\bitrix\includeD7;
use function vehiclecheck\service\php\erp1c\get_employees_erp1c_ut;
use function vehiclecheck\service\php\erp1c\get_individuals_erp1c_ut;
use function vehiclecheck\service\php\erp1c\get_company_structure_erp1c_ut;
use function vehiclecheck\service\php\erp1c\get_warehouses_erp1c_ut;
use function vehiclecheck\service\php\erp1c\find_employee_erp1c_ut;
use function vehiclecheck\service\php\erp1c\find_individual_erp1c_ut;
use function vehiclecheck\service\php\erp1c\find_individual_erp1c_bk;

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

            global $USER;

            if( !isUserAdminOrHRmanager($USER->getId())){
                return setError("Недостаточно прав для получения данных");
            }

            //----------------------------------------------------------------

            require_once APP_BASE_PATH . '/service/php/erp1c.php';

            //----------------------------------------------------------------

            $ERP_1C_BK_BANKS_DATA = get_banks_erp1c_bk();

            //----------------------------------------------------------------

            return [
                'result' => [
                    'status' => 'ok',
                    'bankList' => $ERP_1C_BK_BANKS_DATA,
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