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
	<script type="text/javascript" src="/HelperCode/Helper"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
	<?php navigation('options', $info); ?>
	<section id='container'>
		<?php section_header('Профиль', 'profile.png'); ?>	
		<div id='middle_part'> 
			<div id = 'column1'>
				<h2 class='header1 wow bounceInUp v-cloak-off' data-wow-delay='0.05s' v-cloak>Настройки для</h2>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:300px;"></p>
				<select class="changable_input wow bounceInUp v-cloak-off" @change="selected_domain = $event.target.value" data-wow-delay='0.05s' v-cloak>
					<option style="background:#252525;color:#fff;" :selected="selected_domain == domain" :value="domain" v-for="(domain, index) in domains">{{domain == 'deffault' ? 'по умолчанию' : domain}}</option>
				</select>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:250px;"></p>
				<div id='helper_name-block' class='wow bounceInUp v-cloak-off' data-wow-delay='0.1s' v-cloak>
					<h2 class='header1'>Название системы</h2>
					<p style='margin-top:10px;margin-bottom:10px;' class="WhiteBlack">Название при активном ассистенте</p>
					<input  @change="change('sys_name')" style='margin:0;width:350px;border-radius:10px;' maxlength='<?php echo VARIABLES["sysnamelen"]; ?>' type=text  class='changable_input ' name='sys_name' v-model='sys_name' :title='sys_name'/>
					<p style='margin-top:10px;margin-bottom:10px;' class="WhiteBlack">Название в отсутствие активных ассистентов</p>
					<input @change="change('SYSname_offline')" style='margin:0;width:350px;border-radius:10px;' maxlength='<?php echo VARIABLES["sysnamelen"]; ?>' type=text  class='changable_input ' name='SYSname_offline' v-model='SYSname_offline' :title='SYSname_offline'/>
				</div>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:250px;"></p>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:275px;"></p>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:300px;"></p>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:275px;"></p>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:300px;"></p>
				<p class='text1 wow bounceInUp v-cloak-off' v-cloak data-wow-delay='0.125s'>Название, указанное на кнопке interhelper и шапке окна чата.</p>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:400px;"></p>
				<h2 class='header1 wow bounceInUp v-cloak-off' data-wow-delay='0.15s' v-cloak>Первое сообщение системы</h2>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:350px;"></p>
				<textarea @change="change('helper_fmessage')" style='width:350px;height:150px;padding:10px;resize:none;border-radius:10px;' maxlength='<?php echo VARIABLES["fmessagelen"]; ?>' type='text'  class='changable_input  wow bounceInUp v-cloak-off' v-cloak data-wow-delay='0.2s' name='helper_fmessage' :title='first_msg' v-html="first_msg"></textarea>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:150px !important;width:350px;"></p>
				<p class='text1 wow bounceInUp v-cloak-off' data-wow-delay='0.25s' v-cloak>Первое сообщение, которое увидят ваши пользователи в окне чата interhelper.</p>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:400px;"></p>
				<h2 class='header1 wow bounceInUp v-cloak-off' v-cloak data-wow-delay='0.3s'>Уведомления</h2>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:200px;"></p>
				<span class='wow bounceInUp opt_tumb v-cloak-off' data-wow-delay='0.37s' v-cloak>
					<h2 class='text2 WhiteBlack'>Получать сообщения посетителей на e-mail</h2>
					<span style='margin-left:10px;' @click='change_mail_status()' class='check_btn'><span :class='[{"checked_btn_span": !email_msgs_status}, {"unchecked_btn_span": email_msgs_status}]'></span></span>
				</span>
				<input placeholder="Почта для сообщений посетителей" data-wow-delay='0.37s' style='margin:0;margin-top:10px;width:350px;border-radius:10px;' type="text" @change="change('msgs_email')" v-model="msgs_email" class="changable_input v-cloak-off wow bounceInUp" v-cloak>
				<span class='wow bounceInUp opt_tumb v-cloak-off' data-wow-delay='0.35s' v-cloak>
					<h2 class='text2 WhiteBlack'>Выключить графическое уведомление пользователей</h2>
					<span style='margin-left:10px;' @click='change_graphic_status()' class='check_btn'><span :class='[{"checked_btn_span": !graphic_status}, {"unchecked_btn_span": graphic_status}]'></span></span>
				</span>
				<span class='wow bounceInUp opt_tumb v-cloak-off' data-wow-delay='0.4s' v-cloak>
					<h2 class='text2 WhiteBlack'>Выключить звуковое уведомление пользователей</h2>
					<span style='margin-left:10px;' @click='change_audio_status()' class='check_btn'><span :class='[{"checked_btn_span": !audio_status}, {"unchecked_btn_span": audio_status}]'></span></span>
				</span>
				<span class='opt_tumb v-cloak-on' v-cloak>
					<p style="width:300px;" class='v-cloak-text2'></p>
					<span style='margin-left:10px;' class='check_btn v-cloak-block'><span class='checked_btn_span v-cloak-block'></span></span>
				</span>
				<p v-cloak class="v-cloak-on v-cloak-text2" style="margin-top:10px;height:40px !important;width:300px;"></p>
				<span class='opt_tumb v-cloak-on' v-cloak>
					<p style="width:300px;" class='v-cloak-text2'></p>
					<span style='margin-left:10px;' class='check_btn v-cloak-block'><span class='checked_btn_span v-cloak-block'></span></span>
				</span>
				<span class='opt_tumb v-cloak-on' v-cloak>
					<p style="width:300px;" class='v-cloak-text2'></p>
					<span style='margin-left:10px;' class='check_btn v-cloak-block'><span class='checked_btn_span v-cloak-block'></span></span>
				</span>
			</div>
		</div>
	</section>
	<?php appendfooter(); ?>
	<script src='/scripts/libs/vue.js'></script>
	<script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>