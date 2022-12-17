var app = require('express')();
const readline = require('readline');
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});
var fs = require('fs');
const mysql = require("mysql2");
const { exit } = require('process');
const hostname = '0.0.0.0';
const http_port = 5320;
const https_port = 5321;
const opts = {
    key: fs.readFileSync('pk.key'),
    ca: fs.readFileSync('ca.pem'),
    cert: fs.readFileSync('cer.crt')
}
var http = require('http').Server(app);
var https = require('https').Server(opts, app);
https.listen(https_port,hostname, () => {
    console.log('\u001b[1;32m'+`HTTPS on port : ${https_port}`);
});
http.listen(http_port,hostname, () => {
    console.log('\u001b[1;32m'+`Listening on port : ${http_port}`);
});
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
    res.redirect('https://interhelper.interfire.ru/index.php');
});
// посетители
var guest_rooms = [];
// ассистенты
var assistents = {};
var connection = conn();
// данные по ассистентам из бд
var sql = `SELECT id,email,domain,name,departament, photo, buttlecry, phone FROM assistents`;
connection.query(sql, function(err, results) {
    if(err) console.log('\u001b[1;31m'+`${err}`);
    for(key in results){
        let new_domain = results[key]['domain'];
        let new_user = results[key]['email'];
        if(!assistents.hasOwnProperty(new_domain)) assistents[new_domain] = {};
        if(!assistents[new_domain].hasOwnProperty(new_user)) createAssistent(results[key]["id"], new_user, results[key]["departament"], results[key]["photo"], results[key]["name"], results[key]["buttlecry"], results[key]["phone"], new_domain, 'create');
    }
});
// данные по посетителям из бд
var sql = `SELECT room,domain, room_name, assistents, user_name, user_email, user_phone FROM rooms`;
connection.query(sql, function(err, results) {
    if(err) console.log('\u001b[1;31m'+`${err}`);
    for(key in results){
		let room_domain = results[key]['room'].split("!@!@2@!@!")[0];
        let room_domain_adress = JSON.parse(results[key]['domain']);
        let new_room = results[key]['room'];
		let guest_assistents_story =  JSON.parse(results[key]['assistents']);
        if(!guest_rooms.hasOwnProperty(room_domain)) guest_rooms[room_domain] = {};
        if(!guest_rooms[room_domain].hasOwnProperty(new_room)) createGuest(room_domain, new_room, room_domain_adress, guest_assistents_story, results[key]['room_name'], results[key]['user_name'], results[key]['user_email'], results[key]['user_phone'], '', '', 'exist', 0, 'create');
    }
});
connection.end();
// комнаты чата ассистентов
var rooms = [];
// подключение
io.on('connection', (socket) => {
	// настройки ассистентов
	socket.on('change_assistent_settings', (data) => {
		if(!assistents.hasOwnProperty(data.domain)) assistents[data.domain] = {}
		if(assistents[data.domain].hasOwnProperty(data.email)){	
			if(data.setting == "departament") assistents[data.domain][data.email]["departament"] = data.value;
			else if(data.setting == "email"){
				psettings = assistents[data.domain][data.email];
				delete assistents[data.domain][data.email];
				assistents[data.domain][data.value] = psettings;
                assistents[data.domain][data.value]["hab"] = data.value;
                io.to(String(data.domain)).emit('assistentlist_update', ({"type": "change_settings_email", "value": assistents[data.domain][data.value], "target": data.email, "option": data.value}));
			}
			else if(data.setting == "photo") assistents[data.domain][data.email]["photo"] = data.value;
			else if(data.setting == "name") assistents[data.domain][data.email]["name"] = data.value;
			else if(data.setting == "remove") delete assistents[data.domain][data.email];		
			else if(data.setting == "phone") assistents[data.domain][data.email]["phone"] = data.value;
            else if(data.setting == "buttlecry") assistents[data.domain][data.email]["buttlecry"] = data.value;
            io.to(data.email).emit('assistentlist_update', ({"type": "change_settings", "value": data.value, "target": data.email, "option": data.setting}));
            if(data.setting != "email") io.to(String(data.domain)).emit('assistentlist_update', ({"type": "change_settings", "value": data.value, "target": data.email, "option": data.setting}));
			console.log('\u001b[1;32m'+` Настройки ассистента сменены : ${data.setting}` + ' у :' + data.email + 'на :' + data.value);
		}
		else if(data.setting == "new"){ createAssistent(data.id, data.email, data.departament, data.photo, data.name, data.buttlecry, data.phone, data.domain, "create"); io.to(String(data.domain)).emit('assistentlist_update', ({"type": "new_assistent", "value": createAssistent(data.id, data.email, data.departament, data.photo, data.name, data.buttlecry, data.phone, data.domain, "return"), "target": data.email, "option": null})); } 
		else socket.emit("page_reload");
	});
	// настройки комнаты
	socket.on('room_settings_changes', (data) => {
		if(guest_rooms[data.domain].hasOwnProperty(data.room)){
			var connection = conn().promise();
			connection.query("SET NAMES utf8");
			var adress = JSON.stringify(guest_rooms[data.domain][data.room]["domain_adress"]);
            var room_assistents = JSON.stringify(guest_rooms[data.domain][data.room]["assistent_story"]);
			var sql = `INSERT IGNORE INTO rooms(id, room, domain, assistents) SELECT 0, '${data.room}','${adress}','${room_assistents}' FROM dual WHERE NOT EXISTS (SELECT * FROM rooms WHERE room='{data.room}')`;	
			connection.query(sql).then(result => {
                var sql = ``;
				if (data.settings_name == 'room_name'){
					guest_rooms[data.domain][data.room]["room_name"] = data.value;
					sql = `UPDATE rooms SET room_name = '${data.value}' WHERE room = '${data.room}'`;
				}
				else if(data.settings_name == 'user_name'){
					sql = `UPDATE rooms SET user_name = '${data.value}' WHERE room = '${data.room}'`;
					guest_rooms[data.domain][data.room]["user_name"] = data.value;
				}
				else if(data.settings_name == 'user_email'){
					sql = `UPDATE rooms SET user_email = '${data.value}' WHERE room = '${data.room}'`;
					guest_rooms[data.domain][data.room]["user_email"] = data.value;
				}
				else if(data.settings_name == 'user_phone'){
					sql = `UPDATE rooms SET user_phone = '${data.value}' WHERE room = '${data.room}'`;
					guest_rooms[data.domain][data.room]["user_phone"] = data.value;
				}
				else console.log('\u001b[1;31m'+` Такой настройки нет !`);
                if(sql != `` && sql != null && sql != undefined){
                    var connection2 = conn().promise();
                    connection2.query("SET NAMES utf8");
                    connection2.query(sql).catch(err =>{
						console.log('\u001b[1;31m'+` promise error N1 : ${err}`);
					});
					connection2.end();
                }
			}).catch(err =>{
				console.log('\u001b[1;31m'+` promise error N1 : ${err}`);
			});
			connection.end();
			data.domain = String(data.domain);
			socket.broadcast.to(data.domain).emit('userlist_update', ({"type": "room_settings", "value":data.value, "target": data.room, "option": data.settings_name}));
			console.log('\u001b[1;32m'+` настройка : ${data.settings_name} / вступила в силу в комнате : ${data.room} / на домене : ${data.domain} / по значению : ${data.value}`);
			
		}
	});
	// чат ассистентов
    socket.on('assistent_chat_join', (data) => {
        socket.leaveAll();
        if(data.room != '' && data.room != undefined && data.room != null){
            var exist = true;
            // приватный чат
            if(data.room.includes('!@!@2@!@!')){
                for(var key2 = 0; key2 <= rooms.length; key2++){
                    if(rooms[key2] == data.room){
                        socket.room = data.room;
                        socket.join(data.room);
                        console.log('\u001b[1;36m'+` / Чат Ассистентов (private) / Вход в комнату : ${data.room}`);
                        exist = false;
                        break;
                    }
                    else if(rooms[key2] == data.room.split('!@!@2@!@!').reverse().join('!@!@2@!@!')){
                        room = data.room.split('!@!@2@!@!').reverse().join('!@!@2@!@!');
                        socket.room = room;
                        socket.join(room);
                        exist = false;
                        console.log('\u001b[1;36m'+`/ Чат Ассистентов (private) / Вход в комнату (reverse) : ${room}`);
                        break;
                    }
                }
				// если не существует
                if(exist){
                    rooms.push(data.room);
                    socket.join(data.room);
                    socket.room = data.room;
                    console.log('\u001b[1;36m'+` / Чат Ассистентов (private) / Вход в комнату (new) : ${data.room}`);
                }
            }
            // общая комната
            else{
                socket.room = data.room;
                socket.join(data.room);
                console.log('\u001b[1;36m'+` / Чат Ассистентов (public) / Вход в комнату (all) : ${data.room}`);
            }
        } 
        else  console.log('\u001b[1;31m'+` / Чат Ассистентов (error) / комната не отправлена `); 
    });
    // выход
    socket.on('disconnect', () => {
        if(socket.type != "boss") user_flag = true;
        else user_flag = false;
		// если посетитель
        if(guest_rooms.hasOwnProperty(socket.domain)){
            let guest_room;
            // с пользовательской стороны
            if(guest_rooms[socket.domain].hasOwnProperty(socket.room)) guest_room = socket.room;
            // с пользовательской стороны
            else if(guest_rooms[socket.domain].hasOwnProperty(socket.prevroom))  guest_room = socket.prevroom;
            // с пользовательской стороны
            else if(guest_rooms[socket.domain].hasOwnProperty(socket.prevroom2))  guest_room = socket.prevroom2;
            // при смене ip старый 
            else if(guest_rooms[socket.domain].hasOwnProperty(socket.prevroom3))  guest_room = socket.prevroom3;
            // при смене ip новый
            else if(guest_rooms[socket.domain].hasOwnProperty(socket.prevroom4))  guest_room = socket.prevroom4;
            if(guest_room){
                if(guest_rooms[socket.domain][guest_room]['typing'] != ''){
                    guest_rooms[socket.domain][guest_room]['typing'] = '';
                    socket.broadcast.to(guest_room).emit('stoptyping');
                    socket.broadcast.to(String(socket.domain)).emit('userlist_update', ({"type": "typing", "value":null, "target": guest_room, "option": null}));	
                }
                user_flag = false;
                let connections = guest_rooms[socket.domain][guest_room]['connections'];
                if(guest_rooms[socket.domain][guest_room]['status'] == 'online' || connections - 1 >= 0){
                    if(connections - 1 >= 0){connections--;  guest_rooms[socket.domain][guest_room]["connections"] = connections;}
                    if(!connections){
                        console.log('\u001b[1;36m'+"Гость вышел ("+guest_room+") ");
                        guest_rooms[socket.domain][guest_room]['status'] = 'offline';
                        socket.broadcast.to(guest_room).emit('room_status', {"status": "offline"});												
                        socket.broadcast.to(String(socket.domain)).emit('userlist_update', ({"type": "status", "value":"offline", "target": guest_room, "option": null}));	
                    }
                    else console.log('\u001b[1;36m'+"Гость ("+guest_room+") закрыл одно из своих подключений. Осталось - " + connections);	
                    guest_rooms[socket.domain][guest_room]["lastActivityTime"] = getTime();
                    socket.broadcast.to(String(socket.domain)).emit('userlist_update', ({"type": "time", "value":null, "target": guest_room, "option": null, "lastActivityTime": getTime() }));							
                }	
            }
        }
		// если ассистент
		if(user_flag){
            if(assistents.hasOwnProperty(socket.domain)){
                let assistent_email;
                if(assistents[socket.domain].hasOwnProperty(socket.room)) assistent_email = socket.room;
                else if(assistents[socket.domain].hasOwnProperty(socket.email)) assistent_email = socket.email;
                else {
                    for(assistent_key in assistents[socket.domain]){
                        if(assistents[socket.domain][assistent_key]["prev_email"] == socket.email){ assistent_email = assistent_key;  assistents[socket.domain][assistent_key]["prev_email"] = assistent_key; break; }
                        else if(assistents[socket.domain][assistent_key]["prev_email"] == socket.room){ assistent_email = assistent_key; assistents[socket.domain][assistent_key]["prev_email"] = assistent_key; break;}
                    }
                }
                if(assistent_email){	
                    user_flag = false;
                    let connections = assistents[socket.domain][assistent_email]["connections"];			
                    if(connections - 1 >= 0){connections--; assistents[socket.domain][assistent_email]["connections"] = connections; }
                    if(!connections){
                        assistents[socket.domain][assistent_email]["status"] = "offline";
                        socket.broadcast.to(String(socket.domain)).emit('assistentlist_update', ({"type": "status", "value":"offline", "target": assistent_email, "option": null}));	
                        // никого нет в сети -> оффлайн форма
                        check_assistents_status(socket.domain, 'public');
                        console.log('\u001b[1;36m'+"Ассистент вышел - " + assistent_email);
                    }
                    else console.log('\u001b[1;36m'+"Ассистент ("+assistent_email+") закрыл одно из своих подключений. Осталось - " + connections);	
                    // убираем из консультаций
                    if(Object.keys(assistents[socket.domain][assistent_email]["consultation_list"]).length > 0){
                        let consulated_guest_mas = [];
                        for (consulated_guest in assistents[socket.domain][assistent_email]["consultation_list"]){
                            consulated_guest_mas.push(consulated_guest);
                            if(guest_rooms[socket.domain].hasOwnProperty(consulated_guest)){
                                if(guest_rooms[socket.domain][consulated_guest]["assistents"].hasOwnProperty(assistent_email)){
                                    delete guest_rooms[socket.domain][consulated_guest]["assistents"][assistent_email];
                                    console.log('\u001b[1;36m'+"Ассистент перестал консультировать - "+ assistent_email);
                                }	
                            }
                        }
                        assistents[socket.domain][assistent_email]["consultation_list"] = {};
                        // обновляем массив посетителей
                        io.to(String(socket.domain)).emit('userlist_update', ({"type": "consultant_list_live", "value":consulated_guest_mas, "target": assistent_email, "option": "delete"}));					
                    }
                }       
            }
        }
        if(user_flag) console.log('\u001b[1;31m'+"Не существующая комната (disconect)");  
    });
	// ассистент печатает
    socket.on('typing', (data) => {
        socket.broadcast.to(socket.room).emit('typing', (data));
    });
    socket.on('stopTyping', () => {
        socket.broadcast.to(socket.room).emit('stopTyping');
    });
    socket.on('leave', (data) => {
        socket.broadcast.emit('leave', (data));
        socket.broadcast.to(socket.room).emit('stopTyping');
    });
	// ассистент или босс берёт коллег из списка
	socket.on('get_teammate_mas', (data) => {
		socket.emit('get_teammate_mas', {"assistents": assistents[data.domain]});
		console.log('\u001b[1;32m'+'Список ассистентов отправлен на домене - ' + data.domain );
    });
    // запрос на информацию о комнате
    socket.on('room_status', (data) => { 
        if(data.type == 'guest'){
            if(guest_rooms[data.domain].hasOwnProperty(data.room)) socket.emit('room_status', {"status": guest_rooms[data.domain][data.room]['status'], "prev_page": guest_rooms[data.domain][data.room]['prev_page'], "this_page": guest_rooms[data.domain][data.room]['this_page'], "typing":  guest_rooms[data.domain][data.room]['typing']});
        }
        if(data.type == 'assistent'){
            if(assistents[data.domain].hasOwnProperty(data.email)) socket.emit('room_status', {"status": assistents[data.domain][data.email]['status']});
        }
        console.log("Статус комнаты ("+(data.email||data.room)+") обновлён / " + data.type);
	});
	// удаление комнаты
	socket.on('remove_room', (data) => {
		guest_rooms[data.domain][data.room]["hide"] = true;
		data.domain = String(data.domain);
        io.to(String(data.domain)).emit('userlist_update', ({"type": "delete", "value": null, "target": data.room, "option": null}));
    });
    // ассистент входит в комнату
    socket.on('assistent-join', (data) => {
		if(data.room != undefined){ 
			socket.leaveAll();
			socket.join(data.room);
			console.log('\u001b[1;36m'+'Ассистент меняет комнату - '+ data.room + ' / домен - ' + data.domain);
            socket.room = data.room;
            socket.domain = data.domain;
        }
    });
    // ассистент получает старые сообщения
    socket.on('get-assistent-messages', (data) => {
        var sql = '';
        if(data.type == "guest") sql = `SELECT email, name, message, SendTime, phone, photo, departament, assistent_settings, sender, adds FROM messages_with_users_guests  LEFT JOIN assistents  ON (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id )) WHERE ( messages_with_users_guests.domain = '${data.domain}' AND messages_with_users_guests.room = (SELECT id FROM rooms WHERE room = '${data.room}') ) ORDER BY messages_with_users_guests.id ASC`; 
        else if(data.type == "assistent") sql = `SELECT email, name, message, SendTime, photo, departament, adds FROM assistents_chat_messages LEFT JOIN assistents  ON (( assistents_chat_messages.sender = assistents.id ) OR ( assistents_chat_messages.sender IS NULL AND assistents_chat_messages.sender != assistents.id )) WHERE ( assistents_chat_messages.domain = '${data.domain}' AND (assistents_chat_messages.room = '${data.room1}' OR assistents_chat_messages.room = '${data.room2}') ) ORDER BY assistents_chat_messages.id ASC`;
        else console.log("Не существующий тип - " + data.type);
        if(sql != ''){
            var connection = conn();
            connection.query(sql, function(err, results) {
                if(err) console.log('\u001b[1;31m'+`${err}`);
                socket.emit('get_previous_messages_assistent', (results));
            });
            connection.end();
            console.log('\u001b[1;32m'+"Ассистент получил сообщения по комнате / " + data.type);
        }
    });
    // проверка статуа ассистента
    socket.on('assistent-check', (data) => {
		if(assistents.hasOwnProperty(data.domain)){
			if( assistents[data.domain].hasOwnProperty(data.email) ){
				let assistents_status = true;
                socket.email = data.email;
                socket.domain = data.domain;
                assistents[data.domain][data.email]["status"] = "online";
                assistents[data.domain][data.email]["connections"]++;
				data.domain = String(data.domain);
                socket.broadcast.to(String(socket.domain)).emit('assistentlist_update', ({"type": "status", "value":"online", "target": data.email, "option": null}));	
				for(key in assistents[data.domain]){
					if(assistents[data.domain][key]["status"] == "online" && key != data.email) assistents_status = false;
                }	
                // если никого не было до этого
				if(assistents_status){
					for(key in guest_rooms[data.domain]){
						socket.to(key).emit("delete_form");
					}
					console.log('\u001b[1;36m'+"Домен перешёл в онлайн - " + data.domain);
				}
			}
        }
        else console.log('\u001b[1;31m'+"Домен не существует" +data.domain);
    });
	// ассистент входит в комнату с посетителем (последствия)
    socket.on('assistent-join-chat', (data) => {
        if(guest_rooms[data.domain].hasOwnProperty(data.room)){
            if(!guest_rooms[data.domain][data.room].hasOwnProperty(data.email)){
                guest_rooms[data.domain][data.room]["assistents"][data.email] = data.email;
				if(guest_rooms[data.domain][data.room]['new_message']["status"] == "unreaded") guest_rooms[data.domain][data.room]['new_message']["status"] = "readed";
				if(guest_rooms[data.domain][data.room]["assistent_story"]["assistents"].indexOf(data.email) == -1 && guest_rooms[data.domain][data.room]["messages_exist"] == "exist"){
					guest_rooms[data.domain][data.room]["assistent_story"]["assistents"].push(data.email);
					var connection = conn().promise();
					var assistents_list = '';
					connection.query(`SELECT assistents FROM rooms WHERE room = '${data.room}'`).then(result => {
						var connection2 = conn().promise();
						assistents_list = JSON.parse(result[0][0]["assistents"]);
						assistents_list["assistents"].push(data.email);
						assistents_list = JSON.stringify(assistents_list);
						connection2.query(`UPDATE rooms SET assistents = '${assistents_list}' WHERE room = '${data.room}'`).then(result => {
						}).catch(err => {
							console.log('\u001b[1;31m'+'promise error (add to assistents_list): '+err);
						});
						connection2.end();
					}).catch(err =>{
						console.log('\u001b[1;31m'+'promise error (add to assistents_list): '+ err);
					});
                    connection.end();
                    console.log('\u001b[1;36m'+'Ассистент ('+data.email+') начал консультировать посетителя: '+ data.room);
                    io.to(data.domain).emit('userlist_update', ({"type": "consultant_list_story", "value": data.email, "target": data.room, "option": null}))
				}
				else if(guest_rooms[data.domain][data.room]["messages_exist"] != "exist" && guest_rooms[data.domain][data.room]["assistent_story"]["assistents"].indexOf(data.email) == -1){
                    guest_rooms[data.domain][data.room]["assistent_story"]["assistents"].push(data.email);
                    console.log('\u001b[1;36m'+'Ассистент ('+data.email+') продолжил консультировать посетителя: '+ data.room);
				}
				assistents[data.domain][data.email]["consultation_list"][data.room] = data.room;
				socket.room = data.email;
                data.domain = String(data.domain);
                io.to(data.domain).emit('userlist_update', ({"type": "consultant_list_live", "value": data.email, "target": data.room, "option": "add"}));  
            }
        }
    });
    //вышел из диалога с посетителем
    socket.on('assistent-exit-chat', (data) => {
        // тут будем завершать диалог
    });
    socket.on('assistent-send-message-for-all', (data) => {
        for(key in guest_rooms[socket.domain]){
            if(guest_rooms[socket.domain][key]["status"] == "online" || guest_rooms[socket.domain][key]["connections"] > 0){
                var lct = getTime();
                if(data.message_status == "visible"){
                
                }
                socket.to(key).emit()
            }
        }
    });
    // гость входит
    socket.on('guest-join', (data) => {
        socket.room = data.room;
        socket.domain = data.domain;
        if(data.prev_room) socket.prevroom = data.prev_room;
        socket.join(data.room);
		let new_adress = data.domain_adress;
        let guest_domen_exist = true; let guest_room_exist = true;
        // проверка домена
        for(var key in guest_rooms){ if(key == data.domain){ guest_domen_exist = false; break; } }
        // если не существует создаём
        if(guest_domen_exist){ guest_rooms[data.domain] = {}; console.log('\u001b[1;35m'+`Новый домен - ` + data.domain); }
        // если существует проверяем посетителя
        for(var room in guest_rooms[data.domain]){ if(room == data.room){ guest_room_exist = false; break; } }
        // если не существует создаём
        if(guest_room_exist){
            createGuest(data.domain, data.room, {"domains": [data.domain_adress]}, {"assistents": []}, '','', '','', data.prev_page, data.this_page, "unexist", 1, 'add');
            console.log('\u001b[1;35m'+`новый посетитель (${data.room}) ` );
            socket.broadcast.to(String(data.domain)).emit('userlist_update', ({"type": "new_guest", "value":createGuest(data.domain, data.room, {"domains": [data.domain_adress]}, {"assistents": []}, '','', '','', data.prev_page, data.this_page, "unexist", 1, 'return'), "target": data.room, "option": null}));
        }
        // если существует
        else{
            guest_rooms[data.domain][data.room]["hide"] = false;
			guest_rooms[data.domain][data.room]["prev_page"] = data.prev_page;
            guest_rooms[data.domain][data.room]["this_page"] = data.this_page;
            // проверка домена на наличие у посетителя
			if(guest_rooms[data.domain][data.room]["domain_adress"]["domains"].indexOf(new_adress) == -1 && new_adress != null && guest_rooms[data.domain][data.room]["messages_exist"] == "exist"){	
				guest_rooms[data.domain][data.room]["domain_adress"]["domains"].push(new_adress);
				var connection = conn().promise();
				var domains_list;
				connection.query(`SELECT domain FROM rooms WHERE room = '${data.room}'`).then(result => {
					var connection2 = conn().promise();
					domains_list = JSON.parse(result[0][0]["domain"]);
					domains_list["domains"].push(new_adress);
					domains_list = JSON.stringify(domains_list);
					connection2.query(`UPDATE rooms SET domain = '${domains_list}' WHERE room = '${data.room}'`).then(result => {
					}).catch(err => {
						console.log('\u001b[1;31m'+'promise error (guest join): '+err);
					});
					connection2.end();
				}).catch(err =>{
					console.log('\u001b[1;31m'+'promise error (guest join): '+err);
				});	
                connection.end();
                io.to(String(data.domain)).emit('userlist_update', ({"type": "domain","value":new_adress, "target": data.room, "option": null}));
				console.log('\u001b[1;35m'+"Гость вошёл на новый домен! - " + data.room);
			}
			else if(guest_rooms[data.domain][data.room]["domain_adress"]["domains"].indexOf(new_adress) == -1 && new_adress != null && guest_rooms[data.domain][data.room]["messages_exist"] != "exist"){
                guest_rooms[data.domain][data.room]["domain_adress"]["domains"].push(new_adress);
                io.to(String(data.domain)).emit('userlist_update', ({"type": "domain","value":new_adress, "target": data.room, "option": null}));
			}
            guest_rooms[data.domain][data.room]["status"] = "online";
            guest_rooms[data.domain][data.room]["connections"]++;
            let connections = guest_rooms[data.domain][data.room]["connections"];
            if(connections == 1) console.log('\u001b[1;36m'+'Посетитель ('+data.room+') вернулся ');
            else if(connections > 1) console.log('\u001b[1;36m'+'Посетитель открыл ('+data.room+') новую вкладку - ' + connections);
            guest_rooms[data.domain][data.room]["lastActivityTime"] = getTime();
            socket.broadcast.to(String(data.domain)).emit('userlist_update', ({"type": "status", "value":"online", "target": data.room, "option": [data.prev_page, data.this_page], "lastActivityTime": getTime()}));
        }
		socket.broadcast.to(data.room).emit('room_status', {"status": "online", "prev_page": guest_rooms[data.domain][data.room]['prev_page'], "this_page": guest_rooms[data.domain][data.room]['this_page']});
        //отправляем посетителю его ip 
        socket.emit('guest-ip', (data.ip));
        //обновляем посетителей на домене
    });
    // берём настройки
    socket.on("get_settings", (data) => {
        var connection = conn();
        // отправляем настройки
        var sql = `SELECT json_extract(settings, '$.InterHelperOptions') AS settings FROM users WHERE id = '${data.domain}'`;
            connection.query(sql, function(err, results) {
            if(err) console.log('\u001b[1;31m'+`${err}`);
            if(results) socket.emit('chat-settings', results[0]);
        });
        connection.end();
    });
    // проверка домена
    socket.on("check_interhelper_domain", (data) => {
        var connection = conn();
        var sql = `SELECT domain,id FROM users WHERE domain LIKE '%"${data.domain}"%'`;
        connection.query(sql, function(err, results) {
            if(err) console.log('\u001b[1;31m'+`${err}`);
            // существует
            if(results.hasOwnProperty(0) ){
                socket.emit('interhelper_status', {'status': 'exist', 'id': results[0]["id"] });
                console.log('\u001b[1;32m'+` Домен подтверждён - ` + data.domain);
            }
            // не существует
            else{
                console.log('\u001b[1;31m'+` Домен не найден - ` + data.domain);
                socket.emit('interhelper_status', {'status': 'unexist'});               
            }  
        });
        connection.end();
    });
    // проверка статуса домена
	socket.on("check_offline_status", (data) => {
		check_assistents_status(data.domain, 'personal');
	});
    // берём массив с посетителями
    socket.on('get-guests-mas-first', (data) => {
        socket.emit('get-guests-mas-first', ({"rooms": guest_rooms[data.domain]}))
    });
    // посетитель получает старые сообщения
    socket.on('get-guest-messages', (data) => {
        var connection = conn();
        let sql = `SELECT email,name, message, SendTime,phone, photo, departament, assistent_settings, sender,adds FROM messages_with_users_guests  LEFT JOIN assistents  ON (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id )) WHERE ( messages_with_users_guests.domain = '${data.domain}' AND (messages_with_users_guests.room = (SELECT id FROM rooms WHERE room = '${data.room}')) ) ORDER BY messages_with_users_guests.id ASC`; 
        connection.query(sql, function(err, results) {
            if(err) console.log('\u001b[1;31m'+`${err}`);
            socket.emit('get_previous_messages', (results));
        });
        connection.end();
        console.log('\u001b[1;32m'+"Гость получил сообщения по комнате");
    });
    // посетитель пишет
    socket.on('guest-message', (data) => {
		var strDate = getTime();
		let msg_type = data.type;
        data.time = strDate;
        // пересылаем сообщение в комнату
        io.in(socket.room).emit('guest-message', (data));
        let guest_domain_adress = JSON.stringify(guest_rooms[data.domain][data.room]["domain_adress"]);
        let guest_assistents_story = JSON.stringify(guest_rooms[data.domain][data.room]["assistent_story"]);
        var connection = conn().promise();
        connection.query("SET NAMES utf8");
        var sql = `INSERT IGNORE INTO rooms (id, room, domain, assistents) VALUES (0,'${data.room}','${guest_domain_adress}','${guest_assistents_story}')`;
        connection.query(sql).then(result => {
            sql = ``;
            if(msg_type == "message") sql = `INSERT INTO messages_with_users_guests (id, message, SendTime, domain, room) VALUES (0,'${data.message}', '${strDate}','${data.domain}', (SELECT id FROM rooms WHERE room = '${data.room}') )`;
            else if(msg_type == "photo") sql = `INSERT INTO messages_with_users_guests (id, message, SendTime, domain, room, adds) VALUES (0,'', '${strDate}','${data.domain}', (SELECT id FROM rooms WHERE room = '${data.room}'), '${data.adds}' )`;
            if(sql  != `` && sql != null && sql != undefined){
                var connection2 = conn().promise();
                connection2.query("SET NAMES utf8");
                connection2.query(sql).then(result => {
                    console.log('\u001b[1;32m'+"Сообщение сохранено в базу данных");
                }).catch(err =>{
                    console.log('\u001b[1;31m'+'promise error (guest message): '+ err);
                }); 
                connection2.end();
            }
        }).catch(err =>{
            console.log('\u001b[1;31m'+'promise error (guest message): '+ err);
        }); 
        connection.end();
        guest_rooms[data.domain][data.room]["messages_exist"] = "exist";
        // статус сообщения
        var guest_msg = '';
		if (data.type != "photo") guest_msg = data.message;
        else guest_msg = data.adds;
        if(Object.keys(guest_rooms[data.domain][data.room]["assistents"]).length === 0){
            guest_rooms[data.domain][data.room]["new_message"] = {"message": guest_msg, "status": "unreaded"}; 
            data.domain = String(data.domain);
            socket.broadcast.to(String(data.domain)).emit('userlist_update', ({"type": "message", "value": data.message, "target": data.room, "option": null}));
            console.log('\u001b[1;36m'+'Новое сообщение без внемания !');
        }
        else guest_rooms[data.domain][data.room]["new_message"]["message"] = guest_msg;
        guest_rooms[data.domain][data.room]["lastActivityTime"] = getTime();
        socket.broadcast.to(String(data.domain)).emit('userlist_update', ({"type": "time", "value":null, "target": data.room, "option": null, "lastActivityTime": getTime() }));
        console.log('\u001b[1;36m'+'Гость ('+ data.room +') пишет : '+guest_msg);
    });
    // смена ip у посетителя
	socket.on('guest-new-ip', (data) => {
        socket.prevroom3 = data.previp;
        socket.prevroom4 = data.newip;
		console.log('\u001b[1;33m'+"Процедура смены ip адреса началась");
		if(guest_rooms.hasOwnProperty(data.domain)){
			console.log('\u001b[1;32m'+"Домен подтверждён (Процедура смены ip) ");
			if(guest_rooms[data.domain].hasOwnProperty(data.previp)){
				let previnfo = guest_rooms[data.domain][data.previp];
                delete guest_rooms[data.domain][data.previp];
				guest_rooms[data.domain][data.newip] = previnfo;
				connection = conn();
				sql = `UPDATE rooms SET room = '${data.newip}' WHERE room = '${data.previp}'`; 
				connection.query(sql, function(err, results) { if(err) console.log('\u001b[1;31m'+`${err}`); });
				connection.end();
                console.log('\u001b[1;32m'+"IP сменился");
                io.to(String(data.domain)).emit("ChangeInfo", ({"type": "new_ip", "value": data.newip, "target": data.previp, "option": null}));
            }
            else console.log('\u001b[1;31m'+"Старый IP не найден");
        }
        socket.emit('ip_answer');
    });
	// отслеживаем что пишет юзер
	socket.on("guest_print", (data) => {
		socket.to(data.room).emit("guest_print", {"text": data.text});
		if(guest_rooms.hasOwnProperty(data.domain)){
			if(guest_rooms[data.domain].hasOwnProperty(data.room)){
                guest_rooms[data.domain][data.room]["typing"] = data.text;
                guest_rooms[data.domain][data.room]["lastActivityTime"] = getTime();
            	socket.broadcast.to(String(data.domain)).emit('userlist_update', ({"type": "typing", "value": data.text, "target": data.room, "option": null, "lastActivityTime": getTime() }));
				console.log('\u001b[1;36m'+data.room + "Гость печатает ("+ data.room +") : " + data.text);
			}	 
		}
	});
    // консультант пишет
    socket.on('consultant-message', (data) => {
		if(assistents.hasOwnProperty(data.domain)){ 
			if(assistents[data.domain].hasOwnProperty(data.email)){
                var aid = assistents[data.domain][data.email]["id"]; 
                if(data.message_adds != '' && data.message_adds != null && data.message_adds != undefined) data.message_adds = JSON.stringify(data.message_adds);
                var lct = getTime();
                data.time = lct;
				io.in(data.room).emit('consultant-message', (data));
				console.log('\u001b[1;36m'+'Ассистент пишет ('+data.email+') : '+ data.message + '/ тип сообщения - ' + data.type);
                // если не скрипт
				if(data.message_status == "visible"){
                    let sql = '';
                    let guest_assistents_story, guest_domain_adress;
                    if(guest_rooms.hasOwnProperty(data.domain)){
						if(guest_rooms[data.domain].hasOwnProperty(data.room)){
                            guest_rooms[data.domain][data.room]["messages_exist"] = "exist";
							guest_domain_adress = JSON.stringify(guest_rooms[data.domain][data.room]["domain_adress"]);
                            guest_assistents_story = JSON.stringify(guest_rooms[data.domain][data.room]["assistent_story"]);
                        }
                    }
                    // чат с консультантами 
                    if(data.type == 'assistent_chat_photo' || data.type == "assistent_chat"){
                        if(data.type == "assistent_chat") sql = `INSERT INTO assistents_chat_messages (id, sender, message, SendTime, domain, room) VALUES (0,'${aid}','${data.message}', '${lct}','${data.domain}', '${data.room}')`;
                        else if(data.type == 'assistent_chat_photo') sql = `INSERT INTO assistents_chat_messages (id, sender, message, SendTime, domain, room, adds) VALUES (0, '${aid}', '${data.message}', '${lct}','${data.domain}', '${data.room}', '${data.message_adds}' )`;
                        else console.log('\u001b[1;31m'+"не существующий тип сообщения");
                        if(sql != ''){
                            let connection = conn().promise();
                            connection.query("SET NAMES utf8");
                            connection.query(sql).then(result => {
                                console.log('\u001b[1;32m'+"сообщение сохранено ! / " + data.type);
                            }).catch(err =>{
                                console.log('\u001b[1;31m'+'promise error (consultant message): '+ err);
                            });
                            connection.end();
                        }
                    }
                    // чат с посетителями
                    else if(data.type == "guest_chat" || data.type == "guest_chat_photo" || data.type == "guest_chat_photo_and_message"){
                        var connection = conn().promise();
                        connection.query("SET NAMES utf8");
                        if(guest_rooms.hasOwnProperty(data.domain)) if(guest_rooms[data.domain].hasOwnProperty(data.room)) sql = `INSERT IGNORE INTO rooms (id, room, domain, assistents) VALUES (0,'${data.room}','${guest_domain_adress}','${guest_assistents_story}')`;
                        connection.query(sql).then(result => {
                            sql = ``;
                            if(data.type == "guest_chat") sql = `INSERT INTO messages_with_users_guests (id, sender, message, SendTime, domain, room) VALUES (0, '${aid}', '${data.message}', '${lct}','${data.domain}', (SELECT id FROM rooms WHERE room = '${data.room}'))`;
                            else if(data.type == "guest_chat_photo") sql = `INSERT INTO messages_with_users_guests (id, sender, message, SendTime, domain, room, adds) VALUES (0, '${aid}', '', '${lct}','${data.domain}', (SELECT id FROM rooms WHERE room = '${data.room}'), '${data.message_adds}' )`;
                            else if(data.type == "guest_chat_photo_and_message") sql = `INSERT INTO messages_with_users_guests (id, sender, message, SendTime, domain, room, adds) VALUES (0, '${aid}', '${data.message}', '${lct}','${data.domain}', (SELECT id FROM rooms WHERE room = '${data.room}'), '${data.message_adds}' )`;
                            else console.log('\u001b[1;31m'+"не существующий тип сообщения");
                            if(sql != ``){
                                var connection2 = conn().promise();
                                connection2.query("SET NAMES utf8");
                                connection2.query(sql).then(result => {
                                    console.log('\u001b[1;32m'+"сообщение сохранено ! / " + data.type);
                                }).catch(err =>{
                                    console.log('\u001b[1;31m'+'promise error (consultant message): '+ err);
                                }); 
                                connection2.end();
                            }
                        }).catch(err =>{
                            console.log('\u001b[1;31m'+'promise error (consultant message): '+ err);
                        }); 
                        connection.end();
                    }
				}
				else console.log('\u001b[1;32m'+"скрипт отправлен ! - " + data.message.replace(/\\r?\\n/g, ' '));
			}
			else{
				console.log('\u001b[1;35m'+"Отправка сообщения с не существующего аккаунта - "+ data.email);
				socket.emit("page_reload");
			}
		}
		else{
			console.log('\u001b[1;35m'+"Не существующий домен - " + data.domain);
		}
    });
    // проверка статуса ассистентов
    function check_assistents_status(domain, type){
        for(key in assistents[domain]){
            if(assistents[domain][key]["status"] == "online"){
                if(type == 'personal'){
                    socket.emit('delete_form');
                }
                return false;
            } 
        }
        var connection = conn();
        var sql = `SELECT json_extract(settings, '$.feedbackform') AS settings FROM users WHERE id = '${domain}'`;
        connection.query(sql, function(err, results) {
            if(err) console.log('\u001b[1;31m'+`${err}`);
            if(type == 'personal'){
                socket.emit('offline-form', {'offline_settings': results[0]});
                console.log('\u001b[1;36m'+'Домен перешёл в оффлайн (personal)');
            }
            else if(type == 'public'){
                console.log('\u001b[1;36m'+'Домен перешёл в оффлайн (public)');
                for(key in guest_rooms[domain]){
                    socket.to(key).emit('offline-form', {'offline_settings': results[0]});
                }
            }
            else{
                console.log('\u001b[1;31m'+'не существующий тип');
            }
        });
        connection.end();
        return true;
    }
});   
// подключение
function conn(){
	return mysql.createConnection({ 
	  charset: "utf8_general_ci",
      host: "82.202.227.174",
      user: "user4874_interhelper",
      database: "user4874_interhelper",
      password: "Dad123Kek123"
	});
}	
// время
function getTime(){
		let d = new Date();
        let seconds = d.getSeconds();
        if(seconds < 10){
            seconds = '0' + seconds;
        }
        let minutes = d.getMinutes();
        if(minutes < 10){
            minutes = '0' + minutes;
        }
        let hours = d.getHours();
        if(hours < 10){
            hours = '0' + hours;
        }
        let days = d.getDate();
        if(days < 10){
            days = '0' + days;
        }
        let months = d.getMonth() + 1;
        if(months < 10){
            months = '0' + months;
        }
      
        let strDate = d.getFullYear() + "-" + months + "-" + days + " " + hours + ":" + minutes + ":" + seconds;
		return strDate;
}
// чистка базы данных
setInterval(clearInfo, 1000 * 60 * 60 * 24 * 7);
function clearInfo(){
	console.log('\u001b[1;33m'+'delete process started');
	var connection = conn();
	let newdate = new Date;
	let newdays = newdate.getDate() + 1;
    if(newdays < 10 && newdays > 7){
        newdays = '0' + (newdays - 7);
    }
	else if(newdays < 10 && newdays <= 7){
		ost = newdays - 7
		newdays = '0' + ost;
	}
	var newmonths = newdate.getMonth() + 1;
	if(newmonths < 10){
		newmonths = '0' + newmonths;
	}
	var sql = "DELETE FROM 'messages_with_users_guests' WHERE 'SendTime' <= '"+newdate.getFullYear()+"-"+newmonths+"-"+newdays+" 99:99:99'";
	connection.query(sql, function(err, results) {
		if(err) console.log('\u001b[1;31m'+`${err}`);
		else console.log('\u001b[1;32m'+" --> deleted messages > 7 days from chat with guests <-- ");
	});
	var sql = "DELETE FROM 'assistents_chat_messages' WHERE 'SendTime' <= '"+newdate.getFullYear()+"-"+newmonths+"-"+newdays+" 99:99:99'";
	connection.query(sql, function(err, results) {
		if(err) console.log('\u001b[1;31m'+`${err}`);
		else console.log('\u001b[1;32m'+" --> deleted messages > 7 days from chat with assistents <-- ");
	});
	connection.end();
}
// создание ассистента
function createAssistent(id, email, departament, photo, name, buttlecry, phone, domain, type){
    if(type != "return") assistents[domain][email]  = {"consultation_list": {},"id": id, "status":"offline", "hab": email, "departament": departament, "photo": photo, "name":name, "buttlecry": buttlecry, "phone": phone, "connections": 0 , "prev_email": email};
    else return {"consultation_list": {},"id": id, "status":"offline", "hab": email, "departament": departament, "photo": photo, "name":name, "buttlecry": buttlecry, "phone": phone, "connections": 0, "prev_email": email};
}
// создание посетителя
function createGuest(domain, room, domain_story, assistent_story, room_name, user_name, user_email, user_phone, ppage, tpage, msg_exist_status, connections,type){
    if(type == "create") guest_rooms[domain][room] = {"assistents": {},"domain_adress":domain_story, "assistent_story": assistent_story, "status": "offline", "new_message": {"message": "", "status": "readed"}, "room_name": room_name, "user_name": user_name, "user_email": user_email, "user_phone": user_phone, "messages_exist": msg_exist_status, "this_page": tpage, "prev_page": ppage, "typing": "", "hide": false, "connections": connections, "lastActivityTime": ""};
    if(type == "return") return {"assistents": {},"domain_adress":domain_story, "assistent_story": assistent_story, "status": "online", "new_message": {"message": "", "status": "readed"}, "room_name": room_name, "user_name": user_name, "user_email": user_email, "user_phone": user_phone, "messages_exist": msg_exist_status, "this_page": tpage, "prev_page": ppage, "typing": "", "hide": false, "connections": connections , "lastActivityTime": getTime()};
    if(type == "add")  guest_rooms[domain][room] = {"assistents": {},"domain_adress":domain_story, "assistent_story": assistent_story, "status": "online", "new_message": {"message": "", "status": "readed"}, "room_name": room_name, "user_name": user_name, "user_email": user_email, "user_phone": user_phone, "messages_exist": msg_exist_status, "this_page": tpage, "prev_page": ppage, "typing": "", "hide": false, "connections": connections , "lastActivityTime": getTime()};
}
// консоль
rl.on('line', (input) => {
    switch(input.toLowerCase()){
        case 'command-list':
            GetCommandList();
            break;
        case 'kill':
            process.exit();
        case 'clear-logs':
            process.stdout.write('\033c');
            break;
        case 'timeout-kill':
            setTimeout('process.exit()', 1000 * 60 * 5);
        case 'alert-timeout-kill':
            setTimeout('AlertKill()', 1000 * 60 * 5);
        default:
            console.log("Это не выполнится");
    }
});
function AlertKill(){
    io.emit("alert", "Сервер закроется через 5 минут !");
    process.exit();
}
function GetCommandList(){
    mas = ['command-list', 'kill', 'clear-logs', 'timeout-kill', 'alert-timeout-kill']
    for(key in mas) console.log('\u001b[1;35m' + '--> ' +mas[key]);
}