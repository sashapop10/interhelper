<?php
	header('Access-Control-Allow-Origin: *');
	session_start();
    $response = ["errors" => [], "success" => []];
    include 'connection.php'; 
	include "func.php"; 
	global $connection;
	$today = date('Y-m-d H:i:s');
	$today_date = date("Y-m-d");
	include 'config.php';
	$json = ["login" => VARIABLES["login"], "password"=>VARIABLES["password"], "type"=>"", "info" => []];
	$host = SERVERPATH . '/admin';
	$bonus = VARIABLES["starter_bonus"];
	$lol = $_POST;
    foreach ($lol as $index => $value){ $lol[$index] = htmlencrypt($lol[$index]); }
	if (isset($lol['user']) && isset($_POST['email']) && isset($_POST['password'])) { // регистрация
		$name = $lol['user']; $pass = trim($_POST['password']); $mail = mb_strtolower($_POST['email']); 
		if(isset($_POST['phone'])) $phone = $lol['phone'];
		else $phone = '';
		if(strlen(trim($pass)) < 7 or strlen(trim($pass)) > 30) array_push($response['errors'], ERRORS['uncorrect_new_pass']);
		elseif(strlen(trim($mail)) < 3 or strlen(trim($mail)) > 30) array_push($response['errors'], ERRORS['invalid_new_email']);
		elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)) array_push($response['errors'], ERRORS['uncorrect_new_email']);
		$sql = "SELECT count(1) FROM users WHERE email = '$mail'";
		$count = attach_sql($connection, $sql, 'row')[0];
		$sql = "SELECT time FROM unconfimed_users WHERE email = '$mail' ORDER BY unconfimed_users.time DESC";
		$reg_time = attach_sql($connection, $sql, 'row');
		if ($count > 0) array_push($response['errors'], ERRORS['email_already_exist']);
		if(isset($reg_time)){
			if (strtotime($reg_time[0].' +30 seconds') > strtotime($today)) array_push($response['errors'], 'Для повторной отправки подождите 30 секунд !');
		} 
		if(count($response['errors']) == 0){
			$pass = password_hash($pass, PASSWORD_BCRYPT);
			//$inique_hash_firstpart = password_hash($mail, PASSWORD_BCRYPT);
			//$inique_hash = uniqid($inique_hash_firstpart, true);
			// $sql = "INSERT INTO unconfimed_users(id, name, password, email, hash, time, phone) VALUES (0,'$name','$pass','$mail', '$inique_hash', '$today', '$phone')";
			// if ($connection->query($sql) === TRUE) { 
			// 	$response['success'] = ["response" => "Отлично. Теперь подтвердите вашу почту, письмо уже отправлено !"];
			// 	$result = send_mails(array(["email" => $mail, "name" => $name]), 'Подтверждение почты', VARIABLES['InterHelper'], VARIABLES['smtp_from'], VARIABLES['smtp_password'], VARIABLES['smtp_secure'], VARIABLES["smtp_port"], VARIABLES['smtp_host'], '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
			// 	<html>
			// 		<head>
			// 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			// 			<title>Подтверждение почты</title>
			// 		</head>
			// 		<body style="width:450px;height:300px;">
			// 			<div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
			// 				<img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
			// 				<h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Подтверждение почты</h1>
			// 				<p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Подтвердите почту перейдя по <a href="https://interhelper.ru/engine/login?hash='.$inique_hash.'">ссылке</a> </p>
			// 			</div>
			// 		</body>
			// 	</html>');
			// 	if(count($result['errors']) > 0) array_push($response['errors'], 'Ошибка рассылки, проверьте вводимые данные. '. implode(',', $result['errors']));
			// } else array_push($response['errors'], ERRORS['sql_error']); 
			$options = json_encode([
				"adds" => [
					"adds_trys" => 5,
					"adds_redirect" => "checked",
					"adds_autoban" => "unchecked",
				],
				"feedbackform" => [ // OFFLINE 1
					"feedbackENABLED" => "checked", 
					"feedbackTEXT" => "К сожалению, на данный момент нет активных консультантов, но вы можете задать свой вопрос через форму ниже. Мы обязательно Вам ответим.",
					"feedbackMAIL" => $mail,
					"feedbackformName" => "unchecked",
					"feedbackformEmail" => "checked",
					"feedbackformPhone" => "checked"
				],
				// "feedback_form" => [ // OFFLINE 2
				// 	"deffault" => [
				// 		"name" => "Offline форма",
				// 		"email" => $mail,
				// 		"text" => "К сожалению, на данный момент нет активных консультантов, но вы можете задать свой вопрос через форму ниже. Мы обязательно Вам ответим.",
				// 		"conditions" => [
				// 			"consultant_offline" => "consultant_offline",
				// 		],
				// 		"fields" => [
				// 			"name" => [
				// 				"name" => "Имя",
				// 				"type" => "text",
				// 			],
				// 			"email" => [
				// 				"name" => "Почта",
				// 				"type" => "email",
				// 			],
				// 		],
				// 	]
				// ],
				"InterHelperOptions" => [
					"deffault" => [
						"bgcolor" => "#333333",
						"PersonalSize" => "uncheked",
						"textcolor" => "#eeeeee",
						"scroll_color" => "#00aaee",
						"logobgcolor" => "#00aaee",
						"logodetailscolor" => "#ffffff",
						"window_shadow" => "#000000",
						"button_shadow" => "#000000",
						"position_type" => "nineth_position",
						"mobile_position_type" => "special4_position",
						"FrameColor" => "#222222",
						"AmessagesColor" => "#444444",
						"UmessagesColor" => "#00aaee",
						"btn_svg_size" => "30",
						"chatheader_font_weight" => "900",
						"chatheader_font_size" => "30",
						"msg_font_weight" => "100",
						"msg_font_size" => "13",
						"SvgColor" => "#00aaee",
						"windowbgcolor" => "#333333",
						"windowtextcolor" => "#eeeeee",
						"SYSname" => "InterHelper",
						"SYSname_offline" => "InterHelper",
						"SYSFmessage" => "Оставьте свое сообщение, мы ответим в ближайшее время.",
						"error_color" => "#c0392b",
						"modal_message_text_color" => "#eeeeee",
						"modal_message_color" => "#444444",
						"helper_btn_height" => "40",
						"helper_btn_width" => "260",
						"helper_btn_font" => "22",
						"helper_btn_font_weigt" => "700",
						"helper_svg_size" => "30",
						"modal_message_shadow_color" => "#eeeeee",
						"success_color" => "#1bb154",
						"StatusOfflinecolor" => "#eb2f1e",
						"StatusOnlinecolor" => "#0de761",
						"chat_status_checkbox" => "checked",
						"InterHelperInvitesOptions" => [
							"graphic_invite_status" => "unchecked",
							"audio_invite_status" => "unchecked"
						],
						"email_msgs_status" => "unchecked",
						"msgs_email" => $mail,
					]
				],
				"departaments" => [],
				"fastMessages" => [
					"main" => [],
				],
				"mailer" => [
					"deffault" => [
						"SMTPsecure" => "ssl",
						"SMTPserver" => "",
						"SMTPport" => "",
						"SMTPemail" => $mail,
						"SMTPpassword" => "",
						"sender_name" => "InterHelper",
						"mail_name" => "Рассылка, InterHelper",
					]
				],
			], JSON_UNESCAPED_UNICODE);
			$sql = "INSERT INTO users(id,name, password, email, settings, photo, domain, money, tariff, payday, time, phone) VALUES (0,'$name','$pass','$mail', '$options','user.png', '{\"domains\":{}}', 0, (SELECT value FROM variables WHERE name = 'starter_tariff'), '$today_date', '$today', '$phone')";
			$connection->query($sql);
			$sql = "SELECT id FROM users WHERE email = '$mail'";
			$user_id = attach_sql($connection, $sql, 'row')[0];
			$_SESSION["boss"] = $user_id;
			$sql = "INSERT INTO crm(id, owner_id, columns) VALUES (0, '$user_id', '{\"Лиды\": {\"deffault_columns\": {\"helper_info\": {\"display\":\"false\", \"priority\": -1, \"deffault\": \"\"}, \"helper_name\":{\"display\":\"false\", \"priority\": -2, \"deffault\": \"новый\"}, \"helper_photo\": {\"display\":\"false\", \"priority\": -3, \"deffault\": \"\"}}, \"table_columns\": {}}, \"Клиенты\": {\"deffault_columns\":{\"helper_info\": {\"display\":\"false\", \"priority\": -1, \"deffault\": \"\"}, \"helper_name\":{\"display\":\"false\", \"priority\": -2, \"deffault\": \"новый\"}, \"helper_photo\": {\"display\":\"false\",\"priority\": -3, \"deffault\": \"\"}}, \"table_columns\":{}}}')";
			$connection->query($sql);		
			$sql = "INSERT INTO statistic(id, owner_id, info, utm) VALUES (0, '$user_id', '{\"statistic\": {}}', '{\"statistic\": {}}')";
			$connection->query($sql);	
			$json["type"] = "add_boss";
			$json["info"] = ["id" => $user_id, "hash"=> $hash, "email"=> $mail]; 
			send_curl($json, $host);	
			session_start();
			$_SESSION["boss"] = $user_id;
			$response['success'] = ["link" => "/engine/pages/profile"];
		} 
	} elseif(isset($_GET['hash'])) { // подтверждение почты
		$hash = $_GET['hash'];
		$sql = "SELECT * FROM unconfimed_users WHERE hash = '$hash'";
		$results = attach_sql($connection, $sql, 'query');
		foreach($results as $result){
			$mail = $result["email"];
			$name = $result["name"];
			$password = $result["password"];
		}
		$sql = "SELECT count(1) FROM users WHERE email = '$mail'";
		$count = attach_sql($connection, $sql, 'row')[0];
		if(isset($mail) && !$count){
			$options = json_encode([
				"adds" => [
					"adds_trys" => 5,
					"adds_redirect" => "checked",
					"adds_autoban" => "unchecked",
				],
				"feedbackform" => [ // OFFLINE 1
					"feedbackENABLED" => "checked", 
					"feedbackTEXT" => "К сожалению, на данный момент нет активных консультантов, но вы можете задать свой вопрос через форму ниже. Мы обязательно Вам ответим.",
					"feedbackMAIL" => $mail,
					"feedbackformName" => "unchecked",
					"feedbackformEmail" => "checked",
					"feedbackformPhone" => "checked"
				],
				// "feedback_form" => [ // OFFLINE 2
				// 	"deffault" => [
				// 		"name" => "Offline форма",
				// 		"email" => $mail,
				// 		"text" => "К сожалению, на данный момент нет активных консультантов, но вы можете задать свой вопрос через форму ниже. Мы обязательно Вам ответим.",
				// 		"conditions" => [
				// 			"consultant_offline" => "consultant_offline",
				// 		],
				// 		"fields" => [
				// 			"name" => [
				// 				"name" => "Имя",
				// 				"type" => "text",
				// 			],
				// 			"email" => [
				// 				"name" => "Почта",
				// 				"type" => "email",
				// 			],
				// 		],
				// 	]
				// ],
				"InterHelperOptions" => [
					"deffault" => [
						"bgcolor" => "#333333",
						"PersonalSize" => "uncheked",
						"textcolor" => "#eeeeee",
						"scroll_color" => "#00aaee",
						"logobgcolor" => "#00aaee",
						"logodetailscolor" => "#ffffff",
						"window_shadow" => "#000000",
						"button_shadow" => "#000000",
						"position_type" => "nineth_position",
						"mobile_position_type" => "special4_position",
						"FrameColor" => "#222222",
						"AmessagesColor" => "#444444",
						"UmessagesColor" => "#00aaee",
						"btn_svg_size" => "30",
						"chatheader_font_weight" => "900",
						"chatheader_font_size" => "30",
						"msg_font_weight" => "100",
						"msg_font_size" => "13",
						"SvgColor" => "#00aaee",
						"windowbgcolor" => "#333333",
						"windowtextcolor" => "#eeeeee",
						"SYSname" => "InterHelper",
						"SYSname_offline" => "InterHelper",
						"SYSFmessage" => "Оставьте свое сообщение, мы ответим в ближайшее время.",
						"error_color" => "#c0392b",
						"modal_message_text_color" => "#eeeeee",
						"modal_message_color" => "#444444",
						"helper_btn_height" => "40",
						"helper_btn_width" => "260",
						"helper_btn_font" => "22",
						"helper_btn_font_weigt" => "700",
						"helper_svg_size" => "30",
						"modal_message_shadow_color" => "#eeeeee",
						"success_color" => "#1bb154",
						"StatusOfflinecolor" => "#eb2f1e",
						"StatusOnlinecolor" => "#0de761",
						"chat_status_checkbox" => "checked",
						"InterHelperInvitesOptions" => [
							"graphic_invite_status" => "unchecked",
							"audio_invite_status" => "unchecked"
						],
						"email_msgs_status" => "unchecked",
						"msgs_email" => $mail,
					]
				],
				"departaments" => [],
				"fastMessages" => [
					"main" => [],
				],
				"mailer" => [
					"deffault" => [
						"SMTPsecure" => "ssl",
						"SMTPserver" => "",
						"SMTPport" => "",
						"SMTPemail" => $mail,
						"SMTPpassword" => "",
						"sender_name" => "InterHelper",
						"mail_name" => "Рассылка, InterHelper",
					]
				],
			], JSON_UNESCAPED_UNICODE);
			$sql = "INSERT INTO users(id,name, password, email, settings, photo, domain, money, tariff, payday, time) VALUES (0,'$name','$password','$mail', '$options','user.png', '{\"domains\":{}}', $bonus, (SELECT value FROM variables WHERE name = 'starter_tariff'), '$today_date', '$today')";
			$connection->query($sql);
			$sql = "SELECT id FROM users WHERE email = '$mail'";
			$user_id = attach_sql($connection, $sql, 'row')[0];
			$_SESSION["boss"] = $user_id;
			$sql = "INSERT INTO crm(id, owner_id, columns) VALUES (0, '$user_id', '{\"Лиды\": {\"deffault_columns\": {\"helper_info\": {\"display\":\"false\", \"priority\": -1, \"deffault\": \"\"}, \"helper_name\":{\"display\":\"false\", \"priority\": -2, \"deffault\": \"новый\"}, \"helper_photo\": {\"display\":\"false\", \"priority\": -3, \"deffault\": \"\"}}, \"table_columns\": {}}, \"Клиенты\": {\"deffault_columns\":{\"helper_info\": {\"display\":\"false\", \"priority\": -1, \"deffault\": \"\"}, \"helper_name\":{\"display\":\"false\", \"priority\": -2, \"deffault\": \"новый\"}, \"helper_photo\": {\"display\":\"false\",\"priority\": -3, \"deffault\": \"\"}}, \"table_columns\":{}}}')";
			$connection->query($sql);		
			$sql = "INSERT INTO statistic(id, owner_id, info, utm) VALUES (0, '$user_id', '{\"statistic\": {}}', '{\"statistic\": {}}')";
			$connection->query($sql);	
			$json["type"] = "add_boss";
			$json["info"] = ["id" => $user_id, "hash"=> $hash, "email"=> $mail]; 
			send_curl($json, $host);	
			echo "
				<head>
					<script type='text/javascript' src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
				</head>
				<p>Вы <span style='color:#0ae;'>успешно подтвердили</span> вашу почту ! </br> Для входа в личный кабинет перейдите по <a href='/index'>ссылке</a></p>
				<style>
					body{
						background:#252525;
						color:#fff;
						display:flex;justify-content:center;align-items:center;
						font-size:20px;
					} 
					a{ color:#0ae; }
				</style>
			";
		} else {
			echo "
				<p>Ссылка <span style='color:#f90;'>не действительна</span> ! </br>
					Для возвращения переёдите по <a href='/index'>ссылке</a>
				</p>
				<style>
					body{
						background:#252525;
						color:#fff;
						display:flex;justify-content:center;align-items:center;
						font-size:20px;
					} 
					a{ 
						color:#0ae;
					}
				</style>
			";
		}
		mysqli_close($connection);
		exit;
	} elseif(isset($_GET['reset']) && trim($_GET['reset']) != ''){ // сброс пароля переход по ссылке
		$hash = $_GET['reset'];
		$password = uniqid('', true);
		$hashpassword = password_hash($password, PASSWORD_BCRYPT);
		$sql = "SELECT email FROM password_reset_keys WHERE hash = '$hash'";
		$mail = attach_sql($connection, $sql, 'row')[0];
		if(isset($mail)){
			$sql = "DELETE FROM password_reset_keys WHERE hash = '$hash' or email = '$mail'";
			$connection->query($sql);
			$sql = "UPDATE users SET password = '$hashpassword' WHERE email = '$mail'";
			$connection->query($sql);
			echo "<p>Ваш новый пароль - <span style='color:#0ae;'>$password</span> </br> Обязательно смените его в личном кабинете ! </br> Для возвращения перейдите по <a href='/index'>ссылке</a></p><style>body{background:#252525; color: #fff; display:flex; justify-content:center; align-items:center;} a{color:#0ae;} p{color:#f90p; font-size:20px;}</style> ";
		} else echo "<p>Ссылка <span style='color:#f90;'>не действительна</span> ! </br> Для возвращения перейдите по <a href='/index'>ссылке</a></p><style>body{background:#252525; color: #fff; display:flex; justify-content:center; align-items:center;} a{color:#0ae;} p{color:#f90p; font-size:20px;}</style> ";
	} elseif(isset($_POST['reset-password'])){ // сброс пароля
		$email = $_POST['reset-password'];
		$sql = "SELECT count(1) FROM users WHERE email = '$email'";
		$count = attach_sql($connection, $sql, 'row')[0];
		$sql = "SELECT time FROM password_reset_keys WHERE email = '$email' ORDER BY password_reset_keys.time DESC";
		$reg_time = attach_sql($connection, $sql, 'row');
		if(isset($reg_time)){ if (strtotime($reg_time[0].' +30 seconds') > strtotime($today)) array_push($response['errors'], 'Для повторной отправки подождите 30 секунд !'); } 
		if($count > 0 && count($response['errors']) == 0){
			$key = uniqid('', true);
			$key .= $email;
			$key .= date("H:i:s"); 
			$hash = password_hash($key, PASSWORD_BCRYPT);
			$sql = "INSERT INTO password_reset_keys (id, hash, email, time) VALUES (0, '$hash', '$email', '$today')";
			$connection->query($sql);
			$result = send_mails(array(["email" => $email]), 'Подтверждение почты', VARIABLES['InterHelper'], VARIABLES['smtp_from'], VARIABLES['smtp_password'], VARIABLES['smtp_secure'], VARIABLES["smtp_port"], VARIABLES['smtp_host'], '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
			<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>Сброс пароля</title>
				</head>
				<body style="width:450px;height:300px;">
					<div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
						<img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
						<h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Сброс пароля</h1>
						<p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Сбросьте пароль, перейдя по <a href="http://interhelper.ru/engine/login?reset='.$hash.'"> ссылке</a>.</p>
						<p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Если это были не вы, немедлено сообщите в службу поддержки !</p>
					</div>
				</body>
			</html>');
			if(count($result['errors']) > 0) array_push($response['errors'], 'Ошибка рассылки, проверьте вводимые данные. '. implode(',', $result['errors']));
			$response['success']['response'] = "Вам на почту было отправлено письмо. Следуйте инструкции письма для сброса пароля.";
		} else array_push($response['errors'], ERRORS['user_not_found']);
	} elseif (isset($_POST['login']) && isset($_POST['pass'])) { // вход
		$log = mb_strtolower($_POST['login']);
		$pass = $_POST['pass'];
		$sql = "SELECT count(1) FROM unconfimed_users WHERE email = '$log'";
		$count = attach_sql($connection, $sql, 'row')[0];	
		if($count == 0){
			$sql = "SELECT password, id FROM users WHERE email = '$log'";
			$row = attach_sql($connection, $sql, 'row');
			if(isset($row)){
				$hash = $row[0];
				$boss_id = $row[1];
				if(password_verify($pass, $hash)){
					session_start();
					$_SESSION["boss"] = $boss_id;
					$response["success"] = ["link" => "/engine/pages/profile"];
					$sql = "UPDATE users SET time = '$today' WHERE id = '$boss_id'";
					$connection->query($sql);
					if(isset($_SESSION['employee'])){
						$prevbossid = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
						if($boss_id != $prevbossid){
							unset($_SESSION["employee"]);
							$response['success']['response'] = "Выполнен вход в персональный аккаунт, однако вы ещё входили в аккаунт ассистента, который привязан к другому владельцу, поэтому, в целях безопасности, мы выполнили выход с вашего аккаунта ассистента.";
						}
					}
				} else array_push($response['errors'], ERRORS['wrong_pass']);
			} else array_push($response['errors'], ERRORS['account_not_exist']);
		} else array_push($response['errors'], ERRORS['account_not_exist']);
	} elseif(isset($_POST['exit'])) { // выход
		session_start();
		$boss_id = $_SESSION['boss'];
		$sql = "UPDATE users SET time = '$today' WHERE id = '$boss_id'";
		$connection->query($sql);
		unset($_SESSION['boss']);
		session_destroy();
		$response['success'] = ["reload" => true];
	} elseif(isset($_POST['phone']) && isset($_POST['name'])){ // обратная связь 
		$phone = $_POST['phone']; $name = $_POST['name'];
		$result = send_mails(array(["email" => 'info@interfire.ru', "name" => '123']), 'Обратный звонок', VARIABLES['InterHelper'], VARIABLES['smtp_from'], VARIABLES['smtp_password'], VARIABLES['smtp_secure'], VARIABLES["smtp_port"], VARIABLES['smtp_host'], 
		'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
	 	<html>
	 		<head>
	 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	 			<title>Обратный звонок</title>
	 		</head>
	 		<body style="width:450px;height:300px;">
	 			<div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
	 				<img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
	 				<h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Обратный звонок</h1>
	 				<p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">'.$name.' - '.$phone.'</p>
	 			</div>
	 		</body>
	 	</html>
		 ');
	} else array_push($response['errors'], ERRORS['empty_fields']);
	echo json_encode($response, JSON_UNESCAPED_UNICODE); 
    if(isset($connection)) mysqli_close($connection);
?>