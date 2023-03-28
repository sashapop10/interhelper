<?php
	session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    $buy_tarif = "$('#loginingmenu').css('display', 'block'); containers.addClass('right-panel-active');";
	if(isset($_SESSION["boss"])){ 
        $buy_tarif = "window.location.href= '/engine/pages/tariff'";
		$user_id = $_SESSION["boss"];
		$sql = "SELECT photo, count(1) FROM users WHERE id = '$user_id'";
        $results = attach_sql($connection, $sql, 'row');
        if(intval($results[1]) != 0) $photo = str_replace(' ', '%20', $results[0]); 
        else {
            unset($_SESSION['boss']); 
            header("Location: /index"); exit;
        }
	}
    $editions_photos_path = VARIABLES["photos"]["tariff_photo"]["upload_path"];
    if(!isset($photo)) $photo = '';
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <link rel="stylesheet" href="/scss/main_page.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
	<title>INTERHELPER - Больше чем онлайн консультант</title>
	<link rel='shortcut icon' href='/scss/imgs/interhelper_icon.svg' type='image/png'>
</head>
<body>
    <?php head3($file_path, $photo); ?>
    <section class="tariffs_section vue_el2" style="margin-top:100px;">
        <h1 class="tt1 WhiteBlack wow bounceInUp" data-wow-delay='0s'>Сравнение тарифов</h1>
        <div class="tariff_block bgblackwhite">
            <div class="tariff_row wow bounceInUp" data-wow-delay='0.05s'>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Тариф</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Цена</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Уникальных посетителей</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Записей в CRM</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Задач в CRM</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Ассистентов</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Доменов</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Отделов</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Таблиц в CRM</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Столбцов в CRM</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Вариантов списка в CRM</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Разделов для шаблонов ответов</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Шаблонов ответов</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Подмен</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Рассылок</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Стоимость рассылки почты за единицу</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Цена за посетителя сверх лимита</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name">Подключение</h2></div>
            </div>
            <div class="tariff_row wow bounceInUp"  v-for="(edition, index) in editions">
                <div class="tariff_part tariff_photo_name">
                    <span onclick="window.location.href = '/page/tariff?tariff=index'" style="cursor:pointer;" class="tariff_photo" :style="'background-image:url(<?php echo $editions_photos_path; ?>'+edition.img+');'"></span>
                    <h2 class="WhiteBlack tariff_name">{{edition.name}}</h2>
                </div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.cost.value + ' ' + edition.cost.text}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.unique_visits.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.crm_items.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.tasks.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.assistents.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.domains.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.departaments.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.tables.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.table_columns.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.variants.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.fast_messages_dirs.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.fast_messages.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.swaper.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.autosender.value}}</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.mailer.value}} ₽</h2></div>
                <div class="tariff_part"><h2 class="WhiteBlack tariff_part_name" style="font-size:20px;">{{edition.include.unique_visits_limit.value}} ₽</h2></div>
                <div class="tariff_part"><h2 onclick="<?php echo $buy_tarif; ?>" class="WhiteBlack tariff_part_name" style="font-size:20px;color:#0ae !important;cursor:pointer;">подключить</h2></div>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
	<?php login_menu(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script src="/scripts/router?script=main"></script>
</html>