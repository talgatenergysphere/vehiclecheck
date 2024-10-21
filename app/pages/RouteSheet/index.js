const PageRouteSheetModule = {
    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("PageRouteSheet: Возникла критическая ошибка - " + error.message);
            window.location.reload();
        }
    },

    formatAddress: function (address) {
        const streetTypes = ['ул.', 'пр.', 'шоссе', 'Трасса', 'Көшесі'];

        const address_part = address.split(', ');
        
        const houseNumber = /\d/.test(address_part[1]) ? ', д. ' + address_part[1] : '';


        const regex = new RegExp(`([^,]+)(${streetTypes.join('|')})`, 'i');

        const match = address_part[0].match(regex);

        let streetName;

        if (match) {
            let streetType = match[2].replace('шоссе', 'ш.').replace('Трасса', 'тр.').replace('Көшесі', 'ул.');
            streetName = `${streetType} ${match[1].trim()}`;
        } else{
            streetName = `ул. ${address_part[0].trim()}`;
        }

        return `${streetName}${houseNumber}`;
    },
    
    getWialonTrack: async function (unit, selectedDate) {
        const {
            calculateDistance,
            preloaderHide, wialon_login, wialon_logout, wialon_get_objects, wialon_get_location, wialon_get_locations, wialon_get_track,
        } = GLOBALS.JS;

        await wialon_login();

        let addressList = [];
        let distance;
        
        const track = await wialon_get_track(unit, selectedDate);

        if (track) {

            distance = (track.mileage / 1000).toFixed(1);

            let pos_list = [];

            let current_pos = {
                lat: null,
                lon: null
            }

            let first_time, last_time;
            
            for (const trip of track.trips) {
                
                first_time = first_time ?? trip.first.time;
                last_time = trip.last.time;

                if (
                    calculateDistance(current_pos.lat, current_pos.lon, trip.first.lat, trip.first.lon) > 100
                ) {
                    current_pos.lat = trip.first.lat;
                    current_pos.lon = trip.first.lon;

                    pos_list.push({
                        lat: trip.first.lat,
                        lon: trip.first.lon,
                        time: trip.first.time,
                    });

                }

                if (
                    calculateDistance(current_pos.lat, current_pos.lon, trip.last.lat, trip.last.lon) > 100
                ) {

                    current_pos.lat = trip.last.lat;
                    current_pos.lon = trip.last.lon;

                    pos_list.push({
                        lat: trip.last.lat,
                        lon: trip.last.lon,
                        time: trip.last.time,
                    });

                }
            }

            let locations = await wialon_get_locations(pos_list);

            for (let i = 0; i < locations.length; i++) {
                addressList.push({
                    ...pos_list[i],
                    location: PageRouteSheetModule.formatAddress(locations[i]),
                });
            }

            pos_list =[];
            current_pos = {
                lat: null,
                lon: null
            }
            
            for (const point of track.points) {
                if(point.i == 0 && first_time < point.t && point.t < last_time){
                    
                    if (
                        calculateDistance(current_pos.lat, current_pos.lon, point.pos.y, point.pos.x) > 500
                    ) {
                        
                        current_pos.lat = point.pos.y;
                        current_pos.lon = point.pos.x;
    
                        pos_list.push({
                            lat: point.pos.y,
                            lon: point.pos.x,
                            time: point.t,
                        });
                    }
                }
            }

            locations = await wialon_get_locations(pos_list);

            for (let i = 0; i < locations.length; i++) {
                        
                addressList.push({
                    ...pos_list[i],
                    location: PageRouteSheetModule.formatAddress(locations[i]),
                });

            }

            addressList.sort((a, b) => a.time - b.time);

            current_pos = {
                lat: null,
                lon: null
            }

            addressList = addressList.reduce((list, element) => {
                if (
                    calculateDistance(current_pos.lat, current_pos.lon, element.lat, element.lon) > 500
                ) {
                    current_pos.lat = element.lat;
                    current_pos.lon = element.lon;
                    element.time = new Date(element.time * 1000).toTimeString().split(' ')[0];
                    list.push(element);
                }
                
                return list;
            }, []);

        }

        await wialon_logout();

        console.log('PageRouteSheet: данные Wialon: %O, расстояние: %O', addressList, distance);
        return {addressList, distance};
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

                    wialonData: ref({}),
                };
            },
            computed: {

            },
            methods: {

                /*----------------------Стандартные функции страницы--------------------------*/

                openPage(data) {
                    this.currentVehicle = data;

                    if (!this.currentVehicle.wialonUnit) {
                        preloaderShow();
                        vueLayerMain.initVehicleWialonUnit(this.currentVehicle.VEHICLE_NUMBER).then(wialon_data => {
                            this.currentVehicle.wialonUnit = wialon_data;
                        }).catch(e => {
                            preloaderHideWithAlert('error', 'Запрос к серверу Wialon', 'Не удалось запросить данные об автомобиле: ' + e.message);
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
                    const currentDate = this.selectedDate ? new Date(this.selectedDate) : new Date();
                    currentDate.setDate(currentDate.getDate() + days);
                    this.selectedDate = currentDate.toISOString().split('T')[0];
                    this.changeDate();
                },

                async changeDate() {
                    if (this.selectedDate && !this.wialonData[this.selectedDate]) {

                        preloaderShow();

                        const selectedDate = new Date(this.selectedDate);

                        if (this.currentVehicle.wialonUnit) {
                            this.wialonData[this.selectedDate] = await PageRouteSheetModule.getWialonTrack(this.currentVehicle.wialonUnit, selectedDate);
                        }else{
                            this.wialonData[this.selectedDate] = null;
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