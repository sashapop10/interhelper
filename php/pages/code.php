<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/php/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/php/func.php"); 
    checkuser();
	$ip = $_SERVER['REMOTE_ADDR'];
	
	if (isset($_SESSION["loginkey"])) {
	$clientEmail = strval($_SESSION["loginkey"]); 
	global $connection;
	$sql = "SELECT domain FROM users WHERE email='$clientEmail'";
	$resultcomand = mysqli_query($connection, $sql);
	$rows = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);
	foreach($rows as $row){
	$user_domain = $row['domain'];
	}
	if($user_domain == ''){
	    $user_domain = 'не заполнено';
	}
	
	}
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
<body>
<link rel="stylesheet" type="text/css" href="/HelperCode/helper.css">
<script type="text/javascript" src="/HelperCode/Helper.js"></script>
	<?php
	if (isset($_SESSION["loginkey"])) {
		head();
		echo "
		
		<section id='container'>
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Get code</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
					    <h2 class='header1'>Домен</h2>
					    <input type='text' name='domain' class='changable_input' value='".$user_domain."'/>
						<h2 class='header1'>Получить код</h2>
						<p class='text1'>Поместите этот HTML-код - на тег вашего тела.</p>
						<textarea readonly id='textarea1'><!--InterHelper-->&#13;&#10&#13;&#10<link rel='stylesheet' type='text/css' href='HelperCode/helper.css'>&#13;&#10&#13;&#10<script type='text/javascript' src='HelperCode/Helper.js'></script>&#13;&#10&#13;&#10<!--InterHelper-->
						</textarea>
						<p class='text1'>Если у вас возникла проблема, обратитесь в нашу <br/> службу поддержки.</p>
						
					</div>
				    
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		";
	appendfooter();
	ajaxs();
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
	background: url(scss/imgs/gear.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt2').removeClass('target');
		$('.opt2').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>