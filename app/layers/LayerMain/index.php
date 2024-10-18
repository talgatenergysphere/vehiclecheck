<?php

/**
 * --------------------------------------------------------------------------------------------------------------------
 */

namespace vehiclecheck\app\layers;

use Exception;
use function vehiclecheck\service\php\utils\getAppFileUrl;
use function vehiclecheck\service\php\utils\fetchPost;

use function vehiclecheck\app\components\Preloader;
use function vehiclecheck\app\pages\PageVehicleList;
use function vehiclecheck\app\pages\PageVehicle;
use function vehiclecheck\app\pages\PageVehicleAdd;
use function vehiclecheck\app\modals\ModalVehicleUpdateModule;

global $_ENV;
global $app_vehiclecheck;

/**-------------------------------------------------------------------------------------------------------------------*/

defined('APP_BASE_PATH');
defined('IS_DEV');
defined('APP_CONFIG');

/**-------------------------------------------------------------------------------------------------------------------*/

require_once APP_BASE_PATH . '/config/env.php';
require_once APP_BASE_PATH . '/service/php/utils.php';

/**-------------------------------------------------------------------------------------------------------------------*/

if (empty($_GET['APP_SID'])) {
    $responce = fetchPost($_ENV['VHCL_APP_URL'] . '/api/v1/bitrix/getAccessToken/');
    if (!empty($responce['access_token'])) {
        define('access_token', $responce['access_token']);
    } else {
        throw new Exception('Отсутствуют данные для авторизации');
    }
}

/**-------------------------------------------------------------------------------------------------------------------*/

define('VHCL_ID_LAYER_MAIN', 'layer-main');
define('VHCL_ID_PAGE_VEHICLE_LIST', 'page-vehicle-list');
define('VHCL_ID_PAGE_VEHICLE', 'page-vehicle');
define('VHCL_ID_PAGE_VEHICLE_ADD', 'page-vehicle-add');
define('VHCL_ID_MODAL_VEHICLE_UPDATE', 'modal-vehicle-update');

/**-------------------------------------------------------------------------------------------------------------------*/

$app_vehiclecheck['scripts'][] = function () {
    ?>
    <script>

        if (!window.GLOBALS) {
            window.GLOBALS = {};
        }

        window.GLOBALS.IS_DEV = "<?= IS_DEV ?>";
        window.GLOBALS.appUrl = "<?= $_ENV['VHCL_APP_URL'] ?>";
        window.GLOBALS.restUrl = "<?= $_ENV['BITRIX_REST_DOMEN'] ?>";
        window.GLOBALS.restInstance = "<?= $_ENV['BITRIX_REST_INSTANCE'] ?>";
        window.GLOBALS.restToken = "<?= $_ENV['BITRIX_REST_TOKEN'] ?>";

        window.GLOBALS.bitrixDepartmentParentList = <?= json_encode(APP_CONFIG['VHCL_BITRIX_DEPARTMENT_PARENT_LIST']) ?>;
        window.GLOBALS.bitrixPhoneFieldList = <?= json_encode(APP_CONFIG['VHCL_BITRIX_PHONE_FIELD_LIST']) ?>;

        window.GLOBALS.wialonDomen = "<?= $_ENV['WIALON_DOMEN'] ?>";
        window.GLOBALS.wialonToken = "<?= $_ENV['WIALON_TOKEN'] ?>";

        window.GLOBALS.Scope = {
            VHCL_SCOPE_VEHICLE_LIST_ALL: 'VHCL_SCOPE_VEHICLE_LIST_ALL',
            VHCL_SCOPE_VEHICLE_ADD: 'VHCL_SCOPE_VEHICLE_ADD',
            VHCL_SCOPE_VEHICLE_UPDATE: 'VHCL_SCOPE_VEHICLE_UPDATE',
        };

        window.GLOBALS.Identificators = {
            VHCL_ID_LAYER_MAIN: "<?= VHCL_ID_LAYER_MAIN ?>",
            VHCL_ID_PAGE_VEHICLE_LIST: "<?= VHCL_ID_PAGE_VEHICLE_LIST ?>",
            VHCL_ID_PAGE_VEHICLE: "<?= VHCL_ID_PAGE_VEHICLE ?>",
            VHCL_ID_PAGE_VEHICLE_ADD: "<?= VHCL_ID_PAGE_VEHICLE_ADD ?>",
            VHCL_ID_MODAL_VEHICLE_UPDATE: "<?= VHCL_ID_MODAL_VEHICLE_UPDATE ?>",
        }

        <?php if (defined('access_token')): ?>
            window.GLOBALS.access_token = "<?= access_token ?>";
        <?php endif; ?>
            
        <?php if( IS_DEV ): ?>
            console.log("LayerMain: Получены серверные данные: %o", window.GLOBALS);
        <?php endif; ?>
            
    </script>
    <?php
};

