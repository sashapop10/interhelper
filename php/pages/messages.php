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
	$endmessage = $json_array['SYSmessages']['endmessage'];
    $feedbacktimer = $json_array['SYSmessages']['FEEDBACKafktimeout'];
    $message_count = 0;
    foreach($json_array['SYSmessages']['AFKmessages'] as $name => $values){
        
        $message_after[] = $values['AFKtimeout'];
        $message_message[] = $values['AFKmessage'];
        $message_count += 1;
        $message_name[] = $name;
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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>messages</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
						<div id='sys_mes_block'>
							<div>
							<h2>Сообщение, когда консультант закончил диалог</h2>
							<textarea class='changable_input' style='height:200px;border-top:3px solid #0ae;resize:none;padding-top:20px; '  name='end_message' id='finsh_message'>".$endmessage."</textarea>
							</div>
							<div>
							<div class='seconds_block'><h2>Показать форму обратной связи, если консультант не отвечает в течение<input type='number' class='changable_input'style='background:#0ae;width:80px;' name='feedback_sec' class='sec_count' value='".$feedbacktimer."' /> секунд</h2></div>
								<p class= 'text1'>Если вы установите 0, то форма обратной связи не появится.</p>
							</div>
							
							<div class='add_button_ajax' value ='add' name='add_new_message' id='add_new_mess_btn'>Добавить новое</div>
						</div>
					</div>
				    <div id='time_messages_body' style='display:inline-flex;flex-wrap:wrap;width:100%;height:auto;'></div>
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
	background: url(scss/imgs/leftImg5.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt7').removeClass('target');
		$('.opt7').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
	let appendbody = $('#time_messages_body');
	<?php 
	if (isset($_SESSION["loginkey"])) {
	    for($i =0; $i < $message_count; $i++){
	    echo "createmessage('$message_after[$i]','$message_message[$i]','$message_name[$i]');";
	    }
	}
	?>
	function createmessage(timer,message,name){
	    var time_message = $("<div class='time_message' style='margin:50px;'></div>");
	    var time_message_timer = $("<div class='seconds_block'><h2 style='color:#fff;font-size:1.1em;text-align:center;'>Сообщение, если консультант не отвечает в течение <input type='number' style='background:#0ae;width:80px;' data-message='"+name+"' name='timeMessageTimer' value='"+timer+"' style='height:200px;text-align:center;resize:none;border-top:2px #0ae solid;width:350px; ' class='changable_input_atmessages' name='afk_sec' class='sec_count' /> секунд</h2></div>");
	    var time_message_message = $("<textarea data-message='"+name+"' class='changable_input_atmessages' name='timeMessageText' style='height:200px;text-align:center;resize:none;border-top:2px #0ae solid;width:320px; '>"+message+"</textarea>");
	    
	    var form = $("<form class=send_ajax_form ></form>");
	    var time_message_name = $("<input style='display:none;' name='time_message_remove' value='"+name+"' />");
	    var time_message_remove = $('<button class=remove_assistent type=submit >Удалить</button>');
	    time_message.append(time_message_timer);
	    time_message.append(time_message_message);
	    time_message.append(form);
	    form.append(time_message_name);
	    form.append(time_message_remove);
	    appendbody.append(time_message);
	    
	}
	
</script>
<?php
if (isset($_SESSION["loginkey"])) {
    ajaxs();
}
?>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>