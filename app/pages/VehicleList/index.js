const PageVehicleListModule = {

    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("PageVehicleList: Возникла критическая ошибка - " + error.message);
            window.location.reload();
        }
    },

    initUserList: async function () {
        const {
            VHCL_SCOPE_VEHICLE_LIST_ALL,
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VEHICLE_UPDATE,
        } = GLOBALS.Scope;

        const result = [];

        result.sort((a, b) => {
            if (a.LAST_NAME < b.LAST_NAME) return -1;
            if (a.LAST_NAME > b.LAST_NAME) return 1;
            if (a.NAME < b.NAME) return -1;
            if (a.NAME > b.NAME) return 1;
            return a.ID - b.ID;
        });

        console.log('PageVehicleList: загружены данные реклам: %o', JSON.parse(JSON.stringify(result)));

        return result;
    },

    createApp: async function (vueLayerMain, elementTemplate) {

        const {
            VHCL_ID_PAGE_VEHICLE,
            VHCL_ID_PAGE_VEHICLE_ADD,
            VHCL_ID_PAGE_DOCUMENT_GENERATOR,
        } = GLOBALS.Identificators;

        const {
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VHCL_INVITE,
            VHCL_SCOPE_USER_ID_VIEW,
        } = GLOBALS.Scope;

        const { createApp, ref } = Vue;

        const vehicleList = await PageVehicleListModule.initUserList(vueLayerMain.vehicleList, vueLayerMain.currentUser);
        
        return createApp({
            data() {
                return {
                    vehicleList: ref(vehicleList),
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
                        }).catch(e => {
                            preloaderHideWithAlert('Не удалось запросить список автомобилей: ' + e.message);
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
                    //todo: delete this rows
                    if( window.GLOBALS.IS_DEV ){
                        // const index = this.vehicleList.findIndex(user => user.ID == 858 );
                        // this.filterText = this.vehicleList[index].title;
                        // this.filterActive = !this.vehicleList[index].active;
                        // vueLayerMain.openPage(VHCL_ID_PAGE_VEHICLE, this.vehicleList[index]);
                    }
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
                        data.TITLE,
                    ].filter(Boolean).join(' ').toUpperCase();

                    const filterWords = filterText.split(' ');
                    
                    return filterWords.some(word => findData.includes(word) );

                },

                openVehicle: (data) => {
                    vueLayerMain.openPage(VHCL_ID_PAGE_VEHICLE, data);
                },

                openVehicleAdd: () => {
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