/**-------------------------------------------------------------------------------------------------------------------*/

$app_vehiclecheck['stylesheets'] = array_merge($app_vehiclecheck['stylesheets'] ?? [], [
    getAppFileUrl('/service/cdn/bootstrap.min.css'),
    getAppFileUrl('/service/cdn/swiper-bundle.min.css'),
]);

$app_vehiclecheck['scripts'] = array_merge($app_vehiclecheck['scripts'] ?? [], [
    getAppFileUrl('/service/cdn/bootstrap.bundle.min.js'),
    getAppFileUrl('/service/cdn/swiper-bundle.min.js'),
    getAppFileUrl('/service/cdn/vue.global.js'),
    getAppFileUrl('/service/cdn/wialon.js'),
    "//api.bitrix24.com/api/v1/",
    getAppFileUrl('/service/js/utils.js'),
    getAppFileUrl('/service/js/phone.js'),
    getAppFileUrl('/service/js/wialon.js'),
    getAppFileUrl('/app/layers/LayerMain/index.js'),
]);


$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        #<?= VHCL_ID_LAYER_MAIN ?> {
            
            layer-id {
                --page-id: "#<?= VHCL_ID_LAYER_MAIN ?>"
            }

            svg{
                transition: scale 0.5s ease-in-out;
            }

            svg:active,
            svg:hover{
                scale: 1.1;
            }
        }
    </style>
    <?php
};

/**-------------------------------------------------------------------------------------------------------------------*/

require_once APP_BASE_PATH . '/app/components/Preloader.php';
require_once APP_BASE_PATH . '/app/pages/VehicleList/index.php';
require_once APP_BASE_PATH . '/app/pages/Vehicle/index.php';
require_once APP_BASE_PATH . '/app/pages/VehicleAdd/index.php';
require_once APP_BASE_PATH . '/app/modals/VehicleUpdate/index.php';

/**-------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('LayerMain')) {
    function LayerMain()
    {
        global $app_vehiclecheck;

        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>
                Контроль автомобилей <?= $_ENV['VHCL_APPLICATION_VERSION'] . (IS_DEV ? "(dev)" : null) ?>
            </title>

            <?php foreach ($app_vehiclecheck['stylesheets'] as $stylesheet): ?>
                <?php if (is_callable($stylesheet)): ?>
                    <?= $stylesheet() ?>
                <?php else: ?>
                    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </head>

        <body>
            <?= Preloader() ?>

            <?php
            if (IS_DEV) {
                ?>
                <span class="position-fixed z-3 top-0 start-50 translate-middle-x rounded-bottom-3 text-bg-light px-2">
                    Dev <?= $_ENV['VHCL_APPLICATION_VERSION'] ?>
                </span>
                <?php
            }
            ?>

            <div id="<?= VHCL_ID_LAYER_MAIN ?>" >
                
                <div v-for="(modal, index) in availableModals" :id="modal">
                </div>

                <div class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item" v-for="(page, index) in availablePages" :class="[index==0?'active':'']"
                            :id="page" style="min-height: 100vh;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: none;" id="layer-templates">
                <template id="<?= VHCL_ID_PAGE_VEHICLE_LIST ?>-template">
                    <?= PageVehicleList() ?>
                </template>

                <template id="<?= VHCL_ID_PAGE_VEHICLE ?>-template">
                    <?= PageVehicle() ?>
                </template>

                <template id="<?= VHCL_ID_PAGE_VEHICLE_ADD ?>-template">
                    <?= PageVehicleAdd() ?>
                </template>

                <template id="<?= VHCL_ID_MODAL_VEHICLE_UPDATE ?>-template">
                    <?= ModalVehicleUpdateModule() ?>
                </template>
            </div>

            <?php foreach ($app_vehiclecheck['scripts'] as $script): ?>
                <?php if (is_callable($script)): ?>
                    <?= $script() ?>
                <?php else: ?>
                    <script src="<?= $script ?>"></script>
                <?php endif; ?>
            <?php endforeach; ?>

        </body>
        <?php
    }
}

