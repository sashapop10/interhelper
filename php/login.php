<?php
    $eror_count = 0;
    $error_info = '';
	//connection db
    include 'connection.php'; 
	global $connection;
	//register
	if (isset($_POST['User']) && isset($_POST['Email']) && isset($_POST['Password']) && trim($_POST['User']!='') && trim($_POST['Email']!='') && trim($_POST['Password'] !='')) {
		$visitor_ip = $_SERVER['REMOTE_ADDR'];
		$name = $_POST['User'];
		$pass = $_POST['Password'];
		$mail = $_POST['Email'];
		$emailexist = 'noexist';
		// pass shoud be bigger than
		
		if(strlen(trim($pass)) < 7 or strlen(trim($pass)) > 30)
		{
			$error_info .= '/ Паоль должен быть больше 7 символов и меньше 40! /';
			$eror_count+=1;
		}
		elseif(strlen(trim($mail)) < 3 or strlen(trim($mail)) > 30)
		{
			$error_info .= '/ Почта должна быть больше 7 и меньше 30 символов! /';
			$eror_count+=1;
		}
		$sql = "SELECT email FROM users WHERE email = '$mail'";
		$resultcomand = mysqli_query($connection, $sql);
		$mailExist = mysqli_fetch_row($resultcomand);
			// if user exist
   			 if ($mailExist[0] == $mail)
   			 {
   			 	$emailexist = 'exist';
				$error_info .= '/ Такая почта уже создана ! /';
				$eror_count+=1;
				
   			 }
   			 // if new user 
		if($emailexist != 'exist' && $eror_count == 0){
			$ip = $_SERVER['REMOTE_ADDR'];
			$pass = password_hash($pass, PASSWORD_BCRYPT);
			$today = date("H:i:s");

			$sql = "SELECT visits FROM guest WHERE ip='$visitor_ip'";
			$resultcomand = mysqli_query($connection, $sql);
			$user_visits = mysqli_fetch_row($resultcomand);
			$sql = "DELETE FROM guest WHERE ip = '$visitor_ip'";
			if ($connection->query($sql) === TRUE) {}
			$options = '{"departament_checkboxes":{"departament_check/1":"checked","departament_check/2":"unchecked","departament_check/3":"unchecked"},"InterHelperInvitesOptions":{"InvitesForAllsystem":"unchecked","AlwaysShowInvite":"unchecked","SYSname":"InterHelper","InviteText":"Здравствуйте, если есть вопросы, задавайте мне, я с радостью на них отвечу.","InviteForReturn":"Здравствуйте. Спасибо, что снова зашли. Я могу вам помочь?","ShowAfterPagesCount":"0","ShowAfterSecondsCount":"5","HideAfterPagesCount":"10","InvitesForPages":{}},"feedbackform":{"feedbackENABLED": "checked", "feedbackTEXT":"К сожалению, на данный момент нет активных консультантов, но вы можете задать свой вопрос через форму ниже. Мы обязательно Вам ответим.","feedbackMAIL":"'.$mail.'","feedbackformName":"unchecked","feedbackformEmail":"checked","feedbackformPhone":"checked"}, "InterHelperOptions":{"bgcolor":"#000","textcolor":"#fff","position_top":"top:85%;","position_left":"left:100%;","transform_translate":"tranform:translateY(-50%);","windowbgcolor":"#000","windowtextcolor":"#fff"}, "SYSmessages":{"endmessage":"Консультант закончил с вами диалог","FEEDBACKafktimeout":"300","AFKmessages":{"AFKmessage/1":{"AFKtimeout":"60","AFKmessage":"Подождите ответа консультанта."}}}}';
			$sql = "INSERT INTO users(id,name, password, email, ip, visits, lastVisit, settings,photo) VALUES (0,'".$name."','".$pass."','".$mail."','".$ip."','".$user_visits[0]."','".$today."', '".$options."','/user_photos/user.png')";

				if ($connection->query($sql) === TRUE) { 
					
					$response_info .= '/ Вы создали аккаунт! /';
					
   				}
   				else{
   					$error_info .= '/ Нет соединения с базой данных /';
					$eror_count+=1;
   				}
		}
		mysqli_close($connection);
	}
	//login
	elseif (isset($_POST['EmailL']) && isset($_POST['PasswordL']) && trim($_POST['EmailL']!='') && trim($_POST['PasswordL']!='')) {
		$log = $_POST['EmailL'];
		$pass = $_POST['PasswordL'];
		$sql = "SELECT password FROM users WHERE email = '$log'";
		$resultcomand = mysqli_query($connection, $sql);
		$hash = mysqli_fetch_row($resultcomand);
		if(password_verify($pass, $hash[0])){
			session_start();
			mysqli_close($connection);
			$_SESSION["loginkey"]=$log;
			$response_info = 'Вы вошли';
		}
		else{
			$error_info .= 'Ваш пароль или почта не существует!';
			$eror_count+=1;
		}
	}
	//exit
	elseif(isset($_POST['remove']))
	{
			mysqli_close($connection);
			session_start();
			unset($_SESSION['loginkey']);
			session_destroy();
			$response_info = 'Вы вышли!';
	}
	//rise filds not filled
	else{
			mysqli_close($connection);
			$error_info .= 'Не все поля заполнены';
			$eror_count+=1;
	}
	if ($eror_count >= 1){
		echo 'Ошибки: '.$error_info. ' Количество ошибок: ' .$eror_count;
	}
	else{
		echo $response_info;
	}
	
?>
