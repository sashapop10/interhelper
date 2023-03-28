<?php
	session_start();
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$info = check_user($connection);
	if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
	if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
	<title>InterHelper</title>
	<meta charset="utf-8">
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
	<?php navigation('profile', $info); ?>
	<section id='container'>
		<?php section_header('Профиль', 'profile.png'); ?>
		<div id = 'column1' style="margin:20px;">
			<div id='img_first_part' class='wow bounceInUp' data-wow-delay='0.1s' >
				<input name='fileimg' id='userimg_inp' @change="change('photo')" style='display:none;' type='file' />
				<label for='userimg_inp' id='userimg' :style='"background-image: url(<?php echo $file_path; ?>"+photo+");"'></label>
				<div id='userinfo'>
					<input style="border-radius:10px;margin-top:0;" v-model:value='name' class='changable_input' @change="change('name')" />
					<input style="border-radius:10px;" type = 'mail' class='changable_input' @change="change('email')" v-model:value='email'/>
				</div>
			</div>
			<div class='wow bounceInUp' data-wow-delay='0.15s' id='img_sec_part' style='margin-top:5px;'>
				<p>Чтобы ваш сайт выглядил живее, <br/> у вас должно быть полное имя и фамилия</p>
				<div id='animablock' class='wow bounceInUp' data-wow-delay='0.25s'>
					<h3 class='WhiteBlack' style='margin-right:10px;'><span style='color:tomato;'>Выключить</span> всплывающие <span style='color:#0ae;'>анимации</span></h3>
					<span @click="animations = !animations" class="check_btn">
						<span :class="!animations ? 'checked_btn_span' : 'unchecked_btn_span'"></span>
					</span>
				</div>
			</div>
			<div style='display:inline-flex;margin-bottom:30px;' class='wow bounceInUp' data-wow-delay='0.2s'>
				<div id='log_out-exit_btn' class='WhiteBlack' onclick="window.top.postMessage('exit', '*');" @click="exit()">Выйти из аккаунта</div>
				<p id='changepass' class="WhiteBlack" @click='pass = !pass' v-if='!pass'>Сменить пароль</p>
			</div>
			<div v-if='pass' style='margin-bottom:30px;'>
				<div id='pass_top'>
					<div style="width:300px;position:relative;">
						<input v-model:value="old" id="oldpass" type='password' placeholder='Старый пароль' />
						<span class="password_eye"></span>
					</div>
					<div style="width:300px;position:relative;">
						<input v-model:value="newpass" id="newpass" type='password' placeholder='Новый пароль' />
						<span class="password_eye"></span>
					</div>
					<div style="width:300px;position:relative;">
						<input v-model:value="repeat" id="repeatnewpass" type='password' placeholder='Повторите новый пароль' />
						<span class="password_eye"></span>
					</div>
				</div>
				<div id='pass_floor'>
					<button  @click="change_pass()">Сменить пароль</button>
					<p @click='pass = !pass' id='cancelpass'>Отмена</p>
				</div>
			</div>
		</div>
	</section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>