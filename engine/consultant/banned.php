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
</head>
<body>
    <?php navigation('banned', $info); ?>
    <div id="app" v-cloak>
        <?php team_msg_notification_body($file_path); ?>
        <div class="guests_info bgblackwhite">
            <h3 style='color:#f90;font-size:20px;text-transform:uppercase;margin-left:10px;'>Чёрный список</h3>
            <div  class="v-cloak-off" v-cloak>
                <h3 class="WhiteBlack">Заблокировано: <span style="color:tomato;">{{Object.keys(userlist.rooms||{}).length}}</span></h3>
            </div>
            <div style='display:inline-flex;width:230px;' class="v-cloak-on" v-cloak>
                <p class="v-cloak-text2"></p>
            </div>
        </div>
        <div class="app_row">
            <div class="Online-List-User2 v-cloak-off" style="width:100%;" v-if='userlist.hasOwnProperty("rooms")' id="visitors_wow" v-cloak>
                <p v-if="Object.keys(userlist['rooms']||{}).length == 0" style="font-size:25px;height:100%;width:100%;display:flex;align-items:center;justify-content:center;">Чёрный список пуст</p>
                <form class="OnlineUser wow bounceInUp" v-for="(elem, index) in userlist['rooms']" :style="'border-color:'+elem.photo.color+';'">
                    <!-- PHOTO -->
                    <span v-if="!elem.crm" class="OnlineUser-image2" style="background-color:#252525;"  :style='"background-image:url(/visitors_photos/"+elem.photo.img+");background-color:"+elem.photo.color+";background-size:80%;"'></span>
                    <span v-else class="OnlineUser-image2" style="background-color:#252525;"  :style='{backgroundImage: "url(/crm_files/"+crm_items[elem.crm][elem.room_id].helper_photo+")"}'></span>
                    <!-- control -->
                    <span :style="'border-color:'+elem.photo.color+';'" class="room_options room_options_close" @click = "room_list($event.target)">
                        <span :style="'background-color:'+elem.photo.color+';'"></span>
                        <span :style="'background-color:'+elem.photo.color+';'"></span>
                        <span :style="'background-color:'+elem.photo.color+';'"></span>
                    </span>
                    <span :style="'border-color:'+elem.photo.color+';'" class="ban_room room_option" @click = "unban_room(index)">
                        <svg :style="'fill:'+elem.photo.color+';'" version="1.1" id="unlock_btn" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 438.533 438.533" style="enable-background:new 0 0 438.533 438.533;" xml:space="preserve"><g><path d="M375.721,227.259c-5.331-5.331-11.8-7.992-19.417-7.992H146.176v-91.36c0-20.179,7.139-37.402,21.415-51.678c14.277-14.273,31.501-21.411,51.678-21.411c20.175,0,37.402,7.137,51.673,21.411c14.277,14.276,21.416,31.5,21.416,51.678c0,4.947,1.807,9.229,5.42,12.845c3.621,3.617,7.905,5.426,12.847,5.426h18.281c4.945,0,9.227-1.809,12.848-5.426c3.606-3.616,5.42-7.898,5.42-12.845c0-35.216-12.515-65.331-37.541-90.362C284.603,12.513,254.48,0,219.269,0c-35.214,0-65.334,12.513-90.366,37.544c-25.028,25.028-37.542,55.146-37.542,90.362v91.36h-9.135c-7.611,0-14.084,2.667-19.414,7.992c-5.33,5.325-7.994,11.8-7.994,19.414v164.452c0,7.617,2.665,14.089,7.994,19.417c5.33,5.325,11.803,7.991,19.414,7.991h274.078c7.617,0,14.092-2.666,19.417-7.991c5.325-5.328,7.994-11.8,7.994-19.417V246.673C383.719,239.059,381.053,232.591,375.721,227.259z"/></g></svg>
                    </span>
                    <!-- DOMAINS -->
                    <p class='user_domain' >
                        <span v-for="domain in elem.domains_list.domains">{{domain}}</span>
                    </p>
                    <!-- CRM -->
                    <p v-if="elem.crm"  style = "color:#f90;font-size:20px;">CRM</p>
                    <!-- INFO -->
                    <p style="color:#0ae;word-break:break-word;">Клиент {{elem.info["ip"]}}</p>
                    <div class="room_property" v-if='Object.keys(elem.properties.properties).length > 0' v-for="(property_value, property_name) in elem.properties.properties" >
                        <p style="color:#f90;word-break:break-word;" v-html="property_name"></p>
                        <p style="color:#fff;word-break:break-word;" v-html="property_value"></p>
                    </div>
                    <p>
                        Время на сайте
                        {{
                            ((elem.session_time||0) / 1000 / 60).toFixed(1)
                        }} мин
                    </p>
                    <p>Посещений {{elem.visits}}</p>
                    <!-- ban info-->
                    <p style="color:#f90;word-break:break-all;">Забанил</p>
                    <div style="color:#fff;word-break:break-word;display:inline-flex;align-items:center;">
                        <span :style='"background-image:url(<?php echo $file_path; ?>"+assistents[elem.bannedBy][`photo`]+");"' class="cons_photo"></span>
                        <p style="margin-left:5px;">{{assistents[elem.bannedBy]["name"]}}</p>
                    </div>
                    <p style="color:#f90;word-break:break-all;">Причина бана</p>
                    <p style="color:#fff;word-break:break-word;">{{elem.reason}}</p>
                    <!-- consulation -->
                    <div class="consulation_list_block" v-if = 'elem.served_list.assistents.length > 0 '>
                        <h2>Обслуживали</h2>
                        <div class="consulation_list">
                            <div v-for="assistent in elem.served_list.assistents" v-if="assistents[assistent]">
                                <span :style='"background-image:url(<?php echo $file_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
                                <p style="margin-left:5px;">{{assistents[assistent]["name"]}}</p>
                            </div>
                        </div>
                    </div>
                    <p v-if="elem.lastActivityTime" style="color:#f90;">Время бана</p>
                    <p  v-if="elem.lastActivityTime" >{{elem.lastActivityTime.split(' ')[0].split('-').reverse().join('.')}} <span style="font-weight:bold;color:#f90;">{{elem.lastActivityTime.split(' ')[1].split(':').splice(0, 2).join(':')}}</span></p>
                    <!-- btns -->
                    <button v-if="elem.messages_exist" :style="'border-color: '+elem.photo.color+';'" @click.prevent="changeChat(index)">Переписка</button>
                    <button type="button" v-else>Сообщений нет</button>
                </form>
            </div>
            <div :style='{"display":userlist.hasOwnProperty("rooms") ? "none" : "flex !important"}' class="Online-List-User2 v-cloak-on" style="margin-top:0px;width:100%;border-radius:0;" v-cloak>
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
    </div>
    <?php appendfooter(); ?>
</body>
<script src="/scripts/libs/howler.min.js"></script>
<script src="/scripts/libs/vue.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>