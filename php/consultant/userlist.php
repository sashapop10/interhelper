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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>User list</h2></div></div>
			<div id='middle_part'> 
			<div id='users_list_block'>
				<h2 style='color:#fff; font-size:1.1em;'>Выберите пользователя или найдите его <input type='text' style='margin-left:10px; height:40px; with:200px; padding-left:10px; outline:none; border:none;' value='name or email'/></h2>
				<div style='margin-top:50px;height:350px;overflow:auto; with:400px; display:flex; align-items:flex-start; justify-content: flex-start; flex-wrap:wrap;'>
					<div class='user_in_list'>
					<div style='margin-bottom:10px; height:60px;width:60px; background:url(scss/imgs/user.png) no-repeat center center; background-size:contain;'></div>
					<h2 class='user_in_list_name'>Name Surname</h2>
					<p class='user_in_list_mail'>email</p>
					<div class='choose_button'>Выбрать</div>
					</div>
					
				</div>
				<h2 class='header1'>Информация о выбранном пользователе</h2>
				<div id='user_in_list_info' style='box-shadow: 0 0 20px rgba(0,0,0,0.5);border-left: 2px dotted #0ae;'>
					<div style='margin-top:20px;margin-left:10px; height:60px;width:60px; background:url(scss/imgs/user.png) no-repeat center center; background-size:contain;'></div>
					<h2 class='text2'>Name Surname</h2>
					
					<p class='text2'>email</p>
					<p class='text2'>ip</p>
					<p class='text2'>country</p>
					<p class='text2'>brauser</p>
					<p class='text2'>visits</p>
					<p class='text2'>last visit</p>
					<p class='text2'>time</p>
					<p class='text2'>path through the site</p>
				</div>
			</div>
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		<script>$('#add_new_assistent').on('click', ()=>{
			$('#add_assistent_block').css('max-height', '30em');
			});</script>
		";
		appendfooter();
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
	background: url(scss/imgs/leftImg1.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt4').removeClass('target');
		$('.opt4').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

</body>
</html>