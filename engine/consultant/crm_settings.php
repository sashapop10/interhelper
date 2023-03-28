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
  if(isset($_SESSION['boss'])) $boss_id = $_SESSION['boss'];
  if(isset($_SESSION['employee'])) $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
  if(!isset($_GET["type"])){ header("Location: /engine/consultant/crm"); exit; }
  else {
    $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
    $row = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
    if(!isset($row[$_GET['type']])){ mysqli_close($connection); header("Location: /engine/consultant/crm"); exit; } 
  }
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
    <?php navigation('crm', $info); ?>
    <div id="app" v-cloak>
        <?php team_msg_notification_body($file_path); ?>
        <div class="app_row">
            <div class="crm_container">
                <div class="crm_nav v-cloak-off" style="z-index: 2; "  v-if="movement_panel" v-cloak>
                    <a href="/engine/consultant/tasks" class="crm_tasks">
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
                    <a v-for="table in tables" :href="'/engine/consultant/crm?type='+encodeURI(table)" :class="'crm_clients ' + (get_name == table ? 'active' : '')"><p style="margin:0;" v-html="table"></p> <span class="task_count">{{counters[table]||0}}</span></a>
                </div>
                <div class="v-cloak-on v-cloak-block crm_nav" v-cloak></div>
                <div class="crm_opt_panel">
                    <a href="#" class="crm_opt_btn active">Настройки</a>
                    <a :href="'/engine/consultant/crm?type='+encodeURI(get_name)" class="crm_opt_clients_list">Список</a>
                </div>
                <div class="v-cloak-on  v-cloak-block crm_opt_panel" v-cloak></div>
                <div class="crm_menu_container">
                    <div class="crm_left_side_panel bgblackwhite">
                        <div class="crm_left_side_panel_header bgblackwhite v-cloak-off" v-cloak>
                            <span @click="table_remove" title="Удалить таблицу" style="cursor:pointer;background-image:url(/scss/imgs/remove.png);background-repeat:no-repeat;background-size:cover;background-position:center;height:40px;width:40px;"></span>
                            <input style="margin:10px;background:rgba(0,0,0,0.5);border-radius:10px;text-align:center;" class="changable_input" type="text" @change="change_table_name" :value="get_name" />
                        </div>
                        <div class="crm_left_side_panel_header bgblackwhite v-cloak-on" v-cloak>
                            <span style="cursor:pointer;height:40px;width:40px;border-radius:50%" class="v-cloak-block"></span>
                            <p class="v-cloak-text2" style="width:200px;height:40px !important;"></p>
                        </div>
                        <div class="crm_left_side_panel_container v-cloak-off" v-cloak>
                            <div v-if="column_redactor" class="redactor_top_panel" :style="{'justify-content': column_header == 'helper_photo' || column_header == 'helper_name' || column_header == 'helper_info' ? 'space-between' : 'center'}">
                                <h2 style='color:#f90;font-weight:bold;font-size:20;' v-if="column_header == 'helper_name'">Имя</h2>
                                <h2 style='color:#f90;font-weight:bold;font-size:20;' v-else-if="column_header == 'helper_info'">Информация с консультации</h2>
                                <h2 style='color:#f90;font-weight:bold;font-size:20;' v-else-if="column_header == 'helper_photo'">Фото</h2>
                                <input v-else class="changable_input" style="width:350px;background:url(/scss/imgs/classy_fabric.png) repeat center center;border-radius:10px;border-bottom:2px solid #0ae;color:#f90;font-weight:bold;font-size:20;" title="Название столбца" v-model="column_header" placeholder="Название"/>
                                <div style="display:inline-flex;margin-top:10px;align-items:center;" v-if="column_header == 'helper_photo' || column_header == 'helper_name' || column_header == 'helper_info'">
                                    <h2 style="color: #fff;width: 100%;font-size: 20px;">Скрыть</h2>
                                    <span @click="hide_column = !hide_column" style='margin-left:10px;' class="check_btn">
                                        <span style="pointer-events: none;" :class="{'checked_btn_span': !hide_column, 'unchecked_btn_span': hide_column}"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="column_opts_container" v-if="column_redactor">
                                <div class="column_box">
                                    <h2 class="WhiteBlack">Приоритет вывода</h2>
                                    <input style="width:200px;" title="Порядок по которому будут выведены столбцы, от меньшего к большему" class="priority_value" type="number" placeholder="Приоритет вывода" v-model="priority_value"/>
                                </div>
                                <p style="margin:10px;color:grey;font-weight:bold;font-size:14px;" v-if="column_header != 'helper_photo' && column_header != 'helper_name' && column_header != 'helper_info'"> При смене типа столбца, могут пропасть внесённые вами данные для записей !</p>
                                <div class="column_box" v-if="column_header != 'helper_photo' && column_header != 'helper_name' && column_header != 'helper_info'">
                                    <h2 class="WhiteBlack">Тип столбца</h2>
                                    <div style="width:200px;" class="crm_column_redactor_select_type">
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 0">Текст</p>
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 1">Цифра</p>
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 2">Список</p>
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 3">Дата</p>
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 4">Дата и время</p>
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 5">Валюта</p>
                                        <p @click="list('.column_type_list')" v-if="choosen_type == 6">Архив или изображение</p>
                                        <ul class="column_type_list">
                                            <li v-if="choosen_type != 0" @click="list('.column_type_list'); choosen_type = 0;">Текст</option>
                                            <li v-if="choosen_type != 1" @click="list('.column_type_list'); choosen_type = 1;">Цифра</option>
                                            <li v-if="choosen_type != 2" @click="list('.column_type_list'); choosen_type = 2;">Список</option>
                                            <li v-if="choosen_type != 3" @click="list('.column_type_list'); choosen_type = 3;">Дата</option>
                                            <li v-if="choosen_type != 4" @click="list('.column_type_list'); choosen_type = 4;">Дата и время</option>
                                            <li v-if="choosen_type != 5" @click="list('.column_type_list'); choosen_type = 5;">Валюта</option>
                                            <li v-if="choosen_type != 6" @click="list('.column_type_list'); choosen_type = 6;">Архив или изображение</option>
                                        </ul>
                                    </div>
                                </div>
                                <div class="column_box" v-if="choosen_type == 1 || choosen_type == 0 || choosen_type == 5">
                                    <h2 class="WhiteBlack">Значение по умолчанию</h2>
                                    <input :type="choosen_type == 1 || choosen_type == 5 ? 'number' : 'text'" title="Значение будет выставляться по умолчанию в этом столбце" v-model="deffault_val" placeholder="По умолчанию"/>
                                </div>
                                <div class="column_box" v-if="choosen_type == 3 || choosen_type == 4">
                                    <h2 class="WhiteBlack">Значение по умолчанию</h2>
                                    <input :type="choosen_type == 3 ? 'date' : 'datetime-local'" title="Значение будет выставляться по умолчанию в этом столбце" v-model="deffault_val"/>
                                </div>
                                <div class="column_box" v-if="choosen_type == 6">
                                    <h2 class="WhiteBlack">Файл по умолчанию</h2>
                                    <label class="deffault_photo">
                                        <input @change="deffault_photo()" type="file" class="display_none" name="image"/>
                                    </label>
                                </div>
                                <div class="column_box" v-if="choosen_type == 5">
                                    <h2 class="WhiteBlack">Валюта</h2>
                                    <div style="width:200px;" class="crm_column_redactor_select_type">
                                        <p @click="list('.column_valute_list')" v-if="valute == 0">₽</p>
                                        <p @click="list('.column_valute_list')" v-if="valute == 1">＄</p>
                                        <p @click="list('.column_valute_list')" v-if="valute == 2">€</p>
                                        <ul class="column_valute_list">
                                            <li v-if="valute != 0" @click="list('.column_valute_list'); valute = 0;">₽</option>
                                            <li v-if="valute != 1" @click="list('.column_valute_list'); valute = 1;">＄</option>
                                            <li v-if="valute != 2" @click="list('.column_valute_list'); valute = 2;">€</option>
                                        </ul>
                                    </div>
                                </div>
                                <div class="column_box" style="flex-direction:column;" v-if="choosen_type == 2">
                                    <div style="display:inline-flex;justify-content:space-between;width:100%;">
                                        <input style="width:250px;" title="Добавить пункт в список" v-model="new_choice"  placeholder = "Новый пункт" />
                                        <span  @click="add_new_choice()"  class="column_Btn">Добавить пункт</span>
                                    </div>
                                    <h2 style='text-align:center;'>Список</h2>
                                    <div class="column_list">
                                        <div v-for="elem in choosen_choices">
                                            <div style="min-width:70px;max-width:70px;display:inline-flex;">
                                                <span @click="delete_default(elem)">
                                                    <span></span><span></span>
                                                </span>
                                                <span @click="choose_deffault(elem)" style="margin-left:10px;" :style="{'opacity': elem == choosen_deffault ? '1' : '0.5'}">
                                                    <span></span><span></span>
                                                </span>
                                            </div>          
                                            <p style="text-align:right;">{{elem}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display:inline-flex;" v-if= "column_redactor">
                                <span class='column_btn_control' v-if="column_redactor == 'new'" @click="new_column()" >Добавить</span>
                                <span class='column_btn_control' v-if="column_redactor != 'new' && column_header != 'helper_photo' && column_header != 'helper_name' && column_header != 'helper_info'"  @click="delete_column" >Удалить столбец</span>
                                <span class='column_btn_control' v-if="column_redactor != 'new'" @click="save_column" >Сохранить изменения</span>
                                <span class='column_btn_control' @click="column_redactor=null">Отмена</span>
                            </div>
                            <div style="margin-bottom:20px;height:100%;width:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;" v-else>
                                <p class="column_redactor_text WhiteBlack">Создайте новый стобец или выберите существующий !</p> 
                                <span class="column_redactor_button" @click="column_redactor='new'" >Создать столбец</span>
                            </div>
                        </div>
                        <div class="crm_left_side_panel_container v-cloak-on" style="display:flex;align-items:center;justify-content:center;" v-cloak>
                            <p class="v-cloak-text2" style="matgin:0;margin-top:10px;width:300px;border-bottom:4px solid #000;"></p>
                            <p class="v-cloak-text2" style="matgin:0;margin-top:20px;width:400px;height:50px !important;border-bottom:4px solid #000;"></p>
                            <p class="v-cloak-text2" style="matgin:0;margin-top:20px;width:400px;height:50px !important;border-bottom:4px solid #000;"></p>
                            <p class="v-cloak-text2" style="matgin:0;margin-top:20px;width:400px;height:50px !important;border-bottom:4px solid #000;"></p>
                            <p class="v-cloak-text2" style="matgin:0;margin-top:20px;width:400px;height:50px !important;border-bottom:4px solid #000;"></p>
                            <p class="v-cloak-text2" style="matgin:0;margin-top:20px;width:400px;height:50px !important;border-bottom:4px solid #000;"></p>
                            <div style="display:inline-flex;align-items:center;">
                                <p class="v-cloak-text2" style="matgin:0;margin-top:10px;width:200px"></p>
                                <p class="v-cloak-text2" style="margin-top:10px;width:200px"></p>
                            </div>
                        </div>
                    </div>
                    <div class="crm_right_side_panel bgblackwhite v-cloak-off" v-cloak>
                        <div class="crm_filter_panel">
                            <h2 class="crm_filter_header">Столбцы</h2>
                            <span class="crm_redactor_column" :class="{'active_column': selected_column == index}" v-for="(column, index) in sort_columns(columns)" @click="selected_column = index; column_redactor = 'redactor';">
                                <p class="WhiteBlack" v-if="index == 'helper_name'">Имя</p>
                                <p class="WhiteBlack" v-else-if="index == 'helper_info'">Информация с консультации</p>
                                <p class="WhiteBlack" v-else-if="index == 'helper_photo'">Фото</p>
                                <p class="WhiteBlack" v-else v-html="column['helper_column_name']"></p>
                            </span>
                        </div>
                    </div>
                    <div class="crm_right_side_panel bgblackwhite v-cloak-on" v-cloak>
                        <div class="crm_filter_panel">
                            <p class="v-cloak-text2" style="width:90%;margin:10px;"></p>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                            <span class="crm_redactor_column">
                                <p class="v-cloak-text2" style="margin:10px;"></p>
                            </span>
                        </div>
                    </div>
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