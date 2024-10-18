<?php
use function vehiclecheck\service\php\bitrix\includeD7;
use function vehiclecheck\service\php\phone\getFormatedTelephoneNumberAndProvider;
use function vehiclecheck\service\php\phone\isNumberMSISDN;

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

            require_once APP_BASE_PATH . '/service/php/bitrix.php';

            includeD7();
            //---------------------------------------------------------
            
            $url = "https://gps.point.kz/login.html?";
            $url .= "access_type=0x200";
            $url .= "&duration=0";
            $url .= "&lang=ru";
            echo $url;
            
            //---------------------------------------------------------

            // $currentDateTime = date('Ymd');

            // echo "<pre>"; var_dump($currentDateTime); echo "</pre>";

            //---------------------------------------------------------

            // require_once APP_BASE_PATH . '/service/php/phone.php';

            // $input = '+7 ( 705 ) 123  - 45 67';

            // $input = preg_replace('/\D/', '', $input);

            // $isNumberMSISDN = isNumberMSISDN($input);

            // echo "Номер до: $input, это телефон: $isNumberMSISDN";

            // [$formattedNumber, $provider] = getFormatedTelephoneNumberAndProvider($input, '87777777777');

            // echo "
            // Номер: $formattedNumber, Провайдер: $provider";

            //---------------------------------------------------------

            // $UF_DEPARTMENT = 45;

            // $departmentData = $UF_DEPARTMENT ? \CIBlockSection::GetList(
            //     ["SORT" => "ASC"],
            //     [
            //         'IBLOCK_ID' => '1',
            //         'ID' => $UF_DEPARTMENT,
            //     ],
            //     false,
            //     ['UF_HEAD'],
            // )->fetch() : null;
            
            // echo "<pre>"; print_r($departmentData); echo "</pre>";
            
            //---------------------------------------------------------

            // echo "<pre>"; print_r(\CUser::GetByID(581)->Fetch()); echo "</pre>";

            //---------------------------------------------------------
            // $result = \Bitrix\Main\UserGroupTable::getList([

            //     'filter' => [            
            //         "LOGIC" => "AND",
            //         'GROUP_ID' => 1,
            //     ],
            //     'select' => [
            //         'USER_ID'
            //     ]
            // ]);

            // while ($group = $result->fetch()) {
            //     echo "<pre>"; print_r($group); echo "</pre>";
            // }

            //---------------------------------------------------------
            // $result = \Bitrix\Main\UserGroupTable::getList([

            //     'filter' => [            
            //         "LOGIC" => "AND",
            //         'USER_ID' => 581  , 
            //         [   
            //             "LOGIC" => "OR", 
            //             ['GROUP_ID' => 1],
            //             ['GROUP_ID' => 24],
            //         ]
            //     ],
    
            // ]);

            // while ($group = $result->fetch()) {
            //     echo "<pre>"; print_r($group); echo "</pre>";
            // }

            //---------------------------------------------------------
            // $userCodes = \CAccess::GetUserCodes(838);

            // while ($userCode = $userCodes->fetch()) {
            //     echo "<pre>"; print_r($userCode); echo "</pre>";
            // }

            //---------------------------------------------------------

            //  $fields = [
            //     "PASSWORD" => "t9141849",
            //     "CONFIRM_PASSWORD" => "t9141849",
            //     // "CHECKWORD" => $checkword         // а иначе в $USER->Update() при изменении поля  PASSWORD сгенерируется свой новый CHECKWORD
            // ];

            //  $USER = new CUser;

            //  if ($USER->Update(654, $fields)) {
            //     echo "<pre>"; print_r("Пароль успешно обновлён"); echo "</pre>";
            //     // $message = 'Ваш новый пароль: '.$password;   
            //     // $result = $SMS4B->SendSMS($message, $phone); 
            //     // $arFields['CHECKWORD'] = $checkword;  // меняем на новый для дальнейших манипуляций (отправка кода в письме)
            //  } else {
            //     echo "<pre>"; print_r($USER->LAST_ERROR); echo "</pre>";
            //  }

        } catch (\Throwable $th) {
            return setError($th->getMessage());
        }
    }

}

// Вызов функции getResult для получения результата
$result = getResult();

// Вывод результата в формате JSON
header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE);