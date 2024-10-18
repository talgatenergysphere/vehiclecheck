const PageVehicleModule = {
    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("PageVehicle: Возникла критическая ошибка - " + error.message);
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
            fetchSelf, fetchRest, downloadFile,
            preloaderShow, preloaderHide, preloaderHideWithToast, preloaderHideWithAlert, prepareUserInfo,

        } = GLOBALS.JS;

        const { createApp, ref } = Vue;

        return createApp({
            data() {
                return {
                    currentVehicle: ref({}),
                    canPageClose: ref(vueLayerMain.availablePages.length > 1),

                    canUpdateVehicleData: vueLayerMain.currentUser.SCOPE.includes(VHCL_SCOPE_VEHICLE_UPDATE),
                };
            },
            computed: {

            },
            methods: {

                /*----------------------Стандартные функции страницы--------------------------*/

                openPage(data) {
                    this.currentVehicle = data;

                    console.log('PageVehicle: запущена страница просмотра автомобиля: %o', JSON.parse(JSON.stringify(data)));
                },

                updateData(data = null) {

                    if (data) {
                        this.currentVehicle = data;
                    }
                },

                allLayerComponentsLoaded() {
                },

                closePage() {
                    vueLayerMain.goBackFromPage(this.currentVehicle);
                },

                /*----------------------Расширенные функции страницы--------------------------*/

                openVehicleUpdateModal(dataType, info = null) {
                    vueLayerMain.openModal(VHCL_ID_MODAL_VEHICLE_UPDATE, {
                        dataType: [dataType],
                        vehicle: this.currentVehicle,
                        info: info
                    });
                },

                /*----------------------Завершение описания функци-----------------------------*/
            },
            mounted() {
                console.log('PageVehicle: выполнено монтирование элемента');
            },
            template: elementTemplate.innerHTML,
        });
    },

    init: async function () {
        try {

            const {
                VHCL_ID_PAGE_VEHICLE,
            } = GLOBALS.Identificators;

            const vueLayerMain = GLOBALS?.vueLayerMain;

            const elementTemplate = document.querySelector(`#${VHCL_ID_PAGE_VEHICLE}-template`);

            if (elementTemplate && vueLayerMain?.availablePages?.includes(VHCL_ID_PAGE_VEHICLE)) {

                const element = document.querySelector(`#${VHCL_ID_PAGE_VEHICLE}`);

                if (!element) {
                    throw new Error(`Не найден бланк страницы ${VHCL_ID_PAGE_VEHICLE}`);
                }

                const vueApp = await PageVehicleModule.createApp(vueLayerMain, elementTemplate);

                const vueData = vueApp.mount(element);

                vueLayerMain.layerComponentLoaded(VHCL_ID_PAGE_VEHICLE, vueData);

            }

        } catch (error) {
            PageVehicleModule.handleError(error);
        }
    }
}

document.addEventListener("LayerMainLoaded", PageVehicleModule.init);