<?php

namespace vehiclecheck\app\pages;

use function vehiclecheck\service\php\utils\getAppFileUrl;

use function vehiclecheck\app\components\icons\IconCloseSquare;

global $app_vehiclecheck;
global $_ENV;

require_once APP_BASE_PATH . '/service/php/utils.php';
require_once APP_BASE_PATH . '/app/components/icons/IconCloseSquare.php';

defined('VHCL_ID_PAGE_VEHICLE_ADD');

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        #<?= VHCL_ID_PAGE_VEHICLE_ADD ?> {
            page-id {
                --page-id: "#<?= VHCL_ID_PAGE_VEHICLE_ADD ?>"
            }

            .close-button {
                border-radius: 10px;
                width: min(10%, 2rem);
                min-width: min(10%, 2rem);
            }

            .form-label.required::after {
                content: "*";
                color: red;
                margin-left: 0.25em;
            }

        }
    </style>
    <?php
};

if (!function_exists('PageVehicleAdd')) {
    function PageVehicleAdd()
    {
        ?>
        <div class="container my-4">

            <!-- Заголовок -->
            <div class="d-flex w-100 align-items-center gap-2 gap-sm-4 mb-4 position-relative">

                <div class="flex-fill text-center text-truncate">
                    <span class="text-body fw-bolder">Добавление данных об автомобиля</span>
                </div>

                <button v-if="canPageClose"
                    class="btn btn-outline-secondary border-0 position-absolute top-50 end-0 translate-middle-y close-button p-0"
                    @click="closePage">
                    <?= IconCloseSquare('100%', '100%') ?>
                </button>
            </div>

            <!-- Форма добавления автомобиля -->
            <form @submit="submitAdd">

                <div class="row mb-3 gap-3 gap-md-0">

                    <div class="col-12 col-md-6">
                        <label for="VEHICLE_MARK" class="form-label required">Марка</label>
                        <input type="text" class="form-control" id="VEHICLE_MARK" name="VEHICLE_MARK"
                            pattern="^[0-9A-Za-zА-Яа-яӘәҒғҚқҢңӨөҰұҮүҺһІіЁё\s]+$" v-model="currentVehicle.VEHICLE_MARK"
                            title="Только буквы, цифры и пробелы, без прочих символов" placeholder="Введите марку автомобиля" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="VEHICLE_NUMBER" class="form-label required">Номер</label>
                        <input type="text" class="form-control" id="VEHICLE_NUMBER" name="VEHICLE_NUMBER"
                            pattern="^[0-9A-Z\s]+$" v-model="currentVehicle.VEHICLE_NUMBER"
                            title="Только латинские буквы, цифры и пробелы" placeholder="Введите номер автомобиля" required>
                    </div>

                </div>

                <div class="mb-3">
                    <button type="submit" :disabled="disableAdd" class="btn btn-primary w-100">
                        Добавить
                    </button>
                </div>

            </form>

        </div>
        <?php
    }
}

$app_vehiclecheck['scripts'][] = getAppFileUrl('/app/pages/VehicleAdd/index.js');