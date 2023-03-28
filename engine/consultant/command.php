<?php
	session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
    if (!isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
    $info = check_user($connection);
    if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
    if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
    $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
    $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    mysqli_close($connection);
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
    <?php navigation('command', $info); ?>
    <div id="app">
        <?php team_msg_notification_body($file_path); ?>
        <div class="guests_info bgblackwhite" >
            <h3 style='color:#f90;font-size:20px;text-transform:uppercase;'>Список чатов</h3>
            <div style='display:inline-flex;flex-wrap:wrap;' class="v-cloak-off" v-cloak>
                <h3 class="WhiteBlack">Онлайн: <span style="color:lightgreen;" class="online_guest_count">{{get_count('online')}}</span></h3>
                <h3 class="WhiteBlack">Оффлайн: <span style="color:tomato;margin-right:10px;" class="offline_guest_count">{{get_count('offline')}}</span></h3>
            </div>
            <div style='display:inline-flex;width:230px;' class="v-cloak-on" v-cloak>
                <p class="v-cloak-text2"></p>
                <p style="margin-left:10px !important;"class="v-cloak-text2"></p>
            </div>
        </div>
        <div class="Online-List-User2 v-cloak-off"  id="visitors_wow" style="margin-top:0px;width:100%;border-radius:0;" v-if='userlist.hasOwnProperty("assistents")' v-cloak>
            <!-- общий чат -->
            <form class="OnlineUser wow bounceInUp bgblackwhite">
                <p class="WhiteBlack" style="margin-top:10px;font-size:20px;font-weight:bold;word-break:break-all;">Общий чат</p>
                <span  @click = "modal_window('open', 'public_chat_notification')" v-if="userlist['assistents']['public_room'].message" style="background:#fff url('/scss/imgs/email.png') no-repeat center center;background-size:130%;left:-15px;top:-15px;position:absolute;height:30px;width:30px;border-radius:50%;cursor:pointer;"></span>
                <div id="public_chat_notification" class="message_modal_window">
                    <span class="room_options remove_room" v-on:click="modal_window('close', 'public_chat_notification')">
                        <span></span>
                        <span></span>
                    </span>
                    <div style="display:flex;flex-direction:column;align-items:flex-start;justify-content:center;">
                        <p class="list-message-block-message" style="word-break: break-word;text-align:start;" v-if="userlist['assistents']['public_room'].message" v-html="find_emojis(userlist['assistents']['public_room'].message)"></p>
                        <div style="display:flex;flex-direction:column;" v-if = "userlist['assistents']['public_room'].message_adds">
                            <img v-for ="add in JSON.parse(userlist['assistents']['public_room'].message_adds)" :src="files_path + add" style="display:block;max-height:234px;max-width:234px;margin:10px;" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) == -1"></img>
                            <p style="margin:20px;margin-left:0;" v-for ="add in JSON.parse(userlist['assistents']['public_room'].message_adds)" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) != -1">
                                <a class="download_btn" :href="files_path + add" download  >Скачать {{add.split('.').slice(-1)[0]}}</a>
                            </p>
                        </div>
                    </div>
                </div>
                <button class="WhiteBlack" @click.prevent="changeChat('<?php echo $boss_id; ?>', 'public_room')" type="submit">Написать</button>
            </form>
            <!-- ассистенты -->
            <form class="OnlineUser wow bounceInUp bgblackwhite" v-for="(elem, index) in sort_mas(userlist['assistents'])"  v-if="index != 'public_room' && index != '<?php echo $personal_id; ?>'">
                <!-- photo -->
                <span class="OnlineUser-image2 bgblackwhite"v-bind:style='{backgroundImage: "url(<?php echo $file_path; ?>"+elem.photo+")"}'></span>
                <!-- info -->
                <p class="WhiteBlack" style="margin-top:20px;word-wrap:break-all;">{{elem.name}}</p>
                <p class="WhiteBlack" v-if='elem.hab' style="word-wrap:break-all;">{{elem.departament}}</p>
                <p style="font-size:1.3em;" :class="elem.status">{{elem.status}}</p>
                <!-- time -->
                <p class="WhiteBlack" v-if='elem.status == "offline" && elem.time != "новый"' >Последняя активность</p>
                <p class="WhiteBlack" style="color:#fff;" v-if='elem.status == "offline" && elem.time != "новый"' >{{elem.time.split(' ')[0].split('-').reverse().join('.') }} <span style="color:#f90;font-weight:bold;">{{elem.time.split(' ')[1].split(':').slice(0, 2).join(':')}}</span></p>
                <p class="WhiteBlack" style="color:#0ae;font-size:20px;font-weight:bold;" v-else-if="elem.status == 'offline'">новый</p>
                <!-- msg -->
                <span  @click = "modal_window('open', index.split('@')[0].replaceAll('.', '_'))" v-if="elem.message || elem.message_adds" style="background:#fff url('/scss/imgs/email.png') no-repeat center center;background-size:130%;left:-15px;top:-15px;position:absolute;height:30px;width:30px;border-radius:50%;cursor:pointer;"></span>
                <div :id="index.split('@')[0].replaceAll('.', '_')" class="message_modal_window">
                    <span class="room_options remove_room" v-on:click="modal_window('close', index.split('@')[0].replaceAll('.', '_'))">
                        <span></span>
                        <span></span>
                    </span>
                    <div style="display:flex;flex-direction:column;align-items:flex-start;justify-content:center;">
                        <p class="list-message-block-message" style="word-break: break-word;text-align:start;" v-if="elem.message" v-html="find_emojis(elem.message)"></p>
                        <div style="display:flex;flex-direction:column;" v-if = "elem.message_adds">
                            <img v-for ="add in JSON.parse(elem.message_adds)" :src="files_path + add" style="display:block;max-height:234px;max-width:234px;margin:10px;" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) == -1"></img>
                            <p style="margin:20px;margin-left:0;" v-for ="add in JSON.parse(elem.message_adds)" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) != -1">
                                <a class="download_btn" :href="files_path + add" download  >Скачать {{add.split('.').slice(-1)[0]}}</a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- btns -->
                <button class="WhiteBlack"  @click.prevent="changeChat(elem.id + '!@!@2@!@!' + '<?php echo $personal_id; ?>', index)" type="submit">Написать</button>
            </form>
        </div>
        <div :style='{"display":userlist.hasOwnProperty("assistents") ? "none" : "flex !important"}' class="Online-List-User2 v-cloak-on" style="margin-top:0px;width:100%;border-radius:0;" v-cloak>
            <form class="OnlineUser wow bounceInUp">
                <p class="v-cloak-text2" ></p>
                <button class="v-cloak-block" type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
            <form class="OnlineUser wow bounceInUp" >
                <span class="OnlineUser-image2 v-cloak-block" ></span>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <p class="v-cloak-text"></p>
                <button class="v-cloak-block"  type="button"></button>
            </form>
        </div>
    </div>
    <?php appendfooter(); ?>
</body>
<script src="/scripts/libs/howler.min.js"></script>
<script src="/scripts/libs/vue.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>