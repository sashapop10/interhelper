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
<head>
    <meta charset="utf-8">
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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Consultation</h2></div></div>
			<div id='middle_part'> 
			<h2 class='header1'>Пользователи нуждаются в помощи!</h2>
			<div id='consulation_choice_block'>
				<div class='consulation_choice'>
					<h2>Name Surname</h2>
					<p>255.255.255.0</p>
					<p>email</p>
					<div class='choose_button'>Помочь</div>
				</div>
			</div>
			<h2 style='position:relative;bottom:20px;' class='header1'>Чат</h2>
			<div id='consulation_chat_block'>
				<div id='consulation_chat_window'>

				<div class='message_by_user'>
				<p>Name Surname</p>
				<p>message</p>
				<p>12:00</p>
				</div>

				<div class='message_by_me'>
				<p>Name Surname</p>
				<p>message</p>
				<p>12:00</p>
				</div>

				</div>
				<div id='consulation_input_block'>
					<div id='button_under_input'>
					<span>#1</span>
					<span>#1</span>
					<span>#1</span>
					<span>#1</span>
					<span>#1</span>
					<span>#1</span>
					</div>
					<div id='consultant_input_under_buttons'>
					<textarea>Hello</textarea><button style='width:120px;'>Отправить</button>
					</div>
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
	background: url(scss/imgs/leftImg4.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt2').removeClass('target');
		$('.opt2').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

</body>
</html>