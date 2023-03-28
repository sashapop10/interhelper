<?php
	session_start();
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"]) && !isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$info = check_user($connection);
	if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
	if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/client_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
	<link rel="stylesheet" href="/scss/libs/animate.css">
	<link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
	<?php navigation('autosender', $info); ?>
    <section id='container'>
        <?php 
            section_header('Уведомления', 'autosend.png'); 
            create_book('');    
        ?>
        <div style="padding:20px;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;'">
            <h2 class='header1 wow bounceInUp'>Рассылка уведомлений</h2>
            <div class="swap_form" style="width:auto;" v-if="new_notification.status">
                <span class="close_new_notification" @click="new_notification.status = !new_notification.status">
                    <span></span>
                    <span></span>
                </span>
                <div style="display:inline-flex;">
                    <div style="display:flex;flex-direction:column;align-items:center;margin-right:10px;">
                        <p>Тип уведомления</p>
                        <div class="swap_type_block">
                            <p :vlaue="new_notification.type" @click="new_notification.type_list = !new_notification.type_list">{{new_notification.type == 'text' ? 'Обычный формат' : (new_notification.type == 'JavaScript' ? 'JavaScript формат' : (new_notification.type == 'DOM' ? 'DOM формат' : 'Не выбрано'))}}</p>
                            <ul id="swap_type" :style="{'max-height': new_notification.type_list ? 3 * 45 + 'px' : '0'}">
                                <li @click="new_notification.type = 'text'; new_notification.type_list = !new_notification.type_list;">Обычный формат</li>
                                <li @click="new_notification.type = 'JavaScript'; new_notification.type_list = !new_notification.type_list;">JavaScript формат</li>
                                <li @click="new_notification.type = 'DOM'; new_notification.type_list = !new_notification.type_list;">DOM формат</li>
                            </ul>
                        </div>
                        <p v-if="new_notification.type == 'JavaScript'" class="text1" style="font-size:13px;margin-bottom:10px;"> * Скрипт сохраняется всегда, а также он скрыт для посетителей.</p>
                        <p v-if="new_notification.type">Условия</p>
                        <div v-if="new_notification.type" class="swap_type_block">
                            <!--Выбранные --> 
                            <ul id="swap_type" :style="{'max-height':  (Object.keys(new_notification.conditions).length * 45) + 'px'}">
                                <li class="selected_condition" v-if="!conditions.hasOwnProperty(condition_id)" v-for="(condition, condition_id) in new_notification.conditions">
                                    {{
                                        (condition.type == 'activity_time' ? 'Время на сайте ' : '') + 
                                        (condition.second == '>' ? (condition.type == 'time' ? 'После ' : 'Больше ') : (condition.second == '<' ? (condition.type == 'time' ? 'До ' : 'Меньше ') : (condition.second == '=' ? 'Ровно  ' : condition.second||'' + ' '))) +  
                                        (condition.type == 'link' ? 'Ссылка включает ' : '') +
                                        condition.main 
                                    }}
                                    <span @click="removeCondition('new', condition_id);">
                                        <span></span>
                                        <span></span>
                                    </span>
                                </li>
                                <li class="selected_condition" v-if="conditions.hasOwnProperty(condition_id)" v-for="(condition, condition_id) in new_notification.conditions">
                                    {{conditions[condition_id].text}}
                                    <span @click="removeCondition('new', condition_id);">
                                        <span></span>
                                        <span></span>
                                    </span>
                                </li>
                            </ul>
                            <!--Выбор --> 
                            <ul id="swap_type" :style="{
                                    'max-height': new_notification.condition_list ? Object.entries(conditions).map((el) => { return el[1].input_status ? 4 : 1 }).reduce((a, b) => a + b) * 45 + 'px' : '0', 
                                    'border-top': Object.keys(new_notification.conditions).length > 0 ? '5px solid #000' : 'none'
                            }">
                                <li class="condition_list_nores" v-if="!condition.input_status && !(condition_id in new_notification.conditions)" v-for="(condition, condition_id) in conditions" @click="addCondition('new', condition_id)">{{condition.text}}</li>
                                <li class="condition_list_res" v-if="condition.input_status" v-for="(condition, condition_id) in conditions">
                                    <p>{{condition.text}}</p>
                                    <div class="condition_res">
                                        <select v-if="condition_id == 'time' || condition_id == 'open_counter' || condition_id == 'activity_time'">
                                            <option value=">">{{condition_id == 'time' ? 'После' : 'Больше'}}</option>
                                            <option value="<">{{condition_id == 'time' ? 'До' : 'Меньше'}}</option>
                                            <option v-if="condition_id == 'open_counter'" value="=">Ровно</option>
                                        </select>
                                        <input class="not_main" :style="{'max-width': condition_id == 'personal_event' ? '135px' : '190px'}" :placeholder="condition.placeholder1" :type="condition.input_type" />
                                        <input class="not_second" style="max-width:110px;" :placeholder="condition.placeholder2" :type="condition.input_type" v-if="condition_id == 'personal_event'" />
                                        <button @click="addCondition('new', condition_id)">Добавить</button>
                                    </div>
                                </li>
                            </ul>
                            <span :style="{'border-top': !new_notification.condition_list ? 'none' : '2px solid #000'}" class="notif_btn" @click="new_notification.condition_list = !new_notification.condition_list">{{ new_notification.condition_list ? 'Закрыть' : 'Добавить' }}</span>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;margin-left:10px;">
                        <p>Фотография отправителя</p>
                        <label class="review_photo_placeholder">
                            <input @change="upload_photo('new')" type="file" style='display:none;' />
                        </label>
                        <p v-if="new_notification.photo">Отправитель</p>
                        <input v-if="new_notification.photo" v-model="new_notification.sender" type="text" class="cr_notification_input">
                        <p v-if="new_notification.sender">Отдел или подпись</p>
                        <input v-if="new_notification.sender" v-model="new_notification.departament"  type="text" class="cr_notification_input">
                        <p v-if="new_notification.departament">Название рассылки *</p>
                        <input v-if="new_notification.departament" v-model="new_notification.name"  type="text" class="cr_notification_input">
                    </div>
                </div>
                <p>{{(new_notification.type == 'JavaScript' ? 'Введите скрипт' : (new_notification.type == 'DOM' ? 'Введите текст (html5 теги разрешены)' : 'Введите текст уведомления'))}}</p>
                <p v-if="new_notification.type == 'JavaScript'" class="text1" style="font-size:13px;margin-bottom:10px;"> * Не используйте теги < script > .</p>
                <div style="max-width:540px;background:#333;margin-top:10px;border:2px solid #000;border-radius:10px;width:100%;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;">
                    <div class="chat_footer" style="line-height:1.4;background:#333;border-bottom:2px solid #000;display:flex;align-items:flex-end;flex-direction:column;background:transparent" class="card-body message_panel_input">
                        <div type="text" data-textplace="new_notification" style="border-bottom:2px solid #000;border-top-left-radius:10px;border-top-right-radius:10px;background:url(/scss/imgs/classy_fabric.png) repeat center center;" contenteditable="true" aria-multiline="true" role="textbox"  class="chat_block_textarea form-control"></div>
                        <div class="btns_panel">
                            <span v-if="new_notification.type != 'JavaScript'" title="Добавить emoji" @click="smiles_mode = !smiles_mode;" class="btns_panel_btn send_smile"></span>
                            <span title="Быстрые команды" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                            <input @change="handleChange('new_notification')" multiple name='addphoto' class='add_photo'  id='add_photo' type='file' style='display:none;' />
                            <label v-if="new_notification.type != 'JavaScript'" title="Приложить фотографию" class="btns_panel_btn add_file" for="add_photo" ></label>
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
                        <div class="preview_img_block" v-for="(file, index) in new_notification.files" :key="index">
                            <span class="preview_remove" @click="removeFile(index, 'new_notification')"><span></span><span></span></span>
                            <span class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                            <p :title="file.name" style="background:#0ae;position:absolute;bottom:7px;left:0;color:#000;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.name}}</p>
                        </div>
                    </div>
                </div>
                <button v-if="new_notification.type && Object.keys(new_notification.conditions).length > 0 && new_notification.photo && new_notification.sender && new_notification.departament && new_notification.photo" @click="addNotification">добавить</button>
            </div>
            <span v-if='!new_notification.status' class="add_swap" @click="new_notification.status = !new_notification.status">Добавить уведомление</span> 
            <p class="text1">Уведомления без условий не выполняются</p>
            <div style="display:inline-flex;flex-wrap:wrap;align-items:flex-start;justify-content:flex-start;margin-top:20px;">
                <div v-for="(notification, notification_id) in notifications" :key="notification_id" class="notification_body bgblackwhite">
                    <span @click="remove_notification(notification_id)" class="delete_notification"><span></span><span></span></span>
                    <span class="grapth_btn" 
                        style="position: absolute; top: -20px ; right: 40px ; height: 40px ; width: 40px ;"
                        :style="{'background-color': (!notification?.['chart_settings']?.['status'] || notification?.['chart_settings']?.['openBY'] != 'MAIN_SWAP_CHART' || notification?.['chart_settings']?.['openRazdel'] != 'main') ? 'tomato' : 'lightgreen'}" 
                        @click="
                        (
                            !notification?.['chart_settings'] || notification?.['chart_settings']?.['openBY'] != 'MAIN_SWAP_CHART' || notification?.['chart_settings']?.['openRazdel'] != 'main'
                        ) ? create_chart(notification_id, 'MAIN_SWAP_CHART', 'main') : notification['chart_settings']['status'] = !notification['chart_settings']['status']"
                    ></span>
                    <div class="notification-top">
                        <div class="notification-top-left">
                            <div class="notification-main-info">
                                <label class="bgblackwhite" :style="'background-image:url(/notifications_photos/notification_photos/'+notification.photo+');'">
                                    <input @change="update_notification(notification_id, 'photo')" type="file" style="display:none;">
                                </label>
                                <div style="display:flex;flex-direction:column;">
                                    <p class="WhiteBlack">Навзвание рассылки</p>
                                    <input @change="update_notification(notification_id, 'notification_name')" style="border-radius:10px;margin-top:5px;" type="text" class="changable_input" :value="fixsring(notification.name)">
                                </div>
                            </div>
                            <div class="notification-second-info">
                                <div style="display:flex;flex-direction:column;">
                                    <p class="WhiteBlack">Имя отправителя (системы)</p>
                                    <input @change="update_notification(notification_id, 'name')" style="border-radius:10px;margin-top:5px;" class="changable_input" type="text" :value="fixsring(notification.sender)">
                                 </div>
                                 <div style="display:flex;flex-direction:column;margin-top:15px;">
                                    <p class="WhiteBlack">Подпись / отдел  (системы)</p>
                                    <input @change="update_notification(notification_id, 'departament')" style="border-radius:10px;margin-top:5px;" class="changable_input" type="text" :value="fixsring(notification.departament)">
                                </div>
                            </div>
                            <div style="max-width:333px;background:#333;margin-top:10px;border:2px solid #000;border-radius:10px;width:100%;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;">
                                <div class="domModified chat_footer" style="line-height:1.4;background:#333;border-bottom:2px solid #000;display:flex;align-items:flex-end;flex-direction:column;background:transparent" class="card-body message_panel_input">
                                    <div v-html="find_emojis(notification.text)" type="text" :data-notification_id="notification_id" data-textplace="new_notification" style="border-bottom:2px solid #000;border-top-left-radius:10px;border-top-right-radius:10px;background:url(/scss/imgs/classy_fabric.png) repeat center center;" contenteditable="true" aria-multiline="true" role="textbox"  class="chat_block_textarea not_message form-control"></div>
                                    <div class="btns_panel">
                                        <span v-if="new_notification.type != 'JavaScript'" title="Добавить emoji" @click="notification_smiles_mode(notification_id)" class="btns_panel_btn send_smile"></span>
                                        <span title="Быстрые команды" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                                        <label  v-if="new_notification.type != 'JavaScript'" title="Приложить фотографию" class="btns_panel_btn add_file" >
                                            <input @change="handleChange(notification_id)" :data-id="notification_id" multiple name='addphoto' class='add_photo'  type='file' style='display:none;' />
                                        </label>
                                    </div>   
                                </div> 
                                <div v-if="notification.smiles_mode" class="smiles_container">
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
                                    <div :style="{'padding': regexp.indexOf('.'+file.split('.')[1]) == -1 ? '0px' : '20px' }" class="preview_img_block" v-for="(file, index) in notification.adds" :key="index">
                                        <span class="preview_remove" @click="removeFile(index, notification_id)"><span></span><span></span></span>
                                        <span v-if="regexp.indexOf('.'+file.split('.')[1]) == -1" class="preview_img" :style="'border-radius:10px;height:100%;width:100%;background-image:url(/notifications_photos/notification_adds/'+file+');background-size:cover;background-position:center;background-repeat:no-repeat;'"></span>
                                        <span v-else class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                                        <p :title="file.name" style="background:#0ae;position:absolute;bottom:7px;left:0;color:#000;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.split('.')[1]}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="notification-top-right">
                            <p class="WhiteBlack">Тип уведомления</p>
                            <div style="margin-top:10px;" class="swap_type_block">
                                <p @click="update_type(null, notification_id)">{{notification.type == 'text' ? 'Обычный формат' : (notification.type == 'JavaScript' ? 'JavaScript формат' : (notification.type == 'DOM' ? 'DOM формат' : 'Не выбрано'))}}</p>
                                <ul id="swap_type" :style="{'max-height': notification.type_list ? (45 * 3) + 'px' : '0px'}">
                                    <li @click="update_type('text', notification_id)">Обычный формат</li>
                                    <li @click="update_type('JavaScript', notification_id)">JavaScript формат</li>
                                    <li @click="update_type('DOM', notification_id)">DOM формат</li>
                                </ul>
                            </div>
                            <p class="WhiteBlack" style="margin-top:10px;">Условия</p>
                            <div style="margin-top:10px;" class="swap_type_block">
                                <!--Выбранные --> 
                                <ul id="swap_type" :style="{'max-height':  (Object.keys(notification.conditions).length * 45) + 'px'}">
                                    <li class="selected_condition" v-if="!conditions.hasOwnProperty(condition_id)" v-for="(condition, condition_id) in notification.conditions">
                                        {{
                                            (condition.type == 'activity_time' ? 'Время на сайте ' : '') +
                                            (condition.second == '>' ? (condition.type == 'time' ? 'После ' : 'Больше ') : (condition.second == '<' ? (condition.type == 'time' ? 'До ' : 'Меньше ') : (condition.second == '=' ? 'Ровно  ' : condition.second||'' + ' '))) +  
                                            (condition.type == 'link' ? 'Ссылка включает ' : '') +
                                            condition.main 
                                        }}
                                        <span @click="removeCondition(notification_id, condition_id);">
                                            <span></span>
                                            <span></span>
                                        </span>
                                    </li>
                                    <li class="selected_condition" v-if="conditions.hasOwnProperty(condition_id)" v-for="(condition, condition_id) in notification.conditions">
                                        {{conditions[condition_id].text}}
                                        <span @click="removeCondition(notification_id, condition_id);">
                                            <span></span>
                                            <span></span>
                                        </span>
                                    </li>
                                </ul>
                                <!--Выбор --> 
                                <ul id="swap_type" :style="{
                                        'max-height': notification.condition_list ? Object.entries(conditions).map((el) => { return el[1].input_status ? 4 : 1 }).reduce((a, b) => a + b) * 45 + 'px' : '0', 
                                        'border-top': Object.keys(notification.conditions).length > 0 ? '5px solid #000' : 'none'
                                }">
                                    <li class="condition_list_nores" v-if="!condition.input_status" v-for="(condition, condition_id) in conditions" @click="addCondition(notification_id, condition_id)">{{condition.text}}</li>
                                    <li class="condition_list_res" v-if="condition.input_status" v-for="(condition, condition_id) in conditions">
                                        <p>{{condition.text}}</p>
                                        <div class="condition_res">
                                            <select v-if="condition_id == 'time' || condition_id == 'open_counter' || condition_id == 'activity_time'">
                                                <option value=">">{{condition_id == 'time' ? 'После' : 'Больше'}}</option>
                                                <option value="<">{{condition_id == 'time' ? 'До' : 'Меньше'}}</option>
                                                <option v-if="condition_id == 'open_counter' || condition_id == 'activity_time'" value="=">Ровно</option>
                                            </select>
                                            <input class="not_main" :style="{'max-width': condition_id == 'personal_event' ? '135px' : '190px'}" :placeholder="condition.placeholder1" :type="condition.input_type" />
                                            <input class="not_second" style="max-width:110px;" :placeholder="condition.placeholder2" :type="condition.input_type" v-if="condition_id == 'personal_event'" />
                                            <button @click="addCondition(notification_id, condition_id)">Добавить</button>
                                        </div>
                                    </li>
                                </ul>
                                <span style="color:#fff;" :style="{'border-top': !notification.condition_list ? 'none' : '2px solid #000'}" class="notif_btn" @click="condition_list(notification_id)">{{ notification.condition_list ? 'Закрыть' : 'Добавить' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="swap_graphic" :style="{'max-height': !notification?.['chart_settings']?.['status'] ? 0 : get_canvas_height(notification_id)+'px'}">
                        <div id="chart_options">
                            <div id="chart_type" style="padding:0;">
                                <span @click="chart_model(notification_id, 'line')" data-chart="line" :class="{active_chart: notification?.['chart_settings']?.['type'] == 'line'}"></span>
                                <span @click="chart_model(notification_id, 'bar')" data-chart="bar" :class="{active_chart: notification?.['chart_settings']?.['type'] == 'bar'}"></span>
                            </div>
                            <div id="pereod" style="display:inline-flex;align-items:center;justify-content:center;">
                                <input @change="chart_update(notification_id, 'from')" type="date" :value="get_time(notification?.['chart_settings']?.['from'])" id='prereod_from'>
                                <span style="height:5px;width:10px;background:#fff;display:block;"></span>
                                <input @change="chart_update(notification_id, 'to')" :value="get_time(notification?.['chart_settings']?.['to'])" type="date" id='prereod_to'>
                            </div>
                            <select @change="chart_update(notification_id, 'date_type')" id="pereod_type" style="margin-left:10px;">
                                <option :selected="notification?.['chart_settings']?.['date_type'] == 'days'" value="days">Дни</option>
                                <option :selected="notification?.['chart_settings']?.['date_type'] == 'mounths'" value="mounths">Месяцы</option>
                                <option :selected="notification?.['chart_settings']?.['date_type'] == 'years'" value="years">Годы</option>
                            </select>
                        </div>
                        <div class="chart_bg">
                            <canvas :id="'myChart_' + notification_id"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<?php appendfooter();?>
</body>
<script src="/scripts/libs/chart.js"></script>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>
