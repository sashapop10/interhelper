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
	$selectors = '';
	$json_array = json_decode($user_settings, JSON_UNESCAPED_UNICODE);
	foreach($json_array['departaments'] as $content){
	    $selectors .= '<option>'.$content.'</option>';
	}

    foreach($json_array['departament_checkboxes'] as $content){
	   $checkboxesStatus[] = strval($content);
	   
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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Departaments</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
					<div id='adddepartblock'>
					<h2 class='header1'>Добавить новый отдел</h2>
					<form class='send_ajax_form' id='adddepart'><input type='text' name='departament_add'  /><button type='submit'>Добавить</button></form>
					</div>
					<div id='remdepartblock' style='flex-direction:column;'>
					<h2 class='header1'>Удалить отдел</h2>
					<form class='send_ajax_form' id='remdepart'><select name='departament_remove'><option disabled selected></option>".$selectors."</select><button type='submit'>Удалить</button></form>
					</div>
					<div id='checkboxesblock'>
						<div><input type='checkbox' class='changable_input2' name = 'departament_check_1' value='".$checkboxesStatus[0]."' ".$checkboxesStatus[0]." /><h2 class='header1'>Пользователи будут выбирать отдел перед диалогом</h2></div>
						<div><input type='checkbox' class='changable_input2' name = 'departament_check_2' value='".$checkboxesStatus[1]."' ".$checkboxesStatus[1]." /><h2 class='header1'>Пользователи будут выбирать отправителя после приглашения в чат</h2></div>
						<div><input type='checkbox' class='changable_input2' name = 'departament_check_3' value='".$checkboxesStatus[2]."' ".$checkboxesStatus[2]." /><h2 class='header1'>Ввод имени должен быть заполнен у каждого пользователя</h2></div>
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
	background: url(scss/imgs/leftImg1.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt3').removeClass('target');
		$('.opt3').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>