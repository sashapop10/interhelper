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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Team chat</h2></div></div>
			<div id='middle_part'> 
			<h2 class='header1'>Выбрать чат</h2>
			<div style='margin-top:50px;height:350px;overflow:auto; with:400px; display:flex; align-items:flex-start; justify-content: flex-start; flex-wrap:wrap;' id='consultant_list_block'>
				<div class='consultant_at_list'>
					<h2>Общий</h2>
					<div class='choose_button'>Выбрать</div>
				</div>
			</div>
			<h2 style='position:relative; bottom:100px;' class='header1'>Чат</h2>
			<div id='consultant_chat_block'>
			<div id='consultant_chat_window'>
			<div class='message_by_other_consultants'>
			<p>Name Surname</p>
			<p>Hello Friend!</p>
			<p>12:00</p>
			</div>
			<div class='message_by_this_consultant'>
			<p>Name Surname</p>
			<p>Hello Friend! Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!Hello Friend!</p><p>12:00</p>
			</div>

	
			</div>
			<div id='consultant_input'>
			<div id='consultant_chat_options'><span class='consultant_chat_option'>#1</span><span class='consultant_chat_option'>#2</span><span class='consultant_chat_option'>#3</span><span class='consultant_chat_option'>#4</span></div>
			<div id='consultant_input_block'><textarea type='text'>HELLO</textarea><button style='width:120px;'>Отправить</button></div>
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
	background: url(scss/imgs/leftImg2.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt3').removeClass('target');
		$('.opt3').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

</body>
</html>