let url = location.href;
var scrollTimeout = null;
var scrollTimeout2 = null;
let url_parts = url.split('/');
let url_page = url_parts[url_parts.length - 1];
let url_dir = url_parts[url_parts.length - 2];
let wow;
let conditions = {
    "desktop":{
        'text': 'Только для компьютеров',
        'type': 'prop',
        'uncheck': 'mobile',
    },
    "mobile":{
        'text': 'Только для моб. устройств',
        'type': 'prop',
        'uncheck': 'desktop'
    },
    "time":{
        'type': 'condition',
        'text': 'Промежуток времени',
        'input_type': 'time'
    },
    "open_counter":{
        'type': 'condition',
        'text': 'Количество посещений',
        'input_type': 'number'
    },
    "activity_time":{
        'type': 'condition',
        'text': 'Время на сайте',
        'input_type': 'time'
    },
    "link":{
        'type': 'condition',
        'text': 'Ссылка включает',
        'input_type': 'text'
    },
    "open":{
        'type': 'prop',
        'text': 'При открытии чата',
    },
    "close":{
        'type': 'prop',
        'text': 'При закрытии чата',
    },
    "msg_input":{
        'type': 'prop',
        'text': 'При вводе сообщения',
    },
    "msg_send":{
        'type': 'prop',
        'text': 'При отправке сообщения',
    },
    "consultant_offline":{
        'type': 'prop',
        'text': 'Ассистенты вне сети',
        'uncheck': 'consultant_online',
    },
    "consultant_online":{
        'type': 'prop',
        'text': 'Ассистенты в сети',
        'uncheck': 'consultant_offline',
    },
    "personal_event":{
        'type': 'condition',
        'text': 'Своё событие',
        'input_type': 'text',
        'placeholder1': 'Объект (#e,.e,e)',
        'placeholder2': 'Ивент (click)',
    },
}
// new alerts
let alerts = [];
let alerts_links = [];
let alert_body = $(`
    <div class="alert_container">
        <span class="alert_img"></span>
        <div class="alert_info">
            <p class="alert_info_message"></p>
        </div>
        <div class="alert_buttons">
            <button class="alert_btn" type="button">понятно</button>
        </div>
    </div>
`);
$('body').append(alert_body);
$('.alert_btn').on('click', () => {
    if(alerts.length == 1){ 
        if(alerts_links.length > 0){
            if(alerts_links[alerts_links.length - 1]) location.href = alerts_links[alerts_links.length - 1];
        }
        alerts_links = [];
        alerts = [];
        $('.alert_container').css({
            'top': '-600px',
        });
        return;
    }
    let message = alerts[0][0];
    let status = alerts[0][1];
    let link = alerts[0][2];
    alerts_links.push(link);
    $('.alert_img').css('background-image', 'url(/scss/imgs/'+(status == 'error' ? 'warning.png' : (status == 'log' ? 'question.svg' : 'check.png'))+')');
    $('.alert_container').css({
        'top': '100px',
        'background': (status == 'error' ? 'tomato' : (status == 'log' ? '#0ae' : 'lightgreen')),
        'color': (status == 'error' ? '#000000' : (status == 'log' ? '#000000' : '#000000')),
    });
    $('.alert_info_message').css({
        'color': (status == 'error' ? '#000000' : (status == 'log' ? '#000000' : '#000000')),
    });
    $('.alert_info_message').html(message);
    alerts.splice(0, 1);
});
window.alert = (message, status, link) => {
    if(!link) link = false;
    if(alerts.length == 0) alerts.push([message, status, link]);
    else if(alerts[alerts.length - 1][0] != message) alerts.push([message, status, link]);
    if(alerts.length > 1) return;
    $('.alert_img').css('background-image', 'url(/scss/imgs/'+(status == 'error' ? 'warning.png' : (status == 'log' ? 'question.svg' : 'check.png'))+')');
    $('.alert_container').css({
        'top': '100px',
        'background': (status == 'error' ? 'tomato' : (status == 'log' ? '#0ae' : 'lightgreen')),
        'color': (status == 'error' ? '#000000' : (status == 'log' ? '#000000' : '#000000')),
    });
    $('.alert_info_message').css({
        'color': (status == 'error' ? '#000000' : (status == 'log' ? '#000000' : '#000000')),
    });
    $('.alert_info_message').html(message);
};
//pasword check
$('.password_eye').on('click', function(e) {
    if($(this).siblings('input').attr('type') == 'password'){ 
        $(this).siblings('input').attr('type', 'text');
        $(this).css('background-image', 'url(/scss/imgs/open_eye.png)');
    } else { 
        $(this).siblings('input').attr('type', 'password');
        $(this).css('background-image', 'url(/scss/imgs/close_eye.png)');
    }
});
// navigation
const observer = new ResizeObserver(entries => {
    let el = entries[0];
    let width = el.contentRect.width;
    if(width <= 425){
        nav_controll(width - 60, 0);
    } else {
        nav_controll(300, 80)
    }
});
const body = document.body;
observer.observe(body);
let active_page = null;
$('#navigation ul li:not(:first-child)').on('mouseenter', function(e){
    let index = $(this).data('index') - 1;
    index = index * 70 + 110;
    $('.nav-selector').css('top', index);
    if($(this).hasClass('active-nav')) return;
    $('.active-nav p').css('color', 'var(--white)');
    $('.active-nav .nav-icon').css('fill', 'var(--white)');
});
$('#navigation ul li:not(:first-child)').on('mouseleave', function(e){
    let index = $('.active-nav').data('index') - 1;
    index = index * 70 + 110;
    $('.nav-selector').css('top', index);
    $('.active-nav p').css('color', 'var(--blue)');
    $('.active-nav .nav-icon').css('fill', 'var(--blue)');
});
if(localStorage['nav_scroll']) $('navigation').scrollTop(localStorage['nav_scroll']);
$( document ).ready(function(){ 
    $('#navigation, #main').css('transition', '0.5s'); 
    $('.nav-controll span').css('transition', 'width 0.5s, transform 0.5s, margin 0.5s, opacity 0.5s'); 
    $('#navigation ul li p').css({
        'transition':'opacity 0.5s',
    });
    let index = $('.active-nav').data('index') - 1;
    index = index * 70 + 110;
    $('.nav-selector').css('top', index);
});
$('#navigation').on('scroll', function() {
    localStorage['nav_scroll'] = $(this).scrollTop();
});
if(localStorage['header_scroll']) $('header').scrollTop(localStorage['header_scroll']);
$('header').on('scroll', function() {
    localStorage['header_scroll'] = $(this).scrollTop();
});
if((localStorage.anima == "false" || !localStorage.anima) && url_page.indexOf('dialog') == -1) $( document ).ready(function() {wow = new WOW().init();});
if(url_dir != 'consultant' && url_dir != 'pages'){
    $('.open-menu').click(function(e) {
        e.preventDefault();
        $('.nav-links-list').toggleClass('nav-links-list-act');
    });
    $('.nav-link-dropdown').click(function(e) {
        e.preventDefault();
        $(this).find('a').siblings('ul').toggleClass('bld');
    });
    $('.faq-block a').click(function(e) {
        e.preventDefault();
        $('.faq-modal').css('display', 'flex');
    });
    $('.close-modal').click(function() {
        $('.faq-modal').css('display', 'none');
    });
    const signUpButton = $('#signUp');
    const signInButton = $('#signIn');
    const containers = $('#containerr');
    signUpButton.on('click', () => {
        containers.addClass('right-panel-active');
    });
    signInButton.on('click', () => {
        containers.removeClass('right-panel-active');
    });
    $('#loginingmenuExit').on('click', () => {
        $('#loginingmenu').css('display', 'none');
    });
    $('.sigin').on('click', function(){
        $('#loginingmenu').css('display', 'block');
        containers.removeClass('right-panel-active');
    });
    $('.sigup').on('click', function(){
        $('#loginingmenu').css('display', 'block');
        containers.addClass('right-panel-active');
    });
    $('.input_sigup').on('click', function(){
        $('#email_input').val($(this).siblings("input").val());
        $('#loginingmenu').css('display', 'block');
        containers.addClass('right-panel-active');
    });
    $('.ajax_login_form').submit(function(e) {
        e.preventDefault();
        if($('.check_box:not(:checked)').length == 0) send_ajax('/engine/login', $(this).serialize());
        else alert("Примите пользовательское соглашение и политику конфеденциальности!", 'error');
    });
    $('.ajax_register_form').submit(function(e) {
        e.preventDefault();
        send_ajax('/engine/login', $(this).serialize());
    });
    $('.ajax_login_form_out').submit(function(e) {
        e.preventDefault();
        send_ajax('/engine/login', {'exit': true});
    });
    $('.reset_pass').on('click', () => {
        let email = prompt("Введите почту");
        if(email && /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email)) send_ajax('/engine/login', {"reset-password": email})
        else if(email) alert("Введите почту правильно!", 'error');
    });
}
if(url_page == '' || url_page == '#' || url.indexOf('index') != -1){
    var counters = document.getElementsByClassName('number-ticker');
    var defaultDigitNode = document.createElement('div');
    defaultDigitNode.classList.add('digit');
    for (var i = 0; i < 10; i++) { defaultDigitNode.innerHTML += i + '<br>'; }
    [].forEach.call(counters, function (counter) {
        var currentValue = parseInt(counter.getAttribute('data-value')) || 0;
        var digits = [];
        generateDigits(currentValue.toString().length);
        setValue(100000);
        $(window).scroll(function() { 
            if ($(this).scrollTop() > ($('#counter_block').offset().top - 772 ) && $(this).scrollTop() < ($('#counter_block').offset().top)) setTimeout(function () { setValue(Math.floor(currentValue)); }, 100);
        });
        function setValue (number) {
            var s = number.toString().split('').reverse().join('');
            var l = s.length;
            if (l > digits.length)  generateDigits(l - digits.length);
            for (var i = 0; i < digits.length; i++) { setDigit(i, s[i] || 0); }
        }
        function setDigit (digitIndex, number) { digits[digitIndex].style.marginTop = '-' + number + 'em'; }
        function generateDigits (amount) {
            for (var i = 0; i < amount; i++) {
                var d = defaultDigitNode.cloneNode(true);
                counter.appendChild(d);
                digits.unshift(d);
            }
        }
    });
    send_ajax('/engine/settings', {'getSettings': 'editions'});
    send_ajax('/engine/settings', {'getSettings': 'fitchas'});
    send_ajax('/engine/settings', {'getSettings': 'reviews'});
    send_ajax('/engine/settings', {'getSettings': 'problems'});
    var vue = new Vue({ 
		el: '.vue_el1',
		data: {
			editions: {},
		},
	});
	var vue2 = new Vue({ 
		el: '.vue_el2',
		data: {
			fitchas: {},
		},
	});
	var vue3 = new Vue({ 
		el: '.vue_el3',
		data: {
			reviews: {},
		},
		updated: function(){
			$('.vue_el3').slick({
				dots: false,
				arrows: true,
				infinite: true,
				speed: 300,
				slidesToShow: 1,
				adaptiveHeight: true
			});
		},
	});
	var vue4 = new Vue({ 
		el: '.faq',
		data: {
			faq: {0: {}, 1: {}},
		},
	});
} else if(url_dir == 'page' && url_page.indexOf('blog') != -1){
    send_ajax('/engine/settings', {'getSettings': 'news'});
    var vue = new Vue({ 
        el: '#news_section',
        data: {
            news: {},
        },
        methods: {
            redirect(index){
                window.location.href = '/page/news?id=' + index;
            },
        },
    });
} else if(url_dir == 'pages' && url_page.indexOf('profile') != -1){
    send_ajax('/engine/settings', {'getSettings': 'get_boss_info'});
    var vue = new Vue({ 
        el: '#container',
        data: {
            animations: (localStorage.anima == "true"),
            pass: false,
            photo: null,
            name: null,
            email: null,
            old: null,
            newpass: null,
            repeat: null,
        },
        methods: {
            change(type){
                if(type == 'photo') { 
                    if(!$(event.target).prop('files')[0]) return;
                    formData_send_ajax('/engine/settings', construct_formData($(event.target).prop('files')[0], 'profile_photo'));
                    $(event.target).val('');
                }
                else send_ajax('/engine/settings', {'personal_info_value': vue[type], 'personal_info_column': type});
            },
            exit(){ send_ajax('/engine/login', {'exit': true}); },
            change_pass(){ send_ajax('/engine/settings', {'personal_info_value': JSON.stringify({'old': vue.old, 'new': vue.newpass, 'repeat': vue.repeat}), 'personal_info_column': 'password'}); },
        },
        watch: {
            animations(value){ localStorage.anima = value; },
            pass(some){
                this.$nextTick(() => {
                    $('.password_eye').on('click', function(e) {
                        if($(this).siblings('input').attr('type') == 'password'){ 
                            $(this).siblings('input').attr('type', 'text');
                            $(this).css('background-image', 'url(/scss/imgs/open_eye.png)');
                        } else { 
                            $(this).siblings('input').attr('type', 'password');
                            $(this).css('background-image', 'url(/scss/imgs/close_eye.png)');
                        }
                    });
                });
            },
        },
    });
} else if(url_dir == 'page' && url_page.indexOf('capabilitys') != -1){
    let count = 0;
    send_ajax('/engine/settings', {'getSettings': 'fitchas'});
    var vue2 = new Vue({ 
        el: '.vue_el2',
        data: {
            fitchas: {},
        },
        methods: {
            chet(){
                count += 1
                if(count % 2 == 0) return true;
                else return false
            },
        },
    });
} else if(url_dir == 'page' && url_page.indexOf('contacts') != -1){
    function send_form(){
        event.preventDefault();
        $('.preloader').css('display', 'block');
        $('.contact_from_btn').css('display', 'none');
        $.when(send_ajax('/engine/contact_mail', $(event.target).serialize())).done(function() {
            $('.preloader').css('display', 'none');
            $('.contact_from_btn').css('display', 'block');
        });
    }
} else if(url_dir == 'page' && url_page.indexOf('help') != -1){
    var time;
    send_ajax('/engine/settings', {'getSettings': 'problems'});
    if(localStorage.getItem('help_form_status')){
        var cache_time = new Date(localStorage.getItem('help_form_status'));
        cache_time.setMinutes(cache_time.getMinutes() + 5);
        let today = new Date();
        if(cache_time > today) time = cache_time.getHours() + ':' + cache_time.getMinutes() + ':' + cache_time.getSeconds();
    }
    var res_count_num = 0;
    var vue4 = new Vue({ 
        el: '#help_section',
        data: {
            mail: null,
            message: null,
            load: false,
            time: time,
            search_text: null,
            faq: {},
            search_res: {},
        },
        methods: {
            send(){
                let mail = vue4.mail;
                let message = vue4.message;
                if(mail && message){
                    if(vue4.validateEmail(mail)){
                        if(message.length > 10){
                            let today = new Date();
                            let cache_time;
                            if(window.localStorage.getItem('help_form_status')){
                                cache_time = new Date(window.localStorage.getItem('help_form_status'));
                                cache_time.setMinutes(cache_time.getMinutes() + 5);
                            }
                            if(cache_time <= today || !cache_time){
                                cache_time = today;
                                cache_time.setMinutes(cache_time.getMinutes() + 5);
                                vue4.time = cache_time.getHours() + ':' + cache_time.getMinutes() + ':' + cache_time.getSeconds();
                                vue4.load = true;
                                $.when(send_ajax('/engine/contact_mail', {'email': mail, 'message': message})).done(function() {
                                    window.localStorage.setItem('help_form_status', today); 
                                    vue4.load = true;
                                });
                            } else alert("Вы уже отправили форму обратной связи ! Ждите ответ на электронную почту !", 'error');
                        } else alert('Опишите проблему подробнее !', 'error');
                    } else alert('Введите почту правильно !', 'error');
                } else alert('Заполнены не все поля !', 'error');
            },
            validateEmail(email) {
                var re = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
                return re.test(String(email).toLowerCase());
            },
            res_count(){
                res_count_num += 0.25;
                return res_count_num;
            },
            open_ul(){
                let ul_list = $(event.target).siblings('ul');
                let parent = $(event.target).closest('ul');
                if(ul_list.css('max-height') == '0px'){ 
                    ul_list.css('max-height', "none"); 
                    parent.removeClass('close');
                    parent.addClass('open');
                } else { 
                    ul_list.css('max-height', 0);
                    parent.removeClass('open');
                    parent.addClass('close');
                }
            }
        },
        watch: {
            search_text(some){
                res_count_num = 0;
                if(some){
                    some = String(some).toLowerCase();
                    vue4.search_res = {};
                    for(group in vue4.faq){
                        for(elem_index in vue4.faq[group]){
                            if((elem_index.indexOf(some) != -1 || ((vue4.faq[group][elem_index]["info"]||{'answer':null}).answer||'').indexOf(some) != -1) && vue4.faq[group][elem_index]["type"] == 0){
                                let elem_id = elem_index.replaceAll(' ', '%20');
                                vue4.search_res[elem_index] = {};
                                vue4.search_res[elem_index]["link"] = '/page/faqinfo?id='+elem_id+'&group='+group;
                            } else {         
                                for(elem_group_elem in vue4.faq[group][elem_index]["info"]["list"]){
                                    if(vue4.faq[group][elem_index]["info"]["list"][elem_group_elem]["answer"].indexOf(some) != -1 || elem_group_elem.indexOf(some) != -1){ 
                                        let elem_id = elem_index.replaceAll(' ', '%20');
                                        elem_group_elem2 = elem_group_elem.replaceAll(' ', '%20');
                                        vue4.search_res[elem_group_elem] = {};   
                                        vue4.search_res[elem_group_elem]["link"] = '/page/faqinfo?id='+elem_id+'&list_id='+elem_group_elem2+'&group='+group;
                                    }
                                }
                            }
                        }
                    }
                } else vue4.search_res = {};
            },
        },
    });
} else if(url_dir == 'page' && url_page == 'tariffs'){
    send_ajax('/engine/settings', {'getSettings': 'editions'});
    let count = 0;
    var vue = new Vue({ 
        el: '.vue_el2',
        data: {
            editions: {},
            count:0,
        },
        methods: {
            tcount(){
                count += 0.1;
                return count + 's';
            }
        },
        mounted: function(){
            $('.tariff_row:not(:first-child)').scroll(function(e) { $('.tariff_row:first-child').scrollLeft($(this).scrollLeft()); });
        },
    });
} else if(url_dir == 'page' && url_page.indexOf('tariff') != -1){
    send_ajax('/engine/settings', {'getSettings': 'editions'});
    var vue = new Vue({ 
        el: '.vue_el1',
        data: {
            editions: {},
        },
    });
} else if(url_dir == 'page' && url_page.indexOf('reviews') != -1){
    send_ajax('/engine/settings', {'getSettings': 'reviews'});
    send_ajax('/engine/settings', {'getSettings': 'review'});
    var vue3 = new Vue({ 
		el: '#reviews_section',
		data: {
			reviews: {},
			rating: 0,
			review: null,
			name: null,
			link: null,
			photo: null,
			agreement: false,
			loader:false,
		},
		methods: {
			add_review(){
				if(vue3.agreement){
					var formData = new FormData($('.add_review')[0]);
					formData_send_ajax('/engine/settings', formData);
				} else alert('Требуется согласие на обработку персональных данных!', 'error');
			},
			save_review(){
				var formData = new FormData($('.add_review')[0]);
				formData_send_ajax('/engine/settings', formData);
			},
			remove_review(){
				send_ajax('/engine/settings', {'remove_review': true})
			},
			rate(x){ vue3.rating = x; },
		},
	});
} else if(url_dir == 'pages' && url_page.indexOf('domains') != -1) send_ajax('/engine/settings', {'getSettings': 'domains'});
else if(url_dir == 'pages' && url_page.indexOf('statistic') != -1) send_ajax('/engine/settings', {'getSettings': 'statistic'});
else if(url_dir == 'pages' && url_page.indexOf('anticlicker') != -1){
    var loaded_users = 0;
    send_ajax('/engine/settings', {'getSettings': 'anticlicker'});
    var vue = new Vue({ 
        el: '#container',
        data: {
            autoban_enabled: false,
            redirect_enabled: true,
            adds_trys: 0,
            adds_redirected: 0,
            adds_banned: 0,
            list: {},
            load_count: 0,
        },
        updated(){ loaded_users = -1; },
        methods: {
            btns(name){
                let value = '';
                if(name == 'adds_redirect' && vue.redirect_enabled == true) vue.autoban_enabled = false;
                if(name == 'adds_autoban' && vue.autoban_enabled == true) vue.redirect_enabled = false;
                if(name == 'adds_trys') value = vue.adds_trys;
                let array = {};
                array["name"] = name;
                array["value"] = value;
                array = JSON.stringify(array);
                send_ajax('/engine/settings', {'adds': array});
            },
            check_count(){
                if(Object.keys(this.list).length > this.load_count) return true;
                else return false;
            },
            load_more(){ loaded_users = -1; vue.load_count += 50; },
            sort_mas(array){
                let bunned = [];
                let leeds = [];
                flag = false;
                for(user in array){
                    array[user]["ip"] = user;
                    if(loaded_users >= this.load_count) flag = true;
                    if(array[user].count >= this.adds_trys) bunned.push(array[user]);
                    else if(!flag){ 
                        leeds.push(array[user]);
                        loaded_users++;
                    }
                }
                bunned.sort((a, b) => a.count < b.count ? 1 : -1);
                leeds.sort((a, b) => a.count < b.count ? 1 : -1);
                return bunned.concat(leeds);
            },
        },
    });
    vue.load_more();
} else if(url_dir == 'pages' && url_page.indexOf('dialogs') != -1) send_ajax('/engine/settings', {'getSettings': 'dialogs'});
else if(url_dir == 'pages' && url_page.indexOf('dialog') != -1) send_ajax('/engine/settings', {'getSettings': 'dialog'});
else if(url_dir == 'pages' && url_page.indexOf('options') != -1) send_ajax('/engine/settings', {'getSettings': 'options'});
else if(url_dir == 'pages' && url_page.indexOf('offline') != -1){ // OFFLINE 1
    send_ajax('/engine/settings', {'getSettings': 'offline'});
    var vue = new Vue({ 
        el: '#column1',
        data: {
            feedback_target_email: null,
            feedback_form_checkbox: false,
            feedback_input_checkbox_1: false,
            feedback_input_checkbox_2: false,
            feedback_input_checkbox_3: false,
            feedback_text: null,
        },
        methods: {
            change(type){
                let res = {}; res[type] = $(event.target).val();
                send_ajax('/engine/settings', res);
            },
            change_btn(type){
                vue[type] = !vue[type]; let res = {};
                if(vue[type]) res[type] = 'checked';
                else res[type] = 'unchecked';
                send_ajax('/engine/settings', res);
            },
        },
    });
} else if(url_dir == 'pages' && url_page.indexOf('feedback') != -1) send_ajax('/engine/settings', {'getSettings': 'feedback'}); // OFFLINE 2
else if(url_dir == 'pages' && url_page.indexOf('departaments') != -1) send_ajax('/engine/settings', {'getSettings': 'departaments'});  
else if(url_dir == 'pages' && url_page.indexOf('design') != -1){
    send_ajax('/engine/settings', {'getSettings': 'design'});
	var vue = new Vue({ 
        el: '#container',
        data: {
            positions: ['first_position', 'second_position', 'third_position', 'fourth_position', 'fith_position', 'sixth_position', 'seventh_position', 'eighth_position', 'nineth_position', 'tenth_position', 'eleven_position', 'twelve_position', 'special1_position', 'special2_position', 'special3_position', 'special4_position', 'hidden_position'],
            PersonalSize: false,
            chat_status_checkbox: false,
            active_position: null,
            mobile_active_position: null,
            sizes: {
                'btn_svg_size': {'text': 'Размер картинки на кнопкe', 'value': 30},
                'helper_btn_height': {'text': 'Высота кнопки', 'value': 40},
                'helper_btn_width': {'text': 'Ширина кнопки', 'value': 260},
                'helper_btn_font': {'text': 'Размер шрифта кнопки', 'value': 22},
                'helper_btn_font_weigt': {'text': 'Жирность шрифта кнопки', 'value': 700},
                'chatheader_font_size': {'text': 'Размер шрифта шапки чата', 'value': 30},
                'chatheader_font_weight': {'text': 'Жирность шрифта шапки чата', 'value': 900},
                'msg_font_size': {'text': 'Размер шрифта сообщений', 'value': 13},
                'msg_font_weight': {'text': 'Жирность шрифта сообщений', 'value': 100},
            },
            status_colors: {
                'StatusOfflinecolor': {'text': 'Цвет для статуса оффлайн', 'value': '#eb2f1e'},
                'StatusOnlinecolor': {'text': 'Цвет для статуса онлайн', 'value': '#0de761'},
            },
            colors: {
                "Дизайн для кнопки": {
                    'bgcolor': {'text': 'Цвет кнопки', 'value': '#333333'},
                    'textcolor': {'text': 'Цвет текста кнопки', 'value': '#eeeeee'},
                    'button_shadow': {'text': 'Цвет тени кнопки', 'value': 'rgba(255,255,255,0.5)'},
                    'logobgcolor': {'text': 'Цвет картинки на кнопке', 'value': '#00aaee'},
                    'logodetailscolor': {'text': 'Цвет деталей картинки на кнопке', 'value': '#eeeeee'},
                    'SvgColor': {'text': 'Цвет деталей', 'value': '#00aaee', 'about': 'Цвет деталей, таких как: кнопка закрытия чата, кнопка отправки сообщения, значок статуса чата ( если он отключен ), кнопка закрытия уведомления'},
                },
                "Дизайн для окна чата": {
                    'UmessagesColor': {'text': 'Цвет сообщений посетителя', 'value': '#00aaee'},
                    'AmessagesColor': {'text': 'Цвет сообщений ассистента', 'value': '#444444'},
                    'FrameColor': {'text': 'Цвет рамки окна', 'value': '#222222'},
                    'windowbgcolor': {'text': 'Цвет окна', 'value': '#333333'},
                    'windowtextcolor': {'text': 'Цвет текста окна', 'value': '#eeeeee'},
                    'window_shadow': {'text': 'Цвет тени окна', 'value': 'rgba(0,0,0,0.5)'},
                    'scroll_color': {'text': 'Цвет полосы прокрутки', 'value': '#0ae'},
                    'success_color': {'text': 'Цвет сообщений об ошибке', 'value': '#1bb154'},
                    'error_color': {'text': 'Цвет сообщений об успехе', 'value': '#c0392b'},
                },
                "Дизайн для уведомлений пользователя": {
                    'modal_message_text_color': {'text': 'Цвет текста уведомления', 'value': '#eeeeee'},
                    'modal_message_color': {'text': 'Цвет уведомления', 'value': '#444444'},
                    'modal_message_shadow_color': {'text': 'Цвет тени уведомления', 'value': '#eeeeee'},
                },
            },
            domains: [],
            selected_domain: 'deffault',
            design: {},
        },
        methods: {
            new_position(position){
                vue.active_position = position;
                send_ajax('/engine/settings', {'InterHelper_button_position': position, 'design_domain': vue.selected_domain});
                vue.$set(vue.design[vue.selected_domain], 'position_type', position);
            },
            new_mobile_position(position){
                vue.mobile_active_position = position;
                send_ajax('/engine/settings', {'InterHelper_button_position': position, 'mobile': true, 'design_domain': vue.selected_domain});
                vue.$set(vue.design[vue.selected_domain], 'mobile_position_type', position);
            },
            status(type){
                vue[type] = !vue[type]; let res = {'design_domain': vue.selected_domain};
                if(vue[type]) res[type] = 'checked';
                else res[type] = 'unchecked';
                send_ajax('/engine/settings', res);
                vue.$set(vue.design[vue.selected_domain], type, res[type]);
            },
            change(global_type, type){
                let value = $(event.target).val();
                if(global_type == 'sizes') send_ajax('/engine/settings', {'personal_sizes': JSON.stringify({"type": type, "value": value}), 'design_domain': vue.selected_domain});
                else if(global_type == 'design') send_ajax('/engine/settings', {'interhelper_design_option_name': type,'interhelper_design_option_val':  value, 'design_domain': vue.selected_domain});
                vue.$set(vue.design[vue.selected_domain], type, value);
            },
        },
        watch: {
            selected_domain(some){
                let design = vue.design[some];
                vue.sizes['btn_svg_size']['value'] = design['btn_svg_size'];
                vue.sizes['helper_btn_height']['value'] = design['helper_btn_height'];
                vue.sizes['helper_btn_width']['value'] = design['helper_btn_width'];
                vue.sizes['helper_btn_font']['value'] = design['helper_btn_font'];
                vue.sizes['helper_btn_font_weigt']['value'] = design['helper_btn_font_weigt'];
                vue.sizes['chatheader_font_size']['value'] = design['chatheader_font_size'];
                vue.sizes['chatheader_font_weight']['value'] = design['chatheader_font_weight'];
                vue.sizes['msg_font_size']['value'] = design['msg_font_size'];
                vue.sizes['msg_font_weight']['value'] = design['msg_font_weight'];
                vue.status_colors['StatusOfflinecolor']['value'] = design['StatusOfflinecolor'];
                vue.status_colors['StatusOnlinecolor']['value'] = design['StatusOnlinecolor'];
                vue.colors['Дизайн для кнопки']['bgcolor']['value'] = design['bgcolor'];
                vue.colors['Дизайн для кнопки']['textcolor']['value'] = design['textcolor'];
                vue.colors['Дизайн для кнопки']['button_shadow']['value'] = design['button_shadow'];
                vue.colors['Дизайн для кнопки']['logobgcolor']['value'] = design['logobgcolor'];
                vue.colors['Дизайн для кнопки']['logodetailscolor']['value'] = design['logodetailscolor'];
                vue.colors['Дизайн для кнопки']['SvgColor']['value'] = design['SvgColor'];
                vue.colors['Дизайн для уведомлений пользователя']['modal_message_text_color']['value'] = design['modal_message_text_color'];
                vue.colors['Дизайн для уведомлений пользователя']['modal_message_color']['value'] = design['modal_message_color'];
                vue.colors['Дизайн для уведомлений пользователя']['modal_message_shadow_color']['value'] = design['modal_message_shadow_color'];
                vue.colors['Дизайн для окна чата']['UmessagesColor']['value'] = design['UmessagesColor'];
                vue.colors['Дизайн для окна чата']['AmessagesColor']['value'] = design['AmessagesColor'];
                vue.colors['Дизайн для окна чата']['FrameColor']['value'] = design['FrameColor'];
                vue.colors['Дизайн для окна чата']['windowbgcolor']['value'] = design['windowbgcolor'];
                vue.colors['Дизайн для окна чата']['windowtextcolor']['value'] = design['windowtextcolor'];
                vue.colors['Дизайн для окна чата']['window_shadow']['value'] = design['window_shadow'];
                vue.colors['Дизайн для окна чата']['scroll_color']['value'] = design['scroll_color'];
                vue.colors['Дизайн для окна чата']['success_color']['value'] = design['success_color'];
                vue.colors['Дизайн для окна чата']['error_color']['value'] = design['error_color'];
                vue.PersonalSize = (design['PersonalSize'] == 'checked');
                vue.chat_status_checkbox = (design['chat_status_checkbox'] == 'checked');
                vue.active_position = design['position_type'];
                vue.mobile_active_position = design['mobile_position_type'];
            },
        }
    });
} else if(url_dir == 'pages' && url_page.indexOf('tariff') != -1){ 
    send_ajax('/engine/settings', {'getSettings': 'tariff'});
    var vue = new Vue({ 
        el: '#column1',
        data: {
            editions: {},
            banned_count: 0,
            assistents_count: 0,
            crm_tasks_count: 0,
            departaments: 0,
            items_count: 0,
            tables: 0,
            columns:0,
            domains: 0,
            money: 0,
            payday: "Не оплачен",
            uip_count: 0,
            unused: 0,
            tariff: 'Стартовый',
        },
        methods: {
            change_tariff(index){
                let access = confirm('Вы уверены, что хотите сменить тариф ?');
                if(access) send_ajax('/engine/settings', {'select_tariff': index});
            },
        },
    });
} else if(url_dir == 'pages' && url_page.indexOf('autosender') != -1){
    send_ajax('/engine/settings', {'getSettings': 'autosender'});
    let mounth_ago = new Date();
    mounth_ago.setMonth(mounth_ago.getMonth() - 1);
    let today = new Date();
    var vue = new Vue({ 
        el: '#container',
        data: {
            new_notification: { 
                status: false,
                type: null,
                type_list: false,
                condition_list: false,
                conditions: {},
                sender: null,
                departament: null,
                photo: null,
                text: null,
                files: [], 
                name: null,
            },
            conditions: {
                "use_once": {
                    'text': 'Не повторять (кэшировать)',
                    'input_status': false,
                },
                "desktop":{
                    'text': 'Только для компьютеров',
                    'input_status': false,
                    'uncheck': 'mobile',
                },
                "save":{
                    'text': 'Сохранять уведомление',
                    'input_status': false,
                },
                "mobile":{
                    'text': 'Только для мобильных устройств',
                    'input_status': false,
                    'uncheck': 'desktop'
                },
                "time":{
                    'input_status': true,
                    'text': 'Промежуток времени',
                    'input_type': 'time'
                },
                "open_counter":{
                    'input_status': true,
                    'text': 'Количество посещений',
                    'input_type': 'number'
                },
                "activity_time":{
                    'input_status': true,
                    'text': 'Время на сайте',
                    'input_type': 'time'
                },
                "link":{
                    'input_status': true,
                    'text': 'Ссылка включает',
                    'input_type': 'text'
                },
                "open":{
                    'input_status': false,
                    'text': 'При открытии чата',
                },
                "close":{
                    'input_status': false,
                    'text': 'При закрытии чата',
                },
                // "msg_input":{
                //     'input_status': false,
                //     'text': 'При вводе сообщения',
                // },
                "msg_send":{
                    'input_status': false,
                    'text': 'При отправке сообщения',
                },
                "consultant_offline":{
                    'input_status': false,
                    'text': 'Ассистенты вне сети',
                    'uncheck': 'consultant_online',
                },
                "consultant_online":{
                    'input_status': false,
                    'text': 'Ассистенты в сети',
                    'uncheck': 'consultant_offline',
                },
                "personal_event":{
                    'input_status': true,
                    'text': 'Своё событие',
                    'input_type': 'text',
                    'placeholder1': 'Объект (#e,.e,e)',
                    'placeholder2': 'Ивент (click)',
                },
            },
            notifications: {},
            emojis: {},
            regexp: {},
            smiles_mode: false,
            fastmessages: {
                "search_chapter": '',
                "search_message": '',
                "selected_chapter": 'main',
                "chapters_mode": false,
                'chapters': {"main": {}},
            },
            commands_mode: false,
            from: mounth_ago,
            to: today,
        },
        methods: {
            create_chart(id, openBY, openRazdel){
                if(vue.notifications[id]){
                    if(vue.notifications[id]["chart_settings"] && vue.notifications[id]["chart"] && ((vue.notifications[id]["chart_settings"]||{})['openBY'] != openBY || (vue.notifications[id]["chart_settings"] || {})['openRazdel'] != openRazdel)){ 
                        vue.notifications[id]["chart"].destroy();
                        vue.$set(vue.notifications[id]['chart_settings'], 'status', true);
                        Vue.delete(vue.notifications[id], "chart");
                        vue.$set(vue.notifications[id]["chart_settings"], 'openRazdel', openRazdel); 
                        vue.$set(vue.notifications[id]["chart_settings"], 'openBY', openBY); 
                    }
                }
                var ctx = $('#myChart_'+id);
                let today = new Date();
                let events;
                let month_ago = new Date(); month_ago.setMonth(month_ago.getMonth() - 1);
                if(!vue.notifications[id]["chart_settings"]) vue.$set(vue.notifications[id], "chart_settings", {
                    "type": 'bar',
                    "from": month_ago,
                    "to": today,
                    "date_type": 'days',
                    'search_for': 'notifications',
                    "status": true,
                    'openBY': openBY,
                    'openRazdel': openRazdel,
                });
                events = vue.notifications[id]["statistic"]['statistic'];
                let chart_settings = vue.notifications[id]["chart_settings"];
                vue.$set(vue.notifications[id], "chart",  new Chart(ctx[0], {
                    type: chart_settings.type,
                    data: {
                        datasets: getStatistic(events, chart_settings.date_type, chart_settings.from, chart_settings.to, chart_settings.search_for),
                    },
                }));
                console.log(vue.notifications);
            },
            chart_update(id, type){
                vue.notifications[id]["chart"].destroy();
                let value = event.target.value;
                if(type == "from" || type == "to") value = new Date(value);
                vue.$set(vue.notifications[id]["chart_settings"], type, value);
                vue.create_chart(id, vue.notifications[id]["chart_settings"]['openBY'], vue.notifications[id]["chart_settings"]['openRazdel']);
            },
            chart_model(id, type){  
                vue.notifications[id]["chart"].destroy();
                vue.$set(vue.notifications[id]["chart_settings"], "type", type);
                vue.create_chart(id, vue.notifications[id]["chart_settings"]['openBY'], vue.notifications[id]["chart_settings"]['openRazdel']);
            },
            get_canvas_height(id){
                return $('#myChart_'+id).height()+80;
            },
            get_time(time){
                if(!time) return;
                return time.toISOString().split('T')[0];
            },
            create_chapter(type){
                if(type == 'new') vue.$set(vue.fastmessages, 'create_chapter', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages, 'create_chapter');
                else if(type == 'save'){
                    let value = vue.fastmessages.create_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_chapter", "fastMessages_value": value });
                    Vue.delete(vue.fastmessages, 'create_chapter');
                }
            },
            newfastmessage(type){
                if(type == 'new') vue.$set(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                else if(type == 'save'){
                    let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter].create_fastmessage;
                    let column = vue.fastmessages.selected_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value}) });
                    Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                }
            },
            copy_fastmessage(uid){
                $('.fast_message_'+uid).focus().select();
                document.execCommand('copy');
            },
            remove_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                send_ajax('/engine/settings', {"fastMessages_type": "remove_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": uid}) });
                Vue.delete(vue.fastmessages.chapters[column], uid);
            },
            update_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter][uid];
                send_ajax('/engine/settings', {"fastMessages_type": "save_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value, "uid": uid}) });
            },
            remove_chapter(uid){
                send_ajax('/engine/settings', {"fastMessages_type": "remove_chapter", "fastMessages_value": uid });
                if(uid == vue.fastmessages.selected_chapter) vue.$set(vue.fastmessages, 'selected_chapter', 'main');
                Vue.delete(vue.fastmessages.chapters, uid);
            },
            update_chapter(){
                let chapter = vue.fastmessages.selected_chapter;
                let chapter_name = vue.fastmessages.chapters[chapter].chapter_name;
                send_ajax('/engine/settings', {"fastMessages_type": "chapter_name", "fastMessages_value": JSON.stringify({"value": chapter_name, "uid": chapter})});
            },
            handleChange(type){
                let e = event.target;
                if (!e.files.length) return;    
                const files = Object.keys(e.files).map((i) => e.files[i]);
                if(type == 'new_notification') vue.$set(vue.new_notification, 'files', vue.new_notification.files.concat(files));
                else {
                    let fd = new FormData;
                    for(i in files) fd.append('notiffication_add'+i, files[i]);
                    fd.append("notification_id", type);
                    fd.append("notification_type", 'adds');
                    formData_send_ajax('/engine/settings', fd);
                }
                $(e).val('');
            },
            addNotification(){
                let fd = new FormData;
                let message = $('.chat_block_textarea[data-textplace=new_notification]').html();
                var container = $('<div>').html(message);
                container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                message = container.html();
                message = $("<div/>").html(message).text();
                vue.$set(vue.new_notification, 'text', message);
                fd.append('notification_photo', vue.new_notification.photo);
                fd.append('notification_departament', vue.new_notification.departament);
                fd.append('notification_sender', vue.new_notification.sender);
                fd.append('notification_text', vue.new_notification.text);
                fd.append('notification_conditions', JSON.stringify(vue.new_notification.conditions));
                fd.append('notification_type', vue.new_notification.type);
                fd.append('notification_name', vue.new_notification.name);
                for(file in vue.new_notification.files) fd.append('notification_add'+file, vue.new_notification.files[file]);;
                formData_send_ajax('/engine/settings', fd);
            },
            addCondition(type, condition_id){
                let el = $(event.target);
                let main = el.siblings('.not_main').val();
                let second = el.siblings('select').val();
                if(el.siblings('input').length == 2) second = el.siblings('.not_second').val();
                let condition_uid = uniqid();
                if(type == 'new'){
                    if(!vue.conditions[condition_id].input_type){ 
                        vue.$set(vue.new_notification.conditions, condition_id, condition_id);
                        if(vue.conditions[condition_id].uncheck && vue.new_notification.conditions.hasOwnProperty(vue.conditions[condition_id].uncheck)) Vue.delete(vue.new_notification.conditions, vue.conditions[condition_id].uncheck);
                    } else if((main && second ) || (main && condition_id == 'link') ) { 
                        vue.$set(vue.new_notification.conditions, condition_uid, {
                            'type': condition_id,
                            'main': main,
                            'second': second||null,
                        }); 
                        el.siblings('.not_main').val('');
                        if(el.siblings('input').length == 2)  el.siblings('.not_second').val('');
                    } else alert('Поля не заполнены !', 'error');
                } else {
                    if(!vue.conditions[condition_id].input_type){ 
                        vue.$set(vue.notifications[type].conditions, condition_id, condition_id);
                        if(vue.conditions[condition_id].uncheck && vue.notifications[type].conditions.hasOwnProperty(vue.conditions[condition_id].uncheck)) Vue.delete(vue.notifications[type].conditions, vue.conditions[condition_id].uncheck);
                        send_ajax('/engine/settings', {"notification_type": 'add_condition', "notification_id": type, "notification_value": JSON.stringify({ 'type': condition_id, 'uncheck': vue.conditions[condition_id].uncheck })});
                    } else if((main && second) || (main && condition_id == 'link') ) {
                        vue.$set(vue.notifications[type].conditions, condition_uid, {
                            'type': condition_id,
                            'main': main,
                            'second': second||null,
                        });
                        el.siblings('.not_main').val('');
                        if(el.siblings('input').length == 2)  el.siblings('.not_second').val('');
                        send_ajax('/engine/settings', {"notification_type": 'add_condition', "notification_id": type, "notification_value": JSON.stringify({ 'type': condition_id, 'main': main, 'second': second||null, "uid": condition_uid })});
                    } else alert('Поля не заполнены !', 'error');
                }
            },
            removeCondition(type, condition_id){
                if(type == 'new') Vue.delete(vue.new_notification.conditions, condition_id);
                else {
                    Vue.delete(vue.notifications[type].conditions, condition_id);
                    send_ajax('/engine/settings', {"notification_type": 'remove_condition', "notification_value": condition_id, "notification_id": type});
                }
            },
            upload_photo(type){
                if(type == 'new'){ 
                    let file = $(event.target).prop('files')[0];
                    if(!file) return;
                    if(!file.type.match(/image\/(jpeg|jpg|png|gif|webp|ico|bmp|svg)/)){
                        alert( 'Не верный формат фотографии !', 'error');
                        return;
                    }
                    vue.$set(vue.new_notification, 'photo', file);
                    readURL(file, '.review_photo_placeholder');
                }
            },
            select_smile(smile, folder, smile_file){
                let target = $(event.target).parent().parent().parent().parent().siblings('.chat_footer').children('.chat_block_textarea');
                target.html(target.html()+`<img class="InterHelper_emoji" src="/emojis/${folder}/${smile_file}" alt="${smile}" />`); 
            },
            removeFile(index, type) { 
                if(type == 'new_notification') vue.new_notification.files.splice(index, 1); 
                else {
                    Vue.delete(vue.notifications[type].adds, index);
                    send_ajax('/engine/settings', {"notification_type": 'remove_add', "notification_value": index, "notification_id": type});
                }
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            update_type(type, id){
                vue.$set(vue.notifications[id], 'type_list', !vue.notifications[id].type_list)
                if(!type) return;
                vue.$set(vue.notifications[id], 'type', type);
                send_ajax('/engine/settings', {"notification_type": 'type', "notification_value": type, "notification_id": id});
            },
            notification_smiles_mode(id){ vue.$set(vue.notifications[id], 'smiles_mode', !vue.notifications[id].smiles_mode) },
            condition_list(id){ vue.$set(vue.notifications[id], 'condition_list', !vue.notifications[id].condition_list); },
            update_notification(id, type){
                if(type == 'photo'){
                    let fd = new FormData;
                    fd.append('notification_photo', $(event.target).prop('files')[0]);
                    fd.append('notification_type', 'photo');
                    fd.append('notification_id', id);
                    formData_send_ajax('/engine/settings', fd);
                } else send_ajax('/engine/settings', {"notification_type": type, "notification_value": $(event.target).val(), "notification_id": id});
            },
            remove_notification(id){
                send_ajax('/engine/settings', {"notification_type": 'remove', "notification_id": id});
                Vue.delete(vue.notifications, id);
                $(".chat_block_textarea").bind("DOMSubtreeModified", function(){ 
                    let message = $(this).html();
                    let id = $(this).data('notification_id');
                    var container = $('<div>').html(message);
                    container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                    message = container.html();
                    message = $("<div/>").html(message).text();
                    send_ajax('/engine/settings', {"notification_type": 'text', "notification_id": id, "notification_value": message});
                });
            },
            fixsring(str){ return $("<div/>").html(str).text(); },
        },
        updated: function(){
            this.$nextTick(() => {
                $(".not_message").bind("DOMSubtreeModified", function(){ 
                    let message = $(this).html();
                    let id = $(this).data('notification_id');
                    var container = $('<div>').html(message);
                    container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                    message = container.html();
                    message = $("<div/>").html(message).text();
                    send_ajax('/engine/settings', {"notification_type": 'text', "notification_id": id, "notification_value": message});
                });
            });
        },
    });
} else if(url_dir == 'pages' && url_page.indexOf('swaper') != -1)  send_ajax('/engine/settings', {'getSettings': 'swaper'});
else if(url_dir == 'pages' && url_page.indexOf('payment') != -1) {
    var vue = new Vue({ 
		el: '#column1',
		data: { 
			money: 100, 
		},
        methods: {
            sberbank(){
                send_ajax('/engine/sberbank', {'make_order': vue.money});
            },
        },
	});
} else if(url_dir == 'pages' && url_page.indexOf('assistents') != -1) send_ajax('/engine/settings', {'getSettings': 'assistents'});
else if(url_dir == 'consultant' && url_page.indexOf('assistent') != -1) send_ajax('/engine/settings', {'getSettings': 'assistent'});
else if(url_dir == 'consultant' && url_page.indexOf('crm_settings') != -1) {send_ajax('/engine/settings', {'getSettings': 'crm_settings', 'crm_type': get_reader(window.location)['type']});}
else if(url_dir == 'consultant' && url_page.indexOf('crm') != -1) send_ajax('/engine/settings', {'getSettings': 'crm', 'crm_type': get_reader(window.location)['type']});
else if(url_dir == 'consultant' && url_page.indexOf('tasks') != -1) send_ajax('/engine/settings', {'getSettings': 'tasks'});
else if(url_dir == 'consultant' && url_page.indexOf('hub') != -1) send_ajax('/engine/settings', {'getSettings': 'hub'});
else if(url_dir == 'consultant' && url_page.indexOf('banned_chat') != -1) send_ajax('/engine/settings', {'getSettings': 'banned_chat'});
else if(url_dir == 'consultant' && url_page.indexOf('command_chat') != -1) send_ajax('/engine/settings', {'getSettings': 'command_chat', 'room': get_reader(window.location)['room']});
else if(url_dir == 'consultant' && url_page.indexOf('chat') != -1) send_ajax('/engine/settings', {'getSettings': 'chat', 'room': get_reader(window.location)['room']});
else if(url_dir == 'consultant' && url_page.indexOf('forms') != -1) send_ajax('/engine/settings', {'getSettings': 'forms'});
else if(url_dir == 'consultant' && url_page.indexOf('banned') != -1) send_ajax('/engine/settings', {'getSettings': 'banned'});
else if(url_dir == 'consultant' && url_page.indexOf('command') != -1) send_ajax('/engine/settings', {'getSettings': 'command'});
else if(url_dir == 'pages' &&  url_page.indexOf('mailer') != -1) send_ajax('/engine/settings', {'getSettings': 'mailer'});
function nav_controll(nav_width_max, nav_width_min){
    let width
    if(localStorage['nav_status']){
        width = nav_width_max;
        let el = $('.nav-controll-close');
        $(el).removeClass('nav-controll-close');
        $(el).addClass('nav-controll-open');
    } else {
        width = nav_width_min;
        let el = $('.nav-controll-open');
        $(el).removeClass('nav-controll-open');
        $(el).addClass('nav-controll-close');
    }
    $('#navigation').css({
        'width':width+'px',
    });
    $('#navigation ul li p').css({
        'opacity': localStorage['nav_status'] ? 1 : 0,
    });
    $('#main').css({
        'width': 'calc(100% - '+width+'px)',
        'right':width+'px'
    });
    $('.nav-controll').unbind('click');
    $('.nav-controll').on('click', function(e){
        let width;
        if($(this).hasClass('nav-controll-close')){
            width = nav_width_max;
            localStorage['nav_status'] = true;
            $(this).removeClass('nav-controll-close');
            $(this).addClass('nav-controll-open');
        } else {
            width = nav_width_min;
            delete localStorage['nav_status'];
            $(this).removeClass('nav-controll-open');
            $(this).addClass('nav-controll-close');
        }
        $('#navigation').css({
            'width':width+'px',
        });
        $('#navigation ul li p').css({
            'opacity': $(this).hasClass('nav-controll-close') ? 0 : 1,
        });
        $('#main').css({
            'width': 'calc(100% - '+width+'px)',
            'right':width+'px'
        });
    });
}
function swaper(settings){
    let mounth_ago = new Date();
    mounth_ago.setMonth(mounth_ago.getMonth() - 1);
    let today = new Date();
    var vue = new Vue({ 
        el: '#container',
        data: {
            conditions: {
                "desktop":{
                    'text': 'Только для компьютеров',
                    'input_status': false,
                    'uncheck': 'mobile',
                },
                "mobile":{
                    'text': 'Только для мобильных устройств',
                    'input_status': false,
                    'uncheck': 'desktop'
                },
                "time":{
                    'input_status': true,
                    'text': 'Промежуток времени',
                    'input_type': 'time'
                },
                "open_counter":{
                    'input_status': true,
                    'text': 'Количество посещений',
                    'input_type': 'number'
                },
                "activity_time":{
                    'input_status': true,
                    'text': 'Время на сайте',
                    'input_type': 'time'
                },
                "link":{
                    'input_status': true,
                    'text': 'Ссылка включает',
                    'input_type': 'text'
                },
                "open":{
                    'input_status': false,
                    'text': 'При открытии чата',
                },
                "close":{
                    'input_status': false,
                    'text': 'При закрытии чата',
                },
                "msg_input":{
                    'input_status': false,
                    'text': 'При вводе сообщения',
                },
                "msg_send":{
                    'input_status': false,
                    'text': 'При отправке сообщения',
                },
                "consultant_offline":{
                    'input_status': false,
                    'text': 'Ассистенты вне сети',
                    'uncheck': 'consultant_online',
                },
                "consultant_online":{
                    'input_status': false,
                    'text': 'Ассистенты в сети',
                    'uncheck': 'consultant_offline',
                },
                "personal_event":{
                    'input_status': true,
                    'text': 'Своё событие',
                    'input_type': 'text',
                    'placeholder1': 'Объект (#e,.e,e)',
                    'placeholder2': 'Ивент (click)',
                },
            },
            settings: settings,
            new_swap: false,
            from: mounth_ago,
            to: today,
            type: 'dates',
            swap_type: null,
            swap_types:{
                "phone":{
                    "text": "Номер телефона (a[href*='tel:'])",
                    "text_from": "Подменяемый номер телефона",
                    "text_to": "Поменяющий номер телефона",
                },
                "mail": {
                    "text": "Почта (a[href*='mailto:'])",
                    "text_from": "Подменяемый почта",
                    "text_to": "Поменяющая почта",
                },
                "link": {
                    "text": "Ссылка (a)",
                    "text_from": "Подменяемая ссылка",
                    "text_to": "Поменяющая ссылка",
                    "placeholder1": "page1",
                    "placeholder2": "page2",
                },
                "text": {
                    "text": "Текст (свой тег)",
                    "text_from": "Класс/id/тег элемента",
                    "text_to": "Поменяющая ссылка",
                    "placeholder1": ".myClass / #myID / h2 / p / h1",
                    "placeholder2": "Hello world !",
                },
                "img": {
                    "text": "Фотография (img[src*=''])",
                    "text_from": "Ссылка на первую фотографию",
                    "text_to": "Ссылка на вторую фотографию",
                    "placeholder1": "/css/imgs/background1.png",
                    "placeholder2": "/css/imgs/background2.png",
                },
                "statistic": {
                    "text": "Статистика"
                },
            }, 
            swap_type_list: false,
        },
        methods: {
            escapeHtml(str){ return $("<div/>").html(str).text(); },
            discard(swap_id, utm_part_id){
                send_ajax('/engine/settings', {"swap_id": swap_id, "discard_part": utm_part_id});
                vue.$set(vue.settings[swap_id]["swap_utmparts"][utm_part_id], "results", {});
                vue.$set(vue.settings[swap_id]["swap_utmparts"][utm_part_id], "searchmas", {});
            },
            load_more(utm_part){
                if(!utm_part.load_count) vue.$set(utm_part, 'load_count', 20);
                utm_part.load_count += 10;
            },
            sort_mas(utm_part){
                let result = {};
                let load_count = utm_part.load_count||20;
                let key = 'searchmas';
                if(!utm_part[key]) key = 'results';
                for(index in utm_part[key]){
                    result[index] = utm_part[key][index];
                    if(Object.keys(result).length >= load_count) return result;
                }
                return result;
            },
            open_panel(swap, panel){
                vue.$set(swap, 'panel', panel);
            },
            add_swap(){ // добавить подмену
                let swap_id = uniqid();
                let swap_from = ($('.swap_input_from').val()||'statistic').trim();
                let swap_to = ($('.swap_input_to').val()||'statistic').trim();
                if(swap_from && swap_to && vue.swap_type){
                    send_ajax('/engine/settings', {
                        "swap_from": swap_from, 
                        "swap_to": swap_to,
                        "swap_id": swap_id, 
                        "swap_type": vue.swap_type,
                    }, vue);
                } else alert('Заполнены не все поля !', 'error');
            },
            open_utm_part(utm_part){
                vue.$set(utm_part, 'status', (!utm_part['status'] ? true : false));
            },
            addif_mode(swap, mode){
                vue.$set(swap, 'addif', mode);
                vue.$set(swap, 'addif_type', null); 
                vue.$set(swap, 'addif_condition', null);
            },
            fast_add_utm_part(swap){
                vue.$set(swap, "addutmpart", event.target.value);
            },
            add_utmpart(swap, swap_id){
                let part_id = uniqid();
                send_ajax('/engine/settings', {"swap_utmpart": swap.addutmpart, "swap_id": swap_id, "part_id": part_id});
                if(!vue.settings[swap_id]['swap_utmparts']) vue.$set(vue.settings[swap_id], 'swap_utmparts', {});
                vue.$set(vue.settings[swap_id]["swap_utmparts"], part_id, {"utm_part_name": swap.addutmpart});
            },
            add_UTMPART_mode(swap, mode){
                vue.$set(swap, 'add_utm_parts_mode', mode);
                vue.$set(swap, 'addutmpart', null); 
            },
            addif_type(swap){
                vue.$set(swap, 'addif_type', event.target.value);
                vue.$set(swap, 'addif_condition', {"main": null, "second": null});
            },
            addif_condition(swap, type){
                vue.$set(swap.addif_condition, type, event.target.value);
            },
            swap_time(swap_id){
                if(event.target.value == 'always' || event.target.value == 'never') {
                    if(!confirm('Вы уверены ? При выборе данного варианта ваши условия не будут иметь значения.')) return;
                }
                send_ajax('/engine/settings', {"swap_time": event.target.value, "swap_id": swap_id});
                vue.$set(vue.settings[swap_id], 'swap_time', event.target.value);
            },
            remove_swap(swap_id){
                send_ajax('/engine/settings', {"remove_swap": swap_id});
                Vue.delete(vue.settings, swap_id);
            },
            cache(swap_id){
                send_ajax('/engine/settings', {"swap_cache": swap_id});
            },
            swap_changename(swap_id){
                send_ajax('/engine/settings', {"swap_changename": swap_id});
            },
            change_utmpart(swap_id, utm_part_id){
                send_ajax('/engine/settings', {"utm_part": event.target.value, "swap_id": swap_id, "utm_part_id": utm_part_id});
                vue.$set(vue.settings[swap_id]["swap_utmparts"][utm_part_id], 'utm_part_name', event.target.value);
            },
            remove_utmpart(swap_id, utm_part_id){
                send_ajax('/engine/settings', {"utm_part_remove_id": utm_part_id, "swap_id": swap_id});
                Vue.delete(vue.settings[swap_id]["swap_utmparts"], utm_part_id);
            },
            change_swap(swap_id, swap_type){
                send_ajax('/engine/settings', {"swap_number": event.target.value, "swap_id": swap_id, "swap_type": swap_type});
                vue.$set(vue.settings[swap_id], swap_type, event.target.value);
            },
            getevent(target, search, key){
                if(!target) return 0;
                let counter = 0;
                let from = (target.chart_settings||{}).from;
                let to = (target.chart_settings||{}).to;
                if(!from) from = new Date().setMonth(new Date().getMonth() - 1);
                if(!to) to = new Date();
                to = new Date(to);
                from = new Date(from);
                if(key) target = target[key];
                for(year in target){
                    if(from.getFullYear() > year || year > to.getFullYear()) continue;
                    for(month in target[year]){
                        if(from.getMonth() + 1 <= parseInt(month) && parseInt(month) <= 1 + to.getMonth()){
                            for(day in target[year][month]){
                                if(from <= new Date(year+'-'+month+'-'+day) && new Date(year+'-'+month+'-'+day) <= to) counter += target[year][month][day][search]||0;
                            }
                        }
                    }
                }
                return counter;
            },
            create_chart(swap_id, openBY, openRazdel){
                if(vue.settings[swap_id]){
                    if(vue.settings[swap_id]["chart_settings"] && vue.settings[swap_id]["chart"] && ((vue.settings[swap_id]["chart_settings"]||{})['openBY'] != openBY || (vue.settings[swap_id]["chart_settings"] || {})['openRazdel'] != openRazdel)){ 
                        vue.settings[swap_id]["chart"].destroy();
                        vue.$set(vue.settings[swap_id]['chart_settings'], 'status', true);
                        Vue.delete(vue.settings[swap_id], "chart");
                        vue.$set(vue.settings[swap_id]["chart_settings"], 'openRazdel', openRazdel); 
                        vue.$set(vue.settings[swap_id]["chart_settings"], 'openBY', openBY); 
                    }
                }
                var ctx = $('#myChart_'+swap_id);
                let today = new Date();
                let events;
                let month_ago = new Date(); month_ago.setMonth(month_ago.getMonth() - 1);
                if(!vue.settings[swap_id]["chart_settings"]) vue.$set(vue.settings[swap_id], "chart_settings", {
                    "type": 'bar',
                    "from": month_ago,
                    "to": today,
                    "date_type": 'days',
                    'search_for': 'swap',
                    "status": true,
                    'openBY': openBY,
                    'openRazdel': openRazdel,
                });
                if(openBY == "MAIN_SWAP_CHART") events = vue.settings[swap_id]["events"];
                else events = vue.settings[swap_id]['swap_utmparts'][openRazdel]["results"][openBY];
                let chart_settings = vue.settings[swap_id]["chart_settings"];
                vue.$set(vue.settings[swap_id], "chart",  new Chart(ctx, {
                    type: chart_settings.type,
                    data: {
                        datasets: getStatistic(events, chart_settings.date_type, chart_settings.from, chart_settings.to, chart_settings.search_for),
                    },
                }));
            },
            chart_update(swap_id, type){
                vue.settings[swap_id]["chart"].destroy();
                let value = event.target.value;
                if(type == "from" || type == "to") value = new Date(value);
                vue.$set(vue.settings[swap_id]["chart_settings"], type, value);
                vue.create_chart(swap_id, vue.settings[swap_id]["chart_settings"]['openBY'], vue.settings[swap_id]["chart_settings"]['openRazdel']);
            },
            chart_model(swap_id, type){  
                vue.settings[swap_id]["chart"].destroy();
                vue.$set(vue.settings[swap_id]["chart_settings"], "type", type);
                vue.create_chart(swap_id, vue.settings[swap_id]["chart_settings"]['openBY'], vue.settings[swap_id]["chart_settings"]['openRazdel']);
            },
            get_canvas_height(swap_id){
                return $('#myChart_'+swap_id).height()+80;
            },
            get_time(time){
                if(!time) return;
                return time.toISOString().split('T')[0];
            },
            remove_utmpart_inner(swap_id, utm_part_id, index){
                send_ajax('/engine/settings', {"utm_inner_part_remove_id": index, "swap_id": swap_id, "utm_part_id": utm_part_id});
                Vue.delete(vue.settings[swap_id]["swap_utmparts"][utm_part_id]['results'], index);
            },
            open_swapif(swap){
                vue.$set(swap, 'status', (!swap['status'] ? true : false));
            },
            addCondition(type, condition_id){
                if(!vue.settings[type].hasOwnProperty('swap_if')) vue.$set(vue.settings[type], 'swap_if', {});
                let el = $(event.target); let main = el.siblings('.not_main').val(); let second = el.siblings('select').val();
                if(el.siblings('input').length == 2) second = el.siblings('.not_second').val();
                let condition_uid = uniqid();
                if(!vue.conditions[condition_id].input_type){ 
                    vue.$set(vue.settings[type].swap_if, condition_id, condition_id);
                    if(vue.conditions[condition_id].uncheck && vue.settings[type].swap_if.hasOwnProperty(vue.conditions[condition_id].uncheck)) Vue.delete(vue.settings[type].swap_if, vue.conditions[condition_id].uncheck);
                    send_ajax('/engine/settings', {
                        "swap_id": type, 
                        "type": condition_id,
                        'uncheck': vue.conditions[condition_id].uncheck
                    });
                } else if((main && second) || (main && condition_id == 'link') ) {
                    vue.$set(vue.settings[type].swap_if, condition_uid, {
                        'type': condition_id,
                        'main': main,
                        'second': second||null,
                    });
                    el.siblings('.not_main').val('');
                    if(el.siblings('input').length == 2)  el.siblings('.not_second').val('');
                    send_ajax('/engine/settings', {
                        "main_condition": main, 
                        "second_condition": second||null, 
                        "type": condition_id, 
                        "swap_id": type, 
                        "uid": condition_uid
                    });
                } else alert('Поля не заполнены !', 'error');
            },
            removeCondition(type, condition_id){
                Vue.delete(vue.settings[type].swap_if, condition_id);
                send_ajax('/engine/settings', {"remove_condition": condition_id, "swap_id": type});
            },
            condition_list(id){ vue.$set(vue.settings[id], 'condition_list', !vue.settings[id].condition_list); },
        },
    });
}
function formData_send_ajax(path, formData, vue_component){
	$.ajax({
		type: 'POST',
		url: path,
		data: formData,
        processData: false,
        contentType: false,
		success: function(data) {
			data = JSON.parse(data);
			if(data.errors.length > 0) {
                for(error_key in data.errors) alert(data.errors[error_key], 'error');
            }
            if(Object.keys(data.success).length > 0){
                if(data['success'].hasOwnProperty('photo')){
                    if(url_dir == 'pages' && url_page.indexOf('profile') != -1) vue.photo = data['success']['photo'];
                    if(url_dir == 'consultant' && url_page.indexOf('crm_settings') != -1 && data.errors.length == 0){
                        vue_component.$delete(vue_component.columns, vue_component.selected_column);    
                        vue_component.$set(vue_component.columns, data.success.column_index||formData.get('column_index'), {
                            "type": formData.get('type')||formData.get('save_type'), 
                            "deffault": data['success']['photo']||'folder.png',
                            "variants": null, 
                            'priority': formData.get('priority'),
                            "helper_column_name": formData.get('header')||formData.get('save_header'),
                        });    
                    } 
                }
                if(data.success.hasOwnProperty('create_notification')){
					let notif = data.success['create_notification'];
                    if(Object.keys(vue.notifications).length == 0) vue.$set(vue, 'notifications', {});
					vue.$set(vue.notifications, notif.uid, {
						"photo": notif.photo,
						"adds": notif.adds,
						"sender": vue.new_notification.sender,
						"name": vue.new_notification.name,
						"departament": vue.new_notification.departament,
						"text": vue.new_notification.text,
						"conditions": vue.new_notification.conditions,
						"type": vue.new_notification.type,

					});
					vue.new_notification = {
						status: false,
						type: null,
						type_list: false,
						condition_list: false,
						conditions: {
							'conditions': [],
							'inp_conditions': {},
						},
						text: null,
						sender: null,
						departament: null,
						photo: null,
						files: [], 
					};
					$(".chat_block_textarea").bind("DOMSubtreeModified", function(){ 
                        let message = $(this).html();
                        let id = $(this).data('notification_id');
                        var container = $('<div>').html(message);
                        container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                        message = container.html();
                        message = $("<div/>").html(message).text();
                        send_ajax('/engine/settings', {"notification_type": 'text', "notification_id": id, "notification_value": message});
                    });
				}
				if(data.success.hasOwnProperty('notification_photo')) vue.$set(vue.notifications[data.success['notification_id']], 'photo', data.success['notification_photo']);
				if(data.success.hasOwnProperty('new_notification_adds')) vue.$set(vue.notifications[data.success['notification_id']], 'adds', data.success['new_notification_adds']);
                if(data.success.hasOwnProperty('response')){ 
                    if(data.success.hasOwnProperty('link')){ alert(data.success['response'], 'success', data.success['link']); }
                    else alert(data.success['response'], 'success');
                } else if(data.success.hasOwnProperty('link')) window.location.href = data.success['link'];
                if(data.success.hasOwnProperty('loader') && vue_component) vue_component.loader = false;
                if(data.success.hasOwnProperty('reload')) location.reload();
            }
            if(vue){ 
				if(vue.loader) vue.loader = false; 
			}
		},
		error: function() { console.log('Ошибка в ajax запросе! '); }
	});
}
function sort_array(header, type, el, vue){
	reverse = false;
	if(type == 'unknown'){
		type = vue.columns[header].type;
		if(type == 3) type = 'date';
		else if(type == 4) type = 'local_date';
		else if(type == 5) type = 'valute';
		else type = 'multiply';
	}
	if(el.hasClass('unactive_sort_btn')){
		el.removeClass('unactive_sort_btn');
		el.addClass('active_sort_btn');
	} else {
		reverse = true;
		el.removeClass('active_sort_btn');
		el.addClass('unactive_sort_btn');
	}
	let new_array = {}; let fast_array = []; let array = vue.searchmas;
	for(key in array){ array[key]['header'] = key; fast_array.push(array[key]); } 
	sortByPriority(fast_array, type, header, reverse, vue);
	for(key in fast_array){
		let header = fast_array[key]['header'];
		delete fast_array[key]['header'];
		new_array[header] = fast_array[key];
	}
	vue.searchmas = new_array;
}
function options_page(domains, design){
    var vue = new Vue({ 
        el: '#middle_part',
        data: {
            graphic_status: (design['deffault']['InterHelperInvitesOptions']['graphic_invite_status'] == 'checked'),
            audio_status: (design['deffault']['InterHelperInvitesOptions']['audio_invite_status'] == 'checked'),
            first_msg: htmldecoder(design['deffault']['SYSFmessage']),
            sys_name: htmldecoder(design['deffault']['SYSname']),
            SYSname_offline: htmldecoder(design['deffault']['SYSname_offline']),
            domains: domains,
            selected_domain: 'deffault',
            msgs_email: htmldecoder(design['deffault']['msgs_email']),
            email_msgs_status: (design['deffault']['email_msgs_status'] == 'checked'),
        },
        methods:{
            change(name){
                let res = {'design_domain': vue.selected_domain}; res[name] = $(event.target).val();
                send_ajax('/engine/settings', res);
                if(name == 'sys_name') name = 'SYSname';
                else if(name == 'helper_fmessage') name = 'SYSname';    
                design[vue.selected_domain][name] = $(event.target).val();
            },
            change_graphic_status(){
                vue.graphic_status = !vue.graphic_status;
                let some = vue.graphic_status;
                if(some) some = 'checked';
                else some = 'unchecked';
                send_ajax('/engine/settings', {'notification_graphic_checkbox': some, 'design_domain': vue.selected_domain});
                design[vue.selected_domain]['InterHelperInvitesOptions']['graphic_invite_status'] = some;
            },
            change_audio_status(){
                vue.audio_status = !vue.audio_status;
                let some = vue.audio_status;
                if(some) some = 'checked';
                else some = 'unchecked';
                send_ajax('/engine/settings', {'notification_audio_checkbox': some, 'design_domain': vue.selected_domain});
                design[vue.selected_domain]['InterHelperInvitesOptions']['audio_invite_status'] = some;
            },
            change_mail_status(){
                vue.email_msgs_status = !vue.email_msgs_status;
                let some = vue.email_msgs_status;
                if(some) some = 'checked';
                else some = 'unchecked';
                send_ajax('/engine/settings', {'email_msgs_status': some, 'design_domain': vue.selected_domain});
                design[vue.selected_domain]['InterHelperInvitesOptions']['audio_invite_status'] = some;
            },
        },
        watch:{
            selected_domain(some){
                vue.graphic_status = (design[some]['InterHelperInvitesOptions']['graphic_invite_status'] == 'checked');
                vue.audio_status = (design[some]['InterHelperInvitesOptions']['audio_invite_status'] == 'checked');
                vue.first_msg = htmldecoder(design[some]['SYSFmessage']);
                vue.sys_name = htmldecoder(design[some]['SYSname']);
                vue.SYSname_offline = htmldecoder(design[some]['SYSname_offline']);
                vue.msgs_email = htmldecoder(design[some]['msgs_email']);
                vue.msgs_email_status = (design['deffault']['email_msgs_status'] == 'checked');
            }
        }
    });
}
function send_ajax(path, array, vue_component){
	$.ajax({
		type: 'POST',
		url: path,
		data: array,
		success: function(data) {
			data = JSON.parse(data);
			if(data.errors.length > 0) {
                for(error_key in data.errors) alert(data.errors[error_key], 'error');
            }
            if(array['getSettings'] == 'departaments') departaments_page(data.success);
            else if(array['getSettings'] == 'swaper') swaper(data['success']);
            else if(Object.keys(data.success).length > 0){
                if(array.hasOwnProperty('getSettings')){
                    if(array['getSettings'] == 'editions') vue['editions'] = data.success;
                    else if(array['getSettings'] == 'fitchas') vue2['fitchas'] = data.success;
                    else if(array['getSettings'] == 'reviews') vue3['reviews'] = data.success;
                    else if(array['getSettings'] == 'problems') vue4['faq'] = data.success;
                    else if(array['getSettings'] == 'news') vue['news'] = data.success;
                    else if(array['getSettings'] == 'anticlicker'){
                        vue.list = data.success['adds_visitors'];
                        vue.autoban_enabled = (data.success['autoban'] == 'checked');
                        vue.redirect_enabled = (data.success['redirect'] == 'checked');
                        vue.adds_trys = data.success['adds_trys'];
                        vue.adds_redirected = data.success['adds_redirected'];
                        vue.adds_banned = data.success['adds_banned'];
                    } else if(array['getSettings'] == 'statistic') statistic_page(data.success);
                    else if(array['getSettings'] == 'review'){
                        vue3.name = (data['success']||{'name': null})['name'];
                        vue3.rating = (data['success']||{'rating': null})['rating'];
                        vue3.link = (data['success']||{'link': null})['link'];
                        vue3.review = (data['success']||{'review': null})['review'];
                        vue3.photo = (data['success']||{'photo': null})['photo'];
                    } else if(array['getSettings'] == 'get_boss_info'){ 
                        if(url_dir == 'pages' && url_page.indexOf('profile') != -1){
                            vue.photo = htmldecoder(data['success']['boss']['photo']);
                            vue.email = htmldecoder(data['success']['boss']['email']);
                            vue.name = htmldecoder(data['success']['boss']['name']);
                        } else if(url_dir == 'pages' && url_page.indexOf('domains') != -1) vue.domains = JSON.parse(data['success']['boss']['domain'])['domains'];
                    } else if(array['getSettings'] == 'dialogs') dialogs_page(data.success.token, data.success.dirname, data.success.rooms, data.success.assistents);
                    else if(array['getSettings'] == 'dialog') dialog_page(data.success.token, data.success.dirname, data.success.emojis, data.success.regexp);
                    else if(array['getSettings'] == 'options') options_page(data.success.domains, data.success.design);
                    else if(array['getSettings'] == 'offline'){
                        vue.feedback_target_email = htmldecoder(data.success['feedbackMAIL']);
                        vue.feedback_text = htmldecoder(data.success['feedbackTEXT']);
                        vue.feedback_form_checkbox = (data.success['feedbackENABLED'] == 'checked');
                        vue.feedback_input_checkbox_1 = (data.success['feedbackformName'] == 'checked');
                        vue.feedback_input_checkbox_2 = (data.success['feedbackformPhone'] == 'checked');
                        vue.feedback_input_checkbox_3 = (data.success['feedbackformEmail'] == 'checked');
                    }  else if(array['getSettings'] == 'design'){
                        let design = data.success.design;
                        let domains = data.success.domains;
                        vue.design = design;
                        design = design['deffault'];
                        vue.domains = domains;
                        vue.sizes['btn_svg_size']['value'] = design['btn_svg_size'];
                        vue.sizes['helper_btn_height']['value'] = design['helper_btn_height'];
                        vue.sizes['helper_btn_width']['value'] = design['helper_btn_width'];
                        vue.sizes['helper_btn_font']['value'] = design['helper_btn_font'];
                        vue.sizes['helper_btn_font_weigt']['value'] = design['helper_btn_font_weigt'];
                        vue.sizes['chatheader_font_size']['value'] = design['chatheader_font_size'];
                        vue.sizes['chatheader_font_weight']['value'] = design['chatheader_font_weight'];
                        vue.sizes['msg_font_size']['value'] = design['msg_font_size'];
                        vue.sizes['msg_font_weight']['value'] = design['msg_font_weight'];
                        vue.status_colors['StatusOfflinecolor']['value'] = design['StatusOfflinecolor'];
                        vue.status_colors['StatusOnlinecolor']['value'] = design['StatusOnlinecolor'];
                        vue.colors['Дизайн для кнопки']['bgcolor']['value'] = design['bgcolor'];
                        vue.colors['Дизайн для кнопки']['textcolor']['value'] = design['textcolor'];
                        vue.colors['Дизайн для кнопки']['button_shadow']['value'] = design['button_shadow'];
                        vue.colors['Дизайн для кнопки']['logobgcolor']['value'] = design['logobgcolor'];
                        vue.colors['Дизайн для кнопки']['logodetailscolor']['value'] = design['logodetailscolor'];
                        vue.colors['Дизайн для кнопки']['SvgColor']['value'] = design['SvgColor'];
                        vue.colors['Дизайн для уведомлений пользователя']['modal_message_text_color']['value'] = design['modal_message_text_color'];
                        vue.colors['Дизайн для уведомлений пользователя']['modal_message_color']['value'] = design['modal_message_color'];
                        vue.colors['Дизайн для уведомлений пользователя']['modal_message_shadow_color']['value'] = design['modal_message_shadow_color'];
                        vue.colors['Дизайн для окна чата']['UmessagesColor']['value'] = design['UmessagesColor'];
                        vue.colors['Дизайн для окна чата']['AmessagesColor']['value'] = design['AmessagesColor'];
                        vue.colors['Дизайн для окна чата']['FrameColor']['value'] = design['FrameColor'];
                        vue.colors['Дизайн для окна чата']['windowbgcolor']['value'] = design['windowbgcolor'];
                        vue.colors['Дизайн для окна чата']['windowtextcolor']['value'] = design['windowtextcolor'];
                        vue.colors['Дизайн для окна чата']['window_shadow']['value'] = design['window_shadow'];
                        vue.colors['Дизайн для окна чата']['scroll_color']['value'] = design['scroll_color'];
                        vue.colors['Дизайн для окна чата']['success_color']['value'] = design['success_color'];
                        vue.colors['Дизайн для окна чата']['error_color']['value'] = design['error_color'];
                        vue.PersonalSize = (design['PersonalSize'] == 'checked');
                        vue.chat_status_checkbox = (design['chat_status_checkbox'] == 'checked');
                        vue.active_position = design['position_type'];
                        vue.mobile_active_position = design['mobile_position_type'];
                    } else if(array['getSettings'] == 'tariff' || array['getSettings'] == 'autosender'){ 
                        for(index in data['success']){ 
                            if(index == 'fastmessages') vue[index]['chapters'] = data['success'][index];
                            else vue[index] = data['success'][index];
                        }
                    } else if(array['getSettings'] == 'dialog') vue.settings = data['success'];
                    else if(array['getSettings'] == 'assistents') assistents_page(data.success.token, data.success.dirname, data.success.departaments, data.success.domains);
                    else if(array['getSettings'] == 'assistent') employee_page(data.success.token, data.success.dirname, data.success.info, data.success.regexp, data.success.emojis);
                    else if(array['getSettings'] == 'crm') crm_page(data.success.token, data.success.dirname, data.success.tables, data.success.today, data.success.local_date, data.success.regexp, data.success.emojis, data.success.crm, data.success.tasks, data.success.fastmessages, data.success.mailer, data.success.domains);
                    else if(array['getSettings'] == 'tasks') tasks_page(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis, data.success.tasks, data.success.items, data.success.tables);
                    else if(array['getSettings'] == 'crm_settings') crm_settings_page(data.success.token, data.success.dirname, data.success.columns, data.success.max_count, data.success.emojis, data.success.regexp);
                    else if(array['getSettings'] == 'hub') hub_page(data.success.token, data.success.dirname, data.success.assistents, data.success.domains, data.success.regexp, data.success.emojis, data.success.fastmessages, data.success.tables, data.success.personal_id, data.success.tasks);
                    else if(array['getSettings'] == 'chat') chat_page(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis, data.success.personal_id, data.success.fastmessages, data.success.properties, data.success.notes, data.success.buttlecry, data.success.tables, data.success.tasks);
                    else if(array['getSettings'] == 'forms') forms_page(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis, data.success.assistents, data.success.forms);
                    else if(array['getSettings'] == 'banned') banned_page(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis, data.success.assistents);
                    else if(array['getSettings'] == 'banned_chat') banned_chat(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis);
                    else if(array['getSettings'] == 'command') command_page(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis, data.success.personal_id);
                    else if(array['getSettings'] == 'command_chat') command_chat(data.success.token, data.success.dirname, data.success.regexp, data.success.emojis, data.success.personal_id, data.success.fastmessages, data.success.oponent_email, data.success.boss_id, data.success.oponent_departament);
                    else if(array['getSettings'] == 'mailer') mailer_page(data.success);
                    else if(array['getSettings'] == 'domains') domains_page(data.success.domains, data.success.design_domains);
                    else if(array['getSettings'] == 'feedback') feedback_page();
                    else if(array['getSettings'] == 'payment') payment_page(data.success.client_id, data.success.email);
                } 
                if(url_dir == 'consultant' && url_page.indexOf('crm_settings') != -1 && (array['column_index'] || data.success.column_index) && data.errors.length == 0){
                    if(data.success.column_index) array['column_index'] = data.success.column_index;
                    vue_component.$delete(vue_component.columns, vue_component.selected_column);    
                    vue_component.$set(vue_component.columns, array['column_index'], {
                        "type": (array.hasOwnProperty('type') ? array['type'] : array['save_type']), 
                        "deffault": (array['type'] == 5 || array['save_type'] == 5 ? JSON.parse(array.deffault) : array.deffault), 
                        "variants": (typeof(array.variants) == 'string' ? JSON.parse(array.variants) : array.variants), 
                        'priority': array['priority'],
                        "helper_column_name": array['header']||array['save_header'],
                    });    
                } 
                if(data.success.hasOwnProperty('fastMessages')){ 
                    let type = data.success.fastMessages.type;
					let value = data.success.fastMessages.value;
					let uid = data.success.fastMessages.uid;
					if(type == 'new_fast_message'){ 
                        if(Object.keys(vue_component.fastmessages.chapters[value.column]).length == 0 && value.column == 'main') vue_component.$set(vue_component.fastmessages.chapters, value.column, {});
                        vue_component.$set(vue_component.fastmessages.chapters[value.column], uid, value.value);
                    } else if(type == 'new_chapter') vue_component.$set(vue_component.fastmessages.chapters, uid, {"chapter_name": value});
                }
                if(array.hasOwnProperty('select_tariff')){
                    if(data.errors.length > 0) return;
                    vue.money = data['success']['money'];
                    vue.tariff = data['success']['tariff'];
                    vue.unused = vue.editions[data['success']['tariff']]['cost']['value'];
                }
                if(data.success.hasOwnProperty('new_domain_key')){ 
                    vue_component.domains[data['success']['new_domain_key']] = vue_component.new_domain;
                    vue_component.new_domain = '';
                }
                if(data.success.hasOwnProperty('new_departament_key')){ 
                    if(Object.keys(vue_component.departaments).length == 0) vue_component.$set(vue_component, 'departaments', {});
                    vue_component.$set(vue_component.departaments, data['success']['new_departament_key'], data['success']['new_departament_value']);
                    vue_component.dep_name = '';
                }
                if(data.success.hasOwnProperty('response')){ 
                    if(data.success.hasOwnProperty('link')){ alert(data.success['response'], 'success', data.success['link']); }
                    else alert(data.success['response'], 'success');
                } else if(data.success.hasOwnProperty('link')) window.location.href = data.success['link'];
                if(data.success.hasOwnProperty('loader') && vue_component){ 
                    if(vue_component['loader']) vue_component.loader = false;
                    if(vue_component['load']) vue_component.load = false;
                }
                if(data.success.hasOwnProperty('create_swap')){
                    if(Object.keys(vue_component.settings).length == 0) vue_component.$set(vue_component, 'settings', {});
                    vue_component.$set(vue_component.settings, array.swap_id, {
                        "swap_to": array.swap_to, 
                        "swap_time": "always", 
                        "swap_from": array.swap_from, 
                        "swap_cache": false,
                        "swap_type":  vue_component.swap_type,
                        "swap_changename": true,
                    });
                }
                if(data.success.hasOwnProperty('create_note')){
                    vue_component.$set(vue_component, data.success.create_note.type, data.success.create_note.value);
                    vue_component.new_property_name = null;
                    vue_component.new_property_value = null;
                    vue_component.new_note = null;
                }
                if(data.success.hasOwnProperty('add_task')){
                    let time = array['task_time'];
                    let text = array['text'];
                    let type = array['task_type'];
                    let selected = JSON.parse(array['task_selected']);
                    let table = array['table'];
                    for(select in selected){
                        let selected_index = selected[select];
                        if(!vue_component.tasks[selected_index]) vue_component.$set(vue_component.tasks, selected_index, []);
                        vue_component.tasks[selected_index].push({
                            "type": type,
                            "time": time,
                            "text": text,
                            "selected": selected,
                            'table': table,
                            'index': data.success['add_task']
                        });
                    }
                }
                if(data.success.hasOwnProperty('reload')) location.reload();
                if(data.success.hasOwnProperty('log')) console.log(data.success.log);
            }
		}, error: function() { console.log('Ошибка в ajax запросе! '); }
	});
}
function feedback_page(){ // OFFLINE 2
    let mounth_ago = new Date();
    mounth_ago.setMonth(mounth_ago.getMonth() - 1);
    let today = new Date();
    var vue = new Vue({
        el: '.main-window', 
        data:{
            conditions: conditions,
            new_form: {
                open_status: false,
                email_folder: false,
                conditions_folder: false,
                add_new_field: false, 
                new_field: {"type": null, "required": false, "placeholder": null, "list": {}},   
                fields: {},
                name: '',
                email: '',
                text: '',
                conditions: {},
            },
            filed_lsit: {
                'text': 'Текст', 
                'email': 'Электронная почта', 
                'phone': 'Номер телефона',
                'link': 'Адрес web-страницы',
                'file': 'Файлы',
                'number': 'Число',
                'list': 'Список | Выбор',
            },
            forms: {},
        },
        methods: {
            add_new_form(){
                
            },
        }
    });
}
function domains_page(domains, design_domains){
    var vue = new Vue({ 
        el: '#container',
        data: {
            domains: domains,
            new_domain: '',
            load: false,
            design_domains: design_domains,
        },
        methods: {
            remove(index){ send_ajax('/engine/settings', {'remove_domain': index}); Vue.delete(vue.domains, index); },
            add_domain(){ vue.load = true; send_ajax('/engine/settings', {'domain': vue.new_domain}, vue); },
            personal_design(domain){ 
                send_ajax('/engine/settings', {'personal_design_domain_status': domain});
                if(design_domains.indexOf(domain) == -1) design_domains.push(domain);
                else design_domains.splice(design_domains.indexOf(domain), 1);
            },
        }
    });
}
function sortByPriority(arr, type, header, reverse, vue) { 
	if(type == 'priority'){ arr.sort((a, b) => a.priority > b.priority ? 1 : -1); } 
	else if(type == 'date' || type == 'local_date'){ 
		arr.sort(function(a, b) {
			dateA = a[header];
			dateB = b[header];
			if(type == 'date'){
				if(!dateA) dateA = vue.date;
				if(!dateB) dateB = vue.date;
			} else {
				if(!dateA) dateA = vue.local_date;
				if(!dateB) dateB = vue.local_date;
			}
			dateA = new Date(dateA);  dateB = new Date(dateB); 
			if(!reverse) return dateA-dateB;
			else return dateB-dateA;
		}); 
	} else { 
		arr.sort(function(a, b) { 
			let varA = a[header]; 
			let varB = b[header];
			var flag = true;
			if(header != 'helper_info' && header != 'helper_name'){
				if(type != 'valute'){
					if(!varA) varA = vue.columns[header].deffault;
					if(!varB) varB = vue.columns[header].deffault;
				} else {
					if(!varA) varA = vue.columns[header].deffault.value;
					if(!varB) varB = vue.columns[header].deffault.value;
				}
				flag = true; 
			} else flag = false;
			if(!isNaN(varA) && !isNaN(varB) && flag){
				varA = parseInt(varA);
				varB = parseInt(varB);
			}
			if(!reverse){
				if(varA > varB) return  1;
				else if(varA < varB || !varA) return -1;
				return 0;
			} else {
				if(varA > varB || !varB) return  -1;
				else if(varA < varB) return 1;
				return 0;
			}
		}); 
	}
}
function readURL(file, el) {
	if (!file) return;
	var reader = new FileReader();
	reader.onload = function(e) {
		$(el).css('background-image', `url(${e.target.result})`);
		$(el).css('background-size', `cover`);
	}
	reader.readAsDataURL(file);
}
function styles_mode(){
	let menu_container = $('<div></div>');
	let left_menu = $('<div></div>');
	let right_menu = $('<div></div>');
	let menu_text = $('<h2>Включить ночной режим ?</h2>');
	let mode_menu_button = $('<span class="check_btn"></span>');
	let mode_menu_button_slider;
    let light_styles = $(`
        <style id="light_mode">
            .WhiteBlack{
                color:#000 !important;
            }   
            body, .news_block{
                background:url(/scss/imgs/hypnotize.png) repeat center center !important;
            }
             #footer_contacts div:nth-child(2) .icon{
                background:url(/scss/imgs/blacklogo.png) repeat center center;
                background-size:cover;
            }
            #copyright{
                background:url(/scss/imgs/blackcopyright.png) repeat center center !important;
            }
            #footer_bottom{
                border-top:2px solid #000 !important;
            }
            .borderblackwhite{
                border-color:#000 !important;
            }
            .bgblackwhite{
                background-color: whitesmoke !important;
            }
        </style>
    `);
    let black_styles = $(`
        <style id="black_mode">
            .WhiteBlack{
                color:#fff !important;
            }   
            body, .news_block{
                background:url(/scss/imgs/classy_fabric.png) repeat center center !important;
            }
            #footer_contacts div:nth-child(2) .icon{
                background:url(/scss/imgs/logo.png) repeat center center;
                background-size:cover;
            }
            #copyright{
                background:url(/scss/imgs/copyright.png) repeat center center !important;
            }
            #footer_bottom{
                border-top:2px solid #fff !important;
            }
            .borderblackwhite{
                border-color:#fff !important;
            }
            .bgblackwhite{
                background-color:#252525 !important;
            }
        </style>
    `);
	$(document).ready(() => {
		if(localStorage.mode){
			mode_menu_button_slider = $('<span class="unchecked_btn_span"></span>');
            localStorage.mode = 'light';
            $('body').append(black_styles);
		} else { 
            mode_menu_button_slider = $('<span class="checked_btn_span"></span>');
            $('body').append(light_styles);
		}
		$("#footer_contacts div:nth-child(2) .icon").css("background-size", "contain");
		$("#copyright").css("background-size", "contain");
		mode_menu_button.append(mode_menu_button_slider);
	});
	$(document.body).append(menu_container);
	menu_container.append(left_menu);
	let left_menu_image = $("<span></span>");
	left_menu.append(left_menu_image);
	menu_container.append(right_menu);
	right_menu.append(menu_text);
	right_menu.append(mode_menu_button);
	left_menu_image.css({
		"height":"40px",
		"width":"40px",
		"background":"url(/scss/imgs/dn.png) no-repeat center center",
		"background-size":"contain",
		"display":"block",
	});
	menu_container.css({
		"padding":"10px",
		"position":"fixed",
		"bottom":"5%",
		"right":"-260px",
		"display":"inline-flex",
		"color":"#fff",
		"transition":"0.75s",
		"z-index": "210000",
	});
	left_menu.css({
		"height":"80px",
		"position":"relative",
		"border-top-left-radius":"20px",
		"border-bottom-left-radius":"20px",
		"width":"55px",
		"background": "#0ae",
		"color":"#fff",
		"cursor":"pointer",
		"display":"flex",
		"align-items":"center",
		"justify-content": "center",
	});
	right_menu.css({
		"height":"100%",
		"position":"relative",
		"border-top-right-radius":"20px",
		"border-bottom-right-radius":"20px",
		"width":"250px",
		"background": "#252525",
		"color":"#fff",
		"display":"flex",
		"flex-direction":"column",
		"justify-content": "center",
		"align-items": "center",
	});
	menu_text.css({
		"color":"#0ae",
		"margin-top":"10px",
		"margin-bottom":"10px",
		"font-size":"17px",
	});
	mode_menu_button.css({
		"margin-bottom":"10px",
	});
	left_menu.on("click", () => {
		if(menu_container.css("right") == 0 || menu_container.css("right") == "0px") menu_container.css("right", "-260px");
		else menu_container.css("right", "0px");
	});
	mode_menu_button.on("click", () => {
		$("body").css("transition", "all 0.75s");
		$("body").css("-webkit-transition", "all 0.75s");
		$(".news_block").css("transition", "all 0.75s");
		$(".WhiteBlack").css("transition", "all 0.75s");
		$("#footer_bottom").css("transition", "all 0.75s");
		$("#footer_contacts div:nth-child(2) .icon").css("transition", "all 0.75s");
		$("#section_name").css("transition", "all 0.75s");
		$("#copyright").css("transition", "all 0.75s");
		$("#logo").css("transition", "all 0.75s");
		$(".bgblackwhite").css("transition", "all 0.75s");
		if(!localStorage.mode){
			mode_menu_button_slider.removeClass("checked_btn_span");
			mode_menu_button_slider.addClass("unchecked_btn_span");
			localStorage.mode = "light";
            $('body #light_mode').remove();
            $('body').append(black_styles);
		} else {
			mode_menu_button_slider.removeClass("unchecked_btn_span");
			mode_menu_button_slider.addClass("checked_btn_span");
			delete localStorage.mode;
            $('body #black_mode').remove();
            $('body').append(light_styles);
		}
		$("#copyright").css("background-size", "contain");
		$("#footer_contacts div:nth-child(2) .icon").css("background-size", "contain");
	});
}
// day - dark | theme
function color_mode(){
	// let menu_container = $('<div id="day-dark-theme-container"></div>');
	// let left_menu = $(`
    //     <div id="day-dark-theme-btn">
    //         <svg version="1.1" class="day-night-img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><path style="fill:#F2E7BF;" d="M299.346,86.692c-0.001,0-0.002,0-0.003,0V425.46c0.001,0,0.002,0,0.003,0c93.548,0,169.384-75.836,169.384-169.384S392.893,86.692,299.346,86.692z"/><circle style="opacity:0.08;enable-background:new ;" cx="367.226" cy="327.807" r="37.156"/><path style="opacity:0.08;enable-background:new ;" d="M315.682,409.122c-5.513,0-10.963-0.273-16.341-0.788v17.125c0.001,0,0.002,0,0.003,0c50.932,0,96.608-22.484,127.66-58.062C397.235,393.378,358.299,409.122,315.682,409.122z"/><path style="fill:#E18C36;" d="M288.327,6.362l-59.689,103.386h70.709V0C295.06,0,290.775,2.121,288.327,6.362z"/><path style="fill:#EC8560;" d="M114.98,87.293l30.897,115.312l99.998-99.998L130.564,71.71C121.102,69.175,112.445,77.832,114.98,87.293z"/><path style="fill:#E18C36;" d="M49.633,267.094l103.386,59.689V185.366L49.633,245.055C41.15,249.953,41.15,262.197,49.633,267.094z"/><path style="fill:#EC8560;" d="M130.564,440.44l115.312-30.897l-99.998-99.998L114.98,424.857C112.445,434.318,121.102,442.975,130.564,440.44z"/><path style="fill:#E18C36;" d="M228.636,402.402l59.689,103.386c2.042,3.537,5.362,5.599,8.892,6.186c1.11,0.185,2.127-0.66,2.127-1.785V402.402L228.636,402.402L228.636,402.402z"/><path style="opacity:0.1;enable-background:new ;" d="M140.033,180.793l5.845,21.812l92.608-92.608C197.568,120.73,162.699,146.383,140.033,180.793z"/><path style="opacity:0.1;enable-background:new ;" d="M129.675,198.843c-11.184,22.609-17.48,48.067-17.48,74.999c0,10.594,0.982,20.958,2.843,31.014l37.981,21.929V185.366L129.675,198.843z"/><path style="opacity:0.1;enable-background:new ;" d="M145.877,309.545l-12.447,46.453c15.387,27.687,38.306,50.606,65.992,65.992l46.453-12.447L145.877,309.545z"/><path style="opacity:0.1;enable-background:new ;" d="M228.636,402.401l21.929,37.981c10.055,1.861,20.42,2.843,31.014,2.843c6.003,0,11.927-0.33,17.767-0.94v-39.884H228.636z"/><path style="fill:#F7D64C;" d="M129.962,256.075c0,93.548,75.836,169.384,169.384,169.384V86.692C205.798,86.692,129.962,162.527,129.962,256.075z"/><g><path style="fill:#394049;" d="M320.558,283.383c-8.33,0-15.862-3.486-21.212-9.076c-5.351,5.59-12.882,9.076-21.213,9.076c-16.196,0-29.373-13.176-29.373-29.373c0-4.508,3.654-8.16,8.16-8.16c4.507,0,8.16,3.652,8.16,8.16c0,7.197,5.856,13.053,13.053,13.053c7.197,0,13.053-5.856,13.053-13.053c0-4.508,3.654-8.16,8.16-8.16c4.507,0,8.16,3.652,8.16,8.16c0,7.197,5.855,13.053,13.052,13.053c7.197,0,13.053-5.856,13.053-13.053c0-4.508,3.654-8.16,8.16-8.16c4.507,0,8.16,3.652,8.16,8.16C349.93,270.207,336.754,283.383,320.558,283.383z"/><path style="fill:#394049;" d="M201.368,230.475c-4.507,0-8.16-3.652-8.16-8.16v-9.01c0-4.508,3.654-8.16,8.16-8.16s8.16,3.652,8.16,8.16v9.01C209.528,226.822,205.875,230.475,201.368,230.475z"/><path style="fill:#394049;" d="M397.323,230.475c-4.507,0-8.16-3.652-8.16-8.16v-9.01c0-4.508,3.654-8.16,8.16-8.16c4.507,0,8.16,3.652,8.16,8.16v9.01C405.483,226.822,401.829,230.475,397.323,230.475z"/></g></svg>
    //     </div>
    // `);
	// let right_menu = $('<div id="day-dark-theme-menu"></div>');
	// let menu_text = $('<h2>Day theme | Dark theme</h2>');
	// let mode_menu_button = $('<span class="check_btn"></span>');
	// let mode_menu_button_slider;
    let transitions = $(`
        <style id="transitions">
            *{
                transition: all 0s !important;
            }
        </style>
    `);
    let day = $(`
        <style id="day_mode">
            :root{
                --white:#ffffff;
                --black:#222222;
            }
        </style>
    `);
    let dark = $(`
        <style id="dark_mode">
            :root{
                --white:#222222;
                --black:#ffffff;
            }
        </style>
    `);
	$(document).ready(() => {
		if(localStorage.mode){
	//		mode_menu_button_slider = $('<span class="unchecked_btn_span"></span>');
            localStorage.mode = 'day';
            $('body').append(dark);
		} else { 
    //        mode_menu_button_slider = $('<span class="checked_btn_span"></span>');
            $('body').append(day);
		}
	//	mode_menu_button.append(mode_menu_button_slider);
	});
//	$(document.body).append(menu_container);
//	menu_container.append(left_menu);
//	let left_menu_image = $("<span></span>");
//	left_menu.append(left_menu_image);
//	menu_container.append(right_menu);
//	right_menu.append(menu_text);
//	right_menu.append(mode_menu_button);
//	left_menu.on("click", () => {
//		if(menu_container.css("right") == "-40px") menu_container.css("right", "-260px");
//		else menu_container.css("right", "-40px");
//	});
//	mode_menu_button.on("click", () => {
//		if(!localStorage.mode){
	// 		mode_menu_button_slider.removeClass("checked_btn_span");
	// 		mode_menu_button_slider.addClass("unchecked_btn_span");
	// 		localStorage.mode = "day";
    //         $('body #day_mode').remove();
    //         $('body').append(transitions);
    //         setTimeout(() => { $('#transitions').remove(); }, 500)
    //         $('body').append(dark);
	// 	} else {
	// 		mode_menu_button_slider.removeClass("unchecked_btn_span");
	// 		mode_menu_button_slider.addClass("checked_btn_span");
	// 		delete localStorage.mode;
    //         $('body').append(transitions);
    //         setTimeout(() => { $('#transitions').remove(); }, 500)
    //         $('body #dark_mode').remove();
    //         $('body').append(day);
	// 	}
	// });
}
function construct_formData(file, inputname){
	let fd = new FormData;
	fd.append(inputname, file);
	return fd;
}
function getStatistic(array, type, from, to, search_element){
	let result = [];
	let search_elements = labels(search_element); // поисковые лэйблы
	for(i in search_elements){
		let data = {
			'label': search_elements[i]['label'],
			'backgroundColor': search_elements[i]['backgroundColor'],
			'borderColor': search_elements[i]['borderColor'],
			'data': [],
		};
		let years_count = mounths_count = 0;
		for(year in array){
			if(from.getFullYear() > parseInt(year) || parseInt(year) > to.getFullYear()) continue;
			if(type == 'years') data['data'].push({'x': year, 'y': 0});
			Object.keys(array[year]).sort().forEach(function(mounth) {
				if((from.getMonth() + 1 <= parseInt(mounth) || from.getFullYear() != parseInt(year)) && (parseInt(mounth) <= 1 + to.getMonth() || parseInt(year) != to.getFullYear())){
					if(type == 'mounths') data['data'].push({'x': mounth+'.'+year, 'y': 0});
					Object.keys(array[year][mounth]).sort().forEach(function(day) {
						if(from <= new Date(year+'-'+mounth+'-'+day) && new Date(year+'-'+mounth+'-'+day) <= to){
							if(type == 'days') data['data'].push({'x': day+'.'+mounth+'.'+year, 'y': array[year][mounth][day][i]||0});
							else if(type == 'mounths') data['data'][mounths_count]['y'] += array[year][mounth][day][i]||0;
							else if(type == 'years') data['data'][years_count]['y'] += array[year][mounth][day][i]||0;
						}
					}); mounths_count++;
				}
			}); years_count++;
		}
		result.push(data);
	}
	return result;
}
function updateChart(statistic_data, type, from, to, chart_info, chart){
	chart.data.labels = [];
	chart.data.datasets = getStatistic(statistic_data, type, from, to, chart_info);
	chart.update();
}
function utm_search(swap_id, utm_part_id) {
	let filter = event.target.value.toUpperCase();
	if(!vue.settings[swap_id]["swap_utmparts"][utm_part_id].hasOwnProperty('searchmas')) vue.$set(vue.settings[swap_id]["swap_utmparts"][utm_part_id], 'searchmas', Object.assign({}, vue.settings[swap_id]["swap_utmparts"][utm_part_id]["results"]));
	for (key in vue.settings[swap_id]["swap_utmparts"][utm_part_id]["results"]) {
		if(String(key).toUpperCase().indexOf(filter) == -1) vue.$delete(vue.settings[swap_id]["swap_utmparts"][utm_part_id]['searchmas'], key);
		else Vue.set(vue.settings[swap_id]["swap_utmparts"][utm_part_id]['searchmas'], key, vue.settings[swap_id]["swap_utmparts"][utm_part_id]["results"][key]);
	}
}
function fix_array_max3inner(array){
	let new_array = {};
	for(column_name in array){
		let new_column_name = htmldecoder(column_name);
		if(typeof array[column_name] === 'object'){
			new_array[new_column_name] = {};
			for(column_info in array[column_name]){
				let new_column_info_name = htmldecoder(column_info);
				if(typeof array[column_name][column_info] === 'object' && array[column_name][column_info] != null && array[column_name][column_info] != undefined){
					new_array[new_column_name][new_column_info_name] = {};
					for(column_innerinfo in array[column_name][column_info]){ new_array[new_column_name][new_column_info_name][htmldecoder(column_innerinfo)] =  htmldecoder(array[column_name][column_info][column_innerinfo]); }
				} else new_array[new_column_name][new_column_info_name] = htmldecoder(array[column_name][column_info]);
			}
		} else new_array[new_column_name] = htmldecoder(array[column_name]);
	}
	return new_array;
}
function htmldecoder(str){
	if(typeof str != 'string') return str;
	return $("<div/>").html(str).text();
}
function statistic_page(statistic_data){
    utm_data = statistic_data[1];
    statistic_data = statistic_data[0];
    let host_name_selector = '<select id="host_select">';
    for(key in Object.keys(statistic_data)){
        let name = 'Общая статистика'
        if(Object.keys(statistic_data)[key] != 'statistic') name = Object.keys(statistic_data)[key];
        host_name_selector += `<option value="${Object.keys(statistic_data)[key]}">${name}</option>`;
    }
    host_name_selector += '</select>';
    host_name_selector = $(host_name_selector);
    $('#chart_options').append(host_name_selector);
    let statistic_domain = 'statistic';
    host_name_selector.on('change', function(){
        statistic_domain = $(this).val();
        updateChart(statistic_data[statistic_domain], type, mounth_ago, today, chart_info, chart);
    });
    var ctx = document.getElementById('myChart');
    let today = new Date();
    let mounth_ago = new Date(); mounth_ago.setMonth(mounth_ago.getMonth() - 1);
    let type = "days";
    let chart_info = 'visitor';
    let vue = new Vue({
        el: '#UTM_statistic',
        data: { 
           utm: interval_utm_statistic(utm_data[statistic_domain], mounth_ago, today),
        },
        methods:{
            update_status(target){
                vue.$set(target, 'helper_status', !target['helper_status']);
            }
        },
    });
    $('#prereod_from').val(mounth_ago.toISOString().split('T')[0]);
    $('#prereod_to').val(today.toISOString().split('T')[0]);
    $('#prereod_from').on('change', function(e){
        mounth_ago = new Date($(this).val());
        updateChart(statistic_data[statistic_domain], type, mounth_ago, today, chart_info, chart);
        vue.utm = interval_utm_statistic(utm_data[statistic_domain], mounth_ago, today);
    });
    $('#prereod_to').on('change', function(e){
        today = new Date($(this).val());
        updateChart(statistic_data[statistic_domain], type, mounth_ago, today, chart_info, chart);
        vue.utm = interval_utm_statistic(utm_data[statistic_domain], mounth_ago, today);
    });
    $('#pereod_type').on('change', function(e){
        type = $(this).val();
        updateChart(statistic_data[statistic_domain], type, mounth_ago, today, chart_info, chart);
    });
    $('#chart_type span').on('click', function(e){
        $('.active_chart').removeClass('active_chart');
        chart.destroy();
        $(this).addClass('active_chart');
        let chart_type = $(this).data('chart');
        chart = new Chart(ctx, {
            type: chart_type,
            data:{
                datasets: getStatistic(statistic_data[statistic_domain], type, mounth_ago, today, chart_info)
            },
        });
    });
    $('.graphic_nav span').on('click', function(e){
        $('.active_graphic').removeClass('active_graphic');
        $(this).addClass('active_graphic');
        chart_info = $(this).data('filter');
        updateChart(statistic_data[statistic_domain], type, mounth_ago, today, chart_info, chart)
    });
    var chart = new Chart(ctx, {
        type: 'line',
        data:{
            datasets: getStatistic(statistic_data[statistic_domain], type, mounth_ago, today, 'visitor')
        },
    });
}
function interval_utm_statistic(array, from, to){
    let result = {};	
    for(year in array){
        if(from.getFullYear() > year || year > to.getFullYear()) continue;
        Object.keys(array[year]).sort().forEach(function(mounth) {
            if(from.getMonth() + 1 <= parseInt(mounth) && parseInt(mounth) <= 1 + to.getMonth()){
                Object.keys(array[year][mounth]).sort().forEach(function(day) {
                    if(from <= new Date(year+'-'+mounth+'-'+day) && new Date(year+'-'+mounth+'-'+day) <= to){
                        let utm_sources = Object.keys(array[year][mounth][day]);
                        for(utm_source of utm_sources){
                            if(result.hasOwnProperty(utm_source)){
                                let utm_mediums = Object.keys(array[year][mounth][day][utm_source]);
                                for(utm_medium of utm_mediums){
                                    if(result[utm_source].hasOwnProperty(utm_medium)){
                                        let utm_compaigns = Object.keys(array[year][mounth][day][utm_source][utm_medium]);
                                        for(utm_compaign of utm_compaigns){
                                            if(result[utm_source][utm_medium].hasOwnProperty(utm_compaign)){
                                                let utm_contents = Object.keys(array[year][mounth][day][utm_source][utm_medium][utm_compaign]);
                                                for(utm_content of utm_contents){
                                                    if(result[utm_source][utm_medium][utm_compaign].hasOwnProperty(utm_content)){
                                                        let utm_terms = Object.values(array[year][mounth][day][utm_source][utm_medium][utm_compaign][utm_content]);
                                                        for(utm_term of utm_terms){
                                                            if(array[year][mounth][day][utm_source][utm_medium][utm_compaign][utm_content].indexOf(utm_term) == -1)
                                                                array[year][mounth][day][utm_source][utm_medium][utm_compaign][utm_content].append(utm_term);
                                                        }
                                                    } else result[utm_source][utm_medium][utm_compaign][utm_content] = array[year][mounth][day][utm_source][utm_medium][utm_compaign][utm_content];
                                                }
                                            } else result[utm_source][utm_medium][utm_compaign] = array[year][mounth][day][utm_source][utm_medium][utm_compaign];
                                        }
                                    } else result[utm_source][utm_medium] = array[year][mounth][day][utm_source][utm_medium];
                                }
                            } else result[utm_source] = array[year][mounth][day][utm_source];
                        }
                    }
                }); 
            }
        });
    }
	return result;
}
function dialogs_page(token, dirname, rooms, assistents){
    $(document).ready(function() {
        if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
            boxClass:     'wow',      
            animateClass: 'animated',
            offset:       0,       
            mobile:       true,       
            live:         true,      
            callback:     function(box) {},
            scrollContainer: '.Online-List-User2',
        }).init();
    });
    let loaded_users = -1;
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('boss_join', {'room': 'MAIN', 'token': token});
        socket.emit('get_leeds');
        socket.emit('get_clients');
    });
    let vue = new Vue({
        el: '#container',
        data: { 
            userlist:  JSON.parse(rooms),
            searchmas:  JSON.parse(rooms),
            load_count: 50,
            assistents: assistents,
            crm_items: {},
        },
        updated(){ loaded_users = -1; },
        methods:{
            changeChat(data){  
                let photo = vue.userlist[data]['photo'];
                room_name = vue.userlist[data]['info']['ip'];
                let room_id = vue.userlist[data]['room_link'];
                location.href = '/engine/pages/dialog?room='+room_id+'&name=' +room_name+'&ip='+data+'&img='+photo.img+'&color='+photo.color.replace('#', '');
            },
            check_count(){
                if(Object.keys(this.searchmas).length >= this.load_count) return true;
                return false;
            },
            search(){
                let value = $(event.target).val();
                cards_search_dialogs(this, value);
            },
            load_more(){ loaded_users = -1; vue.load_count += 10; },
            sort_mas(array){
                let fast_mas = [];
                let result = {};
                for(card in array){
                    let key = card;
                    let card_info = array[card];
                    card_info['array_key'] = key;
                    fast_mas.push(card_info);
                    loaded_users++;
                    if(loaded_users >=  this.load_count) break;
                }
                fast_mas.sort((a, b) => {
                    return new Date(b.time) - new Date(a.time);
                });
                for(key in fast_mas){
                    let array_key = fast_mas[key]['array_key'];
                    delete fast_mas[key]['array_key'];
                    result[array_key] = fast_mas[key];
                }
                return result;
            },
        },
    });
    crmchanges(socket, vue);
    remove_loader();
}
function remove_loader(){
    $('.page_loader').css('opacity', 0);
    setTimeout(() => {
        $('.page_loader').css('display', 'none');
    }, 700)
}
function dialog_page(token, dirname, emojis, regexp){
    var params = get_reader(window.location);
    $(document).ready(function() {
        $(".chat_block_textarea").bind("DOMSubtreeModified",function(){ 
            vue.newMessage = $(this).text();
            if (vue.newMessage.length > 0)  $('#placeholder').css('display', 'none');
            else  $('#placeholder').css('display', 'block');
        });
        $('body').append(`
            <style>
            *::-webkit-scrollbar-thumb { background-color: #${ params['color'] ? params['color'] : '0ae' } !important; }
            </style>
        `);
        if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
            boxClass:     'wow',      
            animateClass: 'animated',
            offset:       0,       
            mobile:       true,       
            live:         true,      
            callback:     function(box) {},
            scrollContainer: '#chat_body',
        }).init();
    });
    // персональные данные
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
       socket.emit('boss_join', {'room': params['room'], 'token': token});
       socket.emit('get-assistent-messages', {'type': 'guest'});
       socket.emit('room_status', {'type': 'guest'});
    });
    let dates = [];
    let vue = new Vue({
        el: '#app',
        data: {
            files_path: location.origin + '/user_adds/',
            messages: [],
            info: {},
            messages_loaded: false,
            emojis: emojis,
            status: '',
            regexp: regexp,
            room: params['room'],
            g_photo: null,
            g_name: null, 
            g_type: null,
            chat_name: params['name'],
            photo: {
                img:  params['img'],
                color: params['color'],
            },
            g_columns: {},
        },
        created() {
            socket.on('page_reload', () => { location.href = location.href; });
        },
        updated(){
            dates = [];
        },
        methods: {
            find_emojis(str){
                for(folder in vue.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in vue.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${vue.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            htmldecoder(str){ return $("<div/>").html(str).text(); },
            decodeHtml(str) {
                var textArea = document.createElement('textarea');
                textArea.innerHTML = str;
                return textArea.value;
            },
            load_date(date){
                if(dates.indexOf(date) != -1) return false; 
                dates.push(date);
                return true;
            },
            exit(){ window.location.href = '/engine/pages/dialogs';},
        },
    });
    crmchanges_inroom(socket, vue);
    get_messages(socket, vue, '');
}
function crmchanges(socket, vue){
    socket.on('add_item', (data) => { Vue.set(vue.crm_items, data.index, data.info); });
    socket.on('change_item', (data) => {  Vue.set(vue.crm_items[data.index], data.column, data.value); }); 
    socket.on('delete_item', (data) => {  vue.$delete(vue.crm_items, data.index); });
    socket.on('get_items', (data) => { vue.crm_items = data.items; });
}
function crmchanges_inroom(socket, vue){
    socket.on('change_this', (data) => { 
        if(data.column == 'helper_photo') vue.g_photo = data.value;
        else if(data.column == 'helper_name') vue.g_name = data.value;
        else Vue.set(vue.g_columns, data.column, data.value);
    }); 
    socket.on('room_status', (data) => {
        if(data.CRM_info){
            if(Object.keys(data.CRM_info).length > 0){
                vue.g_name = data.CRM_info.helper_name;
                vue.g_photo = data.CRM_info.helper_photo;
                vue.g_type = data.table;
            }
        }
        if(data.session){
            let session_start = data.session.session_start;
            let session_time = data.session.session_time;
            if(!session_start || data.status == 'offline') vue.room_time = session_time;
            else {
                let diff = Math.abs(new Date() - new Date(session_start));
                if(session_time != 0) diff += session_time;
                vue.room_time = diff;
                vuetimer = setInterval(() => {
                    vue.room_time += 1000;
                }, 1000, vue);
            }
        }
        if(data.photo){ 
            vue.photo = data.photo;
            $('body').append(`
                <style>
                *::-webkit-scrollbar-thumb, #chat_settings_menu::-webkit-scrollbar-thumb { background-color: ${vue.photo.color}; }
                [data-tooltip]::after {
                    background:${vue.photo.color};
                    color:#000;
                } 
                </style>
            `);
        }
        if(data.visits) vue.visits = data.visits;
        if(data.status) vue.status = data.status;
        if(data.actual_page) vue.this_page = data.actual_page;
        if(data.previous_page) vue.prev_page = data.previous_page;
        if(data.typing) vue.typing = data.typing;
        if(data.info) vue.info = data.info;
    });
    socket.on('delete_this', () => { 
        vue.g_photo = null;
        vue.g_name = null; 
        vue.g_type = null;
    });
}
function labels(type){
	if(type == 'visitor') return{
		"adds": {
			'label': "Посещений по рекламной ссылке",
			'backgroundColor': 'green',
			'borderColor': 'green'
		}, 
		"unique_visitors": {
			'label': "Уникальных посещений",
			'backgroundColor': '#0ae',
			'borderColor': '#0ae'
		},
		"visits": {
			'label': "Повторных посещений",
			'backgroundColor': 'purple',
			'borderColor': 'purple'
		},
	};
	else if(type == 'anticlicker') return {
		"adds_banned": {
			'label': "Заблокировано",
			'backgroundColor': 'tomato',
			'borderColor': 'tomato'
		}, 
		"adds_redirected": {
			'label': "Переадресовано",
			'backgroundColor': '#f90',
			'borderColor': '#f90'
		}
	};
	else if(type == 'CRM') return {
			"leeds": {
				'label': "Созданных лидов",
				'backgroundColor': 'yellow',
				'borderColor': 'yellow'
			}, 
			"clients": {
				'label': "Созданных клиентов",
				'backgroundColor': 'lightblue',
				'borderColor': 'lightblue'
			},
			"tasks": {
				'label': "Созданных задач",
				'backgroundColor': 'grey',
				'borderColor': 'grey'
			},
			"add_fromcards_leeds": {
				'label': "Добавленных лидов-посетителей",
				'backgroundColor': 'green',
				'borderColor': 'green'
			},
			"add_fromcards_clients": {
				'label': "Добавленных клиентов-посетителей",
				'backgroundColor': 'purple',
				'borderColor': 'purple'
			}
		};
	else if(type == 'chat') return {
		"guests_messages": {
			'label': "Сообщений посетителей",
			'backgroundColor': '#f90',
			'borderColor': '#f90'
		}, 
		"consultants_messages": {
			'label': "Сообщений ассистентов",
			'backgroundColor': '#0ae',
			'borderColor': '#0ae'
		},
		"banned": {
			'label': "заблокировано",
			'backgroundColor': 'black',
			'borderColor': 'black'
		},
		"offline_forms": {
			'label': "Форм обратной связи",
			'backgroundColor': 'purple',
			'borderColor': 'purple'
		},
	};
	else if(type == 'consultation') return {
		"started_consulations": {
			'label': "Начатых диалогов",
			'backgroundColor': 'lightgreen',
			'borderColor': 'lightgreen'
		}, 
		"restarted_consulations": {
			'label': "Возобновлённых диалогов",
			'backgroundColor': '#f90',
			'borderColor': '#f90'
		},
		"finished_consulations": {
			'label': "Завершённых диалогов",
			'backgroundColor': 'tomato',
			'borderColor': 'tomato'
		},
	};
	else if(type == 'adds') return {
		"google": {
			'label': "Реклама google",
			'backgroundColor': 'orange',
			'borderColor': 'orange'
		}, 
		"facebook": {
			'label': "Реклама facebook",
			'backgroundColor': 'blue',
			'borderColor': 'blue'
		},
		"yandex": {
			'label': "Реклама яндекс",
			'backgroundColor': 'tomato',
			'borderColor': 'tomato'
		},
		"other_adds": {
			'label': "Прочие рекламные компании",
			'backgroundColor': 'lightgreen',
			'borderColor': 'lightgreen'
		},
	};
	else if(type == 'swap') return {
		"clicks": {
			'label': "Кликов по подменённому телефону",
			'backgroundColor': 'orange',
			'borderColor': 'orange'
		}, 
		"shown": {
			'label': "Количество подмен",
			'backgroundColor': 'darkgreen',
			'borderColor': 'darkgreen'
		},
		"cache_clicks": {
			'label': "Кликов по кэшированной подмене",
			'backgroundColor': 'darkred',
			'borderColor': 'darkred'
		},
		"cache_shown": {
			'label': "Кэшированных подмен",
			'backgroundColor': 'brown',
			'borderColor': 'brown'
		},
	}
    else if(type == 'notifications') return {
		"send_count": {
			'label': "Отправлено / показано раз",
			'backgroundColor': 'green',
			'borderColor': 'green'
		}, 
	}
	return {};
}
(function () {
    this.uniqid = function (pr, en) {
        var pr = pr || '', en = en || false, result, us;
        this.seed = function (s, w) {
            s = parseInt(s, 10).toString(16);
            return w < s.length ? s.slice(s.length - w) : (w > s.length) ? new Array(1 + (w - s.length)).join('0') + s : s;
        };
        result = pr + this.seed(parseInt(new Date().getTime() / 1000, 10), 8) + this.seed(Math.floor(Math.random() * 0x75bcd15) + 1, 5);
        if (en) result += (Math.random() * 10).toFixed(8).toString();
        return result;
    };
})();
function cards_search_dialogs(vue, value) {
	let input = document.getElementsByClassName("cards_search_input")[0];
	let filter = input.value.toUpperCase();
    if(value) filter = value.toUpperCase();
	for (key in vue.userlist) {
		let flag = false;
		for (value in vue.userlist[key]){
			if(!vue.userlist[key][value]) continue;
			else if(value == "time"){
				let time = vue.userlist[key][value];
				if(time.toUpperCase().indexOf(filter) > -1) { flag = true; break; }
				else if((time.split(' ')[0].split('-').reverse().join('.') + ' ' + time.split(' ')[1].split(':').splice(0,2).join(':')).toUpperCase().indexOf(filter) > -1) { flag = true; break; }
			} else if(value == "domains_list"){ // поиск по доменам
				for(item in vue.userlist[key][value]["domains"]){ 
					if (vue.userlist[key][value]["domains"][item].toUpperCase().indexOf(filter) > -1){  flag = true; break; }
				}	
			} else if (String(vue.userlist[key][value]).toUpperCase().indexOf(filter) > -1){ flag = true; break; } // string info
			else if(value == 'info'){ // geo, ip, adds
				if (
					String(vue.userlist[key][value]["ip"]).toUpperCase().indexOf(filter) > -1 || 
					String(vue.userlist[key][value]["geo"]["city"]).toUpperCase().indexOf(filter) > -1 || 
					String(vue.userlist[key][value]["geo"]["country"]).toUpperCase().indexOf(filter) > -1 ||
					String(vue.userlist[key][value]["geo"]["timezone"]).toUpperCase().indexOf(filter) > -1 ||
					(vue.userlist[key][value]['advertisement']||"").indexOf(filter) > -1
				){ flag = true; break; }
			} else if(value == "served_list" || value == "serving_list"){ // consulated
				for(index in vue.userlist[key][value]["assistents"]){ 
					item = vue.userlist[key][value]["assistents"][index];
					let name = vue.assistents[item]["name"];
					if (name.toUpperCase().indexOf(filter) > -1){  flag = true; break; }
				}	
			} else if(filter == "обслуживают".toUpperCase() || filter == "обслуживали".toUpperCase()){
				let type = (filter == "обслуживают".toUpperCase() ? "serving_list" : "served_list");
				if (vue.userlist[key][type]["assistents"].length > 0) { flag = true; break; }
			}
		}
		let mas;
		// CRM
		if(vue.crm_items.hasOwnProperty(key) && 'CRM'.toUpperCase().indexOf(filter) <= -1) mas = vue.crm_items;
		else if(vue.crm_items.hasOwnProperty(key) || vue.crm_items.hasOwnProperty(key)) flag = true;
		if(mas){
			for(column in mas){
				if(String(mas[column]).toUpperCase().indexOf(filter) > -1){  flag = true; break; }
			}
		}
		if(!flag) vue.$delete(vue.searchmas, key);
		else Vue.set(vue.searchmas, key, vue.userlist[key]);
	}
}
function cards_search(vue, value) {
	let input = document.getElementsByClassName("cards_search_input")[0];
	let filter = input.value.toUpperCase();
    if(value) filter = value.toUpperCase();
	for (key in vue.userlist["rooms"]) {
		let flag = false;
		for (value in vue.userlist["rooms"][key]){
			if(!vue.userlist["rooms"][key][value] || value == "new_message") continue;
			else if(value == "lastActivityTime"){
				let time = vue.userlist["rooms"][key][value];
				if(time.toUpperCase().indexOf(filter) > -1) { flag = true; break; }
				else if((time.split(' ')[1].split(':').splice(0,2).join(':') + ' ' + time.split(' ')[0].split('-').reverse().join('.')).toUpperCase().indexOf(filter) > -1) { flag = true; break; }
			} else if(value == "domains_list"){ // поиск по доменам
				for(item in vue.userlist["rooms"][key][value]["domains"]){ 
					if (vue.userlist["rooms"][key][value]["domains"][item].toUpperCase().indexOf(filter) > -1){  flag = true; break; }
				}	
			} else if(value == 'notes'){
                for(prop in vue.userlist["rooms"][key]['notes']['notes']){
                    if(vue.userlist["rooms"][key]['notes']['notes'][prop].toUpperCase().indexOf(filter) > -1){ 
                        flag = true; 
                        break; 
                    }
                }
            } else if(value == 'properties'){
                for(prop in vue.userlist["rooms"][key]['properties']['properties']){
                    if((prop.toUpperCase() +' '+ vue.userlist["rooms"][key]['properties']['properties'][prop].toUpperCase()).indexOf(filter) > -1){ 
                        flag = true; 
                        break; 
                    }
                }
            } else if (String(vue.userlist["rooms"][key][value]).toUpperCase().indexOf(filter) > -1){ 
                flag = true; 
                break; 
            } else if(value == 'info'){ // geo, ip, adds
				if (
					String(vue.userlist["rooms"][key][value]["ip"]).toUpperCase().indexOf(filter) > -1 || 
					String(vue.userlist["rooms"][key][value]["geo"]["city"]).toUpperCase().indexOf(filter) > -1 || 
					String(vue.userlist["rooms"][key][value]["geo"]["country"]).toUpperCase().indexOf(filter) > -1 ||
					String(vue.userlist["rooms"][key][value]["geo"]["timezone"]).toUpperCase().indexOf(filter) > -1 ||
					(vue.userlist["rooms"][key][value]['advertisement']||"").indexOf(filter) > -1
				){ flag = true; break; }
			} else if(value == "served_list" || value == "serving_list"){ // consulated
				for(index in vue.userlist["rooms"][key][value]["assistents"]){ 
					item = vue.userlist["rooms"][key][value]["assistents"][index];
					let name = vue.assistents[item]["name"];
					if (name.toUpperCase().indexOf(filter) > -1){  flag = true; break; }
				}	
			} else if(filter == "обслуживают".toUpperCase() || filter == "обслуживали".toUpperCase()){
				let type = (filter == "обслуживают".toUpperCase() ? "serving_list" : "served_list");
				if (vue.userlist["rooms"][key][type]["assistents"].length > 0) { flag = true; break; }
			} 
            if(flag) break
		}
		let mas;
		// CRM
		if(vue.crm_items.hasOwnProperty(key) && 'CRM'.toUpperCase().indexOf(filter) <= -1) mas = vue.crm_items[key];
		else if(vue.crm_items.hasOwnProperty(key)) flag = true;
		if(mas){
			for(column in mas){
				if(String(mas[column]).toUpperCase().indexOf(filter) > -1){  flag = true; break; }
			}
		}
		if(!flag) vue.$delete(vue.searchmas["rooms"], key);
		else Vue.set(vue.searchmas["rooms"], key, vue.userlist["rooms"][key]);
	}
}
function get_reader(url){
    return url.search.replace('?','').split('&').reduce((p,e) => {
        var a = e.split('=');
        p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
        return p;
    },{});
}
function get_messages(socket, vue, buttlecry){
    socket.on('get_previous_messages_assistent', (data) => {
        vue.messages = [];
        for(key in data){
            if(!data[key].sender){
                vue.messages.push({
                    message: data[key].message,
                    photo: '/scss/imgs/user.png',
                    time: data[key].SendTime,
                    sender: '',
                    message_adds: data[key].adds,
                    mode: data[key].mode,
                });
            } else if(data[key].sender == 'notification'){
                vue.messages.push({
                    message: data[key].notification_text,
                    photo: data[key].notification_photo,
                    sender: data[key].sender,
                    message_adds: data[key].notification_adds,
                    departament: data[key].notification_departament,
                    phone: null,
                    user: data[key].notification_name,
                    time: data[key].SendTime,
                    mode: data[key].notification_type,
                });
            } else if(data[key]["name"]) {
                vue.messages.push({
                    message: data[key].message,
                    photo: data[key].photo,
                    sender: data[key].sender,
                    email: data[key].email,
                    message_adds: data[key].adds,
                    departament: data[key].departament,
                    phone: data[key].phone,
                    user: data[key].name,
                    time: data[key].SendTime,
                    mode: data[key].mode,
                });
            } else if(data[key].sender == "offline_form"){
                vue.messages.push({
                    message: data[key].form_message,
                    photo: 'user.png',
                    sender: data[key].sender,
                    email: data[key].form_email,
                    message_adds: null,
                    departament:null,
                    phone: data[key].form_phone,
                    user: data[key].form_name,
                    time: data[key].SendTime,
                    mode: data[key].mode,
                });
            } else {
                vue.messages.push({
                    message: data[key].message,
                    photo: 'user.png',
                    sender: data[key].sender,
                    email: null,
                    message_adds: null,
                    departament: null,
                    phone: null,
                    user: 'DELETED',
                    time: data[key].SendTime,
                    mode: data[key].mode,
                });
            }
        }
        if(vue.hasOwnProperty('messages_loaded')) vue.messages_loaded = true;
        setTimeout("$('#chat_body').scrollTop($('#chat_body')[0].scrollHeight + 10000000000)", 10);
        if(Object.keys(vue.messages).length == 0){
            $('#placeholder').css('display', 'none');
            $('.chat_block_textarea').html(buttlecry);
        }
    });
}
function smiles_folder(){
	let container = $(event.target).siblings('.smiles');
	if(container.hasClass('smiles_close')){
		container.removeClass('smiles_close');
		container.addClass('smiles_open');
	} else {
		container.removeClass('smiles_open');
		container.addClass('smiles_close');
	}
}
function assistents_page(token, dirname, departaments, domains){
    var vue = new Vue({ 
        el: '#container',
        data: {
            add: false,
            userlist: {},
            load:[],
            loader: false,
            departaments: Object.keys(departaments),
            domains: domains
        },
        methods: {
            sort_mas(array){
                let online = {}; 
                let offline = {};
                for(user in array){
                    key = user;
                    user = fix_array_max3inner(array[user]);
                    if(user.status == 'online') online[key] = user;
                    else offline[key] = user;
                }
                return Object.assign({}, online, offline);
            },
            ocform(){
                if(!vue.add) $('#add_assistent_block').css('max-height', '37em');
                else  $('#add_assistent_block').css('max-height', '0em');
                vue.add = !vue.add;
            },
            list(){
                let element = $(event.target);
                if(element.siblings('.departaments_list').length > 0) element = element.siblings('.departaments_list');
                else element = element.closest('.departaments_list');
                if($(element).css('max-height') == '0px') $(element).css('max-height', vue.departaments.length * 2 + 'rem');
                else $(element).css('max-height','0px');
            }, 
            change_assistent_photo(email, inputname){
                if (!event.target.files.length) return;
                let fd = new FormData;
                fd.append(inputname, $(event.target).prop('files')[0]);
                fd.append('assistent_img_id', email);
                formData_send_ajax('/engine/settings', fd);
            },
            change_assistent_info(id, inputname, value){
                if(!value) value = $(event.target).val();
                send_ajax('/engine/settings', {'changesName': inputname, 'changesValue': value, 'assistent_id': id});
            },
            remove_assistent(email){
                send_ajax('/engine/settings', {'remove_assistent': email});
            },
            add_assistent(){
                send_ajax('/engine/settings', $(event.target).serialize(), vue);
                vue.loader = true;
            },
            get_count(status){
                let count = 0;
                for(key in this.userlist){
                    if(this.userlist[key]['hide'] != true && this.userlist[key]["status"] == status) count++;
                }
                return count;
            },
            enter_assistent(id){
                send_ajax('/engine/settings', {'enter_assistent': id});
            },
            ban_assistent(id){
                send_ajax('/engine/settings', {'ban_assistent': id});
            },
        },
    });
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{ 
        socket.emit('boss_join', {'room': 'MAIN', 'token': token});
        socket.emit('get_teammate_mas'); 
    });
    socket.io.on('reconnect', () =>{  socket.emit('get_teammate_mas'); });
    socket.on('assistentlist_update', (data)=>{ 
        let type = data.type; let value = data.value; let target = data.target; let option = htmldecoder(data.option);
        if(type == "new_assistent"){  Vue.set(vue.userlist, target, htmldecoder(value)); }
        if(type == "status") vue.userlist[target]["status"] = htmldecoder(value);
        if(type == "change_settings"){
          if (option != "remove"){ vue.$set(vue.userlist[target], option, htmldecoder(value)); } 
          else vue.$delete(vue.userlist, target);
        } 
        if(option == "settings") Vue.set(vue.userlist[target], option, htmldecoder(value));
    });
    socket.on('get_teammate_mas', (data) => { vue.userlist = data.assistents; console.log(data); });
}
function employee_page(token, dirname, info, regexp, emojis){
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    var vue = new Vue({ 
        el: '#app',
        data: {
            name:  htmldecoder(info.name),
            photo: htmldecoder(info.photo),
            email: htmldecoder(info.email),
            buttlecry: htmldecoder(info.buttlecry),
            departament: htmldecoder(info.departament),
            pass: false,
            animations: (localStorage.anima == "true"),
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            emojis: emojis,
            old: null,
            newpass: null,
            repeat: null,
        },
        methods: {
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            changepass(){ send_ajax('/engine/settings', {'personal_info_value': JSON.stringify({'old': vue.old, 'new': vue.newpass, 'repeat': vue.repeat}), 'personal_info_column': 'password'}); },
            change(type){
                if(type == 'photo') {
                    formData_send_ajax('/engine/settings', construct_formData($(event.target).prop('files')[0], 'profile_photo'));
                    readURL($(event.target).prop('files')[0], '#assistent_img_place');
                } else send_ajax('/engine/settings', {'personal_info_value': vue[type], 'personal_info_column': type});
            },
            exit(){
                send_ajax('/engine/assistent_login', {'exit': true});
            },
        },
        watch:{
            animations(value){ localStorage.anima = value; },
            pass(some){
                this.$nextTick(() => {
                    $('.password_eye').on('click', function(e) {
                        if($(this).siblings('input').attr('type') == 'password'){ 
                            $(this).siblings('input').attr('type', 'text');
                            $(this).css('background-image', 'url(/scss/imgs/open_eye.png)');
                        } else { 
                            $(this).siblings('input').attr('type', 'password');
                            $(this).css('background-image', 'url(/scss/imgs/close_eye.png)');
                        }
                    });
                });
            },
        },
    });
    socket.io.on('reconnect', () => { location.reload(); });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': 'PERSONAL', 'token': token});
        socket.emit('assistent-check');
    });
    socket.on('page_reload', () => { location.reload(); });
    team_msg_notification(vue, socket);
    socket.on('assistentlist_update', (data)=>{ 
        data.value = htmldecoder(data.value);
        if(data.option == 'photo') vue.photo = htmldecoder(data.value);
        if(data.option == 'remove') location.reload();
        if(data.option == 'phone') vue.phone = htmldecoder(data.value);
        if(data.option == 'departament') vue.departament = htmldecoder(data.value);
        if(data.option == 'email') vue.email = htmldecoder(data.value);
        if(data.option == 'name') vue.name = htmldecoder(data.value);
        if(data.option == 'buttlecry') vue.buttlecry = htmldecoder(data.value);
    });
}
function team_msg_notification(vue, socket){
    $('.close_notification').on('click', () => {
        $('.message_notification_form').css('right', '-500px'); 
    });
    socket.on('consultant_message_notification', (data) => { 
        vue.notification_msg = data;
        if(!vue.room) $('.message_notification_form').css('right', '100px'); 
        else if(vue.room != data.sender || data.notification_type == 'assistent_chat') $('.message_notification_form').css('right', '100px'); 
        if(data.notification_type == 'assistent_chat'){
            if(!(vue.userlist||{}).assistents) return;
            if(vue.userlist.assistents && !data.type){
                vue.userlist.assistents[data.sender]['message'] = data.message;
                vue.userlist.assistents[data.sender]['message_adds'] = data.message_adds;
            } else if(vue.userlist.assistents.public_room){
                vue.userlist.assistents['public_room']['message'] = data.message;
                vue.userlist.assistents['public_room']['message_adds'] = data.message_adds;
            }
        } else {
            if(!(vue.userlist||{}).assistents) return;
            vue.userlist.rooms[data.sender]['new_message']['message'] = data.message;
            vue.userlist.rooms[data.sender]['new_message']['status'] = 'unreaded';
            vue.userlist.rooms[data.sender]['new_message']['message_adds'] = data.message_adds;
        }
    }); 
}
function remember_pasword(){
    let mail = prompt('Введите вашу зарегистрированную почту');
    if(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)) send_ajax('/engine/assistent_login', {"reset-password": mail});
}
function assistent_login(e){ 
    e.preventDefault();
    send_ajax('/engine/assistent_login', $(e.target).serialize()); 
}
function move_navigation(){
    let header_pos = localStorage.header_position; 
    if(header_pos){
        $('header').css({'left': '-120px'});
        $('#app').css({'width':'100%', 'left': '0'});
        $('#container').css({'width':'100%', 'left': '0'});
        $('.incolumn_left').css('left', '0');
        $('.chat_statistic').css('left', '65px');
        $('.header_control').removeClass('active_header_control');
        $('.header_control').addClass('unactive_header_control');
        $('.header_control').css('left', '0');
    }
}
move_navigation();
function control(type, obj, active, unactive){
	let el = $('.'+type);
	if(el.hasClass('unactive_'+type)){
		$(obj).css(active);
		el.removeClass('unactive_'+type);
		el.addClass('active_'+type);
		if(obj == 'header'){ 
			$('#app').css({"width":"calc(100% - 90px)", "left": "90px"});
			$('#container').css({"width":"calc(100% - 90px)", "left": "90px"});
			$('.header_control').css({"left": '90px'});
			$('.incolumn_left').css({"left": "90px"});
			$('.chat_statistic').css({"left": "160px"});
			delete localStorage.header_position;
		}
	} else {
		$(obj).css(unactive);
		el.removeClass('active_'+type);
		el.addClass('unactive_'+type);
		if(obj == 'header'){ 
			$('#app').css({"width":"100%", "left": 0});
			$('#container').css({"width":"100%", "left": 0});
			$('.header_control').css({"left": 0});
			$('.incolumn_left').css({"left": "0"});
			$('.chat_statistic').css({"left": "65px"});
			localStorage.header_position = 'unactive';
		}
	}
}
function crm_page(token, dirname, tables, today, local_date, regexp, emojis, crm, tasks, fastmessages, mailer, domains){
    var params = get_reader(window.location);
    let get_name = params["type"];
    for(table in tables){
        for(column in tables[table]['deffault_columns']){
            tables[table]["table_columns"][column] = tables[table]['deffault_columns'][column];
        }
    } 
    var shown_value = 0;
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    change_table(socket, url_page, get_name);
    socket.on('connect', () => {
        socket.emit('assistent-join', {'room': 'MAIN', 'token': token});
        socket.emit('get_crm-info');
    });
    socket.on('page_reload', () => { location.reload(); });
    let vue = new Vue({
        el: '#app',
        data: {
            users: {},
            filterusers: {},
            domains: domains,
            selected_domain: 'deffault',
            searchmas: {},
            complete_task_count: 0,
            travel_table: 'не выбрано',
            uncomplete_task_count: 0,
            mailer: mailer,
            filters: true,
            movement_panel: true,
            recepient: {
                'name': '',
                'email': '',
            },
            control_panel: true,
            travel_mode: false,
            load_count: 25,
            load_more_count: 25,
            selected: [],
            mailer_selected: {},
            new_table_mode:false,
            date: today,
            mailer_name: false,
            local_date: local_date,
            mailer_mode: false,
            tables: Object.keys(tables),
            columns: (get_name ? sort_columns(fix_array_max3inner(tables[get_name]['table_columns']), this) : {}),
            columns_withoutfix: (get_name ? tables[get_name]['table_columns'] : {}),
            add_mode:false,
            remove_mode:false,
            standart_columns: ['photo', 'name', 'info'],
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            tasks: {},
            emojis: emojis,
            get_name: get_name,
            counters: {},
            files: [],
            sender_name: mailer['deffault'].sender_name,
            mail_name: mailer['deffault'].mail_name,
            fastmessages: {
                "search_chapter": '',
                "search_message": '',
                "selected_chapter": 'main',
                "chapters_mode": false,
                'chapters': fastmessages,
            },
            commands_mode: false,
            mail_column: null,
            loader:false,
            selected_task:null,
        },
        created() {
            socket.on('crm_teleport', (data) => {
                if(vue.users[data.index]){
                    vue.$delete(vue.users, data.index); 
                    vue.$delete(vue.searchmas, data.index); 
                } else if(data.table == vue.get_name) {
                    Vue.set(vue.searchmas, data.index, data.item); 
                    Vue.set(vue.users, data.index, data.item);
                    setTimeout(bindcrm_scroll, 100);
                    setTimeout(bindheader_scroll, 100);
                }
            });
            socket.on('add_item', (data) => {  
                vue.counters[data.table]++;   
                if(data.table != vue.get_name) return;
                Vue.set(vue.searchmas, data.index, data.info); 
                Vue.set(vue.users, data.index, data.info);
                setTimeout(bindcrm_scroll, 100);
                setTimeout(bindheader_scroll, 100);
            });
            socket.on('delete_item', (data) => {  
                vue.counters[data.table]--;
                if(data.table != vue.get_name) return;
                vue.$delete(vue.users, data.index); 
                vue.$delete(vue.searchmas, data.index); 
            });
            socket.on('change_item', (data) => {  
                if(data.table != vue.get_name) return; 
                if(vue.searchmas.hasOwnProperty(data.index)) Vue.set(vue.searchmas[data.index], vue.escapeHtml(data.column), vue.escapeHtml(data.value));
                if(vue.users.hasOwnProperty(data.index)) Vue.set(vue.users[data.index], vue.escapeHtml(data.column), vue.escapeHtml(data.value));
                if(vue.filterusers.hasOwnProperty(data.index)) Vue.set(vue.filterusers[data.index], vue.escapeHtml(data.column), vue.escapeHtml(data.value));
            });
            socket.on('new_table', (data) => { vue.tables.push(data.index); })
            get_crm_info(socket, this);
            socket.on('error_msg', (data) => { alert(data.text, 'error'); });
        }, 
        updated(){ shown_value = 0; },
        mounted(){
            control("choose_guests_btn", ".incolumn", {"top": "-"+($('.incolumn').height() + 20)+"px"}, {"top": "0"});
            control("mailer_crm_menu_btn", ".mailer_crm_menu", {"bottom": "-" +59+"px"}, {"bottom": "0"});
            control("add_task_menu_btn", ".add_task_menu", {"right": -$(".add_task_menu").width() - 20}, {"right": 0});
            remove_loader();
            if(localStorage.load_count) this.load_count = parseInt(localStorage.load_count);
            if(localStorage.load_more_count) this.load_more_count = parseInt(localStorage.load_more_count);
            if(localStorage.filters) this.filters = (localStorage.filters == "true");
            if(localStorage.movement_panel) this.movement_panel = (localStorage.movement_panel == "true");
            if(localStorage.control_panel) this.control_panel = (localStorage.control_panel == "true");
        },
        methods: {
            remove_task(){
                send_ajax('/engine/settings', {'crm_remove_task_index': vue.selected_task}); 
                for(task_index in vue.tasks){
                    for(select in vue.tasks[task_index]){
                        if(vue.tasks[task_index][select].index == vue.selected_task) Vue.delete(vue.tasks[task_index], select);
                    }
                }
                vue.selected_task = null;
                vue.close_user_task();
            },
            copy_table(){
                send_ajax('/engine/settings', {'copy_table': vue.get_name });
            },
            mailer_select_all(){
                if(!vue.mail_column){ alert('Вы не выбрали колонку для почты !', 'error'); return; }
                for(index in vue.searchmas){
                    let email = vue.users[index][vue.mail_column];
                    let res = {'email': email};
                    if(vue.mailer_name) res['name'] = vue.users[index]['helper_name'];
                    if(validateEmail(email)) vue.$set(vue.mailer_selected, 'email', res);
                }
                if(Object.keys(vue.mailer_selected).length == 0) alert('Никто не прошёл проверку на почту.', 'error'); 
            },
            mailer_select(index){
                if(!vue.mail_column){ alert('Вы не выбрали колонку для почты !', 'error'); return; }
                let email = vue.users[index][vue.mail_column];
                if(validateEmail(email)){ 
                    if(vue.mailer_selected.hasOwnProperty(index)){
                        Vue.delete(vue.mailer_selected, index);
                        return;
                    }
                    let res = {'email': email};
                    if(vue.mailer_name) res['name'] = vue.users[index]['helper_name'];
                    vue.$set(vue.mailer_selected, index, res);
                } else alert('Почта не указана или введена не верно.', 'error');
            },
            add_table(){
                let value = $(event.target).siblings('input').val();
                send_ajax('/engine/settings', {'table_add': value});
            },
            sort_array(header, type, el){ sort_array(header, type, $(event.target), vue) },
            check_file(file_name){
                if(!file_name) return '/scss/imgs/photo.png';
                return (regexp.indexOf(file_name.substr(file_name.lastIndexOf("."), file_name.length)) == -1 ? '/crm_files/'+file_name : '/scss/imgs/folders.png')
            },
            check_task(data){
                let task_info_container = $('.user_task_info');
                task_info_container.removeClass('task_close');
                vue.selected_task = data.index;
                task_info_container.addClass('task_open');
                let selected_users = '';
                for(selected_uid in data.selected){
                    if(vue.users[data.selected[selected_uid]]) selected_users += `
                        <div class="task_prev_inner">
                            <span style="background-image:url(/crm_files/${vue.users[data.selected[selected_uid]].helper_photo})"></span>
                            <span>
                                ${vue.users[data.selected[selected_uid]].helper_name}
                            </span>
                        </div>
                    `;
                }
                let now = new Date();
                let time = new Date(data.time);
                $(task_info_container).find('.user_task_users').html(selected_users);
                $(task_info_container).find('.user_task_time').html(
                    data.time.split(' ')[0].split('-').reverse().join('.') + ' <span style="font-weight:bold;color:#f90;">' + data.time.split(' ')[1].split(':').slice(0, 2).join(":") + '</span>' + 
                    ' <span style="font-weight:bold;color:'+(time > now ? "tomato" : "lightgreen")+';">'+(time > now ? 'Не выполнена' : 'Выполнена')+'</span>'
                );
                $(task_info_container).find('.user_task_text').html(data.text);
                $(task_info_container).find('.user_task_type').html((data.type == 1 ? 'Публичная' : 'Личная'));              
            },
            check_page(page){
                return (window.location.href.split('/')[window.location.href.split('/').length - 1] == page ? 'active' : '');
            },
            travel_user(index){
                if(vue.tables.indexOf(vue.travel_table) == -1){ alert('Выберите таблицу для переноса !', 'error'); return;}
                if(!localStorage.hasOwnProperty('travel_notification')){
                    if(confirm('При отсутстивии заполненных колонок в таблице для переноса, записи пропадут ! Вы хотите продолжить ?')){
                        localStorage['travel_notification'] = true;
                    } else return;
                }
                send_ajax('/engine/settings', {'teleport_index': index, "table_to": vue.travel_table, "table_from": get_name});
            },
            find(index){
                $('.crm_serch_panel input').val(index);
                search(vue, index);
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            close_user_task(){
                $('.user_task_info').removeClass('task_open');
                $('.user_task_info').addClass('task_close');
            },
            escapeHtml(text) { return $("<div/>").html(text.replaceAll('<br/>', '\n')).text(); },
            returnClass(index){
                if(index.indexOf('!@!@2@!@!') != -1) return index.split('!@!@2@!@!')[1].replaceAll('.', '_');
                return index;
            },
            add_user(){ send_ajax('/engine/settings', {'crm_item_add_table': get_name}); },
            delete_user(index){ send_ajax('/engine/settings', {'crm_item_delete_index': index, 'crm_item_delete_table': get_name}); },
            add_task(){
                if(!$('.task_date_time').val()){ alert('Введите время для задачи !', 'error'); return; }
                let input_time = Date.parse($('.task_date_time').val());
                let today = Date.parse(vue.local_date);
                if(input_time < today){ alert('Мы не сможем вернуться в прошлое =/'); return; }
                if($('.choosen_task_type').data('choice') != 0 && $('.choosen_task_type').data('choice') != 1 ){ alert('Выберите тип задачи !', 'error'); return }
                if(!$('.task_text').val()){ alert('Введите задачу !', 'error'); return; }
                if(vue.selected.length == 0){ alert('Выберите лидов !', 'error'); return; }
                time = $('.task_date_time').val().split('T')[0] + ' ' + $('.task_date_time').val().split('T')[1] + ':00';
                let text = $('.task_text').val();
                let type = $('.choosen_task_type').data('choice');
                send_ajax('/engine/settings', {'task_time': time, 'task_text': text, 'task_type': type, 'task_selected': JSON.stringify(vue.selected), 'table': get_name}, vue);
                vue.add_mode = false;
            },
            check_count(){
                if(Object.keys(this.searchmas).length > this.load_count) return true;
                else return false;
            },
            load_more(){ shown_value = 0; vue.load_count = parseInt(vue.load_count) + parseInt(vue.load_more_count); },
            sort_mas(array){
                let fast_array = {}
                let mas = [];
                for(i in array){ array[i]['header'] = i; mas.push(array[i]); }
                for(let i = mas.length - 1; i != -1; i--){ 
                    shown_value++;
                    fast_array[mas[i]['header']] = mas[i]; 
                    delete fast_array[mas[i]['header']]['header']; 
                    if(shown_value >= vue.load_count) return fast_array;
                }
                return fast_array;
            },
            change(index, column){ 
                let value =  $(event.target).val();
                if(column == 'helper_photo' || (vue.columns[column]||{}).type == 6){
                    let inputname = 'crm_item_img';
                    let fd = new FormData;
                    fd.append(inputname, $(event.target).prop('files')[0]);
                    fd.append('crm_item_index', index);
                    fd.append('crm_item_column', column);
                    fd.append('crm_item_table', get_name);
                    formData_send_ajax('/engine/settings', fd);
                } else send_ajax('/engine/settings', {'crm_item': index, 'crm_column': column, 'crm_value': value, 'table': get_name});
            },
            filter(variant, column){
                let element = $(event.target).children('span');
                if(element.hasClass('unchecked_btn_span')){
                    element.removeClass('unchecked_btn_span');
                    element.addClass('checked_btn_span');
                    filter_search(variant, true, column, vue);
                } else {
                    element.removeClass('checked_btn_span');
                    element.addClass('unchecked_btn_span');
                    filter_search(variant, false, column, vue);
                }
            },
            task_type_control(){
                if( $('.task_type_menu').css('max-height') == '0px') $('.task_type_menu').css('max-height', '10em');
                else $('.task_type_menu').css('max-height', '0px');
            },
            select(index){  
                if(vue.selected.indexOf(index) == -1){
                    if(vue.users[index]['helper_name'] != 'новый' && vue.users[index]['helper_name']) vue.selected.push(index);
                    else alert('Заполните поле ввода имени, для того, чтобы добавить его в задачу !', 'error');
                } else vue.selected.splice(vue.selected.indexOf(index), 1);
            },
            choose_type(type){
                if(type) $('.choosen_task_type').text('Публичная');
                else $('.choosen_task_type').text('Личная');
                $('.choosen_task_type').data('choice', type);
                $('.task_type_menu').css('max-height', '0px');
            },
            search(text){
                if(!text || text == '') text = $(event.target).val();
                search(vue, text);
            },
            htmldecode(text){
                return htmldecoder(text);
            },
            mailer_handleChange(){
                let e = event.target;
                if (!e.files.length) return;    
                const files = Object.keys(e.files).map((i) => e.files[i]);
                vue.$set(vue, 'files', vue.files.concat(files));
                $(e).val('');
            },
            mailer_removeFile(index) { 
                vue.files.splice(index, 1); 
            },
            add_recepient(){
                if(!validateEmail(vue.recepient.email)){ alert('Введите почту правильно !', 'error'); return;}
                let res = {'email': vue.recepient.email};
                if(vue.recepient.name != '' && vue.recepient.name) res['name'] = vue.recepient.name;
                vue.$set(vue.mailer_selected, vue.recepient.email, res); 
                vue.$set(vue.recepient, 'name', ''); 
                vue.$set(vue.recepient, 'email', '');
            },
            remove_recepient(index){
                Vue.delete(vue.mailer_selected, index);
            },
            send_mails(){
                let fd = new FormData;
                fd.append('mailer_name', vue.mail_name);
                fd.append('mailer_info', JSON.stringify(Object.values(vue.mailer_selected)));
                fd.append('sender_name', vue.sender_name);
                fd.append('mailer_message', $(".chat_block_textarea").text());
                fd.append('design_domain', vue.selected_domain);
                vue.files.map((file, index) => { fd.append(`mailer_files${index + 1}`, file); });
                formData_send_ajax('/engine/settings', fd, vue);
                vue.loader = true;
            },
            create_chapter(type){
                if(type == 'new') vue.$set(vue.fastmessages, 'create_chapter', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages, 'create_chapter');
                else if(type == 'save'){
                    let value = vue.fastmessages.create_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_chapter", "fastMessages_value": value}, vue);
                    Vue.delete(vue.fastmessages, 'create_chapter');
                }
            },
            newfastmessage(type){
                if(type == 'new') vue.$set(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                else if(type == 'save'){
                    let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter].create_fastmessage;
                    let column = vue.fastmessages.selected_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value})}, vue);
                    Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                }
            },
            copy_fastmessage(uid){
                $('.fast_message_'+uid).focus().select();
                document.execCommand('copy');
            },
            remove_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                send_ajax('/engine/settings', {"fastMessages_type": "remove_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": uid}) });
                Vue.delete(vue.fastmessages.chapters[column], uid);
            },
            update_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                let value = $('.fast_message_'+uid).val();
                send_ajax('/engine/settings', {"fastMessages_type": "save_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value, "uid": uid}) });
            },
            remove_chapter(uid){
                send_ajax('/engine/settings', {"fastMessages_type": "remove_chapter", "fastMessages_value": uid });
                if(uid == vue.fastmessages.selected_chapter) vue.$set(vue.fastmessages, 'selected_chapter', 'main');
                Vue.delete(vue.fastmessages.chapters, uid);
            },
            update_chapter(){
                let chapter = vue.fastmessages.selected_chapter;
                let chapter_name = vue.fastmessages.chapters[chapter].chapter_name;
                send_ajax('/engine/settings', {"fastMessages_type": "chapter_name", "fastMessages_value": JSON.stringify({"value": chapter_name, "uid": chapter})});
            },
        },
        watch: {
            load_count(some){ localStorage.load_count = some;},
            load_more_count(some){ localStorage.load_more_count = some; },
            filters(some){localStorage.filters = some; },
            movement_panel(some){localStorage.movement_panel = some; },
            control_panel(some){localStorage.control_panel = some; },
            mailer_mode(some){ vue.mailer_selected = {}; },
            add_mode(some){ vue.selected = []; },
            selected_domain(some){
                vue.sender_name =  mailer[some].sender_name;
                vue.mail_name  = mailer[some].mail_name;
            }
        }
    });
    crm_getter(crm, vue, params['search'], get_name, socket);
    task_getter(tasks, vue, true);
    team_msg_notification(vue, socket);
}
function filter_search(filter_element, type, column, vue){
	if(column != "tasks"){
		let input = filter_element;
		let filter = String(input).toUpperCase();
		for(key in vue.users){
			if(vue.users[key][column]){
				if(vue.users[key][column].toUpperCase().indexOf(filter) == -1 && (filter != 'helper_empty_fields'.toUpperCase() || vue.users[key][column] != '')) continue;
				if (type) vue.$delete(vue.filterusers, key);
				else Vue.set(vue.filterusers, key, vue.users[key]);
			} else {
				if(vue.columns[column]){
					if (String(vue.columns[column].deffault||"").toUpperCase().indexOf(filter) == -1 && (filter != 'helper_empty_fields'.toUpperCase() || vue.columns[column].deffault)) continue;
					if (type) vue.$delete(vue.filterusers, key);
					else Vue.set(vue.filterusers, key, vue.users[key]);
				}
			}
		}
	} else {
		for(key in vue.users){
			if(vue.tasks.hasOwnProperty(key) != filter_element) continue;
			if (type) vue.$delete(vue.filterusers, key);
			else Vue.set(vue.filterusers, key, vue.users[key]);
		}
	}
	vue.searchmas = {};
 	vue.searchmas = JSON.parse(JSON.stringify(vue.filterusers)); 
	search(vue);
}
function task_getter(tasks, vue, sort_by_users){
    if(vue.hasOwnProperty('tasks_loaded')) vue.tasks_loaded = true;
    if(!sort_by_users) vue.tasks = tasks;
    else {
        for(task_uid in tasks){
            if(tasks[task_uid].table != vue.get_name && url_page.indexOf('hub') == -1 && url_page.indexOf('chat') == -1) continue;
            for(selected_id in tasks[task_uid].selected){
                let selected_uid = tasks[task_uid].selected[selected_id];
                if(!vue.tasks.hasOwnProperty(selected_uid)) vue.$set(vue.tasks, selected_uid, []);
                tasks[task_uid].index = task_uid;
                vue.tasks[selected_uid].push(tasks[task_uid]);
            }
        }
    }
}
function sort_columns(array, vue){
	let new_array = {}; 
	let fast_array = [];
	for(key in array){ array[key]['header'] = key; fast_array.push(array[key]); } 
	sortByPriority(fast_array, 'priority', '', false, vue);
	for(key in fast_array){
		let header = fast_array[key]['header'];
		delete fast_array[key]['header'];
		new_array[header] = fast_array[key];
	}
	return new_array;
}
function get_crm_info(socket, vue){
    socket.on('get_crm-info', (data) => { 
        vue.complete_task_count = data.complete_tasks; 
        vue.uncomplete_task_count = data.uncomplete_tasks; 
        for(index in data){
            if(index == 'complete_tasks' || index == 'uncomplete_tasks') continue;
            vue.$set(vue.counters,index, data[index]);
        }
    });
}
function search(vue, search) {
	let input = document.getElementsByClassName("crm_serch_input")[0];
	let filter = input.value.toUpperCase();
    if(search) filter = search.toUpperCase();
	for (key in vue.filterusers) {
		let flag = false;
		for (value in vue.filterusers[key]){
			if (vue.filterusers[key][value].toUpperCase().indexOf(filter) > -1 || key.toUpperCase() == filter){ flag = true; break; }
		}
		if(!flag) vue.$delete(vue.searchmas, key);
		else Vue.set(vue.searchmas, key, vue.filterusers[key]);
	}
	setTimeout(bindcrm_scroll, 100);
    setTimeout(bindheader_scroll, 100);
}
function bindheader_scroll(){
    $('.crm_user_card:first-child').on('scroll', function(e) { 
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function(){
            bindcrm_scroll();
        },500);
        $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:not(:first-child)').unbind('scroll');
        let rightScroll = $('.crm_menu_container:not(.v-cloak-on) .crm_cards_container').scrollTop() + $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:first-child').scrollTop();
        let el_height = $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:not(:first-child)').height();
        let this_el = Math.round(rightScroll / el_height); let i = j = this_el; j++;
        let max_index = $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:not(:first-child)').length - 1;
        while((i != this_el - 10 && i >= 0) || (j != this_el + 10 && j <= max_index)){
            if(j != this_el + 10 && j <= max_index)
                $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:not(:first-child)')[j].scrollLeft = $(this).scrollLeft();
            if(i != this_el - 10 && i >= 0)
                $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:not(:first-child)')[i].scrollLeft = $(this).scrollLeft();
            i--; j++;
        }
    });
}
function bindcrm_scroll(){
    $('.crm_user_card:not(:first-child)').on('scroll', function(e) { 
        if (scrollTimeout2) clearTimeout(scrollTimeout2);
        scrollTimeout2 = setTimeout(function(){
            bindheader_scroll();
        },500);
        $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:first-child').unbind('scroll');
        $('.crm_menu_container:not(.v-cloak-on) .crm_user_card:first-child').scrollLeft($(this).scrollLeft());
    });
}
function crm_getter(crm, vue, search, table, socket){
    if(table == 'all'){
        vue.items = fix_array_max3inner(crm);
    } else {
        let unexist_columns = {};
        for(user in crm) { // UNEXIST COLUMNS
            for(column in crm[user]) {
                if(!vue.columns_withoutfix.hasOwnProperty(column) && vue.standart_columns.indexOf(column) == -1){ 
                    if(!unexist_columns.hasOwnProperty(user)) unexist_columns[user] = {}
                    unexist_columns[user][column] = column;
                    console.log('unexist-column', user, column);
                }
            } 
        }
        let all_cards = fix_array_max3inner(crm);
        vue.$set(vue, 'searchmas', JSON.parse(JSON.stringify(all_cards))||{});
        vue.$set(vue, 'filterusers', JSON.parse(JSON.stringify(all_cards))||{});
        vue.$set(vue, 'users', JSON.parse(JSON.stringify(all_cards))||{});
        if(Object.keys(unexist_columns).length > 0) socket.emit('unexits_columns', {'users': unexist_columns, 'table': table});
        setTimeout(bindcrm_scroll, 100);
        setTimeout(bindheader_scroll, 100);
        if(search) vue.find(search);
    }
}
function tasks_page(token, dirname, regexp, emojis, tasks, items, tables){
    $(document).ready(function() {
        if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
            boxClass:     'wow',      
            animateClass: 'animated',
            offset:       0,       
            mobile:       true,       
            live:         true,      
            callback:     function(box) {},
            scrollContainer: '.Online-List-User2',
        }).init();
    });
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': 'MAIN', 'token': token});
        socket.emit('get_crm-info');
    });
    socket.on('page_reload', () => { location.reload(); });
    let vue = new Vue({
        el: '#app',
        data: {
            tables: Object.keys(tables),
            items: {},
            uncomplete_task_count: 0,
            complete_task_count: 0,
            counters:{},
            movement_panel: true,
            tasks: {},
            regexp: regexp,
            new_table_mode: false,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            loaded: false,
            emojis: emojis,
            tasks_loaded: false,
        },
        mounted(){ remove_loader(); },
        created() {
            get_crm_info(socket, this);
            socket.on('new-task', (data) => { 
                vue.uncomplete_task_count++;
                vue.$set(vue.tasks, data.uid, data.info); 
            });
            socket.on('new_table', (data) => { vue.tables.push(data.index); })
            socket.on('delete_task', (data) => {
                if(vue.tasks[data.uid]["status"] == 'completed') vue.complete_task_count--;
                else vue.uncomplete_task_count--;
                Vue.delete(vue.tasks, data.uid); 
            });
        },
        methods: {
            find(uid, group){ location.href = `/engine/consultant/crm?type=${group}&search=${uid}`; },
            add_table(){
                let value = $(event.target).siblings('input').val();
                send_ajax('/engine/settings', {'table_add': value});
            },
            room_list(e){
                if($(e).hasClass('room_options_close')){
                    $('.room_options').removeClass('room_options_open');
                    $('.room_options').addClass('room_options_close');
                    $(e).removeClass('room_options_close');
                    $(e).addClass('room_options_open');
                    $('.room_option').css('top','-20px');
                    $(e).parent().children(".room_option").each(function(i,elem){
                        $(elem).css('top',(40 * (i + 1) + i * 15) +'px');
                    });
                } else {
                    $('.room_option').css('top','-20px');
                    $(e).removeClass('room_options_open');
                    $(e).addClass('room_options_close');
                }
            },
            remove_task(index){ send_ajax('/engine/settings', {'crm_remove_task_index': index}); },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            task_sort(array){
                let completed = {};
                let uncompleted = {};
                for(task_index in array){
                    task = array[task_index];
                    if(new Date(task.time) > new Date()) completed[task_index] = array[task_index];
                    else uncompleted[task_index] = array[task_index];
                }
                return Object.assign(uncompleted, completed);
            },
            check_selected(selected, index){
                for(select in selected){
                    if(vue.items[selected[select]]) return true;
                }
                vue.remove_task(index);
                return false;
            }
        },
    });
    crm_getter(items, vue, null, 'all', socket);
    task_getter(tasks, vue, false);
    team_msg_notification(vue, socket);
}
function change_table(socket, url_page, get_name){
    socket.on('change_table', (data) => {
        if(data.table_to && data.table_from && get_name == data.table_from) location.href = '/engine/consultant/'+url_page+'?'+table_to;
        else if(data.table_remove) location.href = '/engine/consultant/crm';
    });
}
function crm_settings_page(token, dirname, tables, max_count, emojis, regexp){
    var params = get_reader(window.location);
    let get_name = params['type'];
    for(table in tables){
        for(column in tables[table]['deffault_columns']){
            tables[table]["table_columns"][column] = tables[table]['deffault_columns'][column];
        }
    } 
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () => {
        socket.emit('assistent-join', {'room': 'MAIN', 'token': token});
        socket.emit('get_crm-info');
    });
    change_table(socket, url_page, get_name);
    socket.on('page_reload', () => { location.reload(); });
    let vue = new Vue({
        el: '#app',
        data: {
            choosen_type: 0,
            choosen_deffault: null,
            choosen_choices: [],
            movement_panel: true,
            new_table_mode:false,
            new_choice: null,
            tables: Object.keys(tables),
            columns: sort_columns(fix_array_max3inner(tables[get_name]['table_columns']), this),
            column_redactor: null,
            deffault_val: null,
            deffault_file: null,
            valute: 0,
            hide_column: false,
            column_index: null,
            column_header: null,
            selected_column: null,
            max_count: max_count,
            complete_task_count: 0,
            uncomplete_task_count: 0,
            counters: {},
            priority_value: null,
            regexp:regexp,
            emojis:emojis,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            get_name: get_name,
        },
        created() {
            get_crm_info(socket, this);
            socket.on('error_msg', (data) => { alert(data.text, 'error'); });
            socket.on('new_table', (data) => { vue.tables.push(data.index); })
        }, 
        mounted(){ remove_loader(); },
        methods: {
            add_table(){
                let value = $(event.target).siblings('input').val();
                send_ajax('/engine/settings', {'table_add': value});
            },
            change_table_name(){
                let new_table_name = $(event.target).val();
                let prev_table_name  = vue.get_name;
                send_ajax('/engine/settings', {'prev_table_name': prev_table_name, 'new_table_name': new_table_name});      
            },
            deffault_photo(){
                let file = $(event.target).prop('files')[0];
                let file_name = file.name;
                $(event.target).val('');
                vue.deffault_file = file;
                if(vue.regexp.indexOf(file_name.substr(file_name.lastIndexOf("."), file_name.length)) == -1) readURL(file, $('.deffault_photo'));
                else $('.deffault_photo').css('background-image', 'url(/scss/imgs/folders.png)');
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            save_column(){
                if(vue.column_index != 'helper_photo' && vue.column_index != 'helper_name' && vue.column_index != 'helper_info'){
                    if(!(vue.choosen_type || vue.choosen_type == 0)  || !vue.column_header){ alert('Заполнены не все поля', 'error'); return;}
                    if(!isNaN(vue.column_header)){ alert('Название не может быть числом !', 'error'); return; }
                    let deffault, variants;
                    let header = vue.column_header;
                    let type = vue.choosen_type;
                    let priority = vue.priority_value;
                    if(vue.choosen_type == 0 || vue.choosen_type == 1 || vue.choosen_type == 3 || vue.choosen_type == 4) deffault = vue.deffault_val; 
                    else if(vue.choosen_type == 2) {
                        deffault = vue.choosen_deffault;
                        variants = vue.choosen_choices;
                    } else if(vue.choosen_type == 5) deffault = JSON.stringify({'value': vue.deffault_val, 'type': vue.valute});
                    else if(vue.choosen_type == 6) deffault = vue.deffault_file;
                    else { alert('Не существующий тип столбца', 'error'); return; }
                    if(vue.choosen_type != 6) send_ajax('/engine/settings', {
                        'save_type': type, 
                        'save_header': header, 
                        'deffault': deffault, 
                        'variants': JSON.stringify(variants), 
                        'column_index': vue.column_index, 
                        'priority': priority, 
                        'table': get_name
                    }, vue);      
                    else {
                        let fd = new FormData;
                        fd.append('deffault_file', deffault);
                        fd.append('save_type', type); 
                        fd.append('save_header', header); 
                        fd.append('priority', priority); 
                        fd.append('table', get_name);
                        fd.append('column_index', vue.column_index);
                        formData_send_ajax('/engine/settings', fd, vue);
                    }
                } else {
                    let priority = vue.priority_value;
                    let display = String(vue.hide_column);
                    vue.$delete(vue.columns, vue.selected_column);  
                    let header = vue.column_index;  
                    send_ajax('/engine/settings', {'display': display, 'priority': priority, 'column_type': get_name, 'deffault_header': header, 'table': get_name});     
                    Vue.set(vue.columns, header, {"display": display, 'priority': priority});  
                }
                vue.column_redactor = null;
                vue.selected_column = null;
            },
            delete_column(){   
                send_ajax('/engine/settings', {'delete_column': vue.selected_column, 'table': get_name});                   
                vue.$delete(vue.columns, vue.selected_column);      
                vue.column_redactor = null;
                vue.selected_column = null;
            },
            sort_columns(array){
                let new_array = {}; 
                let fast_array = [];
                for(key in array){ array[key]['header'] = key; fast_array.push(array[key]); } 
                sortByPriority(fast_array, 'priority', '', false, this);
                for(key in fast_array){
                    let header = fast_array[key]['header'];
                    delete fast_array[key]['header'];
                    new_array[header] = fast_array[key];
                }
                return new_array;
            },
            new_column(){
                if(!vue.choosen_type && !vue.column_header) return;
                if(!isNaN(vue.column_header)){ alert('Название не может быть числом !', 'error'); return false; }
                let deffault, variants;
                let priority = vue.priority_value; 
                let header = vue.column_header;
                let type = vue.choosen_type;
                if(!type) type = 0;
                if(vue.choosen_type == 0 || vue.choosen_type == 1 || vue.choosen_type == 3 || vue.choosen_type == 4) deffault = vue.deffault_val;
                else if(vue.choosen_type == 2) {
                    deffault = vue.choosen_deffault;
                    variants = vue.choosen_choices;
                } else if(vue.choosen_type == 5) deffault = JSON.stringify({'value': vue.deffault_val, 'type': vue.valute});
                else if(vue.choosen_type == 6) deffault = vue.deffault_file;
                else { alert('Не существующий тип столбца', 'error'); return; }
                if(vue.choosen_type != 6) send_ajax('/engine/settings', {
                    'type': type, 
                    'deffault': deffault, 
                    'header': header, 
                    'variants': JSON.stringify(variants), 
                    'priority': priority, 
                    'table': get_name
                }, vue);    
                else {
                    let fd = new FormData;
                    fd.append('deffault_file', deffault);
                    fd.append('type', type); 
                    fd.append('header', header); 
                    fd.append('priority', priority); 
                    fd.append('table', get_name);
                    formData_send_ajax('/engine/settings', fd, vue);
                    deffault = deffault.name;
                }
                vue.column_redactor = null;
            },
            add_new_choice(){
                if(vue.choosen_choices.indexOf(vue.new_choice) == -1 && vue.choosen_choices.length <= vue.max_count && vue.new_choice){ 
                    vue.choosen_choices.push(vue.new_choice);
                    vue.new_choice = null;
                }
                else if(vue.choosen_choices.length > vue.max_count) alert('Слишком много вариантов !', 'error');
            },
            delete_default(index){
                vue.choosen_choices.splice(vue.choosen_choices.indexOf(index), 1);
                if(vue.choosen_deffault == index) vue.choosen_deffault = null;
            },
            list(type){
                if($(type).css('max-height') == '0px') $(type).css('max-height','1000rem');
                else $(type).css('max-height','0px');
            },  
            choose_deffault(value){
                if(vue.choosen_deffault == value) vue.choosen_deffault = null;
                else vue.choosen_deffault = value;
            },
            table_remove(){
                if(!confirm('Вы уверены ? Пропадут и все записи этой таблицы')) return;
                let table_remove  = vue.get_name;
                send_ajax('/engine/settings', {'table_remove': table_remove});      
            },
            htmldecoder(str){ return $("<div/>").html(str).text(); },
        },
        watch: {
            column_redactor(val){
                if(val == 'new'){
                    vue.choosen_type = 0;
                    vue.deffault_file = null;
                    vue.choosen_deffault = "Не выбрано";
                    vue.choosen_choices = [];
                    vue.valute = 0;
                    vue.new_choice = null;
                    vue.deffault_val = null;
                    vue.column_header = null;
                    vue.selected_column = null;
                    vue.priority_value = null;
                    vue.column_index = null;
                    vue.hide_column = null;
                } else if(!val){ $('.active_column').removeClass('active_column'); }
            },
            selected_column(val){
                if(val){
                    vue.column_index = vue.selected_column;
                    vue.hide_column = (vue.columns[vue.selected_column].display == 'true');
                    vue.choosen_type = vue.columns[vue.selected_column].type;
                    if(vue.columns[vue.selected_column].type != 5 && vue.columns[vue.selected_column].type != 6){ 
                        vue.deffault_val = vue.choosen_deffault = vue.columns[vue.selected_column].deffault;
                    } else if(vue.columns[vue.selected_column].type == 6) {
                        let file_name = vue.columns[vue.selected_column].deffault;
                        let style = $(`<style>.deffault_photo{background-image: url(/scss/imgs/folders.png);}</style>`);
                        if(file_name){
                            if(vue.regexp.indexOf(file_name.substr(file_name.lastIndexOf("."), file_name.length)) == -1){
                                style = $(`<style>.deffault_photo{background-image: url(/crm_files/${file_name});}</style>`);
                            }
                        }
                        $('body').append(style);
                    } else {
                        vue.deffault_val = vue.columns[vue.selected_column].deffault.value;
                        vue.valute = vue.columns[vue.selected_column].deffault.type;
                    }
                    if(vue.columns[vue.selected_column].variants){ 
                        if(typeof(vue.columns[vue.selected_column].variants) == 'object') vue.choosen_choices = Object.values(vue.columns[vue.selected_column].variants);
                        else if(typeof(vue.columns[vue.selected_column].variants) == 'array') vue.columns[vue.selected_column].variants;
                    }
                    else vue.choosen_choices = [];
                    vue.new_choice = null;
                    if(['helper_name', 'helper_photo', 'helper_info'].indexOf(vue.selected_column) != -1) vue.column_header = vue.selected_column;
                    else vue.column_header = vue.columns[vue.selected_column].helper_column_name;
                    vue.priority_value = vue.columns[vue.selected_column].priority;
                }
            },
        },
    });
    team_msg_notification(vue, socket);
}
function hub_page(token, dirname, assistents, domains, regexp, emojis, fastmessages, tables, personal_id, tasks){
    $(document).ready(function() {
        $(".chat_block_textarea").bind("DOMSubtreeModified", function(){ 
            vue.newMessage = $(this).html();
            if (vue.newMessage.length > 0) $('#placeholder').css('display', 'none');
            else  $('#placeholder').css('display', 'block');
        });
    });
    let loaded_users = -1;
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': 'MAIN', 'token': token});
        socket.emit('get-guests-mas-first');
        socket.emit('get_new_assistent_chat_messages', "personal_consulation_messages");
    });
    socket.on('get_new_assistent_chat_messages', (data)=>{
        $.each(data.mas, (index, elem) => {
            Vue.set(vue.userlist.rooms[index], "new_message", elem);
            if((vue.searchmas.rooms||{})[index]) Vue.set(vue.searchmas.rooms[index], "new_message", elem);
        });
    });
    socket.on('choosen-user-answer', (data) => { vue.choosen_users_load = false; });
    socket.on('success_msg', (data) => { alert(data.text, 'success'); });
    socket.on('baned_user', (data) => { 
        vue.$delete(vue.userlist.rooms, data.target); 
        vue.$delete(vue.searchmas.rooms, data.target); 
    });
    socket.on('unbaned_user', (data) => { 
        Vue.set(vue.userlist.rooms, data.guest_id, data.info); 
        Vue.set(vue.searchmas.rooms, data.guest_id, data.info); 
    });
    socket.on('error_msg', (data) => { alert(data.text, 'error'); });
    socket.on('page_reload', () => { location.reload(); });
    let vue = new Vue({
        el: '#app',
        data: {
            searchmas: {},
            filtermas: {},
            personal_id: personal_id,
            crm_items: {},
            selected_domain: 'all',
            userlist: {},
            tables: Object.keys(tables),
            assistents: assistents,
            load_count: 50,
            send_all_load: false,
            choosen_users_load: false,
            domains: domains,
            choosen_users: {},
            choose_mode: false,
            shown_user_counter: 0,
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            emojis: emojis,
            cache_variables: {
                'audio_new_user':{ 
                    "status": true,
                    "name": "Отключить звук уведомляющий о новых посетителях",
                }, 
                'deleted':{ 
                    "status": true,
                    "name": "Показать удалённых посетителей",
                }, 
                'selected':{ 
                    "status": true,
                    "name": "Показать выбранных посетителей",
                },
                'audio_message':{ 
                    "status": true,
                    "name": "Отключить звук новых сообщений",
                }, 
                'audio_user':{ 
                    "status": true,
                    "name": "Отключить звук уведомляющий о возвращении посетителя",
                }, 
                'offline':{ 
                    "status": true,
                    "name": "Скрыть оффлайн посетителей",
                }, 
                'online': { 
                    "status": true,
                    "name": "Скрыть онлайн посетителей",
                }, 
                'consulated': { 
                    "status": true,
                    "name": "Скрыть обслуженных оффлайн посетителей",
                }, 
                'consulated_online':{ 
                    "status": true,
                    "name": "Скрыть обслуженных онлайн посетителей",
                }, 
                'unconsulated': { 
                    "status": true,
                    "name": "Скрыть не обслуженных офлайн посетителей",
                }, 
                'unconsulated_online':{ 
                    "status": true,
                    "name": "Скрыть не обслуженных онлайн посетителей",
                }, 
                'offline_unexist_messages':{ 
                    "status": true,
                    "name": "Скрыть оффлайн комнаты без сообщений",
                }, 
                'online_unexist_messages':{ 
                    "status": true,
                    "name": "Скрыть онлайн комнаты без сообщений",
                }, 
                'offline_exist_messages':{ 
                    "status": true,
                    "name": "Скрыть оффлайн комнаты с сообщениями",
                }, 
                'online_exist_messages':{ 
                    "status": true,
                    "name": "Скрыть онлайн комнаты с сообщениями",
                }, 
                'offline_adds':{ 
                    "status": true,
                    "name": "Скрыть оффлайн посетителей пришедших рекламы",
                }, 
                'online_adds':{ 
                    "status": true,
                    "name": "Скрыть онлайн посетителей пришедших рекламы",
                }, 
                'offline_not_adds':{ 
                    "status": true,
                    "name": "Скрыть оффлайн посетителей не пришедших рекламы",
                }, 
                'online_not_adds':{ 
                    "status": true,
                    "name": "Скрыть оффлайн посетителей пришедших рекламы",
                }, 
                'myconsulating_online':{ 
                    "status": true,
                    "name": "Скрыть мною обслуживаемых онлайн посетителей",
                }, 
                'myconsulating_offline':{ 
                    "status": true,
                    "name": "Скрыть мною обслуживаемых офлайн посетителей",
                }, 
                'myconsulated_online':{ 
                    "status": true,
                    "name": "Скрыть мною обслуженных онлайн посетителей",
                }, 
                'myconsulated_offline':{ 
                    "status": true,
                    "name": "Скрыть мною обслуженных оффлайн посетителей",
                }, 
                'otherconsulating_online': { 
                    "status": true,
                    "name": "Скрыть обслуживаемых другими онлайн посетителей",
                }, 
                'otherconsulating_offline':{ 
                    "status": true,
                    "name": "Скрыть обслуживаемых другими офлайн посетителей",
                }, 
                'otherconsulated_online':{ 
                    "status": true,
                    "name": "Скрыть обслуженных другими онлайн посетителей",
                }, 
                'otherconsulated_offline':{ 
                    "status": true,
                    "name": "Скрыть обслуженных другими онлайн посетителей",
                }, 
                'filters': {
                    "status": true
                },
            },
            message_panel: false,
            js_mode: false,
            style_mode: false,
            commands_mode: false,
            files: [], 
            load: true,
            newMessage: '',
            smiles_mode: false,
            fastmessages: {
                "search_chapter": '',
                "search_message": '',
                "selected_chapter": 'main',
                "chapters_mode": false,
                'chapters': fix_array_max3inner(fastmessages),
            },
            tasks: {},
        },
        mounted(){
            control("chat_statistic_btn", ".chat_statistic", {"top": -$(".chat_statistic").height() - 20}, {"top": "0"});
            remove_loader();
            control("choose_guests_btn", ".incolumn", {"top":  -$(".incolumn").height() - 20}, {"top": "0"});
            for(filter in this.cache_variables){
                if(localStorage[filter]) this.cache_variables[filter]["status"] = ("true" == localStorage[filter]);
            }
        },
        updated(){ loaded_users = -1; vue.shown_user_counter = $('.OnlineUser').length; },
        created() {
            socket.on('get-guests-mas-first', (data) => { 
                Vue.set(vue.userlist, 'rooms', data.rooms);
                Vue.set(vue.searchmas, 'rooms', Object.assign({}, data.rooms));
                this.crm_items = data.crm_items || {};
                this.$nextTick(function () {
                    if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
                        boxClass:     'wow',      
                        animateClass: 'animated',
                        offset:       0,       
                        mobile:       true,       
                        live:         true,      
                        callback:     function(box) {},
                        scrollContainer: '#visitors_wow',
                    }).init();
                    var options = {
                        root: document.querySelector('.Online-List-User2'),
                        rootMargin: '0px',
                        threshold: 1.0
                    }
                    var callback = (entries, observer) => {
                        if(entries[0].isIntersecting) this.load_more();
                    };
                    var observer = new IntersectionObserver(callback, options);
                    observer.observe(vue.$refs.observer);
                });
            }); // получаем комнаты посетителей
            socket.on('userlist_update', (data)=>{ change_room(data.type, data.value, data.target, data.option, data.lastActivityTime, vue); }); // изменения комнат
        },
        methods: {
            cards_search() {
                let value = $(event.target).val();
                cards_search(vue, value);
            },
            create_chapter(type){
                if(type == 'new') vue.$set(vue.fastmessages, 'create_chapter', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages, 'create_chapter');
                else if(type == 'save'){
                    let value = vue.fastmessages.create_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_chapter", "fastMessages_value": value }, vue);
                    Vue.delete(vue.fastmessages, 'create_chapter');
                }
            },
            newfastmessage(type){
                if(type == 'new') vue.$set(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                else if(type == 'save'){
                    let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter].create_fastmessage;
                    let column = vue.fastmessages.selected_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value})}, vue);
                    Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                }
            },
            copy_fastmessage(uid){
                $('.fast_message_'+uid).focus().select();
                document.execCommand('copy');
            },
            remove_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                send_ajax('/engine/settings', {"fastMessages_type": "remove_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": uid}) });
                Vue.delete(vue.fastmessages.chapters[column], uid);
            },
            update_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                let value = $('.fast_message_'+uid).val();
                send_ajax('/engine/settings', {"fastMessages_type": "save_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value, "uid": uid}) });
            },
            remove_chapter(uid){
                send_ajax('/engine/settings', {"fastMessages_type": "remove_chapter", "fastMessages_value": uid });
                if(uid == vue.fastmessages.selected_chapter) vue.$set(vue.fastmessages, 'selected_chapter', 'main');
                Vue.delete(vue.fastmessages.chapters, uid);
            },
            update_chapter(){
                let chapter = vue.fastmessages.selected_chapter;
                let chapter_name = vue.fastmessages.chapters[chapter].chapter_name;
                send_ajax('/engine/settings', {"fastMessages_type": "chapter_name", "fastMessages_value": JSON.stringify({"value": chapter_name, "uid": chapter})});
            },
            select_smile(smile, folder, smile_file){
                let res = $('.chat_block_textarea').html();
                let res2 = `<img class="InterHelper_emoji" src="/emojis/${folder}/${smile_file}" alt="${smile}" />`;
                $('.chat_block_textarea').html(res + res2); 
            },
            remove_command(command){ send_ajax('/engine/settings', {'assistent_delete_command': command}); },
            paste_command(command){ 
                if($(event.target).hasClass('command_remove') || $(event.target).hasClass('command_remove_span')) return;
                $('.chat_block_textarea').html($('.chat_block_textarea').html() + command); 
            },
            removeFile(index) { vue.files.splice(index, 1); },
            new_command(){ send_ajax('/engine/settings', $('#new_command_form').serialize()); },
            get_time_diff(element){
                if(element.session_time && element.status == 'offline') return vue.msToTime(element.session_time);
                else if(!element.session_time && element.status == 'offline') return '00:00:00';
                let diff = Math.abs(new Date() - new Date(element.session_start));
                diff += element.session_time;
                vue.$set(element, 'current_session_time', diff);
                vue.$set(element, 'timer', setInterval(() => {
                    element.current_session_time += 1000;
                }, 1000, element));
                return '00:00:00';
            },
            msToTime(duration) {
                var seconds = parseInt((duration/1000)%60)
                , minutes = parseInt((duration/(1000*60))%60)
                , hours = parseInt((duration/(1000*60*60))%24);
            
               hours = (hours < 10) ? "0" + hours : hours;
               minutes = (minutes < 10) ? "0" + minutes : minutes;
               seconds = (seconds < 10) ? "0" + seconds : seconds;
            
               return hours + ":" + minutes + ":" + seconds;
            },
            filter_status(filterIndex){
                vue.cache_variables[filterIndex]['status'] = !vue.cache_variables[filterIndex]['status'];
                localStorage[filterIndex] = vue.cache_variables[filterIndex]['status'];
            },
            get_count(type, status){
                let count = 0;
                for(key in this.userlist['rooms']){
                    if(this.userlist.rooms[key]['hide'] != true && this.userlist['rooms'][key][type] == status && type == "status") count++;
                    else if(type == "served_list" || type=="serving_list") {if(this.userlist['rooms'][key][type]["assistents"].length > 0) count++;}
                    else if(type == "time"){
                        let lastActivityTime = new Date(this.userlist['rooms'][key]["lastActivityTime"]);
                        let prev_day = new Date();
                        prev_day.setDate(prev_day.getDate() - 1);
                        if(lastActivityTime > prev_day) count++;
                    }
                    else if(type == "advertisement" && this.userlist['rooms'][key].info.advertisement) count++;
                }
                return count;
            },
            sort_mas(array){
                // сообщение
                let online_unreaded_unconsulated = {}; // 1
                let online_unreaded_consulated = {}; // 2
                let offline_unreaded_unconsulated = {}; // 3
                let offline_unreaded_consulated = {}; // 4
                // консультация
                let online_unconsulated = {}; // 5
                let online_consulated = {}; // 6
                let offline_unconsulated = {}; // 7
                let offline_consulated = {}; // 8
                let offline_flag = true;
                cache_variables = vue.cache_variables;
                for(user in array){
                    key = user;
                    user = array[user];
                    if( 
                        ( // сообщений нет оффлайн
                            ((!user.messages_exist && cache_variables['offline_unexist_messages']['status']) || user.messages_exist) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // сообщения есть оффлайн
                            ((user.messages_exist && cache_variables['offline_exist_messages']['status']) || !user.messages_exist) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // сообщений нет онлайн
                            ((!user.messages_exist && cache_variables['online_unexist_messages']['status']) || user.messages_exist) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && ( // собщения есть онлайн
                            ((user.messages_exist && cache_variables['online_exist_messages'])['status'] || !user.messages_exist) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && ( // оффлйн
                            cache_variables['offline']['status'] || user.status == 'online' 
                        ) && ( // онлайн
                            cache_variables['online']['status'] || user.status == 'offline' 
                        ) && ( // удалённые
                            (cache_variables['deleted'].status && !user.hide) || 
                            (!cache_variables['deleted'].status && user.hide) 
                        ) && ( // обслуженные оффлайн
                            ((cache_variables['consulated']['status'] && user.served_list.assistents.length > 0) || user.served_list.assistents.length == 0) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // обслуженные онлайн
                            ((cache_variables['consulated_online']['status'] && user.served_list.assistents.length > 0) || user.served_list.assistents.length == 0) && 
                            user.status == 'online' || user.status == 'offline'  
                        ) && ( // не обслуженные оффлайн
                            ((cache_variables['unconsulated']['status'] && user.served_list.assistents.length == 0) || user.served_list.assistents.length > 0) &&
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // обслуженные онлайн
                            ((cache_variables['unconsulated_online']['status'] && user.served_list.assistents.length == 0) || user.served_list.assistents.length > 0) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && ( // без рекламы онлайн
                            ((cache_variables['online_not_adds']['status'] && !user.info.advertisement) || user.info.advertisement) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && ( // без рекламы оффлайн
                            ((cache_variables['offline_not_adds']['status'] && !user.info.advertisement) || user.info.advertisement) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // c рекламы оффлайн
                            ((cache_variables['offline_adds']['status'] && user.info.advertisement) || !user.info.advertisement) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // c рекламы онлайн
                            ((cache_variables['online_adds']['status'] && user.info.advertisement) || !user.info.advertisement) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && ( // мною консультироанный оффлайн
                            ((cache_variables['myconsulated_offline']['status'] && user.served_list.assistents.indexOf(vue.personal_id) != -1) || user.served_list.assistents.indexOf(vue.personal_id) == -1) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // мною консультируемый онлайн
                            ((cache_variables['myconsulated_online']['status'] && user.served_list.assistents.indexOf(vue.personal_id) != -1) || user.served_list.assistents.indexOf(vue.personal_id) == -1) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && ( // мною консультироанный оффлайн
                            ((cache_variables['myconsulating_offline']['status'] && user.serving_list.assistents.indexOf(vue.personal_id) != -1) || user.serving_list.assistents.indexOf(vue.personal_id) == -1) && 
                            user.status == 'offline' || user.status == 'online' 
                        ) && ( // мною консультируемый онлайн
                            ((cache_variables['myconsulating_online']['status'] && user.serving_list.assistents.indexOf(vue.personal_id) != -1) || user.serving_list.assistents.indexOf(vue.personal_id) == -1) && 
                            user.status == 'online' || user.status == 'offline' 
                        ) && (  // консультированные оффлайн
                            (
                                (cache_variables['otherconsulated_offline']['status'] && user.served_list.assistents.indexOf(vue.personal_id) == -1 && user.served_list.assistents.length > 0) || 
                                user.served_list.assistents.length == 0 || user.served_list.assistents.indexOf(vue.personal_id) != -1
                            ) && 
                            user.status == 'offline' || user.status == 'online'
                        ) && ( // консультированные онлайн
                            (
                                (cache_variables['otherconsulated_online']['status'] && user.served_list.assistents.indexOf(vue.personal_id) == -1 && user.served_list.assistents.length > 0) || 
                                user.served_list.assistents.length == 0 || user.served_list.assistents.indexOf(vue.personal_id) != -1
                            ) && 
                            user.status == 'online' || user.status == 'offline'
                        ) && ( // консультируемые оффлайн
                            (
                                (cache_variables['otherconsulating_offline']['status'] && user.serving_list.assistents.indexOf(vue.personal_id) == -1 && user.serving_list.assistents.length > 0) || 
                                user.serving_list.assistents.length == 0 || user.serving_list.assistents.indexOf(vue.personal_id) != -1
                            ) && 
                            user.status == 'offline' || user.status == 'online'
                        ) && ( // консультируемые онлайн
                            (
                                (cache_variables['otherconsulating_online']['status'] && user.serving_list.assistents.indexOf(vue.personal_id) == -1 && user.serving_list.assistents.length > 0) || 
                                user.serving_list.assistents.length == 0 || user.serving_list.assistents.indexOf(vue.personal_id) != -1
                            ) && 
                            user.status == 'online' || user.status == 'offline'
                        ) && ( // выбранные посетители
                            (cache_variables['selected'].status ) || 
                            (!cache_variables['selected'].status && vue.choosen_users.hasOwnProperty(key)) 
                        )
                    ){
                        if(user.domains_list.domains.indexOf(vue.selected_domain) == -1 && vue.selected_domain != 'all') continue;
                        if(user.status == 'offline' && user.new_message.status != 'unreaded') loaded_users++;
                        if(loaded_users >= vue.load_count) offline_flag = false;
                        if(user.new_message.status == 'unreaded'){
                            if(user.status == 'online'){
                                if(user.served_list.assistents.length == 0) online_unreaded_unconsulated[key] = user;
                                else online_unreaded_consulated[key] = user;
                            } else {
                                if(user.served_list.assistents.length == 0) offline_unreaded_unconsulated[key] = user;
                                else offline_unreaded_consulated[key] = user;
                            }
                        } else {
                            if(user.status == 'online'){
                                if(user.served_list.assistents.length == 0) online_unconsulated[key] = user;
                                else online_consulated[key] = user;
                            } else if(offline_flag){
                                if(user.served_list.assistents.length == 0) offline_unconsulated[key] = user;
                                else offline_consulated[key] = user;
                            }
                        }
                    }
                }
                return Object.assign(online_unreaded_unconsulated, online_unreaded_consulated, offline_unreaded_unconsulated, 
                offline_unreaded_consulated, online_unconsulated, online_consulated, offline_unconsulated, offline_consulated);
            },
            handleChange(){
                let e = event.target;
                if (!e.files.length) return;    
                const files = Object.keys(e.files).map((i) => e.files[i]);
                vue.$set(vue, 'files', vue.files.concat(files));
                $(e).val('');
            },
            check_count(){
                if($('.OnlineUser').length >= this.load_count) return true;
                return false;
            },
            load_more(){ loaded_users = -1; vue.load_count += 10; },
            user_counter(){
                if(loaded_users <= vue.load_count){ loaded_users++; return true; } 
                return false;                       
            },
            choose(status, type){
                $.each(vue.sort_mas(vue.searchmas.rooms), (index, value) => {
                    if((type == "all" && value['status'] == status && !vue.choosen_users.hasOwnProperty(index)) || (value.served_list.length <= 0 && type == 'online_unconsulated')) Vue.set(vue.choosen_users, index, index);
                }); 
            },
            room_add(room){  Vue.set(vue.choosen_users, room, room); },
            room_delete(room){  vue.$delete(vue.choosen_users, room); },
            choosen_users_func(type){
                if(Object.keys(vue.choosen_users).length <= 0){ alert("Сначала выберите посетителей ! ", 'error'); return;}
                if(type == 'restore'){
                    for(user_id in vue.choosen_users){
                        if(!vue.userlist.rooms[user_id].hide) vue.$delete(vue.choosen_users, user_id);
                    }
                    if(Object.keys(vue.choosen_users).length <= 0){ alert("Вы не выбрали ни одного удалённого посетителя ! ", 'error'); return;}
                }
                if(type == 'dialog_start' || type == 'dialog_stop'){
                    for(user_id in vue.choosen_users){
                        if(
                            ((vue.userlist.rooms[user_id].serving_list.assistents.length == 0 || vue.userlist.rooms[user_id].serving_list.assistents.indexOf(vue.personal_id) == -1) && type == 'dialog_stop') ||
                            ((vue.userlist.rooms[user_id].serving_list.assistents.length != 0 || vue.userlist.rooms[user_id].serving_list.assistents.indexOf(vue.personal_id) != -1) && type == 'dialog_start')
                        ) vue.$delete(vue.choosen_users, user_id);
                    }
                    if(Object.keys(vue.choosen_users).length <= 0){ 
                        if(type == 'dialog_start') alert("Вы не выбрали посетителей или все выбранные вами посетители вами не консультируются ! ", 'error'); 
                        else alert("Вы не выбрали посетителей или все выбранные вами посетители уже консультируются ! ", 'error'); 
                        return;
                    }
                }
                let reason;
                if(type == 'ban'){ 
                    reason = prompt('Причина бана ? ', 'Оскорбление чувств верующих моей обители!');
                    if(!reason) return;
                } else if(type == 'hide'){ 
                    let confirm = confirm('Вы уверены, что хотите удалить выбранных ?');
                    if(!confirm) return;
                }
                vue.choosen_users_load = true;
                send_ajax('/engine/settings', {'type':  type, 'selected_visitors':JSON.stringify(vue.choosen_users), 'reason':reason}); 
            },
            remove_room(roomname, type){ socket.emit('remove_room', {'room': roomname, 'type': type}); },
            ban_room(roomname){
                let reason = prompt('Причина бана ? ', 'Оскорбление чувств верующих моей обители!');
                if(reason) socket.emit('ban_room', {'room': roomname, 'reason':reason});
            },
            add_room(index, type){ 
                socket.emit('add_from_cards', {'index': index, 'table': type}); 
                vue.modal_window('close', 'add_mv_' + index.split('!@!@2@!@!')[1].replaceAll('.', '_'));
            },
            room_list(e){
                if($(e).hasClass('room_options_close')){
                    $('.room_options').removeClass('room_options_open');
                    $('.room_options').addClass('room_options_close');
                    $(e).removeClass('room_options_close');
                    $(e).addClass('room_options_open');
                    $('.room_option').css('top','-20px');
                    $(e).parent().children(".room_option").each(function(i,elem){
                        $(elem).css('top',(40 * (i + 1) + i * 15) +'px');
                    });
                } else {
                    $('.room_option').css('top','-20px');
                    $(e).removeClass('room_options_open');
                    $(e).addClass('room_options_close');
                }
            },
            modal_window(status, ob){
                if(status == 'close') $('#' + ob).css('display', 'none');
                else if(status == 'open') $('#' + ob).css('display', 'flex');
            },
            consultation(index, type){ 
                send_ajax('/engine/settings', {'room':  index, 'type': type}); 
                if(type == "start" || type == "restart" || type == "continue"){
                    socket.emit('consultant_chat_readed', {
                        "room": index,
                        "type": "personal_consulation_messages"
                    });
                }
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            send() {
                if(Object.keys(vue.choosen_users).length <= 0){ alert("Сначала выберите посетителей ! ", 'error'); return;}
                vue.smiles_mode = false;
                let message = this.newMessage;
                var container = $('<div>').html(message);
                container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                message = container.html();
                message = $("<div/>").html(message).text();
                if(this.newMessage && Object.keys(this.files).length == 0) send_ajax('/engine/settings', {
                    "message_to_guset": message, 
                    "selected": JSON.stringify(vue.choosen_users),
                    "mode": (vue.js_mode ? 'js_mode' : (vue.style_mode ? 'style_mode' : null)),
                });  // сообщение 
                else if(Object.keys(this.files).length > 0 && !vue.style_mode && !vue.js_mode) formData_send_ajax('/engine/settings', getFilesFormData2(vue.files, JSON.stringify(vue.choosen_users), message, (vue.js_mode ? 'js_mode' : (vue.style_mode ? 'style_mode' : null)))); // сообщение + файлы
                else if(vue.js_mode || vue.style_mode) alеrt("Нельзя отправить файл, не отключив спец-режимы.");
                else alert("Поля не заполнены", 'error');
                this.newMessage = '';
                $('.chat_block_textarea').html('');
                vue.files = [];
            },
        },
        watch: {
            choose_mode(some){
                if(!some) vue.choosen_users = {};
            },
        }
    });
    crmchanges(socket, vue);
    task_getter(tasks, vue, true);
    team_msg_notification(vue, socket);
    console.log(vue.userlist);
}
function change_room(type, value, target, option, time, vue){
    if (!vue.userlist.hasOwnProperty("rooms")) Vue.set(vue.userlist, 'rooms', {});
	if(type == "typing"){
		if(value != null && value != undefined){ if(value.replaceAll(' ', '') == ''){ value = null;} } 
			if (vue.userlist.rooms.hasOwnProperty(target)) vue.userlist.rooms[target]["typing"] = value;
	} else if(type == "served_list" || type == "serving_list"){
		if(option == "delete") vue.userlist.rooms[target][type]["assistents"].splice(vue.userlist.rooms[target][type]["assistents"].indexOf(value), 1);
		if(option == "add"){ 
			vue.userlist.rooms[target][type]["assistents"].push(value);
			if(type == "serving_list") vue.userlist.rooms[target]["new_message"]["status"] = "readed";
		}
	} else if(type == "delete") vue.userlist.rooms[target]["hide"] = true; 
	else if(type == "restore") vue.userlist.rooms[target]["hide"] = false; 
	else if(type == "room_settings") vue.userlist.rooms[target][option] = value;
	else if(type == "domain"){ Vue.set(vue.userlist.rooms[target]["domain_adress"]["domains"], Object.keys(vue.userlist.rooms[target]["domain_adress"]["domains"]).length, value); }
	else if(type == "message"){
		if (vue.userlist.rooms.hasOwnProperty(target)){ 
				vue.userlist.rooms[target]["new_message"]["message"] = value["message"];
				vue.userlist.rooms[target]["new_message"]["message_adds"] = value["message_adds"];
				vue.userlist.rooms[target]["new_message"]["status"] = "unreaded";
		}
		if(vue.cache_variables.audio_message) new_message_signal();
	} else if(type == "new_ip"){
		let old_info = vue.userlist.rooms[target];
		vue.$delete(vue.userlist.rooms, target);
		Vue.set(vue.userlist.rooms, value, old_info);
	} else if(type == "new_guest"){
		Vue.set(vue.userlist.rooms, target, value);
		if(vue.searchmas) Vue.set(vue.searchmas.rooms, target, value);
		if(vue.cache_variables.audio_new_user) new_user_signal();
	} else if(type == "status"){
		if(vue.audio_user && value == "online" && vue.userlist.rooms[target]["status"] == 'offline') new_user_signal();
		if(value == 'offline') clearInterval(vue.userlist.rooms[target]["timer"]);
		if(value == 'online') vue.$set(vue.userlist.rooms[target], 'timer', setInterval(() => {
			vue.userlist.rooms[target].current_session_time += 1000;
		}, 1000, vue, target));
		if (vue.userlist.rooms.hasOwnProperty(target)) vue.userlist.rooms[target]["status"] = value;
		if (vue.userlist.rooms.hasOwnProperty(target)) vue.userlist.rooms[target]["hide"] = false;
		if(option){
			if (vue.userlist.rooms.hasOwnProperty(target)){ 
				if(option[0]) vue.userlist.rooms[target]['prev_page'] = option[0];
				if(option[1]) vue.userlist.rooms[target]['this_page'] = option[1];
				if(option[2]) vue.userlist.rooms[target]['visits'] = option[2];
			}
		}
	} else if(type == 'dialog_start' || type == 'dialog_stop'){
        if(vue.userlist.rooms[target].serving_list.assistents.indexOf(value) != -1) vue.userlist.rooms[target].serving_list.assistents.splice(vue.userlist.rooms[target].serving_list.assistents.indexOf(value), 1);
        else vue.userlist.rooms[target].serving_list.assistents.push(value);
    }
	if(time) if (vue.userlist.rooms.hasOwnProperty(target)) vue.userlist.rooms[target]["lastActivityTime"] = time;   
}
function chat_page(token, dirname, regexp, emojis, personal_id, fastmessages, properties, notes, buttlecry, tables, tasks){
    var params = get_reader(window.location);
    $(document).ready(function() {
        let foot_height = $('#chat_footer').height();
        $('#chat_body').css('height', `calc(700px - ${foot_height}px)`);
        $(".chat_block_textarea").bind("DOMSubtreeModified", function(){ 
            vue.newMessage = $(this).html();
            if (vue.newMessage.length > 0) $('#placeholder').css('display', 'none');
            else  $('#placeholder').css('display', 'block');
            let foot_height = $('#chat_footer').height();
            $('#chat_body').css('height', `calc(700px - ${foot_height}px)`);
        });
        if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
            boxClass:     'wow',      
            animateClass: 'animated',
            offset:       0,       
            mobile:       true,       
            live:         true,      
            callback:     function(box) {},
            scrollContainer: '#chat_body',
        }).init();
    });
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () => {
       socket.emit('assistent-join', {'room': params['room'], 'token': token});
       socket.emit('get-assistent-messages', {'type': 'guest'});
       socket.emit('room_status', {'type': 'guest'});
    });
    socket.on('page_reload', () => { location.reload(); });
    socket.on('error_msg', (data) => { alert(data.text, 'error'); });
    let dates = [];
    let vue = new Vue({
        el: '#app',
        data: {
            tasks: {},
            commands_mode: false,
            load: true,
            commands: 'disabled',
            newMessage: '',
            messages: [],
            smiles_mode: false,
            js_mode: false,
            style_mode: false,
            visits: 1,
            tables: Object.keys(tables),
            photo: {
                color: "#0ae",
                img: null,
            },
            typing: null,
            add_crm_mode: false,
            card_load: true,
            messages_loaded: false,
            info: {},
            emojis: emojis,
            status: '',
            files_path: '/user_adds/',
            this_page: '',
            prev_page: '',
            room: params['room'],
            fastmessages: {
                "search_chapter": '',
                "search_message": '',
                "selected_chapter": 'main',
                "chapters_mode": false,
                'chapters': fix_array_max3inner(fastmessages),
            },
            notes: JSON.parse(notes),
            properties: JSON.parse(properties),
            files: [],
            room_time: null,
            room_timer: null,
            add_files: null,
            g_photo: 'user.png',
            g_name: null, 
            g_type: null,
            g_columns: {},
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            new_property_value: null,
            new_property_name: null,
            new_note: null,
        },
        updated(){ dates = []; },
        created() {
            socket.on('stoptyping', () =>{ vue.typing = null; });
            socket.on('guest-message', (data) => {
                this.messages.push(data);
                new_message_signal();
                socket.emit('consultant_chat_readed', {
                    "room": vue.room,
                    "type": "personal_consulation_messages"
                });
            });
            socket.on('guest_print', (data) => { vue.typing = data.text; });
            socket.on('consultant-message', (data) => {
                vue.load = true;
                this.messages.push(data);
                if(data.sender != personal_id) new_message_signal();
                setTimeout("$('#chat_body').scrollTop($('#chat_body')[0].scrollHeight + 10000000000)", 10);
            });
            socket.on('page_reload', () => { location.href = location.href; });
            socket.on('success_msg', (data) => { alert(data.text, 'success'); });
        },
        mounted: () => { remove_loader(); },
        methods: {
            msToTime(duration) {
                if(!duration || isNaN(duration)) return  "00:00:00";
                var seconds = parseInt((duration/1000)%60)
                , minutes = parseInt((duration/(1000*60))%60)
                , hours = parseInt((duration/(1000*60*60))%24);
            
               hours = (hours < 10) ? "0" + hours : hours;
               minutes = (minutes < 10) ? "0" + minutes : minutes;
               seconds = (seconds < 10) ? "0" + seconds : seconds;
            
               return hours + ":" + minutes + ":" + seconds;
            },
            reverse(object){
                var newObject = {};
                var keys = [];
                for (var key in object) keys.push(key);
                for (var i = keys.length - 1; i >= 0; i--) {
                    var value = object[keys[i]];
                    newObject[keys[i]]= value;
                }      
                return newObject;
            },
            add(type){
                send_ajax('/engine/settings', {
                    'note_room': vue.room,
                    'note_type': type, 
                    'note_value': (type == 'note' ? vue.new_note : JSON.stringify({'name': vue.new_property_name, 'value': vue.new_property_value}))
                }, vue);
            },
            remove(type, id){
                send_ajax('/engine/settings', {'note_room': vue.room, 'note_type': type, 'note_id': id}, vue);
            },
            change(id, type, note_inner_type){ 
                send_ajax('/engine/settings', {
                    'note_room': vue.room, 
                    'note_type': type, 
                    'note_id': id, 
                    'note_inner_type': (note_inner_type ? note_inner_type : 'note'),
                    'note_update_value': $(event.target).val(),
                }, vue);
            },
            create_chapter(type){
                if(type == 'new') vue.$set(vue.fastmessages, 'create_chapter', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages, 'create_chapter');
                else if(type == 'save'){
                    let value = vue.fastmessages.create_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_chapter", "fastMessages_value": value }, vue);
                    Vue.delete(vue.fastmessages, 'create_chapter');
                }
            },
            newfastmessage(type){
                if(type == 'new') vue.$set(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                else if(type == 'save'){
                    let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter].create_fastmessage;
                    let column = vue.fastmessages.selected_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value})}, vue);
                    Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                }
            },
            copy_fastmessage(uid){
                $('.fast_message_'+uid).focus().select();
                document.execCommand('copy');
            },
            remove_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                send_ajax('/engine/settings', {"fastMessages_type": "remove_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": uid})});
                Vue.delete(vue.fastmessages.chapters[column], uid);
            },
            update_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                let value = $('.fast_message_'+uid).val();
                send_ajax('/engine/settings', {"fastMessages_type": "save_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value, "uid": uid})});
            },
            remove_chapter(uid){
                send_ajax('/engine/settings', {"fastMessages_type": "remove_chapter", "fastMessages_value": uid });
                if(uid == vue.fastmessages.selected_chapter) vue.$set(vue.fastmessages, 'selected_chapter', 'main');
                Vue.delete(vue.fastmessages.chapters, uid);
            },
            update_chapter(){
                let chapter = vue.fastmessages.selected_chapter;
                let chapter_name = vue.fastmessages.chapters[chapter].chapter_name;
                send_ajax('/engine/settings', {"fastMessages_type": "chapter_name", "fastMessages_value": JSON.stringify({"value": chapter_name, "uid": chapter})});
            },
            convert_emojis(str){
                var container = $('<div>').html(str);
                container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                message = container.html();
                return vue.find_emojis(message);
            },
            htmldecoder(str){ return $("<div/>").html(str).text(); },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            select_smile(smile, folder, smile_file){
                $('.chat_block_textarea').html($('.chat_block_textarea').html()+`<img class="InterHelper_emoji" src="/emojis/${folder}/${smile_file}" alt="${smile}" />`); 
            },
            removeFile(index) { vue.files.splice(index, 1); },
            new_command(){ send_ajax('/engine/settings', $('#new_command_form').serialize()); },
            send() {
                vue.smiles_mode = false;
                let message = this.newMessage;
                var container = $('<div>').html(message);
                container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                message = container.html();
                message = $("<div/>").html(message).text();
                if(this.newMessage && Object.keys(this.files).length == 0) send_ajax('/engine/settings', {
                    "message_to_guset": message, 
                    "message_to_guest_type": "guest",
                    "mode": (vue.js_mode ? 'js_mode' : (vue.style_mode ? 'style_mode' : null)),
                });  // сообщение 
                else if(Object.keys(this.files).length > 0 && !vue.style_mode && !vue.js_mode) formData_send_ajax('/engine/settings', getFilesFormData(vue.files, "guest", message, 'SendImg')); // сообщение + файлы
                else if(vue.js_mode || vue.style_mode) alеrt("Нельзя отправить файл, не отключив спец-режимы.", 'error');
                else alert("Поля не заполнены", 'error');
                this.newMessage = '';
                $('.chat_block_textarea').html('');
                vue.files = [];
            },
            finish(){
                send_ajax('/engine/settings', {'room':  vue.room, 'type': 'finish'});
                location.href = "/engine/consultant/hub";
            },
            add_room(type){ socket.emit('add_from_cards', {'table': type}); vue.add_crm_mode = !vue.add_crm_mode; },
            exit(){ javascript:history.go(-1) },
            paste_command(command){ 
                if($(event.target).hasClass('command_remove') || $(event.target).hasClass('command_remove_span')) return;
                $('.chat_block_textarea').html($('.chat_block_textarea').html() + command); 
            },
            ban_room(){
                let reason = prompt('Причина бана ? ', 'Оскорбление чувств верующих моей обители!');
                if(reason) socket.emit('ban_room', {'room': vue.room, 'reason':reason});
                if(reason) window.location.href = '/engine/consultant/hub';
            },
            remove_room(){ 
                socket.emit('remove_room', {'room': vue.room});
                window.location.href = '/engine/consultant/hub';
            },
            remove_command(command){ send_ajax('/engine/settings', {'assistent_delete_command': command}); },
            decodeHtml(str) {
                var textArea = document.createElement('textarea');
                textArea.innerHTML = str;
                return textArea.value;
            },
            load_date(date){
                if(dates.indexOf(date) != -1) return false; 
                dates.push(date);
                return true;
            },
            handleChange(){
                let e = event.target;
                if (!e.files.length) return;    
                const files = Object.keys(e.files).map((i) => e.files[i]);
                vue.$set(vue, 'files', vue.files.concat(files));
                $(e).val('');
            },
        },
    });
    get_messages(socket, vue, buttlecry);
    crmchanges_inroom(socket, vue);
    team_msg_notification(vue, socket);
    task_getter(tasks, vue, true);
}
function banned_chat(token, dirname, regexp, emojis){
    var params = get_reader(window.location);
    $(document).ready(function() {
        if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
            boxClass:     'wow',      
            animateClass: 'animated',
            offset:       0,       
            mobile:       true,       
            live:         true,      
            callback:     function(box) {},
            scrollContainer: '#chat_body',
        }).init();
        $('body').append(`
            <style>
            *::-webkit-scrollbar-thumb { background-color: #<?php echo isset(${params['color']}) ? ${params['color']} : '0ae'; ?> !important; }
            </style>
        `);
    });
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': params['room'], 'token': token});
        socket.emit('get-assistent-messages', {'type': 'bannedguest'});
        socket.emit('room_status', {'type': 'bannedguest', 'room': params['ip']});
    });
    socket.on('page_reload', () => { location.reload(); });
    let dates = [];
    let vue = new Vue({
        el: '#app',
        data: {
            files_path: location.origin + '/user_adds/',
            messages: [],
            info: {},
            photo: {
                img: params['img'],
                color: '#'+params['color'],
            },
            messages_loaded: false,
            emojis: emojis,
            status: '',
            regexp: regexp,
            room: params['room'],
            g_photo: null,
            g_name: null, 
            g_type: null,
            g_columns: {},
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
        },
        mounted: () => { remove_loader(); },
        created() {
            socket.on('page_reload', () => { location.href = location.href; });
        },
        methods: {
            exit(){ window.location.href = '/engine/consultant/banned';},
            load_date(date){
                if(dates.indexOf(date) != -1) return false; 
                dates.push(date);
                return true;
            },
            htmldecoder(str){ return $("<div/>").html(str).text(); },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            unban_room(){
                socket.emit('unban_room', {'room': params['ip']});
                window.location.href = '/engine/consultant/banned';
            },
        },
    });
    get_messages(socket, vue, '');
    team_msg_notification(vue, socket);
    crmchanges_inroom(socket, vue);
}
function banned_page(token, dirname, regexp, emojis, assistents){
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': 'MAIN', 'token': token});
        socket.emit('get-bannedguests-mas');
    });
    socket.on('page_reload', () => { location.reload(); });
    let vue = new Vue({
        el: '#app',
        data: {
            crm_items: {},
            userlist: {},
            assistents: assistents,
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            emojis:emojis,
        },
        created() {
            socket.on('get-bannedguests-mas', (data) => { 
                Vue.set(this.userlist, 'rooms', data.rooms);
                vue.crm_items = data.crm_items||{};
            });
            socket.on('unbaned_user', (data) => { vue.$delete(vue.userlist.rooms, data.target); });
            socket.on('buned_user', (data) => {
                if(vue.userlist.rooms) Vue.set(vue.userlist.rooms, data.bun_id, data.info);
                else{
                    vue.userlist['rooms'] = {};
                    Vue.set(vue.userlist.rooms, data.bun_id, data.info);
                }
            }); 
        },
        mounted(){ 
            this.$nextTick(function(){
                if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
                    boxClass:     'wow',      
                    animateClass: 'animated',
                    offset:       0,       
                    mobile:       true,       
                    live:         true,      
                    callback:     function(box) {},
                    scrollContainer: '#visitors_wow',
                }).init();
            });
            remove_loader(); 
        },
        methods: {
            unban_room(roomname){ socket.emit('unban_room', {'room': roomname}); },
            changeChat(data){  
                let photo = vue.userlist.rooms[data]['photo'];
                room_name = vue.userlist.rooms[data]['info']['ip'];
                let room_id = vue.userlist.rooms[data]['room_id'];
                location.href = '/engine/consultant/banned_chat?room='+room_id+'&name=' +room_name+'&ip='+data+'&img='+photo.img+'&color='+photo.color.replace('#', '');
            },
            room_list(e){
                if($(e).hasClass('room_options_close')){
                    $('.room_options').removeClass('room_options_open');
                    $('.room_options').addClass('room_options_close');
                    $(e).removeClass('room_options_close');
                    $(e).addClass('room_options_open');
                    $('.room_option').css('top','-20px');
                    $(e).parent().children(".room_option").each(function(i,elem){ $(elem).css('top',(40 * (i + 1) + i * 15) +'px'); });
                } else {
                    $('.room_option').css('top','-20px');
                    $(e).removeClass('room_options_open');
                    $(e).addClass('room_options_close');
                }
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
        },
    });
    crmchanges(socket, vue);
    team_msg_notification(vue, socket);
}
function forms_page(token, dirname, regexp, emojis, assistents, forms){
    let loaded_users = -1;
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': 'MAIN', 'token': token});
        socket.emit('get-guests-mas-first');
    });
    socket.on('send-all-answer', (data) => { vue.send_all_load = false; alert(`Сообщение отправлено ${data.count} посетителям`, 'success'); });
    socket.on('choosen-user-answer', (data) => { vue.choosen_users_load = false; });
    socket.on('success_msg', (data) => { alert(data.text, 'success'); });
    socket.on('error_msg', (data) => { alert(data.text, 'error'); });
    socket.on('page_reload', () => { location.reload(); });
    let vue = new Vue({
        el: '#app',
        data: {
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            emojis: emojis,
            crm_items: {},
            forms: forms,
            userlist: {},
            assistents: assistents,
        },
        mounted(){ 
            this.$nextTick(() => {
                $(document).ready(function() {
                    if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
                        boxClass:     'wow',      
                        animateClass: 'animated',
                        offset:       0,       
                        mobile:       true,       
                        live:         true,      
                        callback:     function(box) {},
                        scrollContainer: '#visitors_wow',
                    }).init();
                });
            });
            remove_loader(); 
        },
        updated(){ loaded_users = -1; vue.shown_user_counter = $('.OnlineUser').length; },
        created() {
            socket.on('get-guests-mas-first', (data) => { 
                Vue.set(vue.userlist, 'rooms', data.rooms);
                this.crm_items = data.crm_items || {};
                socket.emit('get-bannedguests-mas');
            }); 
            socket.on('get-bannedguests-mas', (data) => { 
                for(i in data.rooms){ 
                    Vue.set(vue.userlist.rooms, data.rooms[i]['room_id'], data.rooms[i]); 
                    Vue.set(vue.userlist.rooms[data.rooms[i]['room_id']], 'ban_status', true);
                }
            });
            socket.on('userlist_update', (data)=>{ change_room(data.type, data.value, data.target, data.option, data.lastActivityTime, vue); }); // изменения комнат
        },
        methods: {
            ban_dialog(data){  
                let photo = vue.userlist.rooms[data]['photo'];
                room_name = vue.userlist.rooms[data]['info']['ip'];
                let room_id = vue.userlist.rooms[data]['room_id'];
                location.href = '/engine/consultant/banned_chat?room='+room_id+'&name=' +room_name+'&ip='+data+'&img='+photo.img+'&color='+photo.color.replace('#', '');
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            consultation(index, type){ send_ajax('/engine/settings', {'room':  index, 'type': type}); },
        },
    });
    team_msg_notification(vue, socket);
    crmchanges(socket, vue);
}
function command_page(token, dirname, regexp, emojis, personal_id){
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
        socket.emit('assistent-join', {'room': "MAIN", 'token': token});
        socket.emit('get_teammate_mas');
        socket.emit('get_new_assistent_chat_messages', "assistent_chat_messages");
    });
    socket.on('page_reload', () => { location.reload(); });
    socket.on('get_new_assistent_chat_messages', (data)=>{
        $.each(data.mas, (index, elem) => {
            Vue.set(vue.userlist.assistents[index], "message", elem.message);
            Vue.set(vue.userlist.assistents[index], "message_adds", elem.message_adds);
        });
    });
    var vue = new Vue({
        el: '#app',
        data: {
            userlist: {},
            files_path: '/user_adds/',
            regexp: regexp,
            notification_msg: {
                'photo':"user.png",
                'name': "TEST",
                'email': "TEST",
                'departament': "TEST",
                'time': "2021-07-03 19:33",
                'message': "TEST",
                'message_adds': null,
            },
            emojis: emojis,
        },
        created() {
            socket.on('get_teammate_mas', (data) => { this.userlist = data; });
            socket.on('assistentlist_update', (data)=>{
                if(data.type == "status") vue.userlist.assistents[data.target]["status"] = data.value;
                if(data.type == "change_settings"){
                    if (data.option != "remove"){ vue.userlist.assistents[data.target][data.option] = data.value; } 
                    else vue.$delete(vue.userlist.assistents, data.target);
                } 
                if(data.type == "time") vue.userlist.assistents[data.target]["time"] = data.value;
                if(data.type == "new_assistent"){  Vue.set(vue.userlist.assistents, data.target, data.value); }
            });
        },
        mounted(){ 
            this.$nextTick(() => {
                if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
                    boxClass:     'wow',      
                    animateClass: 'animated',
                    offset:       0,       
                    mobile:       true,       
                    live:         true,      
                    callback:     function(box) {},
                    scrollContainer: '#visitors_wow',
                }).init();
            });
            remove_loader(); 
        },
        methods: {
            sort_mas(array){
                let online = {}; 
                let offline = {};
                for(user in array){
                    key = user;
                    user = array[user];
                    if(user.status == 'online') online[key] = user;
                    else offline[key] = user;
                }
                return Object.assign({}, online, offline);
            },
            changeChat(data, index){
                socket.emit('consultant_chat_readed', {
                    "room": index,
                    "type": "assistent_chat_messages"
                });
                location.href = '/engine/consultant/command_chat?room='+data; 
            },
            modal_window(status, ob){
                if(status == 'close') $('#' + ob).css('display', 'none');
                else if(status == 'open') $('#' + ob).css('display', 'flex');
            },
            get_count(status){
                let count = 0;
                for(key in this.userlist['assistents']){
                    if(key != personal_id && this.userlist.assistents[key]["status"] == status) count++;
                }
                return count;
            },
            find_emojis(str){
                for(folder in this.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in this.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${this.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
        },
    });
    team_msg_notification(vue, socket);
}
function getFilesFormData(files, type, message) {
    const formData = new FormData();
    files.map((file, index) => {
        let ext = file.name.substr(file.name.lastIndexOf('.'), file.name.length);
        formData.append(`SendImg${index + 1}`, file);
    });
    formData.append('message_type', type);
    formData.append('message', message)
    return formData;
}
function getFilesFormData2(files, selected, message, mode) {
    const formData = new FormData();
    files.map((file, index) => {
        let ext = file.name.substr(file.name.lastIndexOf('.'), file.name.length);
        formData.append(`SendToSelectedImg${index + 1}`, file);
    });
    formData.append('selected', selected);
    formData.append('message', message);
	formData.append('mode', mode);
    return formData;
}
function command_chat(token, dirname, regexp, emojis, personal_id, fastmessages, oponent_email, boss_id, oponent_departament, oponent_name){
    var params = get_reader(window.location);
    $(document).ready(function() {
        $(".chat_block_textarea").bind("DOMSubtreeModified",function(){ 
            vue.newMessage = $(this).html();
            if (vue.newMessage.length > 0) $('#placeholder').css('display', 'none');
            else  $('#placeholder').css('display', 'block');
            let foot_height = $('#chat_footer').height();
            $('#chat_body').css('height', `calc(700px - ${foot_height}px)`);
        });
        if(localStorage.anima == "false" || !localStorage.anima) wow = new WOW({
            boxClass:     'wow',      
            animateClass: 'animated',
            offset:       0,       
            mobile:       true,       
            live:         true,      
            callback:     function(box) {},
            scrollContainer: '#chat_body',
        }).init();
    });
    var oponent = params['room'].split('!@!@2@!@!')[0];
    var socket = io(dirname,{
        'reconnection': true,
        'reconnectionDelay': 1000,
        'reconnectionDelayMax' : 5000,
        'reconnectionAttempts': Infinity
    });
    socket.on('connect', () =>{
       socket.emit('assistent-join', {'room' : 'ASSISTENT', 'token': token, 'room_id': params['room']});
       socket.emit('get-assistent-messages', {'type': 'assistent'});
       if(params['room'] != boss_id) socket.emit('room_status', {'oponent': oponent, 'type': 'assistent'});
    });
    socket.on('page_reload', () => { location.reload(); });
    let dates = [];
    let vue = new Vue({
        el: '#app',
        data: {
            oponent: oponent,
            oponent_email: oponent_email,
            load: true,
            newMessage: '',
            commands_mode: false,
            messages: [],
            messages_loaded: false,
            fastmessages: {
                "search_chapter": '',
                "search_message": '',
                "selected_chapter": 'main',
                "chapters_mode": false,
                'chapters': fastmessages,
            },
            typing: null,
            smiles_mode: false,
            emojis: emojis,
            status: null,
            regexp: regexp,
            files: [],
            files_path: '/user_adds/',
            oponent_departament: oponent_departament,
            oponent_name: oponent_name
        },
        mounted(){ remove_loader(); },
        created() {
            socket.on('assistentlist_update', (data)=>{
                if(data.type == "status" && data.target == oponent) vue.status = data.value;
            });
            socket.on('room_status', (data) => { this.status = data.status; });
            socket.on('consultant-message', (message) => {
                vue.load = true;
                if(message.sender != personal_id) socket.emit('consultant_chat_readed', {
                    "room": vue.oponent,
                    "type": "assistent_chat_messages"
                });
                this.messages.push(message);
                setTimeout("$('#chat_body').scrollTop($('#chat_body')[0].scrollHeight + 10000000000)", 10);
            });
            socket.on('get_previous_messages_assistent', (data) => {
                vue.messages = [];
                for(key in data){
                    vue.messages.push({
                        message: data[key].message,
                        photo: data[key].photo||"user.png",
                        message_adds: data[key].adds,
                        departament: data[key].departament,
                        user: data[key].name,
                        time: data[key].SendTime,
                        sender: data[key].sender
                    });
                }
                vue.messages_loaded = true;
                setTimeout("$('#chat_body').scrollTop($('#chat_body')[0].scrollHeight + 10000000000)", 10);
            });
            socket.on('typing', (data) => { this.typing = data; });
            socket.on('stopTyping', () => { this.typing = false; });
        },
        watch: { newMessage(value) { value ? socket.emit('typing') : socket.emit('stopTyping') } },
        methods: {
            create_chapter(type){
                if(type == 'new') vue.$set(vue.fastmessages, 'create_chapter', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages, 'create_chapter');
                else if(type == 'save'){
                    let value = vue.fastmessages.create_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_chapter", "fastMessages_value": value}, vue);
                    Vue.delete(vue.fastmessages, 'create_chapter');
                }
            },
            newfastmessage(type){
                if(type == 'new') vue.$set(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                else if(type == 'save'){
                    let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter].create_fastmessage;
                    let column = vue.fastmessages.selected_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value})}, vue);
                    Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                }
            },
            copy_fastmessage(uid){
                $('.fast_message_'+uid).focus().select();
                document.execCommand('copy');
            },
            remove_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                send_ajax('/engine/settings', {"fastMessages_type": "remove_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": uid}) });
                Vue.delete(vue.fastmessages.chapters[column], uid);
            },
            update_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                let value = $('.fast_message_'+uid).val();
                send_ajax('/engine/settings', {"fastMessages_type": "save_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value, "uid": uid}) });
            },
            remove_chapter(uid){
                send_ajax('/engine/settings', {"fastMessages_type": "remove_chapter", "fastMessages_value": uid });
                if(uid == vue.fastmessages.selected_chapter) vue.$set(vue.fastmessages, 'selected_chapter', 'main');
                Vue.delete(vue.fastmessages.chapters, uid);
            },
            update_chapter(){
                let chapter = vue.fastmessages.selected_chapter;
                let chapter_name = vue.fastmessages.chapters[chapter].chapter_name;
                send_ajax('/engine/settings', {"fastMessages_type": "chapter_name", "fastMessages_value": JSON.stringify({"value": chapter_name, "uid": chapter})});
            },
            htmldecoder(str){ return $("<div/>").html(str).text(); },
            removeFile(index) { vue.files.splice(index, 1); },
            exit(){  location.href = '/engine/consultant/command'; },
            send() {
                vue.smiles_mode = false;
                let message = this.newMessage;
                var container = $('<div>').html(message);
                container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                message = container.html();
                message = $("<div/>").html(message).text();
                if(this.newMessage && Object.keys(this.files).length == 0) send_ajax('/engine/settings', {"message_to_guset": message, "message_to_guest_type": "assistent"});  // сообщение 
                else if(Object.keys(this.files).length > 0 ) formData_send_ajax('/engine/settings', getFilesFormData(vue.files, "assistent", message)); // сообщение + файлы
                else alert("Поля не заполнены", 'error');
                this.newMessage = '';
                $('.chat_block_textarea').html('');
                vue.files = [];
            },
            handleChange(){
                let e = event.target;
                if (!e.files.length) return;    
                const files = Object.keys(e.files).map((i) => e.files[i]);
                vue.$set(vue, 'files', vue.files.concat(files));
                $(e).val('');
            },
            load_date(date){
                if(dates.indexOf(date) != -1) return false; 
                dates.push(date);
                return true;
            },
            convert_emojis(str){
                var container = $('<div>').html(str);
                container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                message = container.html();
                return vue.find_emojis(message);
            },
            find_emojis(str){
                for(folder in vue.emojis){
                    if(str.indexOf(folder) == -1) continue;
                    for(emoji in vue.emojis[folder]){
                        if(str.indexOf(emoji) == -1) continue;
                        str = str.replaceAll(emoji, `<img class="InterHelper_emoji" alt="${emoji}" src='/emojis/${folder}/${vue.emojis[folder][emoji]}' />`);
                    }
                }
                return str;
            },
            select_smile(smile, folder, smile_file){
                $('.chat_block_textarea').html($('.chat_block_textarea').html()+`<img class="InterHelper_emoji" src="/emojis/${folder}/${smile_file}" alt="${smile}" />`); 
            },
        },
    });
}
function new_user_signal(){
    var sound = new Howl({
        src: ['/audio-effect/new-user.mp3'],
        volume: 0.5,
        onend: function () {}
    });
    sound.play();
}
function new_message_signal(){
    var sound = new Howl({
        src: ['/audio-effect/new_message.mp3'],
        volume: 0.5,
        onend: function () {}
    });
    sound.play()
}
function departaments_page(departaments){
    for(departament in departaments){
        if(typeof departaments[departament] == 'object') departaments[departament] = Object.values(departaments[departament]);
    }
    var vue = new Vue({ 
        el: '#container',
        data: {
            'departaments': departaments,
            'dep_name': '',
            'pages': {
                "crm": 'CRM', 
                "forms":"Обратная связь", 
                "hub": 'Консультировани', 
                "command": 'Коммандный чат', 
                "banned": 'Заблокированные посетители',
                "domains": 'Домены', 
                "departaments": 'Отделы', 
                "tariff": 'Тарифы', 
                "statistic": "Статистика", 
                "assistents": "Ассистенты", 
                "design": "Дизайн", 
                "dialogs": 'История дилогов', 
                "offline": 'Оффлайн форма', 
                "options": "Настройки системы", 
                "anticlicker": "Антискликиватель", 
                "swaper": "Подмена контента", 
                "autosender": "Активные приглашения",
                "mailer": "Рассылка почты",
            }
        },
        methods: {
            remove(index){ 
                send_ajax('/engine/settings', {'departament_remove': index}); 
                Vue.delete(vue.departaments, index); 
            },
            update_departament(inner, departament){
                let status;
                let index = vue.departaments[departament].indexOf(inner);
                if(index == -1){ 
                    status = true;
                    vue.departaments[departament].push(inner);
                } else {
                    status = false;
                    vue.departaments[departament].splice(index, 1);
                }
                send_ajax('/engine/settings', {'departament_update': JSON.stringify({'status': status, 'inner': inner, 'departament': departament}) }); 
            },
            add_departament(){ 
                send_ajax('/engine/settings', {'departament_add': String(vue.dep_name)}, vue); 
            },
            change(index){
                let info = vue.departaments[index];
                let value = $(event.target).val();
                Vue.delete(vue.departaments, index);
                vue.$set(vue.departaments, value, info);
                send_ajax('/engine/settings', {'departament_name_update': JSON.stringify({
                    'prev': index,
                    'new': value
                })});
            },
            htmldecoder(str){
                if(typeof str != 'string') return str;
                return $("<div/>").html(str).text();
            }
        }
    });
}
function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
function mailer_page(info){
    var vue = new Vue({ 
        el: '#container',
        data: {
            SMTPsecure: htmldecoder(info.mailer['deffault'].SMTPsecure),
            SMTPserver: htmldecoder(info.mailer['deffault'].SMTPserver),
            SMTPport: htmldecoder(info.mailer['deffault'].SMTPport),
            SMTPemail: htmldecoder(info.mailer['deffault'].SMTPemail),
            SMTPpassword: htmldecoder(info.mailer['deffault'].SMTPpassword),
            sender_name: htmldecoder(info.mailer['deffault'].sender_name),
            mail_name: htmldecoder(info.mailer['deffault'].mail_name),
            selected_domain: 'deffault',
            domains: info.domains,
            fastmessages: {
                "search_chapter": '',
                "search_message": '',
                "selected_chapter": 'main',
                "chapters_mode": false,
                'chapters': info.fastmessages,
            },
            loader: false,
            regexp: info.regexp,
            selected: [],
            recepient: {'name': '', 'email': ''},
            commands_mode: false,
            files: [],
            mailer: info.mailer,
        },
        methods: {
            htmldecoder(str){ return $("<div/>").html(str).text(); },
            create_chapter(type){
                if(type == 'new') vue.$set(vue.fastmessages, 'create_chapter', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages, 'create_chapter');
                else if(type == 'save'){
                    let value = vue.fastmessages.create_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_chapter", "fastMessages_value": value}, vue);
                    Vue.delete(vue.fastmessages, 'create_chapter');
                }
            },
            newfastmessage(type){
                if(type == 'new') vue.$set(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage', null);
                else if(type == 'cancel') Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                else if(type == 'save'){
                    let value = vue.fastmessages.chapters[vue.fastmessages.selected_chapter].create_fastmessage;
                    let column = vue.fastmessages.selected_chapter;
                    if(!value) return;
                    send_ajax('/engine/settings', {"fastMessages_type": "new_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value})}, vue);
                    Vue.delete(vue.fastmessages.chapters[vue.fastmessages.selected_chapter], 'create_fastmessage');
                }
            },
            copy_fastmessage(uid){
                $('.fast_message_'+uid).focus().select();
                document.execCommand('copy');
            },
            remove_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                send_ajax('/engine/settings', {"fastMessages_type": "remove_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": uid}) });
                Vue.delete(vue.fastmessages.chapters[column], uid);
            },
            update_fastmessage(uid){
                let column = vue.fastmessages.selected_chapter;
                let value = $('.fast_message_'+uid).val();
                send_ajax('/engine/settings', {"fastMessages_type": "save_fast_message", "fastMessages_value": JSON.stringify({"column": column, "value": value, "uid": uid}) });
            },
            remove_chapter(uid){
                send_ajax('/engine/settings', {"fastMessages_type": "remove_chapter", "fastMessages_value": uid });
                if(uid == vue.fastmessages.selected_chapter) vue.$set(vue.fastmessages, 'selected_chapter', 'main');
                Vue.delete(vue.fastmessages.chapters, uid);
            },
            update_chapter(){
                let chapter = vue.fastmessages.selected_chapter;
                let chapter_name = vue.fastmessages.chapters[chapter].chapter_name;
                send_ajax('/engine/settings', {"fastMessages_type": "chapter_name", "fastMessages_value": JSON.stringify({"value": chapter_name, "uid": chapter})});
            },
            handleChange(){
                let e = event.target;
                if (!e.files.length) return;    
                const files = Object.keys(e.files).map((i) => e.files[i]);
                vue.$set(vue, 'files', vue.files.concat(files));
                $(e).val('');
            },
            removeFile(index) { 
                vue.files.splice(index, 1); 
            },
            add_recepient(){
                if(!validateEmail(vue.recepient.email)){ alert('Введите почту правильно !', 'error'); return;}
                let res = {'email': vue.recepient.email};
                if(vue.recepient.name != '' && vue.recepient.name) res['name'] = vue.recepient.name;
                vue.selected.push(res); 
                vue.$set(vue.recepient, 'name', ''); 
                vue.$set(vue.recepient, 'email', '');
            },
            remove_recepient(index){
                vue.selected.splice(index, 1);
            },
            send_mails(){
                let fd = new FormData;
                fd.append('mailer_name', vue.mail_name);
                fd.append('mailer_info', JSON.stringify(vue.selected));
                fd.append('sender_name', vue.sender_name);
                fd.append('mailer_message', $(".chat_block_textarea").text());
                fd.append('design_domain', vue.selected_domain);
                vue.files.map((file, index) => { fd.append(`mailer_files${index + 1}`, file); });
                formData_send_ajax('/engine/settings', fd, vue);
                vue.loader = true;
            },
            change(type){
                send_ajax('/engine/settings', {'mailer_type': type, 'mailer_value': $(event.target).val(), 'design_domain': vue.selected_domain});
                vue.$set(vue.mailer[vue.selected_domain], type, $(event.target).val()); 
            },
        },
        watch: {
            selected_domain(some){
                vue.SMTPsecure = htmldecoder(vue.mailer[some].SMTPsecure);
                vue.SMTPserver = htmldecoder(vue.mailer[some].SMTPserver);
                vue.SMTPport = htmldecoder(vue.mailer[some].SMTPport);
                vue.SMTPemail = htmldecoder(vue.mailer[some].SMTPemail);
                vue.SMTPpassword = htmldecoder(vue.mailer[some].SMTPpassword);
                vue.sender_name = htmldecoder(vue.mailer[some].sender_name);
                vue.mail_name = htmldecoder(vue.mailer[some].mail_name);
            }
        }
    });
}
if(location.href.indexOf('feedback') != -1) color_mode();
else styles_mode();
class TextScramble {
    constructor(el) {
      this.el = el;
      this.chars = '!<>-_\\/[]{}—=+*^?#01________';
      this.update = this.update.bind(this);
    }
    setText(newText) {
      const oldText = this.el.innerText;
      const length = Math.max(oldText.length, newText.length);
      const promise = new Promise((resolve) => this.resolve = resolve);
      this.queue = [];
      for (let i = 0; i < length; i++) {
        const from = oldText[i] || '';
        const to = newText[i] || '';
        const start = Math.floor(Math.random() * 40);
        const end = start + Math.floor(Math.random() * 40);
        this.queue.push({ from, to, start, end });
      }
      cancelAnimationFrame(this.frameRequest);
      this.frame = 0;
      this.update();
      return promise;
    }
    update() {
      let output = '';
      let complete = 0;
      for (let i = 0, n = this.queue.length; i < n; i++) {
        let { from, to, start, end, char } = this.queue[i];
        if (this.frame >= end) {
          complete++;
          output += to;
        } else if (this.frame >= start) {
          if (!char || Math.random() < 0.28) {
            char = this.randomChar();
            this.queue[i].char = char;
          }
          output += `<span class="dud">${char}</span>`;
        } else {
          output += from;
        }
      }
      this.el.innerHTML = output;
      if (complete === this.queue.length) {
        this.resolve();
      } else {
        this.frameRequest = requestAnimationFrame(this.update);
        this.frame++;
      }
    }
    randomChar() {
      return this.chars[Math.floor(Math.random() * this.chars.length)];
    }
}
const phrases = [
    'InterHelper', 'InterFire',
    '˚ʚ(`◡`)ɞ˚', '1nt3rH3lp3r',
    'Hello neo',
    'Hello world', 'Best choice',
    'Hello user', 'InterHelper',
    'InterFire',
    'Team', 
    'H4ck3d by sashapop10',
    'Best perfomance',
    'Comfort', 'InterHelper',
    'Time', 'Best choice',
    '¯\\_(๑❛ᴗ❛๑)_/¯',
    'Phrase', 'InterFire',
    'Put some text',
    'Best choice',
    'Lets Rock',
    'JavaScript', 'InterHelper',
    'Best Tool',
    'CSS', '1nt3rF1r3',
    'Opening 443 port',
    'Connection..', '1nt3rH3lp3r',
    'Getting root',
    'teamwork',
    '> 6years at code',
    'Shrek 2', 'Best Tool',
    'Space',
    'Try hard', 'InterHelper',
    '(⊙.⊙(☉ₒ☉)⊙.⊙)',
    'Skaven here',
    'don\'t give up', 'the best',
    'oops',
    'loosing data', 'InterFire',
    'just fun',
    'bruitforcing', '1nt3rH3lp3r',
    '2+2=4',
    'work...', '1nt3rF1r3',
    'pro', 'InterHelper',
    'Engineer',
    '(͠≖ ͜ʖ͠≖)👌',
    'IT',
    'wrong sertificate','InterFire',
    'boom',  'Best Tool',
    '2003',
    '2022', 'Best choice',
    'Packet lost', '1nt3rH3lp3r',
    'New fitcha soon',
    '',
    'Is it work ?',
    'somebody fix IT', 'InterHelper',
    'cyberHelper', 'the best',
    'Helper 11.23.03', 
    'eazy','InterFire',
    'miltichat',
    'the best',
    'Best service', 'InterFire',
    'Too hard',
    'ʕ•́ᴥ•̀っ',
    '( ◡́.◡̀)(^◡^ )',
    '٩(˘◡˘)۶',   
]
const el = document.querySelector('.hex-animation');
const fx = new TextScramble(el);
let prev = 0;
const next = () => {
    let current = Math.floor(Math.random()*phrases.length);
    if(prev == current) current = Math.floor(Math.random()*phrases.length);
    prev = current;
    fx.setText(phrases[current]).then(() => {
        setTimeout(next, 20000);
    })
}
setTimeout(next, 300000);  