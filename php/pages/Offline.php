<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/php/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/php/func.php"); 
    checkuser();
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SESSION["loginkey"])) {
	$clientEmail = strval($_SESSION["loginkey"]); 
	global $connection;
	$sql = "SELECT settings FROM users WHERE email='$clientEmail'";
	$resultcomand = mysqli_query($connection, $sql);
	$rows = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);
	foreach($rows as $row){
	$user_settings = $row['settings'];
	
	}
	
	$json_array = json_decode($user_settings, JSON_UNESCAPED_UNICODE);
	$feedback_enabled = $json_array['feedbackform']['feedbackENABLED'];
    $feedback_text = $json_array['feedbackform']['feedbackTEXT'];
    $feedback_email = $json_array['feedbackform']['feedbackMAIL'];
    $feedback_formename = $json_array['feedbackform']['feedbackformName'];
    
    $feedback_formphone = $json_array['feedbackform']['feedbackformPhone'];
    $feedback_formemail = $json_array['feedbackform']['feedbackformEmail'];
    if($feedback_enabled == 'unchecked'){
        $feedback_enabled = '';
    }
    if($feedback_formname == 'unchecked'){
        $feedback_formname = '';
    }
    if($feedback_formphone == 'unchecked'){
        $feedback_formphone = '';
    }
    if($feedback_formemail == 'unchecked'){
        $feedback_formemail = '';
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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Offline form</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
					<div id='innerColumn'>
					<div><input style='min-width: 20px;' class='checkbox_input changable_input2' name='feedback_form_checkbox' type ='checkbox' value=".$feedback_enabled."  ".$feedback_enabled."/><p>Показывать для пользователей форму обратной связи</p></div>
					<h2 class='header1'>Сообщение обратной формы.</h2>
					<div><textarea name='feedback_text' class='changable_input' style='height:300px;background:transparent;color:#fff;width:300px;border-top:3px solid #0ae;'>".$feedback_text."</textarea>
					</div>
					<div style='display:flex;flex-direction:column;align-items:flex-start; margin-left:0;'><input id='minp' name='feedback_target_email' class='changable_input' style='background:transparent;width:auto; color:#fff;border-bottom:3px solid #0ae;' value='".$feedback_email."' type='mail' /><p style='margin-top:20px; margin-left:0;'>Адрес электронной почты, на который отправляются сообщения посетителей из формы обратной связи будут отправлены.</p></div>
					<div><input class='checkbox_input changable_input2' name='feedback_input_checkbox_1' type ='checkbox' value=".$feedback_formename." ".$feedback_formename." /><p>Поле ввода имени должно<br/> быть заполнено</p></div>
					<div><input class='checkbox_input changable_input2' name='feedback_input_checkbox_2' type ='checkbox' value=".$feedback_formphone."  ".$feedback_formphone." /><p>Поле ввода телефона<br/> должно быть заполнено</p></div>
					<div><input  class='checkbox_input changable_input2'name='feedback_input_checkbox_3'  type ='checkbox' value=".$feedback_formemail."  ".$feedback_formemail." /><p>Поле ввода E-mail<br/> должно быть заполнено</p></div>
					</div>
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
	background: url(scss/imgs/leftImg6.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt8').removeClass('target');
		$('.opt8').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>