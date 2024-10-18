<?php

/**
 * --------------------------------------------------------------------------------------------------------------------
 */

namespace vehiclecheck\app;

use Exception;
use function vehiclecheck\app\layers\LayerMain;
use function vehiclecheck\app\layers\LayerError;

global $_ENV;
global $app_vehiclecheck;

define('APP_BASE_PATH', dirname(__DIR__) );

try {
    
    require_once APP_BASE_PATH . '/config/env.php';
    require_once APP_BASE_PATH . '/config/config.php';

    if(!empty($_GET['ERROR'])){
        throw new Exception($_GET['ERROR']);
    }
    
    require_once APP_BASE_PATH . '/app/layers/LayerMain/index.php';

    LayerMain();

} catch (\Throwable $th) {
    try {
        require_once APP_BASE_PATH . '/app/layers/LayerError/index.php';
        LayerError($th->getMessage());
    } catch (\Throwable $innerTh) {
        echo 'Error: '. $th->getMessage();
    }
}

