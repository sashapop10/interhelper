<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/php/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/php/func.php"); 
    checkuser();
    if(isset($_SESSION["loginkey"])){
        $clientEmail = strval($_SESSION["loginkey"]); 
    	global $connection;
	    $sql = "SELECT domain FROM users WHERE email='$clientEmail'";
    	$resultcomand = mysqli_query($connection, $sql);
    	$domain = mysqli_fetch_row($resultcomand);
    	$user_domain = $domain[0];
    	$sql = "SELECT email FROM assistents WHERE domain='$user_domain'";
    	$resultcomand = mysqli_query($connection, $sql);
    	$domain = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);
    	$assistents = '';
    	foreach($domain as $email){
    	    $assistents .= '<option>'.$email['email'].'</option>';
    	}
    
    }
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
<body><link rel="stylesheet" type="text/css" href="/HelperCode/helper.css">
<script type="text/javascript" src="/HelperCode/Helper.js"></script>
	<?php
	if (isset($_SESSION["loginkey"])) {
		head();
		echo "
		
		<section id='container'>
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Dialog list</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
					<h2 class='header1'>История сообщений</h2>
					<form id='search' method= 'post' action='search.php'>
						<div><select><option disabled>Select employ</option>".$assistents."</select><p class='text1'>Ассистент</p></div>
						<div><input type='date'/><p class='text1'>Начало</p></div>
						<div><input type='date'/><p class='text1'>Конец</p></div>
					</form>
					<div id='searchResultBlock'>

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
	background: url(scss/imgs/leftImg8.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt10').removeClass('target');
		$('.opt10').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>