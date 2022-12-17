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
	$interhelperBUTTONbgcolor = $json_array['InterHelperOptions']['bgcolor'];
    $interhelperBUTTONtextcolor = $json_array['InterHelperOptions']['textcolor'];
    $interhelperWINDOWbgcolor = $json_array['InterHelperOptions']['windowbgcolor'];
    $interhelperWINDOWtextcolor = $json_array['InterHelperOptions']['windowtextcolor'];
    $interhelperBUTTONpositionLEFT = $json_array['InterHelperOptions']['position_left'];
    $interhelperBUTTONpositionTOP = $json_array['InterHelperOptions']['position_top'];
    $interhelperBUTTONpositionTRANSFORM  = $json_array['InterHelperOptions']['transform_translate'];
    
    
	}
	
	//--------
	if($interhelperBUTTONpositionLEFT == "left:0%;" && $interhelperBUTTONpositionTOP == "top:0%;")
	{
//1	        
           
	        $first_position = 'class=active_position';
	        $second_position = $third_position = $fourth_position = $fith_position = $sixth_position= $seventh_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	elseif($interhelperBUTTONpositionLEFT == 'left:50%;' && $interhelperBUTTONpositionTOP == 'top:0%;')
	{
//2
	        $second_position = 'class=active_position';
	        $first_position = $third_position = $fourth_position = $fith_position = $sixth_position= $seventh_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	elseif($interhelperBUTTONpositionLEFT == 'left:100%;' && $interhelperBUTTONpositionTOP == 'top:0%;')
	{
//3
	        $third_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $fith_position = $sixth_position= $seventh_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	//-----
	elseif($interhelperBUTTONpositionLEFT == 'left:0%;' && $interhelperBUTTONpositionTOP == 'top:100%;')
	{
//4
	        $fourth_position = 'class=active_position';
	        $first_position = $second_position = $third_position = $fith_position = $sixth_position= $seventh_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	elseif($interhelperBUTTONpositionLEFT == 'left:50%;' && $interhelperBUTTONpositionTOP == 'top:100%;')
	{
//5
	        $fith_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $third_position = $sixth_position= $seventh_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	elseif($interhelperBUTTONpositionLEFT == 'left:100%;' && $interhelperBUTTONpositionTOP == 'top:100%;')
	{
//6
	        $sixth_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $fith_position = $third_position= $seventh_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	
	elseif($interhelperBUTTONpositionLEFT == 'left:0%;' && $interhelperBUTTONpositionTOP == 'top:85%;')
	{
//7
	        $seventh_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $fith_position = $sixth_position= $third_position = $eighth_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	//-------
	elseif($interhelperBUTTONpositionLEFT == 'left:0%;' && $interhelperBUTTONpositionTOP == 'top:15%;')
	{
//8
	        $eighth_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $fith_position = $sixth_position= $seventh_position = $third_position = $nineth_position = $tenth_position = 'class=change_position_ajax';
	}
	//-------------
	elseif($interhelperBUTTONpositionLEFT == 'left:100%;' && $interhelperBUTTONpositionTOP == 'top:85%;')
	{
//9
	        $nineth_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $fith_position = $sixth_position= $seventh_position = $eighth_position = $third_position = $tenth_position = 'class=change_position_ajax';
	}
	elseif($interhelperBUTTONpositionLEFT == 'left:100%;' && $interhelperBUTTONpositionTOP == 'top:15%;')
	{
//10
	        $tenth_position = 'class=active_position';
	        $first_position = $second_position = $fourth_position = $fith_position = $sixth_position= $seventh_position = $eighth_position = $third_position = $nineth_position = 'class=change_position_ajax';
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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Chat options</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
					<h2 class='header1'>Выбрать стартовую позицию кнопки InterHelper</h2>
					<div id='chat_position'>
						<span ".$first_position." id='first_position'></span>
						<span ".$second_position." id='second_position'></span>
						<span ".$third_position." id='third_position'></span>
						<span ".$fourth_position." id='fourth_position'></span>
						<span ".$fith_position." id='fith_position'></span>
						<span ".$sixth_position." id='sixth_position'></span>
						<span ".$seventh_position." id='seventh_position'></span>
						<span ".$eighth_position." id='eighth_position'></span>
						<span ".$nineth_position." id='nineth_position'></span>
						<span ".$tenth_position." id='tenth_position'></span>
					</div>
					<div id='chat_color'>
						<div id='chat_button_color'><label style='height:50px;width:50px;border-radius:50%;background:".$interhelperBUTTONbgcolor.";cursor-pointer;'><input class='changable_color' style='display:none;' name='InterHelperButtonColor' type = 'color' value='#000000'></label> <h2 class='header1'>Цвет кнопки InterHelper</h2></div>
						<div id='chat_button_color'><label style='height:50px;width:50px;border-radius:50%;background:".$interhelperBUTTONtextcolor.";cursor-pointer;'><input class='changable_color' style='display:none;' name='InterHelperButtonTextColor' type = 'color' value='#ffffff'></label> <h2 class='header1'>Цвет текста кнопки InterHelper</h2></div>
						<div id='chat_window_color'><label style='height:50px;width:50px;border-radius:50%;background:".$interhelperWINDOWbgcolor.";cursor-pointer;'><input class='changable_color' style='display:none;' name='InterHelperWindowColor' type = 'color' value='#000000'></label> <h2 class='header1'>Цвет окна InterHelper</h2></div>
						<div id='chat_window_color'><label style='height:50px;width:50px;border-radius:50%;background:".$interhelperWINDOWtextcolor.";cursor-pointer;'><input class='changable_color' style='display:none;' name='InterHelperWindowTextColor' type = 'color' value='#ffffff'></label> <h2 class='header1'>Цвет текста окна InterHelper</h2></div>
					</div>
					</div>
				
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		";
		ajaxs();
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
	background: url(scss/imgs/leftImg3.png) no-repeat center center;
	background-size: contain;
	}
	
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt5').removeClass('target');
		$('.opt5').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>