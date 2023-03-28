let url = location.href;
let url_parts = url.split('/');
let url_page = url_parts[url_parts.length - 1];
let url_dir = url_parts[url_parts.length - 2];
let wow;
if((localStorage.anima == "false" || !localStorage.anima) && url_page.indexOf('dialog') == -1) wow = new WOW().init();
if(url.indexOf('assistents') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'assistents'});
else if(url.indexOf('login') != -1){
	$('.send_ajax_form_assistent').submit(function(e) {
        e.preventDefault();
        send_ajax('/engine/admin_login', $(this).serialize());
    });
} else if(url.indexOf('faq') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'faq'}); 
else if(url.indexOf('users') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'users'}); 
else if(url.indexOf('news') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'news'}); 
else if(url.indexOf('reviews') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'reviews'}); 
else if(url.indexOf('tariff') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'tariff'}); 
else if(url.indexOf('variables') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'variables'}); 
else if(url.indexOf('tools') != -1) send_ajax('/engine/adminSettings', {'getSettings': 'tools'}); 
function faq_page(faq){
	var vue = new Vue({ 
        el: '#container',
        data: {
            mas: faq,
            selected_index: 'Ответ',
            add: false,
            loader: false,
            header: null,
            answer: null,
            column: 'Часто задаваемые вопросы',
            video_link: null,
        },
        methods: {
            remove(column, header){
                send_ajax('/engine/adminSettings', {'remove_faq_header': header, 'remove_faq_column': column});
                Vue.delete(vue.mas[column], header);
            },
            new_innercard(){
                let elem = event.target;
                $(elem).css('display', 'none');
                $(elem).siblings('.new_innercard').css('display', 'flex');
            },
            ocform(){
                if(!vue.add) $('#add_assistent_block').css('max-height', '40em');
                else  $('#add_assistent_block').css('max-height', '0em');
                vue.add = !vue.add;
            },
            add_faq(){
                let type = vue.selected_index;
                let header = vue.header;
                let answer = vue.answer;
                let column = vue.column;
                let video_link = vue.video_link;
                if(column == 'Часто задаваемые вопросы') column = 0;
                else column = 1;
                if(type == 'Ответ') type = 0;
                else type = 1;
                send_ajax('/engine/adminSettings', {'add_header': header, 'add_answer':answer, 'add_column': column, 'add_type': type, 'video': video_link});
                let arr;
                if(type == 0) arr = {'info': {'answer': answer, 'video': video_link||''}, 'type': 0,};
                else arr = {'info': {'list': {}}, 'type': 1};
                vue.$set(vue.mas[column], header, arr);
                vue.ocform();
            },
            save_innercard(column, item){
                let elem = event.target;
                $(elem).closest('.new_innercard').css('display', 'none');
                $(elem).closest('.new_innercard').siblings('.add_faq_group').css('display', 'block');
                let header = $(elem).siblings('.faq_inner_header').val();
                let answer = $(elem).siblings('.faq_answer').val();
                let video = $(elem).siblings('.faq_video').val();
                if(!header || !answer || !column || !item) return;
                else{
                    send_ajax('/engine/adminSettings', {'header': header, 'answer': answer, 'column':column, 'item': item, 'video': video});
                    vue.$set(vue.mas[column][item]['info']['list'], header, {'answer':answer, 'video': video});
                }
            },
            remove_innergroup(column, item, inneritem){
                send_ajax('/engine/adminSettings', {'remove_inneritem': inneritem, 'remove_column':column, 'remove_item': item});
                Vue.delete(vue.mas[column][item]['info']['list'], inneritem);
            },
            change_location(column, item){
                send_ajax('/engine/adminSettings', {'change_column':column, 'change_item': item});
                let column2;
                if(column == 1) column2 = 0;
                else column2 = 1;
                vue.$set(vue.mas[column2], item, vue.mas[column][item]);
                Vue.delete(vue.mas[column], item);
            },
            change_text(column, header, inner, type){
                let value = $(event.target).val();
                send_ajax('/engine/adminSettings', {'value':value, 'header': header, 'innerheader': inner, 'type': type, 'column': column});
                if(type == 'header'){
                    vue.mas[column][value] = vue.mas[column][header];
                    delete vue.mas[column][header];
                } else if(type == 'innerheader'){
                    vue.mas[column][header]['info']['list'][value] = vue.mas[column][header]['info']['list'][inner];
                    delete vue.mas[column][header]['info']['list'][inner];
                }
            },
        },
    });
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
function reviews_page(reviews){
	var vue = new Vue({ 
        el: '#middle_part',
        data: {
            add: false,
            reviews: reviews,
            loader:false,
            rating: 0,
        },
        methods: {
            ocform(){
                if(!vue.add) $('#add_assistent_block').css('max-height', '40em');
                else  $('#add_assistent_block').css('max-height', '0em');
                vue.add = !vue.add;
            },
            change(index, type){
                let value;
                if (type != 'img'){ 
                    value = event.target.value;
                    if(type == 'text') type1 = 'review';
                    else type1 = type;
                    send_ajax('/engine/adminSettings', {'value': value, 'review_index': index, 'type': type1});
                    vue.$set(vue.reviews[index], type, value);
                } else if(event.target.files.length) { 
                    value = new FormData;
                    value.append('review_photo', $(event.target).prop('files')[0]);
                    value.append('index', index);
                    formData_send_ajax('/engine/adminSettings', value, vue);
                }
            },
            add_review(){
                var formData = new FormData($('form')[0]);
                formData_send_ajax('/engine/adminSettings', formData, vue);
            },
            remove_review(index){
                send_ajax('/engine/adminSettings', {'remove_review': index});
                Vue.delete(vue.reviews, index);
            },
            rate(x, index){
                send_ajax('/engine/adminSettings', {'value': x, 'review_index': index, 'type': 'rating'});
                vue.$set(vue.reviews[index], 'rating', x);
            },
        },
    });
}
function assistents_page(users){
	var vue = new Vue({ 
        el: '#container',
        data: {
            mas: JSON.parse(users),
            money_mode: false,
            searchmas: JSON.parse(users),
            tariff_mode: false,
        },
        methods: {
            profile(personal_id, boss_id){ send_ajax('/engine/adminSettings', {'assistent_profile': JSON.stringify({'personal_id': personal_id, 'boss_id': boss_id})}); },
			search(){ search(vue, $(event.target).val()); },
        },
    });
}
function users_page(users){
	var vue = new Vue({ 
        el: '#container',
        data: {
            mas: JSON.parse(users),
            money_mode: false,
            searchmas: JSON.parse(users),
            tariff_mode: false,
        },
        methods: {
            monthlater(date){
                date = new Date(date);
                date.setMonth(date.getMonth() + 1);
                return date.toISOString().split('T')[0].split('-').reverse().join('.');
            },
            profile(id){ send_ajax('/engine/adminSettings', {'user_profile': id}); },
            remove_user(id){
                if(!confirm("Вы уверены ?")) return;
                send_ajax('/engine/adminSettings', {'remove_user': id});
                Vue.delete(vue.mas, id);
                Vue.delete(vue.searchmas, id);
            },
            ban(id, type){
                send_ajax('/engine/adminSettings', {'user_id': id, "ban_type": type});
                if(type == 'ban'){ 
                    vue.$set(vue.mas[id], "ban", "banned");
                    if(vue.searchmas[id]) vue.$set(vue.searchmas[id], "ban", "banned");
                } else { 
                    vue.$set(vue.mas[id], "ban", null);
                    if(vue.searchmas[id]) vue.$set(vue.searchmas[id], "ban", null);
                }
            },
            set_money(id){
                let value = parseInt(event.target.value);
                if(!value) value = 0;
                send_ajax('/engine/adminSettings', {'user_money': value, 'user_id': id});
                vue.$set(vue.mas[id], "money", value);
                if(vue.searchmas[id]) vue.$set(vue.searchmas[id], "money", value);
            },
			search(){ search(vue, $(event.target).val()); },
        },
    });
}
function news_page(news){
	var vue = new Vue({ 
        el: '#middle_part',
        data: {
            add: false,
            loader: false,
            news: news,
        },
        methods: {
            ocform(){
                if(!vue.add) $('#add_assistent_block').css('max-height', '52em');
                else  $('#add_assistent_block').css('max-height', '0em');
                vue.add = !vue.add;
            },
            add_news(){
                var formData = new FormData($('form')[0]);
                formData_send_ajax('/engine/adminSettings', formData, vue);
            },
            remove_news(index){
                send_ajax('/engine/adminSettings', {'remove_news': index});
                Vue.delete(vue.news, index)
            },
            change(index, type){
                if (type != 'photo'){ 
                    let value = event.target.value;
                    send_ajax('/engine/adminSettings', {'value': value, 'news_index': index, 'type': type});
                    vue.$set(vue.news[index], type, value);
                } else if(event.target.files.length) { 
                    let value = new FormData;
                    value.append('news_photo', $(event.target).prop('files')[0]);
                    value.append('index', index);
                    formData_send_ajax('/engine/adminSettings', value, vue);
                }
            },
        },
    });
}
function send_ajax(path, array, vue_component){
	$.ajax({
		type: 'POST',
		url: path,
		data: array,
		success: function(data) {
			data = JSON.parse(data);
			if(data.errors.length > 0) alert(data.errors.reduce((element, index) => { return element + '/' + index; }));
			if(Object.keys(data.success).length > 0){
				if(array.hasOwnProperty('getSettings')){
					if(array['getSettings'] == 'assistents') assistents_page(data.success.users);
					else if(array['getSettings'] == 'faq') faq_page(data.success.faq);
					else if(array['getSettings'] == 'users') users_page(data.success.users);
					else if(array['getSettings'] == 'reviews') reviews_page(data.success.reviews);
					else if(array['getSettings'] == 'news') news_page(data.success.news);
					else if(array['getSettings'] == 'tariff') tariff_page(data.success.editions);
					else if(array['getSettings'] == 'variables') variables_page(data.success.variables);
					else if(array['getSettings'] == 'tools') tools_page(data.success.tools);
				}
				if(data.success.hasOwnProperty('log')) console.log(data.success.log);
                if(data.success.hasOwnProperty('reload')) location.reload();
                if(data.success.hasOwnProperty('link')) location.href = data.success['link'];
				if(data.success.hasOwnProperty('response')) alert(data.success.response);
                if(data.success.hasOwnProperty('new_tariff') && data.success.hasOwnProperty('new_tariff_index')){  
					vue_component.$set(vue_component.editions, data.success['new_tariff_index'], JSON.parse(data.success['new_tariff']));
					vue_component.ocform();
				}	
				if(data.success.hasOwnProperty('new_window')) window.open(data.success.new_window, '_blank');
			}
		},
		error: function() { alert('Ошибка в ajax запросе! '); }
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
			console.log(data);
			if(data.errors.length > 0) alert(data.errors.reduce((element, index) => { return element + '/' + index; }));
			if(Object.keys(data.success).length > 0){
				if(data.success.hasOwnProperty('reload')) location.reload();
				if(data.success.hasOwnProperty('response')) alert(data.success.response);
				if(data.success.hasOwnProperty('tariff_photo') && data.success.hasOwnProperty('tariff_index')) vue_component.$set(vue_component.editions[data.success['tariff_index']], 'img', data.success['tariff_photo']); 
				if(data.success.hasOwnProperty('review_photo') && data.success.hasOwnProperty('review_index')){ vue_component.$set(vue_component.reviews[data.success['review_index']], 'img', data.success['review_photo']);} 
				if(data.success.hasOwnProperty('news_photo') && data.success.hasOwnProperty('news_index')) vue_component.$set(vue_component.news[data.success['news_index']], 'photo', data.success['news_photo']); 
				if(data.success.hasOwnProperty('tool_photo') && data.success.hasOwnProperty('tool_index')){ 
					for(row in vue_component.tools){
						if(vue_component.tools[row].hasOwnProperty(data.success['tool_index'])){ 
							vue_component.$set(vue_component.tools[row][data.success['tool_index']], 'photo', data.success['tool_photo']);
							break;
						}
					}
				} 
				if(data.success.hasOwnProperty('new_tool') && data.success.hasOwnProperty('new_tool_index') && data.success.hasOwnProperty('new_tool_row')){  
					vue_component.$set(vue_component.tools[data.success['new_tool_row']], data.success['new_tool_index'], data.success['new_tool']); 
					vue_component.ocform();
				}
				if(data.success.hasOwnProperty('new_review_index') && data.success.hasOwnProperty('new_review')){  
					vue_component.$set(vue_component.reviews, data.success.new_review_index, data.success.new_review);
					vue_component.ocform();
				}
				if(data.success.hasOwnProperty('new_news_index') && data.success.hasOwnProperty('new_news')){  
					vue_component.$set(vue_component.news, data.success.new_news_index, data.success.new_news);
					vue_component.ocform();
				}
            }
		},
		error: function() { alert('Ошибка в ajax запросе! '); }
	});
}
function construct_formData(file, inputname){
	let fd = new FormData;
	fd.append(inputname, file);
	return fd;
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
function search(vue, search) {
	let input = document.getElementsByClassName("crm_serch_input")[0];
	let filter = input.value.toUpperCase();
	if(search) filter = search.toUpperCase();
	for (key in vue.mas) {
		let flag = false;
		for (value in vue.mas[key]){
			if(!vue.mas[key][value]) continue;
			if(value == "domain"){
				for(domain in vue.mas[key][value]["domains"]){ 
					if (vue.mas[key][value]["domains"][domain].toUpperCase().indexOf(filter) > -1){ 
						flag = true; break;
					}
				}
			} else if(vue.mas[key][value].toUpperCase().indexOf(filter) > -1){ flag = true; break; }
		}
		if(!flag) vue.$delete(vue.searchmas, key);
		else Vue.set(vue.searchmas, key, vue.mas[key]);
	}
}
function tariff_page(editions){
	var vue = new Vue({ 
        el: '#middle_part',
        data: {
            add: false,
            editions: editions,
            loader: false,
        },
        methods: {
            ocform(){
                if(!vue.add) $('#add_assistent_block').css('max-height', '57em');
                else  $('#add_assistent_block').css('max-height', '0em');
                vue.add = !vue.add;
            },
            add_tariff(){ send_ajax('/engine/adminSettings', $(event.target).serialize(), vue); },
            remove_tariff(name){
                send_ajax('/engine/adminSettings', {'remove_tariff': name});
                Vue.delete(vue.editions, name);
            },
            change(fitchaindex, type2, index, type1){
                let value;
                if(type1 != 'img'){
                    if(type1 != 'type') value = event.target.value;
                    else if(vue.editions[index].type == 'visible') value = 'hidden';
                    else value = 'visible';
                    send_ajax('/engine/adminSettings', {'type1': type1, 'type2': type2, 'index': index, 'value': value, 'fitchaindex': fitchaindex});
                    if(type1 == 'type') vue.$set(vue.editions[index], 'type', value)
                } else if(event.target.files.length){
                    let fd = new FormData;
                    fd.append('tariff_photo', $(event.target).prop('files')[0]);
                    fd.append('index', index);
                    formData_send_ajax('/engine/adminSettings', fd, vue);
                }
            },
        },
    });
}
function tools_page(tools){
	var vue = new Vue({ 
        el: '#middle_part',
        data: {
            add: false,
            tools: tools,
            loader:false,
            selected_index: 0,
            selected_color: '#00aaee',
        },
        methods: {
            ocform(){
                if(!vue.add) $('#add_assistent_block').css('max-height', '40em');
                else  $('#add_assistent_block').css('max-height', '0em');
                vue.add = !vue.add;
            },
            change(index, type, row){
                let value;
                if (type != 'photo'){ 
                    value = event.target.value;
                    send_ajax('/engine/adminSettings', {'value': value, 'tool_index': index, 'type': type});
                    vue.$set(vue.tools[row][index], type, value);
                } else if(event.target.files.length) { 
                    value = new FormData;
                    value.append('tool_photo', $(event.target).prop('files')[0]);
                    value.append('index', index);
                    formData_send_ajax('/engine/adminSettings', value, vue);
                }
            },
            add_tool(){
                var formData = new FormData($('form')[0]);
                formData_send_ajax('/engine/adminSettings', formData, vue);
            },
            remove_tool(index, row){
                send_ajax('/engine/adminSettings', {'remove_tool': index});
                Vue.delete(vue.tools[row], index);
            },
        },
    });
}
function variables_page(variables){
	var vue = new Vue({ 
        el: '#middle_part',
        data: {
            mas: variables,
            loader: false,
            pass: false,
        },
        methods: {
            change(name){
                let value = event.target.value;
                send_ajax('/engine/adminSettings', {'name': name, 'value': value});
                vue.$set(vue.editions[name],'type', value);
            },
            changepass(e){
                send_ajax('/engine/adminSettings', $(event.target).serialize());
                vue.pass = !vue.pass; 
                alert('Вы сменили пароль !'); // JS
            },
        },
    });
}
styles_mode();