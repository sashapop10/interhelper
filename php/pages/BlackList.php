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
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" type="text/css" href="scss/reset.css">
	<link rel="stylesheet" type="text/css" href="scss/main.css">
	<link rel="stylesheet" type="text/css" href="scss/media.css">
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<link rel="stylesheet" type="text/css" href="/HelperCode/helper.css">
<script type="text/javascript" src="/HelperCode/Helper.js"></script>
<body>
	<?php
	if (isset($_SESSION["loginkey"])) {
		head();
		echo "
		
		<section id='container'>
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Black list</h2></div></div>
			<div id='middle_part'> 
			<h2 class='header1'>Заблокированные пользователи</h2>
					<div id = 'column2'>
					
					<div class='bad_user'>
					<h2 class='bad_user_name'>Name Surname</h2>
					<p class='bad_user_reason'>reason</p>
					<p class='bad_user_ip'>123.123.123.0</p>
					<p class='bad_user_mail'>mail@mail.com</p>
					<p class='banned_by'>Assistent name<p>
					<form><input style='font-size:1em; width:150px;' value='разблокировать' type='submit'/></form>
					</div>
					
					</div>
				
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		";
	}
	else{
			echo '<script>window.location.replace("/index.php");</script>';
	}
	appendfooter();
	?>
<style type="text/css">
	#section_name::after{
	content: '';
	margin-left: 20px;
	position: relative;
	height: 40px;
	width: 40px;
	background: url(scss/imgs/leftImg7.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt9').removeClass('target');
		$('.opt9').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>