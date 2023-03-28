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
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
	<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
</head>
<body>
	<?php navigation('statistic', $info); ?>
	<section id='container'>
		<?php section_header('Статистика', 'crm_statistic.png'); ?>
		<div class="graphic_nav">
			<span data-filter="anticlicker">Антискликиватель</span>
			<span data-filter="CRM">CRM</span>
			<span data-filter="visitor" class="active_graphic">Посетители</span>
			<span data-filter="chat">Переписки</span>
			<span data-filter="consultation" >Обслуживание</span>
			<span data-filter="adds" >Реклама</span>
		</div>
		<div id='middle_part' style='padding-top:20px;'> 
			<div id="chart_options">
				<div id="chart_type">
					<span data-chart="line" class="active_chart"></span>
					<span data-chart="bar"></span>
				</div>
				<div id="pereod">
					<input type="date" id='prereod_from'>
					-
					<input type="date" id='prereod_to'>
				</div>
				<select id="pereod_type">
					<option selected value="days">Дни</option>
					<option value="mounths">Месяцы</option>
					<option value="years">Годы</option>
				</select>
			</div>
			<div id = 'column2' style='margin-top:0;background:#fff;border-radius:10px;' >
				<canvas id="myChart"></canvas>
			</div>
		</div>
		<h2 class="WhiteBlack" style="margin:20px;text-align:center;">UTM метки</h2>
		<div id="UTM_statistic" class="UTM_statistic_column">
			<div class="UTM_statistic_column" v-for="(value1, utm_source) in utm" v-if="utm_source != 'helper_status'">
				<div style="z-index:20;" class="UTM_statistic_line bgblackwhite"><span @click="update_status(value1)" :class="value1['helper_status'] ? 'open-utm' : 'close-utm'"></span><p class="WhiteBlack">{{utm_source}}</p></div>
				<div style="z-index:19;" class="UTM_statistic_column" v-for="(value2, utm_medium) in utm[utm_source]" v-if="value1['helper_status'] && utm_medium != 'helper_status'">
					<div style="z-index:18;" class="UTM_statistic_line bgblackwhite"><span @click="update_status(value2)" :class="value2['helper_status'] ? 'open-utm' : 'close-utm'"></span><p class="WhiteBlack">{{utm_medium}}</p></div>
					<div style="z-index:17;" class="UTM_statistic_column" v-for="(value3, utm_compaign) in utm[utm_source][utm_medium]" v-if="value2['helper_status'] && utm_compaign != 'helper_status'">
						<div style="z-index:16;" class="UTM_statistic_line bgblackwhite"><span @click="update_status(value3)" :class="value3['helper_status'] ? 'open-utm' : 'close-utm'"></span><p class="WhiteBlack">{{utm_compaign}}</p></div>
						<div style="z-index:15;" class="UTM_statistic_column" v-for="(value4, utm_content) in utm[utm_source][utm_medium][utm_compaign]" v-if="value3['helper_status'] && utm_content != 'helper_status'">
							<div style="z-index:14;" class="UTM_statistic_line bgblackwhite"><span @click="update_status(value4)" :class="value4['helper_status'] ? 'open-utm' : 'close-utm'"></span><p class="WhiteBlack">{{utm_content}}</p></div>
							<div style="z-index:13;" class="UTM_statistic_column" v-for="utm_term in utm[utm_source][utm_medium][utm_compaign][utm_content]" v-if="value4['helper_status'] && utm_term != 'helper_status'">
								<div class="UTM_statistic_line bgblackwhite"><p class="WhiteBlack">{{utm_term}}</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<p style="color:grey;text-align:center;width:100%;margin:0;font-size:20px;font-weight:bold;" v-if="Object.keys(utm).length == 0">Мы пока не нашли у ВАС UTM меток..</p>
		</div>
	</section>
</body>
<script src="/scripts/libs/chart.js"></script>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
<?php appendfooter(); ?>
</html>