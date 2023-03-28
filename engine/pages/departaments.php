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
	<?php navigation('departaments', $info); ?>
	<section id='container'>
		<?php section_header('Отделы', 'leftImg1.png'); ?>
		<div id = 'column1'>
			<div id='adddepartblock' class='wow bounceInUp' data-wow-delay='0.1s'>
				<h2 class='header1'>Добавить новый отдел</h2>
				<div id='adddepart'>
					<input v-model='dep_name' maxlength='".VARIABLES["departamentlen"]."' placeholder="Введите имя отдела" class="changable_input" style="margin:0;" type='text' name='departament_add'  />
					<button @click="add_departament()" class='WhiteBlack' type='button' style='border:3px solid #0ae;'>Добавить</button>
				</div>
			</div>
			<div id='remdepartblock' style='flex-direction:column;text-align:center;' >
			<h2 class='header1 wow bounceInUp' data-wow-delay='0.15s'>Ваши отделы</h2>
			<div class='departaments_container' style='align-items: flex-start;' v-if='Object.keys(departaments).length > 0'>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-off' data-wow-delay='0.2s' v-for='(departament_inners, index) in departaments' v-cloak>
					<input @change="change(index)" class="changable_input" style="height:40px;margin:0;padding:10px;width:100%;font-size:25px;font-weight:bold;" :value="htmldecoder(index)"/>
					<button class='departament_remove_btn' @click="remove(index)" class='WhiteBlack' type='button'>Удалить</button>
					<div v-for="(page, page_index) in pages" style="display:inline-flex;align-items:center;justify-content:space-between;">
						<p class='departament_name WhiteBlack' style='word-break:break-word;text-align:center;'>{{page}}</p>
						<span style='margin-left:10px;' @click='update_departament(page_index, index)' class='check_btn'>
							<span :class='[{"checked_btn_span": departaments[index].indexOf(page_index) == -1}, {"unchecked_btn_span": departaments[index].indexOf(page_index) != -1}]'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
				<div style='background-color: rgb(37, 37, 37);' class='departament_container bgblackwhite wow bounceInUp v-cloak-on' v-cloak>
					<p class='v-cloak-text2' style="margin:0;width:300px;"></p>
					<p class='v-cloak-text2' style="margin:0;margin-top:10px;width:300px;"></p>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
					<div style="display:inline-flex;align-items:center;justify-content:space-between;margin-top:10px;">
						<p class='v-cloak-text2' style="margin:0;"></p>
						<span style='margin-left:10px;' class='check_btn v-cloak-block'>
							<span class='checked_btn_span v-cloak-block'></span>
						</span>
					</div>
				</div>
			</div>
			<p v-else class='WhiteBlack wow bounceInUp text1' data-wow-delay='0.2s'>Тут пока ничего нет, давайте это исправим !</p>
		</div>
	</section>
	<?php appendfooter(); ?>
	<script src='/scripts/libs/vue.js'></script>
	<script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>