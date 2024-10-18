const PageRouteSheetModule = {
    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("PageRouteSheet: Возникла критическая ошибка - " + error.message);
            window.location.reload();
        }
    },

    getWialonTrack: async function (unit, selectedDate){
        const {
            preloaderHide, wialon_login, wialon_logout, wialon_get_objects, wialon_get_location, wialon_get_locations, wialon_get_track,
        } = GLOBALS.JS;
                
        await wialon_login();

        const result = [];

        const track = await wialon_get_track(unit, selectedDate);


        if( track ){
            const pos_list = [];
            // let current_pos = {};
            for (const trip of track.trips) {
                
                // if( trip.first.lat != current_pos.lat && trip.first.lon != current_pos.lon){

                //     current_pos.lat = trip.first.lat;
                //     current_pos.lon = trip.first.lon;

                    pos_list.push({
                        lon: trip.first.lon, 
                        lat: trip.first.lat
                    });

                // }

                // if( trip.last.lat != current_pos.lat && trip.last.lon != current_pos.lon){
                    
                //     current_pos.lat = trip.last.lat;
                //     current_pos.lon = trip.last.lon;

                    pos_list.push({
                        lon: trip.last.lon, 
                        lat: trip.last.lat
                    });

                // }
            }

            let locations = await wialon_get_locations(pos_list);

            console.log(locations);
            
        }

        await wialon_logout();

        console.log('PageRouteSheet: данные Wialon: %O', track);
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
            fetchSelf, fetchRest, downloadFile,
            preloaderShow, preloaderHide, preloaderHideWithToast, preloaderHideWithAlert, prepareUserInfo,
            wialon_login, wialon_logout, wialon_get_objects, wialon_get_location, wialon_get_track,

        } = GLOBALS.JS;

        const { createApp, ref } = Vue;

        return createApp({
            data() {
                return {
                    currentVehicle: ref({}),
                    canPageClose: ref(vueLayerMain.availablePages.length > 1),

                    selectedDate: ref(null),
                };
            },
            computed: {

            },
            methods: {

                /*----------------------Стандартные функции страницы--------------------------*/

                openPage(data) {
                    this.currentVehicle = data;

                    if( !this.currentVehicle.wialonUnit ){
                        preloaderShow();
                        vueLayerMain.initVehicleWialonUnit(this.currentVehicle.VEHICLE_NUMBER).then(wialon_data=>{
                            this.currentVehicle.wialonUnit = wialon_data;
                        }).catch(e => {
                            preloaderHideWithAlert('error', 'Запрос к серверу Wialon', 'Не удалось запросить данные об автомобиле: '+e.message);
                        }).finally(preloaderHide);
                    }

                    console.log('PageRouteSheet: запущена страница просмотра автомобиля: %o', JSON.parse(JSON.stringify(data)));
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

                moveSelectedDate(days) {
                    const currentDate =  this.selectedDate ? new Date(this.selectedDate) : new Date();
                    currentDate.setDate(currentDate.getDate() + days);
                    this.selectedDate = currentDate.toISOString().split('T')[0]; 
                    this.changeDate();
                },

                async changeDate(){
                    if(this.selectedDate){

                        preloaderShow();

                        const selectedDate = new Date(this.selectedDate);

                        if( this.currentVehicle.wialonUnit ){
                            await PageRouteSheetModule.getWialonTrack(this.currentVehicle.wialonUnit, selectedDate);
                        }

                        preloaderHide();
                    }
                }
                
                /*----------------------Завершение описания функци-----------------------------*/
            },
            mounted() {
                console.log('PageRouteSheet: выполнено монтирование элемента');
            },
            template: elementTemplate.innerHTML,
        });
    },

    init: async function () {
        try {

            const {
                VHCL_ID_PAGE_ROUTE_SHEET,
            } = GLOBALS.Identificators;

            const vueLayerMain = GLOBALS?.vueLayerMain;

            const elementTemplate = document.querySelector(`#${VHCL_ID_PAGE_ROUTE_SHEET}-template`);

            if (elementTemplate && vueLayerMain?.availablePages?.includes(VHCL_ID_PAGE_ROUTE_SHEET)) {

                const element = document.querySelector(`#${VHCL_ID_PAGE_ROUTE_SHEET}`);

                if (!element) {
                    throw new Error(`Не найден бланк страницы ${VHCL_ID_PAGE_ROUTE_SHEET}`);
                }

                const vueApp = await PageRouteSheetModule.createApp(vueLayerMain, elementTemplate);

                const vueData = vueApp.mount(element);

                vueLayerMain.layerComponentLoaded(VHCL_ID_PAGE_ROUTE_SHEET, vueData);

            }

        } catch (error) {
            PageRouteSheetModule.handleError(error);
        }
    }
}

document.addEventListener("LayerMainLoaded", PageRouteSheetModule.init);