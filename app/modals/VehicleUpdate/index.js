const ModalVehicleUpdateModule = {
    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("VehicleUpdate: Возникла критическая ошибка - " + error.message);
            window.location.reload();
        }
    },

    createApp: async function (vueLayerMain, elementTemplate) {

        const {
            VHCL_ID_MODAL_VEHICLE_UPDATE,
        } = GLOBALS.Identificators;

        const {
            VHCL_SCOPE_VEHICLE_LIST_ALL,
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VEHICLE_UPDATE,
        } = GLOBALS.Scope;

        const {
            isNumberMSISDN, isValidInputMSISDN, getFormatedTelephoneNumberAndProvider,
            fetchSelf, fetchRest, fetchWebHook, 
            preloaderShow, preloaderHide, preloaderHideWithToast, preloaderHideWithAlert, prepareUserInfo, prepareVehicleInfo,
        } = GLOBALS.JS;

        const { createApp, ref } = Vue;

        return createApp({
            data() {
                return {
                    currentVehicle: ref({}),
                    modal: null,
                    updatedDataList: ref([]),
                    additionalParameters: ref(null),

                };
            },
            computed: {
            },
            methods: {

                /*----------------------Стандартные функции модального окна--------------------*/

                openModal(data) {

                    this.currentVehicle = {...data.vehicle};
                    this.updatedDataList = data.dataType;
                    this.additionalParameters = data.info;

                    console.log('ModalVehicleUpdate: запущена модальное окна редактирования автомобиля: %o\n с редактированием данных: %o\n дополнительные данные: %o ',
                        JSON.parse(JSON.stringify(data.vehicle)),
                        data.dataType,
                        data.info
                    );

                    this.modal.show();
                },

                closeModal() {

                },

                allLayerComponentsLoaded() {

                },

                /*----------------------Расширенные функции страницы---------------------------*/

                async submitUpdate(e) {
                    try {
                        e.preventDefault();

                        preloaderShow();

                        /*---------- Обрабатываем данные формы ------------------------------------------------------*/

                        const formData = new FormData(e.target);

                        const data = {};

                        let isUpdated = false;

                        for (let [key, value] of formData.entries()) {
                            if( key != 'ID'){
                                isUpdated = true;
                            }
                            data[key] = value === 'true' ? true : value === 'false' ? false : value;
                        }

                        /*---------- Обновляем данные автомобиля --------------------------------------------------*/
                        console.log(data);
                        
                        if ( isUpdated ) {
                            if( !vueLayerMain.vehicleList ){
                                await vueLayerMain.initVehicleList();
                            }

                            const index = vueLayerMain.vehicleList.findIndex(vehicle => vehicle.ID === this.currentVehicle.ID);

                            if (index !== -1) {
                                const vehicle = vueLayerMain.vehicleList[index];
                                for (const key in data) {
                                    if (Object.prototype.hasOwnProperty.call(data, key)) {
                                        vehicle[key] = data[key];
                                    }
                                }

                                await fetchSelf('/api/v1/internal/saveData/', {
                                    save_data: vueLayerMain.vehicleList,
                                    data_type: 'vehicleList',
                                });
                            } 
                        }

                        /*---------- Проверяем изменение активности --------------------------------------------------------*/

                        if( this.updatedDataList.includes('VEHICLE_ACTIVE') ){
                            this.currentVehicle.ACTIVE = !this.currentVehicle.ACTIVE;
                        }

                        /*---------- Возвращаемся на предыдущую страницу ----------------------------------------------------*/

                        this.currentVehicle = prepareVehicleInfo(this.currentVehicle);

                        this.modal.hide();

                        vueLayerMain.goBackFromModal(this.currentVehicle);

                        preloaderHideWithToast('Данные автомобиля обновлены');

                    } catch (error) {
                        preloaderHideWithAlert('error', 'Ошибка обновления данных автомобиля', error.message)
                    } finally {
                        preloaderHide();
                    };
                }

                /*----------------------Завершение описания функци-----------------------------*/
            },
            mounted() {

                this.modal = new bootstrap.Modal(this.$refs.modal);

                this.$refs.modal.addEventListener('hidden.bs.modal', this.closeModal);

                console.log('ModalVehicleUpdate: выполнено монтирование элемента');
            },
            template: elementTemplate.innerHTML,
        });
    },

    init: async function () {
        try {

            const {
                VHCL_ID_MODAL_VEHICLE_UPDATE,
            } = GLOBALS.Identificators;

            const vueLayerMain = GLOBALS?.vueLayerMain;

            const elementTemplate = document.querySelector(`#${VHCL_ID_MODAL_VEHICLE_UPDATE}-template`);

            if (elementTemplate && vueLayerMain?.availableModals?.includes(VHCL_ID_MODAL_VEHICLE_UPDATE)) {

                const element = document.querySelector(`#${VHCL_ID_MODAL_VEHICLE_UPDATE}`);

                if (!element) {
                    throw new Error(`Не найден бланк модального окна ${VHCL_ID_MODAL_VEHICLE_UPDATE}`);
                }

                const vueApp = await ModalVehicleUpdateModule.createApp(vueLayerMain, elementTemplate);

                const vueData = vueApp.mount(element);

                vueLayerMain.layerComponentLoaded(VHCL_ID_MODAL_VEHICLE_UPDATE, vueData);

            }

        } catch (error) {
            ModalVehicleUpdateModule.handleError(error);
        }
    }
}

document.addEventListener("LayerMainLoaded", ModalVehicleUpdateModule.init);