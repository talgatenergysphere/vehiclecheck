const PageVehicleAddModule = {

    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("Возникла критическая ошибка: " + error.message);
            window.location.reload();
        }
    },

    createApp: async function (vueLayerMain, elementTemplate) {

        const {
            VHCL_ID_PAGE_ROUTE_SHEET,
            VHCL_ID_PAGE_VEHICLE,
            VHCL_ID_PAGE_VEHICLE_ADD,
            VHCL_ID_PAGE_VEHICLE_LIST,
            VHCL_ID_MODAL_VEHICLE_UPDATE,
        } = GLOBALS.Identificators;

        const {
            VHCL_SCOPE_VEHICLE_LIST_ALL,
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VEHICLE_UPDATE,
        } = GLOBALS.Scope;

        const {
            isNumberMSISDN, isValidInputMSISDN, getFormatedTelephoneNumberAndProvider,
            fetchSelf,
            preloaderShow, preloaderHide, preloaderHideWithToast, preloaderHideWithAlert, prepareUserInfo,
        } = GLOBALS.JS;

        const { createApp, ref } = Vue;

        const result = createApp({
            data() {
                return {
                    canPageClose: ref(vueLayerMain.availablePages.length > 1),

                    currentVehicle: ref({}),

                    disableAdd: ref(false),
                };
            },
            computed: {

            },
            methods: {

                /*----------------------Стандартные функции страницы--------------------------*/

                openPage() {
                    this.currentVehicle = {};
                },

                updateData(data = null) {
                },

                allLayerComponentsLoaded() {

                },

                closePage() {
                    vueLayerMain.goBackFromPage();
                },

                /*----------------------Расширенные функции страницы--------------------------*/

                async submitAdd(e) {
                    try {

                        /*---------------------------------------------------------------------------------------*/

                        e.preventDefault();

                        preloaderShow();

                        /*---------------------------------------------------------------------------------------*/

                        const formData = new FormData(e.target);

                        const data = {};

                        for (let [key, value] of formData.entries()) {
                            data[key] = value;
                        }

                        /*---------------------------------------------------------------------------------------*/

                        if( !vueLayerMain.vehicleList ){
                            await vueLayerMain.initVehicleList();
                        }

                        /*---------------------------------------------------------------------------------------*/

                        data.ID = vueLayerMain.vehicleList.length > 0 ? vueLayerMain.vehicleList.reduce((result, vehicle)=>{
                            const vehicle_id = +vehicle.ID;
                            return vehicle_id >= result ? vehicle_id + 1 : result;
                        }, 0) : 1;

                        data.ACTIVE = true;

                        /*---------------------------------------------------------------------------------------*/

                        vueLayerMain.vehicleList.push( data );

                        /*---------------------------------------------------------------------------------------*/

                        await fetchSelf('/api/v1/internal/saveData/', {
                            save_data: vueLayerMain.vehicleList,
                            data_type: 'vehicleList',
                        });

                        /*---------------------------------------------------------------------------------------*/

                        vueLayerMain.openPage(VHCL_ID_PAGE_VEHICLE, data, false);

                        /*---------------------------------------------------------------------------------------*/

                        preloaderHideWithToast('Автомобиль успешно создана');

                        /*---------------------------------------------------------------------------------------*/

                    } catch (error) {

                        preloaderHideWithAlert('error', 'Ошибка операции создания автомобиля', error.message);

                    } finally {
                        preloaderHide();
                    }
                },

                /*----------------------Завершение описания функци-----------------------------*/
            },
            mounted() {
                console.log('PageVehicleAdd: выполнено монтирование элемента');
            },
            template: elementTemplate.innerHTML,
        });

        return result;
    },

    init: async function () {
        try {

            const {
                VHCL_ID_PAGE_VEHICLE_ADD
            } = GLOBALS.Identificators;

            const vueLayerMain = GLOBALS?.vueLayerMain;

            const elementTemplate = document.querySelector(`#${VHCL_ID_PAGE_VEHICLE_ADD}-template`);

            if (elementTemplate && vueLayerMain?.availablePages?.includes(VHCL_ID_PAGE_VEHICLE_ADD)) {

                const element = document.querySelector(`#${VHCL_ID_PAGE_VEHICLE_ADD}`);

                if (!element) {
                    throw new Error(`Не найден бланк страницы ${VHCL_ID_PAGE_VEHICLE_ADD}`);
                }

                const vueApp = await PageVehicleAddModule.createApp(vueLayerMain, elementTemplate);

                const vueData = vueApp.mount(element);

                vueLayerMain.layerComponentLoaded(VHCL_ID_PAGE_VEHICLE_ADD, vueData);

            }

        } catch (error) {
            PageVehicleAddModule.handleError(error);
        }
    },
}

document.addEventListener("LayerMainLoaded", PageVehicleAddModule.init);