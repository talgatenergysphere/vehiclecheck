<?php

global $_ENV;

/*------------------------------------------------------------------------------------------------------------*/

if (!defined('IS_DEV')) {
    define('IS_DEV', strpos(__DIR__, "dev") == true);
}

/*------------------------------------------------------------------------------------------------------------*/

$_ENV['VHCL_APPLICATION_VERSION'] = '0.001';

/*-------------------------BITRIX ENV-------------------------------------------------------------------------*/

switch (IS_DEV) {
    case true:// Development mode
        /*------------------------------------------------------------------------------------------------------------*/

        $_ENV['VHCL_APP_URL'] = 'https://crm.kilem-khan.kz/local/crm.kilem-khan.kz/dev/vehiclecheck';

        $_ENV['VHCL_MOBILE_MARKET_PLACE_APP'] = '/mobile/marketplace/?id=40';

        $_ENV['VHCL_SCRIPT_CACHE'] = $_ENV['VHCL_APPLICATION_VERSION'];
        // $_ENV['VHCL_SCRIPT_CACHE'] = $_ENV['VHCL_APPLICATION_VERSION'] . '&timestamp=' . time();
        
        /*------------------------------------------------------------------------------------------------------------*/
        break;
    case false:// Production mode
        /*------------------------------------------------------------------------------------------------------------*/

        /*------------------------------------------------------------------------------------------------------------*/
        break;
    default:
        break;
}

/*------------------------------------------------------------------------------------------------------------*/

/*-------------------------REST ENV---------------------------------------------------------------------------*/

$_ENV['BITRIX_REST_DOMEN'] = 'https://crm.kilem-khan.kz/rest/';

$_ENV['BITRIX_REST_INSTANCE'] = '581';

$_ENV['BITRIX_REST_TOKEN'] = '29zhx2lb4259wzvq';

$_ENV['BITRIX_REST_NOTIFICATION_INSTANCE'] = '525';

$_ENV['BITRIX_REST_NOTIFICATION_TOKEN'] = 'ng8t42trukh8gq84';

$_ENV['BITRIX_D7_INSTANCE'] = "kilem.verification@mail.ru";

$_ENV['BITRIX_D7_TOKEN'] = "123qweasdzxcCde!$";


/*-------------------------WHATSAPP ENV-----------------------------------------------------------------------*/

$_ENV['WHATSAPP_DOMEN'] = 'https://api.ultramsg.net/';

$_ENV['WHATSAPP_INSTANCE'] = 'instance67589';

$_ENV['WHATSAPP_TOKEN'] = 'sycfrnx9ryoh6syr';

/*-------------------------WIALON ENV-------------------------------------------------------------------------*/

$_ENV['WIALON_DOMEN'] = "https://gps.point.kz";

$_ENV['WIALON_TOKEN'] = '87e8b1a4c0043c80fc4f6de06177543e28F5CDD3373CC861DD9AC63304C49AAB26B0ECA8'; 
// $_ENV['WIALON_TOKEN'] = '87e8b1a4c0043c80fc4f6de06177543eD718DCE58C436C93A5FBCCE8C2EF4FEB75102AAB'; // Права: просмотр данных
// $_ENV['WIALON_TOKEN'] = '87e8b1a4c0043c80fc4f6de06177543eF9A6C196D46A4D6ABD2087C07474457448CDA386'; // Права: слежение онлайн

/*------------------------------------------------------------------------------------------------------------*/