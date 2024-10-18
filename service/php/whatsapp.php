<?php

namespace vehiclecheck\service\php\whatsapp;

use Exception;

/*--------------------Базовые функции модуля--------------------------------*/

// Определяем базовый путь к проекту
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__, 2)); // Два уровня вверх от service/php
}

if (!function_exists('fetch_whatsapp')) {
    function fetch_whatsapp($method, $postData = []){
        require_once APP_BASE_PATH . '/config/env.php';

        $domen = $_ENV['WHATSAPP_DOMEN'];
        $instance = $_ENV['WHATSAPP_INSTANCE'];
        $token = $_ENV['WHATSAPP_TOKEN'];

        $url = "$domen$instance/$method";

        $postData['token'] = $token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            throw new Exception('Whatsapp - ошибка выполнения запроса: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}

if (!function_exists('fetch_whatsapp_message')) {
    function fetch_whatsapp_message($phone_number, $message)
    {
        return fetch_whatsapp('messages/chat', [
            'to' => $phone_number,
            'body' => $message,
            'priority' => 10,
        ]);
    }
}

if (!function_exists('fetch_whatsapp_document')) {
    function fetch_whatsapp_document($phone_number, $document_url, $filename, $caption)
    {
        return fetch_whatsapp('messages/document', [
            'to' => $phone_number,
            'filename' => $filename,
            'document' => $document_url,
            'caption' => $caption,
        ]);
    }
}

if (!function_exists('fetch_whatsapp_image')) {
    function fetch_whatsapp_image($phone_number, $image_url, $caption)
    {
        return fetch_whatsapp('messages/image', [
            'to' => $phone_number,
            'image' => $image_url,
            'caption' => $caption,
        ]);
    }
}

/*--------------------Переферийные функции модуля---------------------------*/

if (!function_exists('sendFirstWhastappMessage')) {
    function sendFirstWhastappMessage($userList, $msg)
    {

        foreach ($userList as $user) {

            $phone = $user['PERSONAL_PHONE'] ?? $user['PERSONAL_MOBILE'] ?? $user['WORK_PHONE'] ?? null;

            if ($phone) {
                $responce = fetch_whatsapp_message($phone, $msg);

                if ($responce['sent']) {
                    return true;
                }
            }
        }

        return false;
    }
}