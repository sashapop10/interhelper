<?php
	session_start();
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"]) && !isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$info = check_user($connection);
	if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
	if(isset($_GET['message'])) echo "<script>alert('".$_GET['message']."');</script>";
	$tariff_path = VARIABLES["photos"]["tariff_photo"]["upload_path"];
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
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
	<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
	<?php navigation('tariff', $info); ?>
	<section id='container'>
		<?php section_header('Тариф', 'payment.png'); ?>
		<div id = 'column1' style='margin-top:0;'>
			<div style='margin-top:0;'>
				<h2 class='header1 wow bounceInUp' data-wow-delay='0.01s'>Баланс: <span style='color:#0ae;margin:10px;'>{{money}}</span> рублей</h2>
			<div>
			<div style='margin-top:30px;' class='wow bounceInUp' data-wow-delay='0.05s'> 
				<span class='statistic_button' style='background:#0ae;color:#fff;'>Управление</span> <span class='statistic_button' onclick='window.location.href= "payment.php"'>Оплата</span> 
			</div>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Текущий тариф</h2>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.15s'>Тариф: <span style='color:#0ae;'>{{editions[tariff]?.name}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.2s' v-if='payday != "Не оплачен"'>Статус: <span style='color:#0ae;'>{{payday.split(' ').slice(0,2).join(' ')}} {{payday.split(' ')[2].split('-').reverse().join('.')}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.2s' v-else>Статус: <span style='color:#0ae;'>Не оплачен</span></p>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.22s'>Статистика</h2>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.25s'>Уникальных посетителей: <span style='color:#0ae;'>{{uip_count}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.unique_visits.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.3s'>Колонок в CRM: <span style='color:#0ae;'>{{columns}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.table_columns.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.35s'>Таблиц в CRM: <span style='color:#0ae;'>{{tables}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.tables.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.4s'>Ассистентов: <span style='color:#0ae;'>{{assistents_count}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.assistents.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.45s'>Количество отделов: <span style='color:#0ae;'>{{departaments}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.departaments.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.55s'>Количество доменов: <span style='color:#0ae;'>{{domains}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.domains.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.55s'>Записей в CRM: <span style='color:#0ae;'>{{items_count}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.crm_items.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.6s'>Количество активных задач: <span style='color:#0ae;'>{{crm_tasks_count}}</span> из <span style='color:#f90;'>{{editions[tariff]?.include.tasks.value}}</span></p>
			<p class='statistic_text WhiteBlack wow bounceInUp' data-wow-delay='0.67s'>Заблокированных посетителей: <span style='color:#0ae;'>{{banned_count}}</span></p>
			<p class='statistic_text WhiteBlack  wow bounceInUp' data-wow-delay='0.7s'>Пройден сверх лимит на: <span style='color:#0ae;'>{{uip_count - parseInt(editions[tariff]?.include.unique_visits.value) > 0 ? uip_count - parseInt(editions[tariff].include.unique_visits.value) : 0}}<span style='color:#0ae;'></p>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.67s'>Смена тарифа</h2>
			<p style='text-transform: inherit;' class='text1 wow bounceInUp' data-wow-delay='0.685s'>При смене тарифного плана остаток старого тарифа не сгорает! При смене тарифа вы получите {{parseInt(unused)}} ₽ за не использованные дни.</p>
			<div class='tarif_block wow bounceInUp' data-wow-delay='0.7s'>
				<div class='tarif bgblackwhite' v-for='(edition, index) in editions'> 
					<span class='tarif-img' :style='"background-image: url(<?php echo $tariff_path; ?>"+edition.img+");"'></span>
					<h2 class='WhiteBlack'>{{edition.name}}</h2>
					<p class='WhiteBlack'><span style='color:#0ae;font-size:25px;font-weight:bold;'>{{edition.cost.value + ' ' + edition.cost.text }}</span></p>
					<p class='WhiteBlack' style="text-align:center;" v-for='fitcha in edition.include'>{{fitcha.text_before + ' ' + fitcha.value + ' ' + fitcha.text}}</p>
					<span class='tarif_button' @click='change_tariff(index)' v-if ='tariff != index'>подключить</span>
					<span class='tarif_button' v-else style = 'background:#0ae;color:#fff;' >подключен</span>
				</div>
			</div>
		</div>
	</section>
	<?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>