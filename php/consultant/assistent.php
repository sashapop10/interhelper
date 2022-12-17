<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/php/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/php/func.php"); 
    checkuser();
	$ip = $_SERVER['REMOTE_ADDR'];
	ip($ip);
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="scss/reset.css">
	<link rel="stylesheet" type="text/css" href="scss/main.css">
	<link rel="stylesheet" type="text/css" href="scss/media.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<link rel="stylesheet" type="text/css" href="/HelperCode/helper.css">
<script type="text/javascript" src="/HelperCode/Helper.js"></script>
	<?php
	if (isset($_SESSION["loginkey"])) {
		head2();
		echo "
	
		<section id='container'>
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2 >settings</h2></div></div>
			<div id='middle_part'> 
			<div>
				<h2 class='header1'>Имя с фамилией</h2>
				<input style='height:60px; min-width:200px; background:#fff; padding-left:10px; border:none; outline:none;margin-top:50px; ' type='text' value='Name Surname'/>
				<p class='text1'>Имя оператора, которое увидят ваши клиенты.</p>
			</div>
			<div>
				<h2 class='header1'>Текст приветствия</h2>
				<input style='height:60px; width:300px; background:#fff; padding-left:10px; border:none; outline:none;margin-top:50px;word-wrap:break-word; ' type='text' value='Buttle cty AAAAAAAAAAAA!'/>
				<p class='text1'>Текст приветствия для оператора.</p>
			</div>
			<div id='passwordblock'>
				<h2 class='header1'>Пароль</h2>
				<p id='changepass'>Сменить пароль</p>
				
			</div>
			<div>
				<h2 class='header1'>Фото</h2>
				<div id='assistent_img_place'></div>
				<input  id='add_photo' type='file' style='display:none;'/>
				<label for='add_photo' style='margin-top:20px;height:30px;border-radius:10px; width:180px; cursor: pointer; color:#fff; background:#0ae; display:flex; align-items:center; justify-content: center;'>Выбрать файл</label>
				<p class='text1'>Поддерживаемые форматы JPG,PNG,GIF.</p>
			</div>
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		<script>$('#add_new_assistent').on('click', ()=>{
			$('#add_assistent_block').css('max-height', '30em');
			});</script>
		";
		appendfooter();
		echo "<script>$('#changepass').on('click', () => {
	$('#changepass').css('display', 'none');
	$('#passwordblock').append('<h3>Print password from your account, password must be bigger than 6 symbols<h3>');
	$('#passwordblock').append('<form method = post action=http://interhelper/php/datasaver.php><div id=pass_top><input  type=password placeholder=Old password/><input  type=password placeholder=New password/><input  type=password placeholder=Repeat new password/></div><div id =pass_floor><button type=submit>Change password</button><p onclick=cancel() id=cancelpass>Cancel</p></div></form>');
});</script>";
	}
	else{
			echo '<script>window.location.replace("/index.php");</script>';
	}
	?>
	<style type="text/css">
	#section_name::after{
	content: '';
	margin-left: 20px;
	position: relative;
	height: 40px;
	width: 40px;
	background: url(scss/imgs/admin.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt1').removeClass('target');
		$('.opt1').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

</body>
</html>