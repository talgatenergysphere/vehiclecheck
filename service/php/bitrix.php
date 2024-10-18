<?php

namespace vehiclecheck\service\php\bitrix;

use Exception;

if (!function_exists('includeD7')) {
    function includeD7()
    {

        $SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
        $_SERVER['SCRIPT_NAME'] = '/mobile' . $_SERVER['SCRIPT_NAME'];
        require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
        $_SERVER['SCRIPT_NAME'] = $SCRIPT_NAME;
    }
}

if (!function_exists('bitrixStartModule')) {
    function bitrixStartModule()
    {

        define('LANGUAGE_ID', "ru");
        define('SITE_ID', "s1");
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/bx_root.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/start.php';
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/autoload.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/filter_tools.php';

        $GLOBALS["APPLICATION"] = new \CMain;
        $GLOBALS['USER_FIELD_MANAGER'] = new \CUserTypeManager;
    }
}

if (!function_exists('bitrixStartSession')) {
    function bitrixStartSession()
    {

        bitrixStartModule();

        if (!isset($USER)) {
            global $USER;
        }

        $application = \Bitrix\Main\Application::getInstance();
        $application->initializeExtendedKernel(
            [
                "get" => $_GET,
                "post" => $_POST,
                "files" => $_FILES,
                "cookie" => $_COOKIE,
                "server" => $_SERVER,
                "env" => $_ENV
            ]
        );

        $kernelSession = $application->getKernelSession();
        $kernelSession->start();
        $application->getSessionLocalStorageManager()->setUniqueId($kernelSession->getId());

        if (!is_object($USER))
            $USER = new \CUser;

        require_once APP_BASE_PATH . '/config/env.php';

        $GLOBALS['USER']->Login($_ENV['BITRIX_D7_INSTANCE'], $_ENV['BITRIX_D7_TOKEN']);

        includeD7();
    }
}

if (!function_exists('bitrixLogout')) {
    function bitrixLogout()
    {
        global $USER;
        if (is_object($USER)) {
            $USER->Logout();
        }
    }
}

if (!function_exists('getAccessToken')) {
    function getAccessToken()
    {
        global $USER;

        $arApp = \Bitrix\Rest\AppTable::getByClientId(40);

        $arResult = \Bitrix\Rest\Application::getAuthProvider()->get(
            $arApp['CLIENT_ID'],
            $arApp['SCOPE'],
            array(),
            $USER->GetID()
        );

        return $arResult['access_token'];
    }
}

if (!function_exists('isUserAdmin')) {
    function isUserAdmin($ID)
    {

        require_once APP_BASE_PATH . '/config/config.php';
        
        $result = \Bitrix\Main\UserGroupTable::getList([
            'filter' => [  
                "LOGIC" => "AND",
                'USER_ID' => $ID, 
                ['GROUP_ID' => 1],
            ],

        ]);

        return $result->fetch() !== false;
    }
}