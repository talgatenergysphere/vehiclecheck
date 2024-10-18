if (!window.GLOBALS) {
    window.GLOBALS = {
        JS: {}
    };
} else if (!window.GLOBALS.JS) {
    window.GLOBALS.JS = {};
}

const regexData = /^(\d{4})-(\d{2})-(\d{2})T.*/;

window.GLOBALS.JS.fetchSelf = async function (url = "", data = {}) {

    const response = await fetch(window.GLOBALS.appUrl + url, {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        credentials: "same-origin", // include, *same-origin, omit
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        redirect: "follow", // manual, *follow, error
        referrerPolicy: "no-referrer", // no-referrer, *client
        body: JSON.stringify(data), // body data type must match "Content-Type" header
    });

    return await response.json();

}

window.GLOBALS.JS.fetchFile = async function (url = "", data = {}) {

    const response = await fetch(window.GLOBALS.appUrl + url, {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        credentials: "same-origin", // include, *same-origin, omit
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        redirect: "follow", // manual, *follow, error
        referrerPolicy: "no-referrer", // no-referrer, *client
        body: JSON.stringify(data), // body data type must match "Content-Type" header
    });

    return response;
}

window.GLOBALS.JS.fetchRest = async function (method = "", params = {}, type = 'json') {
    return new Promise(function (resolve, reject) {
        if (typeof BX24 !== 'undefined' && BX24 !== null) {
            // Функция для обработки результата и рекурсивного вызова при наличии next
            var accumulatedData = [];
            const handleResult = (result) => {
                if (result.error()) {
                    console.error(result.error().ex);
                    reject(new Error(result.error().ex));
                } else {
                    const data = result.data();
                    if (Array.isArray(data)) {
                        accumulatedData = accumulatedData.concat(data);
                        if (result.more()) {
                            result.next();
                        } else {
                            resolve(accumulatedData);
                        }
                    } else {
                        resolve(data);
                    }
                }
            };

            // Вызов метода BX24
            BX24.callMethod(method, params, handleResult);

        } else if (window.GLOBALS.access_token) {

            // Проверка, является ли method абсолютной ссылкой
            const isAbsoluteUrl = /^https?:\/\//i.test(method);
            const url = isAbsoluteUrl ? method : window.GLOBALS.restUrl + method;

            // Определение типа контента и формирование body
            let headers = {};
            let body;

            if (type === 'formdata') {
                params.append('auth', window.GLOBALS.access_token);
                body = params;
            } else {
                params.auth = window.GLOBALS.access_token;
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(params);
            }

            fetch(url, {
                method: 'POST',
                headers: headers,
                body: body
            }).then(response => {
                return response.json();
            }).then(data => {
                if (data.result != null) {
                    if (data.next) {
                        params.start = data.next;
                        window.GLOBALS.JS.fetchRest(method, params, type).then(dataNext => {
                            resolve(data.result.concat(dataNext));
                        });
                    } else {
                        resolve(data.result);
                    }
                } else {
                    reject(new Error("Не удалось выполнить запрос. " + JSON.stringify(data)));
                }
            }).catch(error => {
                reject(error);
            });
        } else {
            reject(new Error('Не удалось выполнить запрос, не найден токен доступа'));
        }
    });
}

