const PageVehicleListModule = {

    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("PageVehicleList: Возникла критическая ошибка - " + error.message);
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
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VHCL_INVITE,
            VHCL_SCOPE_USER_ID_VIEW,
        } = GLOBALS.Scope;

        const { createApp, ref } = Vue;
        
        return createApp({
            data() {
                return {
                    vehicleList: ref([]),
                    filterText: ref(''),
                    filterActive: ref(false),
                    canAdAdd: vueLayerMain.currentUser?.SCOPE?.includes(VHCL_SCOPE_VEHICLE_ADD),
                };
            },
            computed: {

            },
            methods: {

                /*----------------------Стандартные функции страницы--------------------------*/

                openPage() {
                    if( vueLayerMain.vehicleList ){
                        this.vehicleList = vueLayerMain.vehicleList;
                    }else{
                        preloaderShow();
                        vueLayerMain.initVehicleList().then(data => {
                            this.vehicleList = data;
                            //todo: delete this rows
                            if( window.GLOBALS.IS_DEV ){
                                // const index = this.vehicleList.findIndex(data => data.ID == 1 );
                                // this.filterText = this.vehicleList[index].VEHICLE_MARK;
                                // this.filterActive = !this.vehicleList[index].ACTIVE;
                                // vueLayerMain.openPage(VHCL_ID_PAGE_VEHICLE, this.vehicleList[index]);
                                // vueLayerMain.openPage(VHCL_ID_PAGE_ROUTE_SHEET, this.vehicleList[index]);
                            }
                        }).catch(e => {
                            
                            preloaderHideWithAlert('error', 'Запрос к серверу Vehiclecheck', 'Не удалось запросить список автомобилей: ' + e.message);
                        }).finally(preloaderHide);
                    }
                },

                updateData(data = null) {
                    if (data && data.ID ) {
                        const index = this.vehicleList.findIndex(vehicle => vehicle.ID === data.ID);
                        
                        if (index !== -1) {
                            this.vehicleList[index] = data;
                        } else {
                            this.vehicleList.shift(data);
                        }
                    }
                },

                allLayerComponentsLoaded() {
                },

                closePage() {
                    vueLayerMain.goBackFromPage();
                },

                /*----------------------Расширенные функции страницы--------------------------*/

                filterData(data) {
                    
                    if( data.ACTIVE != undefined && this.filterActive == data.ACTIVE ){
                        return false;
                    }

                    if (!this.filterText || this.filterText.trim() === '') {
                        return true;
                    }

                    const filterText = this.filterText.trim().toUpperCase();
                    
                    const findData = [
                        data.VEHICLE_MARK,
                        data.VEHICLE_NUMBER,
                    ].filter(Boolean).join(' ').toUpperCase();

                    const filterWords = filterText.split(' ');
                    
                    return filterWords.some(word => findData.includes(word) );

                },

                openVehiclePage: (data) => {
                    vueLayerMain.openPage(VHCL_ID_PAGE_VEHICLE, data);
                },

                openVehicleAddPage: () => {
                    vueLayerMain.openPage(VHCL_ID_PAGE_VEHICLE_ADD);
                },
                
                /*----------------------Завершение описания функци-----------------------------*/
            },
            mounted() {
                console.log('PageVehicleList: выполнено монтирование элемента');
            },
            template: elementTemplate.innerHTML,
        });
    },

    init: async function () {
        try {

            const {
                VHCL_ID_PAGE_VEHICLE_LIST,
            } = GLOBALS.Identificators;

            const vueLayerMain = GLOBALS?.vueLayerMain;

            const elementTemplate = document.querySelector(`#${VHCL_ID_PAGE_VEHICLE_LIST}-template`);

            if (elementTemplate && vueLayerMain?.availablePages?.includes(VHCL_ID_PAGE_VEHICLE_LIST)) {

                const element = document.querySelector(`#${VHCL_ID_PAGE_VEHICLE_LIST}`)

                if (!element) {
                    throw new Error(`Не найден бланк страницы ${VHCL_ID_PAGE_VEHICLE_LIST}`);
                }

                const vueApp = await PageVehicleListModule.createApp(vueLayerMain, elementTemplate);

                const vueData = vueApp.mount(element);

                vueLayerMain.layerComponentLoaded(VHCL_ID_PAGE_VEHICLE_LIST, vueData);

            }

        } catch (error) {
            PageVehicleListModule.handleError(error);
        }
    }

}

document.addEventListener("LayerMainLoaded", PageVehicleListModule.init);