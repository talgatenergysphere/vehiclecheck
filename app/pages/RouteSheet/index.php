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
use function vehiclecheck\app\components\icons\IconSignPost;
use function vehiclecheck\app\components\icons\IconLeft;
use function vehiclecheck\app\components\icons\IconRight;

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
require_once APP_BASE_PATH . '/app/components/icons/IconSignPost.php';
require_once APP_BASE_PATH . '/app/components/icons/IconLeft.php';
require_once APP_BASE_PATH . '/app/components/icons/IconRight.php';

defined('VHCL_ID_PAGE_ROUTE_SHEET');

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        #<?= VHCL_ID_PAGE_ROUTE_SHEET ?> {

            page-id {
                --page-id: "#<?= VHCL_ID_PAGE_ROUTE_SHEET ?>"
            }

            .close-button {
                border-radius: 10px;
                width: min(10%, 2rem);
                min-width: min(10%, 2rem);
            }

            .slide-fade-enter-active {
                transition: all .3s ease-out;
            }

            .slide-fade-leave-active {
                transition: all .3s cubic-bezier(1, 0.5, 0.8, 1);
            }

            .slide-fade-enter-from {
                position: absolute;
                width: 100%;
                /* bottom: 0; */
                z-index: 2;
                transform: translateY(-20px);
                opacity: 0;
            }

            .slide-fade-leave-to {
                position: absolute;
                width: 100%;
                /* bottom: 0; */
                z-index: 1;
                transform: translateY(-20px);
                opacity: 0;
            }

        }
    </style>
    <?php
};

if (!function_exists('PageRouteSheet')) {
    function PageRouteSheet()
    {
        ?>
        <div class="container my-4 px-0">

            <!-- Заголовок -->
            <div class="d-flex w-100 align-items-center gap-2 gap-sm-4 mb-4 position-relative">

                <div class="vehicle-info flex-fill text-center text-truncate">
                    <span class="text-body fw-bolder">Информация о маршруте</span>
                </div>

                <button v-if="canPageClose"
                    class="btn btn-outline-secondary border-0 position-absolute top-50 end-0 translate-middle-y close-button p-0"
                    @click="closePage">
                    <?= IconCloseSquare('100%', '100%') ?>
                </button>
            </div>

            <!-- Дата -->
            <div class="row justify-content-center m-0 p-2">

                <div class="col-12 col-md-6 position-relative p-0">

                    <div class="input-group mb-3">

                        <button type="button" class="btn btn-outline-primary p-1" @click="moveSelectedDate(-1)">
                            <?= IconLeft("2rem", "2rem") ?>
                        </button>

                        <input type="date" class="form-control" id="SELECTED_DATE" name="SELECTED_DATE" title="Выберите дату"
                            placeholder="Выберите дату" v-model="selectedDate" @change="changeDate">

                        <button type="button" class="btn btn-outline-primary p-1" @click="moveSelectedDate(1)">
                            <?= IconRight("2rem", "2rem") ?>
                        </button>

                    </div>

                    <Transition name="slide-fade">
                        <div v-if="!selectedDate" class="alert alert-info text-center" role="alert">
                            Данные <strong>отсутствуют</strong>
                        </div>
                    </Transition>

                </div>

            </div>

            <Transition name="slide-fade">
                <template v-if="selectedDate">
                    <div class="row justify-content-center m-0 p-2">

                    </div>
                </template>
            </Transition>

        </div>
        <?php
    }
}

$app_vehiclecheck['scripts'][] = getAppFileUrl('/app/pages/RouteSheet/index.js');
