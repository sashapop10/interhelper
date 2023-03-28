<?php
    session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    if (!isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
    $info = check_user($connection);
    if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
    if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
    mysqli_close($connection);
    $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
    $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    $room_from_get = $_GET['room'];
    if($boss_id != explode('!@!@2@!@!', $room_from_get)[0]){ header("Location: /engine/consultant/hub"); exit;}
    $name_from_get = $_GET['name'];
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
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
    <?php navigation('banned', $info); ?>
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
                    <h3 class="WhiteBlack" style="color:#f90;" v-if="g_type">CRM</h3>
                    <div style='display:inline-flex;flex-wrap:wrap;'>
                        <h4  class="WhiteBlack">Чат <span :style="'color:'+photo.color+';'" style="margin-right:10px;padding:5px;border-radius:10px;background-color:#252525;"><?php echo $name_from_get; ?></span></h4>
                    </div>
                </div>
            </div>
            <div class="chat-header-info v-cloak-on" v-cloak>
                <span  style="height:50px;min-width:50px;border-radius:50%;margin-right:10px;" class="v-cloak-block"></span>
                <p class="v-cloak-text2" style="width:200px;"></p>
            </div>
            <div class="chat-header-info v-cloak-off" v-cloak>
                <span :style="'background-color:#252525;border-color:'+photo.color+';'" data-tooltip="Разблокировать" @click ="unban_room" class="ban_room chat_header_btn">
                    <svg :style="'fill:'+photo.color+';'" version="1.1" id="unlock_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 438.533 438.533" style="enable-background:new 0 0 438.533 438.533;" xml:space="preserve"><g><path d="M375.721,227.259c-5.331-5.331-11.8-7.992-19.417-7.992H146.176v-91.36c0-20.179,7.139-37.402,21.415-51.678c14.277-14.273,31.501-21.411,51.678-21.411c20.175,0,37.402,7.137,51.673,21.411c14.277,14.276,21.416,31.5,21.416,51.678c0,4.947,1.807,9.229,5.42,12.845c3.621,3.617,7.905,5.426,12.847,5.426h18.281c4.945,0,9.227-1.809,12.848-5.426c3.606-3.616,5.42-7.898,5.42-12.845c0-35.216-12.515-65.331-37.541-90.362C284.603,12.513,254.48,0,219.269,0c-35.214,0-65.334,12.513-90.366,37.544c-25.028,25.028-37.542,55.146-37.542,90.362v91.36h-9.135c-7.611,0-14.084,2.667-19.414,7.992c-5.33,5.325-7.994,11.8-7.994,19.414v164.452c0,7.617,2.665,14.089,7.994,19.417c5.33,5.325,11.803,7.991,19.414,7.991h274.078c7.617,0,14.092-2.666,19.417-7.991c5.325-5.328,7.994-11.8,7.994-19.417V246.673C383.719,239.059,381.053,232.591,375.721,227.259z"/></g></svg>
                </span>
                <span style="background-color:#252525;margin-left:10px;" class="break_chat chat_header_btn" v-on:click="exit"><span></span><span></span></span>
            </div>
            <div class="chat-header-btns v-cloak-on" v-cloak>
                <span class="chat_header_btn v-cloak-block"></span>
                <span class="chat_header_btn v-cloak-block" style="margin-left:10px;"></span>
                <span class="chat_header_btn v-cloak-block" style="margin-left:10px;"></span>
            </div>
        </div>
        <div class="app_row">
            <div id="chat_container" style="width:100%" class="card bg-info" >
                <ul id="chat_body" class="list-group list-group-flush text-right">
                     <li class="list-message wow bounceInDown v-cloak-off" v-for="(message, message_index) in messages" v-cloak>
                        <span class="date" v-if="load_date(message.time.split(' ')[0].split('-').reverse().join('.'))" >
                            <span class="bgblackwhite WhiteBlack">{{message.time.split(' ')[0].split('-').reverse().join('.')}}</span>
                        </span>
                        <div style="width:100%;display:flex;" :class="{'message-by-me':message.sender && message.sender != 'offline_form' }">
                            <span class="list-message-block bgblackwhite">
                                <div v-if="message.sender && message.sender != 'offline_form' && message.sender != 'notification' && ((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg bgblackwhite" v-bind:style='{ backgroundImage: "url(<?php echo $file_path; ?>"+message.photo+")"}'></div>
                                <div v-else-if="message.sender != 'notification' && g_type && ((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg bgblackwhite" :style='"background-image:url(/crm_files/"+g_photo+");background-color:"+photo.color+";"'></div>
                                <div v-else-if="message.sender == 'notification'" class="thisuserimg" v-bind:style='{ backgroundImage: "url(/notifications_photos/notification_photos/"+message.photo+")"}'></div>
                                <div v-else-if="((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg" v-bind:style='"background-image:url(/visitors_photos/"+photo.img+");background-size:80%;background-color:"+photo.color+";"'></div>
                                <div class="list-message-info" v-if="message.sender != 'offline_form'">
                                    <small class="WhiteBlack" v-if="message.sender != 'offline_form'"><span v-html="(message.user||'<?php echo $name_from_get; ?>'||(g_name == 'new' ? null : g_name)||'Клиент ')"></span> <span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small v-else class="message_send_time"><span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small class="list-message-block-mail WHiteBlack" v-if="message.sender" v-html="message.departament"></small>
                                </div>
                                <div class="form-info" :style="'border-color:'+photo.color+';'" style="background:rgba(0,0,0,0.8);border-top-right-radius:10px;border-top-left-radius:10px;" v-else>
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
            </div>
        </div> 
    </div>
    <?php appendfooter(); ?>
</body>
<script src="/scripts/libs/howler.min.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>