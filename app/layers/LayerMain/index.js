const LayerMainModule = {

    handleError: function (error) {
        if (GLOBALS?.JS?.appError) {
            GLOBALS.JS.appError(error);
        } else {
            alert("Возинкла критическая ошибка: " + error.message);
            window.location.reload();
        }
    },

    /**
     * Инициализация данных текущего пользователя.
     */

    initCurrentUser: async function () {

        /*--- Формируем данные о пользователе ---------------------------------------------------------------*/

        const {
            fetchRest, prepareUserInfo
        } = GLOBALS.JS;

        // Получаем данные текущего пользователя
        var currentUser = await fetchRest('user.current');

        // Выполняем подготовку информации о сотруднике
        currentUser = prepareUserInfo(currentUser);

        // Проверяем, является ли текущий пользователь администратором
        currentUser.IS_ADMIN = await fetchRest('user.admin');

        /*--- Возвращаем объект с данными текущего пользователя ---------------------------------------------*/
        console.log('LayerMain: текущий пользователь: %o', currentUser);

        // alert(alertMessage);
        return currentUser;
    },

    initVehicleList: async function (){
        const { fetchSelf } = GLOBALS.JS;
        
        var vehicleList = [];

        try {

            const url = window.GLOBALS?.IS_DEV ? "/public/storage/vehicleList_dev.json" : "/public/storage/vehicleList.json";

            const vehicleListResponce = await fetchSelf(url);

            if( vehicleListResponce && Array.isArray(vehicleListResponce)){
                vehicleList = vehicleListResponce;
            }
        } catch (error) {
            console.log(error);
        }

        console.log('LayerMain: получены данные компании: %o', vehicleList);

        return vehicleList;
    },

    initErp1cData: async function (){
        const { fetchSelf } = GLOBALS.JS;
        
        var transportList;

        var responce;

        try {
            responce = await fetchSelf('/api/v1/erp1c/get1cData/');
        } catch (error) {
            throw new Error("Не удалось запросить в 1C данные о банках");
        }
        
        if (responce?.result?.status == 'ok' && responce?.result?.transportList) {
            transportList = responce.result.transportList;
        }

        console.log('LayerMain: запрошены данные в 1c: %o', {transportList});

        return {transportList};
    },

    /** 
     * Определение разрешений автомобиля
     */

    calculateScope: function (currentUser) {

        const {
            VHCL_SCOPE_VEHICLE_LIST_ALL,
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VEHICLE_UPDATE,
        } = GLOBALS.Scope;

        const result = [];

        // Устанавливаем права доступа в зависимости от роли автомобиля
        result.push(VHCL_SCOPE_VEHICLE_LIST_ALL);
        result.push(VHCL_SCOPE_VEHICLE_ADD);
        result.push(VHCL_SCOPE_VEHICLE_UPDATE);

        console.log('LayerMain: выполнен расчёт разрешений: %o', result);

        return result;
    },

    /** 
     * Определение доступных пользователю страниц
     */

    calculatePages: function (currentUser) {

        const {
            VHCL_SCOPE_VEHICLE_LIST_ALL,
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VEHICLE_UPDATE,
        } = GLOBALS.Scope;

        const {
            VHCL_ID_PAGE_ROUTE_SHEET,
            VHCL_ID_PAGE_VEHICLE,
            VHCL_ID_PAGE_VEHICLE_ADD,
            VHCL_ID_PAGE_VEHICLE_LIST,
            VHCL_ID_MODAL_VEHICLE_UPDATE,
        } = GLOBALS.Identificators;

        const result = [];

        if (
            currentUser?.SCOPE?.includes(VHCL_SCOPE_VEHICLE_LIST_ALL)
        ) {
            result.push(VHCL_ID_PAGE_VEHICLE_LIST);
        }

        if (
            currentUser?.SCOPE?.includes(VHCL_SCOPE_VEHICLE_ADD)
        ) {
            result.push(VHCL_ID_PAGE_VEHICLE_ADD);
        }

        result.push(VHCL_ID_PAGE_VEHICLE);
        result.push(VHCL_ID_PAGE_ROUTE_SHEET);

        console.log('LayerMain: выполнен расчёт страниц: %o', result);

        return result;
    },

    /** 
     * Определение доступных пользователю модальных окон
     */

    calculateModals: function (currentUser) {

        const {
            VHCL_SCOPE_VEHICLE_LIST_ALL,
            VHCL_SCOPE_VEHICLE_ADD,
            VHCL_SCOPE_VEHICLE_UPDATE,
        } = GLOBALS.Scope;

        const {
            VHCL_ID_PAGE_ROUTE_SHEET,
            VHCL_ID_PAGE_VEHICLE,
            VHCL_ID_PAGE_VEHICLE_ADD,
            VHCL_ID_PAGE_VEHICLE_LIST,
            VHCL_ID_MODAL_VEHICLE_UPDATE,
        } = GLOBALS.Identificators;

        const result = [];

        result.push(VHCL_ID_MODAL_VEHICLE_UPDATE);

        console.log('LayerMain: выполнен расчёт модальных окон: %o', result);

        return result;
    },

    /** 
     * Отправка события завершения иниацилизации
     */

    dispatchEventFinish: function () {

        console.log('LayerMain: завершена загрузка данных');

        const layerMainLoadedEvent = new CustomEvent("LayerMainLoaded", {
            bubbles: true,
            cancelable: true,
        });

        document.dispatchEvent(layerMainLoadedEvent);
    },

    /** 
     * Отправка события завершения иниацилизации
     */

    deletePageTemplates: function () {

        document.querySelector('#layer-templates')?.remove();
    },

    initVehicleWialonUnit: async function (VEHICLE_NUMBER) {
        const {
            preloaderHide, wialon_login, wialon_logout, wialon_get_objects, wialon_get_location, wialon_get_track,
        } = GLOBALS.JS;
        
        await wialon_login();

        const wialon_objects = await wialon_get_objects();

        // const selected_date = new Date("2024-10-17");

        let result = null;

        for (const unit of wialon_objects) {
            // var pos = unit.getPosition();
            // const address = await wialon_get_location(pos);
            // const track = await wialon_get_track(unit, selected_date);
            // console.log("ID: %O\nName: %O\nIcon: %O\nPos: %O\nAddress: %O\ntrack: %O\n", unit.getId(), unit.getName(), unit.getIconUrl(32), pos, address[0], track );
            if (unit.getName() === VEHICLE_NUMBER) {
                result = unit;
                break;
            }
        }

        await wialon_logout();

        console.log('LayerMain: получены данные автомобиля VEHICLE_NUMBER в системе Wialon: %O', result);

        return result;
    },

    /** 
     * Сборка стейт менеджера приложения
     */

    createApp: async function (carousel) {

        const {
            VHCL_ID_PAGE_ROUTE_SHEET,
            VHCL_ID_PAGE_VEHICLE,
            VHCL_ID_PAGE_VEHICLE_ADD,
            VHCL_ID_PAGE_VEHICLE_LIST,
            VHCL_ID_MODAL_VEHICLE_UPDATE,
        } = GLOBALS.Identificators;

        const {
            preloaderHide, wialon_login, wialon_logout, wialon_get_objects, wialon_get_location, wialon_get_track,
        } = GLOBALS.JS;

        const { createApp, ref } = Vue;

        const currentUser = await LayerMainModule.initCurrentUser();

        currentUser.SCOPE = LayerMainModule.calculateScope(currentUser);

        const availablePages = LayerMainModule.calculatePages(currentUser);

        const availableModals = LayerMainModule.calculateModals(currentUser);

        // await wialon_login();

        // const wialon_objects = await wialon_get_objects();

        // const selected_date = new Date("2024-10-17");

        // for (const unit of wialon_objects) {
        //     var pos = unit.getPosition();
        //     const address = await wialon_get_location(pos);
        //     const track = await wialon_get_track(unit, selected_date);
        //     console.log("ID: %O\nName: %O\nIcon: %O\nPos: %O\nAddress: %O\ntrack: %O\n", unit.getId(), unit.getName(), unit.getIconUrl(32), pos, address[0], track );
        // }

        // await wialon_logout();

        // console.log('LayerMain: данные Wialon: %O', wialon_objects);

        return createApp({
            data() {
                return {
                    currentUser: ref(currentUser),
                    availablePages: ref(availablePages),
                    loadedPages: [],
                    carousel: carousel,
                    historyPages: [],
                    currentPageIndex: 0,
                    availableModals: ref(availableModals),
                    loadedModals: [],

                    vehicleList: null,
                    transportList: null,
                };
            },
            computed: {

            },
            methods: {

                /*----------------------Стандартные функции слоя-----------------------------*/

                openPage(page_id, data = null, history = true) {

                    if (history) {
                        this.historyPages.push({
                            index: this.currentPageIndex,
                            scrollY: window.scrollY,
                        });
                    }

                    const page_index = this.availablePages.indexOf(page_id);

                    this.currentPageIndex = page_index;

                    this.loadedPages[page_index].openPage(data);

                    carousel.to(page_index);

                    window.scrollTo({
                        top: 0,
                        behavior: "smooth",
                    });
                },

                goBackFromPage(data = null) {

                    if (this.historyPages.length > 0) {

                        const page_in_history = this.historyPages.pop();

                        const page_index = page_in_history.index;

                        this.currentPageIndex = page_index;

                        console.log('LayerMain: выполняется возврат к странице %s с данными: %O', this.availablePages[page_index], data);

                        this.loadedPages[page_index].updateData(data);

                        carousel.to(this.currentPageIndex);

                        window.scrollTo({
                            top: page_in_history.scrollY,
                            behavior: "smooth",
                        })

                    } else {
                        LayerMainModule.handleError(new Error('LayerMain: отсутствует страница для возврата'));
                    }
                },

                openModal(modal_id, data = null) {

                    const modal_index = this.availableModals.indexOf(modal_id);

                    this.loadedModals[modal_index].openModal(data);

                },

                goBackFromModal(data = null) {
                    
                    this.loadedPages[this.currentPageIndex].updateData(data);

                    console.log('LayerMain: выполняется возврат к странице %s из модального окна с данными: %O', this.availablePages[this.currentPageIndex], data);

                },

                layerComponentLoaded(page_id, vueData) {

                    // Находим индекс страницы в availablePages
                    const page_index = this.availablePages.indexOf(page_id);

                    // Если индекс страницы найден
                    if (page_index !== -1) {
                        // Записываем данные страницы в loadedPages
                        this.loadedPages[page_index] = vueData;
                    }else{
                        // Находим индекс модального окна в availablePages
                        const modal_index = this.availableModals.indexOf(page_id);
    
                        // Если индекс модального окна найден
                        if (modal_index !== -1) {
                            // Записываем данные модального окна в loadedPages
                            this.loadedModals[modal_index] = vueData;
                        }
                    }

                    // Проверяем, все ли страницы загружены
                    const allPagesLoaded = this.availablePages.every((page, index) => this.loadedPages[index] !== undefined);
                    const allModalsLoaded = this.availableModals.every((page, index) => this.loadedModals[index] !== undefined);

                    // Если все страницы загружены
                    if (allPagesLoaded && allModalsLoaded) {
                        this.allLayerComponentsLoaded();
                    }
                },

                allLayerComponentsLoaded() {
                    LayerMainModule.deletePageTemplates();

                    if (this.availablePages.length == 1 && this.availablePages.includes(VHCL_ID_PAGE_VEHICLE)) {
                        this.openPage(VHCL_ID_PAGE_VEHICLE, this.currentUser, false);
                    }else{
                        this.openPage(this.availablePages[0], this.currentUser, false);
                    }

                    this.loadedPages.forEach(vueData => {
                        vueData.allLayerComponentsLoaded();
                    });

                    this.loadedModals.forEach(vueData => {
                        vueData.allLayerComponentsLoaded();
                    });
                    
                    preloaderHide();
                },

                /*----------------------Расширенные функции слоя------------------------------*/

                async initVehicleList() {
                    const result = await LayerMainModule.initVehicleList();

                    this.vehicleList = result;

                    return result;
                },

                initVehicleWialonUnit(VEHICLE_NUMBER) {
                    return LayerMainModule.initVehicleWialonUnit(VEHICLE_NUMBER);
                },

                async initErp1CData() {
                    const result = await LayerMainModule.initErp1cData();

                    this.transportList = result.transportList;

                    return result;
                },

                /*----------------------Завершение описания функци-----------------------------*/
            },
            mounted() {
                console.log('LayerMain: выполнено монтирование элемента');
            },
        })
    },

    init: async function () {
        try {

            /*--------------------------------------------------------------------------*/

            const {
                VHCL_ID_LAYER_MAIN,
            } = GLOBALS.Identificators;

            /*--------------------------------------------------------------------------*/

            const vueElement = document.querySelector(`#${VHCL_ID_LAYER_MAIN}`)

            if (!vueElement) {
                throw new Error(`Не найден бланк слоя ${VHCL_ID_LAYER_MAIN}`);
            }

            /*--Создание Vue контроллёра для панели с данными автомобиля---------------*/

            const carousel = new bootstrap.Carousel(vueElement, {
                interval: 300,
                touch: false
            });

            /*--Создание Vue контроллёра для панели с данными автомобиля---------------*/

            const vueApp = await LayerMainModule.createApp(carousel);

            const vueData = vueApp.mount(vueElement);

            window.GLOBALS.vueLayerMain = vueData;

            LayerMainModule.dispatchEventFinish();

        } catch (error) {
            LayerMainModule.handleError(error);
        }
    }
}

document.addEventListener("DOMContentLoaded", LayerMainModule.init);