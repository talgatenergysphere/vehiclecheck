<?php

namespace vehiclecheck\app\pages;

use function vehiclecheck\service\php\utils\getAppFileUrl;

use function vehiclecheck\app\components\icons\IconEdit;
use function vehiclecheck\app\components\icons\IconCloseSquare;
use function vehiclecheck\app\components\icons\IconPlus;
use function vehiclecheck\app\components\icons\IconPlusSquare;
use function vehiclecheck\app\components\icons\IconCloudPlus;
use function vehiclecheck\app\components\icons\IconRefreshSquare;
use function vehiclecheck\app\components\icons\IconRecoveryConvert;
use function vehiclecheck\app\components\icons\IconWhatsapp;

global $app_vehiclecheck;
global $_ENV;

require_once APP_BASE_PATH . '/service/php/utils.php';
require_once APP_BASE_PATH . '/app/components/icons/IconEdit.php';
require_once APP_BASE_PATH . '/app/components/icons/IconCloseSquare.php';
require_once APP_BASE_PATH . '/app/components/icons/IconPlus.php';
require_once APP_BASE_PATH . '/app/components/icons/IconPlusSquare.php';
require_once APP_BASE_PATH . '/app/components/icons/IconCloudPlus.php';
require_once APP_BASE_PATH . '/app/components/icons/IconRefreshSquare.php';
require_once APP_BASE_PATH . '/app/components/icons/IconRecoveryConvert.php';
require_once APP_BASE_PATH . '/app/components/icons/IconWhatsapp.php';

defined('VHCL_ID_PAGE_VEHICLE');

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        #<?= VHCL_ID_PAGE_VEHICLE ?> {

            page-id {
                --page-id: "#<?= VHCL_ID_PAGE_VEHICLE ?>"
            }

            .vehicle-avatar {
                width: min(15vw, 5rem);
                padding-top: min(15vw, 5rem);
                min-width: min(15vw, 5rem);
            }

            .vehicle-info {
                max-width: 70%;
            }

            .vehicle-info span,
            .vehicle-info .btn {
                font-size: min(calc(.725em + 1.15vw), 24px);
            }

            .close-button {
                border-radius: 10px;
                width: min(10%, 2rem);
                min-width: min(10%, 2rem);
                margin-right: calc(1.25rem - 5px);
            }

            .btn-text-accordion {
                margin-right: 5.5rem;
            }

            .btn-accordion {
                width: 2.5rem;
            }
        }
    </style>
    <?php
};

if (!function_exists('PageVehicle')) {
    function PageVehicle()
    {
        ?>
        <div class="container my-4 px-0">

            <!-- Заголовок -->
            <div class="d-flex w-100 align-items-center gap-2 gap-sm-4 px-3 position-relative">

                <div class="position-relative">
                    <div class="vehicle-avatar position-relative overflow-hidden rounded-circle bg-light">
                        <img class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                            :src="currentVehicle.PHOTO ? currentVehicle.PHOTO : '<?= getAppFileUrl('/public/images/company_icon.png') ?>'" alt="">
                    </div>

                    <span class="position-absolute top-50 start-50 px-3 text-bg-warning text-center fw-bold rounded-1"
                        v-if="!currentVehicle.ACTIVE" style="transform: translate(-50%, -50%) rotate(-15deg);">
                        ОТКЛЮЧЁН
                    </span>
                </div>

                <div class="vehicle-info w-50 flex-fill text-start text-truncate">

                    <span :class="{'btn btn-outline-secondary pt-0 pb-1 px-2 border-0': canUpdateVehicleData}"
                        @click="e => canUpdateVehicleData ? openVehicleUpdateModal(['VEHICLE_MARK', 'VEHICLE_NUMBER']) : null">
                        {{currentVehicle.VEHICLE_MARK ?? 'Марка не указано'}}({{currentVehicle.VEHICLE_NUMBER ?? 'Номер не указан'}})
                    </span>

                </div>

                <button v-if="canPageClose"
                    class="btn btn-outline-secondary border-0 position-absolute top-50 end-0 translate-middle-y close-button p-0"
                    @click="closePage">
                    <?= IconCloseSquare('100%', '100%') ?>
                </button>

            </div>

            <!-- Панель информации -->
            <div class="accordion mt-4" id="accordionPanels">

                <!-- Данные автомобиля -->
                <div class="accordion-item">
                    <h2 class="accordion-header d-flex position-relative">
                        <button class="accordion-button text-bg-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panel-vehicle-data" aria-expanded="true" aria-controls="panel-vehicle-data">
                            <span class="mb-1">Данные</span>
                        </button>

                        <button v-if="canUpdateVehicleData" type="button"
                            class="btn-text-accordion btn btn-outline-primary border-0 position-absolute end-0 top-50 translate-middle-y z-3 fw-semibold"
                            @click="openVehicleUpdateModal('VEHICLE_ACTIVE')">
                            <div class="d-none d-md-block">{{currentVehicle.ACTIVE ? 'Отключить' : 'Восстановить'}} автомобиль
                            </div>
                            <span class="d-inline d-md-none mb-1">{{currentVehicle.ACTIVE ? 'Отключить' : 'Восстановить'}}</span>
                        </button>

                        <button v-if="canUpdateVehicleData" type="button"
                            class="btn-accordion btn btn-outline-primary border-0 position-absolute end-0 top-50 translate-middle-y p-0 py-1 me-5 z-3"
                            @click="openVehicleUpdateModal('VEHICLE_INFO')">
                            <?= IconEdit('80%', '80%') ?>
                        </button>
                    </h2>
                    <div id="panel-vehicle-data" class="accordion-collapse collapse show">
                        <div class="accordion-body border border-1 px-3 px-sm-0 py-2 bg-light-subtle">

                            <form class="form-vehicle-data">

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}

$app_vehiclecheck['scripts'][] = getAppFileUrl('/app/pages/Vehicle/index.js');
