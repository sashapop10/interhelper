<?php
	session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
    if (!isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
    $info = check_user($connection);
    if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
    if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
    $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
    $sql = "SELECT buttlecry FROM assistents WHERE id = '$personal_id'";
    $buttlecry = attach_sql($connection, $sql, 'row')[0];
    mysqli_close($connection);
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
    $assistents_path = VARIABLES["photos"]["assistent_profile_photo"]["upload_path"];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/consultant_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php  navigation('hub', $info); ?>
    <div id="app">
        <div :style="{'bottom': message_panel ? '25px' : '-800px'}" class="message_panel v-cloak-off" v-cloak>
            <span class="break_chat chat_header_btn" style="position:absolute;top:0px;right:-45px;min-height:30px;width:30px;background:#252525;" v-on:click="message_panel = !message_panel">
                <span style="width:18px;"></span>
                <span style="width:18px;"></span>
            </span>
            <div id="chat_footer" style="border-bottom-right-radius:10px;border-bottom-left-radius:10px;" class="card-body message_panel_input">
                <div type="text" :contenteditable="load ? 'true': 'false'" aria-multiline="true" role="textbox"  class="chat_block_textarea form-control"></div>
                <div id="placeholder">
                    {{
                        load ? 
                            (
                                js_mode ? "Введите ваш скрипт, не используйте тег script" :
                                (
                                    style_mode ? "Введите вашу стрктуру (html/css/js)"
                                    : "Введите ваше сообщение"
                                ) 
                            )  : 'Идёт отправка вашего сообщения'
                    }}
                </div>
                <div class="btns_panel">
                    <span title="Отправить JavaScript код" v-if="load" @click="js_mode = !js_mode; style_mode = false;" :style="{'background-color': js_mode ? 'green' : 'tomato'}" class="btns_panel_btn send_js"></span>
                    <span title="Отправить сообщение содержащие DOM теги" v-if="load" @click="style_mode = !style_mode; js_mode = false;" :style="{'background-color': style_mode ? 'green' : 'tomato'}" class="btns_panel_btn send_css"></span>
                    <span title="Добавить emoji" v-if="load" @click="smiles_mode = !smiles_mode;" class="btns_panel_btn send_smile"></span>
                    <span title="Быстрые команды" v-if="load" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                    <input @change="handleChange()" multiple name='addphoto' class='add_photo'  id='add_photo' type='file' style='display:none;' />
                    <label title="Приложить фотографию" class="btns_panel_btn add_file" v-if="load" for="add_photo" ></label>
                    <button title="Отправить сообщение" class="btns_panel_btn Send_message_button" v-if="load" style="cursor:pointer;" v-on:click = "send" type="button"></button> 
                    <span v-if="!load" class="msg_loader"></span>
                </div>   
            </div>
            <div v-if="smiles_mode" class="smiles_container">
                <div class="smiles_folder" v-for="(folder, folder_name) in emojis">
                    <h2 onclick="smiles_folder()" class="smiles_name">{{folder_name}}</h2>
                    <div class="smiles smiles_close">
                        <div class="smile" v-for="(smile, smile_key) in folder">
                            <span @click="select_smile(smile_key, folder_name, smile)" class="smile_photo" :style="'background-image:url(/emojis/'+folder_name+'/'+smile"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="upload_files_preview">
                <div class="preview_img_block" v-for="(file, index) in files">
                    <span class="preview_remove" @click="removeFile(index)"><span></span><span></span></span>
                    <span class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                    <p :title="file.name" style="background:#0ae;position:absolute;bottom:7px;left:0;color:#000;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.name}}</p>
                </div>
            </div>
        </div>
        <?php team_msg_notification_body($file_path); ?>
        <div style="box-shadow:0 0 20px rgba(0,0,0,0.4);" class="incolumn incolumn_left v-cloak-off" v-cloak>
            <h2>Действия с посетителями</h2>
            <div style="display: flex;justify-content:center;">
                <div style='display:flex;flex-direction:column;'>
                    <span v-if="choosen_users_load" class="msg_loader"></span>
                    <span  v-if="!choose_mode && !choosen_users_load" @click="choose_mode = !choose_mode" class="choose_guests" >Выбрать посетителей</span>
                    <span style="background:rgba(0, 0, 0, 0.6); color:#fff; margin-top:10px;text-align:center;" v-if="choose_mode && !choosen_users_load" @click="choose('offline', 'all')" class="choose_guests" >Выбрать <span class="offline">offline</span>  посетителей</span>
                    <span style="background:rgba(0, 0, 0, 0.6); color:#fff;margin-top:10px;text-align:center;" v-if="choose_mode && !choosen_users_load" @click="choose('online', 'all')" class="choose_guests" >Выбрать <span class="online">online</span>  посетителей</span>
                    <span style="background:rgba(0, 0, 0, 0.6); color:#fff;margin-top:10px;text-align:center;" v-if="choose_mode && !choosen_users_load" @click="choose('online', 'unconsulated')" class="choose_guests" >Выбрать не обслуженных <span class="online">online</span>  посетителей</span>
                    <span style="background:rgba(0, 0, 0, 0.6); color:#fff;margin-top:10px;text-align:center;" v-if="choose_mode && !choosen_users_load" @click="choose('offline', 'unconsulated')" class="choose_guests" >Выбрать не обслуженных <span class="offline">offline</span>  посетителей</span>
                    <span style="background:tomato; color:#fff;margin-top:10px;text-align:center;" v-if="choose_mode && !choosen_users_load" @click="choose_mode = !choose_mode" class="choose_guests" >Отменить выбор</span>
                    <div style="display:inline-flex; align-items:center;margin-top:20px;">
                        <h2 class="header2" style="margin:0;margin-right:10px;">Скрыть фильтры</h2>
                        <span style="background:rgba(255,255,255,0.1);" @click="filter_status('filters')" class="check_btn"><span :class="[{'checked_btn_span': cache_variables['filters']['status']}, {'unchecked_btn_span': !cache_variables['filters']['status']}]"></span></span>
                    </div>
                </div>
                <div style="display: flex;flex-direction:column;" v-if="choose_mode && !choosen_users_load">
                    <span v-if="!choosen_users_load" data-tooltip_ToBottomRight="Удалить выделенных" @click="choosen_users_func('hide')" class="opt_btn delete_room" >
                        <svg version="1.1" id="rubbish_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 408.483 408.483" style="enable-background:new 0 0 408.483 408.483;fill:#000;" xml:space="preserve"><g><g><path d="M87.748,388.784c0.461,11.01,9.521,19.699,20.539,19.699h191.911c11.018,0,20.078-8.689,20.539-19.699l13.705-289.316H74.043L87.748,388.784z M247.655,171.329c0-4.61,3.738-8.349,8.35-8.349h13.355c4.609,0,8.35,3.738,8.35,8.349v165.293c0,4.611-3.738,8.349-8.35,8.349h-13.355c-4.61,0-8.35-3.736-8.35-8.349V171.329z M189.216,171.329c0-4.61,3.738-8.349,8.349-8.349h13.355c4.609,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.737,8.349-8.349,8.349h-13.355c-4.61,0-8.349-3.736-8.349-8.349V171.329L189.216,171.329z M130.775,171.329c0-4.61,3.738-8.349,8.349-8.349h13.356c4.61,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.738,8.349-8.349,8.349h-13.356c-4.61,0-8.349-3.736-8.349-8.349V171.329z"/><path d="M343.567,21.043h-88.535V4.305c0-2.377-1.927-4.305-4.305-4.305h-92.971c-2.377,0-4.304,1.928-4.304,4.305v16.737H64.916c-7.125,0-12.9,5.776-12.9,12.901V74.47h304.451V33.944C356.467,26.819,350.692,21.043,343.567,21.043z"/></g></g></svg>
                    </span>
                    <span v-if="!choosen_users_load" data-tooltip_ToBottomRight="Заблокировать выделенных" @click="choosen_users_func('ban')" class="opt_btn ban_room" >
                        <svg version="1.1" id="lock_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 401.998 401.998" style="enable-background:new 0 0 401.998 401.998;fill:#000;" xml:space="preserve"><g><path d="M357.45,190.721c-5.331-5.33-11.8-7.993-19.417-7.993h-9.131v-54.821c0-35.022-12.559-65.093-37.685-90.218C266.093,12.563,236.025,0,200.998,0c-35.026,0-65.1,12.563-90.222,37.688C85.65,62.814,73.091,92.884,73.091,127.907v54.821h-9.135c-7.611,0-14.084,2.663-19.414,7.993c-5.33,5.326-7.994,11.799-7.994,19.417V374.59c0,7.611,2.665,14.086,7.994,19.417c5.33,5.325,11.803,7.991,19.414,7.991H338.04c7.617,0,14.085-2.663,19.417-7.991c5.325-5.331,7.994-11.806,7.994-19.417V210.135C365.455,202.523,362.782,196.051,357.45,190.721z M274.087,182.728H127.909v-54.821c0-20.175,7.139-37.402,21.414-51.675c14.277-14.275,31.501-21.411,51.678-21.411c20.179,0,37.399,7.135,51.677,21.411c14.271,14.272,21.409,31.5,21.409,51.675V182.728z"/></g></svg>
                    </span>
                    <span v-if="!choosen_users_load" data-tooltip_ToBottomRight="Восстановить удалённых среди выделенных" @click="choosen_users_func('restore')" class="opt_btn ban_room" >
                        <svg style="fill:#000;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.019 512.019" style="enable-background:new 0 0 512.019 512.019;" xml:space="preserve"><g><g><path d="M463.489,287.993h-64.32c-8.352,0-14.784,6.56-15.744,14.848c-8.48,73.152-78.848,127.648-157.248,109.696c-46.208-10.592-83.744-48.064-94.4-94.24c-19.296-83.68,44.064-158.304,124.512-158.304v48c0,6.464,3.904,12.32,9.888,14.784s12.832,1.088,17.44-3.488l96-96c6.24-6.24,6.24-16.384,0-22.624l-96-96c-4.608-4.544-11.456-5.92-17.44-3.456s-9.888,8.32-9.888,14.784v48c-124.608,0-225.76,102.24-223.968,227.264c1.696,118.848,101.728,218.944,220.576,220.736c119.392,1.792,218.08-90.368,226.784-207.168C480.353,295.705,472.641,287.993,463.489,287.993z"/></g></g></svg>
                    </span>
                    <span @click="message_panel = !message_panel" v-if="!choosen_users_load" data-tooltip_ToBottomRight="Отправить сообщение выделенным" class="opt_btn send_message">
                        <svg version="1.1" id="send_msg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;fill:#000;" xml:space="preserve"><g><g><path d="M467,61H45c-6.927,0-13.412,1.703-19.279,4.51L255,294.789l51.389-49.387c0,0,0.004-0.005,0.005-0.007c0.001-0.002,0.005-0.004,0.005-0.004L486.286,65.514C480.418,62.705,473.929,61,467,61z"/></g></g><g><g><path d="M507.496,86.728L338.213,256.002L507.49,425.279c2.807-5.867,4.51-12.352,4.51-19.279V106C512,99.077,510.301,92.593,507.496,86.728z"/></g></g><g><g><path d="M4.51,86.721C1.703,92.588,0,99.073,0,106v300c0,6.923,1.701,13.409,4.506,19.274L173.789,256L4.51,86.721z"/></g></g><g><g><path d="M317.002,277.213l-51.396,49.393c-2.93,2.93-6.768,4.395-10.605,4.395s-7.676-1.465-10.605-4.395L195,277.211L25.714,446.486C31.582,449.295,38.071,451,45,451h422c6.927,0,13.412-1.703,19.279-4.51L317.002,277.213z"/></g></g></svg>
                    </span>
                    <span @click="choosen_users_func('dialog_start')" v-if="!choosen_users_load" data-tooltip_ToBottomRight="Начать чат" class="opt_btn send_message">
                        <svg style="fill:#000;" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M133,440a35.37,35.37,0,0,1-17.5-4.67c-12-6.8-19.46-20-19.46-34.33V111c0-14.37,7.46-27.53,19.46-34.33a35.13,35.13,0,0,1,35.77.45L399.12,225.48a36,36,0,0,1,0,61L151.23,434.88A35.5,35.5,0,0,1,133,440Z"/></svg>
                    </span>
                    <span @click="choosen_users_func('dialog_stop')" v-if="!choosen_users_load" data-tooltip_ToBottomRight="Завершить чат" class="opt_btn send_message">
                        <svg style="fill:#000;" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 365 365" style="enable-background:new 0 0 365 365;" xml:space="preserve"><g><rect x="74.5" width="73" height="365"/><rect x="217.5" width="73" height="365"/></g></svg>
                    </span>
                </div>
            </div>
            <div class="choose_guests_btn choose_guests_btn_left unactive_choose_guests_btn" onclick='control("choose_guests_btn", ".incolumn", {"top": -$(".incolumn").height() - 20}, {"top": "0"})'><span></span></div>
        </div>
        <div style="box-shadow:0 0 20px rgba(0,0,0,0.4);" class="chat_statistic v-cloak-off" v-cloak>
            <h3>Онлайн: <span style="color:lightgreen;" class="online_guest_count">{{get_count('status', 'online')}}</span></h3>
            <h3>Оффлайн: <span style="color:tomato;" class="offline_guest_count">{{get_count('status', 'offline')}}</span></h3>
            <h3>Показано: <span style="color:#0ae;">{{shown_user_counter}}</span></h3>
            <h3>Обслужено посетителей: <span style="color:#0ae;">{{get_count('served_list', null)}}</span></h3>
            <h3>Обслуживаются посетители: <span style="color:#0ae;">{{get_count('serving_list', null)}}</span></h3>
            <h3>Пришло с рекламы: <span style="color:#0ae;">{{get_count('advertisement', null)}}</span></h3>
            <h3>Посетивших за 24 часа: <span style="color:#0ae;">{{get_count('time', null)}}</span></h3>
            <div class="chat_statistic_btn choose_guests_btn_left unactive_chat_statistic_btn" onclick='control("chat_statistic_btn", ".chat_statistic", {"top": -$(".chat_statistic").height() - 20}, {"top": "0"})'><span></span></div>
        </div>
        <div class="app_row" style="margin-top:0px;z-index:1;">
            <?php create_book($buttlecry); ?>
            <div class="Online-List-User2 v-cloak-off" id="visitors_wow" v-if='searchmas.hasOwnProperty("rooms")' style='padding-top:25px;' :style="{'width': cache_variables['filters']['status'] ? 'calc(100% - 350px)' : '100%'}" v-cloak>
                <p v-if="Object.keys(searchmas['rooms']||{}).length == 0" style="font-size:25px;height:100%;width:100%;display:flex;align-items:center;justify-content:center;">Список посетителей пуст</p>
                <form 
                    class="OnlineUser wow bounceInUp" 
                    v-for="(elem, index) in sort_mas(searchmas['rooms'])"  
                    :style="{'border-color': choosen_users.hasOwnProperty(index) ? '#f90' : elem.photo.color}" 
                    :class = "'uid' + index.split('!@!@2@!@!')[1]"
                >
                    <!-- фото -->
                    <span v-if="!elem.crm" class="OnlineUser-image2" style="background-color:#252525;"  :style='"background-image:url(/visitors_photos/"+(elem.photo.img)+");background-size:80%;background-color:"+elem.photo.color+";"'></span>
                    <span v-else class="OnlineUser-image2" style="background-color:#252525;"  :style='{backgroundImage: "url(/crm_files/"+crm_items[elem.crm][index].helper_photo+")"}'></span>
                    <!--устройство-->
                    <span data-tooltip="Компьютер" :style="'background-color: '+elem.photo.color+';'" class="visitor_device" v-if="elem.info.device == 'desktop'">
                        <svg height="512" viewBox="0 0 56 56" width="512" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="016---PC-and-Monitor" fill="rgb(0,0,0)" fill-rule="nonzero"><circle id="Oval" cx="50" cy="23" r="1"/><path id="Shape" d="m31 54h-20c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h20c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"/><path id="Shape" d="m27 46h12c1.6568542 0 3-1.3431458 3-3h-42c0 1.6568542 1.34314575 3 3 3z"/><path id="Rectangle-path" d="m16 48h10v4h-10z"/><circle id="Oval" cx="50" cy="15" r="1"/><path id="Shape" d="m42 17c0-1.6568542-1.3431458-3-3-3h-36c-1.65685425 0-3 1.3431458-3 3v24h42zm-39.71 3.29 4-4c.25365857-.2536586.62337399-.3527235.96987804-.259878.34650405.0928454.61715452.3634959.71.71.09284548.346504-.00621947.7162194-.25987804.969878l-4 4c-.1877666.1893127-.44336246.2957983-.71.2957983s-.5222334-.1064856-.71-.2957983c-.18931265-.1877666-.29579832-.4433625-.29579832-.71s.10648567-.5222334.29579832-.71zm11.42-2.58-10 10c-.1877666.1893127-.44336246.2957983-.71.2957983s-.5222334-.1064856-.71-.2957983c-.18931265-.1877666-.29579832-.4433625-.29579832-.71s.10648567-.5222334.29579832-.71l10-10c.2536586-.2536586.623374-.3527235.969878-.259878.3465041.0928454.6171546.3634959.71.71.0928455.346504-.0062194.7162194-.259878.969878z"/><path id="Shape" d="m55 0h-26c-.5522847 0-1 .44771525-1 1v11h11c2.7614237 0 5 2.2385763 5 5v26c0 2.7614237-2.2385763 5-5 5h-11v4h3c.9724158.0027302 1.8831896.476617 2.4434995 1.2713829.5603099.7947658.7006442 1.8118124.3765005 2.7286171h21.18c.5522847 0 1-.4477153 1-1v-54c0-.55228475-.4477153-1-1-1zm-17 53c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm4 0c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm4 0c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm4 0c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm-3-30c0-1.6568542 1.3431458-3 3-3s3 1.3431458 3 3-1.3431458 3-3 3-3-1.3431458-3-3zm0-8c0-1.6568542 1.3431458-3 3-3s3 1.3431458 3 3-1.3431458 3-3 3-3-1.3431458-3-3zm7 38c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm0-46c0 1.1045695-.8954305 2-2 2h-20c-1.1045695 0-2-.8954305-2-2v-2c0-1.1045695.8954305-2 2-2h20c1.1045695 0 2 .8954305 2 2z"/><path id="Rectangle-path" d="m32 5h20v2h-20z"/></g></g></svg>
                    </span>
                    <span data-tooltip="Мобильное устройство" :style="'background-color: '+elem.photo.color+';'" class="visitor_device" v-if="elem.info.device == 'mobile'">
                        <svg version="1.1" id="phone" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 31.685 31.685" style="enable-background:new 0 0 31.685 31.685;" xml:space="preserve"><g><path d="M22.507,0H9.175C7.9,0,6.87,1.034,6.87,2.309v27.07c0,1.271,1.03,2.306,2.305,2.306h13.332c1.273,0,2.307-1.034,2.307-2.306V2.309C24.813,1.034,23.78,0,22.507,0z M23.085,25.672H8.599V3.895h14.486V25.672z M18.932,2.343h-6.181V1.669h6.182L18.932,2.343L18.932,2.343z M21.577,2.035c0,0.326-0.266,0.59-0.591,0.59c-0.326,0-0.591-0.265-0.591-0.59s0.265-0.59,0.591-0.59C21.312,1.444,21.577,1.709,21.577,2.035z M18.655,29.225h-5.629v-1.732h5.629V29.225z"/></g></svg>
                    </span>
                    <!-- задачи -->
                    <span data-tooltip="Задача" :onclick="'alert(\''+task.text+'\', \'log\');'" :style="'background-color: '+(new Date(task.time) > new Date ? '#f90' : 'lightgreen')+';border:3px solid '+elem.photo.color+';left:'+(task_index == 0 ? '-15' : task_index * 25)+'px;'" class="visitor_task" v-if="tasks.hasOwnProperty(index)" v-for="(task, task_index) in tasks[index]">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="task_mark" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="ic_fluent_important_24_filled" fill="#212121" fill-rule="nonzero"> <path d="M12,17.0015 C13.3813,17.0015 14.5011,18.1213 14.5011,19.5026 C14.5011,20.8839 13.3813,22.0037 12,22.0037 C10.6187,22.0037 9.49888,20.8839 9.49888,19.5026 C9.49888,18.1213 10.6187,17.0015 12,17.0015 Z M11.999,2.00244 C14.1393,2.00244 15.8744,3.7375 15.8744,5.87781 C15.8744,8.71128 14.8844,12.4318 14.339,14.2756 C14.0294,15.322 13.0657,16.0039 12.0006,16.0039 C10.9332,16.0039 9.96846,15.3191 9.65995,14.2708 L9.43749451,13.4935787 C8.88270062,11.4994608 8.12366,8.3311 8.12366,5.87781 C8.12366,3.7375 9.85872,2.00244 11.999,2.00244 Z" id="🎨-Color"></path> </g> </g></svg>
                    </span>
                    <!-- меню карточки -->
                    <span :style="'border-color: '+elem.photo.color+';'" v-if="!choose_mode" data-tooltip="Настройки посетителя" class="room_options room_options_close" @click = "room_list($event.target)">
                        <span :style="'background: '+elem.photo.color+';'"></span>
                        <span :style="'background: '+elem.photo.color+';'"></span>
                        <span :style="'background: '+elem.photo.color+';'"></span>
                    </span>
                    <span :style="'border-color: '+elem.photo.color+';'" v-if="!choose_mode && !elem.hide" data-tooltip="Удалить посетителя" class="room_option delete_room" @click = "remove_room(index, 'remove')">
                        <svg :style="'fill:'+elem.photo.color+';'" version="1.1" id="rubbish_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 408.483 408.483" style="enable-background:new 0 0 408.483 408.483;" xml:space="preserve"><g><g><path d="M87.748,388.784c0.461,11.01,9.521,19.699,20.539,19.699h191.911c11.018,0,20.078-8.689,20.539-19.699l13.705-289.316H74.043L87.748,388.784z M247.655,171.329c0-4.61,3.738-8.349,8.35-8.349h13.355c4.609,0,8.35,3.738,8.35,8.349v165.293c0,4.611-3.738,8.349-8.35,8.349h-13.355c-4.61,0-8.35-3.736-8.35-8.349V171.329z M189.216,171.329c0-4.61,3.738-8.349,8.349-8.349h13.355c4.609,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.737,8.349-8.349,8.349h-13.355c-4.61,0-8.349-3.736-8.349-8.349V171.329L189.216,171.329z M130.775,171.329c0-4.61,3.738-8.349,8.349-8.349h13.356c4.61,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.738,8.349-8.349,8.349h-13.356c-4.61,0-8.349-3.736-8.349-8.349V171.329z"/><path d="M343.567,21.043h-88.535V4.305c0-2.377-1.927-4.305-4.305-4.305h-92.971c-2.377,0-4.304,1.928-4.304,4.305v16.737H64.916c-7.125,0-12.9,5.776-12.9,12.901V74.47h304.451V33.944C356.467,26.819,350.692,21.043,343.567,21.043z"/></g></g></svg>
                    </span>
                    <span :style="'border-color: '+elem.photo.color+';'" v-if="!choose_mode && elem.hide" data-tooltip="Восстановить посетителя" class="room_option delete_room" @click = "remove_room(index, 'restore')">
                        <svg :style="'fill:'+elem.photo.color+';'" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.019 512.019" style="enable-background:new 0 0 512.019 512.019;" xml:space="preserve"><g><g><path d="M463.489,287.993h-64.32c-8.352,0-14.784,6.56-15.744,14.848c-8.48,73.152-78.848,127.648-157.248,109.696c-46.208-10.592-83.744-48.064-94.4-94.24c-19.296-83.68,44.064-158.304,124.512-158.304v48c0,6.464,3.904,12.32,9.888,14.784s12.832,1.088,17.44-3.488l96-96c6.24-6.24,6.24-16.384,0-22.624l-96-96c-4.608-4.544-11.456-5.92-17.44-3.456s-9.888,8.32-9.888,14.784v48c-124.608,0-225.76,102.24-223.968,227.264c1.696,118.848,101.728,218.944,220.576,220.736c119.392,1.792,218.08-90.368,226.784-207.168C480.353,295.705,472.641,287.993,463.489,287.993z"/></g></g></svg>
                    </span>
                    <span :style="'border-color: '+elem.photo.color+';'" v-if="!choose_mode" data-tooltip="Блокировать посетителя" class="ban_room room_option" @click = "ban_room(index)">
                        <svg :style="'fill:'+elem.photo.color+';'" version="1.1" id="lock_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 401.998 401.998" style="enable-background:new 0 0 401.998 401.998;" xml:space="preserve"><g><path d="M357.45,190.721c-5.331-5.33-11.8-7.993-19.417-7.993h-9.131v-54.821c0-35.022-12.559-65.093-37.685-90.218C266.093,12.563,236.025,0,200.998,0c-35.026,0-65.1,12.563-90.222,37.688C85.65,62.814,73.091,92.884,73.091,127.907v54.821h-9.135c-7.611,0-14.084,2.663-19.414,7.993c-5.33,5.326-7.994,11.799-7.994,19.417V374.59c0,7.611,2.665,14.086,7.994,19.417c5.33,5.325,11.803,7.991,19.414,7.991H338.04c7.617,0,14.085-2.663,19.417-7.991c5.325-5.331,7.994-11.806,7.994-19.417V210.135C365.455,202.523,362.782,196.051,357.45,190.721z M274.087,182.728H127.909v-54.821c0-20.175,7.139-37.402,21.414-51.675c14.277-14.275,31.501-21.411,51.678-21.411c20.179,0,37.399,7.135,51.677,21.411c14.271,14.272,21.409,31.5,21.409,51.675V182.728z"/></g></svg>
                    </span>
                    <span :style="'border-color: '+elem.photo.color+';'" v-if="!choose_mode && !elem.crm" data-tooltip="Добавить посетителя в CRM" class="add_room room_option" @click = "modal_window('open', 'add_mv_' + index.split('!@!@2@!@!')[1].replaceAll('.', '_'))">
                        <svg :style="'fill:'+elem.photo.color+';'" version="1.1" id="add_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M255.501,0.499c-81.448,0-147.711,66.264-147.711,147.711c0,50.449,25.429,95.065,64.137,121.724c-36.139,12.471-69.263,33.071-97.091,60.899C26.577,379.093,0,443.254,0,511.501h39.922c0-118.871,96.708-215.579,215.579-215.579c81.448,0,147.711-66.264,147.711-147.712S336.949,0.499,255.501,0.499z M255.501,256c-59.435,0-107.789-48.354-107.789-107.789S196.066,40.421,255.501,40.421S363.29,88.775,363.29,148.211S314.936,256,255.501,256z"/></g></g><g><g><polygon points="428.164,387.743 428.164,303.906 388.242,303.906 388.242,387.743 304.405,387.743 304.405,427.665 388.242,427.665 388.242,511.501 428.164,511.501 428.164,427.665 512,427.665 512,387.743 "/></g></g></svg>
                    </span>
                    <span :style="'border-color: '+elem.photo.color+';'" v-if="choose_mode && !choosen_users.hasOwnProperty(index)" @click="room_add(index)" class="guest_add_btn">
                        <span :style="'background:'+elem.photo.color+';'"></span>
                        <span :style="'background:'+elem.photo.color+';'"></span>
                    </span>
                    <span v-if="choose_mode && choosen_users.hasOwnProperty(index)" @click="room_delete(index)" class="guest_add_btn" style="transform:rotate(-45deg);border-color:#f90;">
                        <span style="background:#F90;"></span>
                        <span style="background:#F90;"></span>
                    </span> 
                    <!-- домены -->
                    <p class='user_domain' >
                        <span v-for = "domain in elem.domains_list.domains">{{domain}}</span>
                    </p>
                    <!-- CRM -->
                    <p v-if="elem.crm"  style = "color:#f90;font-size:20px;">CRM</p>
                    <!-- PAGES -->
                    <p v-if="elem.this_page && elem.status == 'online'" style="color:#f90;">Сейчас на странице</p>
                    <p v-else-if="elem.this_page && elem.status == 'offline'" style="color:#f90;">Последняя страница</p>
                    <a v-if="elem.this_page" class="card_link" :href="elem.this_page" :title="elem.this_page">{{elem.this_page}}</a>
                    <p v-if="elem.prev_page" style="color:#f90;">Пришёл с</p>
                    <a v-if="elem.prev_page" class="card_link" :href="elem.prev_page" :title="elem.prev_page">{{elem.prev_page}}</a>
                    <p>Посещений {{elem.visits}}</p>
                    <!-- adds -->
                    <p v-if="elem.info.advertisement"  style="color:tomato;font-size:15px;">{{elem.info.advertisement}}</p>
                    <!-- ip -->
                    <p style="color:#0ae;word-break:break-word;">Клиент {{elem.info["ip"]}}</p>
                    <p v-if="elem.session_time != 0 || elem.session_start">
                        Время на сайте
                        {{
                            !elem.current_session_time ? get_time_diff(elem) : msToTime(elem.current_session_time)
                        }}
                    </p>
                    <!-- GEO -->
                    <p v-if="elem.info['geo'] != null">{{elem.info["geo"]["country"] || "Неизвестно"}} / {{elem.info["geo"]["city"] || "Неизвестно"}}</p>
                    <p v-else >Неизвестно</p>
                    <!-- room info -->
                    <div class="room_property" v-if='Object.keys(elem.properties.properties).length > 0' v-for="(property_value, property_name) in elem.properties.properties" >
                        <p style="color:#f90;word-break:break-word;" v-html="property_name"></p>
                        <p style="color:#fff;word-break:break-word;" v-html="property_value"></p>
                    </div>
                    <!-- status -->
                    <p style="font-size:20px;" :class="elem.status">{{elem.status}}</p>
                    <!-- new message btn -->
                    <span @click = "modal_window('open', index.split('!@!@2@!@!')[1].replaceAll('.', '_'))" v-if="elem.new_message.status == 'unreaded' " style="background:#fff url('/scss/imgs/email.png') no-repeat center center;background-size:130%;left:-15px;top:-15px;position:absolute;height:30px;width:30px;border-radius:50%;cursor:pointer;"></span>
                    <!-- обслуживающие -->
                    <div class="consulation_list_block" v-if = 'elem.serving_list.assistents.length > 0 '>
                        <h2>Обслуживают</h2>
                        <div class="consulation_list">
                            <div v-for="assistent in elem.serving_list.assistents" v-if="assistents[assistent]">
                                <span :style='"background-image:url(<?php echo $assistents_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
                                <p>{{assistents[assistent]["name"]}}</p>
                            </div>
                        </div>
                    </div>
                    <!-- обслуживали -->
                    <div class="consulation_list_block" v-if = 'elem.served_list.assistents.length > 0 '>
                        <h2>Обслуживали</h2>
                        <div class="consulation_list">
                            <div v-for="assistent in elem.served_list.assistents" v-if="assistents[assistent]">
                                <span :style='"background-image:url(<?php echo $assistents_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
                                <p>{{assistents[assistent]["name"]}}</p>
                            </div>
                        </div>
                    </div>
                    <!-- модальное окно для сообщений -->
                    <div :id="index.split('!@!@2@!@!')[1].replaceAll('.', '_')" style="padding-right:20px;" class="message_modal_window">
                        <span class="room_options remove_room" v-on:click="modal_window('close', index.split('!@!@2@!@!')[1].replaceAll('.', '_'))">
                            <span></span>
                            <span></span>
                        </span>
                        <p v-if="elem.new_message.message" v-html="find_emojis(elem.new_message.message)"></p>
                        <div v-if="elem.new_message.message_adds" style="display:flex;flex-direction:column;" v-if = "elem.new_message.message_adds">
                            <img v-for ="add in JSON.parse(elem.new_message.message_adds)" :src="'/user_adds/' + add" style="display:block;max-height:220px;max-width:220px;margin:10px;" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) == -1"></img>
                            <p style="margin:20px;margin-left:0;" v-for ="add in JSON.parse(elem.new_message.message_adds)" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) != -1">
                                <a class="download_btn" :href="'/user_adds/' + add" download  >Скачать {{add.split('.').slice(-1)[0]}}</a>
                            </p>
                        </div>
                    </div>
                    <!-- модальное окно для сохранение в CRM -->
                    <div :id="'add_mv_' + index.split('!@!@2@!@!')[1].replaceAll('.', '_')" class="add_modal_window">
                        <span :style="'border-color:'+elem.photo.color+';'" class="room_options remove_room" v-on:click="modal_window('close', 'add_mv_' + index.split('!@!@2@!@!')[1].replaceAll('.', '_'))">
                            <span :style="'background:'+elem.photo.color+';'"></span>
                            <span :style="'background:'+elem.photo.color+';'"></span>
                        </span>
                        <h2 style="color:#fff;font-size:17px;font-weight:bold;">Куда сохранить ?</h2>
                        <span v-for="table in tables" :style="'border-color:'+elem.photo.color+';color:'+elem.photo.color+';'" class="modal_window_btn" @click="add_room(index, table)" v-html='table'></span>
                    </div>
                    <!-- PRINT -->
                    <p v-if="elem.typing" style="word-break:break-all;"><p style="font-weight:bold;font-size:17px;" v-if="elem.typing">Печатает:</p> <span style="line-height:2;" v-html="elem.typing"></span></p>
                    <!-- Activity -->
                    <p v-if="elem.lastActivityTime != '' && elem.lastActivityTime != null && elem.lastActivityTime != undefined && elem.status != 'online'" style="color:#fff;">Последняя активность</p>
                    <p  v-if="elem.lastActivityTime != '' && elem.lastActivityTime != null && elem.lastActivityTime != undefined && elem.status != 'online'"  style="color:#fff;"><span style="font-weight:bold;color:#f90;">{{elem.lastActivityTime.split(' ')[1].split(':').slice(0, 2).join(':')}}</span> {{elem.lastActivityTime.split(' ')[0].split('-').reverse().join('.') }}</p>
                    <!-- Btns -->
                    <button v-if='crm_items[elem.crm]?.[index]' :style="'border-color:'+elem.photo.color+';color:'+elem.photo.color+';'" :onclick="'window.location.href=\'/engine/consultant/crm?type='+elem.crm+'&search='+index+'\';'" type="button">Перейти в CRM</button>  
                    <button v-if='elem.serving_list.assistents.length == 0 && elem.served_list.assistents.indexOf("<?php echo $personal_id; ?>") == -1'style="color:lightgreen;border-color:lightgreen;"  @click.prevent="consultation(index, 'start')" type="submit">Начать чат</button>   
                    <button :style="'border-color:'+elem.photo.color+';color:'+elem.photo.color+';'"  v-else-if='elem.serving_list.assistents.indexOf("<?php echo $personal_id; ?>") != -1' @click.prevent="consultation(index, 'continue')" type="submit">Продолжить чат</button>
                    <button v-else-if='elem.served_list.assistents.indexOf("<?php echo $personal_id; ?>") != -1' style="color:#f90;border-color:#f90;" @click.prevent="consultation(index, 'restart')" type="submit">Возобновить чат</button>   
                    <button v-if='elem.serving_list.assistents.indexOf("<?php echo $personal_id; ?>") != -1' style="color:tomato;border-color:tomato;" @click.prevent="consultation(index, 'finish')" type="submit">Завершить чат</button>  
                </form>
                <span ref="observer" class="observer"></span>
            </div>
            <div class="Online-List-User2 v-cloak-on" :style='{"display":searchmas.hasOwnProperty("rooms") ? "none" : "flex !important"}' style="padding-top:25px;width:calc(100% - 350px);border-radius:0;" v-cloak>
                <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form>
                <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form> <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form> <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form> <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form> <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form> <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form> <form class="OnlineUser wow bounceInUp">
                    <span class="OnlineUser-image2 v-cloak-block"></span>
                    <span class= "visitor_device v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <p class="v-cloak-text"></p>
                    <button type="button" class="v-cloak-block"></button>   
                </form>
            </div>
            <div id="chat_settings_menu" class="bgblackwhite v-cloak-off" v-cloak style="min-height:660px;max-height:660px;" v-if="cache_variables['filters']['status']" >
                <input @keyup="cards_search()" placeholder="поиск" class="cards_search_input bgblackwhite WhiteBlack" type="text" />
                <select class="select_domain bgblackwhite WhiteBlack" v-model="selected_domain">
                    <option class="bgblackwhite WhiteBlack" value="all" selected>Все домены</option>
                    <option v-for="(domain, index) in domains" :value="domain">{{domain}}</option>
                </select>
                <div class = "inline_div" v-if="filter['name']" v-for="(filter, index) in cache_variables">
                    <h2 class="header2 WhiteBlack">{{filter['name']}}</h2>
                    <span class="check_btn" @click="filter_status(index)">
                        <span :class="[{'checked_btn_span': filter['status']}, {'unchecked_btn_span': !filter['status']}]"></span>
                    </span>
                </div>
            </div>
            <div id="chat_settings_menu" class="bgblackwhite v-cloak-on" v-cloak style="min-height:660px;max-height:660px;" v-if="cache_variables['filters']['status']" v-cloak>
                <div style="border-radous:10px;" class="cards_search_input v-cloak-block"></div>
                <div style="border-radous:10px;margin-top:10px;" class="select_domain v-cloak-block"></div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div> <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
                <div class = "inline_div" >
                    <p class="header2 v-cloak-text2"></p>
                    <span class="check_btn v-cloak-block" @click="filter_status(index)">
                        <span  class="checked_btn_span v-cloak-block"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php appendfooter(); ?>
</body>
<script src="/scripts/libs/howler.min.js"></script>
<script src="/scripts/libs/vue.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>