window.GLOBALS.JS.fetchWebHook = async function (method = "", data = {}) {
    return new Promise(function (resolve, reject) {
        if (window.GLOBALS.restUrl && window.GLOBALS.restInstance && window.GLOBALS.restToken) {
            fetch(`https://crm.kilem-khan.kz/rest/581/29zhx2lb4259wzvq/${method}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(response => {
                return response.json();
            }).then(data => {
                if (data.result != null) {
                    if (data.next) {
                        params.start = data.next;
                        window.GLOBALS.JS.fetchRest(method, params, type).then(dataNext => {
                            resolve(data.result.concat(dataNext));
                        });
                    } else {
                        resolve(data.result);
                    }
                } else {
                    reject(new Error("Не удалось выполнить запрос: " + JSON.stringify(data)));
                }
            }).catch(error => {
                reject(error);
            })
        } else {
            reject(new Error('Не удалось выполнить запрос, не найден токен доступа'));
        }
    });
}

window.GLOBALS.JS.appError = function (error) {
    console.error(error);

    if (window?.GLOBALS?.IS_DEV) {
        alert(error.message);
        return;
    }

    let currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('ERROR', error.message);
    window.location.href = currentUrl.toString();
};

window.GLOBALS.JS.prepareUserInfo = function (user) {

    if (user.LAST_NAME || user.NAME) {
        user.title = [user.LAST_NAME || null, user.NAME || null].filter(Boolean).join(' ').trim();
        user.title_FULL = [user.LAST_NAME || null, user.NAME || null, user.SECOND_NAME || null].filter(Boolean).join(' ').trim();
        user.title_BRIEF = [
            user.LAST_NAME || null,
            user.NAME ? user.NAME[0] + '.' : null,
            user.SECOND_NAME ? user.SECOND_NAME[0] + '.' : null
        ].filter(Boolean).join(' ').trim();
    } else {
        const phone_fields = window?.GLOBALS?.bitrixPhoneFieldList || ['PERSONAL_PHONE', 'PERSONAL_MOBILE', 'WORK_PHONE'];
        for (let phoneField of phone_fields) {
            if (user[phoneField]) {
                user.title = user[phoneField];
                break;
            }
        }
        if (!user.title) {
            user.title = 'Данные отсутствуют (id: ' + user.ID + ')';
        }
    }

    user.PERSONAL_PHONE = user.PERSONAL_PHONE || user.PERSONAL_MOBILE || user.WORK_PHONE;

    user.photo = user.photo || 'https://crm.kilem-khan.kz/bitrix/js/ui/icons/b24/images/ui-user.svg?v2';

    user.CALC_WORK_POSITION = user.UF_USR_WORK_POSITION_INTERN || user.UF_USR_WORK_POSITION || user.WORK_POSITION;

    user.UF_DEPARTMENT_HEAD = [];
    user.UF_HEAD = [];
    user.IS_HEAD = false;

    if (typeof user.UF_USR_USER_DOC_ISSUE_DATE === 'string'
        && regexData.test(user.UF_USR_USER_DOC_ISSUE_DATE)) {

        user.FORMATTED_DOC_ISSUE_DATE = user.UF_USR_USER_DOC_ISSUE_DATE.split('T')[0];

        const reg = regexData.exec(user.UF_USR_USER_DOC_ISSUE_DATE);

        user.FORMATTED_VIEW_DOC_ISSUE_DATE = `${reg[3]}.${reg[2]}.${reg[1]}`

    }

    if (typeof user.PERSONAL_BIRTHDAY
        && regexData.test(user.PERSONAL_BIRTHDAY)) {
        user.FORMATTED_BIRTHDAY = user.PERSONAL_BIRTHDAY.split('T')[0];

        const reg = regexData.exec(user.PERSONAL_BIRTHDAY);

        user.FORMATTED_VIEW_BIRTHDAY = `${reg[3]}.${reg[2]}.${reg[1]}`
    }

    if (user.PERSONAL_GENDER) {
        switch (user.PERSONAL_GENDER) {
            case "M":
                user.FORMATTED_GENDER = "Мужской";
                break;
            case "F":
                user.FORMATTED_GENDER = "Женский";
                break;
            default:
                break;
        }
    }

    return user;
}

window.GLOBALS.JS.prepareVehicleInfo = function (vehicle) {

    return vehicle;
}

window.GLOBALS.JS.downloadFile = function (url, filename) {
    if (window.parent?.BXMobileApp?.UI?.Document) {
        window.parent.BXMobileApp.UI.Document.open({
            url: url,
            filename: filename,
        });
    } else {
        let link = document.createElement("a");
        link.href = url;
        link.download = filename;
        link.click();
        link.remove();
    }
}