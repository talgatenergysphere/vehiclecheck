<?php

namespace vehiclecheck\service\php\utils;

use Exception;

if( !function_exists('getAppFileUrl')){
    function getAppFileUrl($script){
        global $_ENV;

        if(isset($_ENV['VHCL_APP_URL']) && $_ENV['VHCL_SCRIPT_CACHE']){
            return $_ENV['VHCL_APP_URL'] . $script . '?version=' . $_ENV['VHCL_SCRIPT_CACHE'] ;
        }else{
            throw new Exception('Не удалось сформировать путь для скрипта');
        }
    }
}

if (!function_exists("fetchPost")) {
    function fetchPost($url, $data = null)
    {
        $ch = curl_init($url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    
        $cookies = [];
        
        foreach ($_COOKIE as $key => $value) {
            $cookies[] = "$key=$value";
        }

        $cookies_string = implode('; ', $cookies);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Cookie: $cookies_string"
        ]);
    
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    
        $res = curl_exec($ch);

        curl_close($ch);
        
        $result = json_decode($res, true);
        
        if ($result) {
            if (isset($result['result'])) return $result['result'];
            if (isset($result['error'])) return $result;
            if (is_array($result)) return $result;
        }
    
        return null; // Вернуть null в случае неудачи
    }
}