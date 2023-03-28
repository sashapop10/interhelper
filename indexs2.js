const { get } = require('http');
const { time } = require('console');
const { format } = require('path');
const { start } = require('repl');
const e = require('express');
const { throws } = require('assert');
class _admin{
    login = "interhelper";
    password = "Fadkj123ADSFJ!"; 
}
class _tariff{
    async delete(){
        const sql = `DELETE FROM tariffs WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete variables.tariffs[this.id];
        logIt(`Тариф ${this.id} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE tariffs SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Тариф ${this.id} изменён.`, 'normal');
    }
    id; name; type; structure;
    constructor(id, name, type, structure){
        this.name = name; this.id = id; this.type = type; this.structure = structure;
        objects.tariffs[id] = this;
    }
}
class _tool{
    async delete(){
        const sql = `DELETE FROM tools WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete variables.tools[this.id];
        logIt(`Инструмент ${this.id} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE tools SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Инструмент ${this.id} изменён.`, 'normal');
    }
    id; name; photo; color; info; page;
    constructor(id, name, photo, color, info, page){
        this.name = name; this.id = id; this.photo = photo; this.color = color; this.info = info; this.page = page;
        objects.tools[id] = this;
    }
}
class _news{
    async delete(){
        const sql = `DELETE FROM blog WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete variables.blog[this.id];
        logIt(`Новость ${this.id} удалена.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE blog SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Новость ${this.id} изменена.`, 'normal');
    }
    id; name; photo; info; short_info; time;
    constructor(id, name, photo, info, page, time){
        this.name = name; this.id = id; this.photo = photo;this.info = info; this.page = page; this.time = time;
        objects.blog[id] = this;
    }
}
class _review{
    async delete(){
        const sql = `DELETE FROM reviews WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete variables.reviews[this.id];
        logIt(`Отзыв ${this.id} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE reviews SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Отзыв ${this.id} изменён.`, 'normal');
    }
    id; name; photo; review; link; time; rating; boss_id;
    constructor(id, name, photo, review, link, time, rating, boss_id){
        this.id = id; this.name = name; this.photo = photo; this.review = review; this.link = link; this.time = time; this.rating = rating; this.boss_id = boss_id;
        objects.reviews[id] = this;
    }
}
class _order{
    async delete(){
        const sql = `DELETE FROM orders WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete variables.orders[this.id];
        logIt(`Оплата ${this.id} удалена.`, 'normal');
    }
    id; amount; boss_id; orderNumber; orderId;
    constructor(id, name, photo, review, link, time, rating, boss_id){
        this.id = id; this.name = name; this.photo = photo; this.review = review; this.link = link; this.time = time; this.rating = rating; this.boss_id;
    }
}
class _guest{
    id; room; time; domains_list; served_list; info; removed; serving_list; session_time; photo; sessions; visits; crm_id; properties; notes; boss_id;
    async delete(){
        const sql = `DELETE FROM guests WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete system.users.guests[this.boss_id][this.id];
        logIt(`Посетитель ${this.id} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE guests SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Посетитель ${this.id} изменён.`, 'normal');
    }
    async ban(){

    }
    constructor(id, room, time, domains_list, served_list, info, removed, serving_list, session_time, photo, sessions, visits, crm_id, properties, notes, boss_id){
        this.id = id; this.room = room; this.time = time; this.domains_list = domains_list; this.served_list = served_list; this.info = info; this.removed = removed;
        this.serving_list = serving_list; this.session_time = session_time; this.photo = photo; this.sessions = sessions; this.visits = visits;
        this.crm_id = crm_id; this.properties = properties; this.notes = notes; this.boss_id = boss_id;
        if(!server.users.guests[boss_id]) server.users.guests[boss_id] = {};
        server.users.guests[boss_id][id] = this;
    }
}
class _banned{
    async delete(){
        const sql = `DELETE FROM banned WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete system.users.banned[this.boss_id][this.id];
        logIt(`Заблокированный посетитель ${this.id} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE banned SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Заблокированный посетитель ${this.id} изменён.`, 'normal');
    }
    async free(){

    }
    id; room; ip; boss_id; time; domains_list; served_list; reason; bannedBy; info; session_time; photo; sessions; visits; crm_id; notes; properties;
    constructor(id, room, ip, boss_id, time, domains_list, served_list, reason, bannedBy, info, session_time, photo, sessions, visits, crm_id, notes, properties){
        this.id = id; this.room = room; this.time = time; this.domains_list = domains_list; this.served_list = served_list; this.info = info; this.session_time = session_time; 
        this.photo = photo; this.sessions = sessions; this.visits = visits; this.crm_id = crm_id; this.properties = properties; this.notes = notes; this.boss_id = boss_id;
        this.reason = reason; this.bannedBy = bannedBy; this.ip = ip;
        if(!server.users.banned[boss_id]) server.users.banned[boss_id] = {};
        server.users.banned[boss_id][id] = this;
    }
}
class _table{
    structure; uid; boss_id; 
    async delete(){
        const sql = `DELETE FROM tables WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete system.crm.tables[this.boss_id][this.id];
        logIt(`Таблица ${this.id} удалена.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE tables SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Таблица ${this.id} изменена.`, 'normal');
    }
    constructor(structure, uid, boss_id){
        this.structure = structure; this.uid = uid; this.boss_id = boss_id;
        if(!system.crm.crm[boss_id]) system.crm.crm[boss_id] = {}; 
        system.crm.crm[boss_id][uid] = structure;
    }
}
class _item{
    structure; uid; table; boss_id;
    async delete(){
        const sql = `DELETE FROM items WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete system.crm.items[this.boss_id][this.id];
        logIt(`Запись ${this.id} удалена.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE items SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Запись ${this.id} изменена.`, 'normal');
    }
    constructor(structure, uid, table, boss_id){
        this.structure = structure; this.uid = uid; this.table = table; this.boss_id = boss_id;
        if(!server.crm.items[boss_id]) server.crm.items[boss_id] = {};
        if(!server.crm.items[boss_id][table]) server.crm.items[boss_id][table] = {};
        server.crm.items[boss_id][table][uid] = this;
    }
}
class _faq{
    async delete(){
        const sql = `DELETE FROM faq WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete variables.faq[this.id];
        logIt(`Faq ${this.id} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE faq SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Faq ${this.id} изменён.`, 'normal');
    }
    id; name; type; group; structure; page;
    constructor(id, name, type, group, structure, page){
        this.structure = structure; this.id = id; this.name = name; this.type = type; this.group = group; this.page = page;
        objects.faq[id] = this;
    }
}
class _task{
    async delete(){
        const sql = `DELETE FROM tasks WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete system.crm.tasks[this.boss_id][this.id];
        logIt(`Задача ${this.id} удалена.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE tasks SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Задача ${this.id} изменена.`, 'normal');
    }
    boss_id; type; time; group; text; uid; creator_id; table;
    constructor(boss_id, type, time, group, text, uid, creator_id, table){
        this.boss_id = boss_id; this.type = type; this.time = time; this.group = group; 
        this.text = text; this.uid = uid; this.creator_id = creator_id; this.table = table;
        if(!server.crm.tasks[boss_id]) server.crm.tasks[boss_id] = {};
        if(!server.crm.tasks[boss_id][table]) server.crm.tasks[boss_id][table] = {};
        server.crm.tasks[boss_id][table][uid] = this;
    }
}
class _boss{
    id; name; password; settings; domains; photo; money; tariff; payday; time; ban; pay_notifcation; email;
    async pay(){
        try{
            const tariff = objects.tariffs[this.tariff]; 
            if(tariff['cost']['value'] != 0 && this.money - tariff['cost']['value'] < 0 || this.money < 0) return false;
            this.money -= tariff['cost']['value'];
            connection.open();
            const sql = `DELETE FROM uvisitors WHERE owner_id = '${id}'`;
            connection.command(sql);
            connection.close();
            this.update({'pay_notification': null, 'payday': getTime(new Date(), 'd.m.y')});
            return true; 
        } catch(err) {
            logIt(`ОШИБКА оплаты (${this.id}): ${JSON.stringify(opts)}\n${err}`, 'error'); 
            return false; 
        }
    }
    async delete(){
        try{
            const sql = `DELETE FROM users WHERE id = '${this.id}'`;
            await database.open();
            await database.command(sql);
            database.close();
            delete system.users.bosses[this.boss_id];
            // Тут удалять всё о пользователе
            logIt(`Пользователь ${this.id}: ${this.name} - ${this.domain} удалён.`, 'normal');
        } catch(err) { logIt(`ОШИБКА не могу удалить клиента (${this.id})\n${err}`, 'error'); }
    }
    async update(opts){
        try{
            await database.open();
            const sql = `UPDATE users SET ${sqlOpts(opts)} WHERE id = ${this.id}`;
            await database.command(sql);
            database.close();
            for(key in opts){
                const value = opts[key];
                this[key] = value;
            }
            logIt(`Пользователь ${this.id}/${this.email} изменён.`, 'normal');
        } catch(err) {logIt(`ОШИБКА внесений изменений в пользователя (${this.id}): ${JSON.stringify(opts)}\n${err}`, 'error');}
    }
    constructor(info){
        this.name = info.name; 
        this.email = info.email;
        main = this;
        if(info.create_type == 'old'){
            this.id = info.id; 
            this.password = info.password; 
            this.settings = info.settings;
            this.domains = info.domains; 
            this.photo = info.photo; 
            this.money = info.parseInt(money);
            this.tariff = info.tariff; 
            this.payday = info.payday; 
            this.time = info.time; 
            this.ban = info.ban; 
            this.pay_notifcation = info.pay_notifcation; 
            this.token = info.token; 
        } else {
            const hash = uniqid();
            bcrypt.hash(info.password, 10, function(err, hash) { main.password = hash; });
            server.mailer.send(
                'InterHelper',
                'info@interhelper.ru',
                [this.email],
                'Подтверждение почты !',
                this.mailer.carset(
                    'Подтвердите почту !', 
                    'https://interhelper.ru/setup/start?hash='+hash, 
                    'Чтобы подтвердить почту', 
                    'перейдите по ссылке',
                    'Чтобы приступить к работе с InterHelper, нам важно знать подлиность вашей почты, чтобы в случае потери доступа - его восстановить.',
                    'Подтвердите почту, <a href="https://interhelper.ru/setup/start?hash'+hash+'" style="color:'+colors.white+';">перейдите по ссылке</a>',
                    'Без подтверждения почты - в аккаунт не войти !'
                ), 
                []
            );
            logit(`Отправил подтверждение почты (клиент)`, 'func');
        }
        server.users.bosses[id] = this;
    }
}
class _employee{
    id; name; password; boss_id; photo; departament; email; battlecry; time; ban; token;
    async delete(){
        const sql = `DELETE FROM employers WHERE id = '${this.id}'`;
        await database.open();
        await database.command(sql);
        database.close();
        delete system.users.employers[this.boss_id][this.id];
        logIt(`Работник ${this.boss_id}/${this.id}/${this.email} удалён.`, 'normal');
    }
    async update(name, value){
        await database.open();
        const sql = `UPDATE users SET ${name} = '${value}' WHERE id = ${id}`;
        await database.command(sql);
        database.close();
        this[name] = value;
        logIt(`Работник ${this.boss_id}/${this.id}/${this.email} изменён.`, 'normal');
    }
    constructor(id, name, password, boss_id, photo, departament, email, battlecry, time, ban, token){
        this.id = id; this.name = name; this.password = password; this.boss_id = boss_id; this.photo = photo; this.departament = departament; this.email = email;
        this.battlecry = battlecry; this.time = time; this.ban = ban; this.token = token;
        if(!server.users.employers[boss_id]) server.users.employers[boss_id] = {};
        server.users.employers[boss_id][id] = this;
    }
}
class _variables{
    deffault_tariff = "Стартовый";
    start_capital = 0;
    lens = {
        name: 150, // для названий 
        text: 400, // для текста
    };
    imgs = [
        'jpg', 
        'gif', 
        'png', 
        'bmp', 
        'ico', 
        'jpeg', 
        'webp'
    ];
    files = [
        'rar',
        'zip',
        'doc',
        'docx',
        'ods',
        'odt',
        'pdf',
        'ppt',
        'pptx',
        'xlt',
        'xlsx',
        'xls',
        'docm',
        'dot',
        'txt',
        'zip'
    ];
    paths = {
        guests: 'C:/hosting/interhelper.ru/www/files/guests',
        backup: 'C:/hosting/backup/',
        crm: 'C:/hosting/interhelper.ru/www/files/crm/',
        emojis: 'C:/hosting/interhelper.ru/www/files/emojis/',
        users: 'C:/hosting/interhelper.ru/www/files/users/',
        employers: 'C:/hosting/interhelper.ru/www/files/employers/',
        tools: 'C:/hosting/interhelper.ru/www/files/tools/',
        chat: 'C:/hosting/interhelper.ru/www/files/chat/',
        blog: 'C:/hosting/interhelper.ru/www/files/blog/',
        reviews: 'C:/hosting/interhelper.ru/www/files/reviews/',
    };
    guests = {
        guests_photos: [],
        guests_colors: [
            '#FED6BC', 
            '#FFFADD', 
            '#DEF7FE', 
            '#E7ECFF', 
            '#C3FBD8', 
            '#FDEED9', 
            '#F6FFF8', 
            '#B5F2EA', 
            '#C6D8FF'
        ],
    };
    adds_companys = {
        yandex: "Яндекс Директ",
        google: "Google Adwords",
        facebook: "facebook",
        vk: "Реклама vk",
        targetmail: "myTarget",
        instagram: "instagram"
    };
    emojis = {};
}
class _objects{
    tariffs = {};
    tools = {};
    reviews = {};
    faq = {};
    blog = {};
    orders = {};
    unconfirmed = {
        bosses: {},
        employers: {},
    };
    resets = {};
}
class _database{
    result;
    connection;
    connection_config = { 
        charset: "utf8_general_ci",
        host: "localhost",
        user: "root",
        database: "interhelper",
        password: "Fadkj123ADSFJ!"
    }
    async open(){ this.connection = await mysql.createConnection(this.connection_config); }
    async command(sql){ this.result = await this.connection.execute(sql); }
    async close(){ this.connection.end(); }
    async get_all(){
        var sql = `SELECT * FROM users`;
        await this.open();
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _boss(
                info.id, 
                info.name, 
                info.password, 
                JSON.parse(info.settings), 
                JSON.parse(info.domains), 
                info.photo, 
                parseInt(info.money), 
                info.tariff, 
                info.payday, 
                info.time, 
                info.ban == 'true',
                info.pay_notifcation == 'true',
                info.email, 
                null
            );
        }
        sql = `SELECT * FROM employers`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _employee(
                info.id, 
                info.name, 
                info.password, 
                info.boss_id, 
                info.photo, 
                info.departament, 
                info.email, 
                info.battlecry, 
                info.time, 
                info.ban,
                null
            );
        }
        sql = `SELECT * FROM guests`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _guest(
                info.id, 
                info.room, 
                info.time, 
                JSON.parse(info.domains_list), 
                JSON.parse(info.served_list), 
                JSON.parse(info.info), 
                info.removed, 
                JSON.parse(info.serving_list), 
                info.session_time, 
                JSON.parse(info.photo), 
                JSON.parse(info.sessions), 
                info.visists, 
                info.crm_id, 
                JSON.parse(info.properties), 
                JSON.parse(info.notes), 
                info.boss_id
            );
        }
        sql = `SELECT * FROM banned`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _banned(
                info.id, 
                info.room, 
                info.ip,
                info.boss_id, 
                info.time, 
                JSON.parse(info.domains_list), 
                JSON.parse(info.served_list), 
                info.reason, 
                info.bannedBy, 
                JSON.parse(info.info), 
                info.session_time, 
                JSON.parse(info.photo), 
                JSON.parse(info.sessions), 
                info.visists, 
                info.crm_id, 
                JSON.parse(info.notes), 
                JSON.parse(info.properties)
            );
        }
        sql = `SELECT * FROM unique`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            if(!system.users.unique[info['boss_id']]) system.users.unique[info['boss_id']] = [];
            system.users.unique[info['boss_id']].append(info['ip']);
        }
        sql = `SELECT * FROM adds`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            if(!system.users.unique[info['boss_id']]) system.users.unique[info['boss_id']] = {};
            system.users.unique[info['boss_id']][info['ip']] = info['visits'];
        }
        sql = `SELECT * FROM crm`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _table(
                JSON.parse(info.structure),
                info.uid,
                info.boss_id
            );
        }
        sql = `SELECT * FROM items`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _item(
                JSON.parse(info.structure), 
                info.uid, 
                info.table, 
                info.boss_id
            );
        }
        sql = `SELECT * FROM tasks`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _task(
                info['boss_id'], 
                info['type'], 
                info['time'], 
                JSON.parse(info['group']), 
                info['text'], 
                info['uid'], 
                info['creator_id'], 
                info['table']
            );
        }
        sql = `SELECT * FROM tariffs`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _tariff(
                info.id,
                info.name,
                info.type,
                JSON.parse(info.structure)
            );
        }
        sql = `SELECT * FROM tools`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _tool(info.id, info.name, info.photo, info.color, info.info, info.page);
        }
        sql = `SELECT * FROM blog`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _news(info.id, info.name, info.photo, info.info, info.page, info.time);
        }
        sql = `SELECT * FROM faq`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _faq(info.id, info.name, info.type, info.group, info.structure, info.page)
        }
        sql = `SELECT * FROM reviews`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _review(info.id, info.name, info.photo, info.review, info.link, info.time, info.rating, info.boss_id);
        }
        sql = `SELECT * FROM orders`;
        await this.command(sql);
        for(var info of this.result){
            if(!info['id']) continue;
            new _order(info.id, info.name, info.photo, info.review, info.link, info.time, info.rating, info.boss_id);
        }
        this.close();
    }
    async cleaning(){
        let seven_days_ago = new Date;
        seven_days_ago.setDate(seven_days_ago.getDate() - 7);
        seven_days_ago = getStringFromTime('dateTime', seven_days_ago)
        let month_ago = new Date; month_ago.setMonth(month_ago.getMonth() - 1); month_ago = getStringFromTime('dateTime', month_ago)
        let year_ago = new Date; year_ago.setFullYear(year_ago.getFullYear() - 1); year_ago = getStringFromTime('dateTime', year_ago)
        syslog(`Процесс чистки запущен: ${seven_days_ago} | ${month_ago} | ${year_ago}`, 'func');
        var sql = `
            DELETE FROM messages WHERE cast(SendTime AS DATE) <= cast('${year_ago}' AS DATE) OR 
            (
                (SELECT count(1) FROM guests WHERE guests.id = messages.room) = 0 AND 
                (SELECT count(1) FROM banned WHERE banned.id = messages.room) = 0
            )
        `;
        try{
            database.open();
            database.command(sql);
            // тут удалять файлы (перед удалением брать инфу о файлах пихать в массив и в функцию)
            var sql = `DELETE FROM emessages WHERE cast(SendTime AS DATE) <= cast('${year_ago}' AS DATE)`;
            database.command(sql);
            var sql = `DELETE FROM forms WHERE (SELECT count(1) FROM rooms WHERE guests.id = forms.sender) = 0 AND (SELECT count(1) FROM banned WHERE banned.id = forms.sender) = 0`;
            database.command(sql);
            var sql = `DELETE FROM guests WHERE (cast(time AS DATE) <= cast('${month_ago}' AS DATE) and removed = 'true')`;
            database.command(sql);
            sql = `DELETE FROM adds_visitors WHERE cast(time AS DATE) <= cast('${month_ago}' AS DATE)`;
            database.command(sql);
            fs.readdirSync(backup_folder).forEach(file => {
            let file_date = file.split('_')[1].split('.')[0];
            if(new Date(file_date) < new Date(seven_days_ago)) {
                fs.unlink(backup_folder+file, err => { if(err) syslog(`Ошибка (чистки r) ${err}`, 'error'); });
                syslog(`Бэкап удалён ${file}`, 'func');
                }
            });
            database.close();
        } catch(err) { syslog(`Ошибка (чистки) ${err}`, 'error'); }
    }
}
class _methods{
    constructor(){
        global.url_opts = (url) => { // параметры гет запроса
            return JSON.parse('{"' + url.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) });
        }
        global.getTime = (opts) => { // время
            var d; var time = {}; var result = '';
            if(!opts) d = new Date();
            else d = opts[0];
            time['s'] = d.getSeconds();
            if(time['s'] < 10) time['s'] = '0' + time['s'];
            time['m'] = d.getMinutes();
            if(time['m'] < 10) time['m'] = '0' + time['m'];
            time['h'] = d.getHours();
            if(time['h'] < 10) time['h'] = '0' + time['h'];
            time['d'] = d.getDate();
            if(time['d'] < 10) time['d'] = '0' + time['d'];
            time['m'] = d.getMonth() + 1;
            if(time['m'] < 10) time['m'] = '0' + time['m'];
            time['y'] = d.getFullYear();
            if(!d) return hours + ":" + minutes + ":" + seconds + " " + days + "." + months + "." +  d.getFullYear();
            for(letter in opts[1]){
                letter = letter.toLowerCase();
                if(!time[letter]) result += letter;
                else result += time[letter];
            }
            return result;
        }
        global.logIt = (message, type) => { // логирование
            let types = {
                "error":   "\u001b[1;31m",
                "success": "\u001b[1;32m",
                "func":    "\u001b[1;33m",
                "normal":  "\u001b[1;34m",
                "strange": "\u001b[1;35m",
            };
            console.log(types[type] + getTime() +': ' + message);
        }
        global.sqlOpts = (opts) => { // Объект в sql 
            let result = '';
            for(key in opts){
                const value = opts[key];
                if(value != null) value = `'${value}'`;
                else value = 'NULL';
                result += key + '=' + `${value},`;
            }
            result.slice(0, -1);
        }
        global.toCSV = (array) => { // array => CSV 
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
        global.uniqid = function(pr, en) { // Уникальный индитификатор
            var pr = pr || '', en = en || false, result, us;
            this.seed = function (s, w) {
                s = parseInt(s, 10).toString(16);
                return w < s.length ? s.slice(s.length - w) : (w > s.length) ? new Array(1 + (w - s.length)).join('0') + s : s;
            };
            result = pr + this.seed(parseInt(new Date().getTime() / 1000, 10), 8) + this.seed(Math.floor(Math.random() * 0x75bcd15) + 1, 5);
            if (en) result += (Math.random() * 10).toFixed(8).toString();
            return result;
        };
    }
}
class _backup{
    create(){
        const today = getTime([new Date(), 'y-m-d']);
        backup.backup('C:/hosting/interhelper.ru/', `C:/hosting/backup/website_${today}.backup`);
        backup.backup('Z:/server/', `C:/hosting/backup/server_${today}.backup`);
        exec(`mysqldump -uroot -hlocalhost -pFadkj123ADSFJ! interhelper > C:/hosting/backup/database_${today}.sql`, {cwd: 'C:/Program Files/MariaDB 10.5/bin'}); 
        logIt(`BACKUP`, 'func');
    }
    constructor(){
        this.create(); 
        setInterval(this.create, 1000 * 60 * 60 * 24);
    }
}
class _shell{
    obfuscate(){ // обфускация CLIENT js
        fs.readFile("C:/hosting/interhelper.ru/www/HelperCode/f1239f1wjhkfjhu29834fh2h4389hf234f234/Helper.min.js", "utf8", (error,data) => {
            if(error){ syslog(error, 'error'); return; }
            let obfuscated = String(JavaScriptObfuscator.obfuscate(data));
            fs.writeFileSync("C:/hosting/interhelper.ru/www/HelperCode/f1239f1wjhkfjhu29834fh2h4389hf234f234/Helper.min.js", obfuscated);
        });
        fs.readFile("C:/hosting/interhelper.ru/www/scripts/hidden_scripts/minificated/main.min.js", "utf8", (error,data) => {
            if(error){ syslog(error, 'error'); return; }
            let obfuscated = String(JavaScriptObfuscator.obfuscate(data));
            fs.writeFileSync("C:/hosting/interhelper.ru/www/scripts/hidden_scripts/minificated/main.min.js", obfuscated);
        });
    } 
    list(){ // список команд
        mas = [
            'command-list', 
            'kill', 
            'clear-logs', 
            'reload-users', 
            'remove-user (id босса)', 
            'set-money (босс)/(Количество *1000 = 1000р)', 
            'check-mas (user/boss/assistent/unique/ban/adds/tokens/crm_items/tasks/tariffs)',
            'save'
        ];
        for(key in mas) console.log('\u001b[1;35m'+getTime()+ '--> ' +mas[key]);
    }
    constructor(){
        const main = this;
        global.rl = readline.createInterface({
            input: process.stdin,
            output: process.stdout
        });
        rl.on('line', async (input) => { // консоль
            input = input.toLowerCase();
            if(input == 'commands') GetCommandList();
            else if(input == 'kill') process.exit();
            //else if(input == 'clear') process.stdout.write('\033c');
            else if(input == 'save') main.obfuscate();
            else if(input.split(' ')[0] == 'remove-user'){
            
            } else if(input.split(' ')[0] == 'set-money'){
              
            } else if(input == 'reload-users') io.emit('page_reload');
            else if(input.split(' ')[0] == 'check-mas'){
                let mas = input.split(' ')[1];
                switch(mas){
                    case 'user': break;
                    deffault: console.log(getTime()+": Такой команды нет"); break;
                }
            } else console.log(getTime()+": Такой команды нет");
        });
    }
}
class _colors{
    type = 'day';
    willbe = 'night';
    blue = '#1972F5';
    white = '#ffffff';
    black = '#222222';
    orange = '#ffaa90';
    grey = '#f5f5f5';
    red = '#ff6347';
    darkblue = '#000033';
    darkbluelow = '#f2f2f3';
    darkgrey = '#666666';
    darkwhite = '#cccccc';
    start(){
        const main = this;
        var hour = new Date().getHours();
        var interval;
        if(hour >= 21) interval = 9 * 60 * 60 * 1000;
        else if(hour >= 5) interval = (21 - hour) * 60 * 60 * 1000;
        else interval = (5 - hour) * 60 * 60 * 1000;
        if (hour >= 0 && hour <= 5 || hour >= 21){
            main.white = '#222222';
            main.black = '#ffffff';
        } else {
            main.white = '#ffffff';
            main.black = '#222222';
        }  
        setTimeout((main) => { main.start(); }, interval, main);
    }
    constructor(){
        this.start();
    }
}
class _mailer{
    transporter; result;
    carset = (title, link, llink, rlink, top, middle, bottom) => { return `
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="https://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            ${this.head(title)}
            <body class="em_body" style="margin:0px; padding:0px;" bgcolor="#efefef">
                <table class="em_full_wrap" valign="top" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#efefef" align="center">
                    <tbody>     
                        <tr>
                            <td valign="top" align="center">
                                <table class="em_main_table" style="width:700px;" width="700" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tbody>
                                        ${this.header(link, llink, rlink)}
                                        ${this.banner}                          
                                        ${this.body(top, middle, bottom)}
                                        ${this.footer}
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="em_hide" style="white-space: nowrap; display: none; font-size:0px; line-height:0px;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div>
            </body>
        </html>
    `};
    head = (title) => { return `
        <head>
            <!—[if gte mso 9]><xml>
            <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
            </xml><![endif]—>
            <title>${title}</title>
            <meta http–equiv="Content-Type" content="text/html; charset=utf-8">
            <meta http–equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
            <meta name="format-detection" content="telephone=no">
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
            ${this.style}
        </head>
    `};
    style = `
        <style type="text/css">
            body {
            margin: 0 !important;
            padding: 0 !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
            -webkit-font-smoothing: antialiased !important;
            }
            img {
            border: 0 !important;
            outline: none !important;
            }
            p {
            Margin: 0px !important;
            Padding: 0px !important;
            }
            table {
            border-collapse: collapse;
            mso-table-lspace: 0px;
            mso-table-rspace: 0px;
            }
            td, a, span {
            border-collapse: collapse;
            mso-line-height-rule: exactly;
            }
            .ExternalClass * {
            line-height: 100%;
            }
            .em_defaultlink a {
            color: inherit !important;
            text-decoration: none !important;
            }
            span.MsoHyperlink {
            mso-style-priority: 99;
            color: inherit;
            }
            span.MsoHyperlinkFollowed {
            mso-style-priority: 99;
            color: inherit;
            }
            @media only screen and (min-width:481px) and (max-width:699px) {
            .em_main_table {
            width: 100% !important;
            }
            .em_wrapper {
            width: 100% !important;
            }
            .em_hide {
            display: none !important;
            }
            .em_img {
            width: 100% !important;
            height: auto !important;
            }
            .em_h20 {
            height: 20px !important;
            }
            .em_padd {
            padding: 20px 10px !important;
            }
            }
            @media screen and (max-width: 480px) {
            .em_main_table {
            width: 100% !important;
            }
            .em_wrapper {
            width: 100% !important;
            }
            .em_hide {
            display: none !important;
            }
            .em_img {
            width: 100% !important;
            height: auto !important;
            }
            .em_h20 {
            height: 20px !important;
            }
            .em_padd {
            padding: 20px 10px !important;
            }
            .em_text1 {
            font-size: 16px !important;
            line-height: 24px !important;
            }
            u + .em_body .em_full_wrap {
            width: 100% !important;
            width: 100vw !important;
            }
            }
        </style>
    `;
    header = (link, ltext, title) => { `
        <tr>
            <td style="padding:15px;" class="em_padd" valign="top" bgcolor="#f6f7f8" align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td style="font-family:'Open Sans', Arial, sans-serif; font-size:12px; line-height:15px; color:#0d1121;" valign="top" align="center">
                                ${title} | 
                                <a href="${link}" target="_blank" style="color:#0d1121; text-decoration:underline;">${ltext}</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    `};
    banner = `
        <tr>
            <td valign="top" align="center">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>                             
                        <tr>
                            <td valign="top" align="center">
                                <img class="em_img" alt="InterHelper" style="background-color:${colors.blue}; display:block; font-family:Arial, sans-serif; font-size:30px; line-height:34px; color:${colors.white}; max-width:700px;" src="https://interhelper.ru/imgs/helper_logo.png" width="700" border="0" height="345">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    `;
    body = (top, middle, bottom) => { `
        <tr>
            <td style="padding:35px 70px 30px;" class="em_padd" valign="top" bgcolor="${colors.black}" align="center">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td style="font-family:'Open Sans', Arial, sans-serif; font-size:16px; line-height:30px; color:${colors.white};" valign="top" align="center">
                                ${top}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:0px; line-height:0px; height:15px;" height="15">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-family:'Open Sans', Arial, sans-serif; font-size:18px; line-height:22px; color:${blue}; letter-spacing:2px; padding-bottom:12px;" valign="top" align="center">
                                ${middle}
                            </td>
                        </tr>
                        <tr>
                            <td class="em_h20" style="font-size:0px; line-height:0px; height:25px;" height="25">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-family:'Open Sans', Arial, sans-serif; font-size:18px; line-height:22px; color:${blue}; text-transform:uppercase; letter-spacing:2px; padding-bottom:12px;" valign="top" align="center">
                                ${bottom}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    `};
    footer = `
        <tr>
            <td style="padding:38px 30px;" class="em_padd" valign="top" bgcolor="#f6f7f8" align="center">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td style="padding-bottom:16px;" valign="top" align="center">
                                <table cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tbody>
                                        <tr>
                                            <td valign="top" align="center">
                                                <a href="#" target="_blank" style="text-decoration:none;" title="Студия interfire.ru">
                                                    <img src="https://interhelper.ru/files/imgs/logo.png" alt="fb" style="display:block; font-family:Arial, sans-serif; font-size:14px; line-height:14px; color:${white}; max-width:26px;" width="26" border="0" height="26">
                                                </a>
                                            </td>
                                            <td style="width:6px;" width="6">&nbsp;</td>
                                            <td valign="top" align="center">
                                                <a href="https://interhelper.ru" title="Мы interhelper.ru" target="_blank" style="text-decoration:none;">
                                                    <img src="https://interhelper.ru/files/imgs/interhelper_icon.svg" alt="tw" style="display:block; font-family:Arial, sans-serif; font-size:14px; line-height:14px; color:${white}; max-width:27px;" width="27" border="0" height="26">
                                                </a>
                                            </td>
                                            <td style="width:6px;" width="6">&nbsp;</td>
                                            <td valign="top" align="center">
                                                <a href="https://www.youtube.com/channel/UCnObj4J7fiML4n01GIXfXNg" target="_blank" style="text-decoration:none;" title="Наш youtube">
                                                    <img src="https://interhelper.ru/files/imgs/youtube.png" alt="yt" style="display:block; font-family:Arial, sans-serif; font-size:14px; line-height:14px; color:${white}; max-width:26px;" width="26" border="0" height="26">
                                                </a>
                                            </td>
                                            <td style="width:6px;" width="6">&nbsp;</td>
                                            <td valign="top" align="center">
                                                <a href="https://vk.com/interfire" target="_blank" style="text-decoration:none;" title="Наш вк">
                                                    <img src="https://interhelper.ru/files/imgs/vk.png" alt="yt" style="display:block; font-family:Arial, sans-serif; font-size:14px; line-height:14px; color:${white}; max-width:26px;" width="26" border="0" height="26">
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family:'Open Sans', Arial, sans-serif; font-size:11px; line-height:18px; color:#999999;" valign="top" align="center">
                                <!-- <a href="#" target="_blank" style="color:#999999; text-decoration:underline;">PRIVACY STATEMENT</a> | 
                                <a href="#" target="_blank" style="color:#999999; text-decoration:underline;">TERMS OF SERVICE</a> | 
                                <a href="#" target="_blank" style="color:#999999; text-decoration:underline;">RETURNS</a><br>
                                -->
                                ©${new Date().getFullYear()} InterHelper. All Rights Reserved.<br>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    `;
    send(name, email, to, subject, mail, attachments){
        this.result = await transporter.sendMail({
            from: `"${name}" <${email}>`,
            to: to.reduce((e, next) => {return e + ', ' + next}),
            subject: subject,
            // text: text,
            html: mail,
            attachments: attachments
        });
    }
    constructor(host, port, secure, user, pass){
        this.transporter = nodemailer.createTransport({
            host: host,
            port: port,
            secure: (secure == 'ssl'),
            auth: {
              user: user,
              pass: pass,
            },
        })
    }
}
class _server{
    link = 'https://interhelper.ru';
    http_port = 5320;
    https_port = 5321;
    hostname = '0.0.0.0';
    users = {
        guests: {}, // Посетители клиентских сайтов
        employers: {}, // Сотрудники клиентов 
        banned: {}, // Заблокированные посетители клиентских сайтов
        bosses: {}, // Клиенты 
        unique: {}, // Уникальные ip адреса
        adds: {} // Посетители пришедшие по рекламной ссылке
    }
    crm = {
        items: {}, // Записи CRM
        tasks: {}, // Задачи CRM
        tables: {} // Таблицы CRM
    }
    achats = {};
    tokens = {
        employers: {}, // токены авторизации сотрудников
        bosses: {} // токны авторизации клиентов
    }; 
    systems = {
        notifications: {}, // Рассылки клиентов
        forms: {}, // Формы обратной связи клиентов 
        statistic: {}, // Статистика клиентов
    };
    mailer;
    _libs(){
        global.nodemailer = require('nodemailer')
        global.express = require('express');
        global.app = express();
        global.obfuscator = require('javascript-obfuscator');
        global.readline = require('readline');
        global.geoip = require('geoip-lite');
        global.bodyParser = require('body-parser');
        global.urlencodedParser = bodyParser.urlencoded({extended: false});
        global.jsonParser = bodyParser.json();
        global.bcrypt = require('bcrypt');
        global.fs = require('fs');
        global.exec = require('child_process').exec;
        global.backup = require('backup');
        global.mysql = require("mysql2/promise");
        global.jsdom = require('jsdom');
        const { JSDOM } = jsdom;
        const { window } = new JSDOM();
        const { document } = (new JSDOM('')).window;
        global.document = document;
        global.$ = global.jquery = require('jquery')(window);
        global.fileupload = require("express-fileupload");
    }
    _listen(){
        const main = this;
        const opts = {
            key: fs.readFileSync('pk.key'),
            ca: fs.readFileSync('ca.pem'),
            cert: fs.readFileSync('cer.crt')
        }
        global.http = require('http').Server(app);
        global.https = require('https').Server(opts, app);
        https.listen(this.https_port, this.hostname, () => { syslog(`: HTTPS порт: ${this.https_port}`, 'func'); });
        http.listen(this.http_port, this.hostname, () => { syslog(`: HTTP порт: ${this.http_port}`, 'func'); });
        app.get('', (req, res) => { res.redirect(main.link+'/index'); });
        app.post('/client', async function(req, res) { // для боссов POST
            res.header("Access-Control-Allow-Origin", "*");
            res.header("Access-Control-Allow-Headers", "X-Requested-With");
            res.header('Access-Control-Allow-Headers', 'Content-Type');
            try{
                
            } catch(err) { 
                syslog(err, 'error'); 
                res.json({'success': {}, 'errors': ['Ошибка на стороне сервера']}); 
            }
        });
    }
    _io(){
        global.io = require('socket.io')(this.http, { cors: {origin: "*", methods: ["GET", "POST"]} });
        io.attach(this.https, {cors: {origin: "*", methods: ["GET", "POST"]}});
    }
    _load_files(){
        fs.readdirSync(variables.paths.visitors).forEach(file => { variables.visitors.photos.push(file); });
        fs.readdirSync(variables.paths.visitors).forEach(file => { variables.visitors.photos.push(file); });
    }
    payday(){
        var hour = new Date().getHours(); const main = this; const interval = (24 - hour) * 60 * 60 * 1000;
        if(hour == 0){
            let month_ago = new Date(); month_ago.setMonth(month_ago.getMonth() - 1);
            syslog(`Запущен процесс оплаты ! Дата начала тарифа <= ${getTime([month_ago, 'd.m.y'])}`, 'func'); 
            let poorClients = [];
            for(boss of this.users.bosses){
                if(new Date(boss.payday).valueOf() > month_ago.valueOf()) continue;
                const status = boss.pay();
                if(status){
                    syslog(`
                        \nКлиент (${boss.id}): оплатил ещё один месяц,\n 
                        Сумма списания: ${objects.tariffs[boss.tariff].cost.value},\n 
                        Остаточный баланс: ${boss.money}\n,
                        Тариф: ${objects.tariffs[boss.tariff].structure.name}
                    `, 'success'); 
                    continue;
                }
                boss.payday = 0;
                if(boss.pay_notifcation){
                    syslog(`
                        \nКлиент (${boss.id}): не смог оплатить ещё один месяц,\n 
                        Сумма списания: ${objects.tariffs[boss.tariff].cost.value},\n 
                        Остаточный баланс: ${boss.money}\n,
                        Тариф: ${objects.tariffs[boss.tariff].structure.name},\n
                        Рассылка: НЕТ
                    `, 'strange'); 
                    continue;
                }
                syslog(`
                    \nКлиент (${boss.id}): не смог оплатить ещё один месяц,\n 
                    Сумма списания: ${objects.tariffs[boss.tariff].cost.value},\n 
                    Остаточный баланс: ${boss.money}\n,
                    Тариф: ${objects.tariffs[boss.tariff].structure.name},\n
                    Рассылка: ДА
                `, 'strange'); 
                poorClients.append(boss.email);
            }
            if(poorClients.length > 0){
                this.mailer.send(
                    'InterHelper',
                    'info@interhelper.ru',
                    poorClients,
                    'На вашем счёте не достаточно средств !',
                    this.mailer.carset(
                        'На вашем счёте не достаточно средств !', 
                        'https://interhelper.ru/client/profile', 
                        'Чтобы пополнить счёт', 
                        'перейдите по ссылке',
                        'К сожалению на Вашем счёте, в момент попопытки продлить срок действия вашего тариф, было не достаточно средств.',
                        'Чтобы продлить срок действия тарифа <a href="https://interhelper.ru/client/profile" style="color:'+colors.white+';">перейдите по ссылке</a>',
                        'Мы ограничили функционал InterHelper, пока на Вашем счёте не достаточно средств.'
                    ), 
                    []
                )
            }
        } 
        syslog(`Следующий вызов оплаты через ${ 24 - hour } часа`, 'func'); 
        setTimeout((main) => { main.payday(); }, interval, main);
    }
    constructor(){
        this._libs(); // Подключаем библиотеки
        this._listen(); // Слушаем порты
        this._load_files(); // Индексируем файлы
        this.payday(); // Регулярная оплата
        this.mailer = new _mailer('smtp.yandex.ru', 587, 'tls', 'InterHelper', 'Fadkj123ADSFJ!'); // Почтовая функция от InterHelper
    }
}
var colors = new _colors(); // Цвета сайта | Меняются днём и ночью
var variables = new _variables();// Создаём переменные 
var objects = new _objects();// Создаём объекты 
new _methods(); // Создаём методы
new _backup(); // Регулярный BACKUP
new _shell(); // Вертуальная CMD 
const server = new _server(); // Запускаем сервер | Подключаем библиотеки | Индексируем файлы
const database = new _database(); // создаём БД
database.get_all(); // Переносим информацию на сервер из БД























// старый код
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
                    let todaymonth = today.getMonth() + 1;
                    if(parseInt(todaymonth) < 10) todaymonth = '0' + todaymonth;
                    let today_year = today.getFullYear();
                    today = today_year + '-' + todaymonth + '-' +today_date;
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
            let todaymonth = today.getMonth() + 1;
            if(parseInt(todaymonth) < 10) todaymonth = '0' + todaymonth;
            let today_year = today.getFullYear();
            if(!statistic["statistic"].hasOwnProperty(today_year)) statistic["statistic"][today_year] = {};
            if(!statistic["statistic"][today_year].hasOwnProperty(todaymonth)) statistic["statistic"][today_year][todaymonth] = {};
            if(!statistic["statistic"][today_year][todaymonth].hasOwnProperty(today_date)) statistic["statistic"][today_year][todaymonth][today_date] = {};
            if(!statistic["statistic"][today_year][todaymonth][today_date].hasOwnProperty(type)) statistic["statistic"][today_year][todaymonth][today_date][type] = 1;  
            else statistic["statistic"][today_year][todaymonth][today_date][type]++;
            if(hosts){
                for(host_key in hosts){
                    let host = hosts[host_key];
                    if(!host) continue;
                    if(!statistic.hasOwnProperty(host)) statistic[host] = {};
                    if(!statistic[host].hasOwnProperty(today_year)) statistic[host][today_year] = {};
                    if(!statistic[host][today_year].hasOwnProperty(todaymonth)) statistic[host][today_year][todaymonth] = {};
                    if(!statistic[host][today_year][todaymonth].hasOwnProperty(today_date)) statistic[host][today_year][todaymonth][today_date] = {};
                    if(!statistic[host][today_year][todaymonth][today_date].hasOwnProperty(type)) statistic[host][today_year][todaymonth][today_date][type] = 1;  
                    else statistic[host][today_year][todaymonth][today_date][type]++;
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
            let todaymonth = today.getMonth() + 1;
            if(parseInt(todaymonth) < 10) todaymonth = '0' + todaymonth;
            let today_year = today.getFullYear();
            if(!statistic["statistic"].hasOwnProperty(today_year)) statistic["statistic"][today_year] = {};
            if(!statistic["statistic"][today_year].hasOwnProperty(todaymonth)) statistic["statistic"][today_year][todaymonth] = {};
            if(!statistic["statistic"][today_year][todaymonth].hasOwnProperty(today_date)) statistic["statistic"][today_year][todaymonth][today_date] = {};
            if(!statistic["statistic"][today_year][todaymonth][today_date].hasOwnProperty(utm_source)) statistic["statistic"][today_year][todaymonth][today_date][utm_source] = {};  
            if(utm_medium) statistic["statistic"][today_year][todaymonth][today_date][utm_source][utm_medium] = {};
            if(utm_medium && utm_campaign) statistic["statistic"][today_year][todaymonth][today_date][utm_source][utm_medium][utm_campaign] = {};
            if(utm_medium && utm_campaign && utm_content) statistic["statistic"][today_year][todaymonth][today_date][utm_source][utm_medium][utm_campaign][utm_content] = [];
            if(utm_medium && utm_campaign && utm_content && utm_term) statistic["statistic"][today_year][todaymonth][today_date][utm_source][utm_medium][utm_campaign][utm_content].push(utm_term);
            if(hosts){
                for(host_key in hosts){
                    let host = hosts[host_key];
                    if(!host) continue;
                    if(!statistic.hasOwnProperty(host)) statistic[host] = {};
                    if(!statistic[host].hasOwnProperty(today_year)) statistic[host][today_year] = {};
                    if(!statistic[host][today_year].hasOwnProperty(todaymonth)) statistic[host][today_year][todaymonth] = {};
                    if(!statistic[host][today_year][todaymonth].hasOwnProperty(today_date)) statistic[host][today_year][todaymonth][today_date] = {};
                    if(!statistic[host][today_year][todaymonth][today_date].hasOwnProperty(utm_source)) statistic[host][today_year][todaymonth][today_date][utm_source] = {};  
                    if(utm_medium) statistic[host][today_year][todaymonth][today_date][utm_source][utm_medium] = {};
                    if(utm_medium && utm_campaign) statistic[host][today_year][todaymonth][today_date][utm_source][utm_medium][utm_campaign] = {};
                    if(utm_medium && utm_campaign && utm_content) statistic[host][today_year][todaymonth][today_date][utm_source][utm_medium][utm_campaign][utm_content] = [];
                    if(utm_medium && utm_campaign && utm_content && utm_term) statistic[host][today_year][todaymonth][today_date][utm_source][utm_medium][utm_campaign][utm_content].push(utm_term);
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
        let todaymonth = today.getMonth() + 1;
        if(parseInt(todaymonth) < 10) todaymonth = '0' + todaymonth;
        let today_year = today.getFullYear();
        if(!statistic["statistic"].hasOwnProperty(today_year)) statistic["statistic"][today_year] = {};
        if(!statistic["statistic"][today_year].hasOwnProperty(todaymonth)) statistic["statistic"][today_year][todaymonth] = {};
        if(!statistic["statistic"][today_year][todaymonth].hasOwnProperty(today_date)) statistic["statistic"][today_year][todaymonth][today_date] = {};
        if(!statistic["statistic"][today_year][todaymonth][today_date].hasOwnProperty(type)) statistic["statistic"][today_year][todaymonth][today_date][type] = 1;  
        else statistic["statistic"][today_year][todaymonth][today_date][type]++;
        if(hosts){
            for(host_key in hosts){
                let host = hosts[host_key];
                if(!host) continue;
                if(!statistic.hasOwnProperty(host)) statistic[host] = {};
                if(!statistic[host].hasOwnProperty(today_year)) statistic[host][today_year] = {};
                if(!statistic[host][today_year].hasOwnProperty(todaymonth)) statistic[host][today_year][todaymonth] = {};
                if(!statistic[host][today_year][todaymonth].hasOwnProperty(today_date)) statistic[host][today_year][todaymonth][today_date] = {};
                if(!statistic[host][today_year][todaymonth][today_date].hasOwnProperty(type)) statistic[host][today_year][todaymonth][today_date][type] = 1;  
                else statistic[host][today_year][todaymonth][today_date][type]++;
            }
        }
        statistic = JSON.stringify(statistic);
        sql = `UPDATE statistic SET info = '${statistic}' WHERE owner_id = '${domain}'`;
        await connection.execute(sql);
        connection.end();
    } catch(err) { syslog(`Ошибка (statistic) ${err}`, 'error'); }
}
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
function escapeRegExp(string){ return string.replace(/[\\]/g, "\\$&"); }
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