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
    if(isset($_SESSION['boss'])) $boss_id = $_SESSION['boss'];
    if(isset($_SESSION['employee'])) $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    if(isset($_GET['type'])){
        $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
        $row = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        if(!isset($row[$_GET['type']])){ mysqli_close($connection); header("Location: /engine/consultant/crm"); exit; } 
    }
    $sql ="SELECT buttlecry FROM assistents WHERE id = '$personal_id'";
    $buttlecry = attach_sql($connection, $sql, 'row')[0];
    mysqli_close($connection);
    $assistent_file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
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
        <?php create_book($buttlecry); ?>
        <?php team_msg_notification_body($assistent_file_path); ?>
        <!-- Управление отображением -->
        <div style="box-shadow:0 0 20px rgba(0,0,0,0.4);" class="incolumn incolumn_right v-cloak-off" v-cloak>
            <div style="display: flex;justify-content:center;">
                <div style='display:flex;flex-direction:column;'>
                    <h2 style='text-align:center;margin-top:5px;font-size:16px;'>Показать найденных записей</h2>
                    <input style='background:#fff;color:#000;border-radius:10px;margin-top:5px;' class='changable_input' type='number' v-model='load_count' :max='Object.keys(users).length'/>
                    <h2 style='text-align:center;margin-top:10px;font-size:16px;'>Добавлять записей при нажатии кнопки</h2>
                    <input style='background:#fff;color:#000;border-radius:10px;margin-top:5px;' class='changable_input' type='number' v-model='load_more_count' :max='Object.keys(users).length'/>
                    <div style="display:inline-flex; align-items:center;margin-top:20px;">
                        <h2 class="header2" style="margin:0;margin-right:10px;">Скрыть фильтры</h2>
                        <span style="background:rgba(255,255,255,0.1);" @click="filters = !filters" class="check_btn"><span :class="[{'checked_btn_span': filters}, {'unchecked_btn_span': !filters}]"></span></span>
                    </div>
                    <div style="display:inline-flex; align-items:center;margin-top:20px;">
                        <h2 class="header2" style="margin:0;margin-right:10px;">Скрыть панель перемещения</h2>
                        <span style="background:rgba(255,255,255,0.1);" @click="movement_panel = !movement_panel" class="check_btn"><span :class="[{'checked_btn_span': movement_panel}, {'unchecked_btn_span': !movement_panel}]"></span></span>
                    </div>
                    <div style="display:inline-flex; align-items:center;margin-top:20px;">
                        <h2 class="header2" style="margin:0;margin-right:10px;">Скрыть панель управления</h2>
                        <span style="background:rgba(255,255,255,0.1);" @click="control_panel = !control_panel" class="check_btn"><span :class="[{'checked_btn_span': control_panel}, {'unchecked_btn_span': !control_panel}]"></span></span>
                    </div>
                </div>
            </div>
            <div class="choose_guests_btn choose_guests_btn_right unactive_choose_guests_btn" onclick='control("choose_guests_btn", ".incolumn", {"top": -$(".incolumn").height() - 20}, {"top": "0"})'><span></span></div>
        </div>
        <!-- рассылка -->
        <div style="box-shadow:0 0 20px rgba(0,0,0,0.4);" class="mailer_crm_menu v-cloak-off" v-cloak>
            <div style="display: inline-flex;justify-content:flex-start;" v-if="mailer_mode">
                <div>
                    <div class="add_tsk_btn" style="background:tomato;"  @click="mailer_mode = !mailer_mode">отменить</div>
                    <p class="task_header">Отправить от лица:</p>
                    <select class="task_text" v-model="selected_domain" style="height:40px;font-size:14px;">
                        <option style="background:#252525;color:#ffffff;" :selected="domain == selected_domain" :value="domain" v-for="(domain,index) in domains">{{domain == 'deffault' ? 'По умолчанию' : domain}}</option>
                    </select>
                    <p class="task_header">Название письма(рассылки)</p>
                    <input v-model="mail_name" class="task_text" style="height:40px;" type="text" placeholder="Название письма(рассылки)"/>
                    <p class="task_header">Имя отправителя</p>
                    <input v-model="sender_name" class="task_text" style="height:40px;" type="text" placeholder="Имя отправителя"/>
                    <div>
                        <p class="task_header">Содержание письма</p>
                        <div style="max-width:250px;background:#333;margin-top:10px;border:2px solid #000;border-radius:10px;width:100%;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;">
                            <div id="chat_footer" style="line-height:1.4;background:#333;border-bottom:2px solid #000;display:flex;align-items:flex-end;flex-direction:column;background:transparent" class="card-body message_panel_input">
                                <div type="text" style="border-bottom:2px solid #000;border-top-left-radius:10px;border-top-right-radius:10px;background:url(/scss/imgs/classy_fabric.png) repeat center center;" contenteditable="true" aria-multiline="true" role="textbox"  class="chat_block_textarea form-control"></div>
                                <div class="btns_panel">
                                    <span title="Быстрые команды" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                                    <input @change="mailer_handleChange()" multiple name="myfile[]" class='add_photo'  id='add_photo' type='file' style='display:none;' />
                                    <label title="Приложить фотографию" class="btns_panel_btn add_file" for="add_photo" ></label>
                                </div>   
                            </div> 
                            <div id="upload_files_preview">
                                <div class="preview_img_block" v-for="(file, index) in files" :key="index">
                                    <span class="preview_remove" @click="mailer_removeFile(index)"><span></span><span></span></span>
                                    <span class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                                    <p :title="file.name" style="background:#0ae;position:absolute;bottom:7px;left:0;color:#000;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.name}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span v-if="!loader" class="add_tsk_btn" style="width:100%;margin-top:10px;" @click="send_mails">Отправить</span>
                    <div style="min-height:40px;min-width:40px;margin:20px;" v-else class="domain-loader"></div>
                </div>
                <div style="margin-left:10px;">
                    <div class="mailer_select" style="width:250px;margin-top:0;">
                        <h3 >Выбрать из CRM</h3>
                        <p style="font-size:11px;">Выбирите колонку, отвечающую за почту</p>
                        <div style="width:100%;display:flex;flex-direction:column;margin-top:10px;margin-bottom:10px;">
                            <select @change="mail_column = $event.target.value" style="color:#fff;padding:10px;height:40px;width:100%;background:#333;outline:none;border-radius:10px;">
                                <option :value="index"  style="background:#333;color:#fff;" v-if="['helper_photo'].indexOf(index) == -1 && column.type != 6 && column.type != 5 && column.type != 4 && column.type != 3 && column.type != 1" v-for="(column, index) in columns">{{(index == 'helper_name' ? 'Имя' : (index == 'helper_info' ? 'Информация' : index))}}</option>
                            </select>
                            <div style="display:inline-flex;align-items:center;margin-top:10px;margin-bottom:10px;">
                                <p style="font-size:13px;margin-right:10px;text-align:flex-start;">Брать имена из колнки "имя"</p>
                                <span @click="mailer_name = !mailer_name" class="check_btn">
                                    <span :class="mailer_name ? 'unchecked_btn_span' : 'checked_btn_span'" class="pointer_none"></span>
                                </span>
                            </div>
                            <div style="width:100%;" class="add_tsk_btn" @click="mailer_select_all">Выбрать всех по столбцу</div>
                        </div>
                        <p style="font-size:12px;text-align:center;">Ваши фильтры влияют на выбор через эту опцию</p>
                    </div>
                    <div class="mailer_select" style="width:250px;">
                        <h3>Получатели</h3>
                        <div class="mailer_select_info">
                            <div style="width:100%;display:flex;flex-direction:column;">
                                <input class="task_text" style="width:100%;height:40px;" v-model="recepient.name" placeholder="имя получателя *" style="margin:0;border-radius:10px;" class="changable_input" type="text"/>
                                <input class="task_text" style="margin-top:10px;width:100%;height:40px;" v-model="recepient.email" placeholder="Почта получателя" style="margin-left:0;border-radius:10px;" class="changable_input" type="text"/>
                                <span class="add_tsk_btn" style="width:100%;margin-top:10px;" @click="add_recepient">Добавить</span>
                            </div>
                        </div>
                        <div v-if="Object.keys(mailer_selected).length > 0">
                            <div class="mailer_selected" v-for="(item, index) in mailer_selected">
                                
                                <div style="display:flex;flex-direction:column;justify-content:flex-start;width:calc(100% - 40px);align-items:flex-start;">
                                    <p>{{item.email}}</p>
                                    <p v-if="item.name">{{item.name}}</p>
                                </div>
                                <span @click="remove_recepient(index)"></span>
                            </div>
                        </div>
                        <div v-else> 
                            <p style="text-align:center;width:100%;">Добавьте получателей через поля выше, <br/>чтобы  здесь что-то появилось</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="add_tsk_btn" @click="mailer_mode = !mailer_mode;add_mode=false;" v-else>Открыть меню</div>
            <div class="mailer_crm_menu_btn unactive_mailer_crm_menu_btn" onclick='control("mailer_crm_menu_btn", ".mailer_crm_menu", {"bottom": -$(".mailer_crm_menu").height() - 20}, {"bottom": "0"})'><span></span></div>
        </div>
        <!-- задачи -->
        <div class="add_task_menu v-cloak-off" v-cloak>
            <div class="task_first_column" v-if='add_mode'>
                <div class="add_tsk_btn" @click="add_mode = !add_mode">отменить</div>
                <p class="task_header">Время задачи</p>
                <input class="task_date_time" type="datetime-local" />
                <p class="task_header">Тип задачи</p>
                <a @click="task_type_control()" data-choice="0" class="choosen_task_type cursor_pointer">Не выбрано</a>
                <ul class="task_type_menu">
                    <li @click="choose_type(0)" class="task_type">Личная</li>
                    <li @click="choose_type(1)" class="task_type">Публичная</li>
                </ul>
                <p class="task_header">Задача</p>
                <textarea class="task_text" placeholder="Введите задачу"></textarea> 
            </div>
            <div class="task_second_column" v-if='add_mode'>
                <p class="task_header">Выбранные</p>
                <div class="task_for">
                    <div @click="find(selected_item)" class="task_for_choosen" v-for="(selected_item, index) in selected">
                        <div class="task_for_choosen_photo" :style="'background-image:url(/crm_files/'+users[selected_item].helper_photo+');'"></div>
                        <div class="task_for_choosen_name">{{users[selected_item].helper_name}}</div>
                    </div>
                </div>
                <button @click="add_task()" class="add_task_btn">Добавить задачу</button>
            </div>
            <div class="add_tsk_btn" @click="add_mode = !add_mode;mailer_mode=false;" v-else>Добавить задачу</div>
            <div class="add_task_menu_btn unactive_add_task_menu_btn" onclick='control("add_task_menu_btn", ".add_task_menu", {"right": -$(".add_task_menu").width() - 20}, {"right": "0"})'><span></span></div>
        </div>
        <div class="user_task_info task_close bgblackwhite v-cloak-off" v-cloak>
            <span @click="close_user_task" class="close_user_task">
                <span></span><span></span>
            </span>
            <span class="user_task_time WhiteBlack"></span>
            <p class="user_task_type"></p>
            <span class="user_task_users_block"> 
                <p class="WhiteBlack">Участники</p>
                <div class="user_task_users WhiteBlack"></div>
            </span>
            <p class="user_task_text WhiteBlack"></p>
            <div style="width:100%;margin-top:10px;display:flex;justify-content:flex-end;align-items:flex-end;">
                <span @click="remove_task" style="padding:10px;border:2px solid tomato;cursor:pointer;border-radius:10px;background:tomato;color:#000;">Удалить задачу</span>
            </div>
        </div>
        <!-- CRM -->
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
                    <div style="overflow-x:auto;width:calc(100% - 250px);display:inline-flex;align-items:flex-start;justify-content:flex-start;height:100%;">
                        <a v-for="table in tables" :href="'/engine/consultant/crm?type='+encodeURI(table)" :class="'crm_clients ' + (get_name == table ? 'active' : '')"><p style="margin:0;" v-html="table"></p> <span class="task_count_p">{{counters[table]||0}}</span></a>
                    </div>
                </div>
                <div class="v-cloak-on v-cloak-block crm_nav" v-cloak></div>
                <div class="v-cloak-off crm_opt_panel" v-if="control_panel && <?php echo (isset($_GET['type']) ? 'true' : 'false') ?>" v-cloak>
                    <a :href="'/engine/consultant/crm_settings?type='+encodeURI(get_name)" class="crm_opt_btn">Настройки</a>
                    <a href="#" class="crm_opt_clients_list active">Список</a>
                    <a class="cursor_pointer crm_download_btn" :href="'/engine/download?table='+get_name" download v-if="!travel_mode">Скачать</a>
                    <a class="cursor_pointer crm_add_leed_btn" @click="add_user();">Добавить</a>
                    <a class="cursor_pointer crm_delete_leed_btn" @click="remove_mode = true;travel_mode = false;add_mode=false;mailer_mode=false;" v-if="!remove_mode && !add_mode && !mailer_mode">Удалить</a>
                    <a class="cursor_pointer crm_delete_leed_btn" @click="remove_mode = false;" v-if="remove_mode && !add_mode && !mailer_mode">Выйти из режима</a>
                    <a class="cursor_pointer crm_teleport_leed_btn" @click="travel_mode = true;remove_mode = false;" v-if="!travel_mode">Перенос</a>
                    <a class="cursor_pointer crm_teleport_leed_btn"  v-if="travel_mode">
                        <span class="close" @click="travel_mode = false;"></span>
                        <select v-model="travel_table" style="background:#333;color:#fff;margin-left:10px;outline:none;cursor:pointer;border:none;border-radius:10px;padding:5px;">
                            <option disabled selected>не выбрано</option>
                            <option :value="table" v-if="table != get_name" v-for="table in tables" v-html="table"></option>
                        </select>
                    </a>
                    <a title="Копировать структру тыблицы, без её записей" class="cursor_pointer copy_table" @click="copy_table">Копировать таблицу</a>
                </div>
                <div class="v-cloak-on  v-cloak-block crm_opt_panel" v-cloak></div>
                <div class="crm_menu_container v-cloak-off" v-if="<?php echo (isset($_GET['type']) ? 'true' : 'false') ?>"  v-cloak>
                    <div class="crm_left_side_panel" :style="{'width': !filters ? '100%' : 'calc(100% - 350px)'}">
                        <div class="crm_serch_panel">
                            <input @keyup="search('')" placeholder="поиск" class="crm_serch_input bgblackwhite WhiteBlack" type="text" />
                        </div>
                        <div class="crm_cards_container bgblackwhite" :style="{'height': movement_panel && control_panel ? '500px' : ((control_panel && !movement_panel) || (movement_panel && !control_panel) ? '590px' : '680px')}">
                            <div class="crm_user_card bgblackwhite">
                                <div class="WhiteBlack border_right_5 crm_user_card_part crm_user_card_photo" v-if="add_mode||mailer_mode">Добавить</div>
                                <div class="WhiteBlack border_right_5 crm_user_card_part crm_user_card_photo" v-if="travel_mode">Перенести</div>
                                <div class="WhiteBlack border_right_5 crm_user_card_part crm_user_card_photo" v-if="remove_mode">Удалить</div>
                                <div class="border_right_5 crm_user_card_part" v-for="(column, index) in columns" v-if="column.display != 'true'" :style="{'max-width': index == 'helper_photo' || column.type == 6 ? '90px !important' : '250px', 'min-width': index == 'helper_photo' || column.type == 6 ? '90px !important' : '250px'}">
                                    <span v-if="index != 'helper_photo' && column.type != 6" @click="sort_array(index, 'unknown')" class="sort_btn unactive_sort_btn"></span>
                                    <p class="WhiteBlack" v-if="index == 'helper_name'">имя</p>
                                    <p class="WhiteBlack" v-else-if="index == 'helper_info'">Информация с консультации</p>
                                    <p class="WhiteBlack" v-else-if="index == 'helper_photo'">фото</p>
                                    <p class="WhiteBlack" v-else v-html="column['helper_column_name']"></p>
                                </div>
                            </div>
                            <div class="crm_user_card" v-for="(user, index) in sort_mas(searchmas)" :key="index" :class="returnClass(index)" >
                                <div class="crm_user_card_part crm_user_card_photo" v-if="add_mode||mailer_mode">
                                    <div @click="add_mode ? select(index) : mailer_select(index)" class="add_to_task_btn"  :class="{'add_to_active_task_btn': selected.indexOf(index) == -1 && !mailer_selected[index], 'add_to_unactive_task_btn': selected.indexOf(index) != -1 || mailer_selected[index]}"><span></span><span></span></div>
                                </div>
                                <div class="crm_user_card_part crm_user_card_photo" v-if="remove_mode">
                                    <div @click="delete_user(index)" class="remove_crm_item"><span></span><span></span></div>
                                </div>
                                <div class="crm_user_card_part crm_user_card_photo" v-if="travel_mode">
                                    <div @click="travel_user(index)" class="travel_crm_item"><span></span><span></span><span></span></div>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" v-for="(column, column_index) in columns" v-if="column.display != 'true'" :style="{'max-width': column_index == 'helper_photo' || column.type == 6 ? '90px !important' : '250px', 'min-width': column_index == 'helper_photo' || column.type == 6 ? '90px !important' : '250px', 'display': column_index == 'helper_photo' || column.type == 6 ? 'flex' : 'block'}" style="align-items:center;justify-content:center;">
                                    <label v-if="column_index == 'helper_photo' || column.type == 6" :style="'background-image:url('+(column_index == 'helper_photo' ? '/crm_files/'+user.helper_photo : check_file(users[index][column_index]||column.deffault))+');'" class="crm_user_photo">
                                        <input @change="change(index, column_index)" type="file" class="display_none" name="image"/>
                                    </label>
                                    <a class="download-file-btn" :href="'/crm_files/'+users[index][column_index]||column.deffault" v-if="column.type == 6" :download="users[index][column_index]||column.deffault"></a>
                                    <div class="user_tasks_block" v-if="column_index=='helper_name' && tasks?.[index]">
                                        <span class="user_task" @click="check_task(task)" :style="{'background-color': new Date(task.time) > new Date ? '#f90' : 'green'}" v-for="(task, task_index) in tasks[index]"></span>
                                    </div>
                                    <textarea @change="change(index, column_index)" class="WhiteBlack user_columns search_element crm_input" v-if="(column.type == 0 || column_index == 'helper_name' || column_index == 'helper_info') && !searchmas[index][column_index]" >{{column.deffault}}</textarea>
                                    <textarea @change="change(index, column_index)" class="WhiteBlack user_columns search_element crm_input" v-else-if="(column.type == 0 || column_index == 'helper_name' || column_index == 'helper_info') && searchmas[index][column_index]" v-model="searchmas[index][column_index]"></textarea>
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-if="column.type == 1 && !searchmas[index][column_index]" type="number" :value="column.deffault"/>
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-else-if="column.type == 1 && searchmas[index][column_index]" type="number" v-model="searchmas[index][column_index]" />
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-if="column.type == 3 && !searchmas[index][column_index]" type="date" :value="column.deffault"/>
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-else-if="column.type == 3 && searchmas[index][column_index]" type="date" v-model="searchmas[index][column_index]" />
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-if="column.type == 4 && !searchmas[index][column_index]" type="datetime-local" :value="column.deffault"/>
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-else-if="column.type == 4 && searchmas[index][column_index]" type="datetime-local" v-model="searchmas[index][column_index]" />
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-if="column.type == 5 && !searchmas[index][column_index]" type="number" :value="column.deffault.value"/>
                                    <input @change="change(index, column_index)"    class="WhiteBlack user_columns search_element crm_input"  v-else-if="column.type == 5 && searchmas[index][column_index]" type="number" v-model="searchmas[index][column_index]" />
                                    <span class="column_valute WhiteBlack" v-if="column.type == 5 && column.deffault.type == 0">₽</span>
                                    <span class="column_valute WhiteBlack" v-if="column.type == 5 && column.deffault.type == 1">＄</span>
                                    <span class="column_valute WhiteBlack" v-if="column.type == 5 && column.deffault.type == 2">€</span>
                                    <select @change="change(index, column_index)" class="WhiteBlack search_element search_filter_element crm_select" v-if="column.type==2">
                                        <option class="crm_option WhiteBlack bgblackwhite" selected v-if="!user[column_index]">{{column.deffault}}</option>    
                                        <option class="crm_option WhiteBlack bgblackwhite" selected v-else >{{user[column_index]}}</option>   
                                        <option class="crm_option WhiteBlack bgblackwhite" v-for="variant in column.variants" v-if="(variant != column.deffault && !user[column_index]) || (variant != user[column_index] && user[column_index])">{{variant}}</option>
                                        <option class="bgblackwhite" value=""></option>
                                    </select>
                                </div>
                            </div>
                            <form @click="load_more()" v-if="check_count()" class="OnlineUser more_btn" >
                                <p>Загрузить ещё</p>
                            </form>
                        </div>
                    </div>
                    <div class="crm_right_side_panel bgblackwhite" style="direction:rtl;" :style="{'display': !filters ? 'none' : 'flex'}" >
                        <div class="crm_filter_panel" :style="{'height': movement_panel && control_panel ? '560px' : ((control_panel && !movement_panel) || (movement_panel && !control_panel) ? '650px' : '740px')}">
                            <h2 class="crm_filter_header">Фильтры</h2>
                            <div class = "crm_filter_box" style="direction:ltr;" >
                                <h2 class="crm_filter_name">Стандартные</h2>
                                <div class = "crm_filter_container">
                                    <p class="pointer_none WhiteBlack">Без задач</p>
                                    <span @click="filter(false, 'tasks');" class="check_btn">
                                        <span class="unchecked_btn_span pointer_none"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container" >
                                    <p class="pointer_none WhiteBlack">С задачами</p>
                                    <span @click="filter(true, 'tasks');" class="check_btn">
                                        <span class="unchecked_btn_span pointer_none"></span>
                                    </span>
                                </div>
                            </div>
                            <div class = "crm_filter_box" style="direction:ltr;" v-for="(column, index) in columns" v-if="column.type == 2">
                                <h2 class="crm_filter_name">{{column.helper_column_name}}</h2>
                                <div class = "crm_filter_container">
                                    <p class="pointer_none WhiteBlack">Пустые значения</p>
                                    <span @click="filter('helper_empty_fields', index);" class="check_btn">
                                        <span class="unchecked_btn_span pointer_none"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container" v-for="variant in column.variants" >
                                    <p class="pointer_none WhiteBlack">{{variant}}</p>
                                    <span @click="filter(variant, index);" class="check_btn">
                                        <span class="unchecked_btn_span pointer_none"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="crm_filter_box" style="direction:ltr;">
                                <h2 class="crm_filter_name WhiteBlack" style="font-size:15px;margin-bottom:10px;">Создайте столбец типа список во вкладке настройки, чтобы здесь что-то появилось.</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="crm_menu_container v-cloak-off" style="height:500px;diaplay:flex;align-items:center;justify-content:center;" v-else v-cloak>
                    <p>Окройте таблицу или <span style='color:#0ae;font-weight:bold;cursor:pointer;'>создайте новую</span></p>
                </div>
                <div class="crm_menu_container v-cloak-on" v-cloak>
                    <div class="crm_left_side_panel" style="width:calc(100% - 350px);">
                        <div class="crm_serch_panel">
                            <div class="crm_serch_input bgblackwhite WhiteBlack v-cloak-block"></div>
                        </div>
                        <div class="crm_cards_container bgblackwhite"  style="height:500px;">
                            <div class="crm_user_card bgblackwhite">
                                <div class="border_right_5 crm_user_card_part" style="max-width:90px !important; min-width: 90px !important;">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;" ></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;"></p>
                                </div>
                                <div class="border_right_5 crm_user_card_part">
                                    <p class="WhiteBlack v-cloak-text2" style="margin:0 !important;margin-bo"></p>
                                </div>
                            </div>
                            <div class="crm_user_card">
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;max-width:90px !important; min-width: 90px !important;">
                                    <label style="border-radius:50%;" class="crm_user_photo v-cloak-block"></label>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <div class="user_tasks_block" >
                                        <span class="user_task v-cloak-block"></span>
                                    </div>
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                            </div>
                            <div class="crm_user_card">
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;max-width:90px !important; min-width: 90px !important;">
                                    <label style="border-radius:50%;" class="crm_user_photo v-cloak-block"></label>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <div class="user_tasks_block" >
                                        <span class="user_task v-cloak-block"></span>
                                    </div>
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                            </div>
                            <div class="crm_user_card">
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;max-width:90px !important; min-width: 90px !important;">
                                    <label style="border-radius:50%;" class="crm_user_photo v-cloak-block"></label>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <div class="user_tasks_block" >
                                        <span class="user_task v-cloak-block"></span>
                                    </div>
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                            </div>
                            <div class="crm_user_card">
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;max-width:90px !important; min-width: 90px !important;">
                                    <label style="border-radius:50%;" class="crm_user_photo v-cloak-block"></label>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <div class="user_tasks_block" >
                                        <span class="user_task v-cloak-block"></span>
                                    </div>
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                                <div class="padding_0 user_columns crm_user_card_part" style="align-items:center;justify-content:center;">
                                    <p class="v-cloak-text2" style="margin:10px;"></p>
                                </div>
                            </div>
                            <form @click="load_more()" v-if="check_count()" class="OnlineUser more_btn" >
                                <p>Загрузить ещё</p>
                            </form>
                        </div>
                    </div>
                    <div class="crm_right_side_panel bgblackwhite" style="direction:rtl;">
                        <div class="crm_filter_panel"  style="height:560px;">
                            <p class="crm_filter_header v-cloak-text2" style="margin:10px;width:90% !important;"></p>
                            <div class = "crm_filter_box" style="direction:ltr;" >
                                <p class="crm_filter_header v-cloak-text2" style="margin:10px;width:90% !important;"></p>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container" >
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                            </div>
                            <div class = "crm_filter_box" style="direction:ltr;">
                                <p class="crm_filter_header v-cloak-text2" style="margin:10px;width:90% !important;"></p>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                            </div>
                            <div class = "crm_filter_box" style="direction:ltr;">
                                <p class="crm_filter_header v-cloak-text2" style="margin:10px;width:90% !important;"></p>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                                <div class = "crm_filter_container">
                                    <p class="crm_filter_header v-cloak-text3"></p>
                                    <span class="check_btn v-cloak-block">
                                        <span class="unchecked_btn_span pointer_none v-cloak-block"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="crm_filter_box" style="direction:ltr;">
                                <p class="crm_filter_header v-cloak-text2" style="margin:10px;width:90% !important;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo appendfooter(); ?>
</body>
<script src="/scripts/libs/howler.min.js"></script>
<script src="/scripts/libs/vue.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>