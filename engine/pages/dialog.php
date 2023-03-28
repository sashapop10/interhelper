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
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/client_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
    <?php navigation('dialogs', $info); ?>
    <div id="app" v-cloak>
        <div id="chat_header" class="card-header text-white bgblackwhite">
            <div class="chat-header-info v-cloak-off" v-cloak>
                <span v-cloak v-if="g_type"
                    style="height:50px;width:50px;border-radius:50%;background-size:cover;background-position:center;background-repeat:no-repeat;margin-right:10px;"
                    :style="'background-image:url(/crm_files/'+g_photo+');background-color:'+photo.color+';'"
                ></span>
                <span v-else
                    style="height:50px;width:50px;border-radius:50%;background-size:80%;background-position:center;background-repeat:no-repeat;margin-right:10px;"
                    :style="'background-image:url(/visitors_photos/'+photo.img+');background-color:'+photo.color+';'"
                ></span>
                <div v-cloak style="display:flex;flex-direction:column;align-items:flex-start;">
                    <h3 style="color:#f90;" v-if="g_type">CRM</h3>
                    <div style='display:inline-flex;flex-wrap:wrap;'>
                        <h4 class="WhiteBlack">Чат <span style="color:#0ae;margin-right:10px;">{{chat_name}}</span></h4>
                    </div>
                </div>
            </div>
            <div class="chat-header-info v-cloak-on" v-cloak>
                <span  style="height:50px;min-width:50px;border-radius:50%;margin-right:10px;" class="v-cloak-block"></span>
                <p class="v-cloak-text2" style="width:200px;"></p>
            </div>
            <span style="background-color:#252525;" class="break_chat chat_header_btn v-cloak-off" v-on:click="exit" v-cloak><span></span><span></span></span>
            <span class="chat_header_btn v-cloak-block v-cloak-on" v-cloak></span>
        </div>
        <div class="app_row" >
            <div id="chat_container" style="width:100%" class="card bg-info" >
                <ul id="chat_body" class="list-group list-group-flush text-right">
                    <li class="list-message wow bounceInDown v-cloak-off" v-for="(message, message_index) in messages" v-cloak>
                        <span class="date" v-if="load_date(message.time.split(' ')[0].split('-').reverse().join('.'))">
                            <span class="bgblackwhite WhiteBlack">{{message.time.split(' ')[0].split('-').reverse().join('.')}}</span>
                        </span>
                        <div style="width:100%;display:flex;" :class="{'message-by-me':message.sender && message.sender != 'offline_form' }">
                            <span class="list-message-block bgblackwhite">
                                <div v-if="message.sender && message.sender != 'offline_form' && message.sender != 'notification' && ((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg bgblackwhite" v-bind:style='{ backgroundImage: "url(<?php echo $file_path; ?>"+message.photo+")"}'></div>
                                <div v-else-if="message.sender != 'notification' && g_type && ((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg" :style='"background-image:url(/crm_files/"+g_photo+");background-color:"+photo.color+";"'></div>
                                <div v-else-if="message.sender == 'notification'" class="thisuserimg" v-bind:style='"background-image:url(/notifications_photos/notification_photos/"+message.photo+");"'></div>
                                <div v-else-if="((messages[message_index-1]||{'sender': ''}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2])" class="thisuserimg" v-bind:style='"background-image:url(/visitors_photos/"+photo.img+");background-size:80%;background-color:"+photo.color+";"'></div>
                                <div class="list-message-info" style="width:100%;" :style="'border-color:'+photo.color+';'" v-if="message.sender != 'offline_form'">
                                    <small v-if="message.sender != 'offline_form'" style="justify-content:space-between;width:100%;display:inline-flex;" class="WhiteBlack"><span v-html="(message.user||chat_name||(g_name == 'new' ? null : g_name)||'Клиент ')"></span> {{message.sender == 'notification' ? '(Уведомление)' : ''}} <span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small v-else class="message_send_time"><span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small class="list-message-block-mail" v-if="message.sender" class="WhiteBlack" v-html="message.departament"></small>
                                </div>
                                <div class="form-info" :style="{'border-color': photo.color }" style="background:rgba(0,0,0,0.8);border-top-right-radius:10px;border-top-left-radius:10px;" v-else>
                                    <small :style="'border-color:'+photo.color+';'" class="message_send_time" style="font-weight:bold;"><span style="color:#f90;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span> отправлена форма обратной связи</small>
                                    <small :style="'border-color:'+photo.color+';'" v-if="message.user">{{message.user}}</small>
                                    <small :style="'border-color:'+photo.color+';'" v-if="message.email">{{message.email}}</small>
                                    <small :style="'border-color:'+photo.color+';'" v-if="message.phone">{{message.phone}}</small>
                                </div>
                                <p class="list-message-block-message" style="word-break: break-word;" :style="[{'color': message.mode == 'js_mode' || message.mode == 'invisible' ? '#0ae' : '#fff'}, {'border-color': photo.color }]" :class="message.sender == 'offline_form' ? 'form_message' : 'WhiteBlack'" v-if="message.message" v-html="find_emojis((message.mode == 'DOM' ? htmldecoder(message.message) : message.message))"></p>
                                <div style="display:flex;flex-direction:column;" v-if = "message.message_adds">
                                    <img v-for ="add in JSON.parse(message.message_adds)" :src="(message.sender == 'notification' ? '/notifications_photos/notification_adds/' : files_path) + add" style="display:block;max-height:250px;max-width:250px;margin:10px;" v-if="regexp?.indexOf(add.substr(add.lastIndexOf('.'), add.length)) == -1"></img>
                                    <p style="margin:20px;margin-left:0;" v-for ="add in JSON.parse(message.message_adds)" v-if="regexp?.indexOf(add.substr(add?.lastIndexOf('.'), add.length)) != -1">
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
    <script src="/scripts/libs/howler.min.js"></script>
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
    <script src="/scripts/libs/vue.js"></script>
    <script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>