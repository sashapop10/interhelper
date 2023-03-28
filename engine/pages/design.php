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
	<?php navigation('design', $info); ?>
	<section id='container'>
		<?php section_header('Дизайн', 'design-thinking.png'); ?>
		<div id = 'column1'>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.05s'>Дизайн для</h2>
			<select class="changable_input wow bounceInUp" data-wow-delay='0.05s' @change="selected_domain = $event.target.value">
				<option style="background:#252525;color:#fff;" :selected="selected_domain == domain" :value="domain" v-for="(domain, index) in domains">{{domain == 'deffault' ? 'по умолчанию' : domain}}</option>
			</select>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Позиция</h2>
			<div style="display:inline-flex;flex-wrap:wrap;"> 
				<div>
					<div id='chat_position' class='wow bounceInUp bgblackwhite' data-wow-delay='0.15s'>
						<span v-for='position in positions' @click="new_position(position)" :class='"change_position_ajax "+position+` `+(active_position == position ? `active_position` : ``)+""'></span>
					</div>
					<p style='margin-top:10px;' class='WhiteBlack wow bounceInUp' data-wow-delay='0.2s'>Широкоэкранное позиционирование</p>
				</div>
				<div style="margin-left:20px;">
					<div id='mobile_chat_position' class='wow bounceInUp bgblackwhite' data-wow-delay='0.22s'>
						<span v-for='position in positions' @click="new_mobile_position(position)" :class='"change_mobile_position_ajax "+position+` `+(mobile_active_position == position ? `mobile_active_position` : ``)+""'></span>
					</div>
					<p style='margin-top:10px;' class='WhiteBlack wow bounceInUp' data-wow-delay='0.23s'>Мобильное позиционирование</p>
				</div>
			</div>
			<p class='text1 wow bounceInUp' data-wow-delay='0.24s'>От выбранной позиции зависит тип кнопки</p>
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.25s'>Размеры</h2>
			<p class="text1  wow bounceInUp" data-wow-delay='0.28s'>Только для широкоэкранной версии</p>
			<div class='size_cont wow bounceInUp' data-wow-delay='0.35s'>
				<h2 class='header1 WhiteBlack' style='color:#fff;text-transform: inherit;margin:0;'>Использовать свои размеры</h2>
				<span style='margin-left:10px;' @click='status("PersonalSize")' class='check_btn'>
					<span :class='[{"checked_btn_span": !PersonalSize}, {"unchecked_btn_span": PersonalSize}]'></span>
				</span>
			</div>
			<div class='button_size wow bounceInUp '  data-wow-delay='0.4s' :style='[PersonalSize ? {"max-height": "35em"} : {"max-height": "0em"}]' >
				<div v-for="(info, index) in sizes" class='inline_margin_top_20'>
					<h2 class='header1 WhiteBlack transform_intehit_margin_0'>{{info.text}}</h2>
					<input @change='change("sizes", index)' class='changable_input self_opts_inp' name='btn_svg_size' type='number' :value='info.value' />
				</div>
			</div>
			<div id='chat_color' style='margin-top:20px;' class='wow bounceInUp ' data-wow-delay='0.1s'>
				<h2 class='header1' style="margin-top:0px;">Статус чата</h2>
				<span style='display:inline-flex;align-items:center;margin-top:20px;'>
					<h2 class='header1 WhiteBlack' style="margin-top:0px; color: #fff; text-transform: inherit;">Включить статус чата</h2>
					<span style='margin-left:10px;' @click='status("chat_status_checkbox")' class='check_btn'>
						<span :class='[{"checked_btn_span": !chat_status_checkbox}, {"unchecked_btn_span": chat_status_checkbox}]'></span>
					</span>
				</span>
				<div style='margin-top:0;' :style='[chat_status_checkbox ? {"max-height": "15em"} : {"max-height": "0em"}]' id='chat_status_block' >
					<div v-for="(color, color_index) in status_colors" id='message_window_errorcolor'>
						<label class='design_btns' :style='"background: "+color.value+";"'>
						<input @change='change("design", color_index)' class='changable_color' style='display:none;' name='interhelperStatusOnlinecolor' type = 'color' v-model:value='color.value'></label> 
						<h2 class='header1 WhiteBlack'>{{color.text}}</h2>
					</div>
				</div>
				<h2 class='header1' style='margin-bottom:20px;'>Дизайн</h2>
				<div v-for="(part, part_name) in colors" style="display:flex;flex-direction:column;align-items:flex-start;justify-content:start;">
					<p style='margin:10px;color:#0ae;font-size:22px;'>{{part_name}}</p>
					<div v-for="(info, color_index) in part" style="display:flex;flex-direction:column;align-items:flex-start;justify-content:start;">
						<div style="display:inline-flex;align-items:center;">
							<label class='design_btns' :style='"background: "+info.value+";"'>
							<input @change='change("design", color_index)' class='changable_color' style='display:none;' name='InterHelperUmessageColor' type = 'color' v-model:value='info.value'></label>
							<h2 class='header1 WhiteBlack'>{{info.text}}</h2>
						</div>
						<p style="margin:0;margin-top:10px;"v-if="info.about" class="text1">{{info.about}}</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php appendfooter(); ?>
	<script src='/scripts/libs/vue.js'></script>
	<script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>