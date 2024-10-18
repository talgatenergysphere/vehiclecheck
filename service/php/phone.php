<?php

namespace vehiclecheck\service\php\phone;

!defined('FProviderEMPTY') && define('FProviderEMPTY', 'Отсутствует');
!defined('FProviderALTEL') && define('FProviderALTEL', 'Altel');
!defined('FProviderTELE2') && define('FProviderTELE2', 'Tele2');
!defined('FProviderKCELL') && define('FProviderKCELL', 'Kcell');
!defined('FProviderBEELINE') && define('FProviderBEELINE', 'Beeline');

!defined('HTMLpattern') && define('HTMLpattern', '(([+]{1}[7]{1})|[78]{1})(([\\(]{1}([0-9]{3})[\\)]{1})|([0-9]{3}))[\\-\s]{0,1}([0-9]{3})[\\-\s]{0,1}([0-9]{2})[\\-\с]{0,1}([0-9]{2})');

!defined('FProviderItemList') && define('FProviderItemList', [
        ['label' => FProviderEMPTY, 'value' => FProviderEMPTY, 'operatorCode' => []],
        ['label' => FProviderALTEL, 'value' => FProviderALTEL, 'operatorCode' => ['700', '708']],
        ['label' => FProviderTELE2, 'value' => FProviderTELE2, 'operatorCode' => ['707', '747']],
        ['label' => FProviderKCELL, 'value' => FProviderKCELL, 'operatorCode' => ['701', '702', '775', '778']],
        ['label' => FProviderBEELINE, 'value' => FProviderBEELINE, 'operatorCode' => ['705', '771', '776', '777']],
    ]);

if (!isset($FProviderRegEx)) {

    $regexOffsiteMSISDN = 0;

    $FProviderRegEx = implode('|', array_filter(array_map(function ($value) use (&$regexOffsiteMSISDN) {
        $regexOffsiteMSISDN += count($value['operatorCode']);
        return implode('|', array_map(function ($code) {
            return "($code)";
        }, $value['operatorCode']));
    }, FProviderItemList)));

    !defined('regexOffsiteMSISDN') && define('regexOffsiteMSISDN', $regexOffsiteMSISDN);
}

!defined('regexMSISDN') && define('regexMSISDN', 
"/^(([+]{1}[7]{1})|[78]{0,1})[\\(\\с]{0,1}($FProviderRegEx)[\\)\\с]{0,1}[\\-\\с]{0,1}([0-9]{3})[\\-\\с]{0,1}([0-9]{2})[\\-\\с]{0,1}([0-9]{2})$/");

!defined('regexICCID') && define('regexICCID', '/^8999([0-9]{3})([0-9]{11})([0-9A-Za-z]{0,2})$/');

if (!function_exists('isNumberMSISDN')) {
    function isNumberMSISDN($value)
    {
        return defined('regexMSISDN') && preg_match(regexMSISDN, $value) === 1;
    }
}

if (!function_exists('isValidInputMSISDN')) {
    function isValidInputMSISDN($value)
    {
        $validChars = '/^[\d\+\(\)\s]*$/'; // Разрешаем только цифры, +, (, ) и пробелы
        $digitsOnly = preg_replace('/\D/', '', $value); // Удаляем все нецифровые символы
        return preg_match($validChars, $value) === 1 && strlen($digitsOnly) <= 11;
    }
}

if (!function_exists('isNumberICCID')) {
    function isNumberICCID($value)
    {
        return defined('regexICCID') && preg_match(regexICCID, $value) === 1;
    }
}

if (!function_exists('getFormatedTelephoneNumberAndProvider')) {
    function getFormatedTelephoneNumberAndProvider($value, $format = '+7(777)777 77 77')
    {

        if (defined('FProviderItemList') && defined('regexMSISDN') && preg_match(regexMSISDN, $value, $reg)) {

            $telephoneNumber = match ($format) {
                '87777777777' => '8' . $reg[3] . $reg[4 + regexOffsiteMSISDN] . $reg[5 + regexOffsiteMSISDN] . $reg[6 + regexOffsiteMSISDN],
                default => '+7(' . $reg[3] . ')' . $reg[4 + regexOffsiteMSISDN] . ' ' . $reg[5 + regexOffsiteMSISDN] . ' ' . $reg[6 + regexOffsiteMSISDN],
            };

            $provider = array_values(array_filter(FProviderItemList, function ($value) use ($reg) {
                return in_array($reg[3], $value['operatorCode']);
            }))[0]['value'];

            return [$telephoneNumber, $provider];
        }

        return [null, null];
    }
}

if (!function_exists('getPhoneRegex')) {
    function getPhoneRegex($phone)
    {
        $cleanPhone = preg_replace('/\D/', '', $phone);
        $normalizedPhone = (strlen($cleanPhone) === 11) ? substr($cleanPhone, 1) : $cleanPhone;
        $phoneRegexText = '';
        for ($i = 0; $i < strlen($normalizedPhone); $i++) {
            $phoneRegexText .= '[\s\-()\+]*' . $normalizedPhone[$i];
        }

        return "/^([\\+7|8]?{$phoneRegexText}[\\s\\-()\\+]*)\$/";
    }
}
