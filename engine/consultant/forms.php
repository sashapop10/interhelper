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
    $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
    $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
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
    <?php navigation('forms', $info); ?>
    <div id="app" v-cloak>
        <?php team_msg_notification_body($file_path); ?>
        <div class="guests_info bgblackwhite">
            <h3 style='color:#f90;font-size:20px;text-transform:uppercase;margin-left:60px;'>Обратная связь</h3>
            <div style='display:inline-flex;flex-wrap:wrap;'></div>
        </div>
        <div class="app_row" style="margin-top:0px;z-index:1;">
            <div class="Online-List-User2 v-cloak-off" v-if='userlist.hasOwnProperty("rooms")' id="visitors_wow" style="width:100%;" v-cloak>
                <p v-if="Object.keys(forms).length == 0" style="font-size:25px;height:100%;width:100%;display:flex;align-items:center;justify-content:center;">Пока что никто не воспользовался обратной связью</p>
                <div class="user_forms_container" v-for="(user_forms_dates, index) in forms" :style="'border: 3px solid '+userlist?.rooms?.[user_forms_dates[`index`]]?.photo?.color||`#0ae`+';'">
                    <div class="user_form_forms_container">
                        <div class="date_div" v-if="date != 'index'" v-for="(user_forms, date) in user_forms_dates" >
                            <span class="form_date">{{new Date(date * 1000).toISOString().split('T')[0].split('-').reverse().join('.')}}</span>
                            <div v-for="form in user_forms" class="user_form_forms_container_form">
                               <p :style="'border-color: '+userlist?.rooms?.[user_forms_dates[`index`]]?.photo?.color||`#0ae`+';'" v-if="form['name']">{{form['name']}}</p>
                               <p :style="'border-color: '+userlist?.rooms?.[user_forms_dates[`index`]]?.photo?.color||`#0ae`+';'" v-if="form['phone']">{{form['phone']}}</p>
                               <p :style="'border-color: '+userlist?.rooms?.[user_forms_dates[`index`]]?.photo?.color||`#0ae`+';'" v-if="form['email']">{{form['email']}}</p>
                               <p :style="'border-color: '+userlist?.rooms?.[user_forms_dates[`index`]]?.photo?.color||`#0ae`+';'" v-if="form['message']" v-html="form['message']"></p>
                               <span >{{form['time'].split(':').slice(0, 2).join(':')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" v-if="userlist?.rooms?.[user_forms_dates['index']]">
                        <!-- PHOTO -->
                        <span v-if="!userlist.rooms[user_forms_dates[`index`]].crm" class="OnlineUser-image2" style="background-color:#252525;"  :style='"background-image:url(/visitors_photos/"+userlist.rooms[user_forms_dates[`index`]].photo.img+");background-size:80%;background-color:"+userlist.rooms[user_forms_dates[`index`]].photo.color+";"'></span>
                        <span v-else-if="crm_items[userlist.rooms[user_forms_dates[`index`]].crm].hasOwnProperty(user_forms_dates[`index`])" class="OnlineUser-image2" style="background-color:#252525;"  :style='{backgroundImage: "url(/crm_files/"+crm_items[userlist.rooms[user_forms_dates[`index`]].crm][user_forms_dates[`index`]].helper_photo+")"}'></span>
                        <!-- DOMAIN -->
                        <p class='user_domain' >
                            <span v-for = "domain in userlist.rooms[user_forms_dates[`index`]].domains_list.domains">{{domain}}</span>
                        </p>
                        <!-- CRM -->
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].crm"  style = "color:#f90;font-size:20px;">CRM</p>
                        <p style="color:tomato;font-weight:bold;font-size:17px;" v-if="userlist.rooms[user_forms_dates[`index`]].ban_status">Заблокирован</p>
                        <!-- PAGE -->
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].actual_page && userlist.rooms[user_forms_dates[`index`]].status == 'online'" style="color:#f90;">Сейчас на странице</p>
                        <a v-if="userlist.rooms[user_forms_dates[`index`]].actual_page && userlist.rooms[user_forms_dates[`index`]].status == 'online'" style="max-width:180px;min-height:18px;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;cursor:pointer;color:#fff;" :href="userlist.rooms[user_forms_dates[`index`]].actual_page" :title="userlist.rooms[user_forms_dates[`index`]].actual_page">{{userlist.rooms[user_forms_dates[`index`]].actual_page}}</a>
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].previous_page && userlist.rooms[user_forms_dates[`index`]].status == 'online'" style="color:#f90;">Пришёл с</p>
                        <a v-if="userlist.rooms[user_forms_dates[`index`]].previous_page && userlist.rooms[user_forms_dates[`index`]].status == 'online'" style="max-width:180px;min-height:18px;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;cursor:pointer;color:#fff;" :href="userlist.rooms[user_forms_dates[`index`]].previous_page" :title="userlist.rooms[user_forms_dates[`index`]].previous_page">{{userlist.rooms[user_forms_dates[`index`]].previous_page}}</a>
                        <!-- INFO -->
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].info.advertisement"  style="color:tomato;font-size:17px;">{{userlist.rooms[user_forms_dates[`index`]].info.advertisement}}</p>
                        <p style="color:#0ae;word-break:break-word;" v-if=' !userlist.rooms[user_forms_dates[`index`]].room_name '>Клиент {{userlist.rooms[user_forms_dates[`index`]].info["ip"]}}</p>
                        <p v-else style="color:#0ae;word-break:break-word;">{{userlist.rooms[user_forms_dates[`index`]].room_name}}</p>
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].info['geo'] != null">{{userlist.rooms[user_forms_dates[`index`]].info["geo"]["country"] || "Неизвестно"}} / {{userlist.rooms[user_forms_dates[`index`]].info["geo"]["city"] || "Неизвестно"}}</p>
                        <p v-else >Неизвестно</p>
                        <p  v-if='userlist.rooms[user_forms_dates[`index`]].user_name' style="color:#f90;word-break:break-word;">{{userlist.rooms[user_forms_dates[`index`]].user_name}}</p>
                        <p  v-if='userlist.rooms[user_forms_dates[`index`]].user_email' style="color:#f90;word-break:break-word;">{{userlist.rooms[user_forms_dates[`index`]].user_email}}</p>
                        <p  v-if='userlist.rooms[user_forms_dates[`index`]].user_phone' style="color:#f90;word-break:break-word;">{{userlist.rooms[user_forms_dates[`index`]].user_phone}}</p>
                        <p style="font-size:1.3em;  " :class="userlist.rooms[user_forms_dates[`index`]].status">{{userlist.rooms[user_forms_dates[`index`]].status}}</p>
                         <!-- обслуживающие -->
                        <div class="consulation_list_block" v-if = 'userlist.rooms[user_forms_dates[`index`]].serving_list.assistents.length > 0 '>
                            <h2>Обслуживают</h2>
                            <div class="consulation_list">
                                <div v-for="assistent in userlist.rooms[user_forms_dates[`index`]].serving_list.assistents" v-if="assistents[assistent]">
                                    <span :style='"background-image:url(<?php echo $file_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
                                    <p>{{assistents[assistent]["name"]}}</p>
                                </div>
                            </div>
                        </div>
                        <!-- обслуживали -->
                        <div class="consulation_list_block" v-if = 'userlist.rooms[user_forms_dates[`index`]].served_list.assistents.length > 0 '>
                            <h2>Обслуживали</h2>
                            <div class="consulation_list">
                                <div v-for="assistent in userlist.rooms[user_forms_dates[`index`]].served_list.assistents" v-if="assistents[assistent]">
                                    <span :style='"background-image:url(<?php echo $file_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
                                    <p>{{assistents[assistent]["name"]}}</p>
                                </div>
                            </div>
                        </div>
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].typing" style="word-break:break-all;"><p style="font-weight:bold;font-size:17px;" v-if="userlist.rooms[user_forms_dates[`index`]].typing">Печатает:</p> 
                        <p style="word-break:break-all;margin-bottom:10px;" v-html="userlist.rooms[user_forms_dates[`index`]].typing"></p>
                        <p v-if="userlist.rooms[user_forms_dates[`index`]].lastActivityTime != '' && userlist.rooms[user_forms_dates[`index`]].lastActivityTime != null && userlist.rooms[user_forms_dates[`index`]].lastActivityTime != undefined && userlist.rooms[user_forms_dates[`index`]].status != 'online'" style="color:#fff;">Последняя активность</p>
                        <p  v-if="userlist.rooms[user_forms_dates[`index`]].lastActivityTime != '' && userlist.rooms[user_forms_dates[`index`]].lastActivityTime != null && userlist.rooms[user_forms_dates[`index`]].lastActivityTime != undefined && userlist.rooms[user_forms_dates[`index`]].status != 'online'"  style="color:#fff;"><span style="font-weight:bold;color:#f90;">{{userlist.rooms[user_forms_dates[`index`]].lastActivityTime.split(' ')[1].split(':').slice(0, 2).join(':')}}</span> {{userlist.rooms[user_forms_dates[`index`]].lastActivityTime.split(' ')[0].split('-').reverse().join('.') }}</p>
                        <!-- Btns -->
                        <button v-if='userlist.rooms[user_forms_dates[`index`]].ban_status && userlist.rooms[user_forms_dates[`index`]].messages_exist' style="color:#0ae;border-color:#0ae;" @click.prevent="ban_dialog(user_forms_dates[`index`])" type="submit">Переписка</button> 
                        <button v-else-if='userlist.rooms[user_forms_dates[`index`]].ban_status && !userlist.rooms[user_forms_dates[`index`]].messages_exist' style="color:#0ae;border-color:#0ae;" type="button">Сообщений нет</button> 
                        <button v-else-if='userlist.rooms[user_forms_dates[`index`]].serving_list.assistents.length == 0 && userlist.rooms[user_forms_dates[`index`]].served_list.assistents.indexOf("<?php echo $personal_id; ?>") == -1'style="color:lightgreen;border-color:lightgreen;"  @click.prevent="consultation(user_forms_dates[`index`], 'start')" type="submit">Начать чат</button>   
                        <button v-else-if='userlist.rooms[user_forms_dates[`index`]].serving_list.assistents.indexOf("<?php echo $personal_id;?>") != -1' @click.prevent="consultation(user_forms_dates[`index`], 'continue')" type="submit">Продолжить чат</button>
                        <button v-else-if='userlist.rooms[user_forms_dates[`index`]].served_list.assistents.indexOf("<?php echo $personal_id;?>") != -1' style="color:#f90;border-color:#f90;" @click.prevent="consultation(user_forms_dates[`index`], 'restart')" type="submit">Возобновить чат</button> 
                        <button v-if='userlist.rooms[user_forms_dates[`index`]].serving_list.assistents.indexOf("<?php echo $personal_id; ?>") != -1' style="color:tomato;border-color:tomato;" @click.prevent="consultation(user_forms_dates[`index`], 'finish')" type="submit">Завершить чат</button>   
                    </div>
                </div>
            </div>
            <div :style='{"display":userlist.hasOwnProperty("rooms") ? "none" : "flex !important"}' class="Online-List-User2 v-cloak-on" id="visitors_wow" style="width:100%;" v-cloak>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
                <div class="user_forms_container">
                    <div class="user_form_forms_container">
                        <div class="date_div">
                            <span class="form_date v-cloak-block" style="padding:5px;border-radius:10px"><?php echo date('d.m.Y') ?></span>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                            <div class="user_form_forms_container_form">
                               <p style="border-bottom-left-radius:0 !important;border-bottom-right-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-radius:0 !important;" class="v-cloak-text2"></p>
                               <p style="border-top-left-radius:0 !important;border-top-right-radius:0 !important;" class="v-cloak-text2"></p>
                            </div>
                        </div>
                    </div>
                    <div class="user_form_user_info" style="padding:10px;">
                        <span class="OnlineUser-image2 v-cloak-block" style="top:10px;"></span>
                        <p style="margin-top:60px !important;" class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <p class="v-cloak-text"></p>
                        <button style="margin-top:45px !important;" type="button" class="v-cloak-block"></button>   
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php appendfooter(); ?>
    <script src="/scripts/libs/howler.min.js"></script>
    <script src="/scripts/libs/vue.js"></script>
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
    <script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>