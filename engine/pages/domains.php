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
	<script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
	<?php navigation('domains', $info); ?>
	<section id='container' >
		<?php section_header('Домены', 'gear.png'); ?>
		<div id = 'column1' style="margin:20px;">
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.1s' >Создать новый домен</h2>
			<div style='display:inline-flex;align-items:center;margin-top:10px'>
				<input type='text' v-model='new_domain' style="border-radius:10px;" class='changable_input wow bounceInUp domain_input' data-wow-delay='0.15s' placeholder='Введите новый домен'/>
				<button @click="add_domain()" v-if='!load' class='add_dom_btn wow bounceInUp' data-wow-delay='0.15s'>добавить</button>
				<span v-else class='domain-loader'></span>
			</div>
			<h2 class="header1 wow bounceInUp" data-wow-delay='0.2s'>Существующие домены</h2>
			<div class="domains_body">  
				<div class="domain wow bounceInUp" data-wow-delay='0.25s'  v-for='(domain, index) in domains'>
					<div style="display:flex;flex-direction:column;">
						<p style="margin:0;">{{domain}}</p>
						<span style='display:inline-flex;align-items:center;margin-top:10px;'>
							<h2 style="font-size:13px;">Персональные настройки</h2>
							<span style='margin-left:10px;' @click='personal_design(domain)' class='check_btn'>
								<span :class='[{"checked_btn_span": design_domains.indexOf(domain) == -1}, {"unchecked_btn_span": design_domains.indexOf(domain) != -1}]'></span>
							</span>
						</span>
					</div>
					<div @click="remove(index)" class="remove_domain"><span></span><span></span></div>
				</div>
			</div>
			<p class='text1 wow bounceInUp' v-if="Object.keys(domains).length <= 0" data-wow-delay='0.21s'>Тут пока ничего нет..</p>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.3s'>Получить код</h2>
			<p class='text1 wow bounceInUp' data-wow-delay='0.35s'>Поместите этот HTML-код - в тег body вашего сайта.</p>
			<textarea readonly id='textarea1' class='wow bounceInUp' data-wow-delay='0.4s'><!--InterHelper-->&#13;&#10&#13;&#10<noindex>&#13;&#10&#13;&#10<script type='text/javascript' src='https://<?php echo $_SERVER['HTTP_HOST'];?>/HelperCode/Helper'></script>&#13;&#10&#13;&#10</noindex>&#13;&#10&#13;&#10<!--InterHelper--></textarea>
			<p style='display:inline-flex;' class='text1 wow bounceInUp' data-wow-delay='0.45s'>Если у вас возникла проблема, обратитесь в нашу <a style='text-decoration:none;margin-left:10px;color:#0ae;' href='/page/help'>службу поддержки</a>.</p>
		</div>
	</section>
	<?php appendfooter(); ?>
	<script src='/scripts/libs/vue.js'></script>
	<script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>