<?php
	session_start();
    if (!isset($_SESSION["admin"])) { header("Location: /engine/admin/login");  exit; }
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . '/engine/config.php');
    $tariff_path = VARIABLES["photos"]["tariff_photo"]["upload_path"];
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/admin_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
</head>
<body>
	<?php admin_navigation('tariff'); ?>
    <section id='container'>
        <?php section_header('тарифы', 'tariff.png'); ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp'  data-wow-delay='0.1s'>Создать новый тариф</h2>
            <form id='add_assistent_block' @submit.prevent = 'add_tariff' method ='post' action = '/engine/changeSettings'>
                <div><h2 class='WhiteBlack'>Название:</h2><input name='name' type='text'/></div>
                <div><h2 class='WhiteBlack'>Цена (число):</h2><input name='price' type='number'/></div>
                <div><h2 class='WhiteBlack'>Посещений / месяц (число):</h2><input name='visits' type='number'/></div>
                <div><h2 class='WhiteBlack'>Лидов в CRM (число):</h2><input name='leeds' type='number'/></div>
                <div><h2 class='WhiteBlack'>Клиентов в CRM (число):</h2><input name='clients' type='number'/></div>
                <div><h2 class='WhiteBlack'>Ассистентов (число):</h2><input name='assistents' type='number'/></div>
                <div><h2 class='WhiteBlack'>Отделов (число):</h2><input name='departaments' type='number'/></div>
                <div><h2 class='WhiteBlack'>Доменов (число):</h2><input name='domains' type='number'/></div>
                <div><h2 class='WhiteBlack'>Задач в CRM (число):</h2><input name='tasks' type='number'/></div>
                <div><h2 class='WhiteBlack'>Вариантов в CRM (число):</h2><input name='variants' type='number'/></div>
                <div><h2 class='WhiteBlack'>Столбцов в CRM для лидов(число):</h2><input name='leed_columns' type='number'/></div>
                <div><h2 class='WhiteBlack'>Столбцов в CRM для клиентов (число):</h2><input name='client_columns' type='number'/></div>
                <div><h2 class='WhiteBlack'>Посетитель сверх лимита (число):</h2><input name='limit' type='number'/></div>
                <div><h2 class='WhiteBlack'>Описание тарифа:</h2><input name='about' type='text'/></div>
                <button v-if='!loader' style='box-shadow:none;' type='submit'>Добавить</button>
                <span v-else class='load_span'></span>
            </form>
            <div v-if='!add' id='add_new' class='wow bounceInUp' data-wow-delay='0.15s'>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Добавить</div>
            </div>
            <div v-else id='add_new' >
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Закрыть</div>
            </div>
            <h2 class='header1 wow bounceInUp'  data-wow-delay='0.2s'>Управление тарифами</h2>
            <p class='text1 wow bounceInUp'  data-wow-delay='0.25s'>Чтобы сделать тариф бесплатным, введите 0 в поле для цены</p>
            <p class='text1 wow bounceInUp' data-wow-delay='0.3s'>Чтобы сделать неограниченное количество, введите 0 в поле для значения</p>
            <div class='tarif_block wow bounceInUp' data-wow-delay='0.35s'>
                <div class='tarif_block wow bounceInUp' data-wow-delay='0.35s'>
                    <div class='tarif bgblackwhite' style='width:410px;' v-for='(edition, index) in editions'> 
                        <label @change='change(null, null, index, "img")' class='tarif-img' style='cursor:pointer;' :style='"background-image: url(<?php echo $tariff_path; ?>"+edition.img+");"'>
                            <input type='file' style='display:none;'/>
                        </label>
                        <input  @change='change(null, null, index, "name")' class='WhiteBlack changable_input' style='text-align:center;font-weight:900;' :value='edition.name' />
                        <p class='WhiteBlack'>Скрыть / показать</p>
                        <span @click='change(null, null, index, "type")' style='margin-top:10px;' class='check_btn'>
                            <span :class='[{"unchecked_btn_span": edition.type == "visible"}, {"checked_btn_span": edition.type == "hidden"}]'></span>
                        </span>
                        <button style='width:150px;' class ='remove_assistent' @click='remove_tariff(index)' type='submit'>Удалить</button>
                        <span style='color:#0ae;font-size:25px;font-weight:bold;display:inline-flex;'>
                            <input class='WhiteBlack changable_input' @change='change(null, "value", index, "cost")' style='width:153px;text-align:end;':value='edition.cost.value' placeholder='Цена' />
                            <input class='WhiteBlack changable_input'@change='change(null, "text", index, "cost")'  style='width:153px;':value='edition.cost.text' placeholder='Валюта' />
                        </span>
                        <span v-for='(fitcha, fitchaindex) in edition.include' style='display:inline-flex;'>
                            <input :title="fitcha.text_before" class='WhiteBlack changable_input' @change='change(fitchaindex, "text_before", index, "include")' style='width:133px;text-align:end;':value='fitcha.text_before' placeholder='Описание' />
                            <input class='WhiteBlack changable_input' @change='change(fitchaindex, "value", index, "include")' style='width:133px;text-align:center;':value='fitcha.value' placeholder='Значение' />
                            <input :title="fitcha.text" class='WhiteBlack changable_input' @change='change(fitchaindex, "text", index, "include")' style='width:133px;text-align:start;':value='fitcha.text' placeholder='Описание' />
                        </span>
                        <span>
                            <textarea @change='change(null, "tarif_text", index, "personal_page_info")' style='padding:10px;width:400px;height:120px;resize:none;border-radius:20px;'class='WhiteBlack changable_input' >{{edition.personal_page_info.tarif_text}}</textarea>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>