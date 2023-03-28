var app = require('express')();
const link = 'https://interhelper.ru';
const uniqid = require('uniqid');
var JavaScriptObfuscator = require('javascript-obfuscator');
const readline = require('readline');
const geoip = require('geoip-lite');
const bodyParser = require('body-parser');
var urlencodedParser = bodyParser.urlencoded({extended: false});
var jsonParser = bodyParser.json();
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});
const accepted_file_types = ['.jpg','.gif','.bmp','.png','.ico', '.jpeg', '.webp', '.rar','.zip','.doc','.docx','.ods','.odt','.pdf','.ppt','.pptx','.xlt','.xlsx','.xls','.docm','.dot','.txt','.zip'];
var bcrypt = require('bcrypt');
var fs = require('fs');
var exec = require('child_process').exec;
var backup = require('backup');
const mysql = require("mysql2/promise");
const { exit } = require('process');
const e = require('express');
const { get } = require('http');
const { time } = require('console');
const { format } = require('path');
const { start } = require('repl');
var jsdom = require('jsdom');
const { JSDOM } = jsdom;
const { window } = new JSDOM();
const { document } = (new JSDOM('')).window;
global.document = document;
var $ = jQuery = require('jquery')(window);
const hostname = '0.0.0.0';
const http_port = 5320;
const https_port = 5321;
const opts = {
    key: fs.readFileSync('pk.key'),
    ca: fs.readFileSync('ca.pem'),
    cert: fs.readFileSync('cer.crt')
}
const connection_config = { 
    charset: "utf8mb4_general_ci",
    host: "localhost",
    user: "root",
    database: "interhelper",
    password: "Fadkj123ADSFJ!"
}
const adds_company = {
    "yandex": "Яндекс Директ",
    "google": "Google Adwords",
    "facebook": "facebook",
    "vk": "Реклама vk",
    "targetmail": "myTarget",
    "instagram": "instagram"
}
const visitors_photos_folder = 'C:/hosting/interhelper.ru/www/visitors_photos/';
const backup_folder = 'C:/hosting/backup/';
const crm_files = 'C:/hosting/interhelper.ru/www/crm_files/';
const visitors_photos = [];
const visitors_color_bg = ['#FED6BC', '#FFFADD', '#DEF7FE', '#E7ECFF', '#C3FBD8', '#FDEED9', '#F6FFF8', '#B5F2EA', '#C6D8FF'];
var login = "interhelper";
var password = "Dad123kek123!"; 
var deffault_tariff = "Стартовый";
var guest_rooms = {}; // посетители
var assistents = {}; // ассистенты
var rooms = []; // комнаты чата ассистентов
var banned = {}; // комнаты в бане
var crm_items = {}; // CRM строки
var tasks = {}; // задачи
var uvisitors = {}; // уникальные посетители
var bosses = {}; // боссы
var adds_visitors = {}; // посетители с рекламы
var tariffs = {}; // тарифы
var tokens = {"assistent": {}, "boss": {}}; // токены авторизации 
var http = require('http').Server(app);
var https = require('https').Server(opts, app);
fs.readdirSync(visitors_photos_folder).forEach(file => { visitors_photos.push(file); });
FillUsersMas(); // заполняем массивы
https.listen(https_port,hostname, () => { syslog(`: HTTPS порт: ${https_port}`, 'func'); });
http.listen(http_port,hostname, () => { syslog(`: HTTP порт: ${http_port}`, 'func'); });
var io = require('socket.io')(http, {
    cors: {
      origin: "*",
      methods: ["GET", "POST"]
    }
});
io.attach(https, {
    cors: {
      origin: "*",
      methods: ["GET", "POST"]
    }
});
app.get('', (req, res) => {  
    res.redirect(link+'/index');
});
app.post('/client', jsonParser, async function(req, res) { // для боссов POST
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "X-Requested-With");
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    var info = req.body.info;
    var login = req.body.login;
    var password = req.body.password;
    var type = req.body.type;
    try{
        const connection = await mysql.createConnection(connection_config);
        let sql = `SELECT id, password FROM users WHERE email = '${login}'`;
        const [rows, fields] = await connection.execute(sql);
        connection.end();
        if(rows[0]){
            var hash = rows[0]['password'];
            var id = rows[0]['id'];
            hash = hash.replace('$2y$', '$2a$');
            bcrypt.compare(password, hash, async function(err, correct) {
                if(correct){
                    if(type == 'add'){ // Добавить
                        let table = info.table;
                        if(!crm_items.hasOwnProperty(id)) crm_items[id] = {};
                        if(!crm_items[id].hasOwnProperty(table)){ res.json({'success': false, 'error': 'Несуществующая таблица.'}); return;}
                        let access = false;
                        if(parseInt(tariffs[bosses[id]["tariff"]]["include"]["table_items"]["value"]) == 0) access = true;
                        if(!access && Object.keys(crm_items[id][table]).length < parseInt(tariffs[bosses[id]["tariff"]]["include"]["table_items"]["value"])) access = true
                        if(access){
                            let item_id = 'item_' + uniqid();
                            crm_items[id][item_id] = {'photo': 'user.png', 'name': 'новый'};
                            for(column in info.columns) crm_items[id][table][item_id][column] = info[column];
                            io.to(String(id)).emit('add_item', {'info': crm_items[id][table][item_id], 'index': item_id});
                            res.json({'success': true, 'error': 'Успешно.'});
                        } else res.json({'success': false, 'error': 'Лимит превышен.'}); 
                    } else if(type == 'get') { // Взять
                        let table = info.table;
                        if(!crm_items[id].hasOwnProperty(table)){ res.json({'success': false, 'error': 'Несуществующая таблица.'}); return;}
                        res.json({'success': true, 'error': crm_items[id][table]});
                    } else if(type == 'csv') { // csv
                        let table = info.table;
                        if(!crm_items[id].hasOwnProperty(table)){ res.json({'success': false, 'error': 'Несуществующая таблица.'}); return; }
                        if(Object.keys(crm_items[id][table]) == 0){ res.json({'success': false, 'error': 'В таблице нет записей.'}); return;}
                        const connection = await mysql.createConnection(connection_config);
                        let result = await get_crmTable_csv(id, table, connection);
                        connection.end();
                        res.json({'success': true, 'error': result}); 
                    } else res.json({'success': false, 'error': 'Не существующий тип.'});
                } else res.json({'success': false, 'error': 'Данные не прошли проверку.'});
            });
        } else res.json({'success': false, 'error': 'Данные не прошли проверку.'});
    } catch(err) { syslog(err, 'error'); res.json({'success': false, 'error': 'Ошибка на стороне сервера'}); }
});
app.post('/admin', jsonParser, async function(req, res) { // для настроек и админов POST
    console.log(req.body, 123);
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "X-Requested-With");
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    let log = req.body.login;
    let pass = req.body.password;
    let type = req.body.type;
    let info = req.body.info;
    if(login == log && password == pass){
        if(type == 'session_key'){ // токен соединения с сервером
            let domain = info.domain;
            let token = info.token;
            let type = info.type;
            if(info.old_token) delete tokens[type][info.old_token];
            if(type == 'boss') tokens[type][token] = {"domain": domain};
            else tokens[type][token] = {
                "domain": domain, 
                "assistent_id": info.assistent_id
            };
            res.json({'success': true, 'error': ' Токен получен.'});
        } else if(type == 'consultant_message'){ // сообщение консультат
            let token = info.token;
            res.json({'success': true, 'error': ' Сообщение отправлено.'});
            if(!tokens["assistent"]?.[token]){ syslog(`Не верный токен авторизации ${token}`, 'strange'); return; }
            // персональные данные
            let domain = tokens["assistent"][token]["domain"];
            let assistent_id = tokens["assistent"][token]["assistent_id"];
            if(!assistents?.[domain]?.[assistent_id]){ return;}
            let name = assistents[domain][assistent_id]["name"];
            let departament = assistents[domain][assistent_id]["departament"]; 
            let photo = assistents[domain][assistent_id]["photo"];
            // данные по сообщению
            let message = ''; 
            if(info.message) message = info.message.replace(/'/g, "\\'");
            let adds = JSON.stringify(info.adds);
            if(!adds) adds = "";
            let lct = getTime();
            let room = info.room;
            let sql = '';
            let chat_type = info.chat_type;
            let visibility = '';
            if(info.mode == 'js_mode'){ 
                visibility = 'invisible';
                message = info.message_htmlchars;
            } else if(info.mode != 'style_mode') message = info.message_htmlchars;
            else message = message.replace(/`/g, "\\`");
            if(chat_type == 'assistent'){
                if(rooms.indexOf(room.split('!@!@2@!@!').reverse().join('!@!@2@!@!')) != -1) room = room.split('!@!@2@!@!').reverse().join('!@!@2@!@!');
            }
            message_form = {
                message: message,
                user: name,
                departament: departament,
                photo: photo,
                message_adds: adds||"",
                mode: info.mode,
                time: lct,
                type: (room.indexOf('!@!@2@!@!') == -1 && chat_type == 'assistent' ? true : false),
                sender: assistent_id
            };
            io.in(String(room)).emit('consultant-message', (message_form));
            if(chat_type == 'assistent'){ // чат с консультантами 
                message_form["sender"] = assistent_id;
                sql = `INSERT INTO assistents_chat_messages (id, sender, message, SendTime, domain, room, adds) VALUES (0, '${assistent_id}', '${message}', '${lct}','${domain}', '${room}', '${adds}')`;
                message_form["notification_type"] = "assistent_chat";
                message_form["photo"] = "/assistent_photos/" + photo;
                try {
                    const connection = await mysql.createConnection(connection_config); 
                    connection.query("SET NAMES utf8");
                    await connection.execute(sql);
                    if(room.indexOf('!@!@2@!@!') !== -1){ // если приватный чат
                        let oid;
                        if(room.split('!@!@2@!@!')[0] != assistent_id) oid = room.split('!@!@2@!@!')[0];
                        else oid = room.split('!@!@2@!@!')[1];
                        assistents[domain][oid]["assistent_chat_messages"][assistent_id] = {"message": message, "message_adds": adds, "time": lct};
                        io.in("assistent_"+oid).emit('consultant_message_notification', (message_form));
                    } else {
                        for(assistent in assistents[domain]){
                            if(assistent != assistent_id){
                                if(assistents[domain]["public_room"]["assistents_in"].indexOf(assistent) == -1){
                                    assistents[domain][assistent]["assistent_chat_messages"]["public_room"] = {"message": message, "message_adds": adds, "time": lct};
                                    io.in("assistent_"+assistent).emit('consultant_message_notification', (message_form));
                                }
                            }
                        }
                    }
                    connection.end();
                } catch (err) { syslog(`ОШИБКА (отправка сообщения ассистенту) ${err}`, 'error'); }
            } else if(chat_type == 'guest'){
                if(!guest_rooms?.[domain]?.[room]) return;
                guest_rooms[domain][room]["messages_exist"] = true;
                try{
                    const connection = await mysql.createConnection(connection_config);
                    connection.query("SET NAMES utf8");
                    sql = return_sql('visitor', {
                        "id": 0,
                        "room": room,
                        "lastActivityTime": guest_rooms[domain][room]['lastActivityTime'],
                        "domains_list": JSON.stringify(guest_rooms[domain][room]["domains_list"]),
                        "served_list": JSON.stringify(guest_rooms[domain][room]["served_list"]),
                        "serving_list": JSON.stringify(guest_rooms[domain][room]["serving_list"]),
                        "info": JSON.stringify(guest_rooms[domain][room]["info"]),
                        "session_time": guest_rooms[domain][room]["session_time"],
                        "photo": guest_rooms[domain][room]["photo"],
                        "sessions": "",
                        "notes": guest_rooms[domain][room]["notes"],
                        "properties": guest_rooms[domain][room]["properties"],
                        "visits": guest_rooms[domain][room]["visits"],
                    });
                    await connection.execute(sql);
                    sql = return_sql('consultant_message', {
                        id: assistent_id,
                        message: message,
                        time: lct,
                        domain: domain,
                        room: room,
                        adds: adds,
                        visibility: visibility
                    });
                    await connection.execute(sql);
                    connection.end();
                } catch (err) { syslog(`ОШИБКА (отправка сообщения посетителю) ${err}`, 'error'); }
                statistic_2('consultants_messages', domain, guest_rooms[domain][room]['domains_list']['domains']);
            } 
        } else if(type == 'guest_message'){ // USER MSGS (PHOTOS, FORMS, MSGS)
            var domain = info.domain;
            var uid = info.uid;
            var message = info.message;
            let msg_type = info.type;
            var email, phone, name, form_uid;
            var strDate = getTime();
            res.json({'success': true, 'error': ' Сообщение отправлено.'});
            if(!guest_rooms?.[domain]?.[uid]) return;
            var vst_time = guest_rooms[domain][uid]['lastActivityTime'];
            if(msg_type == "photo") message = JSON.stringify(message);
            if(msg_type == "offline_form"){
                name = !info.name ? '' : info.name;
                email = !info.email ? '' : info.email;
                phone = !info.phone ? '' : info.phone;
                message_form = {
                    message: message,
                    name: name,
                    email: email,
                    phone: phone,
                };
                io.in(uid).emit('guest-offline_form', (message_form));
                io.to(String(domain)).emit('guest-offline_form', (message_form));
                statistic_2('offline_forms', domain, guest_rooms[domain][uid]['domains_list']['domains']);
            } else {
                statistic_2('guests_messages', domain, guest_rooms[domain][uid]['domains_list']['domains']);
                message_form = {
                    message: (msg_type == 'message' ? message : ""),
                    message_adds: (msg_type == 'photo' ? message : ""),
                    time: strDate,
                };
                io.in(uid).emit('guest-message', (message_form));
            }
            var sql = return_sql('visitor', {
                "id": 0,
                "room": uid,
                "lastActivityTime": guest_rooms[domain][uid]['lastActivityTime'],
                "domains_list": JSON.stringify(guest_rooms[domain][uid]["domains_list"]),
                "served_list": JSON.stringify(guest_rooms[domain][uid]["served_list"]),
                "serving_list": JSON.stringify(guest_rooms[domain][uid]["serving_list"]),
                "info": JSON.stringify(guest_rooms[domain][uid]["info"]),
                "session_time": guest_rooms[domain][uid]["session_time"],
                "photo": guest_rooms[domain][uid]["photo"],
                "sessions": "",
                "notes": guest_rooms[domain][uid]["notes"],
                "properties": guest_rooms[domain][uid]["properties"],
                "visits": guest_rooms[domain][uid]["visits"],
            });
            try{
                const connection = await mysql.createConnection(connection_config);
                connection.query("SET NAMES utf8");
                await connection.execute(sql);
                if(msg_type == "offline_form"){
                    form_uid = uniqid();
                    sql = `
                        INSERT INTO offline_forms (
                            id,
                            message,
                            name,
                            email,
                            phone,
                            time,
                            sender,
                            uid,
                            owner_id
                        ) VALUES (
                            0,
                            '${message}',
                            '${name}',
                            '${email}',
                            '${phone}',
                            '${strDate}',
                            (SELECT id FROM rooms WHERE room = '${uid}'),
                            '${form_uid}',
                            '${domain}'
                        )
                    `;
                    await connection.execute(sql);
                }
                sql = `
                    INSERT INTO messages_with_users_guests (
                        id,
                        message, 
                        SendTime, 
                        domain, 
                        room, 
                        adds, 
                        sender
                    ) VALUES (
                        0, 
                        '${(msg_type == "message" ? message : (msg_type == "offline_form" ? form_uid : ''))}', 
                        '${strDate}', 
                        '${domain}', 
                        (SELECT id FROM rooms WHERE room = '${uid}'), 
                        '${(msg_type == "photo" ? message: "")}', 
                        '${(msg_type == "offline_form" ? 'offline_form' : '')}'
                    )
                `;
                await connection.execute(sql);
                connection.end();
            } catch(err) { syslog(`ОШИБКА (отправка сообщения от посетителя) ${err}`, 'error'); }
            guest_rooms[domain][uid]["messages_exist"] = true;
            // статус сообщения
            if(guest_rooms[domain][uid]["serving_list"]["assistents"].length === 0){
                guest_rooms[domain][uid]["new_message"] = {
                    "message": (msg_type != "photo" ? message : null), 
                    "status": "unreaded",
                    "message_adds": (msg_type == "photo" ? message : null),
                }; 
                io.to(String(domain)).emit('userlist_update', ({
                    "type": "message", 
                    "value": {
                        "message": (msg_type != "photo" ? message : null), 
                        "message_adds": (msg_type == "photo" ? message : null)
                    },
                    "target": uid, 
                    "option": null
                }));
                sql = `UPDATE assistents SET gmessage = 'true' WHERE domain = '${domain}'`;
                try{
                    const connection = await mysql.createConnection(connection_config);
                    connection.query("SET NAMES utf8");
                    await connection.execute(sql);
                } catch(err) { syslog(`ОШИБКА (отправка сообщения от посетителя) ${err}`, 'error'); }
            } else { 
                let table = guest_rooms[domain][uid].crm;
                for(i in guest_rooms[domain][uid]["serving_list"]["assistents"]){
                    let assistent_index = guest_rooms[domain][uid]["serving_list"]["assistents"][i];
                    if(!assistents?.[domain]?.[assistent_index]) continue;
                    let photo;
                    let name;
                    if(crm_items?.[domain]?.[table]?.[uid]){ 
                        photo = "/crm_files/"+crm_items[domain][table][uid]["helper_photo"];
                        name = crm_items[domain][table][uid]["helper_name"];
                    } else photo = "/assistent_photos/user.png";
                    let notification = {
                        "message": (msg_type != "photo" ? message : null),
                        "time": strDate,
                        'photo':photo,
                        'name': name||guest_rooms[domain][uid]["info"]["ip"]||uid,
                        'email': guest_rooms[domain][uid]["user_email"]||null,
                        'departament': 'Обслуживаемый посетитель',
                        'message_adds': (msg_type == "photo" ? message : null),
                        "sender": uid,
                        "status": "unreaded",
                        "link": "/engine/consultant/chat?room="+uid,
                    };
                    assistents[domain][assistent_index]["personal_consulation_messages"][uid] = notification;
                    io.to("assistent_"+assistent_index).emit('consultant_message_notification', notification);
                }
            }
        } else if(type == 'consultation') { // консультация
            try{
                let boss_id = info.domain;
                let assistent_id = info.assistent_id;
                let consultation_type = info.type;
                let room = info.room;
                let mas_to_save, sql;
                if(!guest_rooms?.[boss_id]?.[room]){ res.json({'success': false, 'error': 'Несуществующая комната !'}); return; }
                let served_index = guest_rooms[boss_id][room]["served_list"]["assistents"].indexOf(assistent_id);
                let serving_index = guest_rooms[boss_id][room]["serving_list"]["assistents"].indexOf(assistent_id);
                if(consultation_type == "start" || consultation_type == "restart" || consultation_type == "continue") guest_rooms[boss_id][room]['new_message']["status"] = "readed";
                if(consultation_type == "start") statistic_2('started_consulations', boss_id, guest_rooms[boss_id][room]['domains_list']['domains']);
                if(consultation_type == "restart") statistic_2('restarted_consulations', boss_id, guest_rooms[boss_id][room]['domains_list']['domains']);
                if(consultation_type == "finsish") statistic_2('finished_consulations', boss_id, guest_rooms[boss_id][room]['domains_list']['domains']);
                if(consultation_type == "start" || consultation_type == "restart"){
                    if(serving_index != -1){ res.json({'success': false, 'error': 'Вы уже консультируете эту комнату !'}); return; }
                    if(served_index == -1 && guest_rooms[boss_id][room]["serving_list"]["assistents"].length > 0){ res.json({'success': false, 'error': 'Комнату уже обслуживают !'}); return; }
                    const connection = await mysql.createConnection(connection_config);
                    if(served_index != -1){ 
                        guest_rooms[boss_id][room]["served_list"]["assistents"].splice(served_index, 1);
                        mas_to_save = JSON.stringify(guest_rooms[boss_id][room]["served_list"]);
                        sql = `UPDATE rooms SET served_list = '${mas_to_save}' WHERE room = '${room}'`;
                        await connection.execute(sql);
                        io.to(String(boss_id)).emit("userlist_update", ({"type": "served_list", "value": assistent_id, "target": room, "option": "delete"}));
                    }
                    io.to(String(boss_id)).emit("userlist_update", ({"type": "serving_list", "value": assistent_id, "target": room, "option": "add"}));
                    guest_rooms[boss_id][room]["serving_list"]["assistents"].push(assistent_id);
                    mas_to_save = JSON.stringify(guest_rooms[boss_id][room]["serving_list"]);
                    sql = `UPDATE rooms SET serving_list = '${mas_to_save}' WHERE room = '${room}'`;
                    await connection.execute(sql);
                    connection.end();
                    io.to(room).emit('servicing_assistent', {
                        "name": assistents[boss_id][assistent_id]["name"],
                        "departament": assistents[boss_id][assistent_id]["departament"],
                        "photo": assistents[boss_id][assistent_id]["photo"]
                    });
                } else if(serving_index != -1 && consultation_type == "finish" && served_index == -1){
                    const connection = await mysql.createConnection(connection_config);
                    guest_rooms[boss_id][room]["serving_list"]["assistents"].splice(serving_index, 1);
                    mas_to_save = JSON.stringify(guest_rooms[boss_id][room]["serving_list"]);
                    sql = `UPDATE rooms SET serving_list = '${mas_to_save}' WHERE room = '${room}'`;
                    await connection.execute(sql);
                    io.to(String(boss_id)).emit("userlist_update", ({"type": "serving_list", "value": assistent_id, "target": room, "option": "delete"}));
                    guest_rooms[boss_id][room]["served_list"]["assistents"].push(assistent_id);
                    mas_to_save = JSON.stringify(guest_rooms[boss_id][room]["served_list"]);
                    sql = `UPDATE rooms SET served_list = '${mas_to_save}' WHERE room = '${room}'`;
                    await connection.execute(sql);
                    io.to(String(boss_id)).emit("userlist_update", ({"type": "served_list", "value": assistent_id, "target": room, "option": "add"}));
                    connection.end();
                    io.to(room).emit('finish_sevice');
                }
                res.json({'success': false, 'error': 'Успешно !'});
            } catch(err) {  
                syslog(`ОШИБКА (консультация) ${err}`, 'error');
                res.json({'success': false, 'error': 'Ошибка на сервере!'});
            }
        } else if(type == "selected_visitors_options") { // действия с выбранными посетителями
            let option_type = info.type;
            let selected_visitors = info.selected_visitors;
            let token = info.token;
            res.json({'success': true, 'error': ' Действие отправлено.'});
            let assistent_id = tokens["assistent"][token]["assistent_id"];
            let domain = tokens["assistent"][token]["domain"];
            if(!tokens["assistent"]?.[token]) return;
            if(!assistents?.[domain]?.[assistent_id]) return;
            if(!selected_visitors || !option_type) return;
            const connection = await mysql.createConnection(connection_config);
            if(option_type == "hide") { // удаление посетителей
                for(key in selected_visitors){
                    if(!guest_rooms?.[domain]?.[key]) continue;
                    guest_rooms[domain][key]["hide"] = true;
                    sql = `UPDATE rooms SET hide = 'deleted' WHERE room = '${key}'`;
                    try { await connection.execute(sql);
                    } catch(err) { syslog(`ОШИБКА (отправка массе) ${err}`, 'error'); }
                    io.to(String(domain)).emit('userlist_update', ({"type": "delete", "value": null, "target": key, "option": null}));
                }
            } else if(option_type == "ban") { // блокировка посетителей
                let reason = info.reason;
                for(key in selected_visitors){
                    if(!guest_rooms?.[domain]?.[key]) continue;
                    let prev_info = guest_rooms[domain][key];
                    for(i in prev_info["serving_list"]["assistents"]) prev_info["served_list"]["assistents"].push(prev_info["serving_list"]["assistents"][i]);
                    prev_info["serving_list"]["assistents"] = [];
                    delete guest_rooms[domain][key];
                    let ban_id = domain + '!@!@2@!@!' + prev_info.info.ip;
                    banned[domain][ban_id] = prev_info;
                    banned[domain][ban_id]['session_time'] = prev_info.session_time;
                    banned[domain][ban_id]['photo'] = prev_info.photo;
                    banned[domain][ban_id]["reason"] = reason;
                    banned[domain][ban_id]["room_id"] = key;
                    banned[domain][ban_id]["status"] = "offline";
                    banned[domain][ban_id]["connections"] = 0;
                    banned[domain][ban_id]["lastActivityTime"] = getTime();
                    banned[domain][ban_id]["bannedBy"] = assistent_id;
                    try {
                        let sql = `SELECT id FROM rooms WHERE room = '${key}'`;
                        let id;
                        const [rows, fields] = await connection.execute(sql);
                        if(rows[0]) id = rows[0]["id"];
                        else id = 0;
                        sql = `DELETE FROM rooms WHERE room = '${key}'`;
                        await connection.execute(sql);
                        connection.query("SET NAMES utf8");
                        sql = return_sql('banned', {
                            'id': id,
                            'ban_id': ban_id,
                            'domains_list':JSON.stringify(banned[domain][ban_id]["domains_list"]),
                            'notes':banned[domain][ban_id]["notes"],
                            'properties':banned[domain][ban_id]["properties"],
                            'served_list': JSON.stringify(banned[domain][ban_id]["served_list"]),
                            'reason': reason,
                            'assistent_id': assistent_id,
                            'info': JSON.stringify(banned[domain][ban_id]["info"]),
                            'room': key,
                            'sessions': "",
                            "session_time": banned[domain][ban_id]['session_time'],
                            'photo': banned[domain][ban_id]['photo'], 
                            'visits': banned[domain][ban_id]['visits'] 
                        });
                        await connection.execute(sql);
                        io.to(key).emit("ban");
                        io.to(String(domain)).emit('baned_user', {"target": key, "info": banned[domain][ban_id], "bun_id": ban_id});
                    } catch(err) { syslog(`ОШИБКА (отправка массе) ${err}`, 'error'); }
                }
            } else if(option_type == "restore") { // блокировка посетителей
                for(key in selected_visitors){
                    if(!guest_rooms?.[domain]?.[key]) continue;
                    guest_rooms[domain][key].hide = false;
                    let sql = `UPDATE rooms SET hide = NULL WHERE room = '${key}'`;
                    await connection.execute(sql);
                    io.to(String(domain)).emit('userlist_update', ({"type": "restore", "value": null, "target": key, "option": null}));
                }
            } else if(option_type == "dialog_start" || option_type == "dialog_stop"){
                for(key in selected_visitors){
                    if(!guest_rooms?.[domain]?.[key]) continue;
                    if(guest_rooms[domain][key].serving_list.assistents.indexOf(assistent_id) != -1 && option_type == "dialog_stop") guest_rooms[domain][key].serving_list.assistents.splice(guest_rooms[domain][key].serving_list.assistents.indexOf(assistent_id), 1);
                    else if(guest_rooms[domain][key].serving_list.assistents.indexOf(assistent_id) == -1 && guest_rooms[domain][key].serving_list.assistents.length == 0 && option_type == 'dialog_start') guest_rooms[domain][key].serving_list.assistents.push(assistent_id);
                    else continue;
                    let sql = `UPDATE rooms SET serving_list = '${JSON.stringify(guest_rooms[domain][key].serving_list)}' WHERE room = '${key}'`;
                    await connection.execute(sql);
                    io.to(String(domain)).emit('userlist_update', ({"type": option_type, "value": assistent_id, "target": key, "option": null}));
                }
            }
            io.to(String(domain)).emit('choosen-user-answer');
            connection.end();
        } else if(type == 'new_tariff'){ // новый тариф
            let name = info.name;
            let tariff = info.tariff;
            tariffs[name] = tariff;
            syslog(`Новый тариф`, 'func');
            res.json({'success': true, 'error': ' Новый тариф.'});
        } else if(type == 'remove_tariff') { // удалить тариф
            let name = info.name;
            delete tariffs[name];
            syslog(`Тариф удалён`, 'func');
            res.json({'success': true, 'error': 'Тариф удалён.'});
        } else if(type == 'change_tariff'){ // изменить тариф
            let name = info.name;
            let tariff = info.tariff;
            tariffs[name] = tariff;
            syslog(`Тариф обновлён`, 'func');
            res.json({'success': true, 'error': ' Тариф обновлён.'});
        } else if(type == 'new_password'){ // новый хеш пароль
            password = info.value;
            syslog(`Пароль изменён`, 'func');
            res.json({'success': true, 'error': ' Пароль изменён.'});
        } else if(type == 'new_login'){ // новый логин
            login = info.value;
            syslog(`Логин изменён`, 'func');
            res.json({'success': true, 'error': ' Логин изменён.'});
        } else if(type == 'default_tariff') { // новый стандартный тариф
            deffault_tariff = info.value;
            syslog(`Новый стартовый тариф`, 'func');
            res.json({'success': true, 'error': 'Стартовый тариф изменён.'});
        } else if(type == 'add_boss'){ // создание босса
            let sql = `SELECT count(1) FROM unconfimed_users WHERE hash = '${info.hash}' or email = '${info.email}'`;
            try{
                const connection = await mysql.createConnection(connection_config);
                const [rows, fields] = await connection.execute(sql);
                if(parseInt(rows[0]['count(1)']) > 0){
                    let sql = `DELETE FROM unconfimed_users WHERE hash = '${info.hash}' or email = '${info.email}'`;
                    await connection.execute(sql);
                    let d = new Date();
                    let days = d.getDate(); 
                    if(days < 10) days = '0' + days;
                    let mounths = d.getMonth() + 1; 
                    if(mounths < 10) mounths = '0' + mounths;
                    let years = d.getFullYear();
                    let today = years + '-' + mounths + '-' + days;
                    bosses[info.id] = {"tariff": deffault_tariff};
                    assistents[info.id] = {};
                    guest_rooms[info.id] = {};
                    rooms[info.id] = {};
                    banned[info.id]= {};
                    crm_items[info.id]= {"Лиды": {}, "Клиенты": {}};
                    tasks[info.id]= {};
                    uvisitors[info.id]= [];
                    assistents[info.id]["public_room"] = {"assistents_in": [], "assistent_chat_messages": {} };
                    syslog(`Новый клиент ${info.email}`, 'func')
                    res.json({'success': true, 'error': ''});
                } else res.json({'success': false, 'error': 'Данных в бд нет !'});
                connection.end();
            } catch(err) { 
                syslog(`Ошибка (новый клиент) ${err}`, 'error');
                res.json({'success': false, 'error': 'На сервере ошибка !'});
            }
        } else if(type == 'add_assistent'){ // создание ассистента
            try{
                const connection = await mysql.createConnection(connection_config);
                sql = `SELECT count(1) FROM unconfimed_assistents WHERE hash = '${info.hash}' or email = '${info.email}'`;
                const [rows, fields] = await connection.execute(sql);
                if(parseInt(rows[0]['count(1)']) > 0){
                    syslog(`Новый ассистент ${info.email} ${info.domain}`, 'func');
                    sql = `DELETE FROM unconfimed_assistents WHERE hash = '${info.hash}' or email = '${info.email}'`;
                    await connection.execute(sql);
                    var assistent_body = {
                        "personal_consulation_messages": {},
                        "assistent_chat_messages": {},
                        "id": info.id, 
                        "status": "offline", 
                        "hab": info.email, 
                        "departament": info.departament, 
                        "photo": info.photo, 
                        "name": info.name, 
                        "buttlecry": info.buttlecry, 
                        "connections": 0, 
                        'time': 'новый',
                        'ban': null,
                    };
                    assistents[info.domain][info.id] = assistent_body;
                    io.to(String(info.domain)).emit('assistentlist_update', ({"type": "new_assistent", "value": assistent_body, "target": info.id, "option": null})); 
                    res.json({'success': true, 'error': 'Ассистент создан.'});
                } else res.json({'success': false, 'error': 'Нет данных в бд !'});
                connection.end();
            } catch(err) { 
                syslog(`Ошибка (новый ассистент) ${err}`, 'error');
                res.json({'success': false, 'error': 'На сервере ошибка !'});
            }
        } else if(type == 'remove_assistent'){ // удаление ассистента
            if(!assistents[info.domain]?.[info.email]){ res.json({'success': false, 'error': 'Ассистент удалён.'}); return; }
            delete assistents[info.domain][info.email];
            io.to("assistent_"+info.email).emit('assistentlist_update', ({"type": "change_settings", "value": "", "target": info.email, "option": "remove"}));
            io.to(String(info.domain)).emit('assistentlist_update', ({"type": "change_settings", "value": "", "target": info.email, "option": "remove"}));
            syslog(`Удалён ассистент`, 'func');
            res.json({'success': true, 'error': 'Ассистент удалён.'});
        } else if(type == 'change_assistent'){ // изменения ассистента
            if(info.setting == "email") info.setting = "hab";
            assistents[info.domain][info.assistent_id][info.setting] = info.value; 
            io.to("assistent_"+info.assistent_id).emit('assistentlist_update', ({"type": "change_settings", "value": info.value, "target": info.assistent_id, "option": info.setting}));
            io.to(String(info.domain)).emit('assistentlist_update', ({"type": "change_settings", "value": info.value, "target": info.assistent_id, "option": info.setting}));
            syslog(`Ассистент изменён`, 'func');
            res.json({'success': true, 'error': 'Ассистент изменён.'});
        } else if(type == 'select_tariff'){ // босс меняет тариф
            if(tariffs.hasOwnProperty(info.tariff)){
                let new_tariff = info.tariff;
                let boss_id = info.domain;
                let tariff = bosses[boss_id]["tariff"];
                let sql  = `SELECT money, payday, domain, settings FROM users WHERE id = '${boss_id}'`;
                try{
                    const connection = await mysql.createConnection(connection_config);
                    const [rows, fields] = await connection.execute(sql);
                    let money = rows[0]["money"];
                    let payday = rows[0]["payday"];
                    let domain = JSON.parse(rows[0]["domain"]);
                    let settings = JSON.parse(rows[0]["settings"]);
                    let domain_count = Object.keys(domain["domains"]).length;
                    let departaments_count = Object.keys(settings['departaments']).length;
                    let d = new Date();
                    let days = d.getDate();
                    if(days < 10) days = '0' + days;
                    let months = d.getMonth() + 1;
                    if(months < 10) months = '0' + months;
                    let years = d.getFullYear() 
                    let today = new Date(years, months, days);
                    payday_to_db = years + '-' + months + '-' + days;
                    payday = new Date(payday.split('-')[0], payday.split('-')[1], payday.split('-')[2]);
                    let used = diffDates(today, payday);
                    let unused = 30 - used;
                    let tariff_cost = tariffs[tariff]["cost"]["value"];
                    let cost_per_day = tariff_cost / 30;
                    let remainder = unused * cost_per_day;
                    let access = false;
                    if(tariffs[new_tariff]["cost"]["value"] == 0) access = true;
                        if(!access) { 
                            if(parseInt(money) + parseInt(remainder) - parseInt(tariffs[new_tariff]["cost"]["value"]) >= 0 ) access = true;
                        }
                        if(access){
                            if(domain_count <= parseInt(tariffs[new_tariff]["include"]["domains"]["value"]) || parseInt(tariffs[new_tariff]["include"]["domains"]["value"]) == 0){ // проверка доменов
                                if(departaments_count <= parseInt(tariffs[new_tariff]["include"]["departaments"]["value"]) || parseInt(tariffs[new_tariff]["include"]["departaments"]["value"]) == 0){ // проверка отделов
                                    let crm_columns_count = crm_items_count = crm_tables_count = assistents_count = tasks_count  = 0;
                                    if(assistents.hasOwnProperty(boss_id)) assistents_count = Object.keys(assistents[boss_id]).length - 1; // количество ассистентов
                                    if(crm_items.hasOwnProperty(boss_id)){  // количество CRM
                                        crm_items_count = Object.keys(crm_items[boss_id]).reduce((el, index) => {return Object.keys(index).length + Object.keys(el).length});
                                        crm_tables_count = Object.keys(crm_items[boss_id]).length;
                                    } 
                                    if(tasks.hasOwnProperty(boss_id)) tasks_count = Object.keys(tasks[boss_id]).length; // количество задач
                                    if(tasks_count <= parseInt(tariffs[new_tariff]["include"]["tasks"]["value"]) || parseInt(tariffs[new_tariff]["include"]["tasks"]["value"]) == 0){ // проверка задач
                                        if(crm_items_count <= parseInt(tariffs[new_tariff]["include"]["crm_items"]["value"]) || parseInt(tariffs[new_tariff]["include"]["crm_items"]["value"]) == 0){ // проверка клиентов
                                            if(assistents_count <= parseInt(tariffs[new_tariff]["include"]["assistents"]["value"]) || parseInt(tariffs[new_tariff]["include"]["assistents"]["value"]) == 0){ // проверка ассистентов 
                                                sql = `SELECT columns FROM crm WHERE owner_id = ${boss_id}`; // колонки 
                                                const [Crows, Cfields] = await connection.execute(sql);
                                                crm_columns_count = Object.values(JSON.parse(Crows[0]["columns"])).reduce((el, el2) => { return Object.keys(el).length + Object.keys(el2).length; }); // колонки CRM
                                                if(crm_columns_count <= parseInt(tariffs[new_tariff]["include"]["table_columns"]["value"]) || parseInt(tariffs[new_tariff]["include"]["table_columns"]["value"]) == 0){ // проверка колонок CRM
                                                    money = parseInt(money) - parseInt(tariffs[new_tariff]["cost"]["value"]) + parseInt(remainder);
                                                    let today_days = today.getDate();
                                                    if(today_days < 10) today_days = '0' + today_days;
                                                    let today_months = today.getMonth();
                                                    if(today_months < 10) today_months = '0' + today_months; 
                                                    today = today.getFullYear() + '-' + today_months + '-' + today_days;
                                                    sql = `UPDATE users SET money = '${money}', tariff = '${new_tariff}', payday='${today}' WHERE id = '${boss_id}'`; 
                                                    await connection.execute(sql);
                                                    bosses[boss_id]["tariff"] = info.tariff;
                                                    syslog(`Босс изменил тариф ${boss_id}`, 'func');
                                                    let inform = {"money": money, "tariff": new_tariff};
                                                    res.json({'success': true, 'error': inform});
                                                } else res.json({'success': false, 'error': 'Слишком много созданных столбцов лидов, чтобы перейти на этот тариф !'});
                                            } else res.json({'success': false, 'error': 'Слишком много созданных ассистентов, чтобы перейти на этот тариф !'});
                                        } else res.json({'success': false, 'error': 'Слишком много созданных ячеек в CRM, чтобы перейти на этот тариф !'});
                                    } else res.json({'success': false, 'error': 'Слишком много созданных задач, чтобы перейти на этот тариф !'});
                                } else res.json({'success': false, 'error': 'Слишком много созданных отделов, чтобы перейти на этот тариф !'});
                            } else res.json({'success': false, 'error': 'Слишком много созданных доменов, чтобы перейти на этот тариф !'});
                        } else res.json({'success': false, 'error': 'Недостаточно денежных средств на балансе аккаунта !'});
                    connection.end();
                } catch(err) { 
                    syslog(`Ошибка (смена тарифа) ${err}`, 'error');
                    res.json({'success': false, 'error': 'Ошибка на сервере!'});
                }
            } else res.json({'success': false, 'error': 'Не существующий тариф!'});
        } else if(type == 'get_adds_users_mas'){ // босс получает массив посетителей с рекламы
            let mas;
            if(adds_visitors.hasOwnProperty(info.domain)) mas = adds_visitors[info.domain];
            else mas = {};
            res.json({'success': true, 'error': mas});
        } else if(type == 'change_crm_item'){ // редактор CRM
            let table, crm_item_column, crm_item_column_value, item_index, assistent_id;
            let boss_id = info.domain; 
            if(info.table) table = info.table;
            if(info.item_index) item_index = info.item_index;
            if(info.assistent_id) assistent_id = info.assistent_id;
            if(info.crm_item_column) crm_item_column = info.crm_item_column;
            if(info.value) crm_item_column_value = info.value;
            else crm_item_column_value = '';
            if(info.setting == 'change') { // изменить
                if(!crm_items[boss_id]?.[table]?.[item_index]){ res.json({'success': false, 'error': "Доступ ограничен !"}); return; }
                crm_items[boss_id][table][item_index][crm_item_column] = crm_item_column_value.replace(/\r?\n/g, "<br/>").replace(/\\/g, "&bsol;");
                io.to(String(boss_id)).emit('change_item', {'index': item_index, 'column': crm_item_column, 'value':crm_item_column_value, 'table': table});
                if(item_index.indexOf('!@!@2@!@!') != -1) io.to(String(item_index)).emit('change_this', {'index': item_index, 'column': crm_item_column, 'value': crm_item_column_value, 'table': table});
                try {
                    const connection = await mysql.createConnection(connection_config);
                    connection.query("SET NAMES utf8");
                    let sql = `SELECT * FROM crm_items WHERE uid = '${item_index}' and owner_id = '${boss_id}'`;
                    const [rows, fields] = await connection.execute(sql);
                    let info;
                    if(!rows[0]){
                        info = crm_items[boss_id][table][item_index];
                        info = JSON.stringify(info);
                        sql = `INSERT INTO crm_items (id, owner_id, info, uid, item_table) VALUES (0, '${boss_id}', '${info}','${item_index}', '${table}')`;
                    } else {
                        info = JSON.parse(rows[0]["info"]);
                        info[crm_item_column] = crm_item_column_value.replace(/\r?\n/g, "<br/>").replace(/\\/g, "&bsol;");
                        info = JSON.stringify(info);
                        sql = `UPDATE crm_items SET info = '${info}' WHERE uid = '${item_index}' and owner_id = '${boss_id}'`;
                    }
                    await connection.execute(sql);
                    connection.end();
                } catch (err) { syslog(`Ошибка (CRM change) ${err}`, 'error'); }
                res.json({'success': true, 'error': "Изменения в CRM внесены"});
            } else if(info.setting == 'remove') { // удалить
                if(!crm_items[boss_id]?.[table]?.[item_index]){ res.json({'success': false, 'error': "Доступ ограничен !"}); return; }
                if(crm_items[boss_id][table][item_index]['helper_photo'] != 'user.png' && crm_items[boss_id][table][item_index]['helper_photo']){ 
                    try{
                        fs.unlinkSync(crm_files+crm_items[boss_id][table][item_index]['helper_photo']);
                    } catch(err) { syslog(`Ошибка (CRM remove files) ${err}`, 'error'); }
                }
                for(column in crm_items[boss_id][table][item_index]){
                    let column_value = crm_items[boss_id][table][item_index][column];
                    if(column_value.indexOf('.') != -1){
                        column_value = column_value.split('.');
                        if(accepted_file_types.indexOf('.'+column_value[column_value.length - 1].toLowerCase()) != -1){
                            fs.access(crm_files+column_value.join('.'), function(error){
                                if (!error && column_value.join('.') != 'user.png') fs.unlinkSync(crm_files+column_value.join('.'));
                            });
                        }
                    }
                }
                delete crm_items[boss_id][table][item_index];
                if(guest_rooms[boss_id][item_index]) guest_rooms[boss_id][item_index].crm = null;
                io.to(String(boss_id)).emit('delete_item', {'index': item_index, 'table': table});
                sql = `DELETE FROM crm_items WHERE uid = '${item_index}' and owner_id = '${boss_id}' `;
                try{
                    const connection = await mysql.createConnection(connection_config);
                    await connection.execute(sql);
                    if(item_index.indexOf('!@!@2@!@!') != -1){
                        sql = `UPDATE rooms SET crm = NULL WHERE room = '${item_index}'`;
                        await connection.execute(sql);
                    }
                    connection.end();
                } catch (err) { syslog(`Ошибка (CRM remove) ${err}`, 'error'); }
                res.json({'success': true, 'error': "Изменения в CRM внесены"});
            } else if(info.setting == 'add') { // добавить
                if(!crm_items[boss_id]?.[table]){ res.json({'success': false, 'error': "Доступ ограничен !"}); return; }
                let access = false;
                if(parseInt(tariffs[bosses[boss_id]["tariff"]]["include"][`crm_items`]["value"]) == 0) access = true; // проверка по тарифу
                if(!access && Object.keys(crm_items[boss_id]).reduce((el, index) => {return Object.keys(index).length + Object.keys(el).length}) < parseInt(tariffs[bosses[boss_id]["tariff"]]["include"][`crm_items`]["value"]) ) access = true;
                if(access){ 
                    statistic_2('crm_items', boss_id); 
                    let client_id =  'item_' + uniqid();
                    crm_items[boss_id][table][client_id] = {'helper_photo': 'user.png', 'helper_name': 'новый', 'helper_info': getTime().split(' ')[1].split(':').slice(0,2).join(':') +' '+ getTime().split(' ')[0].split('-').reverse().join('.')};
                    io.to(String(boss_id)).emit('add_item', {'info': crm_items[boss_id][table][client_id], 'index': client_id, 'table': table});
                    res.json({"success": true, 'error': "CRM обнавлена !"});
                } else res.json({'success': false, 'error': "Лимит на создание клиентов превышен !"});
            } else if(info.setting == 'get_crm') {  
                if(table != 'all') res.json({'success': true, 'error':  crm_items[boss_id][table]});  
                else {
                    let result = {};
                    for(table in crm_items[boss_id]) Object.assign(result, crm_items[boss_id][table])
                    res.json({'success': true, 'error': result});  
                }
            } else if(info.setting == 'get_tasks') { // получить задачи
                let tasks_for_send = {};
                for(key in tasks[boss_id]){
                    if(tasks[boss_id][key]['type'] == 1 || tasks[boss_id][key]['creator_id'] == assistent_id){
                        if(!CompareTime(tasks[boss_id][key]["time"], getTime())) tasks[boss_id][key]['status'] = 'completed';
                        else tasks[boss_id][key]['status'] = 'uncompleted';
                        tasks_for_send[key] = tasks[boss_id][key];
                    }
                }
                res.json({'success': true, 'error': tasks_for_send});
            } else if(info.setting == 'delete_task') { // удалить задачу
                delete tasks[boss_id][item_index];
                try{
                    sql = `DELETE FROM tasks WHERE uid = '${item_index}' and owner_id = '${boss_id}'`;
                    const connection = await mysql.createConnection(connection_config);
                    await connection.execute(sql);
                    connection.end();
                    res.json({'success': true, 'error': "Задача удалена."});
                    io.to(String(boss_id)).emit('delete_task', {'uid': item_index});
                } catch(err) { 
                    syslog(`Ошибка (CRM task remove)`, 'error');
                    res.json({'success': false, 'error': "Серверная ошибка !"});
                }
            } else if(info.setting == 'add_task') { // добавить задачу
                if(!tasks.hasOwnProperty(boss_id)) tasks[boss_id] = {};
                let access = false;
                if(parseInt(tariffs[bosses[boss_id]["tariff"]]["include"]["tasks"]["value"]) == 0) access = true;
                if(!access){
                    if(Object.keys(tasks[boss_id]).length < parseInt(tariffs[bosses[boss_id]["tariff"]]["include"]["tasks"]["value"])) access = true
                }
                let selected = {"selected": info.task_selected}
                if(access){
                    statistic_2('tasks', boss_id);
                    let uid = 'task_' + uniqid();
                    let sql = `INSERT INTO tasks (id, owner_id, type, time, selected, text, uid, creator_id, selected_group) VALUES (0, '${boss_id}', '${info.task_type}', '${info.task_time}', '${JSON.stringify(selected)}', '${info.task_text}', '${uid}', '${assistent_id}', '${info.table}')`;
                    try{
                        const connection = await mysql.createConnection(connection_config);
                        await connection.execute(sql);
                        connection.end();
                        if(!tasks.hasOwnProperty(boss_id)) tasks[boss_id] = {};
                        let task_info = {'type': info.task_type, 'creator_id': assistent_id, 'time': info.task_time, 'text': info.task_text, 'selected': info.task_selected, 'status': 'uncompleted', 'table': info.table};
                        tasks[boss_id][uid] = task_info;
                        if(info.task_type == '1') io.to(String(boss_id)).emit('new-task', {'info': task_info, 'uid': uid});
                        res.json({'success': true, 'error': uid});
                    } catch(err) { syslog(`Ошибка (add task)`, 'error'); res.json({'success': false, 'error': "Ошибка сервера !"});}
                } else res.json({'success': true, 'error': "Превышено ограничение количества задач !"});
            } else if(info.setting == 'teleport') { // переместить строку
                let table_from = info.table_from; let table_to = info.table_to;
                if(!crm_items[boss_id]?.[table_from]) { res.json({'success': false, 'error': "Несуществующая таблица !"}); return; }
                if(!crm_items[boss_id]?.[table_from]?.[item_index]){ res.json({'success': false, 'error': "Доступ ограничен !"}); return; }
                if(!crm_items[boss_id]?.[table_to]) { res.json({'success': false, 'error': "Несуществующая таблица !"}); return; }
                crm_items[boss_id][table_to][item_index] = crm_items[boss_id][table_from][item_index];
                delete crm_items[boss_id][table_from][item_index];
                if(guest_rooms[boss_id][item_index]){ 
                    guest_rooms[boss_id][item_index].crm = table_to;
                    try{
                        const connection = await mysql.createConnection(connection_config);
                        if(item_index.indexOf('!@!@2@!@!') != -1){
                            sql = `UPDATE rooms SET crm = '${table_to}' WHERE room = '${item_index}'`;
                            await connection.execute(sql);
                        }
                        connection.end();
                    } catch (err) { syslog(`Ошибка (CRM teleport) ${err}`, 'error'); }
                }
                io.to(String(boss_id)).emit('crm_teleport', {"index": item_index, "item": crm_items[boss_id][table_to][item_index], 'table_to': table_to, 'table_from': table_from});
                res.json({'success': true, 'error': "CRM обнавлена !"}); return;
            } else if(info.setting == 'table_name'){ // переименовать таблицу
                let new_table = info.new_table; let prev_table = info.prev_table;
                if(crm_items?.[boss_id]?.[prev_table] && !crm_items?.[boss_id]?.[new_table]){
                    crm_items[boss_id][new_table] = crm_items[boss_id][prev_table];
                    delete crm_items[boss_id][prev_table];
                }
                io.to(String(boss_id)).emit('change_table', {'table_from':prev_table, 'table_to': table_to});
                res.json({'success': true, 'error': "CRM обнавлена !"}); return;
            } else if(info.setting == 'table_remove'){ // удалить таблицу
                let table_remove = info.table_remove; 
                try{
                    const connection = await mysql.createConnection(connection_config);
                    for(item in crm_items[boss_id][table_remove]){
                        if(item.indexOf('!@!@2@!@!') == -1) continue;
                        guest_rooms[boss_id][item].crm = null;
                        sql = `UPDATE rooms SET crm = NULL WHERE room = '${item}'`;
                        await connection.execute(sql);
                    }
                    connection.end();
                } catch (err) { syslog(`Ошибка (CRM table remove) ${err}`, 'error'); }
                if(crm_items?.[boss_id]?.[table_remove]) delete crm_items[boss_id][table_remove];
                io.to(String(boss_id)).emit('change_table', {'table_remove':table_remove});
                res.json({'success': true, 'error': "CRM обнавлена !"}); return;
            } else if(info.setting == 'table_add'){ // добавить таблицу
                let table_add = info.table_add; 
                if(!crm_items?.[boss_id]?.[table_add]) crm_items[boss_id][table_add] = {};
                io.to(String(boss_id)).emit('new_table', {'index': table_add});
                res.json({'success': true, 'error': "CRM обнавлена !"}); return;
            }
        } else if(type == 'get_statistic'){ // статистика (страница с выбром тарифов)
            let items_count = uip_count = assistents_count = crm_tasks_count = banned_count = 0;
            let boss_id = info.domain;
            if(assistents.hasOwnProperty(boss_id)) assistents_count = Object.keys(assistents[boss_id]).length - 1;
            if(banned.hasOwnProperty(boss_id)) banned_count = Object.keys(banned[boss_id]).length;
            if(crm_items.hasOwnProperty(boss_id)){ for(table in crm_items[boss_id]){ items_count += Object.keys(crm_items[boss_id][table]).length; } } 
            if(tasks.hasOwnProperty(boss_id)) crm_tasks_count = Object.keys(tasks[boss_id]).length;
            if(uvisitors.hasOwnProperty(boss_id)) uip_count = uvisitors[boss_id].length;
            res.json({'success': true, 'error':{'items_count': items_count, 'uip_count': uip_count, 'assistents_count':assistents_count, 'crm_tasks_count':crm_tasks_count, 'banned_count': banned_count}});
        } else if(type == 'delete_user'){ // удалить пользователя
            let rid = info.id;
            await delete_user(rid);
            res.json({'success': true, 'error': 'Пользователь удалён !'});
        } else if(type == 'set_money'){ // изменить кол денег пользователя
            let money = parseInt(info.money);
            let boss = info.boss;
            if(!money) money = 0;
            await set_money(money, boss);
            res.json({'success': true, 'error': 'Деньги изменены !'});
        } else if(type == 'personal_pay'){ // просроченная оплата 
            const connection = await mysql.createConnection(connection_config);
            let today = new Date(); 
            today =  getStringFromTime('date', today);
            let id = info.id;
            let money = info.money;
            let tariff = info.tariff;
            let status = await user_pay(id, money, tariff, connection, today);
            if(status){
                syslog(`Пользоватил ${id} оплатил ещё один месяц ${tariff}! Баланс - ${money}`, 'success');
                res.json({'success': true, 'error': 'Месяц оплачен!'});
            } else {
                syslog(`Пользоватил ${id} не смог оплатить ещё один месяц ${tariff}! Баланс - ${money}`, 'error');
                res.json({'success': false, 'error': 'Вы не смогли оплатить ещё один месяц !'});
            }
            connection.end();
        } else if(type == 'send_all_answer'){// Работа со множеством юзеров
            res.json({'success': true, 'error': 'Запрос передан'});
            // персональные данные
            let token = info.token;
            let domain = tokens["assistent"][token]["domain"];
            let assistent_id = tokens["assistent"][token]["assistent_id"];
            if(!assistents?.[domain]?.[assistent_id]) return;
            let name = assistents[domain][assistent_id]["name"];
            let departament = assistents[domain][assistent_id]["departament"]; 
            let photo = assistents[domain][assistent_id]["photo"];
            let collected_users = JSON.parse(info.users);
            let action = info.action;
            let decrypted_message = info.decrypted_message;
            let encrypted_message = info.encrypted_message;
            let adds = info.adds;
            let time = getTime();
            if(!adds) adds = '';
            else adds = JSON.stringify(adds);
            let message = '';
            if(action != 'style_mode') message = decrypted_message;
            else message = encrypted_message;
            const connection = await mysql.createConnection(connection_config);
            for(room in collected_users){
                if(!guest_rooms?.[domain]?.[room]) continue;
                io.in(String(room)).emit('consultant-message', ({
                    message: message,
                    user: name,
                    departament: departament,
                    photo: photo,
                    message_adds: adds,
                    mode: action,
                    time: time,
                    type: (room.indexOf('!@!@2@!@!') == -1 && chat_type == 'assistent' ? true : false),
                    sender: assistent_id
                }));
                let sql = return_sql('visitor', {
                    "id": 0,
                    "room": room,
                    "lastActivityTime": guest_rooms[domain][room]['lastActivityTime'],
                    "domains_list": JSON.stringify(guest_rooms[domain][room]["domains_list"]),
                    "served_list": JSON.stringify(guest_rooms[domain][room]["served_list"]),
                    "serving_list": JSON.stringify(guest_rooms[domain][room]["serving_list"]),
                    "info": JSON.stringify(guest_rooms[domain][room]["info"]),
                    "session_time": guest_rooms[domain][room]["session_time"],
                    "photo": guest_rooms[domain][room]["photo"],
                    "sessions": "",
                    "notes": guest_rooms[domain][room]["notes"],
                    "properties": guest_rooms[domain][room]["properties"],
                    "visits": guest_rooms[domain][room]["visits"],
                }); 
                await connection.execute(sql);
                sql = return_sql('consultant_message', {
                    id: assistent_id,
                    message: message,
                    time: time,
                    domain: domain,
                    room: room,
                    adds: adds,
                    visibility: (action == 'js_mode' ? 'invisible' : '')
                });
                await connection.execute(sql);
            }
            connection.end();
        } else if(type == 'csv'){ // csv
            let table = info.table;
            let id = info.boss_id;
            if(!crm_items[id].hasOwnProperty(table)){ res.json({'success': false, 'error': 'Несуществующая таблица.'}); return; }
            if(Object.keys(crm_items[id][table]) == 0){ res.json({'success': false, 'error': 'В таблице нет записей.'}); return;}
            const connection = await mysql.createConnection(connection_config);
            let result = await get_crmTable_csv(id, table, connection);
            connection.end();
            res.json({'success': true, 'error': result}); 
        } else if(type == 'room_info'){ // Заметки / свойства посетителя
            let room = info.room;
            let token = info.token;
            let domain = tokens["assistent"][token]["domain"];
            if (!guest_rooms?.[domain]?.[room]) { res.json({'success': false, 'error': 'Комнаты не существует !'}); return; };
            let setting_name = info.settings.name; 
            let value = info.settings.value;
            guest_rooms[domain][room][setting_name] = value;
            io.to(String(domain)).emit('userlist_update', ({"type": "room_settings", "value":value, "target": room, "option": setting_name}));
            res.json({'success': true, 'error': 'Успешно !'});
        } else if(type == 'change_departament'){ // название отдела
            let domain = info.domain;
            let from = info.from;
            let to = info.to;
            if(assistents.hasOwnProperty(domain)){
                for(assistent_id in assistents[domain]){
                    if(assistents[domain][assistent_id].departament != from) continue;
                    assistents[domain][assistent_id].departament = to;
                }
            }   
            res.json({'success': true, 'error': 'Успешно !'});
        } else if(type == 'reload_assistent'){ // перезагрузить страницу ассистента
            let id = info.id;
            io.to('assistent_'+id).emit('page_reload');
            res.json({'success': true, 'error': 'Успешно !'});
        } else if(type == 'ban_assistent'){ // блоировка ассистента
            let ban_id = info.ban_id;
            let domain = info.domain;
            let status = info.status;
            if(status === true) status = null;
            if(!assistents?.[domain]?.[ban_id]){
                res.json({'success': false, 'error': 'Не существующий ассистент !'});
                return;
            }
            assistents[domain][ban_id].ban = status;
            io.to('assistent_'+ban_id).emit('page_reload');
            io.to(String(info.domain)).emit('assistentlist_update', ({"type": "change_settings", "value": status, "target": ban_id, "option": 'ban'}));
            res.json({'success': false, 'error': 'Успех !'});
        } else res.json({'success': false, 'error': 'Не существующий тип !'});
    } else res.json({'success': false, 'error': 'Не правильный логин !'});
});
function socket_start(){
io.on('connection', async (socket) => {
    socket.on('boss_join', (data) => { // BOSS JOIN
        access = false;
        if(data && !socket.gdomain && !socket.adomain){
            if(data.room && data.token){ 
                if(tokens["boss"]?.[data.token]){
                    socket.leaveAll();
                    let info = tokens["boss"][data.token]; 
                    if(data.room == 'MAIN') data.room = info["domain"];
                    socket.join(data.room);
                    if(!socket.broom) socket.broom = data.room; 
                    if(!socket.bdomain) socket.bdomain = info["domain"]; 
                    socket.token = data.token;
                    access = true;
                }
            }
        }
        if(!access){ 
            socket.disconnect();
            syslog(`Не верный токен авторизации босс ${data.token} ${data.room}`, 'error');
        } 
    });
    socket.on('get_crm-info', () => { // информация по crm для CRM
        let result = {
            'complete_tasks': 0,
            'uncomplete_tasks':0
        };
        for(key in tasks[socket.adomain]){
            let time = tasks[socket.adomain][key]["time"];
            if(Date.parse(getTime()) > Date.parse(time) && (tasks[socket.adomain][key]["type"] == 1 || (tasks[socket.adomain][key]["creator_id"] == socket.assistent_id))) result['complete_tasks']++;
            else if(Date.parse(getTime()) <= Date.parse(time) && (tasks[socket.adomain][key]["type"] == 1 || (tasks[socket.adomain][key]["creator_id"] == socket.assistent_id))) result['uncomplete_tasks']++;
        }
        if(crm_items.hasOwnProperty(socket.adomain)){ 
            for(table in crm_items[socket.adomain]){
                result[table] = Object.keys(crm_items[socket.adomain][table]).length;
            } 
        }
        socket.emit('get_crm-info', result);
    });
    socket.on('get_crm_items', () => { // получить CRM items
        let domain = socket.adomain||socket.bdomain;
        socket.emit('get_crm_items', {'crm_items': crm_items[domain]});
    });
    socket.on('disconnecting', async() => { // выход
        // если посетитель
        if(socket.gdomain){
            if(!guest_rooms?.[socket.gdomain]?.[socket.groom]) return;
            let guest_room = socket.groom;
            user_flag = false;
            let connections = guest_rooms[socket.gdomain][guest_room]['connections'];
            if(guest_rooms[socket.gdomain][guest_room]['status'] == 'online' || connections - 1 >= 0){
                // подключения
                if(connections - 1 >= 0){connections--;  guest_rooms[socket.gdomain][guest_room]["connections"] = connections;}
                if(!connections){
                    setTimeout( async () => {
                        if(guest_rooms[socket.gdomain][guest_room]['connections'] == 0){
                            guest_rooms[socket.gdomain][guest_room]['status'] = 'offline';
                            socket.broadcast.to(guest_room).emit('room_status', {"status": "offline"});	
                            if(!socket.adds_status) statistic('visits', [socket.url_host]);	
                            else{ 
                                statistic("adds", [socket.url_host]);	
                                statistic(socket.adds_status, [socket.url_host]);	
                                if(socket.utm_url) UTM_statistic(socket.utm_url, [socket.url_host]);
                            }								
                            socket.broadcast.to(String(socket.gdomain)).emit('userlist_update', ({"type": "status", "value":"offline", "target": guest_room, "option": null}));	
                            if(guest_rooms[socket.gdomain][guest_room]['typing']){
                                guest_rooms[socket.gdomain][guest_room]['typing'] = '';
                                socket.broadcast.to(guest_room).emit('stoptyping');
                                socket.broadcast.to(String(socket.gdomain)).emit('userlist_update', ({"type": "typing", "value":null, "target": guest_room, "option": null}));	
                            }
                            let time = getTime();
                            guest_rooms[socket.gdomain][guest_room]["session_time"] += Math.abs(new Date(guest_rooms[socket.gdomain][guest_room]["session_start"]) - new Date(time))||0;
                            guest_rooms[socket.gdomain][guest_room]["lastActivityTime"] = time;
                            delete guest_rooms[socket.gdomain][guest_room]["session_start"];
                            socket.broadcast.to(String(socket.gdomain)).emit('userlist_update', ({"type": "time", "value":null, "target": guest_room, "option": null, "lastActivityTime":time }));	
                            let sql = `UPDATE rooms SET visits = '${guest_rooms[socket.gdomain][guest_room]["visits"]}', time = '${time}', session_time = '${guest_rooms[socket.gdomain][guest_room]["session_time"]||0}' WHERE room = '${guest_room}' `;
                            try {
                                const connection = await mysql.createConnection(connection_config);	
                                await connection.execute(sql);
                                connection.end();
                            } catch (err){ syslog(`ОШИБКА (disconnect)`, 'error'); }
                        }
                    }, 10000);
                }
            }	
        }
        // если ассистент
        if(socket.adomain){
            if(!assistents?.[socket.adomain]?.[socket.assistent_id]) return;	
            delete assistents?.[socket.adomain]?.["public_room"]?.["assistents_in"]?.[socket.assistent_id];
            user_flag = false;
            let connections = assistents[socket.adomain][socket.assistent_id]["connections"];			
            if(connections - 1 >= 0){connections--; assistents[socket.adomain][socket.assistent_id]["connections"] = connections; }
            if(!connections){
                setTimeout( async () => {
                    if(assistents[socket.adomain][socket.assistent_id]['connections'] == 0){
                        assistents[socket.adomain][socket.assistent_id]["status"] = "offline";
                        io.to(String(socket.adomain)).emit('assistentlist_update', ({"type": "status", "value":"offline", "target": socket.assistent_id, "option": null}));	
                        await check_assistents_status(socket.adomain, 'public');
                    }
                }, 10000);
            }
            let time = getTime();
            assistents[socket.adomain][socket.assistent_id]["time"] = time;
            io.to(String(socket.adomain)).emit('assistentlist_update', ({"type": "time", "value":time, "target": socket.assistent_id, "option": null }));	
            let sql = `UPDATE assistents SET time = '${time}' WHERE id = '${socket.assistent_id}' `;
            try {
                const connection = await mysql.createConnection(connection_config);	
                await connection.execute(sql);
                connection.end();
            } catch(err) { syslog(`ОШИБКА (disconnect)`, 'error'); }
           
        }
    });
    // ассистент печатает
    socket.on('typing', () => {
        socket.broadcast.to(socket.aroom).emit('typing', (assistents?.[socket.adomain]?.[socket.assistent_id]?.["name"]));
    });
    socket.on('stopTyping', () => {
        socket.broadcast.to(socket.aroom).emit('stopTyping');
    });
    socket.on('leave', (data) => {
        socket.broadcast.emit('leave', (data));
        socket.broadcast.to(socket.aroom).emit('stopTyping');
    });
    socket.on('get_teammate_mas', () => { // ассистент или босс берёт коллег из списка
        let domain = socket.adomain||socket.bdomain;
        socket.emit('get_teammate_mas', {"assistents": assistents[domain]});
    });
    socket.on('consultant_chat_readed', (data) => { // прочитал сообщение чат консультантов
        if(!data) return;
        if(data.type && data.room) delete assistents[socket.adomain][socket.assistent_id][data.type][data.room];
    });
    socket.on("get-bannedguests-mas", () => {  // ассистент получает бан лист
        socket.emit('get-bannedguests-mas', {"rooms": banned[socket.adomain], "crm_items": crm_items[socket.adomain]});
    });
    socket.on('room_status', async (data) => { // запрос на информацию о комнате
        if(data){
            let room = socket.aroom||socket.broom;
            let domain = socket.adomain||socket.bdomain;
            if(guest_rooms.hasOwnProperty(domain) && data.type){
                if(data.type == 'guest'){
                    if(!crm_items.hasOwnProperty(domain)) crm_items[domain] = {};
                    let crm_info = {};
                    let table = guest_rooms[domain][room].crm;
                    if(crm_items[domain]?.[table]?.[room]){
                        let sql = `SELECT columns FROM crm WHERE owner_id = ${domain}`;
                        const connection = await mysql.createConnection(connection_config);
                        const [rows, fieds] = await connection.execute(sql);
                        let columns = JSON.parse(rows[0]["columns"]);
                        crm_info["helper_photo"] =crm_items[domain][table][room]["helper_photo"];
                        crm_info["helper_name"] = crm_items[domain][table][room]["helper_name"];
                        crm_info["columns"] = {};
                        for(column in columns[table]["table_columns"]){
                            crm_info["columns"][column] = crm_items[domain][table][room][column]||columns[table]["table_columns"][column]["deffault"];
                        }
                        connection.end();
                    }
                    if(guest_rooms[domain].hasOwnProperty(room)) socket.emit('room_status', {
                        "session": {
                            "session_time": guest_rooms[domain][room]['session_time'], 
                            "session_start": guest_rooms[domain][room]['session_start']
                        }, 
                        "photo":guest_rooms[domain][room]['photo'], 
                        "status": guest_rooms[domain][room]['status'], 
                        "previous_page": guest_rooms[domain][room]['previous_page'], 
                        "actual_page": guest_rooms[domain][room]['actual_page'], 
                        "typing":  guest_rooms[domain][room]['typing'], 
                        "info": guest_rooms[domain][room]['info'], 
                        "CRM_info": crm_info,
                        "visits": guest_rooms[domain][room]['visits'],
                        "table": table,
                    });
                } else if(data.type == 'bannedguest'){
                    room = data.room; 
                    if(banned?.[domain]?.[room]){                    
                        if(!crm_items.hasOwnProperty(domain)) crm_items[domain] = {};
                        let crm_info = {};
                        let table = banned[domain][room].crm;
                        if(crm_items[domain]?.[table]?.[room]){
                            crm_info["helper_photo"] =crm_items[domain][table][room]["helper_photo"];
                            crm_info["helper_name"] = crm_items[domain][table][room]["helper_name"];
                            crm_info["columns"] = {};
                            let sql = `SELECT columns FROM crm WHERE owner_id = ${domain}`;
                            const connection = await mysql.createConnection(connection_config);
                            const [rows, fieds] = await connection.execute(sql);
                            for(column in columns[table]["table_columns"]){
                                crm_info["columns"][column] = crm_items[domain][table][room][column]||columns[table]["table_columns"][column]["deffault"];
                            }
                        }
                        if(Object.keys(crm_info).length > 0) socket.emit('room_status', {"CRM_info": crm_info});
                    }
                } else if(data.type == 'assistent' && data.oponent && assistents?.[domain]?.[data.oponent]) socket.emit('room_status', {"status": assistents[domain][data.oponent]['status']});
            } else socket.emit('page_reload');
        }
    });
    socket.on('remove_room', async (data) => { // удаление комнаты
        if(data){
            if(!guest_rooms?.[socket.adomain]?.[data.room]) return;
            if(data.type == 'remove') guest_rooms[socket.adomain][data.room]["hide"] = true;
            else guest_rooms[socket.adomain][data.room]["hide"] = false;
            let sql;
            if(data.type == 'remove') sql = `UPDATE rooms SET hide = 'deleted' WHERE room = '${data.room}'`;
            if(data.type == 'restore') sql = `UPDATE rooms SET hide = NULL WHERE room = '${data.room}'`;
            try{
                const connection = await mysql.createConnection(connection_config);
                await connection.execute(sql);
                connection.end();
            } catch(err) { syslog(`ОШИБКА (room remove)`, 'error'); }
            io.to(String(socket.adomain)).emit('userlist_update', ({"type": data.type == 'remove' ? "delete" : "restore", "value": null, "target": data.room, "option": null}));
        }
    });
    socket.on('ban_room', async (data) => { // бан
        if(!data) return;
        if(!data.reason || !data.room) return;
        if(!guest_rooms?.[socket.adomain]?.[data.room]) return;
        prev_info = guest_rooms[socket.adomain][data.room];
        for(i in prev_info["serving_list"]["assistents"]) prev_info["served_list"]["assistents"].push(prev_info["serving_list"]["assistents"][i]);
        prev_info["serving_list"]["assistents"] = [];
        delete guest_rooms[socket.adomain][data.room];
        let ban_id = socket.adomain + '!@!@2@!@!' + prev_info.info.ip;
        banned[socket.adomain][ban_id] = prev_info;
        banned[socket.adomain][ban_id]["reason"] = data.reason;
        banned[socket.adomain][ban_id]["room_id"] = data.room;
        banned[socket.adomain][ban_id]["status"] = "offline";
        banned[socket.adomain][ban_id]["connections"] = 0;
        banned[socket.adomain][ban_id]["lastActivityTime"] = getTime();
        banned[socket.adomain][ban_id]["bannedBy"] = socket.assistent_id;
        banned[socket.adomain][ban_id]["crm"] = prev_info.crm;
        statistic('banned', prev_info['domains_list']['domains']);
        try {
            const connection = await mysql.createConnection(connection_config);
            let sql = `SELECT id FROM rooms WHERE room = '${data.room}'`;
            let id;
            const [rows, fields] = await connection.execute(sql);
            if(rows[0]) id = rows[0]["id"];
            else id = 0;
            sql = `DELETE FROM rooms WHERE room = '${data.room}'`;
            await connection.execute(sql);
            connection.query("SET NAMES utf8");
            sql = return_sql('banned', {
                'id': id,
                'ban_id': ban_id,
                'domains_list':JSON.stringify(banned[socket.adomain][ban_id]["domains_list"]),
                'notes':banned[socket.adomain][ban_id]["notes"],
                'properties':banned[socket.adomain][ban_id]["properties"],
                'served_list': JSON.stringify(banned[socket.adomain][ban_id]["served_list"]),
                'reason': data.reason,
                'assistent_id': socket.assistent_id,
                'info': JSON.stringify(banned[socket.adomain][ban_id]["info"]),
                'room': data.room,
                'sessions': "",
                "session_time": banned[socket.adomain][ban_id]['session_time'],
                'photo': banned[socket.adomain][ban_id]['photo'],
                'visits': banned[socket.adomain][ban_id]['visits'],
            });
            await connection.execute(sql);
            sql = `UPDATE banned SET crm = '${prev_info.crm}' WHERE room = '${ban_id}'`;
            await connection.execute(sql);
            connection.end();
            socket.broadcast.to(data.room).emit("ban");
            io.to(String(socket.adomain)).emit('baned_user', {"target": data.room, "info": banned[socket.adomain][ban_id], "bun_id": ban_id});
        } catch(err) { syslog(`ОШИБКА (ban) ${err}`, 'error'); }
    });
    socket.on('unban_room', async (data) => {  // разбан
        if(!data) return;
        if(!data.room) return;
        if(!banned?.[socket.adomain]?.[data.room]) return;
        let ban_id = data.room;
        delete banned[socket.adomain][ban_id]["reason"];
        delete banned[socket.adomain][ban_id]["bannedBy"];
        let guest_id = banned[socket.adomain][ban_id]["room_id"];
        delete banned[socket.adomain][ban_id]["room_id"];
        prev_info = banned[socket.adomain][data.room];
        delete banned[socket.adomain][data.room];
        guest_rooms[socket.adomain][guest_id] = prev_info;
        guest_rooms[socket.adomain][guest_id]["serving_list"] = {};
        guest_rooms[socket.adomain][guest_id]["serving_list"]["assistents"] = [];
        try{
            const connection = await mysql.createConnection(connection_config);
            let sql = `SELECT id FROM banned WHERE room = '${data.room}'`;
            const [rows, fields] = await connection.execute(sql);
            let id = rows[0]["id"];
            sql = `DELETE FROM banned WHERE room = '${data.room}'`; 
            await connection.execute(sql);
            sql = return_sql('visitor', {
                "id": id,
                "room": guest_id,
                "lastActivityTime": guest_rooms[socket.adomain][guest_id]['lastActivityTime'],
                "domains_list": JSON.stringify(guest_rooms[socket.adomain][guest_id]["domains_list"]),
                "served_list": JSON.stringify(guest_rooms[socket.adomain][guest_id]["served_list"]),
                "serving_list": '{"assistents": []}',
                "info": JSON.stringify(guest_rooms[socket.adomain][guest_id]["info"]),
                "session_time": guest_rooms[socket.adomain][guest_id]["session_time"],
                "photo": guest_rooms[socket.adomain][guest_id]["photo"],
                "sessions": "",
                'notes':guest_rooms[socket.adomain][socket.aroom]["notes"],
                'properties':guest_rooms[socket.adomain][socket.aroom]["properties"],
                "visits": guest_rooms[socket.adomain][guest_id]["visits"],
            });
            await connection.execute(sql);
            sql = `UPDATE rooms SET crm = '${prev_info.crm}' WHERE room = '${data.room}'`;
            await connection.execute(sql);
            connection.end();
            io.to(String(socket.adomain)).emit('unbaned_user', {"target": data.room, "info": guest_rooms[socket.adomain][guest_id], "guest_id": guest_id});
        } catch (err) { syslog(`ОШИБКА (unban)`, 'error'); }
    });
    socket.on('get_new_assistent_chat_messages', (type) => { // новые сообщения (уведомления)
        if(!assistents?.[socket.adomain]?.[socket.assistent_id]) return;
        socket.emit('get_new_assistent_chat_messages', {"mas": assistents[socket.adomain][socket.assistent_id][type]});
    });
    socket.on('assistent-join', (data) => { // ASSISTENT JOIN
        let first_access = false;
        let second_access = false;
        let assistents_status = true;
        if(data && !socket.gdomain && !socket.bdomain){
            if(data.room && data.token){ 
                if(tokens["assistent"]?.[data.token]){
                    socket.leaveAll();
                    let info = tokens["assistent"][data.token]; 
                    if(data.room == "PERSONAL") data.room = info["assistent_id"];
                    else socket.join(info["assistent_id"]);
                    if(data.room == "MAIN") data.room = info["domain"];
                    else if(data.room == "ASSISTENT" && data.room_id){
                        if(data.room_id.includes('!@!@2@!@!')){
                            if(rooms.indexOf(data.room_id) != -1) data.room = data.room_id;
                            else if(rooms.indexOf(data.room_id.split('!@!@2@!@!').reverse().join('!@!@2@!@!')) != -1) data.room = data.room_id.split('!@!@2@!@!').reverse().join('!@!@2@!@!');
                            else{
                                rooms.push(data.room_id);
                                data.room = data.room_id;
                            }
                        } else {
                            data.room = data.room_id;
                            assistents?.[info["domain"]]?.["public_room"]?.["assistents_in"].push(info["assistent_id"]);
                        }
                        socket.join(info["domain"]);
                    }
                    socket.join(data.room);
                    if(!socket.aroom) socket.aroom = data.room;
                    if(!socket.adomain) socket.adomain = info["domain"];
                    if(!socket.assistent_id) socket.assistent_id = info["assistent_id"];
                    socket.join("assistent_"+socket.assistent_id);
                    first_access = true;
                }
            }
        }
        if(first_access){
            if(assistents?.[socket.adomain]?.[socket.assistent_id]){
                assistents[socket.adomain][socket.assistent_id]["status"] = "online";
                assistents[socket.adomain][socket.assistent_id]["connections"]++;
                socket.broadcast.to(String(socket.adomain)).emit('assistentlist_update', ({"type": "status", "value":"online", "target": socket.assistent_id, "option": null}));	
                for(key in assistents[socket.adomain]){ 
                    if(assistents[socket.adomain][key]["status"] == "online" && key != socket.assistent_id) assistents_status = false; 
                }	
                if(assistents_status) socket.to("guest_" + socket.adomain).emit("delete_form");
                second_access = true;
            }
        }
        if(!second_access){ 
            socket.disconnect();
            syslog(`Не верный токен авторизации ассистент ${data.token} ${data.room}`, 'error');
        } 
        console.log(tokens);
    });
    socket.on('get-assistent-messages', async (data) => { // получить старые сообщения
        let room = socket.aroom||socket.broom;
        let domain = socket.adomain||socket.bdomain;
        if(!data) return;
        if(!data.type) return;
        var sql = '';
        if(data.type == "guest") sql = `
            SELECT 
                assistents.name, 
                messages_with_users_guests.message, 
                visibility AS mode,
                SendTime, 
                assistents.photo, 
                assistents.departament, 
                messages_with_users_guests.sender, 
                messages_with_users_guests.adds,
                offline_forms.name AS form_name,
                offline_forms.email AS form_email,
                offline_forms.message AS form_message,
                offline_forms.phone AS form_phone,
                notifications.text as notification_text,
                notifications.departament as notification_departament,
                notifications.type as notification_type,
                notifications.adds as notification_adds,
                notifications.name as notification_name,
                notifications.photo as notification_photo
            FROM messages_with_users_guests  
            LEFT JOIN assistents ON 
                (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id ))
            LEFT JOIN notifications ON
                (messages_with_users_guests.message = notifications.uid AND messages_with_users_guests.sender = 'notification' AND notifications.type != 'JavaScript')     
            LEFT JOIN offline_forms ON
                (messages_with_users_guests.room = offline_forms.sender AND messages_with_users_guests.message = offline_forms.uid)
            WHERE ( messages_with_users_guests.domain = '${domain}' AND messages_with_users_guests.room = (SELECT id FROM rooms WHERE room = '${room}')) 
            ORDER BY messages_with_users_guests.id ASC
        `;
        else if(data.type == "bannedguest") sql = `
            SELECT 
                assistents.name, 
                messages_with_users_guests.message, 
                visibility AS mode,
                SendTime, 
                assistents.photo, 
                assistents.departament, 
                messages_with_users_guests.sender, 
                messages_with_users_guests.adds,
                offline_forms.name AS form_name,
                offline_forms.email AS form_email,
                offline_forms.message AS form_message,
                offline_forms.phone AS form_phone,
                notifications.text as notification_text,
                notifications.departament as notification_departament,
                notifications.type as notification_type,
                notifications.adds as notification_adds,
                notifications.name as notification_name,
                notifications.photo as notification_photo
            FROM messages_with_users_guests  
            LEFT JOIN assistents ON 
                (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id ))
            LEFT JOIN notifications ON
                (messages_with_users_guests.message = notifications.uid AND messages_with_users_guests.sender = 'notification' AND notifications.type != 'JavaScript')
            LEFT JOIN offline_forms ON
                (messages_with_users_guests.room = offline_forms.sender AND messages_with_users_guests.message = offline_forms.uid)
            WHERE ( messages_with_users_guests.domain = '${domain}' AND messages_with_users_guests.room = (SELECT id FROM banned WHERE room_id = '${room}') ) 
            ORDER BY messages_with_users_guests.id ASC
        `;
        else if(data.type == "assistent") sql = `
            SELECT 
                name, 
                message, 
                SendTime,
                photo, 
                departament, 
                adds,
                sender
            FROM assistents_chat_messages 
            LEFT JOIN assistents  
            ON 
                (( assistents_chat_messages.sender = assistents.id ) OR ( assistents_chat_messages.sender IS NULL AND assistents_chat_messages.sender != assistents.id )) 
            WHERE ( assistents_chat_messages.domain = '${domain}' AND (assistents_chat_messages.room = '${room.split('!@!@2@!@!').reverse().join('!@!@2@!@!')}' OR assistents_chat_messages.room = '${room}') ) 
            ORDER BY assistents_chat_messages.id ASC
        `;
        if(sql != ''){
            try{
                const connection = await mysql.createConnection(connection_config);
                const [rows, fields] = await connection.execute(sql);
                socket.emit('get_previous_messages_assistent', (rows));
                connection.end();
            } catch (err) { syslog(`Ошибка (a get msgs)`, 'error'); }
        }
    });
    socket.on('get-guests-mas-first', () => { // берём массив с посетителями
        socket.emit('get-guests-mas-first', {"rooms": guest_rooms?.[socket.adomain], "crm_items": crm_items?.[socket.adomain]});
    });
    socket.on("get_settings", async () => { // берём настройки
        try{
            var sql = `SELECT json_extract(settings, '$.InterHelperOptions') AS settings, json_extract(settings, '$.feedbackform') AS offline_form FROM users WHERE id = '${socket.gdomain}'`;
            const connection = await mysql.createConnection(connection_config);
            const [rows, fields] = await connection.execute(sql);
            let settings = JSON.parse(rows[0]['settings']);
            let offline_form = rows[0]['offline_form'];
            let domain_settings = 'deffault';
            if(socket.url_host){
                if(settings.hasOwnProperty(socket.url_host)) domain_settings = socket.url_host;
            }
            socket.emit('chat-settings', {"settings": settings[domain_settings], 'offline_form': offline_form});
            connection.end();
        } catch(err) { syslog(`Ошибка (settings)`, 'error'); }
    });
    socket.on("check_interhelper_domain", async (data) => { // VISITOR JOIN
        let previous_page = data.previous_page; 
        let actual_page = data.actual_page; 
        let device = data.device;
        let domain;
        try{ 
            if(actual_page) actual_page = decodeURI(actual_page);
            if(previous_page) previous_page = decodeURI(previous_page);
            if(data.actual_page) domain = new URL(data.actual_page).hostname; 
            else if(socket.request.headers.origin) domain = new URL(socket.request.headers.origin).hostname; 
            else {
                domain = "Неизвестно";
                syslog(`Ошибка (domain)`, 'error');
            }
        } catch(err) {syslog(`Ошибка (domain) ${err} ${actual_page} ${previous_page} ${socket.request.headers.origin}`, 'error'); domain = "Неизвестно"  }
        let access = true;
        if(!socket.gdomain){ // проверка домена
            try{
                let www_domain_version;
                if(domain.indexOf('www.') != -1){
                    www_domain_version = domain;
                    domain = domain.replace('www.', '');
                } else www_domain_version = 'www.' + domain;
                const connection = await mysql.createConnection(connection_config);
                var sql = `SELECT id, money, payday FROM users WHERE domain LIKE '%"${domain}"%'`;
                const [rows] = await connection.execute(sql);
                var sql = `SELECT id, money, payday FROM users WHERE domain LIKE '%"${www_domain_version}"%'`;
                const [rows2] = await connection.execute(sql);
                connection.end();
                if(rows[0]){ 
                    let money = parseInt(rows[0]['money']);
                    let payday = rows[0]['payday'];
                    if(money < 0 || payday == 0){
                        socket.disconnect();
                        return;
                    }
                    socket.gdomain = rows[0]["id"];
                    socket.url_host = domain;
                } else if(rows2[0]){
                    let money = parseInt(rows2[0]['money']);
                    let payday = rows2[0]['payday'];
                    if(money < 0 || payday == 0){
                        socket.disconnect();
                        return;
                    }
                    socket.gdomain = rows[0]["id"];
                    socket.url_host = www_domain_version;
                }
            } catch(err) { syslog(`Ошибка (domain) ${err}`, 'error'); }
        }
        // проверка на бан
        if(banned.hasOwnProperty(socket.gdomain)){
            if(banned?.[socket.gdomain]?.[socket.gdomain + '!@!@2@!@!' + socket.request.connection.remoteAddress]){
                access = false; 
                socket.emit("ban");
                socket.domain_status = "canceled"; 
                syslog(`Посетитель в бане (попытка переподключения) ${socket.request.connection.remoteAddress}`, 'strange');
            }
        }
        if(socket.gdomain && access){
            socket.emit('interhelper_status', {'status': 'exist', 'id': socket.gdomain }); 
            let advertisement = '';
            // проверка рекламы
            let adds_status = check_adds(previous_page, actual_page, socket);
            if(adds_status["status"]){
                advertisement = adds_status["adds_type"];
                try{
                    let visitor_ip = socket.request.connection.remoteAddress;
                    let boss_id = socket.gdomain;
                    let today = new Date();
                    let today_date = today.getDate();
                    if(parseInt(today_date) < 10) today_date = '0' + today_date;
                    let today_mounth = today.getMonth() + 1;
                    if(parseInt(today_mounth) < 10) today_mounth = '0' + today_mounth;
                    let today_year = today.getFullYear();
                    today = today_year + '-' + today_mounth + '-' +today_date;
                    const connection = await mysql.createConnection(connection_config);
                    if(!adds_visitors.hasOwnProperty(boss_id)) adds_visitors[boss_id] = {};
                    if(adds_visitors[boss_id].hasOwnProperty(visitor_ip)){
                        adds_visitors[boss_id][visitor_ip]["count"]++;
                        let visits_count = adds_visitors[boss_id][visitor_ip]["count"];
                        sql = `SELECT json_extract(settings, '$.adds') AS settings FROM users WHERE id = '${boss_id}'`;
                        [rows, fields] = await connection.execute(sql);
                        result_rows = JSON.parse(rows[0]['settings']);
                        let limit_count = result_rows['adds_trys'];
                        let redirect = (result_rows['adds_redirect'] == 'checked');
                        let autoban = (result_rows['adds_autoban'] == 'checked');
                        if(limit_count <= visits_count && (autoban || redirect)){ 
                            if(autoban) {
                                socket.emit("ban");
                                statistic("adds_banned", [socket.url_host]);
                            }
                            if(redirect) {
                                let new_url = actual_page.split('?')[0];
                                socket.emit("change_url", {'url': new_url, 'type': 'anticlicker'});
                                statistic("adds_redirected", [socket.url_host]);
                            }
                        }
                        sql = `UPDATE adds_visitors SET count = ${visits_count}, time = '${today}' WHERE ip = '${visitor_ip}' and owner_id = '${boss_id}'`;
                        await connection.execute(sql);
                    } else {
                        adds_visitors[boss_id][visitor_ip] = {"time": today, "count": 1};
                        sql = `INSERT INTO adds_visitors (id, owner_id, ip, time, count) VALUES (0, '${boss_id}', '${visitor_ip}', '${today}', 1)`;
                        await connection.execute(sql);
                    }
                    connection.end();
                } catch(err) { syslog(`Ошибка (adds visitors) ${err}`, 'error'); }
            }
            // генерация uid (если его нет)
            if(!data.uid || data.uid.indexOf('!@!@IP@!@!') !== -1 || data.uid.indexOf('!@!@2@!@!') == -1 ){
                if(!socket.groom) socket.groom = socket.gdomain + '!@!@2@!@!' + uniqid();
                socket.emit('uid', {"uid": socket.groom});
            } else if(!socket.groom){
                if(data.uid.split('!@!@2@!@!')[0] == String(socket.gdomain)) socket.groom = data.uid; 
                else{
                    socket.groom = socket.gdomain + '!@!@2@!@!' + uniqid();
                    socket.emit('uid', {"uid": socket.groom});
                }
            }
            socket.join(socket.groom); // входим в комнату
            socket.join("guest_"+socket.gdomain); // входим в общую комнату посетителей на домене
            // уникальность
            if(uvisitors[socket.gdomain].indexOf(socket.request.connection.remoteAddress) == -1){ // если нет в массиве IP
                try{
                    const connection = await mysql.createConnection(connection_config);
                    sql = `SELECT payday FROM users WHERE id = '${socket.gdomain}'`;
                    let [Drows, Dfields ] = await connection.execute(sql);
                    if(parseInt(Drows[0]["payday"]) != 0){
                        uvisitors[socket.gdomain].push(socket.request.connection.remoteAddress);
                        sql = `INSERT INTO uvisitors (id, owner_id, ip) VALUES (0, '${socket.gdomain}', '${socket.request.connection.remoteAddress}')`;
                        await connection.execute(sql);
                        sql = `SELECT count(1) FROM uvisitors WHERE owner_id = '${socket.gdomain}'`;
                        const [UVrows, UVfields] = await connection.execute(sql);
                        if(UVrows[0]["count(1)"] > tariffs[bosses[socket.gdomain]["tariff"]]["include"]["unique_visits"]["value"] ){
                            let over_price_cost = tariffs[bosses[socket.gdomain]["tariff"]]["include"]["unique_visits_limit"]["value"];
                            sql = `UPDATE users SET money = money - ${over_price_cost} WHERE id = '${socket.gdomain}'`;
                            await connection.execute(sql);
                        }
                        statistic('unique_visitors', [socket.url_host]);
                    }
                    connection.end();
                } catch(err) { syslog(`Ошибка (unique) ${err}`, 'error'); }
            }
            // проверка массива 
            if(!guest_rooms[socket.gdomain]?.[socket.groom]){  // Новый посетитель
                let guest_body = {
                    "served_list": {"assistents": []},
                    "serving_list": {"assistents": []},
                    "domains_list": {"domains": [domain]},
                    "status": "online", 
                    "new_message": {
                        "message": null, 
                        "status": "readed",
                        "message_adds": null,
                    }, 
                    'notes':{"notes": {}},
                    'properties':{"properties": {}},
                    "messages_exist": false, 
                    "actual_page": actual_page, 
                    "previous_page": previous_page, 
                    "typing": "", 
                    "hide": false, 
                    "connections": 1, 
                    "lastActivityTime": getTime(), 
                    "info": {
                        "ip": socket.request.connection.remoteAddress, 
                        "device": device,
                        "user-agent": socket.handshake.headers["user-agent"], 
                        "geo": geoip.lookup(socket.request.connection.remoteAddress), 
                        "advertisement": advertisement
                    },
                    "sessions": {},
                    "movements": {
                        "clicks": [],
                        "max_scroll": 0,
                        "mouse_move": [],
                    },
                    "session_time": 0,
                    "session_start": getTime(),
                    "photo": {
                        "img": visitors_photos[Math.floor(Math.random()*visitors_photos.length)],
                        "color": visitors_color_bg[Math.floor(Math.random()*visitors_color_bg.length)]
                    },
                    "visits": 1,
                };
                guest_rooms[socket.gdomain][socket.groom] = guest_body;
                socket.broadcast.to(String(socket.gdomain)).emit('userlist_update', ({"type": "new_guest", "value":guest_body, "target": socket.groom, "option": null})); // отправляем инфу ассистентам
            } else { // Существующий посетитель
                guest_rooms[socket.gdomain][socket.groom]["info"]['device'] = device;
                if(!guest_rooms[socket.gdomain][socket.groom]["session_start"]) guest_rooms[socket.gdomain][socket.groom]["session_start"] = getTime();
                if(guest_rooms[socket.gdomain][socket.groom]["status"] == "offline") guest_rooms[socket.gdomain][socket.groom]["visits"]++;
                guest_rooms[socket.gdomain][socket.groom]["status"] = "online"; // статус посетителя
                guest_rooms[socket.gdomain][socket.groom]["connections"]++; // + к вкладкам
                if(guest_rooms[socket.gdomain][socket.groom]["hide"]){ // проверка на скрытую комнату
                    let sql = `UPDATE rooms SET hide = null WHERE room = '${socket.groom}'`;
                    try{
                        const connection = await mysql.createConnection(connection_config);
                        await connection.execute(sql);
                        connection.end();
                    } catch(err) { syslog(`Ошибка (reconnect) ${err}`, 'error'); }
                    guest_rooms[socket.gdomain][socket.groom]["hide"] = false;
                }
                // инфа по ссылкам
                guest_rooms[socket.gdomain][socket.groom]["previous_page"] = previous_page;
                guest_rooms[socket.gdomain][socket.groom]["actual_page"] = actual_page;
                if(advertisement) guest_rooms[socket.gdomain][socket.groom]["info"]["advertisement"] = advertisement;
                else advertisement = guest_rooms[socket.gdomain][socket.groom]["info"]["advertisement"];
                // проверка домена посетителя
                if(guest_rooms[socket.gdomain][socket.groom]["domains_list"]["domains"].indexOf(domain) == -1 && domain != null && guest_rooms[socket.gdomain][socket.groom]["messages_exist"]){ // новый домен (юзер существует)
                    guest_rooms[socket.gdomain][socket.groom]["domains_list"]["domains"].push(domain);
                    try{
                        const connection = await mysql.createConnection(connection_config);
                        var domains_list;
                        var sql = `SELECT domains_list FROM rooms WHERE room = '${socket.groom}'`;
                        const [rows, fields] = await connection.execute(sql);
                        domains_list = JSON.parse(rows[0]["domain"]);
                        domains_list["domains"].push(domain);
                        domains_list = JSON.stringify(domains_list);
                        sql = `UPDATE rooms SET domains_list = '${domains_list}' WHERE room = '${socket.groom}'`;
                        await connection.execute(sql);
                        connection.end();
                        io.to(String(socket.gdomain)).emit('userlist_update', ({"type": "domain", "value":domain, "target": socket.groom, "option": null}));
                    } catch(err) { syslog(`Ошибка (new domain) ${err}`, 'error'); }
                } else if(guest_rooms[socket.gdomain][socket.groom]["domains_list"]["domains"].indexOf(domain) == -1 && domain != null && guest_rooms[socket.gdomain][socket.groom]["messages_exist"]){ // новый домен (юзер не существует)
                    guest_rooms[socket.gdomain][socket.groom]["domains_list"]["domains"].push(domain);
                    io.to(String(socket.gdomain)).emit('userlist_update', ({"type": "domain","value":domain, "target": socket.groom, "option": null}));
                }
                if(guest_rooms[socket.gdomain][socket.groom]["serving_list"]["assistents"].length > 0){
                    for(assistent_index in guest_rooms[socket.gdomain][socket.groom]["serving_list"]["assistents"]){
                        let assistent_id = guest_rooms[socket.gdomain][socket.groom]["serving_list"]["assistents"][assistent_index];
                        let assistent = assistents[socket.gdomain][assistent_id];
                        socket.emit("servicing_assistent", ({
                            "name": assistent["name"],
                            "departament": assistent["departament"],
                            "photo": assistent["photo"],
                        }));
                    }
                }
                socket.broadcast.to(String(socket.gdomain)).emit('userlist_update', ({"type": "status", "value":"online", "target": socket.groom, "option": [previous_page, actual_page, guest_rooms[socket.gdomain][socket.groom]["visits"]]})); // отправляем инфу ассистентам
                let info = {
                    "ip": socket.request.connection.remoteAddress, 
                    "user-agent": socket.handshake.headers["user-agent"], 
                    "geo": geoip.lookup(socket.request.connection.remoteAddress), 
                    "advertisement": advertisement
                }
                if(guest_rooms[socket.gdomain][socket.groom]["messages_exist"]){ // обновлем инфу в бд позиция и реклама
                    try{
                        const connection = await mysql.createConnection(connection_config);
                        sql = `UPDATE rooms SET info = '${JSON.stringify(info)}' WHERE room = '${socket.groom}'`;
                        await connection.execute(sql);
                        connection.end();
                    } catch(err) { syslog(`Ошибка (new info) ${err}`, 'error'); }
                }
            }
            socket.broadcast.to(socket.groom).emit('room_status', {
                "session": {
                    "session_time": guest_rooms[socket.gdomain][socket.groom]['session_time'], 
                    "session_start": guest_rooms[socket.gdomain][socket.groom]['session_start']
                }, 
                "photo":guest_rooms[socket.gdomain][socket.groom]['photo'], 
                "status": "online", 
                "previous_page": guest_rooms[socket.gdomain][socket.groom]['previous_page'], 
                "advertisement": advertisement, 
                "actual_page": guest_rooms[socket.gdomain][socket.groom]['actual_page'],
                "visits": guest_rooms[socket.gdomain][socket.groom]['visits'],
            });// инфа в комнату
            try{
                const connection = await mysql.createConnection(connection_config);
                let sql = `
                    SELECT 
                        assistents.name AS user, 
                        messages_with_users_guests.message, 
                        SendTime as time, 
                        assistents.photo, 
                        messages_with_users_guests.sender, 
                        messages_with_users_guests.adds AS message_adds,
                        offline_forms.name AS form_name,
                        offline_forms.email AS form_email,
                        offline_forms.message AS form_message,
                        offline_forms.phone AS form_phone,
                        notifications.text as notification_text,
                        notifications.departament as notification_departament,
                        notifications.type as notification_type,
                        notifications.adds as notification_adds,
                        notifications.name as notification_name,
                        notifications.photo as notification_photo
                    FROM messages_with_users_guests  
                    LEFT JOIN assistents ON 
                        (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id ))
                    LEFT JOIN offline_forms ON
                        (messages_with_users_guests.room = offline_forms.sender AND messages_with_users_guests.message = offline_forms.uid)
                    LEFT JOIN notifications ON
                        (messages_with_users_guests.message = notifications.uid AND messages_with_users_guests.sender = 'notification' AND notifications.type != 'JavaScript')  
                    WHERE (
                            messages_with_users_guests.domain = '${socket.gdomain}' AND messages_with_users_guests.room = (SELECT id FROM rooms WHERE room = '${socket.groom}') AND 
                        (messages_with_users_guests.visibility IS NULL OR messages_with_users_guests.visibility = '')
                    ) 
                    ORDER BY messages_with_users_guests.id ASC
                `;
                const [rows, fields] = await connection.execute(sql);
                socket.emit('get_previous_messages', (rows));
                connection.end();
            } catch(err){ syslog(`Ошибка (get ftime prev messages) ${err}`, 'error'); }
            socket.emit('personal_info', {'visists': guest_rooms[socket.gdomain][socket.groom]['visits'], 'session_start': guest_rooms[socket.gdomain][socket.groom]['session_start'],  'session_time': guest_rooms[socket.gdomain][socket.groom]['session_time']});
            // замена и уведомления
            sql = `SELECT settings FROM users WHERE id = '${socket.gdomain}'`;
            try{
                const connection = await mysql.createConnection(connection_config);
                const [settings_row] = await connection.execute(sql);
                let settings;
                sql = `SELECT departament, name, photo, notification_name, text, conditions, type, adds, uid FROM notifications WHERE owner_id = '${socket.gdomain}'`;
                const [notifications] = await connection.execute(sql);
                if(settings_row[0]) settings = JSON.parse(settings_row[0]['settings']);
                if(Object.keys(settings?.['swap']||{}).length > 0) socket.emit('swap', settings['swap']);
                socket.emit('notifications', notifications);
                connection.end();
            } catch(err){ syslog(`Ошибка (phone swap) ${err}`, 'error'); }
        } else if(access){ 
            syslog(`Нет в бд ${domain}`, 'error');
            socket.disconnect();
        }
    });
    socket.on("conditions_complete", async (data) => {
        let sql = `SELECT settings FROM users WHERE id = '${socket.gdomain}'`;
        try{
            const connection = await mysql.createConnection(connection_config);
            const [settings_row] = await connection.execute(sql);
            let settings;
            if(settings_row[0]) settings = JSON.parse(settings_row[0]['settings']);
            if(!data.type) return;
            settings = add_event(settings, data.type, data.swap_id, data.autoUTM);
            settings = JSON.stringify(settings);
            sql = `UPDATE users SET settings = '${settings}' WHERE id = '${socket.gdomain}'`;
            await connection.execute(sql);
            connection.end();
        } catch(err){ syslog(`Ошибка (phone_swap) ${err}`, 'error'); }
    });
    socket.on("check_offline_status", async () => { await check_assistents_status(socket.gdomain, 'personal');  }); // проверка статуса домена
    socket.on('get-guest-messages', async () => { // посетитель получает старые сообщения
        if(socket.groom && socket.gdomain){
            try{
                const connection = await mysql.createConnection(connection_config);
                let sql = `
                    SELECT 
                        assistents.name AS user, 
                        messages_with_users_guests.message, 
                        SendTime as time, 
                        assistents.photo, 
                        messages_with_users_guests.sender, 
                        messages_with_users_guests.adds AS message_adds,
                        offline_forms.name AS form_name,
                        offline_forms.email AS form_email,
                        offline_forms.message AS form_message,
                        offline_forms.phone AS form_phone,
                        notifications.text as notification_text,
                        notifications.departament as notification_departament,
                        notifications.type as notification_type,
                        notifications.adds as notification_adds,
                        notifications.name as notification_name,
                        notifications.photo as notification_photo
                    FROM messages_with_users_guests  
                    LEFT JOIN assistents ON 
                        (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id ))
                    LEFT JOIN offline_forms ON
                        (messages_with_users_guests.room = offline_forms.sender AND messages_with_users_guests.message = offline_forms.uid)
                    LEFT JOIN notifications ON
                        (messages_with_users_guests.message = notifications.uid AND messages_with_users_guests.sender = 'notification' AND notifications.type != 'JavaScript')  
                    WHERE (
                         messages_with_users_guests.domain = '${socket.gdomain}' AND messages_with_users_guests.room = (SELECT id FROM rooms WHERE room = '${socket.groom}') AND 
                        (messages_with_users_guests.visibility IS NULL OR messages_with_users_guests.visibility = '')
                    ) 
                    ORDER BY messages_with_users_guests.id ASC
                `;
                const [rows, fields] = await connection.execute(sql);
                socket.emit('get_previous_messages', (rows));
                connection.end();
            } catch(err) { syslog(`Ошибка (g get msgs) ${err}`, 'error'); }
        } else{ socket.emit("page_reload"); }
    });
    socket.on("guest_print", (data) => { // отслеживаем что пишет юзер
        socket.to(socket.groom).emit("guest_print", {"text": data.text});
        if(guest_rooms?.[socket.gdomain]?.[socket.groom]){
            guest_rooms[socket.gdomain][socket.groom]["typing"] = data.text;
            socket.broadcast.to(String(socket.gdomain)).emit('userlist_update', ({"type": "typing", "value": data.text, "target": socket.groom, "option": null}));
        }
    });
    socket.on('add_from_cards', async (data) => { // добавить из карточки
        if(!data.table || !bosses?.[socket.adomain] || !guest_rooms?.[socket.adomain]) return; 
        if(!data.index) data.index = socket.aroom;
        if(!guest_rooms?.[socket.adomain]?.[data.index]) return; 
        if(!crm_items.hasOwnProperty(socket.adomain)) crm_items[socket.adomain] = {};
        if(!crm_items[socket.adomain].hasOwnProperty(data.table)) return;
        let access = false;
        if(parseInt(tariffs[bosses[socket.adomain]["tariff"]]["include"]["crm_items"]["value"]) == 0) access = true;
        if(!access && Object.keys(crm_items[socket.adomain]).reduce((el, index) => {return Object.keys(index).length + Object.keys(el).length}) < parseInt(tariffs[bosses[socket.adomain]["tariff"]]["include"]["crm_items"]["value"])) access = true;
        if(access){
            statistic('add_fromcards', guest_rooms[socket.adomain][data.index]['domains_list']['domains']);
            let id = data.index;
            try{
                let sql = `SELECT count(1) FROM crm_items WHERE uid = '${id}'`;
                const connection = await mysql.createConnection(connection_config);
                const [LcountRows, LcountFields] = await connection.execute(sql);
                if(parseInt(LcountRows[0]['count(1)']) == 0){
                    let letter = `Посетитель, ${getTime()}`;
                    sql = return_sql('visitor', {
                        "id": 0,
                        "room": data.index,
                        "lastActivityTime": guest_rooms[socket.adomain][data.index]['lastActivityTime'],
                        "domains_list": JSON.stringify(guest_rooms[socket.adomain][data.index]["domains_list"]),
                        "served_list": JSON.stringify(guest_rooms[socket.adomain][data.index]["served_list"]),
                        "serving_list": JSON.stringify(guest_rooms[socket.adomain][data.index]["serving_list"]),
                        "info": JSON.stringify(guest_rooms[socket.adomain][data.index]["info"]),
                        "session_time": guest_rooms[socket.adomain][data.index]["session_time"],
                        "photo": guest_rooms[socket.adomain][data.index]["photo"],
                        "sessions": "",
                        'notes':guest_rooms[socket.adomain][data.index]["notes"],
                        'properties':guest_rooms[socket.adomain][data.index]["properties"],
                        "visits": guest_rooms[socket.adomain][data.index]["visits"]
                    });
                    await connection.execute(sql);
                    sql = `UPDATE rooms SET crm = '${data.table}' WHERE room = '${data.index}'`;
                    await connection.execute(sql);
                    crm_items[socket.adomain][data.table][id] = {'helper_photo': 'user.png', 'helper_name': 'новый', 'helper_info': letter};
                    sql = `INSERT INTO crm_items (id, owner_id, info, uid, item_table) VALUES (0, '${socket.adomain}', '${JSON.stringify(crm_items[socket.adomain][data.table][data.index])}','${data.index}', '${data.table}')`;
                    io.to(String(socket.adomain)).emit('add_client', {'info': crm_items[socket.adomain][data.table][id], 'index': id});
                    guest_rooms[socket.adomain][data.index].crm = data.table;
                    socket.emit('success_msg', {"text": "Посетитель добавлен в срм !"});
                    await connection.execute(sql);
                } else socket.emit('error_msg', {"text": "Посетитель уже существует в CRM!"});
                connection.end();
            } catch (err) { syslog(`Ошибка (crm add) ${err}`, 'error'); }
        } else socket.emit('error_msg', {"text": "Лимит превышен !"});
    });
    socket.on('unexits_columns', async (data) => { // лишние колонки у клиентов
        if(data.users && data.table){
            try{
                let table = data.table;
                const connection = await mysql.createConnection(connection_config);
                for(key in data.users){ 
                    sql = `SELECT info FROM crm_items WHERE uid = '${key}' and owner_id ='${socket.adomain}'`;
                    const [rows, fields] = await connection.execute(sql);
                    let client_rows = JSON.parse(rows[0]["info"]);
                    for(unexist_column in data.users[key]){ 
                        if(crm_items?.[socket.adomain]?.[table]?.[key]?.[unexist_column]) delete crm_items[socket.adomain][table][key][unexist_column];
                        delete client_rows[unexist_column]; 
                    }
                    client_rows = JSON.stringify(client_rows);
                    sql = `UPDATE crm_items SET info = '${client_rows}' WHERE uid = '${key}' and owner_id = '${socket.adomain}'`;
                    await connection.execute(sql);
                }
                connection.end();
            } catch(err) { syslog(`Ошибка (unexist columns) ${err}`, 'error'); }
        }
    });
    socket.on('save_notification', async (data) => {
        if(!socket.gdomain || !socket.groom || !guest_rooms?.[socket.gdomain]?.[socket.groom]) return;
        var sql = return_sql('visitor', {
            "id": 0,
            "room": socket.groom,
            "lastActivityTime": guest_rooms[socket.gdomain][socket.groom]['lastActivityTime'],
            "domains_list": JSON.stringify(guest_rooms[socket.gdomain][socket.groom]["domains_list"]),
            "served_list": JSON.stringify(guest_rooms[socket.gdomain][socket.groom]["served_list"]),
            "serving_list": JSON.stringify(guest_rooms[socket.gdomain][socket.groom]["serving_list"]),
            "info": JSON.stringify(guest_rooms[socket.gdomain][socket.groom]["info"]),
            "session_time": guest_rooms[socket.gdomain][socket.groom]["session_time"],
            "photo": guest_rooms[socket.gdomain][socket.groom]["photo"],
            "sessions": "",
            'notes':guest_rooms[socket.gdomain][socket.groom]["notes"],
            'properties':guest_rooms[socket.gdomain][socket.groom]["properties"],
            "visits": guest_rooms[socket.gdomain][socket.groom]["visits"],
        });
        try{
            const connection = await mysql.createConnection(connection_config);
            await connection.execute(sql);
            let sql = `SELECT * FROM notifications WHERE uid = '${data}' && owner_id = '${socket.gdomain}'`;
            const [notifications] = await connection.execute(sql);
            for(i in notifications){
                let notification = notifications[i];
                sql = `
                    INSERT INTO messages_with_users_guests (
                        id, 
                        sender, 
                        message, 
                        SendTime, 
                        domain, 
                        room, 
                        visibility
                    ) VALUES (
                        0, 
                        'notification', 
                        '${notification.uid}', 
                        '${getTime()}',
                        '${socket.gdomain}', 
                        (SELECT id FROM rooms WHERE room = '${socket.groom}'),
                        '${notification.type == "JavaScript" ? 'invisible' : ''}'
                    )`;
                await connection.execute(sql);
            }
        }catch(err){ syslog(`Ошибка (save notification) ${err}`, 'error'); }
    });
    socket.on('notification_statistic', async(data) => {
        if(!socket.gdomain || !socket.groom || !guest_rooms?.[socket.gdomain]?.[socket.groom]) return;
        let today = new Date();
        let year = today.getFullYear();
        let month = today.getMonth() + 1;
        if(month < 10) month = '0' + month;
        let day = today.getDate();
        if(day < 10) day = '0' + day;
        try{
            const connection = await mysql.createConnection(connection_config);
            let sql = `SELECT statistic FROM notifications WHERE uid = '${data.uid}' && owner_id = '${socket.gdomain}'`;
            const [statistic] = await connection.execute(sql);
            if((statistic||[]).length > 0){
                let notification_statistic = JSON.parse(statistic[0]['statistic']);
                if(!notification_statistic['statistic'][year]) notification_statistic['statistic'][year] = {};
                if(!notification_statistic['statistic'][year][month]) notification_statistic['statistic'][year][month] = {};
                if(!notification_statistic['statistic'][year][month][day]) notification_statistic['statistic'][year][month][day] = {};
                if(!notification_statistic['statistic'][year][month][day]['send_count']) notification_statistic['statistic'][year][month][day]['send_count'] = 0;
                notification_statistic['statistic'][year][month][day]['send_count']++;
                sql = `UPDATE notifications SET statistic = '${JSON.stringify(notification_statistic)}' WHERE uid = '${data.uid}' && owner_id = '${socket.gdomain}'`;
                await connection.execute(sql);
                connection.end();
            }
        }catch(err){ syslog(`Ошибка (statistic notification) ${err}`, 'error'); }
    });
    function check_assistents_status(domain, type){ // проверка статуса ассистентов
        for(key in assistents[domain]){
            if(assistents[domain][key]["status"] == "online"){
                if(type == 'personal') socket.emit('delete_form');
                return false;
            } 
        }
        if(type == 'personal') socket.emit('offline-form');
        else if(type == 'public') io.to("guest_"+domain).emit('offline-form'); 
        return true;
    }
    async function statistic(type, hosts){ // статистика
        let domain = socket.gdomain||socket.adomain||socket.bdomain;
        if(!domain) return;
        try{
            const connection = await mysql.createConnection(connection_config);
            sql = `SELECT info FROM statistic WHERE owner_id = '${domain}'`;
            let [rows, fields] = await connection.execute(sql);
            let statistic = JSON.parse(rows[0]["info"]);
            let today = new Date();
            let today_date = today.getDate();
            if(parseInt(today_date) < 10) today_date = '0' + today_date;
            let today_mounth = today.getMonth() + 1;
            if(parseInt(today_mounth) < 10) today_mounth = '0' + today_mounth;
            let today_year = today.getFullYear();
            if(!statistic["statistic"].hasOwnProperty(today_year)) statistic["statistic"][today_year] = {};
            if(!statistic["statistic"][today_year].hasOwnProperty(today_mounth)) statistic["statistic"][today_year][today_mounth] = {};
            if(!statistic["statistic"][today_year][today_mounth].hasOwnProperty(today_date)) statistic["statistic"][today_year][today_mounth][today_date] = {};
            if(!statistic["statistic"][today_year][today_mounth][today_date].hasOwnProperty(type)) statistic["statistic"][today_year][today_mounth][today_date][type] = 1;  
            else statistic["statistic"][today_year][today_mounth][today_date][type]++;
            if(hosts){
                for(host_key in hosts){
                    let host = hosts[host_key];
                    if(!host) continue;
                    if(!statistic.hasOwnProperty(host)) statistic[host] = {};
                    if(!statistic[host].hasOwnProperty(today_year)) statistic[host][today_year] = {};
                    if(!statistic[host][today_year].hasOwnProperty(today_mounth)) statistic[host][today_year][today_mounth] = {};
                    if(!statistic[host][today_year][today_mounth].hasOwnProperty(today_date)) statistic[host][today_year][today_mounth][today_date] = {};
                    if(!statistic[host][today_year][today_mounth][today_date].hasOwnProperty(type)) statistic[host][today_year][today_mounth][today_date][type] = 1;  
                    else statistic[host][today_year][today_mounth][today_date][type]++;
                }
            }
            statistic = JSON.stringify(statistic);
            sql = `UPDATE statistic SET info = '${statistic}' WHERE owner_id = '${domain}'`;
            await connection.execute(sql);
            connection.end();
        } catch(err) { syslog(`Ошибка (statistic) ${err}`, 'error'); }
    }
    async function UTM_statistic(url, hosts){ // статистика
        if(url.indexOf('?') == -1) return;
        let parameters = params(url.split('?')[1]);
        let utm_source = parameters['utm_source'];
        let utm_medium = parameters['utm_medium'];
        let utm_campaign = parameters['utm_campaign'];
        let utm_content = parameters['utm_content'];
        let utm_term = parameters['utm_term'];
        let domain = socket.gdomain||socket.adomain||socket.bdomain;
        if(!domain) return;
        try{
            const connection = await mysql.createConnection(connection_config);
            sql = `SELECT utm FROM statistic WHERE owner_id = '${domain}'`;
            let [rows, fields] = await connection.execute(sql);
            let statistic = JSON.parse(rows[0]["utm"]);
            let today = new Date();
            let today_date = today.getDate();
            if(parseInt(today_date) < 10) today_date = '0' + today_date;
            let today_mounth = today.getMonth() + 1;
            if(parseInt(today_mounth) < 10) today_mounth = '0' + today_mounth;
            let today_year = today.getFullYear();
            if(!statistic["statistic"].hasOwnProperty(today_year)) statistic["statistic"][today_year] = {};
            if(!statistic["statistic"][today_year].hasOwnProperty(today_mounth)) statistic["statistic"][today_year][today_mounth] = {};
            if(!statistic["statistic"][today_year][today_mounth].hasOwnProperty(today_date)) statistic["statistic"][today_year][today_mounth][today_date] = {};
            if(!statistic["statistic"][today_year][today_mounth][today_date].hasOwnProperty(utm_source)) statistic["statistic"][today_year][today_mounth][today_date][utm_source] = {};  
            if(utm_medium) statistic["statistic"][today_year][today_mounth][today_date][utm_source][utm_medium] = {};
            if(utm_medium && utm_campaign) statistic["statistic"][today_year][today_mounth][today_date][utm_source][utm_medium][utm_campaign] = {};
            if(utm_medium && utm_campaign && utm_content) statistic["statistic"][today_year][today_mounth][today_date][utm_source][utm_medium][utm_campaign][utm_content] = [];
            if(utm_medium && utm_campaign && utm_content && utm_term) statistic["statistic"][today_year][today_mounth][today_date][utm_source][utm_medium][utm_campaign][utm_content].push(utm_term);
            if(hosts){
                for(host_key in hosts){
                    let host = hosts[host_key];
                    if(!host) continue;
                    if(!statistic.hasOwnProperty(host)) statistic[host] = {};
                    if(!statistic[host].hasOwnProperty(today_year)) statistic[host][today_year] = {};
                    if(!statistic[host][today_year].hasOwnProperty(today_mounth)) statistic[host][today_year][today_mounth] = {};
                    if(!statistic[host][today_year][today_mounth].hasOwnProperty(today_date)) statistic[host][today_year][today_mounth][today_date] = {};
                    if(!statistic[host][today_year][today_mounth][today_date].hasOwnProperty(utm_source)) statistic[host][today_year][today_mounth][today_date][utm_source] = {};  
                    if(utm_medium) statistic[host][today_year][today_mounth][today_date][utm_source][utm_medium] = {};
                    if(utm_medium && utm_campaign) statistic[host][today_year][today_mounth][today_date][utm_source][utm_medium][utm_campaign] = {};
                    if(utm_medium && utm_campaign && utm_content) statistic[host][today_year][today_mounth][today_date][utm_source][utm_medium][utm_campaign][utm_content] = [];
                    if(utm_medium && utm_campaign && utm_content && utm_term) statistic[host][today_year][today_mounth][today_date][utm_source][utm_medium][utm_campaign][utm_content].push(utm_term);
                }
            }
            statistic = JSON.stringify(statistic);
            sql = `UPDATE statistic SET utm = '${statistic}' WHERE owner_id = '${domain}'`;
            await connection.execute(sql);
            connection.end();
        } catch(err) { syslog(`Ошибка (utm_statistic) ${err}`, 'error'); }
    }
    function check_adds(prev_url, real_url, socket){ //проверка рекламы
        let status = false;
        let adds_type = '';
        if(prev_url){
            if(
                prev_url.indexOf('yabs.yandex.ru') != -1 ||
                prev_url.indexOf('webntp.yandex.ru') != -1
            ){
                status = true;
                adds_type = 'yandex реклама';
                socket.adds_status ='yandex';
            }
            if(prev_url.indexOf('facebook.com') != -1){
                status = true;
                adds_type = 'facebook реклама';
                socket.adds_status ='facebook';
            }
        }
        if(real_url){
            if(
                real_url.indexOf('yclid') != -1 ||
                real_url.indexOf('phrase') != -1 ||
                real_url.indexOf('ya.direct') != -1 ||
                (real_url.indexOf('campaign') != -1 && real_url.indexOf('utm_campaign') == -1)
            ){
                status = true;
                adds_type = 'yandex реклама';
                socket.adds_status ='yandex';
            }
            if (real_url.indexOf('utm_source') != -1 && real_url.indexOf('utm_medium') != -1){
                socket.utm_url = real_url;
                status = true;
                let add = real_url.split('utm_source=')[1].split('&')[0];
                adds_type = add + ' реклама';
                if(adds_company.hasOwnProperty(add)) socket.adds_status = add;
                else {
                    syslog(`Новая рекламная компания - ${add}`, 'strange');
                    socket.adds_status = 'other_adds';
                }
            }
        }
        return {'status': status, 'adds_type': adds_type};
    }
}); 
}
function params(search){
    return JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) });
}
async function statistic_2(type, domain, hosts){ // статистика
    if(!domain) return;
    try{
        const connection = await mysql.createConnection(connection_config);
        sql = `SELECT info FROM statistic WHERE owner_id = '${domain}'`;
        let [rows, fields] = await connection.execute(sql);
        let statistic = JSON.parse(rows[0]["info"]);
        let today = new Date();
        let today_date = today.getDate();
        if(parseInt(today_date) < 10) today_date = '0' + today_date;
        let today_mounth = today.getMonth() + 1;
        if(parseInt(today_mounth) < 10) today_mounth = '0' + today_mounth;
        let today_year = today.getFullYear();
        if(!statistic["statistic"].hasOwnProperty(today_year)) statistic["statistic"][today_year] = {};
        if(!statistic["statistic"][today_year].hasOwnProperty(today_mounth)) statistic["statistic"][today_year][today_mounth] = {};
        if(!statistic["statistic"][today_year][today_mounth].hasOwnProperty(today_date)) statistic["statistic"][today_year][today_mounth][today_date] = {};
        if(!statistic["statistic"][today_year][today_mounth][today_date].hasOwnProperty(type)) statistic["statistic"][today_year][today_mounth][today_date][type] = 1;  
        else statistic["statistic"][today_year][today_mounth][today_date][type]++;
        if(hosts){
            for(host_key in hosts){
                let host = hosts[host_key];
                if(!host) continue;
                if(!statistic.hasOwnProperty(host)) statistic[host] = {};
                if(!statistic[host].hasOwnProperty(today_year)) statistic[host][today_year] = {};
                if(!statistic[host][today_year].hasOwnProperty(today_mounth)) statistic[host][today_year][today_mounth] = {};
                if(!statistic[host][today_year][today_mounth].hasOwnProperty(today_date)) statistic[host][today_year][today_mounth][today_date] = {};
                if(!statistic[host][today_year][today_mounth][today_date].hasOwnProperty(type)) statistic[host][today_year][today_mounth][today_date][type] = 1;  
                else statistic[host][today_year][today_mounth][today_date][type]++;
            }
        }
        statistic = JSON.stringify(statistic);
        sql = `UPDATE statistic SET info = '${statistic}' WHERE owner_id = '${domain}'`;
        await connection.execute(sql);
        connection.end();
    } catch(err) { syslog(`Ошибка (statistic) ${err}`, 'error'); }
}
function getTime(){ // время
    let d = new Date();
    let seconds = d.getSeconds();
    if(seconds < 10) seconds = '0' + seconds;
    let minutes = d.getMinutes();
    if(minutes < 10) minutes = '0' + minutes;
    let hours = d.getHours();
    if(hours < 10) hours = '0' + hours;
    let days = d.getDate();
    if(days < 10) days = '0' + days;
    let months = d.getMonth() + 1;
    if(months < 10) months = '0' + months;
    let strDate = d.getFullYear() + "-" + months + "-" + days + " " + hours + ":" + minutes + ":" + seconds;
    return strDate;
}
function getStringFromTime(type, date){
    let result;
    date_days = date.getDate();
    if(date_days < 10) date_days = '0' + date_days;
    let date_mounths = date.getMonth() + 1; 
    if(date_mounths < 10) date_mounths = '0' + date_mounths;
    let date_years = date.getFullYear();
    if(type == 'date'){
        result = date_years + '-' + date_mounths + '-' + date_days;
    } else if(type == 'dateTimeS'){
        let date_seconds = date.getSeconds();
        if(date_seconds < 10) date_seconds = '0' + date_seconds;
        let date_minutes = date.getMinutes();
        if(date_minutes < 10) date_minutes = '0' + date_minutes;
        let date_hours = date.getHours();
        if(date_hours < 10) date_hours = '0' + date_hours;
        result = date_years + '-' + date_mounths + '-' + date_days + ' ' + date_hours + ':' + date_minutes + ':' + date_seconds;
    } else if(type == 'dateTime'){
        let date_minutes = date.getMinutes();
        if(date_minutes < 10) date_minutes = '0' + date_minutes;
        let date_hours = date.getHours();
        if(date_hours < 10) date_hours = '0' + date_hours;
        result = date_years + '-' + date_mounths + '-' + date_days + ' ' + date_hours + ':' + date_minutes;
    }
    return result;
}
clearInfo(); setInterval(clearInfo, 1000 * 60 * 60 * 24); // чистка базы данных
async function clearInfo(){ 
	let seven_days_ago = new Date;
    seven_days_ago.setDate(seven_days_ago.getDate() - 7);
    seven_days_ago = getStringFromTime('dateTime', seven_days_ago)
    let mounth_ago = new Date;
    mounth_ago.setMonth(mounth_ago.getMonth() - 1);
    mounth_ago = getStringFromTime('dateTime', mounth_ago)
    let year_ago = new Date;
    year_ago.setFullYear(year_ago.getFullYear() - 1);
    year_ago = getStringFromTime('dateTime', year_ago)
    syslog(`Процесс чистки запущен - ${seven_days_ago} ${mounth_ago} ${year_ago}`, 'func');
    var sql = `
        DELETE FROM messages_with_users_guests WHERE cast(SendTime AS DATE) <= cast('${year_ago}' AS DATE) OR 
        (
            (SELECT count(1) FROM rooms WHERE rooms.id = messages_with_users_guests.room) = 0 AND 
            (SELECT count(1) FROM banned WHERE banned.id = messages_with_users_guests.room) = 0
        )
    `;
    try{
        const connection = await mysql.createConnection(connection_config);
        await connection.execute(sql);
        var sql = `DELETE FROM assistents_chat_messages WHERE cast(SendTime AS DATE) <= cast('${year_ago}' AS DATE)`;
        await connection.execute(sql);
        var sql = `DELETE FROM offline_forms WHERE (SELECT count(1) FROM rooms WHERE rooms.id = offline_forms.sender) = 0 AND (SELECT count(1) FROM banned WHERE banned.id = offline_forms.sender) = 0`;
        await connection.execute(sql);
        var sql = `DELETE FROM unconfimed_users WHERE cast(time AS DATE) <= cast('${seven_days_ago}' AS DATE)`;
        await connection.execute(sql);
        var sql = `DELETE FROM unconfimed_assistents WHERE cast(time AS DATE) <= cast('${seven_days_ago}' AS DATE)`;
        await connection.execute(sql);
        var sql = `DELETE FROM rooms WHERE (cast(time AS DATE) <= cast('${mounth_ago}' AS DATE) and hide = 'deleted')`;
        await connection.execute(sql);
        var sql = `DELETE FROM password_reset_keys WHERE cast(time AS DATE) <= cast('${seven_days_ago}' AS DATE)`;
        await connection.execute(sql);
        var sql = `DELETE FROM assistents_password_reset_keys WHERE cast(time AS DATE) <= cast('${seven_days_ago}' AS DATE)`;
        await connection.execute(sql);
        var sql = `DELETE FROM adds_visitors WHERE cast(time AS DATE) <= cast('${mounth_ago}' AS DATE)`;
        await connection.execute(sql);
        fs.readdirSync(backup_folder).forEach(file => {
           let file_date = file.split('_')[1].split('.')[0];
           if(new Date(file_date) < new Date(seven_days_ago)) {
               fs.unlink(backup_folder+file, err => { if(err) syslog(`Ошибка (чистки r) ${err}`, 'error'); });
               syslog(`Бэкап удалён ${file}`, 'func');
            }
        });
        connection.end();
    } catch(err) { syslog(`Ошибка (чистки) ${err}`, 'error'); }
}
async function payday(){
    let mounth_ago = new Date(); 
    mounth_ago.setMonth(mounth_ago.getMonth() - 1);
    mounth_ago = getStringFromTime('date', mounth_ago);
    let today = new Date(); 
    today =  getStringFromTime('date', today);
    syslog(`Процесс оплаты запущен ! Дата покупки - ${mounth_ago}`, 'func');
    let sql = `SELECT money, id, tariff FROM users WHERE (payday = 0 && money >= 0) or cast(payday AS DATE) <= cast('${mounth_ago}' AS DATE)`;
    try{
        let do_not_pay = [];
        const connection = await mysql.createConnection(connection_config);
        const [Urows, Ufields] = await connection.execute(sql);
        for(key in Urows){ 
            let money = Urows[key]["money"];
            let tariff = Urows[key]["tariff"];
            let id =  Urows[key]["id"];
            let status = await user_pay(id, money, tariff, connection, today);
            if(status) syslog(`Пользователь ${id} оплатил ещё один месяц ${tariff}! Баланс - ${money}`, 'success');
            else {
                do_not_pay.push(id);
                syslog(`Пользователь ${id} не смог оплатить ещё один месяц ${tariff}! Баланс - ${money}`, 'error');
            }
        }
        connection.end();
        if(do_not_pay.length > 0){
            $.post( "https://interhelper.ru/engine/adminSettings", { login: login, password: password, info: JSON.stringify(do_not_pay), type: 'pay_notification' }).done((data) => {
                data = JSON.parse(data);
                if(data['errors'].length > 0){ syslog("Ошибка отправки напоминаний об оплате " + data['errors'].join('/'), 'error'); return; }
                syslog("Отправка напоминаний об оплате совершена: " + data.success.send_to, 'func');  
                if(data.success.log) console.log(data.success.log);  
            });
        }
    } catch(err) { syslog(`Ошибка (payday) ${err}`, 'error'); }
}
function diffDates(day_one, day_two) { return (day_one - day_two) / (60 * 60 * 24 * 1000); }; // вычитание дат
function CompareTime(time1, time2){ // сравнить время
    if(Date.parse(time1) > Date.parse(time2)) return true;
    return false;                        
}
async function FillUsersMas() { //данные по посетителям
    try{
        const connection = await mysql.createConnection(connection_config);
        // боссы
        let sql = `SELECT tariff, id FROM users`;
        const [BSrows, BSfields] = await connection.execute(sql);
        for(key in BSrows){
            let tariff = BSrows[key]['tariff'];
            let id = BSrows[key]['id'];
            bosses[id] = {"tariff": tariff};
            assistents[id] = {};
            guest_rooms[id] = {};
            rooms[id] = {};
            banned[id]= {};
            crm_items[id] = {};
            tasks[id]= {};
            uvisitors[id]= [];
            assistents[id]["public_room"] = {
                "assistents_in": [], 
                "assistent_chat_messages": {} 
            };
        }
        // данные по ассистентам из бд
        sql = `SELECT * FROM assistents`;
        const [Arows, Afields] = await connection.execute(sql);
        for(key in Arows){
            let boss_id = Arows[key]['domain'];
            let assistent_email = Arows[key]['email'];
            let assistent_id = Arows[key]["id"];
            if(!assistents?.[boss_id]) continue;
            if(!assistents?.[boss_id]?.[assistent_id]) assistents[boss_id][assistent_id] = {
                "assistent_chat_messages": {},
                "personal_consulation_messages": {},
                "id": assistent_id, 
                "status":"offline", 
                "hab": assistent_email, 
                "departament": Arows[key]["departament"], 
                "photo": Arows[key]["photo"], 
                "name": Arows[key]["name"], 
                "buttlecry": Arows[key]["buttlecry"], 
                "connections": 0, 
                "prev_email": assistent_email, 
                'time': Arows[key]["time"],
                "ban": Arows[key]["ban"]||null,
            };
        }
        // данные по посетителям из бд
        sql = `
            SELECT 
                room, 
                time, 
                domains_list,
                notes,
                properties,
                served_list,
                serving_list,
                info,
                hide,
                photo,
                session_time,
                visits,
                crm,
                (SELECT count(1) FROM messages_with_users_guests WHERE messages_with_users_guests.room = rooms.id) AS messages
            FROM rooms
        `;
        const [Grows, Gfields] = await connection.execute(sql);
        for(key in Grows){
            let boss_id = Grows[key]['room'].split("!@!@2@!@!")[0];
            let room_uid = Grows[key]['room'];
            if(!guest_rooms?.[boss_id]) continue;
            if(!guest_rooms?.[boss_id]?.[room_uid]) guest_rooms[boss_id][room_uid] = {
                "served_list": JSON.parse(Grows[key]['served_list']),
                "serving_list": JSON.parse(Grows[key]['serving_list']),
                "domains_list": JSON.parse(Grows[key]['domains_list']),
                "status": "offline", 
                "new_message": {
                    "message": null, 
                    "status": "readed",
                    "message_adds": null,
                }, 
                "notes": JSON.parse(Grows[key]['notes']), 
                "properties": JSON.parse(Grows[key]['properties']), 
                "messages_exist": (Grows[key]['messages'] > 0 ? true : false), 
                "actual_page": null, 
                "previous_page": null, 
                "typing": "", 
                "hide": (Grows[key]["hide"] ? true : false), 
                "connections": 0, 
                "lastActivityTime": Grows[key]['time'], 
                "info": JSON.parse(Grows[key]['info']),
                "sessions": Grows[key]["sessions"]||{},
                "movements": {
                    "clicks": [],
                    "max_scroll": 0,
                    "mouse_move": [],
                },
                "visits": parseInt(Grows[key]['visits'])||1,
                "session_time": parseInt(Grows[key]["session_time"])||0,
                "photo": JSON.parse(Grows[key]["photo"]),
                "crm": Grows[key]['crm'], 
            };
        }
        // данные по заблокированным посетителям из бд
        sql = `
            SELECT 
                room,
                served_list,
                domains_list,
                notes,
                properties,
                info,
                reason,
                BannedBy,
                room_id,
                time,
                visits,
                photo,
                crm,
                session_time,
                (SELECT count(1) FROM messages_with_users_guests WHERE messages_with_users_guests.room = banned.id) AS messages
            FROM banned
        `; 
        const [Brows, Bfields] = await connection.execute(sql);
        for(key in Brows){
            let boss_id = Brows[key]['room'].split("!@!@2@!@!")[0];
            let domains_list = JSON.parse(Brows[key]['domains_list']);
            let info = JSON.parse(Brows[key]['info']);
            let room_id = Brows[key]['room'];
            let served_list =  JSON.parse(Brows[key]['served_list']);
            if(!banned[boss_id]?.[room_id]) banned[boss_id][room_id] = {
                "domains_list":domains_list, 
                "served_list": served_list, 
                "status": "offline", 
                "new_message": {
                    "message": "", 
                    "status": "readed"
                }, 
                "session_time": Brows[key]['session_time'],
                "notes": JSON.parse(Brows[key]['notes']), 
                "properties": JSON.parse(Brows[key]['properties']), 
                "messages_exist": (Brows[key]['messages'] > 0 ? true : false), 
                "actual_page": null, 
                "previous_page": null, 
                "typing": "", 
                "hide": false, 
                "connections": 0, 
                "lastActivityTime": Brows[key]['time'], 
                "reason": Brows[key]['reason'], 
                "bannedBy": Brows[key]['BannedBy'], 
                "info": info, 
                "room_id": Brows[key]['room_id'],
                "visits": parseInt(Brows[key]['visits'])||1,
                "photo": JSON.parse(Brows[key]['photo']),
                "crm": Brows[key]['crm'],
            };
        }
        sql = `SELECT columns, owner_id FROM crm ORDER BY id ASC`; // данные по CRM 
        const [TCrows, TCfields] = await connection.execute(sql);
        for(key in TCrows){
            let tables = Object.keys(JSON.parse(TCrows[key]['columns']));
            let owner_id = TCrows[key]['owner_id'];
            if(!crm_items[owner_id]) crm_items[owner_id] = {};
            for(table in tables) crm_items[owner_id][tables[table]] = {};
        }
        sql = `SELECT owner_id, info, uid, item_table FROM crm_items ORDER BY id ASC`; // данные по CRM
        const [Lrows, Lfields] = await connection.execute(sql);
        for(key in Lrows){
            let owner_id = Lrows[key]['owner_id'];
            let info = JSON.parse(escapeRegExp(Lrows[key]['info']));
            let room_id = Lrows[key]['uid'];
            let table = Lrows[key]['item_table'];
            if(!crm_items.hasOwnProperty(owner_id)) crm_items[owner_id] = {};
            if(!crm_items[owner_id].hasOwnProperty(table)) crm_items[owner_id][table] = {};
            if(!crm_items[owner_id][table].hasOwnProperty(room_id)) crm_items[owner_id][table][room_id] = info;
        }
        // данные по задачам
        sql = `
            SELECT 
                owner_id, 
                type, 
                time, 
                selected, 
                text, 
                uid, 
                creator_id, 
                selected_group 
            FROM tasks 
            ORDER BY tasks.id ASC
        `; 
        const [Trows, Tfields] = await connection.execute(sql);
        for(key in Trows){
            let owner_id = Trows[key]['owner_id'];
            let uid = Trows[key]['uid'];
            let status;
            if(!CompareTime(Trows[key]['time'], getTime())) status = 'completed';
            else status = 'uncompleted';
            let info = {'table': Trows[key]['selected_group'], 'type': Trows[key]['type'], 'creator_id': Trows[key]['creator_id'], 'time': Trows[key]['time'], 'text': Trows[key]['text'], 'selected': JSON.parse(Trows[key]['selected'])['selected'], 'status': status }
            tasks[owner_id][uid] = info;
        }
        sql = `SELECT owner_id, ip FROM uvisitors`; // уникальные 
        const [UVrows, UVfields] = await connection.execute(sql);
        for(key in UVrows){
            let owner_id = UVrows[key]['owner_id'];
            let ip = UVrows[key]['ip'];
            if(!uvisitors.hasOwnProperty(owner_id)) uvisitors[owner_id] = [];
            if(uvisitors[owner_id].indexOf(ip) == -1) uvisitors[owner_id].push(ip);
        }
        sql = `SELECT owner_id, ip, count, time FROM adds_visitors`; // с рекламы 
        const [ADDSUrows, ADDSUfields] = await connection.execute(sql);
        for(key in ADDSUrows){
            let owner_id = ADDSUrows[key]['owner_id'];
            let ip = ADDSUrows[key]['ip'];
            let count = ADDSUrows[key]['count'];
            let time = ADDSUrows[key]['time'];
            if(!adds_visitors.hasOwnProperty(owner_id)) adds_visitors[owner_id] = {};
            if(!adds_visitors[owner_id].hasOwnProperty(ip)) adds_visitors[owner_id][ip] = {"time": time, "count": count};
        }
        sql = `SELECT tariff, name FROM tariffs`; // тарифы
        const [TRrows, TRfields] = await connection.execute(sql);
        for(key in TRrows){
            let tariff = TRrows[key]['tariff'];
            let name = TRrows[key]['name'];
            if(!tariffs.hasOwnProperty(name)) tariffs[name] = JSON.parse(tariff);
        }
        sql = `SELECT value, name FROM variables WHERE name = 'password' or name = 'login' or name = 'starter_tariff'`; // переменные
        const [VRrows, VRfields] = await connection.execute(sql);
        for(key in VRrows){
            let name = VRrows[key]['name'];
            let value = VRrows[key]['value'];
            if(name == 'login') login = value;
            if(name == 'password') password = value;
            if(name == 'starter_tariff') deffault_tariff = value;
        }
        connection.end();
    } catch(err) { syslog(`Ошибка (full mas) ${err}`, 'error'); }
    payday();
    socket_start();
}
setInterval(payday, 1000 * 60 * 60 * 24); // платежи
rl.on('line', async (input) => { // консоль
    input = input.toLowerCase();
    if(input == 'command-list') GetCommandList();
    else if(input == 'tariffs-list') console.log(Object.keys(tariffs));
    else if(input == 'kill') process.exit();
    else if(input == 'clear-logs') process.stdout.write('\033c');
    else if(input == 'save_js') obfuscator();
    else if(input.split(' ')[0] == 'timeout-kill'){
        let time = parseInt(input.split(' ')[1])||1;
        setTimeout('process.exit()', time);
    } else if(input.split(' ')[0] == 'remove-user'){
        let rid = parseInt(input.split(' ')[1]);
        await delete_user(rid);
    } else if(input.split(' ')[0] == 'set-money'){
        let spart = input.split(' ')[1].split('/');
        let money = parseInt(spart[1]);
        if(!money) money = 0;
        let boss = spart[0];
        set_money(money, boss);
    } else if(input == 'reload-users') io.emit('page_reload');
    else if(input.split(' ')[0] == 'check-mas'){
        let mas = input.split(' ')[1];
        if(mas == 'user') console.table(users);
        else if(mas == 'boss') console.table(bosses);
        else if(mas == 'assistent') console.table(assistents);
        else if(mas == 'unique') console.table(unique_visits);
        else if(mas == 'ban') console.table(banned);
        else if(mas == 'adds') console.table(adds_visitors);
        else if(mas == 'tokens') console.table(tokens);
        else if(mas == 'crm_items') console.table(crm_items);
        else if(mas == 'tasks') console.table(tasks);
        else if(mas == 'tariffs') console.table(tariffs);
        else console.log(getTime()+": Это не выполнится");
    } else console.log(getTime()+": Это не выполнится");

});
async function delete_user(rid){
    delete guest_rooms[rid];
    delete assistents[rid];
    delete rooms[rid];
    delete banned[rid];
    delete crm_items[rid];
    delete tasks[rid];
    delete uvisitors[rid];
    delete bosses[rid];
    try{
        const connection = await mysql.createConnection(connection_config);
        let sql = `DELETE FROM users WHERE id = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM crm WHERE owner_id = ${rid};`;  await connection.execute(sql);
        sql = `DELETE FROM crm_items WHERE owner_id = ${rid};`;  await connection.execute(sql);
        sql = `DELETE FROM messages_with_users_guests WHERE domain = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM rooms WHERE LEFT(room, ${String(rid).length}) = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM banned WHERE LEFT(room, ${String(rid).length}) = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM statistic WHERE owner_id = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM tasks WHERE owner_id = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM unconfimed_assistents WHERE domain = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM uvisitors WHERE owner_id = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM assistents WHERE domain = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM assistents_chat_messages WHERE domain = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM reviews WHERE review_id = ${rid};`; await connection.execute(sql);
        sql = `DELETE FROM notifications WHERE owner_id = ${rid};`; await connection.execute(sql);
        connection.end();
        syslog(`Юзер удалён`, 'func');
    } catch(err) { syslog(`Ошибка (удаление юзера) ${err}`, 'error'); }
}
async function set_money(money, boss){
    if(!bosses?.[boss]) return;
    try{
        const connection = await mysql.createConnection(connection_config);
        let sql = `UPDATE users SET money = ${money} WHERE id = ${boss};`; await connection.execute(sql);
        connection.end();
        syslog(`Деньги обновлены`, 'func');
    } catch(err) { syslog(`Ошибка (добавка денег) ${err}`, 'err');}
}
function GetCommandList(){ // список команд
    mas = [
        'command-list', 
        'kill', 
        'clear-logs', 
        'timeout-kill (Время *1min = 60000) ', 
        'reload-users', 
        'remove-user (id босса)', 
        'set-money (босс)/(Количество *1000 = 1000р)', 
        'change-tariff (босс)/(имя издания)', 
        'tariffs-list',
        'check-mas (user/boss/assistent/unique/ban/adds/tokens/crm_items/tasks/tariffs)',
        'save_js'
    ];
    for(key in mas) console.log('\u001b[1;35m'+getTime()+ '--> ' +mas[key]);
}
async function user_pay(id, money, tariff, connection, today){
    let access = today;
    if(tariffs[tariff]['cost']['value'] != '0' && money - parseInt(tariffs[tariff]['cost']['value']) < 0 || money < 0) access = 0;
    else money -= parseInt(tariffs[tariff]['cost']['value']);
    sql = `UPDATE users SET money = '${money}', payday = '${access}' WHERE id = '${id}'`;
    await connection.execute(sql);
    sql = `DELETE FROM uvisitors WHERE owner_id = '${id}'`;
    await connection.execute(sql);
    if(access != 0){ 
        bosses[id]['tariff'] = tariff;
        sql = `UPDATE users SET pay_notification = NULL WHERE id = '${id}'`;
        await connection.execute(sql);
        return true; 
    }
    return false;
}
function add_event(settings, event, swap_id, autoUTM){
    let today = new Date();
    let year = today.getFullYear();
    let month = today.getMonth() + 1;
    if(month < 10) month = '0' + month;
    let day = today.getDate();
    if(day < 10) day = '0' + day;
    if(!settings?.['swap']?.[swap_id]) return settings;
    if(!autoUTM){
        if(!settings['swap'][swap_id]["events"]) 
            settings['swap'][swap_id]["events"] = {};
        if(!settings['swap'][swap_id]["events"][year]) 
            settings['swap'][swap_id]["events"][year] = {};
        if(!settings['swap'][swap_id]["events"][year][month]) 
            settings['swap'][swap_id]["events"][year][month] = {};
        if(!settings['swap'][swap_id]["events"][year][month][day]) 
            settings['swap'][swap_id]["events"][year][month][day] = {};
        if(!settings['swap'][swap_id]["events"][year][month][day][event]) 
            settings['swap'][swap_id]["events"][year][month][day][event] = 0;
            settings['swap'][swap_id]["events"][year][month][day][event]++;
    } else {
        let part = autoUTM['part'];
        let key = autoUTM['utm'];
        if(Object.keys(settings['swap'][swap_id]["swap_utmparts"][part]["results"]||{}) > 100) return settings;
        if(!settings['swap'][swap_id]["swap_utmparts"][part]["results"]) 
            settings['swap'][swap_id]["swap_utmparts"][part]["results"] = {}; 
        if(!settings['swap'][swap_id]["swap_utmparts"][part]["results"][key]) 
            settings['swap'][swap_id]["swap_utmparts"][part]["results"][key] = {};
        if(!settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year]) 
            settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year] = {};
        if(!settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month]) 
            settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month] = {};
        if(!settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month][day]) 
            settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month][day] = {};
        if(!settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month][day][event]) 
            settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month][day][event] = 0;
            settings['swap'][swap_id]["swap_utmparts"][part]["results"][key][year][month][day][event]++;
    }
    return settings;
}
create_backup(); setInterval(create_backup, 1000 * 60 * 60 * 24); // бэкап
function create_backup(){
    let today = new Date();
    let year = today.getFullYear();
    let month = today.getMonth() + 1;
    let day = today.getDate();
    today = year + '-'+month + '-' + day;
    backup.backup('C:/hosting/interhelper.ru/', `C:/hosting/backup/website_${today}.backup`);
    backup.backup('Z:/server/', `C:/hosting/backup/server_${today}.backup`);
    // exec(`mysqldump -uinterhelper_ru -h192.168.1.75 -pGZJ3zyErXXfBBiiL interhelper_ru > C:/hosting/backup/database_${today}.sql`, {cwd: 'C:/Program Files/MariaDB 10.5/bin'}); 
    syslog(`DUMP`, 'func');
}
function return_sql(type, info){
    if(type == 'visitor') return `
        INSERT IGNORE INTO rooms
            (
                id, 
                room, 
                time, 
                domains_list, 
                notes, 
                properties, 
                served_list, 
                serving_list, 
                info,
                session_time,
                photo,
                visits
            ) SELECT 
                '${info.id}', 
                '${info.room}',
                '${info.lastActivityTime}',
                '${info.domains_list}',
                '${JSON.stringify(info.notes)}',
                '${JSON.stringify(info.properties)}',
                '${info.served_list}',
                '${info.serving_list}',
                '${info.info}',
                '${parseInt(info.session_time)||0}',
                '${JSON.stringify(info.photo)}',
                '${info.visits}'
        FROM dual WHERE NOT EXISTS (SELECT * FROM rooms WHERE room='${info.room}')
    `;
    if(type == 'banned') return `
        INSERT IGNORE INTO banned (
            id, 
            room, 
            time, 
            domains_list, 
            notes, 
            properties, 
            served_list, 
            reason, 
            bannedBy, 
            info, 
            room_id,
            photo,
            session_time,
            visits
        ) SELECT 
            ${info.id}, 
            '${info.ban_id}',
            '${getTime()}',
            '${info.domains_list}',
            '${info.notes}',
            '${info.properties}',
            '${info.served_list}',
            '${info.reason}',
            '${info.assistent_id}',
            '${info.info}',
            '${info.room}',
            '${JSON.stringify(info.photo)}',
            '${info.session_time}',
            '${info.visits}'
        FROM dual WHERE NOT EXISTS (SELECT * FROM banned WHERE room='${info.ban_id}')
    `;
    if(type == 'consultant_message') return `
        INSERT INTO messages_with_users_guests (
            id, 
            sender, 
            message, 
            SendTime,
            domain,
            room, 
            adds, 
            visibility
        ) VALUES (
            0, 
            '${info.id}', 
            '${info.message}', 
            '${info.time}',
            '${info.domain}', 
            (SELECT id FROM rooms WHERE room = '${info.room}'), '${info.adds||""}', 
            '${info.visibility}'
        )`;
                    
}
function syslog(message, type){
    let types = {
        "error":   "\u001b[1;31m",
        "success": "\u001b[1;32m",
        "func":    "\u001b[1;33m",
        "normal":  "\u001b[1;34m",
        "strange": "\u001b[1;35m",
        "asasasas": "\u001b[1;36m",
        "normal4": "\u001b[1;38m",
        "unnormal":"\u001b[1;30m",
    };
    console.log(types[type] + getTime() +': ' + message);
};
function escapeRegExp(string){ return string.replace(/[\\]/g, "\\$&"); }
function obfuscator(){
    fs.readFile("C:/hosting/interhelper.ru/www/HelperCode/f1239f1wjhkfjhu29834fh2h4389hf234f234/Helper.min.js", "utf8", (error,data) => {
        if(error){ syslog(error, 'error'); return; }
        let obfuscated = String(JavaScriptObfuscator.obfuscate(data));
        fs.writeFileSync("C:/hosting/interhelper.ru/www/HelperCode/f1239f1wjhkfjhu29834fh2h4389hf234f234/Helper.min.js", obfuscated);
    });
    fs.readFile("C:/hosting/interhelper.ru/www/scripts/hidden_scripts/minificated/admin_page.min.js", "utf8", (error,data) => {
        if(error){ syslog(error, 'error'); return; }
        let obfuscated = String(JavaScriptObfuscator.obfuscate(data));
        fs.writeFileSync("C:/hosting/interhelper.ru/www/scripts/hidden_scripts/minificated/admin_page.min.js", obfuscated);
    });
    fs.readFile("C:/hosting/interhelper.ru/www/scripts/hidden_scripts/minificated/main.min.js", "utf8", (error,data) => {
        if(error){ syslog(error, 'error'); return; }
        let obfuscated = String(JavaScriptObfuscator.obfuscate(data));
        fs.writeFileSync("C:/hosting/interhelper.ru/www/scripts/hidden_scripts/minificated/main.min.js", obfuscated);
    });
} 
function CSV(array) {
    var keys = Object.keys(array[0]);
    var result = keys.join("\t") + "\n";
    array.forEach((obj) => {
        keys.forEach((k, ix) => {
            if (ix) result += "\t";
            result += obj[k]||"";
        });
        result += "\n";
    });
    return result;
}
async function get_crmTable_csv(id, table, connection){
    let result = [];
    let first_item_key = Object.keys(crm_items[id][table])[0];
    let others_keys = Object.keys(crm_items[id][table]);
    others_keys.splice(0, 1);
    result.push(crm_items[id][table][first_item_key]);
    sql = `SELECT columns FROM crm WHERE owner_id = '${id}'`;
    const [COLrows, COLfields] = await connection.execute(sql);
    let dbtable = JSON.parse(COLrows[0]['columns'])[table];
    for(column in dbtable['table_columns']){
        if(!result[0].hasOwnProperty(column)) result[0][column] = '';
    }
    for(column in dbtable['deffault_columns']){
        if(!result[0].hasOwnProperty(column)) result[0][column] = '';
    }
    for(index of others_keys){ result.push(crm_items[id][table][index]);  }
    return CSV(result);
}
obfuscator();