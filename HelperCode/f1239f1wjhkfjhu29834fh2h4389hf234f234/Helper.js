const main_domain = "https://interhelper.ru";	
const ava_path = "/assistent_photos/";
var headTag = document.getElementsByTagName("head")[0];
var styleTag = document.createElement('link');
styleTag.type = 'text/css';
styleTag.rel = 'stylesheet';
styleTag.href = main_domain + '/HelperCode/helper.css';
headTag.appendChild(styleTag);
if(typeof jQuery == 'undefined') {
    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
    jqTag.src = main_domain + '/scripts/libs/jquery-3.6.0.min.js';
    headTag.appendChild(jqTag);
    if(typeof io == 'undefined'){
        var socketTag = document.createElement('script');
        socketTag.type = 'text/javascript';
        socketTag.src = main_domain + '/scripts/libs/socket.io.min.js';
        socketTag.crossorigin = 'anonymous';
        headTag.appendChild(socketTag);
        socketTag.onload = function(){
            jqTag.onload = helpercode();
        }
    } else jqTag.onload = helpercode;
} else {
    if(typeof io == 'undefined'){
        var socketTag = document.createElement('script');
        socketTag.type = 'text/javascript';
        socketTag.src = main_domain + '/scripts/libs/socket.io.min.js';
        socketTag.crossorigin = 'anonymous';
        headTag.appendChild(socketTag);
        socketTag.onload = helpercode;
    } else helpercode();
}
function helpercode(){
    $(document).ready(function(){
        const files_path = main_domain + "/user_adds/";
        var regexp = ['.rar','.zip','.doc','.docx','.ods','.odt','.pdf','.ppt','.pptx','.xlt','.xlsx','.xls','.docm','.dot','.txt','.zip'];
        var audio = new Audio(main_domain+'/audio-effect/new_message.mp3');
        var protocol_flag, dirname,
            offline_color, online_color,
            type, px_length, px_return_length, 
            online_name, offline_name, interval,
            offline_form_info, emojis;
        let InterHelper_sys_name_font_size = InterHelper_sys_name_font_size_offline = '14px';
        var personal_uid = window.localStorage.getItem('uid');
        var graphic_status = audio_status = activity = true;
        var servicing_status = chat_status = personal_sizes = assistent_status = false;
        var title = $('title').html(); 
        var error_trys = skiped_msgs = 0;
        var p_message = '';
        var personal_info = {};
        send_ajax(main_domain+'/engine/guest_func', {'get_emojis':true});
        if (window.location.protocol == "https:"){ dirname = 'https://api.interfire.ru:5321'; protocol_flag = 'https';}
        else {dirname = 'http://api.interfire.ru:5320'; protocol_flag = 'http';}
        var socket = io(dirname, { 
            reconnection: true,
            reconnectionDelay: 1000,
            forceNew: true,
            reconnectionDelayMax : 5000,
            reconnectionAttempts: 50
        });
        socket.on('connect', () => { 
           socket.emit('check_interhelper_domain', {"uid": personal_uid, "actual_page": window.location.href, 'previous_page': document.referrer, 'device': (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? 'desktop' : 'mobile') });
        });         
        socket.on("disconnect", () => { 
            $('#InterHelper_btn').remove();
            $('#InterHelper_msg_preview').remove();
            $('#InterHelper_window').remove();
        });
        socket.on('ban', () => { window.location.href = "https://ban.interhelper.ru";  }); // BAN
        socket.on('change_url', (data) => { 
            if(data.type == 'anticlicker') localStorage.anticlicker = true; 
            window.location.href = data.url; 
        }); // ROUTE
        socket.on('page_relaod', () => { window.location.href = window.location.href; });  // RELOAD
        socket.on('guest-message', (data) => { guest_send_message(data); }); // GUEST MESSAGE
        socket.on('uid', (data) =>{ // UID
            window.localStorage.setItem('uid', data.uid); 
            personal_uid = window.localStorage.getItem('uid');
        });
        document.addEventListener("visibilitychange", function(){
            if (document.hidden) activity = false; 
            else { 
                activity = true;
                clearInterval(interval);
                $('title').html(title);
                skiped_msgs = 0
            }
        });
        socket.on('consultant-message', (data) => { // ASSISTENT MESSAGE
            if(data.mode != 'js_mode') assistent_send_message(data, "new", files_path);
            else console.log(eval(escapeHtml(escapeHtml(data.message))));
            if(!activity){ 
                skiped_msgs++;
                if(interval) clearInterval(interval);
                $('title').html('Новое сообщение ' + skiped_msgs);
                interval = setInterval((skiped_msgs) => { 
                    if($('title').html().indexOf('сообщение') == -1) $('title').html('Новое сообщение ' + skiped_msgs);
                    else $('title').html(title);
                }, 1500, skiped_msgs);
            } 
        }); 
        socket.on("delete_form", () => { // REMOVE OFFLINE FORM
            assistent_status = true;
            if($("#InterHelper_offline_form").length && !$("#InterHelper_offline_form").hasClass('InterHelper_sended_offline_form')) $("#InterHelper_offline_form").remove();
            change_corner_status(online_color, online_name, InterHelper_sys_name_font_size);
        }); 
        socket.on('get_previous_messages', (data) => { // GET PREV MSGS
            for(msg in data){
                if(!data[msg].sender) guest_send_message(data[msg]);
                else if(data[msg].sender == 'offline_form') guest_send_offline_form(data[msg]);
                else assistent_send_message(data[msg], null, data[msg].sender == 'notification' ? '/notifications_photos/notification_adds/' : files_path);
            }
            $('#InterHelper_window_body').find("#offline_form").appendTo("#InterHelper_window_body");
            scroll_helper_body();
        }); 
        socket.on('offline-form', () => { // OFFLINE FORM STATUS
            assistent_status = false;
            if ($("#InterHelper_offline_form").length > 0) return;
            change_corner_status(offline_color, offline_name, InterHelper_sys_name_font_size_offline);
            let today = new Date();
            let cache_time;
            if(window.localStorage.getItem('interhelper_mail_status')){
                cache_time = new Date(window.localStorage.getItem('interhelper_mail_status'));
                cache_time.setMinutes(cache_time.getMinutes() + 5);
            }
            if(!offline_form_info) return;
            if(offline_form_info.feedbackENABLED == 'checked' && (cache_time <= today || !cache_time)) create_offline_form(offline_form_info.feedbackformPhone, offline_form_info.feedbackformEmail, offline_form_info.feedbackformName, htmldecoder(offline_form_info.feedbackTEXT));
        });  
        socket.on('servicing_assistent', (data) => { // SERVICING ASSISTENT
            servicing_status = true;
            let servicing_assistent = $(`
                <inter_div class="InterHelper_servicing_assistent">
                    <inter_span style="background-image: url(${main_domain}/assistent_photos/${data.photo});" class="InterHelper_servicing_photo"></inter_span>
                    <inter_div class="InterHelper_servicing_info">
                        <inter_p title="${data.name}">${data.name}</inter_p>
                        <inter_p title="${data.departament}">${data.departament}</inter_p>
                    </inter_div>
                </inter_div>
            `);
            $('#InterHelper_window_head .InterHelper_sys_name').html(servicing_assistent);
            $('#InterHelper_window_head .InterHelper_sys_name').css({
                'display':'flex',
            });
        });
        socket.on('finish_sevice', () => { // SEVICING FINISH
            servicing_status = false;
            $('#InterHelper_window_head .InterHelper_sys_name').html(online_name);
        });
        socket.on('interhelper_status', (data) => { // SERVER RESPONSE 
        	if(data.status != 'exist'){
                console.log('Пожайлуста активируйте interhelper на сайте или напишите в службу поддержки.');
                return;
            }
            start();
            create_emoji_window(emojis);
            socket.emit('get_settings');
            $('#InterHelper_msg_send_btn').on('click', function() { // MESSAGES
                if ($('#InterHelper_message_input').html().replace(/\r?\n/g, "").replace(/\s/g, '') != '') {
                    let message = $('#InterHelper_message_input').html();
                    var container = $('<div>').html(message);
                    container.find('.InterHelper_emoji').replaceWith(function() { return this.alt; })
                    message = container.html();
                    message = $("<div/>").html(message).text();
                    send_ajax(main_domain+'/engine/guest_func', {'message':message, 'uid': personal_uid, 'hostname': location.hostname});
                    $('#InterHelper_message_input').html('');
                    socket.emit("guest_print", {"text": ''});
                    error_trys = 0;  
                    if(parseInt($('#InterHelper_window_btns_menu').css('top')) == 0) $('#InterHelper_menu_btn').click();
                } else if(error_trys != 1) error_message('заполнены не все поля');	
            }); 
            $('#InterHelper_img_input').change(function(){ // PHOTOS
                if (!$(this).prop('files').length) return;
                loader("send");
                let fd = new FormData;
                fd.append('files', $(this).prop('files')[0]);
                fd.append('uid', personal_uid);
                fd.append('hostname', location.hostname);
                formData_send_ajax(main_domain+'/engine/guest_func', fd);
            }); 
            let foot_height = $('#InterHelper_message_input').height();
            let head_height = $('#InterHelper_window_head').height();
            $('#InterHelper_window_body').css('height', `calc(100% - ${foot_height}px - ${head_height}px - 20px)`);
            $("#InterHelper_message_input").bind("DOMSubtreeModified", function(){ 
                let foot_height = $('#InterHelper_message_input').height();
                let head_height = $('#InterHelper_window_head').height();
                $('#InterHelper_window_body').css('height', `calc(100% - ${foot_height}px - ${head_height}px - 20px)`);
                if($(this).html().length > 0) $('#InterHelper_placeholder').css('display', 'none');
                else $('#InterHelper_placeholder').css('display', 'block');
                socket.emit("guest_print", {"text": $(this).html()});
                $('.InterHelper_emoji_block').css('bottom', (foot_height + 15) + 'px')
            });
            $('#InterHelper_menu_btn').on('click', () => { // BTNS MENU
                if($('#InterHelper_window_btns_menu').css('top') == '-55px') $('#InterHelper_window_btns_menu').css({
                    'position': 'sticky',
                    'top': '0',
                });
                else {
                    $('#InterHelper_window_btns_menu').css({
                        'top': '-55px',
                        'position': 'absolute',
                    });
                    $('.InterHelper_emoji_block').css('display', 'none');
                }
            });
            $('#InterHelper_PDF_btn').click(function () { // PDF
                window.open(protocol_flag+'://interhelper.ru/engine/pdf?id=' + personal_uid, '_blank'); 
            });
            // вебвизор
            let session_movements = [];
            $(window).on('click', () => {
                let x = event.clientX;
                let y = event.clientY;
                session_movements.push({
                    x: x,
                    y: y,
                    type: 'click',
                });
            });
            $(window).on('mouseover', () => {
                let x = event.clientX;
                let y = event.clientY;
                session_movements.push({
                    x: x,
                    y: y,
                    type: 'mouse_move',
                });
            });
            $(window).on('scroll', () => {
                let scroll = $(window).scrollTop();
                session_movements.push({
                    scroll: scroll,
                    type: 'scroll',
                });
            });
        }); 
        socket.on('chat-settings', (data) => { // DOMAIN SETTINGS
            settings = data['settings'];
            offline_form_info = JSON.parse(data['offline_form']);
            online_name = htmldecoder(settings['SYSname']);
            offline_name = htmldecoder(settings['SYSname_offline']);
            if(settings['InterHelperInvitesOptions']['audio_invite_status'] == 'checked') audio_status = false;
            if(settings['InterHelperInvitesOptions']['graphic_invite_status'] == 'checked'){  
                graphic_status = false; 
                $('#InterHelper_msg_preview').css('display', 'none'); 
            }
            if(settings['chat_status_checkbox'] == 'checked') chat_status = true;
            $('.InterHelper_first_assistent_message inter_div').text(htmldecoder(settings['SYSFmessage']));
            let btn_bg = settings['bgcolor'];
            let btn_color = settings['textcolor'];
            let btn_logo_points_bg = settings['logodetailscolor'];
            let btn_logo_bg = settings['logobgcolor'];
            let btn_shadow_bg = settings['button_shadow'];
            let btn_height = settings['helper_btn_height'];
            let btn_width = settings['helper_btn_width'];
            let btn_fontSize = settings['helper_btn_font'];
            let btn_fontWeight = settings['helper_btn_font_weigt'];
            let btn_logo_size = settings['btn_svg_size'];
            let position_type = settings['position_type'];
            online_color = settings['StatusOnlinecolor'];
            offline_color = settings['StatusOfflinecolor'];
            let window_bg = settings['windowbgcolor'];
            let window_color = settings['windowtextcolor'];
            let window_frame_bg = settings['FrameColor'];
            let window_name_fontSize = settings['chatheader_font_size'];
            let window_name_fontWeight = settings['chatheader_font_weight'];
            let window_shadow = settings['window_shadow'];
            let msg_fontSize = settings['msg_font_size'];
            let msg_fontWeight = settings['msg_font_weight'];
            let user_msg_bg = settings['UmessagesColor'];
            let assistent_msg_bg = settings['AmessagesColor'];
            let error_msg_bg = settings['error_color'];
            let success_msg_bg = settings['success_color'];
            let scroll_bg = settings['scroll_color'];
            let svg_color = settings['SvgColor'];
            let modal_msg_color = settings['modal_message_text_color'];
            let modal_msg_bg = settings['modal_message_color'];
            let modal_msg_shadow = settings['modal_message_shadow_color'];
            personal_sizes = (settings['PersonalSize'] == 'checked');
            if(!personal_sizes){
                if(settings['SYSname'] > 50) return;
                else if (settings['SYSname'].length > 40) InterHelper_sys_name_font_size = '15.25px';
                else if (settings['SYSname'].length > 30) InterHelper_sys_name_font_size = '15.75px';
                else if (settings['SYSname'].length > 20) InterHelper_sys_name_font_size = '16px';
                else if (settings['SYSname'].length > 10) InterHelper_sys_name_font_size = '17px';
                else InterHelper_sys_name_font_size = '18px';
                if(settings['SYSname_offline'] > 50) return;
                else if (settings['SYSname_offline'].length > 40) InterHelper_sys_name_font_size_offline = '15.25px';
                else if (settings['SYSname_offline'].length > 30) InterHelper_sys_name_font_size_offline = '15.75px';
                else if (settings['SYSname_offline'].length > 20) InterHelper_sys_name_font_size_offline = '16px';
                else if (settings['SYSname_offline'].length > 10) InterHelper_sys_name_font_size_offline = '17px';
                else InterHelper_sys_name_font_size_offline = '18px';
            }
            if(localStorage['InterHelper_height']) {
                let height = localStorage['InterHelper_height'];
                if(height > document.documentElement.clientHeight - 40) height =  document.documentElement.clientHeight - 40;
                $('#InterHelper_window').css('height', height + 'px');
            }
            if(localStorage['InterHelper_right']){ 
                let right = localStorage['InterHelper_right'];
                if(right > document.documentElement.clientWidth - $('#InterHelper_window').width() - 40) right = document.documentElement.clientWidth - $('#InterHelper_window').width() - 40;
                $('#InterHelper_window').css('right', right + 'px');
            }
            var style = `
                <style>
                    #InterHelper_btn{
                        background: ${btn_bg};
                        box-shadow: 0 0 10px ${btn_shadow_bg}; 
                        display:flex;
                    }
                    #InterHelper_btn .InterHelper_sys_name{
                        color: ${btn_color};
                    }
                    #InterHelper_btn_logo{
                        fill: ${btn_logo_bg};
                    }
                    .helper_st1{
                        fill: ${btn_logo_points_bg};
                    }
                    #InterHelper_window_head,
                    #InterHelper_window_foot{
                        background: ${window_frame_bg};
                    }
                    #InterHelper_msg_preview{
                        color: ${modal_msg_color};
                        background: ${modal_msg_bg};
                        box-shadow: 0 0 10px ${modal_msg_shadow};
                    }
                    #InterHelper_window_head .InterHelper_sys_name,
                    .InterHelper_assistent_message > inter_div,
                    .InterHelper_first_assistent_message > inter_div,
                    #InterHelper_offline_form input,
                    #InterHelper_offline_form textarea,
                    #InterHelper_offline_form button,
                    #InterHelper_offline_form inter_p,
                    #InterHelper_offline_form,
                    .InterHelper_date_time,
                    .InterHelper_guest_message > inter_div,
                    .InterHelper_error_message > inter_div,
                    .InterHelper_guest_message_photo,
                    .sended_offline_form textarea,
                    .sended_offline_form input,
                    .sended_offline_form,
                    #InterHelper_placeholder,
                    .InterHelper_success_message > inter_div{
                        color: ${window_color};
                        border-color: ${window_color};
                    }
                    #InterHelper_close_window_btn{
                        border-color: ${svg_color};
                        background: ${window_frame_bg};
                    }
                    #InterHelper_close_window_btn inter_span{
                        background: ${svg_color};
                    }
                    #InterHelper_window{
                        background:${window_bg};
                        box-shadow: 0 0 10px ${window_shadow}; 
                    }
                    .InterHelper_guest_message_photo,
                    .InterHelper_guest_message > inter_div,
                    InterHelper_guest_photo_before{
                        background: ${user_msg_bg};
                    }
                    #InterHelper_offline_form, 
                    .sended_offline_form,
                    .InterHelper_assistent_message > inter_div,
                    .InterHelper_first_assistent_message > inter_div,
                    InterHelper_assistent_photo_before{
                        background: ${assistent_msg_bg};
                    }
                    .InterHelper_error_message > inter_div{
                        background: ${error_msg_bg};
                    }
                    .InterHelper_success_message > inter_div{
                        background: ${success_msg_bg};
                    }
                    #InterHelper_offline_form textarea::-webkit-scrollbar-thumb, 
                    #InterHelper_message_input::-webkit-scrollbar-thumb,
                    .sended_offline_form textarea::-webkit-scrollbar-thumb,
                    #InterHelper_window_body::-webkit-scrollbar-thumb,
                    .InterHelper_emoji_block::-webkit-scrollbar-thumb{ 
                        background-color: ${scroll_bg} !important; 
                    }
                    ${
                        (
                            personal_sizes ? 
                            `   .InterHelper_first_assistent_message > inter_div,
                                .InterHelper_assistent_message > inter_div,
                                .InterHelper_guest_message > inter_div,
                                .InterHelper_error_message > inter_div,
                                .InterHelper_success_message{
                                    font-size: ${msg_fontSize}px;
                                    font-weight: ${msg_fontWeight};
                                }
                            ` : ""
                        )
                    }
                    #InterHelper_menu_btn,
                    #InterHelper_msg_send_btn,
                    .InterHelper_window_btns_menu_btn{
                        fill: ${svg_color};
                        border-color:${svg_color};
                    }
                    #InterHelper_btn:hover .InterHelper_round_btn{
                        border-color: ${btn_logo_bg} !important;
                    }
                </style>
            `;
            $(document.body).append(style);
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                $('#InterHelper_window_head').css({
                    'border-radius': 'initial',
                });
                $('#InterHelper_window').css({
                    'height': '100%',
                    'width': '100%',
                    'right': 0,
                });
                $('#InterHelper_close_window_btn').css({
                    'left': 'auto',
                    'right': '0',
                    'z-index': '100000000000'
                });
                position_type = settings['mobile_position_type'];
                personal_sizes = false;
            }
            // тип позиционирования
            let button_styles;
            let personal_btn_text = {
                'font-size': btn_fontSize + 'px',
                'font-weight': btn_fontWeight,
            }
            let personal_btn = {
                'height': btn_height + 'px',
                'width': btn_width + 'px',
            }
            let personal_btn_svg = {
                'height': btn_logo_size + 'px',
                'width': btn_logo_size + 'px',
            }
            let personal_window_text = {
                'font-size': window_name_fontSize + 'px',
                'font-weight': window_name_fontWeight,
            }
            let more_btn_styles = {};
            if(position_type == 'first_position' || position_type == 'second_position' || position_type == 'third_position'){
                let BrandNameStyles = { 
                    'margin-left': '10px',
                    'margin-right': '30px',
                };
                let SvgStyles = { 
                    'margin-left': '10px',
                };
                let Status_Styles = {
                    'bottom': '-25px',
                    'right': '-25px',
                };
                button_styles = { 
                    'top': '0', 
                    'left': '15%',
                    'width': 'auto',
                    'min-width': '225px',
                    'min-height': '40px',
                    'border-bottom-left-radius': '15px',
                    'border-bottom-right-radius': '15px',
                    'align-items': 'center',
                    'display': 'inline-flex',
                }; 
                if(position_type == 'first_position') more_btn_styles = {'left': '15%'};
                else if(position_type == 'second_position') more_btn_styles = {'left': '50%', 'transform': 'translate(-50%,0)'}; 
                else if(position_type == 'third_position') more_btn_styles = {'right': '15%'}; 
                button_styles = Object.assign({}, button_styles, more_btn_styles);
                $('#InterHelper_btn').css(button_styles);
                $('#InterHelper_btn .InterHelper_sys_name').css(BrandNameStyles);
                $('#InterHelper_btn_logo').css(SvgStyles);
                $('#InterHelper_btn .InterHelper_status').css(Status_Styles);
                type = 'top'; px_length = '-'+(parseInt($('#InterHelper_btn').css('height').replace('px', '')) + 10)+'px';
            }
            else if(position_type == 'fourth_position' || position_type == 'fith_position' || position_type == 'sixth_position'){
                let BrandNameStyles = { 
                    'margin-left': '10px',
                    'margin-right': '30px',
                };
                let SvgStyles = { 
                    'margin-left': '10px',
                };
                let Status_Styles = {
                    'top': '-25px',
                    'right': '-25px',
                };
                button_styles = { 
                    'bottom': '0', 
                    'width': 'auto',
                    'min-width': '225px',
                    'min-height': '40px',
                    'align-items': 'center',
                    'border-top-left-radius': '15px',
                    'border-top-right-radius': '15px' 
                };
                if(position_type == 'fourth_position') more_btn_styles = {'left': '15%'};
                else if(position_type == 'fith_position') more_btn_styles = {'left': '50%', 'transform': 'translate(-50%,0)'};
                else if(position_type == 'sixth_position') more_btn_styles = {'right': '15%'};
                button_styles = Object.assign({}, button_styles, more_btn_styles);
                $('#InterHelper_btn').css(button_styles);
                $('#InterHelper_btn .InterHelper_sys_name').css(BrandNameStyles);
                $('#InterHelper_btn_logo').css(SvgStyles);
                $('#InterHelper_btn .InterHelper_status').css(Status_Styles);
                type = 'bottom'; px_length = '-'+(parseInt($('#InterHelper_btn').css('height').replace('px', '')) + 10)+'px';
            }
            else if(position_type == 'seventh_position' || position_type == 'eighth_position' || position_type == 'twelve_position'){
                let BrandNameStyles = { 
                    'margin-bottom': '10px',
                    'margin-top': '30px',
                    'writing-mode': 'vertical-lr',
                    'transform': 'scale(-1, -1)',
                };
                let SvgStyles = { 
                    'margin-bottom': '10px',
                    'writing-mode': 'vertical-lr',
                    'transform': 'rotate(-90deg)'
                };
                let Status_Styles = {
                    'top': '-25px',
                    'right': '-25px',
                };
                button_styles = { 
                    'left': '0', 
                    'min-width': '40px', 
                    'height': 'auto', 
                    'min-height': '225px',
                    'align-items': 'center',
                    'border-top-right-radius': '15px', 
                    'border-bottom-right-radius': '15px',
                    'flex-direction':'column-reverse', 
                };
                if(position_type == 'seventh_position') more_btn_styles = { 'bottom': '15%'};
                else if(position_type == 'eighth_position') more_btn_styles = { 'top': '15%'};
                else if(position_type == 'twelve_position') more_btn_styles = { 'top': '50%'};
                button_styles = Object.assign({}, button_styles, more_btn_styles);
                $('#InterHelper_btn').css(button_styles);
                $('#InterHelper_btn .InterHelper_sys_name').css(BrandNameStyles);
                $('#InterHelper_btn_logo').css(SvgStyles);
                $('#InterHelper_btn .InterHelper_status').css(Status_Styles);
                type = 'left'; px_length = '-'+(parseInt($('#InterHelper_btn').css('width').replace('px', '')) + 10)+'px';
            }
            else if(position_type == 'nineth_position' || position_type == 'tenth_position' || position_type == 'eleven_position'){
                let BrandNameStyles = { 
                    'writing-mode': 'vertical-lr',
                    'transform': 'scale(-1, -1)',
                    'margin-bottom': '10px',
                    'margin-top': '30px',
                };
                let SvgStyles = { 
                    'writing-mode': 'vertical-lr',
                    'transform': 'rotate(-90deg)',
                    'margin-bottom': '10px',
                };
                let Status_Styles = {
                    'top': '-25px',
                    'left': '-25px',
                };
                button_styles = { 
                    'right': '0', 
                    'min-width': '40px', 
                    'height': 'auto', 
                    'min-height': '225px',
                    'align-items': 'center',
                    'border-top-left-radius': '20px', 
                    'border-bottom-left-radius': '20px',
                    'flex-direction': 'column-reverse', 
                };
                if(position_type == 'nineth_position')  more_btn_styles = { 'bottom': '15%' };
                else if(position_type == 'tenth_position')  more_btn_styles = { 'top': '15%' };
                else if(position_type == 'eleven_position')  more_btn_styles = { 'top': '50%', 'transform': 'translateY(-50%)' } ;
                button_styles = Object.assign({}, button_styles, more_btn_styles);
                $('#InterHelper_btn').css(button_styles);
                $('#InterHelper_btn .InterHelper_sys_name').css(BrandNameStyles);
                $('#InterHelper_btn_logo').css(SvgStyles);
                $('#InterHelper_btn .InterHelper_status').css(Status_Styles);
                type = 'right'; px_length = '-'+(parseInt($('#InterHelper_btn').css('width').replace('px', '')) + 10)+'px';
            }
            else if(position_type == 'special1_position' || position_type == 'special2_position' || position_type == 'special3_position' || position_type == 'special4_position'){
                button_styles = { 
                    'border-radius': '50%',
                    'height': '65px', 
                    'width': ' 65px',
                    'overflow':' visible',
                };
                $('#InterHelper_btn .InterHelper_sys_name').remove();
                $('#InterHelper_btn_logo').css({ 
                    'top': '50%', 
                    'left': '50%', 
                    'transform': 'translate(-50%,-50%)' 
                });
                $('#InterHelper_btn').append('<inter_span class="InterHelper_round_btn"></inter_span><inter_span class="InterHelper_round_btn"></inter_span>');
                if(position_type == 'special1_position') more_btn_styles = {'top': '30px', 'left': '30px' };
                else if(position_type == 'special2_position') more_btn_styles = {'top': '30px', 'right': '30px' };
                else if(position_type == 'special3_position') more_btn_styles = {'bottom': '30px', 'left': '30px' };
                else if(position_type == 'special4_position') more_btn_styles = {'bottom': '30px', 'right': '30px' };
                button_styles = Object.assign({}, button_styles, more_btn_styles);
                $('#InterHelper_btn').css(button_styles);
                $('#InterHelper_btn .InterHelper_status').css({
                    'position': 'absolute', 
                    'bottom': '-20px', 
                    'width': (parseInt($('#InterHelper_btn').css('width').replace('px', '')) + 10)+'px', 
                    'top': '50%',
                    'left': '50%',                    
                    'height': (parseInt($('#InterHelper_btn').css('height').replace('px', '')) + 10)+'px', 
                    'transform': 'translateZ(-1px) translateX(-50%) translateY(-50%)',
                    'z-index': '-1',
                    'animation': 'InterHelper_Pulse ease 4.85s infinite',
                });
                if(position_type == 'special1_position' || position_type == 'special2_position'){
                    type = 'top'; 
                    px_length = '-'+(parseInt($('#InterHelper_btn').css('height').replace('px', '')) + 30)+'px';
                    px_return_length = '30px';
                } else {
                    type = 'bottom'; 
                    px_length = '-'+(parseInt($('#InterHelper_btn').css('height').replace('px', '')) + 30)+'px';
                    px_return_length = '30px';
                }
            } else {
                $('#InterHelper_btn').css('display', 'none');
                type = 'top'; 
                px_length = '-10000px';
            } 
            if(personal_sizes){
                $('#InterHelper_btn').css(personal_btn);
                $('#InterHelper_btn .InterHelper_sys_name').css(personal_btn_text);
                $('#InterHelper_btn_logo').css(personal_btn_svg);
                $('#InterHelper_window_head .InterHelper_sys_name').css(personal_window_text);
                px_length = '-'+(parseInt($('#InterHelper_btn').css('height').replace('px', '')) + 30)+'px';
            }
            socket.emit("check_offline_status");
        }); 
        socket.on('swap', (data) => {
            if(localStorage.anticlicker){
                delete localStorage.anticlicker;
                return;
            }
            for(swap_id in data) swap_func(data[swap_id], swap_id); 
        });
        socket.on('notifications', (data) => {
            let today = new Date();
            for(index in data) {
                let notification = data[index];
                create_notification(notification, today, false);
            }
        });
        socket.on('personal_info', (data) => { personal_info = data; });
        function check_notification_conditions(notification, today, user_event){
            let conditions = JSON.parse(notification['conditions']);
            let notification_id = notification.uid;
            let external_options = [];
            let flag = true;
            for(condition_id in conditions){
                let condition = conditions[condition_id];
                if(typeof condition === 'object'){
                    let condition_type = condition['type'];
                    let main = condition['main'];
                    let second = condition['second'];
                    if(condition_type == 'link') {
                        let url = location.href;
                        if(url.indexOf(main) == -1) flag = false
                    } else if(condition_type == 'activity_time') {
                        let activity_time = personal_info.session_time;
                        let session_start = new Date(personal_info.session_start);
                        activity_time += (today - session_start);
                        let condition_time = (parseInt(main.split(':')[0]) * 1000 * 60 * 60) + (parseInt(main.split(':')[1]) * 1000 * 60);
                        if((second == '>' && activity_time < condition_time) || (second == '<' && activity_time > condition_time) || (second == '=' && activity_time != condition_time)){
                            flag = false;
                            if(second == '>' && activity_time < condition_time){
                                let interval = condition_time - activity_time;
                                setTimeout((notification) => {
                                    create_notification(notification, new Date());
                                }, interval, notification);
                            }
                        }
                    } else if(condition_type == 'open_counter'){
                        if((personal_info.visits != main && second == '=') || (personal_info.visits < main && second == '>')  || (personal_info.visits > main && second == '<')) flag = false;
                    } else if(condition_type == 'time'){
                        let condition_date = new Date();
                        condition_date.setHours(parseInt(main.split(':')[0]));condition_date.setMinutes(parseInt(main.split(':')[1]));
                        if((second == '>' && today < new Date(condition_date)) || (second == '<' && today > new Date(condition_date))){ 
                            flag = false;
                            if(second == '>' && today < new Date(main)){
                                let interval = new Date(main) - today;
                                setTimeout((notification) => {
                                    create_notification(notification, new Date());
                                }, interval, notification);
                            }
                        }
                    } else if(condition_type == 'personal_event' && !user_event){
                        $(main).on(second, () => { create_notification(notification, new Date(), true); });
                        flag = false;
                    } 
                } else {
                    if(condition == 'desktop' || condition == 'mobile'){
                       if(condition != (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? 'desktop' : 'mobile')) flag = false; 
                    } else if(condition == 'consultant_offline' || condition == 'consultant_online'){
                        if((assistent_status && condition == 'consultant_offline') || (!assistent_status && condition == 'consultant_online')) flag = false;
                    } else if(condition == 'use_once' || condition == 'save') external_options.push(condition);
                    else if((condition == 'open' || condition == 'msg_input' || condition == 'msg_send' || condition == 'close' || condition == 'personal_event') && !user_event){
                        flag = false;
                        if(condition == 'open'){
                            $('#InterHelper_btn').on('click', () => { create_notification(notification, new Date(), true); });
                        } else if(condition == 'close'){
                            $('#InterHelper_close_window_btn').on('click', () => { create_notification(notification, new Date(), true); });
                        } else if(condition == 'msg_input'){
                            $("#InterHelper_message_input").bind("DOMSubtreeModified", () => { create_notification(notification, new Date(), true); });
                        } else {
                            $("#InterHelper_msg_send_btn").on("click", () => { create_notification(notification, new Date(), true); });
                        }
                    } 
                } 
            }
            if(flag){
                if(external_options.indexOf('use_once') != -1){
                    if(localStorage.notifications){
                        let store = JSON.parse(localStorage.notifications);
                        if(store.hasOwnProperty(notification_id)) return false;
                    }
                }
                return true;
            }
            return false;
        }
        function create_notification(notification, today, user_event){
            let status = check_notification_conditions(notification, today, user_event);
            if(status == false) return;
            let message = {
                "message_adds": notification.adds,
                "message": notification.type == 'DOM' ?  escapeHtml(notification.text) : notification.text,
                "user": notification.name,
                "departament": notification.departament,
                "photo": notification.photo,
                "time": new Date().toISOString().split('T').join(' ').split('.')[0],
            }
            if(notification.conditions.indexOf('use_once') != -1){
                if(!localStorage['notifications']) localStorage['notifications'] = '{}';
                let store = JSON.parse(localStorage['notifications']);  
                store[notification.uid] = notification.uid;
                localStorage['notifications'] = JSON.stringify(store);
            } 
            if(notification.conditions.indexOf('save') != -1 && !notification.conditions.hasOwnProperty('msg_input')) socket.emit('save_notification', notification.uid);
            if(notification.type != 'JavaScript') assistent_send_message(message, 'new', {'adds': main_domain + '/notifications_photos/notification_adds/', 'ava': '/notifications_photos/notification_photos/'});
            else console.log(eval(escapeHtml(escapeHtml(message.message))));
            socket.emit('notification_statistic', {'uid': notification.uid});
        }
        function swap_func(swap, swap_id, user_event){
            let swap_from = escapeHtml(swap.swap_from); 
            let swap_if = swap.swap_if||{}; 
            let swap_time = swap.swap_time; 
            let swap_type = swap.swap_type;
            let swap_changename = swap.swap_changename;
            let object_links = null;
            if(swap_time == 'never') return;
            if(swap_type == 'phone') object_links =  $(`a[href*="tel:"]`); 
            else if(swap_type == 'mail') object_links =  $(`a[href*="mailto:"]`); 
            else if(swap_type == 'img') object_links =  $(`img[src*="${swap_from}"]`); 
            else if(swap_type == 'link') object_links = $(`a[href*="${swap_from}"]`); 
            else if(swap_type == 'text') object_links = $(`${swap_from}`);
            if(swap_time == 'always' || Object.keys(swap_if) == 0) swap_object(object_links, swap_type, swap, swap_id, false, swap_changename); 
            else if(swap_check(swap, swap_id, user_event)) swap_object(object_links, swap_type, swap, swap_id, false, swap_changename);
            else if(check_cache_swap(swap_id, swap)) swap_object(object_links, swap_type, swap, swap_id, true, swap_changename); 
        }
        function swap_check(swap, swap_id, user_event){
            let today = new Date();
            let swap_time = swap.swap_time;
            let swap_if = swap.swap_if;
            for(condition_index in swap_if){ // идём по условиям замены
                let condition = swap_if[condition_index]; 
                let main_condition = escapeHtml(condition.main); 
                if(typeof condition === 'object'){
                    if(condition.type == 'time'){
                        let second_condition = escapeHtml(condition.second);  
                        let condition_time = new Date();
                        condition_time.setHours(parseInt(main_condition.split(':')[0])); 
                        condition_time.setMinutes(parseInt(main_condition.split(':')[1]));
                        if((today >= condition_time && second_condition == ">" || today <= condition_time && second_condition == "<") && swap_time == 'ifOne') return true;
                        else if(!(today >= condition_time && second_condition == ">" || today <= condition_time && second_condition == "<") && swap_time == 'ifAll') return false;
                    } else if(condition.type == 'link'){
                        let loc = decodeURI(location.href);
                        if(loc.indexOf(main_condition) != -1 && swap_time == 'ifOne') return true;
                        else if(loc.indexOf(main_condition) == -1 && swap_time == 'ifAll') return false;
                    } else if(condition.type == 'activity_time') {
                        let second_condition = escapeHtml(condition.second);  
                        let activity_time = personal_info.session_time;
                        let session_start = new Date(personal_info.session_start);
                        activity_time += (today - session_start);
                        let condition_time = (parseInt(main_condition.split(':')[0]) * 1000 * 60 * 60) + (parseInt(main_condition.split(':')[1]) * 1000 * 60);
                        if((second_condition == '>' && activity_time < condition_time) || (second_condition == '<' && activity_time > condition_time) || (second_condition == '=' && activity_time != condition_time)){
                            if(second_condition == '>' && activity_time < condition_time){
                                let interval = condition_time - activity_time;
                                setTimeout((swap, swap_id) => {
                                    swap_func(swap, swap_id, false);
                                }, interval, swap, swap_id);
                                if(swap_time == 'ifAll') return false;
                            }
                        } else if(swap_time == 'ifOne') return true;
                    } else if(condition.type == 'open_counter'){
                        let second_condition = escapeHtml(condition.second);  
                        if((personal_info.visits != main_condition && second_condition == '=') || (personal_info.visits < main_condition && second_condition == '>')  || (personal_info.visits > main_condition && second_condition == '<')){
                            if(swap_time == 'ifAll') return false;
                        } else if(swap_time == 'ifOne') return true;
                    } else if(conditio,_type == 'personal_event' && !user_event){
                        $(main).on(second, () => { swap_func(swap, swap_id, true); });
                        if(swap_time == 'ifAll') return false;
                    }
                } else {
                    if(condition == 'desktop' || condition == 'mobile'){
                        if(condition != (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? 'desktop' : 'mobile')){
                            if(swap_time == 'ifAll') return false;
                        } else if(swap_time == 'ifOne') return true;
                    } else if(condition == 'consultant_offline' || condition == 'consultant_online'){
                        if((assistent_status && condition == 'consultant_offline') || (!assistent_status && condition == 'consultant_online')){
                            if(swap_time == 'ifAll') return false;
                        } else if(swap_time == 'ifOne') return true;
                    } else if((condition == 'open' || condition == 'msg_input' || condition == 'msg_send' || condition == 'close' || condition == 'personal_event') && !user_event){
                        if(condition == 'open'){
                            $('#InterHelper_btn').on('click', () => { swap_func(swap, swap_id, true); });
                        } else if(condition == 'close'){
                            $('#InterHelper_close_window_btn').on('click', () => { swap_func(swap, swap_id, true); });
                        } else if(condition == 'msg_input'){
                            $("#InterHelper_message_input").bind("DOMSubtreeModified", () => { swap_func(swap, swap_id, true); });
                        } else {
                            $("#InterHelper_msg_send_btn").on("click", () => { swap_func(swap, swap_id, true); });
                        }
                        if(swap_time == 'ifAll') return false;
                    } 
                } 
            }
            if(swap_time == 'ifAll') return true;
            return false;
        }
        function check_cache_swap(swap_id, swap){
            if(!localStorage.hasOwnProperty("swap_ids")) return false;
            let swap_cache_mas = JSON.parse(localStorage["swap_ids"]);
            if(swap_cache_mas.indexOf(swap_id) != -1 && swap.swap_cache) return true;
            else if(swap_cache_mas.indexOf(swap_id) != -1){ 
                swap_cache_mas.slice(swap_cache_mas.indexOf(swap_id), 1);
                localStorage["swap_ids"] = JSON.stringify(swap_cache_mas);
            }
            return false;
        }
        function start(){ // CHAT CREATOR 
            if($('#InterHelper_window').length > 0) return;
            InterHelperButton = $(`
                <inter_div id="InterHelper_btn">
                    <inter_span class="InterHelper_status"></inter_span>
                    <svg id="InterHelper_btn_logo" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 546.76 380.96" style="enable-background:new 0 0 546.76 380.96;" xml:space="preserve">
                        <g>
                            <path style="transform:rotateY(180deg); transform-origin:center center;" class="helper_st0" 
                            d="M0,203.57V23.48C0,10.51,10.51,0,23.48,0h499.81c12.97,0,23.48,10.51,23.48,23.48l0,334.28c0,21.26-25.62,31.02-40.51,15.85c-4.28-4.36-8.86-8.49-13.73-12.38c-36.77-29.4-89.86-44.31-157.68-44.31H23.48C10.51,316.91,0,306.4,0,293.44V203.57z"/>
                            <circle class="helper_st1" cx="119.05" cy="157.87" r="35.08"/>
                            <circle class="helper_st1" cx="244.91" cy="157.87" r="35.08"/>
                            <circle class="helper_st1" cx="370.77" cy="157.87" r="35.08"/>
                        </g>
                    </svg>
                    <inter_p class="InterHelper_sys_name">InterHelper</inter_p>
                </inter_div>
            `);
            InterHelperForm = $(`
                <inter_div id="InterHelper_window">
                    <inter_div id="InterHelper_close_window_btn">
                        <inter_span></inter_span>
                        <inter_span></inter_span>
                    </inter_div>
                    <inter_div id="InterHelper_window_head">
                        <inter_span class="InterHelper_status"></inter_span>
                        <inter_p class="InterHelper_sys_name">InterHelper</inter_p>
                    </inter_div>
                    <inter_div id="InterHelper_window_body">
                        <inter_div id="InterHelper_window_btns_menu">
                            <inter_div id="InterHelper_PDF_btn" title="Скачать историю диалога в формате PDF.">
                                <svg class="InterHelper_window_btns_menu_btn" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                    <g><g><g>
                                        <path d="M368,256h-64c-8.837,0-16,7.163-16,16v128c0,8.837,7.163,16,16,16h64c17.673,0,32-14.327,32-32v-96C400,270.327,385.673,256,368,256z M368,384h-48v-96h48V384z"/>
                                        <path d="M512,288v-32h-80c-8.837,0-16,7.163-16,16v144h32v-64h64v-32h-64v-32H512z"/>
                                        <path d="M32,464V48c0-8.837,7.163-16,16-16h240v64c0,17.673,14.327,32,32,32h64v48h32v-64c0.025-4.253-1.645-8.341-4.64-11.36l-96-96C312.341,1.645,308.253-0.024,304,0H48C21.49,0,0,21.491,0,48v416c0,26.51,21.49,48,48,48h112v-32H48C39.164,480,32,472.837,32,464z"/>
                                        <path d="M240,256h-64c-8.837,0-16,7.163-16,16v144h32v-48h48c17.673,0,32-14.327,32-32v-48C272,270.327,257.673,256,240,256z M240,336h-48v-48h48V336z"/>
                                    </g></g></g>
                                </svg>
                            </inter_div>
                            <input id="InterHelper_img_input" type="file" style="display:none;" />
                            <label title="Отправить фотографию." for="InterHelper_img_input" id="InterHelper_send_img_btn">
                                <svg class="InterHelper_window_btns_menu_btn" version="1.1" id="InterHelper_send_img_svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 480 480" style="enable-background:new 0 0 480 480;" xml:space="preserve" > 
                                    <g><g>
                                        <path d="M160,344h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S164.418,344,160,344z"/>
                                    </g></g>
                                    <g><g>
                                        <path d="M384,344H192c-4.418,0-8,3.582-8,8s3.582,8,8,8h192c4.418,0,8-3.582,8-8S388.418,344,384,344z"/></g></g><g><g>
                                        <path d="M160,296h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S164.418,296,160,296z"/>
                                    </g></g>
                                    <g><g>
                                        <path d="M384,296H192c-4.418,0-8,3.582-8,8s3.582,8,8,8h192c4.418,0,8-3.582,8-8S388.418,296,384,296z"/></g></g><g><g>
                                        <path d="M160,248h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S164.418,248,160,248z"/>
                                    </g></g>
                                    <g><g>
                                        <path d="M384,248H192c-4.418,0-8,3.582-8,8s3.582,8,8,8h192c4.418,0,8-3.582,8-8S388.418,248,384,248z"/></g></g><g><g>
                                        <path d="M160,200h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S164.418,200,160,200z"/>
                                    </g></g>
                                    <g><g>
                                        <path d="M384,200H192c-4.418,0-8,3.582-8,8s3.582,8,8,8h192c4.418,0,8-3.582,8-8S388.418,200,384,200z"/></g></g><g><g>
                                        <path d="M160,152h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S164.418,152,160,152z"/>
                                    </g></g>
                                    <g><g>
                                        <path d="M384,152H192c-4.418,0-8,3.582-8,8s3.582,8,8,8h192c4.418,0,8-3.582,8-8S388.418,152,384,152z"/></g></g><g><g>
                                        <path d="M439.896,119.496c-0.04-0.701-0.177-1.393-0.408-2.056c-0.088-0.256-0.152-0.504-0.264-0.752c-0.389-0.87-0.931-1.664-1.6-2.344l-112-112c-0.68-0.669-1.474-1.211-2.344-1.6c-0.248-0.112-0.496-0.176-0.744-0.264c-0.669-0.23-1.366-0.37-2.072-0.416C320.328,0.088,320.176,0,320,0H96c-4.418,0-8,3.582-8,8v24H48c-4.418,0-8,3.582-8,8v432c0,4.418,3.582,8,8,8h336c4.418,0,8-3.582,8-8v-40h40c4.418,0,8-3.582,8-8V120C440,119.824,439.912,119.672,439.896,119.496z M328,27.312L412.688,112H328V27.312z M376,464H56V48h32v376c0,4.418,3.582,8,8,8h280V464z M424,416H104V16h208v104c0,4.418,3.582,8,8,8h104V416z"/>
                                    </g></g>
                                    <g><g>
                                        <path d="M192,72h-48c-4.418,0-8,3.582-8,8v48c0,4.418,3.582,8,8,8h48c4.418,0,8-3.582,8-8V80C200,75.582,196.418,72,192,72z M184,120h-32V88h32V120z"/>
                                    </g></g>
                                </svg>
                            </label>
                            <inter_div id="InterHelper_emoji_btn"  title="Отправить emoji." >
                                <svg class="InterHelper_window_btns_menu_btn" enable-background="new 0 0 24 24" height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="m2.149 18.169c-.264 0-.52-.139-.657-.386-.975-1.76-1.492-3.76-1.492-5.783 0-6.617 5.383-12 12-12 1.929 0 3.842.47 5.533 1.358.367.193.508.646.315 1.013-.191.367-.644.509-1.013.315-1.477-.776-3.148-1.186-4.835-1.186-5.79 0-10.5 4.71-10.5 10.5 0 1.769.451 3.517 1.305 5.056.2.362.069.819-.292 1.02-.116.063-.241.093-.364.093z"/>
                                    <path d="m12 24c-2.223 0-4.392-.614-6.272-1.775-.353-.217-.462-.68-.245-1.032.218-.351.681-.462 1.032-.244 1.644 1.014 3.541 1.551 5.485 1.551 5.79 0 10.5-4.71 10.5-10.5 0-1.947-.536-3.84-1.551-5.474-.218-.352-.109-.814.242-1.033.352-.22.814-.11 1.032.242 1.163 1.871 1.777 4.038 1.777 6.265 0 6.617-5.383 12-12 12z"/>
                                    <path d="m9.25 11.25c-.414 0-.75-.336-.75-.75 0-.551-.448-1-1-1s-1 .449-1 1c0 .414-.336.75-.75.75s-.75-.336-.75-.75c0-1.378 1.121-2.5 2.5-2.5s2.5 1.122 2.5 2.5c0 .414-.336.75-.75.75z"/>
                                    <path d="m18.25 11.25c-.414 0-.75-.336-.75-.75 0-.551-.448-1-1-1s-1 .449-1 1c0 .414-.336.75-.75.75s-.75-.336-.75-.75c0-1.378 1.121-2.5 2.5-2.5s2.5 1.122 2.5 2.5c0 .414-.336.75-.75.75z"/>
                                    <path d="m12 18c-2.184 0-4.236-.85-5.78-2.395-.293-.293-.293-.768 0-1.061s.768-.293 1.061 0c1.26 1.262 2.936 1.956 4.719 1.956s3.459-.694 4.72-1.955c.293-.293.768-.293 1.061 0s.293.768 0 1.061c-1.545 1.544-3.597 2.394-5.781 2.394z"/>
                                    <path d="m20 8.036c-.15 0-.3-.045-.43-.135-.595-.417-3.57-2.594-3.57-4.601 0-1.288 1.097-2.335 2.445-2.335.579 0 1.125.195 1.555.531.43-.336.976-.531 1.555-.531 1.348-.001 2.445 1.047 2.445 2.335 0 2.007-2.975 4.185-3.57 4.601-.13.09-.28.135-.43.135zm-1.555-5.572c-.521 0-.945.375-.945.835 0 .8 1.367 2.177 2.5 3.055 1.153-.89 2.5-2.246 2.5-3.055 0-.46-.424-.835-.945-.835-.386 0-.729.203-.874.517-.246.53-1.115.53-1.361 0-.146-.314-.489-.517-.875-.517z"/>
                                    <path d="m4 24.036c-.15 0-.3-.045-.43-.135-.595-.417-3.57-2.594-3.57-4.601 0-1.288 1.097-2.335 2.445-2.335.579 0 1.125.195 1.555.531.43-.336.976-.531 1.555-.531 1.348-.001 2.445 1.047 2.445 2.335 0 2.007-2.975 4.185-3.57 4.601-.13.09-.28.135-.43.135zm-1.555-5.572c-.521 0-.945.375-.945.835 0 .8 1.367 2.177 2.5 3.055 1.153-.89 2.5-2.246 2.5-3.055 0-.46-.424-.835-.945-.835-.386 0-.729.203-.874.517-.246.53-1.115.53-1.361 0-.146-.314-.489-.517-.875-.517z"/>
                                </svg>
                            </inter_div>
                        </inter_div>
                        <inter_div class="InterHelper_first_assistent_message">
                            <inter_div>
                                Оставьте свое сообщение, мы ответим в ближайшее время.
                            </inter_div>
                        </inter_div>
                    </inter_div>
                    <inter_div id="InterHelper_window_foot">
                        <inter_div id="InterHelper_message_input" type="text" contenteditable="true" aria-multiline="true" role="textbox"></inter_div>
                        <div id="InterHelper_placeholder">Введите сообщение</div>
                        <inter_div id="InterHelper_msg_loader"></inter_div>
                        <svg version="1.1" id="InterHelper_menu_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="408px" height="408px" viewBox="0 0 408 408" 
                            style="enable-background:new 0 0 408 408;" xml:space="preserve">
                            <g><g id="more-vert">
                                    <path d="M204,102c28.05,0,51-22.95,51-51S232.05,0,204,0s-51,22.95-51,51S175.95,102,204,102z M204,153c-28.05,0-51,22.95-51,51s22.95,51,51,51s51-22.95,51-51S232.05,153,204,153z M204,306c-28.05,0-51,22.95-51,51s22.95,51,51,51s51-22.95,51-51S232.05,306,204,306z"/>
                            </g></g>
                        </svg>
                        <svg id="InterHelper_msg_send_btn" enable-background="new 0 0 512.005 512.005" height="512" viewBox="0 0 512.005 512.005" width="512" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path d="m511.658 51.675c2.496-11.619-8.895-21.416-20.007-17.176l-482 184c-5.801 2.215-9.638 7.775-9.65 13.984-.012 6.21 3.803 11.785 9.596 14.022l135.403 52.295v164.713c0 6.948 4.771 12.986 11.531 14.593 6.715 1.597 13.717-1.598 16.865-7.843l56.001-111.128 136.664 101.423c8.313 6.17 20.262 2.246 23.287-7.669 127.599-418.357 122.083-400.163 122.31-401.214zm-118.981 52.718-234.803 167.219-101.028-39.018zm-217.677 191.852 204.668-145.757c-176.114 185.79-166.916 176.011-167.684 177.045-1.141 1.535 1.985-4.448-36.984 72.882zm191.858 127.546-120.296-89.276 217.511-229.462z"/>
                            </g>
                        </svg>
                    </inter_div>
                </inter_div>
            `);
            InterHelperMessageWindow = $(`
                <inter_div id="InterHelper_msg_preview">
                    <inter_span id="InterHelper_close_msg_preview_btn">
                        <inter_span class="InterHelper_close_msg_preview_btn_span"></inter_span>
                        <inter_span class="InterHelper_close_msg_preview_btn_span"></inter_span>
                    </inter_span>
                    <inter_div class='InterHelper_msg_preview_info'>
                        <inter_span id="InterHelper_msg_preview_photo"></inter_span>
                        <inter_div id="InterHelper_msg_preview_sender_info">
                            <inter_p id="InterHelper_msg_preview_name"></inter_p>
                            <inter_p id="InterHelper_msg_preview_departament"></inter_p>
                        </inter_div>
                    </inter_div>
                    <inter_p id="InterHelper_msg_preview_time"></inter_p> 
                    <inter_div id="InterHelper_msg_preview_message_block">
                        <inter_p id="InterHelper_msg_preview_message"></inter_p>
                        <inter_div id="InterHelper_msg_preview_adds"></inter_div>
                    </inter_div>
                </inter_div>
            `);
            $('body').append(InterHelperButton);
            $('body').append(InterHelperForm);
            $('body').append(InterHelperMessageWindow);
            $('#InterHelper_close_msg_preview_btn').on('click', () =>{ 
                $('#InterHelper_msg_preview').css('right', '-650px'); 
            });
            $('#InterHelper_msg_preview').on('click', () => { 
                if(event.target.id != 'InterHelper_close_msg_preview_btn' && !event.target.classList.contains('InterHelper_close_msg_preview_btn_span')){
                    $('#InterHelper_msg_preview').css('right', '-650px'); 
                    if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) $('#InterHelper_window').css({'bottom': '0'});
                    else $('#InterHelper_window').css({'right': '0px', 'left': '0', 'bottom': '0'});
                }
            });
            $('#InterHelper_close_window_btn').on('click', () => {
                $('#InterHelper_window').css({'bottom': `-${parseInt($('#InterHelper_window').css('height').replace('px', '')) + 200}px`});
                $('#InterHelper_btn').css(type, px_return_length||'0px');
            });
            $('#InterHelper_btn').on('click', () => {
                if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) $('#InterHelper_window').css({'bottom': '0'});
                else $('#InterHelper_window').css({'right': '0px', 'left': '0', 'bottom': '0'});
                $('#InterHelper_btn').css(type, px_length);
            });
            if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                (function($) {
                    var defaults = {
                        color: 'red'
                    };
                    var f = {
                        init: function(options) {
                            var options = $.extend({}, defaults, options);
                            var c = this;
                            var qe = $("<div class='res' style='position: absolute;top: 0px;background: transparent;z-index: 10000000000;width: 100%;height: 53px;cursor: move;'><div class='resizers'></div><div class='resizers'></div></div>");
                            $(c).append(qe);
                            return this.each(function() {
                                var me = $(this);
                                qe.bind('mousedown', function(e) {
                                    var h = me.height();
                                    var y = screen.height - e.clientY; // точка в момент захвата
                                    var moveHandler = function(e) {
                                        var height = Math.max(400, screen.height - e.clientY + h - y);
                                        var right = Math.max(40, document.documentElement.clientWidth - e.pageX - me.width() / 2);
                                        if(right > document.documentElement.clientWidth - me.width() - 40) right = document.documentElement.clientWidth - me.width() -40;
                                        if(height > document.documentElement.clientHeight - 40) height = document.documentElement.clientHeight - 40;
                                        localStorage['InterHelper_height'] = height;
                                        localStorage['InterHelper_right'] = right;
                                        me.css('right', right+'px')
                                        me.height(height);
                                        return false;
                                    };
                                    var upHandler = function(e) { $('html').unbind('mousemove', moveHandler).unbind('mouseup', upHandler); };
                                    $('html').bind('mousemove', moveHandler).bind('mouseup', upHandler);
                                });
                            });
                        },
                    };
                    $.fn.resizable = function(method) {
                        if(f[method]) return f[method].apply(this, Array.prototype.slice.call(arguments, 1));
                        else if(typeof method === 'object' || ! method) return f.init.apply(this, arguments);
                    };
                }) (jQuery);
                $('#InterHelper_window').resizable();
                window.addEventListener('resize',function(){
                    let right = parseInt($('#InterHelper_window').css('right'));
                    let doc_width = document.documentElement.clientWidth;
                    let doc_height = document.documentElement.clientHeight;
                    let width = $('#InterHelper_window').width();
                    let height = $('#InterHelper_window').height();
                    if(right + width + 40 > doc_width){ 
                        right = right - (right - doc_width) - width - 40;
                        $('#InterHelper_window').css('right', right + 'px');
                        localStorage['InterHelper_right'] = right;
                    }
                    if(height > doc_height - 40){ 
                        height = height - (height - doc_height) - 40;
                        if(height < 400) return;
                        $('#InterHelper_window').css('height', height + 'px');
                        localStorage['InterHelper_height'] = height;
                    }
                });                
            }
        }
        function error_message(error){ // CHAT ERROR
            if(error_trys == 1) return;
            let message_by_system = $(`
                <inter_div class='InterHelper_error_message'>
                    <inter_div>${error}</inter_div>
                </inter_div>
            `);
            message_by_system.appendTo($('#InterHelper_window_body'));
            error_trys = 1;
            scroll_helper_body();
        } 
        function success_message(status){ // CHAT SUCCESS
            let message_by_system = $(`
                <inter_div class='InterHelper_success_message'>
                    <inter_div>${status}</inter_div>
                </inter_div>
            `);
            message_by_system.appendTo($('#InterHelper_window_body'));
            error_trys = 0;
            scroll_helper_body();
        } 
        function create_offline_form(phone_status, email_status, name_status, text){ // OFFLINE FORM
            let name_input = '<input autocomplete="off" class="interHelper_name_input" type ="text" name="name"  placeholder = "Ваше имя">';
            let phone_input = '<input autocomplete="off" class="interHelper_phone_input" type ="phone" name="phone"  placeholder = "Ваш телефон">';
            let email_input = '<input autocomplete="off" class="interHelper_email_input" type ="email" name="mail"  placeholder = "Ваш email">';
            let offline_form = $(`
                <form id="InterHelper_offline_form">
                    <inter_p>${text}</inter_p>
                    ${(name_status == 'checked' ? name_input : '')}
                    ${(email_status == 'checked' ? email_input : '')}
                    ${(phone_status == 'checked' ? phone_input : '')}
                    <textarea placeholder="Введите ваше сообщение"></textarea>
                    <button id="send_offline_form" type="button">отправить</button>
                </form>
            `);
            $('#InterHelper_window_body').append(offline_form);
            $('#send_offline_form').on('click', function(e){
                e.preventDefault();
                let name =  $('.interHelper_name_input').val();
                let email =  $('.interHelper_email_input').val();
                let phone =  $('.interHelper_phone_input').val();
                let message =  $('#InterHelper_offline_form textarea').val();
                if((!phone && phone_status == 'checked') || (!name && name_status == 'checked') || (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(email)) && email_status == 'checked') || !message){
                    error_message("Поля не заполнены!");
                    return;
                }
                let today = new Date();
                let cache_time;
                if(window.localStorage.getItem('interhelper_mail_status')){
                    cache_time = new Date(window.localStorage.getItem('interhelper_mail_status'));
                    cache_time.setMinutes(cache_time.getMinutes() + 5);
                }
                if(cache_time <= today || !cache_time){
                    send_ajax(main_domain+'/engine/guest_func', {'name': name, 'email': email, 'phone':phone, 'offline_message': message, 'uid': personal_uid});
                    window.localStorage.setItem('interhelper_mail_status', today);
                    $('#InterHelper_offline_form input, #InterHelper_offline_form textarea, #send_offline_form').prop('disabled', true);
                    $('#send_offline_form').html('Спасибо!');
                    $('#InterHelper_offline_form').addClass('InterHelper_sended_offline_form');
                    if($('.sended_offline_form').length == 0) success_message('Теперь ваши сообщения не потеряются. Ответ операторов придет вам на почту.');
                } else error_message("Вы уже отправили форму обратной связи ! Ждите ответ на электронную почту !");
            });
            scroll_helper_body();
        } 
        function guest_send_message(data){ // GUEST MESSAGE CONSTRUCTOR
            loader("get");
            let message;
            let p_message_flag = false;
            let file;
            let time = data.time;
            if($('.InterHelper_date_' + time.split(' ')[0]).length == 0) $('#InterHelper_window_body').append($(`
                <inter_div class='InterHelper_date_time InterHelper_date_${time.split(' ')[0]}'>
                    ${time.split(' ')[0].split('-').reverse().join('.')}
                </inter_div>
            `));
            if(p_message != 'guest') p_message_flag = true; 
            p_message = 'guest';
            if(data.message_adds) file = JSON.parse(data.message_adds)[0];
            let message_text = data.message;
            for(folder in emojis){
                if(message_text.indexOf(folder) == -1) continue;
                for(emoji in emojis[folder]){
                    if(message_text.indexOf(emoji) == -1) continue;
                    message_text = message_text.replaceAll(emoji, `<img class='InterHelper_emoji' src='${main_domain + '/emojis/' + folder + '/' + emojis[folder][emoji]}' alt='${emoji}' />`);
                }
            }
            message = $(`
                <inter_div class="InterHelper_guest_message">
                    <inter_div>
                        <inter_div class="InterHelper_guest_message_time">${data.time.split(' ')[1].split(':').slice(0, 2).join(':')}</inter_div>
                        <inter_div style='display: ${(p_message_flag ? 'flex' : 'none')} !important;' class="InterHelper_guest_message_photo">Я</inter_div>
                        <inter_span style='display: ${(p_message_flag ? 'flex' : 'none')} !important;' class="InterHelper_guest_photo_before"></inter_span>
                        ${
                            !data.message_adds ? "<inter_p>"+message_text+"</inter_p>" :
                            (
                                regexp.indexOf('.'+file.split('.').slice(-1)[0]) != -1 ? 
                                "<a class = 'InterHelper_download_link' href='"+files_path+file+"' download>Скачать "+file.split('.').slice(-1)[0]+"</a>" :
                                "<img src='"+files_path + file+"' class='InterHelper_msg_photo'/>"
                            )
                        }
                    </inter_div>
                </inter_div>
            `);
            message.appendTo($('#InterHelper_window_body'));
            scroll_helper_body();
        } 
        function guest_send_offline_form(data){ // GUEST SEND OFFLINE FORM
            p_message = 'offline_form'; 
            let name_input = `<input autocomplete="off" disabled type ="text" name="name" value="${data.form_name}" disabled placeholder = "Ваше имя">`;
            let phone_input = `<input autocomplete="off" disabled type ="phone" value="${data.form_phone}"  name="phone"  placeholder = "Ваш телефон">`;
            let email_input = `<input autocomplete="off" disabled type ="email" value="${data.form_email}"  name="mail"  placeholder = "Ваш email">`;
            let time = data.time;
            if($('.InterHelper_date_' + time.split(' ')[0]).length == 0) $('#InterHelper_window_body').append($(`
                <inter_div class='InterHelper_date_time InterHelper_date_${time.split(' ')[0]}'>
                    ${time.split(' ')[0].split('-').reverse().join('.')}
                </inter_div>
            `));
            let offline_form = $(`
                <form class="sended_offline_form">
                    <inter_p style="text-align:center;">
                        ${data.time.split(' ')[1].split(':').slice(0, 2).join(':')}
                        <br/>
                        Вы отправили оффлайн форму
                    </inter_p>
                    ${(data.form_name ? name_input : '')}
                    ${(data.form_email ? email_input : '')}
                    ${(data.form_phone ? phone_input : '')}
                    <textarea disabled placeholder="Введите ваше сообщение">${data.form_message}</textarea>
                </form>
                $('#InterHelper_window_body').append(offline_form);
            `);
            $('#InterHelper_window_body').append(offline_form);
            scroll_helper_body();
        }
        function assistent_send_message(message, type, files_path){ // ASSISTENT MESSAGE CONSTRUCTOR
            let ava_url = '/assistent_photos/';
            let p_message_flag = false;
            if(p_message != message.sender) p_message_flag = true; 
            p_message = (message.sender == 'notification' ? message.notification_text : message.sender);
            if(message.sender == 'notification'){
                message.message_adds = message.notification_adds;
                ava_url = '/notifications_photos/notification_photos/';
                message.message = message.notification_text;
                message.departament = message.notification_departament;
                message.user = message.notification_name;
                message.photo = message.notification_photo;
                p_message = null;
            } 
            if(typeof files_path === 'object'){
                ava_url = files_path['ava'];
                files_path = files_path['adds'];
            }
            let photorow = return_files(files_path, message.message_adds);
            if($('.InterHelper_date_' + message.time.split(' ')[0]).length == 0) $('#InterHelper_window_body').append($(`
                <inter_div class='InterHelper_date_time InterHelper_date_${message.time.split(' ')[0]}'>
                    ${message.time.split(' ')[0].split('-').reverse().join('.')}
                </inter_div>
            `));
            let message_text = message.message;
            for(folder in emojis){
                if(!message_text) continue;
                if(message_text.indexOf(folder) == -1) continue;
                for(emoji in emojis[folder]){
                    if(message_text.indexOf(emoji) == -1) continue;
                    message_text = message_text.replaceAll(emoji, `<img class='InterHelper_emoji' src='${main_domain + '/emojis/' + folder + '/' + emojis[folder][emoji]}' alt='${emoji}' />`);
                }
            }
            let message_by_us = $(`
                <inter_p class="InterHelper_assistent_message">
                    <inter_div>
                        <inter_span class="InterHelper_assistent_photo" style='background-image:url(${main_domain + ava_url + message.photo});display: ${(p_message_flag ? 'flex' : 'none')} !important;'></inter_span>
                        <inter_span style='display: ${(p_message_flag ? 'flex' : 'none')} !important;' class="InterHelper_assistent_photo_before"></inter_span>
                        <inter_p class="InterHelper_assistent_message_info">${message.user} <inter_span style="font-weight:bold;margin-left:10px;">${message.time.split(' ')[1].split(':').slice(0, 2).join(':')}</inter_span></inter_p>
                        <inter_p class="InterHelper_assistent_message_message">${message_text}</inter_p>
                        ${photorow}
                    </inter_div>
                </inter_p>
            `);
            $('#InterHelper_window_body').append(message_by_us);
            scroll_helper_body();
            if(type != "new") return;
            if(graphic_status){
                $('#InterHelper_msg_preview_time').text(message.time.split(' ')[1].split(':').slice(0, 2).join(':'));
                $('#InterHelper_msg_preview_message').html(message_text);
                $('#InterHelper_msg_preview_adds').html(photorow);
                $('#InterHelper_msg_preview_name').html(message.user);
                $('#InterHelper_msg_preview_departament').html(message.departament);
                $('#InterHelper_msg_preview_photo').css({
                    'background-image': 'url('+main_domain+ava_url+message.photo+')',
                });
                if(parseInt($('#InterHelper_window').css('bottom')) < 0) $('#InterHelper_msg_preview').css('right', '17px');
            }
            if(audio_status) audio.play();
        } 
        function create_emoji_window(emojis){
            let emoji_block = `<inter_div class='InterHelper_emoji_block'>`;
            for(folder in emojis){
                folder_block = `
                    <inter_div class='InterHelper_emoji_folder'>
                        <inter_p class='InterHelper_emoji_folder_name'>${folder}</inter_p>
                        <inter_div class='InterHelper_emojis InterHelper_emojis_close'>
                `;
                for(emoji in emojis[folder]){
                    let emoji_block = `<inter_span class='InterHelper_folder_emoji' data-folder='${folder}' data-emoji='${emoji}' title='${emoji}' style="background-image:url(${main_domain}/emojis/${folder}/${emojis[folder][emoji]})"></inter_span>`;
                    folder_block += emoji_block;
                }
                folder_block += `</inter_div></inter_div>`;
                emoji_block += folder_block;
            }
            emoji_block += `</inter_div>`;
            $('#InterHelper_window').append($(emoji_block));
            $('.InterHelper_emoji_block').css('bottom', ($('#InterHelper_message_input').height() + 10) + 'px')
            $('.InterHelper_emoji_folder_name').on('click', () => {
                let container = $(event.target).siblings('.InterHelper_emojis');
                if(container.hasClass('InterHelper_emojis_close')){
                    container.removeClass('InterHelper_emojis_close');
                    container.addClass('InterHelper_emojis_open');
                } else {
                    container.removeClass('InterHelper_emojis_open');
                    container.addClass('InterHelper_emojis_close');
                }
            });
            $('#InterHelper_emoji_btn').on('click', () => {
                if($('.InterHelper_emoji_block').css('display') == 'none')  $('.InterHelper_emoji_block').css('display', 'flex');
                else $('.InterHelper_emoji_block').css('display', 'none');
            });
            $('.InterHelper_folder_emoji').on('click', function(e) {
                let folder = $(this).data('folder');
                let emoji = $(this).data('emoji');
                let emoji_file = emojis[folder][emoji];
                $('#InterHelper_message_input').html($('#InterHelper_message_input').html()+`<img class="InterHelper_emoji" src="https://interhelper.ru/emojis/${folder}/${emoji_file}" alt="${emoji}" />`);
            }); 
        }
        function return_files(files_path, adds){ // конвертация файлов
            if(!adds || adds == 'null') return '';
            let result = '<inter_div class="InterHelper_files_block">';
            adds = JSON.parse(adds);
            $.each(adds, (index, value) => {
                if(regexp.indexOf('.'+value.split('.').splice(-1)[0]) == -1) result += '<img src="'+files_path+value+'" class="InterHelper_msg_photo" />';
            });
            $.each(adds, (index, value) => {
                if(regexp.indexOf('.'+value.split('.').splice(-1)[0]) != -1) result += '<a class="InterHelper_download_link" href="'+files_path+value+'" download >Скачать '+value.split('.').splice(-1)[0]+'</a>';
             });
             result += "</inter_div>";
             return result;
        }
        function escapeHtml(text) { return $("<div/>").html(text).text(); } 
        function send_ajax(path, array){ // AJAX
            $.ajax({
                type: 'POST',
                url: path,
                data: array,
                success: function(data) {
                    data = JSON.parse(data);
                    if(data.errors.length > 0) error_message(data.errors.reduce((element, index) => { return element + '/' + index; }));
                    if(Object.keys(data.success).length > 0){
                        if(data.success.hasOwnProperty('response')) alert(data.success.response);
                        if(data.success.hasOwnProperty('reload')) location.reload();
                        if(data.success.hasOwnProperty('emojis')) emojis = JSON.parse(data.success.emojis);
                    }
                },
                error: function() { console.log('Ошибка в ajax запросе! '); }
            });
        }
        function formData_send_ajax(path, formData){ // FORM DATA AJAX
            $.ajax({
                type: 'POST',
                url: path,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    loader("get");
                    data = JSON.parse(data);
                    if(data.errors.length > 0) error_message(data.errors.reduce((element, index) => { return element + '/' + index; }));
                    if(Object.keys(data.success).length > 0){
                        if(data.success.hasOwnProperty('response')) alert(data.success.response);
                        if(data.success.hasOwnProperty('reload')) location.reload();
                    }
                },
                error: function() { console.log('Ошибка в ajax запросе! '); }
            });
        }
        function scroll_helper_body(){ // SCROLL CHAT
            $('#InterHelper_window_body').scrollTop($('#InterHelper_window_body').prop('scrollHeight'));
            setTimeout("$('#InterHelper_window_body').scrollTop(1000000)", 1000);
        }
        function loader(status){ // LOADER
            $('#btns_of_InterHelper_window').css('display', (status == "send" ? 'none' : 'block'));
            $('#InterHelper_msg_send_btn').css('display', (status == "send" ? 'none' : 'block'));
            $('#interhelper_opt_btn').css('display', (status == "send" ? 'none' : 'block'));
            $('#InterHelper_msg_loader').css('display', (status == "send" ? 'block' : 'none'));
        }
        function change_corner_status(color, name, font){ // CHAT STATUS
            if(!chat_status) return;
            $('.InterHelper_status').css('background', color); 
            if(!servicing_status) $('#InterHelper_window_head .InterHelper_sys_name').text(name); 
            $('#InterHelper_btn .InterHelper_sys_name').text(name); 
            if(!personal_sizes) $('.InterHelper_sys_name').css('font-size', font);
        }
        function phoneNumberCompare(a, b) {
            if(!a || !b) return false;
            a = a.replace(/[^\d]/g, "").replace(/^.*(\d{10})$/, "$1");
            b = b.replace(/[^\d]/g, "").replace(/^.*(\d{10})$/, "$1");
            return a == b;
        }  
        function swap_object(object_link, object_type, swap, swap_id, cache, swap_changename){
            if($(object_link).length ==0) return;
            let swap_to = escapeHtml(swap.swap_to);
            let swap_from = escapeHtml(swap.swap_from);
            let swap_cache = swap.swap_cache;
            if(swap_cache && !cache){ // сохраняем в кэш
                let swap_cache_mas = [];
                if(!localStorage["swap_ids"]) swap_cache_mas.push(swap_id);
                else { 
                    swap_cache_mas = JSON.parse(localStorage["swap_ids"]);
                    if(swap_cache_mas.indexOf(swap_id) == -1) swap_cache_mas.push(swap_id);
                }
                localStorage["swap_ids"] = JSON.stringify(swap_cache_mas);
            }
            // делаем замену
            if(object_type == 'phone'){
                for(link in object_link){
                    if(isNaN(link)) continue;
                    if(phoneNumberCompare(swap_from, $(object_link[link]).attr('href'))){ 
                        $(object_link[link]).attr('href', 'tel:+7'+swap_to.replace(/[^\d]/g, "").replace(/^.*(\d{10})$/, "$1"));
                        if(swap_changename) $(object_link[link]).html(swap_to);
                    }
                }
            } else if(object_type == 'mail') {
                for(link in object_link){
                    if(isNaN(link)) continue;
                    if($(object_link[link]).attr('href').replace('mailto:', '') == swap_from){ 
                        $(object_link[link]).attr('href', 'mailto:'+swap_to);
                        if(swap_changename) $(object_link[link]).html(swap_to);
                    }
                }
            } else if(object_type == 'img') $(object_link).attr('src', swap_to);
            else if(object_type == 'link') $(object_link).attr('href', swap_to);
            else if(object_type != 'img' && object_type != 'mail' && object_type != 'phone' && swap_changename && object_type != 'statistic') $(object_link).html(swap_to);
            // отслеживание utm
            if(Object.keys(swap.swap_utmparts||{}).length > 0){
                let link = location.href;
                link = decodeURI(link);
                for(part in swap.swap_utmparts){
                    let utm_part = swap.swap_utmparts[part];
                    let link_filter = escapeHtml(utm_part['utm_part_name']);
                    let filter_pos = link.indexOf(link_filter+'=');
                    if(filter_pos == -1) continue;
                    let key = link.split(link_filter+'=')[1];
                    if(key.indexOf('&') != -1) key = key.split('&')[0];
                    if(object_link) $(object_link).on('click', () => {
                        socket.emit('conditions_complete', {"swap_id": swap_id, "type": cache ? "cache_clicks" : "clicks", "autoUTM": {"utm": key, "part": part}});
                    });
                    socket.emit('conditions_complete', {"swap_id": swap_id, "type": cache ? "cache_shown" : "shown", "autoUTM": {"utm": key, "part": part}});
                }
            }
            if(object_link) $(object_link).on('click', () => {
                socket.emit('conditions_complete', {"swap_id": swap_id, "type": cache ? "cache_clicks" : "clicks"});
            });
            socket.emit('conditions_complete', {"swap_id": swap_id, "type": cache ? "cache_shown" : "shown"});
        } 
        function htmldecoder(str){
            if(typeof str != 'string') return str;
            return $("<div/>").html(str).text();
        }
    });
}