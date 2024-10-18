<?php

namespace vehiclecheck\app\modals;

use function vehiclecheck\service\php\utils\getAppFileUrl;

global $app_vehiclecheck;
global $_ENV;

defined('VHCL_ID_MODAL_VEHICLE_UPDATE');

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        #<?= VHCL_ID_MODAL_VEHICLE_UPDATE ?> {
            page-id {
                --page-id: "#<?= VHCL_ID_MODAL_VEHICLE_UPDATE ?>"
            }
        }
    </style>
    <?php
};

if (!function_exists('ModalVehicleUpdateModule')) {
    function ModalVehicleUpdateModule()
    {
        ?>
        <div class="modal fade" tabindex="-1" aria-labelledby="modalVehicleUpdateLabel" aria-hidden="true" ref="modal">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form v-if="currentVehicle" class="modal-content" @submit="submitUpdate">

                    <!-- Заголовок -->
                    <div class="modal-header">
                        <div>
                            <h1 class="modal-title fs-5" id="modalVehicleUpdateLabel">Изменение данных автомобиля</h1>
                            <span class="fst-italic text-primary" id="modalVehicleUpdateLabel"></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Контент -->
                    <div class="modal-body px-3 px-sm-0 py-2 bg-light-subtle">

                        <!-- ID Пользователя -->
                        <input type="hidden" id="ID" name="ID" :value="currentVehicle.ID">
                    </div>

                    <!-- Марка автомобиля -->
                    <template v-if="updatedDataList.includes('VEHICLE_MARK')">
                        <div class="mb-2">
                            <label for="VEHICLE_MARK" class="form-label d-block d-sm-none required">Марка</label>
                            <div class="input-group">
                                <span class="input-group-text d-none d-sm-block border-0 rounded-0 ps-4">Марка</span>
                                <input type="text" class="form-control" id="VEHICLE_MARK" name="VEHICLE_MARK"
                                    v-model="currentVehicle.VEHICLE_MARK" pattern="^[0-9A-Za-zА-Яа-яӘәҒғҚқҢңӨөҰұҮүҺһІіЁё\s]+$"
                                    title="Только буквы, цифры и пробелы, без прочих символов"
                                    placeholder="Введите марку автомобиля" required>
                                <span class="input-group-text d-none d-sm-block border-0 rounded-0"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Номер автомобиля -->
                    <template v-if="updatedDataList.includes('VEHICLE_NUMBER')">
                        <div class="mb-2">
                            <label for="VEHICLE_NUMBER" class="form-label d-block d-sm-none required">Номер</label>
                            <div class="input-group">
                                <span class="input-group-text d-none d-sm-block border-0 rounded-0 ps-4">Номер</span>
                                <input type="text" class="form-control" id="VEHICLE_NUMBER" name="VEHICLE_NUMBER"
                                    v-model="currentVehicle.VEHICLE_NUMBER" pattern="^[0-9A-Z\s]+$"
                                    title="Только латинские буквы, цифры и пробелы" placeholder="Введите номер автомобиля"
                                    required>
                                <span class="input-group-text d-none d-sm-block border-0 rounded-0"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Активность пользователя -->
                    <template v-if="updatedDataList.includes('VEHICLE_ACTIVE')">

                        <input type="hidden" id="ACTIVE" name="ACTIVE" :value="!currentVehicle.ACTIVE">

                        <div class="mb-3" v-if="currentVehicle.ACTIVE">
                            <div class="alert alert-warning text-center" role="alert">
                                Вы действительно уверены в необходимости <strong>отключения</strong> автомобиля?
                            </div>
                        </div>

                        <div class="mb-3" v-else>
                            <div class="alert alert-warning text-center" role="alert">
                                Вы действительно уверены в необходимости <strong>восстановления</strong> автомобиля?
                            </div>
                        </div>

                    </template>

                    <!-- Подвал -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}

$app_vehiclecheck['scripts'][] = getAppFileUrl('/app/modals/VehicleUpdate/index.js');