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
    <?php navigation('swaper', $info); ?>
    <section id='container'>
        <?php section_header('Подмены', 'swap.png'); ?>
        <div style="padding:20px;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;'">
            <h2 class='header1 wow bounceInUp v-cloak-off' v-cloak>Подмена контента</h2>
            <div class="swap_form bgblackwhite v-cloak-off" v-if="new_swap" v-cloak>
                <span class="close_new_notification" @click="new_swap = !new_swap">
                    <span></span>
                    <span></span>
                </span>
                <p class="WhiteBlack">Тип подмены</p>
                <div class="swap_type_block">
                    <p :vlaue="swap_type" @click="swap_type_list = !swap_type_list">{{swap_types?.[swap_type]?.['text']||'не выбрано'}}</p>
                    <ul id="swap_type" :style="{'max-height': swap_type_list ? Object.keys(swap_types).length * 45 + 'px' : '0'}">
                        <li @click="swap_type = type_index;swap_type_list = !swap_type_list;" v-for="(type, type_index) in swap_types" :value="type_index">{{type['text']}}</li>
                    </ul>
                </div>
                <p class="WhiteBlack" v-if="swap_types[swap_type] && swap_type != 'statistic'">{{swap_types[swap_type]['text_from']}}</p>
                <input :placeholder="swap_types[swap_type]['placeholder1']" v-if="swap_types[swap_type] && swap_type != 'statistic'" type="text" class="swap_input_from" />
                <p class="WhiteBlack" v-if="swap_types[swap_type] && swap_type != 'statistic'">{{swap_types[swap_type]['text_to']}}</p>
                <input v-if="swap_type != 'text' && swap_types[swap_type] && swap_type != 'statistic'" :placeholder="swap_types[swap_type]['placeholder2']" type="text" class="swap_input_to" />
                <textarea v-else-if='swap_types[swap_type] && swap_type != "statistic"' :placeholder="swap_types[swap_type]['placeholder2']" class="swap_input_to"></textarea>
                <button v-if="swap_types[swap_type]" @click="add_swap">добавить</button>
            </div>
            <span v-if='!new_swap' class="add_swap v-cloak-off" v-cloak @click="new_swap = !new_swap">Добавить подмену</span> 
            <p class="text1 v-cloak-off" v-cloak>Замены без условий выполняются всегда</p>
            <div class="swap bgblackwhite v-cloak-off" v-for="(swap, swap_id) in settings" :key="swap_id" v-cloak>
                <div class="swap_name">
                    <input style="width:100%;border-color:#eee;" placeholder="Название подмены" :value="escapeHtml(swap.swap_name)" :title="swap.swap_name" @change="change_swap(swap_id, 'swap_name')" placeholder="Название" type="text" />
                </div>
                <div class="swap_info">
                    <div>
                        <div class="swap_phones" v-if="swap['swap_type'] != 'statistic'">
                            <input @change="change_swap(swap_id, 'swap_from')" type="tel" :value="escapeHtml(swap.swap_from)" style="border-color:#f90;"> 
                            <span style="background:#f90;"></span>
                            <span></span>
                            <input v-if="swap['swap_type'] != 'text'" @change="change_swap(swap_id, 'swap_to')" type="tel" :value="escapeHtml(swap.swap_to)">
                            <textarea v-else @change="change_swap(swap_id, 'swap_to')" v-html="swap.swap_to"></textarea>
                        </div>
                        <span class="swap3btn" @click = "open_swapif(swap)" :class="{'close_swapbtn': !swap.status, 'open_swapbtn': swap.status}">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span @click = "remove_swap(swap_id)" class="remove_swap">
                            <span></span>
                            <span></span>
                        </span>
                    </div>
                    <div>
                        <p class="WhiteBlack" v-if="swap.swap_type != 'statistic'">Кликов <span style="color:#f90;font-weight:bold;">{{getevent(swap, 'clicks', 'events')}}</span></p>
                        <p class="WhiteBlack">{{ swap.swap_type != 'statistic' ? 'Подмен' : 'Выполнено' }} <span style="color:#f90;font-weight:bold;">{{getevent(swap, 'shown', 'events')}}</span></p>
                        <p class="WhiteBlack">{{ swap.swap_type != 'statistic' ? 'Кэш-подмен' : 'Кэш-выполнений' }} <span style="color:#f90;font-weight:bold;">{{getevent(swap, 'cache_shown', 'events')}}</span></p>
                        <p class="WhiteBlack" v-if="swap.swap_type != 'statistic'">Кэш-кликов <span style="color:#f90;font-weight:bold;">{{getevent(swap, 'cache_clicks', 'events')}}</span></p>
                        <span class="grapth_btn" 
                            :style="{'background-color': (!swap?.['chart_settings']?.['status'] || swap?.['chart_settings']?.['openBY'] != 'MAIN_SWAP_CHART' || swap?.['chart_settings']?.['openRazdel'] != 'main') ? 'tomato' : 'lightgreen'}" 
                            @click="
                            (
                                !swap?.['chart_settings'] || swap?.['chart_settings']?.['openBY'] != 'MAIN_SWAP_CHART' || swap?.['chart_settings']?.['openRazdel'] != 'main'
                            ) ? create_chart(swap_id, 'MAIN_SWAP_CHART', 'main') : swap['chart_settings']['status'] = !swap['chart_settings']['status']"
                        ></span>
                    </div>
                </div>
                <div :style="{'max-height': swap.status ? '500px' : '0'}" class="swap_if">
                    <!-- условия замены -->
                    <p class="WhiteBlack" style="margin-top:10px;" v-if="'always' != swap.swap_time && 'never' != swap.swap_time && (swap.panel == 'conditions' || !swap.panel)">Условия подмены</p>
                    <div style="margin-top:10px;" class="swap_type_block" v-if="'always' != swap.swap_time && 'never' != swap.swap_time && (swap.panel == 'conditions' || !swap.panel)" >
                        <!--Выбранные --> 
                        <ul id="swap_type" v-if="swap.swap_if" :style="{'max-height':  (Object.keys(swap.swap_if||{}).length * 45) + 'px'}">
                            <li class="selected_condition" v-if="!conditions.hasOwnProperty(condition_id)" v-for="(condition, condition_id) in swap.swap_if" :key="condition_id">
                                {{
                                    (condition.type == 'activity_time' ? 'Время на сайте ' : '') +
                                    (condition.second == '>' ? (condition.type == 'time' ? 'После ' : 'Больше ') : (condition.second == '<' ? (condition.type == 'time' ? 'До ' : 'Меньше ') : (condition.second == '=' ? 'Ровно  ' : escapeHtml(condition.second||'') + ' '))) +  
                                    (condition.type == 'link' ? 'Ссылка включает ' : '') +
                                    escapeHtml(condition.main) 
                                }}
                                <span @click="removeCondition(swap_id, condition_id);">
                                    <span></span>
                                    <span></span>
                                </span>
                            </li>
                            <li class="selected_condition" v-if="conditions.hasOwnProperty(condition_id)" v-for="(condition, condition_id) in swap.swap_if" :key="condition_id">
                                <p v-html="conditions[condition_id].text"></p>
                                <span @click="removeCondition(swap_id, condition_id);">
                                    <span></span>
                                    <span></span>
                                </span>
                            </li>
                        </ul>
                        <!--Выбор --> 
                        <ul id="swap_type" :style="{
                                'max-height': swap.condition_list ? Object.entries(conditions).map((el) => { return el[1].input_status ? 4 : 1 }).reduce((a, b) => a + b) * 45 + 'px' : '0', 
                                'border-top': Object.keys(swap.swap_if||{}).length > 0 ? '5px solid #000' : 'none'
                        }">
                            <li class="condition_list_nores" v-if="!condition.input_status && !((swap.swap_if||{})[condition_id])" v-for="(condition, condition_id) in conditions" :key="condition_id" @click="addCondition(swap_id, condition_id)">{{condition.text}}</li>
                            <li class="condition_list_res" v-if="condition.input_status" v-for="(condition, condition_id) in conditions" :key="condition_id">
                                <p>{{condition.text}}</p>
                                <div class="condition_res">
                                    <select v-if="condition_id == 'time' || condition_id == 'open_counter' || condition_id == 'activity_time'">
                                        <option value=">">{{condition_id == 'time' ? 'После' : 'Больше'}}</option>
                                        <option value="<">{{condition_id == 'time' ? 'До' : 'Меньше'}}</option>
                                        <option v-if="condition_id == 'open_counter' || condition_id == 'activity_time'" value="=">Ровно</option>
                                    </select>
                                    <input class="not_main" :style="{'max-width': condition_id == 'personal_event' ? '135px' : '190px'}" :placeholder="condition.placeholder1" :type="condition.input_type" />
                                    <input class="not_second" style="max-width:110px;" :placeholder="condition.placeholder2" :type="condition.input_type" v-if="condition_id == 'personal_event'" />
                                    <button @click="addCondition(swap_id, condition_id)">Добавить</button>
                                </div>
                            </li>
                        </ul>
                        <span style="color:#fff;" :style="{'border-top': !swap.condition_list ? 'none' : '2px solid #000'}" class="notif_btn" @click="condition_list(swap_id)">{{ swap.condition_list ? 'Закрыть' : 'Добавить' }}</span>
                    </div>
                    <!-- условие подмены -->
                    <select @change ="swap_time(swap_id)" style="border-radius:10px;background:#333333;outline:none;color:#fff;padding:5px;margin-top:10px;">
                        <option :selected="'always' == swap.swap_time" style="color:#fff;" value="always">Выполнять всегда</option>
                        <option :selected="'ifOne' == swap.swap_time" style="color:#fff;" value="ifOne">Если выполнено одно из условий</option>
                        <option :selected="'ifAll' == swap.swap_time" style="color:#fff;" value="ifAll">Если выполнены все условия</option>
                        <option :selected="'never' == swap.swap_time" style="color:#fff;" value="never">Никогда не выполнять</option>
                    </select>
                    <!-- btns -->
                    <div style='display:inline-flex;align-items:center;justify-content:space-between;margin-top:20px;'>
                        <p class="WhiteBlack" style='font-size:20px;'>Кэшировать</p>
                        <span style='margin-left:10px;' @click='cache(swap_id);swap.swap_cache = !swap.swap_cache;' class='check_btn'><span :class='[{"checked_btn_span": !swap.swap_cache}, {"unchecked_btn_span": swap.swap_cache}]'></span></span>
                    </div>
                    <div v-if="['img', 'text', 'link'].indexOf(swap.swap_type) == -1 && swap.swap_type != 'statistic'" style='display:inline-flex;align-items:center;justify-content:space-between;margin-top:20px;' >
                        <p class="WhiteBlack" style='font-size:20px;'>Менять текст</p>
                        <span style='margin-left:10px;' @click='swap_changename(swap_id);swap.swap_changename = !swap.swap_changename;' class='check_btn'><span :class='[{"checked_btn_span": !swap.swap_changename}, {"unchecked_btn_span": swap.swap_changename}]'></span></span>
                    </div>
                    <button style="background:tomato;" @click="addif_mode(swap, false)" v-if="swap.addif" >Отменить</button>
                </div>
                <div class="swap_graphic" :style="{'max-height': !swap?.['chart_settings']?.['status'] ? 0 : get_canvas_height(swap_id)+'px'}">
                    <div id="chart_options">
                        <div id="chart_type" style="padding:0;">
                            <span @click="chart_model(swap_id, 'line')" data-chart="line" :class="{active_chart: swap?.['chart_settings']?.['type'] == 'line'}"></span>
                            <span @click="chart_model(swap_id, 'bar')" data-chart="bar" :class="{active_chart: swap?.['chart_settings']?.['type'] == 'bar'}"></span>
                        </div>
                        <div id="pereod" style="display:inline-flex;align-items:center;justify-content:center;">
                            <input @change="chart_update(swap_id, 'from')" type="date" :value="get_time(swap?.['chart_settings']?.['from'])" id='prereod_from'>
                            <span style="height:5px;width:10px;background:#fff;display:block;"></span>
                            <input @change="chart_update(swap_id, 'to')" :value="get_time(swap?.['chart_settings']?.['to'])" type="date" id='prereod_to'>
                        </div>
                        <select @change="chart_update(swap_id, 'date_type')" id="pereod_type" style="margin-left:10px;">
                            <option :selected="swap?.['chart_settings']?.['date_type'] == 'days'" value="days">Дни</option>
                            <option :selected="swap?.['chart_settings']?.['date_type'] == 'mounths'" value="mounths">Месяцы</option>
                            <option :selected="swap?.['chart_settings']?.['date_type'] == 'years'" value="years">Годы</option>
                        </select>
                    </div>
                    <div class="chart_bg">
                        <canvas :id="'myChart_' + swap_id"></canvas>
                    </div>
                </div>
            </div>
            <div class="v-cloak-text v-cloak-on" style="height:40px !important;width:300px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:40px !important;width:150px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:40px !important;width:350px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
            <div class="v-cloak-text v-cloak-on" style="height:120px !important;width:400px;" v-cloak></div>
        </div>
    </section>
	<?php appendfooter();?>
</body>
<script src="/scripts/libs/chart.js"></script>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>
