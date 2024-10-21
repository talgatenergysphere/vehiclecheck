<?php

namespace vehiclecheck\service\php\erp1c;

use Exception;
use function vehiclecheck\service\php\bitrix\getITHeadOrAdminList;
use function vehiclecheck\service\php\whatsapp\sendFirstWhatsappMessage;
use function vehiclecheck\service\php\utils\preg_math_title;
use function vehiclecheck\service\php\phone\getPhoneRegex;

/*--------------------Базовые функции модуля-------------------------------------*/

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__, 2)); // Два уровня вверх от service/php
}

if (!function_exists('getServerIP')) {
    function getServerIP()
    {
        require_once APP_BASE_PATH . '/config/env.php';

        // Проверка на наличие пути к контейнеру
        if (empty($_ENV['ERP_1C_IP_CONTAINER_PATH'])) {
            throw new Exception('Не указан путь к контейнеру IP-адреса ERP-1С');
        }

        // Проверка на существование файла по указанному пути
        if (!file_exists($_ENV['ERP_1C_IP_CONTAINER_PATH'])) {
            throw new Exception('Контейнер IP-адреса ERP-1С не найден');
        }

        // Попытка прочитать файл
        $file_content = file_get_contents($_ENV['ERP_1C_IP_CONTAINER_PATH']);
        if ($file_content === false) {
            throw new Exception('Не удалось прочитать содержимое контейнера IP-адреса ERP-1С');
        }

        // Попытка декодировать JSON
        $file_json = json_decode($file_content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ошибка при декодировании JSON ERP-1С: ' . json_last_error_msg());
        }

        // Проверка на наличие ключа 'ip'
        if (!isset($file_json['ip'])) {
            throw new Exception('IP-адрес ERP-1С не найден в контейнере');
        }

        // Возврат IP
        return $file_json['ip'];
    }
}

if (!function_exists('fetch_erp1c')) {
    function fetch_erp1c($base_type, $rest, $method, $data = null)
    {
        $server_ip = getServerIP();

        // Подключаем файл конфигурации
        require_once APP_BASE_PATH . '/config/env.php';

        if (
            empty($_ENV["ERP_1C_{$base_type}_BASE_NAME"])
            || empty($_ENV["ERP_1C_{$base_type}_INSTANCE"])
            || empty($_ENV["ERP_1C_{$base_type}_TOKEN"])
        ) {
            throw new Exception("Отсутствуют данные запроса к базе");
        }

        $domen = $server_ip . $_ENV["ERP_1C_{$base_type}_BASE_NAME"];
        $instance = $_ENV["ERP_1C_{$base_type}_INSTANCE"];
        $token = $_ENV["ERP_1C_{$base_type}_TOKEN"];

        $url = "http://$domen/$rest";

        $ch = curl_init($url);

        // Устанавливаем логин и токен для аутентификации
        curl_setopt($ch, CURLOPT_USERPWD, "$instance:$token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Устанавливаем HTTP-метод
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Если есть данные и метод предполагает тело запроса, добавляем их
        if (!empty($data)) {
            if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            } elseif (in_array($method, ['GET', 'DELETE'])) {
                $queryParams = http_build_query($data);
                if (strpos($url, '?') === false) {
                    $url .= "?$queryParams";
                } else {
                    $url .= "&$queryParams";
                }
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception("1С-$base_type - ошибка выполнения запроса: " . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("1С-$base_type - ошибка при декодировании JSON (" . json_last_error_msg() . '): ' . $response);
        }

        return $result;
    }

}

/*--------------------------------------------------------------------------------*/
/*----------------------------------УПРАВЛЕНИЕ ТОРГОВЛЕЙ--------------------------*/
/*--------------------------------------------------------------------------------*/

/*--------------------1С-БК: Функции тестирования соединения----------------------*/

if (!function_exists('get_test_erp1c_ut')) {
    function get_test_erp1c_ut()
    {
        return fetch_erp1c('UT', "test", "GET");
    }
}

/*--------------------1С-УТ: Функции справочника ТранспортныеСредства--------------*/

if (!function_exists('get_transport_erp1c_ut')) {
    function get_transport_erp1c_ut()
    {
        return fetch_erp1c('UT', "transport", "GET");
    }
}

/*---------------------------------------------------------------------------------*/