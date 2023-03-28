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
    <?php navigation('tasks', $info); ?>
    <div id="app" v-cloak>
        <?php team_msg_notification_body($file_path); ?>
        <div class="crm_container" style="margin-top:20px;">
            <div class="crm_nav v-cloak-off" style="z-index: 2; "  v-if="movement_panel" v-cloak>
                <a href="/engine/consultant/tasks" class="crm_tasks active">
                    задачи 
                    <span class="task_count complete_task_count">{{complete_task_count}}</span>
                    <span class="task_count uncomplete_task_count">{{uncomplete_task_count}}</span>
                </a>
                <a class="new_crm_table" @click="new_table_mode = !new_table_mode" v-if="!new_table_mode">новая таблица</a>
                    <a class="new_crm_table" v-if="new_table_mode">
                        <span class="close" @click="new_table_mode = false;"></span>
                        <input class="changable_input" placeholder="имя таблицы" style="margin:0;margin-left:10px;margin-right:10px;height:30px;background:url(/scss/imgs/classy_fabric.png) repeat center center;border-radius:10px;" type="text"/> 
                        <span @click="add_table" title="добавить" class="close" style="background-image: url(/scss/imgs/additem.png);"></span>
                    </a>
                <a v-for="table in tables" :href="'/engine/consultant/crm?type='+table" class="crm_clients"><p style="margin:0;" v-html="table"></p> <span class="task_count">{{counters[table]||0}}</span></a>
            </div>
            <div class="v-cloak-on v-cloak-block crm_nav" v-cloak></div>
        </div>
        <div class="app_row" style="margin-top:0;">
            <div v-if="tasks_loaded" class="Online-List-User2 v-cloak-off" style="width:100%" v-cloak>
                <p v-if="Object.keys(tasks).length == 0" style="font-size:25px;height:650px;width:100%;display:flex;align-items:center;justify-content:center;">У вас нет созданных задач !</p>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in task_sort(tasks)" v-if="check_selected(task.selected, index)">
                    <span :class="[{'task_status_completed': new Date(task.time) < new Date()},{'task_status': new Date(task.time) > new Date()}]"></span>
                    <span class="room_options room_options_close" :class="[{'unactive_task': new Date(task.time) < new Date()},{'active_task': new Date(task.time) > new Date()}]" @click = "room_list($event.target)">
                        <span :class="[{'unactive_task_span': new Date(task.time) < new Date() },{'active_task_span': new Date(task.time) > new Date()}]"></span>
                        <span :class="[{'unactive_task_span': new Date(task.time) < new Date() },{'active_task_span': new Date(task.time) > new Date()}]"></span>
                        <span :class="[{'unactive_task_span': new Date(task.time) < new Date() },{'active_task_span': new Date(task.time) > new Date()}]"></span>
                    </span>
                    <span class="room_option delete_task" :class="[{'unactive_task': new Date(task.time) < new Date()},{'active_task': new Date(task.time) > new Date()}]" @click = "remove_task(index)"><span :class="[{'unactive_task_span': new Date(task.time) < new Date()},{'active_task_span': new Date(task.time) > new Date()}]"></span><span :class="[{'unactive_task_span': new Date(task.time) < new Date()},{'active_task_span': new Date(task.time) > new Date()}]"></span></span>
                    <p class="task_header"  style="margin-top:25px;">Время задачи</p>
                    <p class="task_time" >{{task.time.split(' ')[0].split('-').reverse().join('.')}} <span :style="{'color': new Date(task.time) > new Date() ? '#f90' : 'lightgreen'}" style="font-weight:bold;">{{task.time.split(' ')[1].split(':').splice(0, 2).join(':')}}</span></p>
                    <p class="task_header" >Тип задачи</p>
                    <p v-if="task.type" class="task_type" >Публичная</p>
                    <p v-else class="task_type" >Личная</p>
                    <p class="task_header" >Задача для выбранных</p>
                    <div class="task_for" style="border-color:#eee !important;" :class="[{'unactive_task': new Date(task.time) < new Date()},{'active_task': new Date(task.time) > new Date()}]">
                        <div class="task_for_choosen"  v-if="items?.[select]" v-for="select in task.selected" @click="find(select, task.table)">
                            <div class="task_for_choosen_photo" :style="'background-image: url(/crm_files/'+items[select].helper_photo+');'"></div>
                            <div class="task_for_choosen_name"><p style="margin:0;" v-html='items[select]["helper_name"]'></p></div>
                        </div>
                    </div>
                    <p class="task_header" >Задача</p>
                    <p class="task_task" >{{task.text}}</p>
                </div>
            </div>
            <div class="Online-List-User2 v-cloak-on" :style='{"display":tasks_loaded ? "none" : "flex !important"}' style="width:100%" v-cloak>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
                </div>
                <div class="task_box wow bounceInUp"  v-for="(task, index) in tasks" v-if="check_selected(task.selected, index)">
                    <span class="task_load v-cloak-block"></span>
                    <span class="room_options room_options_close v-cloak-block" ></span>
                    <p style="margin-top:30px !important;" class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <p class="v-cloak-text" ></p>
                    <div class="task_for v-cloak-block" ></div>
                    <p class="v-cloak-text" ></p>
                    <p class="task_task v-cloak-block" ></p>
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