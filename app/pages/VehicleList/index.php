<?php

namespace vehiclecheck\app\pages;

use function vehiclecheck\app\components\icons\IconClose;
use function vehiclecheck\service\php\utils\getAppFileUrl;

use function vehiclecheck\app\components\icons\IconRight;
use function vehiclecheck\app\components\icons\IconCloseSquare;
use function vehiclecheck\app\components\icons\IconPlus;
use function vehiclecheck\app\components\icons\IconPlusCircle;
use function vehiclecheck\app\components\icons\IconPlusSquare;


global $app_vehiclecheck;
global $_ENV;

require_once APP_BASE_PATH . '/service/php/utils.php';
require_once APP_BASE_PATH . '/app/components/icons/IconRight.php';
require_once APP_BASE_PATH . '/app/components/icons/IconClose.php';
require_once APP_BASE_PATH . '/app/components/icons/IconCloseSquare.php';
require_once APP_BASE_PATH . '/app/components/icons/IconPlus.php';
require_once APP_BASE_PATH . '/app/components/icons/IconPlusCircle.php';
require_once APP_BASE_PATH . '/app/components/icons/IconPlusSquare.php';

defined('VHCL_ID_PAGE_VEHICLE_LIST');

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        #<?= VHCL_ID_PAGE_VEHICLE_LIST ?> {
            page-id {
                --page-id: "#<?= VHCL_ID_PAGE_VEHICLE_ADD ?>"
            }

            .vehicle-avatar {
                width: min(15%, 5rem);
                padding-top: min(15%, 5rem);
                min-width: min(15%, 5rem);
            }

            .vehicle-info {
                max-width: 80%;
            }

            .vehicle-button {
                width: min(10%, 2rem);
                min-width: min(10%, 2rem);
            }

            .add-button {
                width: 4rem;
                height: 4rem;
                --bs-btn-bg: white;
            }
        }
    </style>
    <?php
};

if (!function_exists('PageVehicleList')) {
    function PageVehicleList()
    {
        ?>
        <div class="container my-4 position-relative">

            <div class="input-group input-group-sm my-2">
                <span class="input-group-text" id="inputGroup-sizing-sm">Фильтр</span>
                <input type="text" class="form-control z-0 " aria-label="Фильтр" aria-describedby="inputGroup-sizing-sm"
                    :value="filterText" @input="e => filterText = e.target.value">

                <input type="checkbox" class="btn-check" id="filter-active-button" autocomplete="off" v-model="filterActive">
                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                    for="filter-active-button">Отключены</label>

                <button class="btn btn-outline-secondary p-1" type="button" @click="e => {filterText = ''; filterActive=false;}"
                    :disabled="filterText==''&&!filterActive">
                    <?= IconClose("24px", "24px") ?>
                </button>
            </div>

            <div class="list-group">
                <template v-for="(data, index) in vehicleList">
                    <button v-if="filterData(data)" :key="data['ID']" type="button"
                        class="list-group-item list-group-item-action" @click="e=>openVehicle(data)">
                        <div class="d-flex w-100 align-items-center gap-2 gap-sm-4">

                            <div class="vehicle-avatar position-relative overflow-hidden rounded-circle bg-light">
                                <img class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                                    :src="data.photo ? data.PHOTO : '<?= getAppFileUrl('/public/images/company_icon.png') ?>'"
                                    alt="">
                            </div>

                            <div class="vehicle-info flex-fill text-start text-truncate">
                                <span class="text-body">{{data.VEHICLE_MARK || 'Марка не указана'}}</span>
                                <br>
                                <span class="text-body-secondary">{{data.VEHICLE_NUMBER}}</span>
                            </div>

                            <div class="vehicle-button">
                                <?= IconRight('100%', '100%') ?>
                            </div>
                        </div>
                    </button>
                </template>
            </div>

            <div v-if="canAdAdd" class="position-fixed bottom-0 start-0 z-2 w-100" style="pointer-events: none;">
                <div class="container">
                    <div class="d-flex justify-content-end ">
                        <button class="add-button btn btn-outline-primary shadow-lg p-0 border-3 rounded-4 me-3 mb-3"
                            @click="openVehicleAdd" style="pointer-events: auto;">
                            <?= IconPlus('100%', '100%') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

$app_vehiclecheck['scripts'][] = getAppFileUrl('/app/pages/VehicleList/index.js');