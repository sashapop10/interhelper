<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/php/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/php/func.php"); 
	checkuser();
	$ip = $_SERVER['REMOTE_ADDR'];
	// user info
	if (isset($_SESSION["loginkey"])) {
	$clientEmail = strval($_SESSION["loginkey"]); 
	global $connection;
	$sql = "SELECT name,position,signa,phone,operatorRules,photo FROM users WHERE email='$clientEmail'";
	$resultcomand = mysqli_query($connection, $sql);
	$rows = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);
	foreach($rows as $row){
	$user_name = $row['name'];
	$user_position = strval($row['position']);
	$user_signa = strval($row['signa']);
	$user_phone = strval($row['phone']);
	$user_operatorRules = strval($row['operatorRules']);
	$user_Photo = strval($row['photo']);
	}
	if($user_phone == ''){
	    $user_phone = 'не заполнено';
	}
	if($user_signa == ''){
	    $user_signa = $user_name.' , '.$user_position;
	}
	if($user_operatorRules == 'unchecked'){
	    $user_operatorRules = 'unchecked';
	}
	else{
	    $user_operatorRules = 'checked';
	}
	if($user_Photo == ''){
	   $user_Photo = '/user_photos/user.png';
	}
	$user_Photo = str_replace(' ', '%20', $user_Photo);
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
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Admin settings</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
					<div id='img_first_part'>
						<div id='userimg' style='background-image: url(".$user_Photo."); background-position:center; background-repeat:no-repeat;'></div>
						<div id='userinfo'>
							<div id='username'><input value = '".$user_name."' class=changable_input name=name /></div>
							<input id='status' value = '".$user_position."' class=changable_input name=position />
						</div>
					</div>
					<div id='img_sec_part'><input name=fileimg id=profileimg class='changable_input3' type=file /><label for='profileimg'>Загрузить фото</label>
						<p>Чтобы ваш сайт выглядил живее, <br/> у вас должно быть полное имя и фамилия</p>
					</div>
					<div id='emailblock'>
					<h2>Почта [login]</h2>
					<p>Этот адрес электронной почты используется для рассылки уведомлений и является <br/> логином для входа в личный кабинет.</p>
					<input type = 'mail' class='changable_input' name='changeEmail' value='".$clientEmail."'/>
					</div>
					<div id='passwordblock'>
						<h2>Пароль</h2>
						<p id='changepass'>change password</p>
					</div>
					<div id='numberblock'>
						<h2>Номер телефона сотрудника</h2>
						<input type=text  class=changable_input name=phone value='".$user_phone."'/>
					</div>
					<div id='signatureblock'>
						<h2>Подпись</h2>
						<p>Добавьте подпись, которая будет видна в конце вашего письма</p>
						<input type = 'text'  class=changable_input name=signa value = '".$user_signa."'/>
					</div>
					<div id='employmentstatus'>
						<h2>Установить на оператора</h2>
						<p>Только такие сотрудники могут разговаривать с людьми, отвечать на телефон <br/> или почту. Другие могут использовать командный чат, <br/> смотреть пользовательскую информацию, <br/> делать отчеты и пользоваться телефонией.</p>
						<input class=changable_input2 value='".$user_operatorRules."' name='userOperatorRules' type='checkbox' ".$user_operatorRules."/>
					</div>
				
					</div>
				
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		";
		//password
		echo "<script>$('#changepass').on('click', () => {
	$('#changepass').css('display', 'none');
	$('#passwordblock').append('<h3>Print password from your account, password must be bigger than 6 symbols<h3>');
	$('#passwordblock').append('<form class=ajax_change_form method=post action=/php/changeSettings.php><div id=pass_top><input  type=password name=oldpass placeholder=Old password/><input  type=password name=newpass placeholder=New password/><input  type=password name=repeatnewpass placeholder=Repeat new password/></div><div id =pass_floor><button type=submit>Change password</button><p onclick=cancel() id=cancelpass>Cancel</p></div></form>');
	
	 // pass ajax

    $('.ajax_change_form').submit(function(e) {
    
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: $(this).serialize(),

      success: function(data) {
        alert(data);
        location.reload();

      },
      error: function() {
        alert('There was some error performing the AJAX call! ');

      }
        });
    
    });

});


</script>";
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
	background: url(scss/imgs/admin.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt1').removeClass('target');
		$('.opt1').attr('class', 'active');
		$('.active p').css('color','#0ae');
		$('.active p').css('opacity','1');
	});
</script>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>