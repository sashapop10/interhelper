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
    $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    $sql = "SELECT buttlecry FROM assistents WHERE id = '$personal_id'";
    $buttlecry = attach_sql($connection, $sql, 'row')[0];
    mysqli_close($connection);
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
    $_SESSION["assistent_room"] = $_GET['room'];
    if(!isset($_GET['room'])){ header("Location: /engine/consultant/hub"); exit;}
	if($boss_id != explode('!@!@2@!@!' , $_GET['room'])[0]){ header("Location: /engine/consultant/hub"); exit;}
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
    <script src="/scripts/libs/vue.js"></script>
</head>
<body>
    <?php navigation('hub', $info); ?>
    <div id="app" v-cloak>
        <?php team_msg_notification_body($file_path); ?>
        <div id="chat_header" class="card-header text-white bgblackwhite">
            <div class="chat-header-info v-cloak-off" v-cloak>
                <span v-if="g_type"
                    style="height:50px;width:50px;border-radius:50%;background-size:cover;background-position:center;background-repeat:no-repeat;margin-right:10px;"
                    :style="'background-image:url(/crm_files/'+g_photo+');background-color:'+photo.color+';'"
                ></span>
                <span v-else
                    style="height:50px;width:50px;border-radius:50%;background-size:80%;background-position:center;background-repeat:no-repeat;margin-right:10px;"
                    :style="'background-image:url(/visitors_photos/'+photo.img+');background-color:'+photo.color+';'"
                ></span>
                <div style="display:flex;flex-direction:column;align-items:flex-start;">
                    <h3 style="color:#f90;margin-bottom:5px;" v-if="g_type">CRM</h3>
                    <div style='display:inline-flex;flex-wrap:wrap;'>
                        <h4><span :style="'color:'+photo.color+';margin-right:10px;border-radius:10px;padding:5px;background-color:#252525;'">Клиент {{info["ip"]}}</span></h4>
                        <span :class="status" style="text-transform:uppercase;">{{status}}</span>
                    </div>
                </div>
            </div>
            <div class="chat-header-info v-cloak-on" v-cloak>
                <span  style="height:50px;min-width:50px;border-radius:50%;margin-right:10px;" class="v-cloak-block"></span>
                <p class="v-cloak-text2" style="width:200px;"></p>
            </div>
            <div class="chat-header-btns v-cloak-off" v-cloak>
                <span v-if="g_type" style="background-color:#252525;" :style="'border-color:'+photo.color+';margin-right:10px;'" data-tooltip="Перейти в CRM" :onclick="'window.location.href=\'/engine/consultant/crm?type='+g_type+'&search='+room+'\';'" class="ban_room chat_header_btn">
                    <p :style='"color:"+photo.color+";font-weight:bold;margin:0;font-size:15px;"'>CRM</p>
                </span>
                <span style="background-color:#252525;" :style="'border-color:'+photo.color+';'" data-tooltip="Заблокировать" @click ="ban_room" class="ban_room chat_header_btn">
                    <svg :style="'fill:'+photo.color+';'" version="1.1" id="lock_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 401.998 401.998" style="enable-background:new 0 0 401.998 401.998;" xml:space="preserve"><g><path d="M357.45,190.721c-5.331-5.33-11.8-7.993-19.417-7.993h-9.131v-54.821c0-35.022-12.559-65.093-37.685-90.218C266.093,12.563,236.025,0,200.998,0c-35.026,0-65.1,12.563-90.222,37.688C85.65,62.814,73.091,92.884,73.091,127.907v54.821h-9.135c-7.611,0-14.084,2.663-19.414,7.993c-5.33,5.326-7.994,11.799-7.994,19.417V374.59c0,7.611,2.665,14.086,7.994,19.417c5.33,5.325,11.803,7.991,19.414,7.991H338.04c7.617,0,14.085-2.663,19.417-7.991c5.325-5.331,7.994-11.806,7.994-19.417V210.135C365.455,202.523,362.782,196.051,357.45,190.721z M274.087,182.728H127.909v-54.821c0-20.175,7.139-37.402,21.414-51.675c14.277-14.275,31.501-21.411,51.678-21.411c20.179,0,37.399,7.135,51.677,21.411c14.271,14.272,21.409,31.5,21.409,51.675V182.728z"/></g></svg>
                </span>
                <span style="background-color:#252525;margin-left:10px;" :style="'border-color:'+photo.color+';'" data-tooltip="Добавить" v-if="!g_type"  v-if="!add_crm_mode" @click="add_crm_mode = !add_crm_mode" class="add_room chat_header_btn">
                    <svg :style="'fill:'+photo.color+';'" version="1.1" id="add_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M255.501,0.499c-81.448,0-147.711,66.264-147.711,147.711c0,50.449,25.429,95.065,64.137,121.724c-36.139,12.471-69.263,33.071-97.091,60.899C26.577,379.093,0,443.254,0,511.501h39.922c0-118.871,96.708-215.579,215.579-215.579c81.448,0,147.711-66.264,147.711-147.712S336.949,0.499,255.501,0.499z M255.501,256c-59.435,0-107.789-48.354-107.789-107.789S196.066,40.421,255.501,40.421S363.29,88.775,363.29,148.211S314.936,256,255.501,256z"/></g></g><g><g><polygon points="428.164,387.743 428.164,303.906 388.242,303.906 388.242,387.743 304.405,387.743 304.405,427.665 388.242,427.665 388.242,511.501 428.164,511.501 428.164,427.665 512,427.665 512,387.743 "/></g></g></svg>
                </span>
                <span style="background-color:#252525;margin-left:10px;" :style="'border-color:'+photo.color+';'" data-tooltip="Удалить"  @click ="remove_room"  class="delete_room chat_header_btn">
                    <svg :style="'fill:'+photo.color+';'" version="1.1" id="rubbish_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 408.483 408.483" style="enable-background:new 0 0 408.483 408.483;" xml:space="preserve"><g><g><path d="M87.748,388.784c0.461,11.01,9.521,19.699,20.539,19.699h191.911c11.018,0,20.078-8.689,20.539-19.699l13.705-289.316H74.043L87.748,388.784z M247.655,171.329c0-4.61,3.738-8.349,8.35-8.349h13.355c4.609,0,8.35,3.738,8.35,8.349v165.293c0,4.611-3.738,8.349-8.35,8.349h-13.355c-4.61,0-8.35-3.736-8.35-8.349V171.329z M189.216,171.329c0-4.61,3.738-8.349,8.349-8.349h13.355c4.609,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.737,8.349-8.349,8.349h-13.355c-4.61,0-8.349-3.736-8.349-8.349V171.329L189.216,171.329z M130.775,171.329c0-4.61,3.738-8.349,8.349-8.349h13.356c4.61,0,8.349,3.738,8.349,8.349v165.293c0,4.611-3.738,8.349-8.349,8.349h-13.356c-4.61,0-8.349-3.736-8.349-8.349V171.329z"/><path d="M343.567,21.043h-88.535V4.305c0-2.377-1.927-4.305-4.305-4.305h-92.971c-2.377,0-4.304,1.928-4.304,4.305v16.737H64.916c-7.125,0-12.9,5.776-12.9,12.901V74.47h304.451V33.944C356.467,26.819,350.692,21.043,343.567,21.043z"/></g></g></svg>
                </span>
                <span data-tooltip="Завершить обслуживание" style="margin-left:10px;border-color:#f90;" class="finish_chat chat_header_btn" v-on:click="finish"></span>
                <span style="background-color:#252525;margin-left:10px;" data-tooltip="Вернуться к списку посетителей"  class="break_chat chat_header_btn" v-on:click="exit"><span></span><span></span></span>
            </div>
            <div class="chat-header-btns v-cloak-on" v-cloak>
                <span class="chat_header_btn v-cloak-block"></span>
                <span class="chat_header_btn v-cloak-block" style="margin-left:10px;"></span>
                <span class="chat_header_btn v-cloak-block" style="margin-left:10px;"></span>
                <span class="chat_header_btn v-cloak-block" style="margin-left:10px;"></span>
                <span class="chat_header_btn v-cloak-block" style="margin-left:10px;"></span>
            </div>
        </div>
        <div class="app_row">
            <?php create_book($buttlecry); ?>
            <div id="chat_container" class="card bg-info" >
                <ul id="chat_body" class="list-group list-group-flush text-right">
                    <span data-tooltip="Задача" :onclick="'alert(\''+task.text+'\', \'log\');'" :style="'background-color: '+(new Date(task.time) > new Date ? '#f90' : 'lightgreen')+';border:3px solid '+photo.color+';z-index:10;top:15px;left:'+(task_index == 0 ? '15' : (task_index + 1) * 25)+'px;'" class="visitor_task" v-if="tasks.hasOwnProperty(room)" v-for="(task, task_index) in tasks[room]">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="task_mark" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="ic_fluent_important_24_filled" fill="#212121" fill-rule="nonzero"> <path d="M12,17.0015 C13.3813,17.0015 14.5011,18.1213 14.5011,19.5026 C14.5011,20.8839 13.3813,22.0037 12,22.0037 C10.6187,22.0037 9.49888,20.8839 9.49888,19.5026 C9.49888,18.1213 10.6187,17.0015 12,17.0015 Z M11.999,2.00244 C14.1393,2.00244 15.8744,3.7375 15.8744,5.87781 C15.8744,8.71128 14.8844,12.4318 14.339,14.2756 C14.0294,15.322 13.0657,16.0039 12.0006,16.0039 C10.9332,16.0039 9.96846,15.3191 9.65995,14.2708 L9.43749451,13.4935787 C8.88270062,11.4994608 8.12366,8.3311 8.12366,5.87781 C8.12366,3.7375 9.85872,2.00244 11.999,2.00244 Z" id="🎨-Color"></path> </g> </g></svg>
                    </span>
                    <div :style="'border-color:'+photo.color+';'" id ="typing" class="v-cloak-off" v-if="typing" v-cloak><small style ="width:100%;" v-if="typing">Гость пишет : <p v-html="typing"></p></small></div> 
                    <li class="list-message wow bounceInDown v-cloak-off" v-for="(message, message_index) in messages" v-cloak>
                         <span class="date" v-if="load_date(message.time.split(' ')[0].split('-').reverse().join('.'))">
                            <span class="bgblackwhite WhiteBlack">{{message.time.split(' ')[0].split('-').reverse().join('.')}}</span>
                        </span>
                        <div style="width:100%;display:flex;" :class="{'message-by-me':message.sender && message.sender != 'offline_form' }">
                            <span class="list-message-block bgblackwhite">
                                <div v-if="message.sender && message.sender != 'offline_form' && message.sender != 'notification' && ((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg bgblackwhite" v-bind:style='{ backgroundImage: "url(<?php echo $file_path; ?>"+message.photo+")"}'></div>
                                <div v-else-if="message.sender != 'notification' && g_type && ((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg bgblackwhite" v-bind:style='{ backgroundImage: "url(/crm_files/"+g_photo+")"}'></div>
                                <div v-else-if="message.sender == 'notification'" class="thisuserimg" v-bind:style='{ backgroundImage: "url(/notifications_photos/notification_photos/"+message.photo+")"}'></div>
                                <div v-else-if="((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg" v-bind:style='"background-image:url(/visitors_photos/"+photo.img+");background-size:80%;background-color:"+photo.color+";"'></div>
                                <div class="list-message-info" v-if="message.sender != 'offline_form'">
                                    <small class="WhiteBlack" v-if="message.sender != 'offline_form'"><span v-html="(message.user||(g_name == 'new' ? null : g_name)||'Клиент ')"></span> <span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small v-else class="message_send_time"><span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small class="list-message-block-mail" v-if="message.sender" v-html="message.departament"></small>
                                </div>
                                <div style="background:rgba(0,0,0,0.8);border-top-right-radius:10px;border-top-left-radius:10px;" :style="{'border-color': photo.color }" class="form-info" v-else>
                                    <small :style="'border-color:'+photo.color+';'" class="message_send_time" style="font-weight:bold;"><span style="color:#f90;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span> отправлена форма обратной связи</small>
                                    <small :style="'border-color:'+photo.color+';'" v-if="message.user" v-html="message.user"></small>
                                    <small :style="'border-color:'+photo.color+';'" v-if="message.email" v-html="message.email"></small>
                                    <small :style="'border-color:'+photo.color+';'" v-if="message.phone" v-html="message.phone"></small>
                                </div>
                                <p class="list-message-block-message" style="word-break: break-word;" :style="[{'color': message.mode == 'js_mode' || message.mode == 'invisible' ? '#0ae' : '#fff'}, {'border-color': photo.color }]" :class="message.sender == 'offline_form' ? 'form_message' : 'WhiteBlack'" v-if="message.message" v-html="find_emojis((message.mode == 'DOM' ? htmldecoder(message.message) : message.message))"></p>
                                <div style="display:flex;flex-direction:column;" v-if = "message.message_adds">
                                    <img v-for ="add in JSON.parse(message.message_adds)" :src="(message.sender == 'notification' ? '/notifications_photos/notification_adds/' : files_path) + add" style="display:block;max-height:250px;max-width:250px;margin:10px;" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) == -1"></img>
                                    <p style="margin:20px;margin-left:0;" v-for ="add in JSON.parse(message.message_adds)" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) != -1">
                                        <a class="download_btn" :style="'background:'+photo.color+';'" :href="(message.sender == 'notification' ? '/notifications_photos/notification_adds/' : files_path) + add" download  >Скачать {{add.split('.').slice(-1)[0]}}</a>
                                    </p>
                                </div>
                            </span>
                        </div>
                    </li>
                    <li :style="{'display': !messages_loaded ? 'flex !important' : 'none'}" class="list-message wow bounceInDown v-cloak-on" v-cloak>
                        <span class="date"><span class="v-cloak-div bgblackwhite WhiteBlack"><?php echo date("d.m.Y"); ?></span></span>
                        <div style="width:100%;display:flex;">
                            <span class="list-message-block bgblackwhite">
                                <div class="thisuserimg bgblackwhite v-cloak-block"></div>
                                <div class="list-message-info">
                                    <small class="WhiteBlack"><span class="v-cloak-text2" style="width:200px"></span><span class="v-cloak-text2" style="width:100px"></span></small>
                                    <small class="list-message-block-mail v-cloak-text2" style="width:100%;"></small>
                                </div>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                            </span>
                        </div>
                    </li>
                    <li :style="{'display': !messages_loaded ? 'flex !important' : 'none'}" class="list-message wow bounceInDown v-cloak-on" v-cloak>
                        <div style="width:100%;display:flex;" class="message-by-me">
                            <span class="list-message-block bgblackwhite">
                                <div class="thisuserimg bgblackwhite v-cloak-block"></div>
                                <div class="list-message-info">
                                    <small class="WhiteBlack"><span class="v-cloak-text2" style="width:200px"></span><span class="v-cloak-text2" style="width:100px"></span></small>
                                    <small class="list-message-block-mail v-cloak-text2" style="width:100%;"></small>
                                </div>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                            </span>
                        </div>
                    </li>
                    <li :style="{'display': !messages_loaded ? 'flex !important' : 'none'}" class="list-message wow bounceInDown v-cloak-on" v-cloak>
                        <div style="width:100%;display:flex;" class="message-by-me">
                            <span class="list-message-block bgblackwhite">
                                <div class="list-message-info">
                                    <small class="WhiteBlack"><span class="v-cloak-text2" style="width:200px"></span><span class="v-cloak-text2" style="width:100px"></span></small>
                                    <small class="list-message-block-mail v-cloak-text2" style="width:100%;"></small>
                                </div>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                            </span>
                        </div>
                    </li>
                </ul>
                <div id="chat_footer" class="card-body bgblackwhite">
                    <div v-if="smiles_mode" class="smiles_container bgblackwhite v-cloak-off" v-cloak>
                        <div  class="smiles_folder bgblackwhite" v-for="(folder, folder_name) in emojis">
                            <h2 onclick="smiles_folder()" class="smiles_name WhiteBlack">{{folder_name}}</h2>
                            <div class="smiles smiles_close">
                                <div class="smile" v-for="(smile, smile_key) in folder">
                                    <span @click="select_smile(smile_key, folder_name, smile)" class="smile_photo" :style="'background-image:url(/emojis/'+folder_name+'/'+smile"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div type="text" :contenteditable="load ? 'true': 'false'" aria-multiline="true" role="textbox"  class="chat_block_textarea form-control WhiteBlack"></div>
                    <div id="placeholder" class="v-cloak-off" v-cloak>
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
                    <div class="v-cloak-text2 v-cloak-on" id="placeholder" v-cloak style="max-width:350px;"></div>
                    <div class="btns_panel v-cloak-off" v-cloak>
                        <span title="Отправить JavaScript код" v-if="load" @click="js_mode = !js_mode; style_mode = false;" :style="{'background-color': js_mode ? 'green' : 'tomato'}" class="btns_panel_btn send_js"></span>
                        <span title="Отправить сообщение содержащие DOM теги" v-if="load" @click="style_mode = !style_mode; js_mode = false;" :style="{'background-color': style_mode ? 'green' : 'tomato'}" class="btns_panel_btn send_css"></span>
                        <span title="Добавить emoji" v-if="load" @click="smiles_mode = !smiles_mode;" class="btns_panel_btn send_smile"></span>
                        <span title="Быстрые команды" v-if="load" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                        <input @change="handleChange()" multiple name='addphoto' class='add_photo'  id='add_photo' type='file' style='display:none;' />
                        <label title="Приложить фотографию" class="btns_panel_btn add_file" v-if="load" for="add_photo" ></label>
                        <button title="Отправить сообщение" class="btns_panel_btn Send_message_button" v-if="load" style="cursor:pointer;" v-on:click = "send" type="button"></button> 
                        <span v-if="!load" class="msg_loader"></span>
                    </div>   
                    <div class="btns_panel v-cloak-on" v-cloak>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                    </div>
                </div>
            </div>
            <div id="chat_settings_menu" class="bgblackwhite v-cloak-off" style="max-height:701px;min-height:701px;text-align:center;position:relative;" v-cloak>
                <span :style="'border-color:'+photo.color+';color:'+photo.color+';background-color:#252525;'" v-if="add_crm_mode" class="modal_window_btn" @click="add_crm_mode = !add_crm_mode" >Отменить</span>
                <span v-for="table in tables" :style="'border-color:'+photo.color+';color:'+photo.color+';background-color:#252525;'" v-if="add_crm_mode" v-if="add_crm_mode"class="modal_window_btn" @click="add_room(table)" v-html='table'></span>
                <!--устройство-->
                <span :style="'background-color: '+photo.color+';'" class="visitor_device2" v-if="info.device == 'desktop'">
                    <svg height="512" viewBox="0 0 56 56" width="512" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="016---PC-and-Monitor" fill="rgb(0,0,0)" fill-rule="nonzero"><circle id="Oval" cx="50" cy="23" r="1"/><path id="Shape" d="m31 54h-20c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h20c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"/><path id="Shape" d="m27 46h12c1.6568542 0 3-1.3431458 3-3h-42c0 1.6568542 1.34314575 3 3 3z"/><path id="Rectangle-path" d="m16 48h10v4h-10z"/><circle id="Oval" cx="50" cy="15" r="1"/><path id="Shape" d="m42 17c0-1.6568542-1.3431458-3-3-3h-36c-1.65685425 0-3 1.3431458-3 3v24h42zm-39.71 3.29 4-4c.25365857-.2536586.62337399-.3527235.96987804-.259878.34650405.0928454.61715452.3634959.71.71.09284548.346504-.00621947.7162194-.25987804.969878l-4 4c-.1877666.1893127-.44336246.2957983-.71.2957983s-.5222334-.1064856-.71-.2957983c-.18931265-.1877666-.29579832-.4433625-.29579832-.71s.10648567-.5222334.29579832-.71zm11.42-2.58-10 10c-.1877666.1893127-.44336246.2957983-.71.2957983s-.5222334-.1064856-.71-.2957983c-.18931265-.1877666-.29579832-.4433625-.29579832-.71s.10648567-.5222334.29579832-.71l10-10c.2536586-.2536586.623374-.3527235.969878-.259878.3465041.0928454.6171546.3634959.71.71.0928455.346504-.0062194.7162194-.259878.969878z"/><path id="Shape" d="m55 0h-26c-.5522847 0-1 .44771525-1 1v11h11c2.7614237 0 5 2.2385763 5 5v26c0 2.7614237-2.2385763 5-5 5h-11v4h3c.9724158.0027302 1.8831896.476617 2.4434995 1.2713829.5603099.7947658.7006442 1.8118124.3765005 2.7286171h21.18c.5522847 0 1-.4477153 1-1v-54c0-.55228475-.4477153-1-1-1zm-17 53c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm4 0c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm4 0c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm4 0c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm-3-30c0-1.6568542 1.3431458-3 3-3s3 1.3431458 3 3-1.3431458 3-3 3-3-1.3431458-3-3zm0-8c0-1.6568542 1.3431458-3 3-3s3 1.3431458 3 3-1.3431458 3-3 3-3-1.3431458-3-3zm7 38c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1v-2c0-.5522847.4477153-1 1-1s1 .4477153 1 1zm0-46c0 1.1045695-.8954305 2-2 2h-20c-1.1045695 0-2-.8954305-2-2v-2c0-1.1045695.8954305-2 2-2h20c1.1045695 0 2 .8954305 2 2z"/><path id="Rectangle-path" d="m32 5h20v2h-20z"/></g></g></svg>
                </span>
                <span :style="'background-color: '+photo.color+';'" class="visitor_device2" v-if="info.device == 'mobile'">
                    <svg version="1.1" id="phone" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 31.685 31.685" style="enable-background:new 0 0 31.685 31.685;" xml:space="preserve"><g><path d="M22.507,0H9.175C7.9,0,6.87,1.034,6.87,2.309v27.07c0,1.271,1.03,2.306,2.305,2.306h13.332c1.273,0,2.307-1.034,2.307-2.306V2.309C24.813,1.034,23.78,0,22.507,0z M23.085,25.672H8.599V3.895h14.486V25.672z M18.932,2.343h-6.181V1.669h6.182L18.932,2.343L18.932,2.343z M21.577,2.035c0,0.326-0.266,0.59-0.591,0.59c-0.326,0-0.591-0.265-0.591-0.59s0.265-0.59,0.591-0.59C21.312,1.444,21.577,1.709,21.577,2.035z M18.655,29.225h-5.629v-1.732h5.629V29.225z"/></g></svg>
                </span>
                <div style="margin-top:15px;border-bottom:3px solid #000;direction: ltr;padding:10px;">
                    <h2 class="header1" style="padding:5px;border-radius:10px;background:#252525;" :style="'color:'+photo.color+';'">Время на сайте</h2>
                    <h2 class="header1 WhiteBlack" style="color:#fff;">{{msToTime(room_time)}}</h2>
                    <h2 class="header1 WhiteBlack" style="color:#fff;">Посещений <span :style="'color:'+photo.color+';'" style="padding:5px;border-radius:10px;background:#252525;">{{visits}}</span></h2>
                </div>
                <div style="margin-top:15px;border-bottom:3px solid #000;direction: ltr;">
                    <h2 class="header1" :style="'color:'+photo.color+';'" style="padding:5px;border-radius:10px;background:#252525;">Заметки</h2>
                    <div class="note-block-new">
                        <textarea v-model="new_note" placeholder="Введите заметку" class="changable_input"></textarea>
                        <button @click="add('note')">Добавить</button>
                    </div>
                    <div v-for="(note, note_id) in reverse(notes.notes)" class="note-block-new">
                        <textarea class="changable_input" @change="change(note_id, 'note', null)" v-html="note"></textarea>
                        <button style="background:tomato;color:#000;" @click="remove('note', note_id)">Удалить</button>
                    </div>
                </div>
                <div  style='border-bottom:3px solid #000;direction: ltr;'>
                    <h2 class="header1" :style="'color:'+photo.color+';'" style="padding:5px;border-radius:10px;background:#252525;">Свойства посетителя</h2>
                    <div class="note-block-new">
                        <input placeholder="Название свойства" class="changable_input" v-model="new_property_name" type="text"/>
                        <textarea class="changable_input" placeholder="Значение свойства" class="changable_input" v-model="new_property_value"></textarea>
                        <button @click="add('property')">Добавить</button>
                    </div>
                    <div v-for="(property, property_name) in properties.properties" class="note-block-new">
                        <input class="changable_input" type="text" @change="change(property_name, 'property', 'name')" :value="htmldecoder(property_name)" />
                        <textarea class="changable_input" @change="change(property_name, 'property', 'value')" v-html="property"></textarea>
                        <button style="background:tomato;color:#000;" @click="remove('property', property_name)">Удалить</button>
                    </div>
                </div>
                <div v-if="this_page" style="border-bottom:3px solid #000;padding-bottom:15px;">
                    <h2 class="header1" style="margin-top:15px;font-size:17px;padding:5px;border-radius:10px;background:#252525;":style="'color:'+photo.color+';'">Находится на странице</h2>
                    <a href="this_page" class="WhiteBlack" style="margin-top:15px;text-overflow: ellipsis;white-space: nowrap;overflow:hidden;direction:ltr;max-width:310px;">{{this_page}}</a>
                </div>
                <div v-if="prev_page" style="border-bottom:3px solid #000;padding-bottom:15px;">
                    <h2 class="header1" style="margin-top:15px;font-size:17px;padding:5px;border-radius:10px;background:#252525;" :style="'color:'+photo.color+';'">Пришёл со страницы</h2>
                    <a href="prev_page" class="WhiteBlack" style="margin-top:15px;text-overflow: ellipsis;direction:ltr;max-width:310px;white-space: nowrap;overflow:hidden;">{{prev_page}}</a>
                </div>
                <div style="border-bottom:3px solid #000;">
                    <h2 class="header1" style="font-size:17px;margin-top:15px;padding:5px;border-radius:10px;background:#252525;" :style="'color:'+photo.color+';'">Регион</h2>
                    <p class="WhiteBlack" style="margin-top:15px;margin-bottom:15px;word-break:break-all;" v-if="info['geo']">{{info["geo"]["country"]}} / {{info["geo"]["city"] || "не известно"}}</p>
                </div>
                <h2 v-if="g_type" class="header1" style="font-size:17px;margin-top:15px;padding:5px;border-radius:10px;background:#252525;" :style="'color:'+photo.color+';'">Информация из CRM</h2>
                <div>
                    <h2 v-if="g_name" class="header1" style="font-size:15px;margin-top:15px;">Имя</h2>
                    <p style="margin-left:0;margin-top:15px;word-break:break-all;" class="WhiteBlack" v-if="g_name" v-html="g_name"></p>
                </div>
                <div v-for="(column, index) in g_columns">
                    <h2 class="header1" style="direction: ltr;font-size:15px;margin-top:15px;" v-html="index"></h2> 
                    <p class="WhiteBlack" style="margin-left:0;direction: ltr;padding-bottom:15px;margin-top:15px;word-break:break-all;" v-if="typeof column !== 'object' || column === null">
                        {{
                            new Date(column) == 'Invalid Date' ? column : 
                            (column.indexOf('T') == -1 ? column.split('-').reverse().join('.') : 
                                (column.split('T')[0].split('-').reverse().join('.') + ' ' + column.split('T')[1].split(':').splice(0, 2).join(':'))
                            )
                        }}
                    </p>
                    <p  style="margin-left:0;direction: ltr;padding-bottom:15px;margin-top:15px;word-break:break-all;" v-else>{{column.value}} {{column.type == 1 ? '$' : (column.type == 2 ? '€' : '₽')}}</p>
                </div>
                <div :style="{'border-top': g_type ? '3px solid #000;padding-top:0px;' : ''}">
                    <h2 class="header1" style="font-size:17px;margin-top:15px;padding:5px;border-radius:10px;background:#252525;" :style="'color:'+photo.color+';'">Информация об устройстве</h2>
                    <p class="WhiteBlack" style="margin-top:15px;word-break:break-all;" class="WhiteBlack">{{info["user-agent"]}}</p>
                </div>
            </div>
            <div id="chat_settings_menu" class="bgblackwhite v-cloak-on flex-column-center" style="max-height:701px;min-height:701px;text-align:center;position:relative;" v-cloak>
                <span class="visitor_device2 v-cloak-block"></span>
                <p class="v-cloak-text2" style="margin:0;margin-top:40px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
                <p class="v-cloak-text2" style="margin:0;margin-top:30px;width:90%;"></p>
            </div>
        </div>
        <div id="upload_files_preview" class="v-cloak-off" v-cloak>
            <div :style="'border-color:'+photo.color+';'" class="preview_img_block" v-for="(file, index) in files">
                <span class="preview_remove" @click="removeFile(index)"><span></span><span></span></span>
                <span class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                <p :title="file.name" :style="'background-color:'+photo.color+';'" style="position:absolute;bottom:7px;left:0;color:#000;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.name}}</p>
            </div>
        </div>
    </div>
    <?php appendfooter(); ?>
</body>
<script src="/scripts/libs/howler.min.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>