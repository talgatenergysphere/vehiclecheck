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
        }

        // if not logged
        var token = window.GLOBALS.wialonToken; // get token from input
        if (!token) { // if token is empty - print message to log
            reject(new Error('Отсутствует токен авторизации в системе gps-трекинга Wialon'));
        }

        var domen = window.GLOBALS.wialonDomen; // get domen from input
        if (!domen) { // if domen is empty - print message to log
            reject(new Error('Отсутствует домен авторизации в системе gps-трекинга Wialon'));
        }

        sess.initSession(domen); // initialize Wialon session
        sess.loginToken(token, "", // trying login 
            function (code) { // login callback
                if (code) {
                    reject(new Error('Ошибка при авторизации в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                }
                else {
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
        }
        wialon.core.Session.getInstance().logout( // if user exist - logout
            function (code) { // logout callback
                if (code) {
                    reject(new Error('Ошибка при авторизации в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                }
                else {
                    resolve(); // get current user and resolve promise
                }
            }
        );
    });
}

window.GLOBALS.JS.wialon_get_objects = async () => {
    return new Promise(async(resolve, reject) => {
        await window.GLOBALS.JS.wialon_login();

        var sess = wialon.core.Session.getInstance(); // get instance of current Session
        // flags to specify what kind of data should be returned
        var flags = wialon.item.Item.dataFlag.base | wialon.item.Unit.dataFlag.lastMessage;

        sess.loadLibrary("itemIcon"); // load Icon Library	
        sess.updateDataFlags( // load items to current session
            [{ type: "type", data: "avl_unit", flags: flags, mode: 0 }], // Items specification
            async function (code) { // updateDataFlags callback

                if (code) { 
                    reject(new Error('Ошибка при выгрузке объектов системы gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
                } // exit if error code

                // get loaded 'avl_unit's items  
                var units = sess.getItems("avl_unit");

                for (const unit of units) {
                    var pos = unit.getPosition();
                    console.log(pos);
                    
                    if(pos){
                        unit.address = await window.GLOBALS.JS.wialon_get_locations(pos);
                    }
                }

                await window.GLOBALS.JS.wialon_login();

                resolve(units);
            }
        );
    });
}

window.GLOBALS.JS.wialon_get_locations = async (pos) => {
    return new Promise(async(resolve, reject) => {    
        wialon.util.Gis.getLocations([{lon:pos.x, lat:pos.y}], function(code, address){ 
			if (code) { 
                reject(new Error('Ошибка при запросе адреса в системе gps-трекинга Wialon: ' + wialon.core.Errors.getErrorText(code)));
            } // exit if error code

            resolve(address);
		});
    });
}