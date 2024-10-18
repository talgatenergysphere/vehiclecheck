if (!window.GLOBALS) {
    window.GLOBALS = {
        JS: {}
    };
} else if (!window.GLOBALS.JS) {
    window.GLOBALS.JS = {};
}

// Login to server using entered username and password
window.GLOBALS.JS.wialon_login = async () => {
    return new Promise((resolve, reject) => {

        var sess = wialon.core.Session.getInstance(); // get instance of current Session
        var user = sess.getCurrUser(); // get current User
        if (user) { // if user exists - you are already logged, print username to log
            resolve(user);
            return;
        }

        // if not logged
        var token = window.GLOBALS.wialonToken; // get token from input
        if (!token) { // if token is empty - print message to log
            reject(new Error('Отсутствует токен авторизации в системе gps-трекинга Wialon'));
            return;
        }

        var domen = window.GLOBALS.wialonDomen; // get domen from input
        if (!domen) { // if domen is empty - print message to log
            reject(new Error('Отсутствует домен авторизации в системе gps-трекинга Wialon'));
            return;
        }

        sess.initSession(domen); // initialize Wialon session
        sess.loginToken(token, "", // trying login 
            function (code) { // login callback
                if (code) {
                    reject(new Error('Ошибка при авторизации в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                } else {
                    resolve(sess.getCurrUser()); // get current user and resolve promise
                }
            }
        );
    });
}

window.GLOBALS.JS.wialon_logout = async () => {
    return new Promise((resolve, reject) => {
        var user = wialon.core.Session.getInstance().getCurrUser(); // get current user
        if (!user) {
            resolve();
            return;
        }
        wialon.core.Session.getInstance().logout( // if user exist - logout
            function (code) { // logout callback
                if (code) {
                    reject(new Error('Ошибка при авторизации в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                    return;
                } else {
                    resolve(); // get current user and resolve promise
                    return;
                }
            }
        );
    });
}

window.GLOBALS.JS.wialon_get_objects = async () => {
    return new Promise((resolve, reject) => {

        var sess = wialon.core.Session.getInstance(); // get instance of current Session
        // flags to specify what kind of data should be returned
        var flags = wialon.item.Item.dataFlag.base | wialon.item.Unit.dataFlag.lastMessage;

        sess.loadLibrary("itemIcon"); // load Icon Library	
        sess.updateDataFlags( // load items to current session
            [{ type: "type", data: "avl_unit", flags: flags, mode: 0 }], // Items specification
            async function (code) { // updateDataFlags callback

                if (code) {
                    reject(new Error('Ошибка при выгрузке объектов системы gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                    return;
                } // exit if error code

                // get loaded 'avl_unit's items  
                var units = sess.getItems("avl_unit");

                resolve(units);
                return;
            }
        );
    });
}

window.GLOBALS.JS.wialon_get_location = async (pos) => {
    return new Promise((resolve, reject) => {
        wialon.util.Gis.getLocations([{ lon: pos.x, lat: pos.y }], function (code, address) {
            if (code) {
                reject(new Error('Ошибка при запросе адреса в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                return;
            } // exit if error code

            resolve(address);
            return;
        });
    });
}

window.GLOBALS.JS.wialon_get_track = async (unit, selected_date) => {
    return new Promise((resolve, reject) => {

        if (!unit) {
            reject(new Error('Отсутствует уникальный идентификатор объекта в системе gps-трекинга Wialon'));
            return;
        }; // exit if no unit  

        if (!selected_date) {
            reject(new Error('Отсутствует дата трека в системе gps-трекинга Wialon'));
            return;
        }; // exit if no selected_date

        var
            unit_id = unit.getId(),
            sess = wialon.core.Session.getInstance(), // get instance of current Session
            renderer = sess.getRenderer(),
            from = Math.round(new Date(selected_date.getFullYear(), selected_date.getMonth(), selected_date.getDate()) / 1000), // get begin time - beginning of day
            to = from + 3600 * 24 - 1,
            color = "cc0000ff"; // end of day in seconds

        var pos = unit.getPosition(); // get unit position

        if (!pos) {
            reject(new Error('Отсутствует позиция объекта в системе gps-трекинга Wialon'));
            return;
        } // exit if no position

        // callback is performed, when messages are ready and layer is formed
        callback = qx.lang.Function.bind(function (code, layer) {
            if (code) {
                reject(new Error('Ошибка при получении трека объекта в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                return;
            } // exit if error code

            resolve(layer);

            // if (layer) {
                
                // var layer_bounds = layer.getBounds(); // fetch layer bounds
                // if (!layer_bounds || layer_bounds.length != 4 || (!layer_bounds[0] && !layer_bounds[1] && !layer_bounds[2] && !layer_bounds[3])) // check all bounds terms
                //     return;

                // // if map existence, then add tile-layer and marker on it
                // if (map) {
                //     //prepare bounds object for map
                //     var bounds = new L.LatLngBounds(
                //         L.latLng(layer_bounds[0], layer_bounds[1]),
                //         L.latLng(layer_bounds[2], layer_bounds[3])
                //     );
                //     map.fitBounds(bounds); // get center and zoom
                //     // create tile-layer and specify the tile template
                //     if (!tile_layer)
                //         tile_layer = L.tileLayer(sess.getBaseUrl() + "/adfurl" + renderer.getVersion() + "/avl_render/{x}_{y}_{z}/" + sess.getId() + ".png", { zoomReverse: true, zoomOffset: -1 }).addTo(map);
                //     else
                //         tile_layer.setUrl(sess.getBaseUrl() + "/adfurl" + renderer.getVersion() + "/avl_render/{x}_{y}_{z}/" + sess.getId() + ".png");
                //     // push this layer in global container
                //     layers[unit_id] = layer;
                //     // get icon
                //     var icon = L.icon({ iconUrl: unit.getIconUrl(24) });
                //     //create or get marker object and add icon in it
                //     var marker = L.marker({ lat: pos.y, lng: pos.x }, { icon: icon }).addTo(map);

                //     marker.setLatLng({ lat: pos.y, lng: pos.x }); // icon position on map
                //     marker.setIcon(icon); // set icon object in marker
                //     markers[unit_id] = marker;
                // }
                // // create row-string with data
                // var row = "<tr id='" + unit_id + "'>";
                // // print message with information about selected unit and its position
                // row += "<td class='unit'><img src='" + unit.getIconUrl(16) + "'/> " + unit.getName() + "</td>";
                // row += "<td>Position " + pos.x + ", " + pos.y + "<br> Mileage " + layer.getMileage() + "</td>";
                // row += "<td style='border: 1px solid #" + color + "'>     </td>";
                // row += "<td class='close_btn'>x</td></tr>";
                // //add info in table
                // $("#tracks").append(row);
            // }
        });

        // query params
        params = {
            "layerName": "route_unit_" + unit_id, // layer name
            "itemId": unit_id, // ID of unit which messages will be requested
            "timeFrom": from, //interval beginning
            "timeTo": to, // interval end
            "tripDetector": 1, //use trip detector: 0 - no, 1 - yes
            "trackColor": color, //track color in ARGB format (A - alpha channel or transparency level)
            "trackWidth": 5, // track line width in pixels
            "arrows": 0, //show course of movement arrows: 0 - no, 1 - yes
            "points": 1, // show points at places where messages were received: 0 - no, 1 - yes
            "pointColor": color, // points color
            "annotations": 0 //show annotations for points: 0 - no, 1 - yes
        };

        renderer.createMessagesLayer(params, callback);
    });
}
