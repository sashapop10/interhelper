<?php
	session_start();
	header("Access-Control-Allow-Origin: *");
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"]) && !isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$info = check_user($connection);
	$money = $info['info']['money'];
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
		<?php section_header('Оплата', 'payment.png'); ?>
		<div id = 'column1'>
			<div style='margin-top:0;'><h2 class='header1 wow bounceInUp' data-wow-delay='0.01s'>Баланс: <span style='color:#0ae;margin:10px;'><?php echo $money; ?></span> рублей</h2><div>
			<div style='margin-top:30px;' class='wow bounceInUp' data-wow-delay='0.05s'> <span class='statistic_button' onclick='window.location.href= "/engine/pages/tariff"'>Управление</span> <span class='statistic_button' style='background:#0ae;color:#fff;'>Оплата</span> </div>
			<h2 style="margin-top:30px;" class='header1 wow bounceInUp' data-wow-delay='0.05s'>Сумма платежа:</h2>
			<input class='bgblackwhite add_money_input wow bounceInUp WhiteBlack' style="border-radius:10px;border-color:#000;box-shadow:0 0 10px rgba(0, 0, 0, 0.5);color:#fff;margin:10px;margin-top:10px;" v-model='money' data-wow-delay='0.1s' placeholder='Сумма платежа' />
			<h2 style="margin-top:10px;" class='header1 wow bounceInUp' data-wow-delay='0.05s'>Способ оплаты:</h2>
			<div class='payments_container wow bounceInUp' style="align-items:flex-start;justify-content:flex-start;" data-wow-delay='0.15s'>
				<div class='payment bgblackwhite' style="border-color:#66cc33;">
					<span class='payment_logo' style='background-image:url(/scss/imgs/interkassa.png);' ></span>
					<h2 class='payment_name' style='color: #66cc33'>Интеркасса</h2>
					<form style='dysplay:none;' name="payment" id="form_pay" method="post" action="https://sci.interkassa.com/" enctype="utf-8">
						<input type="hidden" name="ik_co_id" value="6060dcbc38f29b64f076e5cb">
						<input type="hidden" name="ik_pm_no" value='<?php echo uniqid(); ?>' id="oplata-id">
						<input type="hidden" :value="money" name="ik_am" placeholder="Сумма пополнения" id="oplata-text">
						<input type="hidden" value='<?php echo (isset($_SESSION["boss"]) ? $_SESSION["boss"] : json_decode($_SESSION["employee"], JSON_UNESCAPED_UNICODE)['boss_id']) ?>' name="ik_x_login" placeholder="Логин" id="oplata" >
						<input type="hidden" name="ik_cli" value='<?php echo $info['info']['email']; ?>'>
						<input type="hidden" name="ik_cur" value="RUB">
						<input type="hidden" name="ik_desc" value="Пополнение счёта личного кабинета INTERHELPER">
						<button class='payment_btn WhiteBlack' style="border-color:#66cc33;font-weight:bold;font-size:20px;border-radius:10px;" type='submit'>Пополнить</button>
					</form>
				</div>
				<div class='payment bgblackwhite' style="border-color:#009900;">
					<span class='payment_logo' style='background-image:url(/scss/imgs/sber.png);' ></span>
					<h2 class='payment_name' style='color: #009900'>Сбербанк</h2>
					<button @click='sberbank' class='payment_btn WhiteBlack' style="border-color:#009900;font-weight:bold;font-size:20px;border-radius:10px;">Пополнить</button>
				</div>
			</div>
		</div>
	</section>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
<?php 
	appendfooter(); 
	if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
	if(isset($_GET['log'])) echo "<script>alert('".$_GET['log']."');</script>";
?>
</html>