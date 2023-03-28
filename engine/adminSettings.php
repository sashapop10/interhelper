<?php
header('Access-Control-Allow-Origin: *');
session_start();
$response = ["errors" => [], "success" => []];
include 'connection.php';
include 'func.php';
global $connection;
include 'config.php';
$host = SERVERPATH . '/admin';
$json = ["login" => VARIABLES["login"], "password"=>VARIABLES["password"], "type"=>"", "info" => [] ];
$lol = $_POST;
foreach ($lol as $index => $value){ $lol[$index] = htmlencrypt($lol[$index]); }
if(isset($_SESSION["admin"])){
	if(isset($_POST['getSettings'])){ // получить данные
		if($lol['getSettings'] == 'assistents'){
			$sql = "SELECT name, email, (SELECT users.domain FROM users WHERE users.id = assistents.domain) AS domain, photo, id, time, domain AS boss_id FROM assistents";
			$rows = attach_sql($connection, $sql, 'query');
			$users = [];
			foreach($rows as $row){
				$users[$row["id"]] = [
					"name" => $row['name'],
					"email" => $row['email'],
					"domain" => json_decode($row['domain'], JSON_UNESCAPED_UNICODE),
					"photo" => $row['photo'],
					"time" => $row['time'],
					"id" => $row['id'],
					"boss_id" => $row['boss_id'],
				];
			}
			$response['success'] = ['users' => json_encode($users, JSON_UNESCAPED_UNICODE)];
		} elseif($lol['getSettings'] == 'faq') $response['success']['faq'] = PROBLEMS;
		elseif($lol['getSettings'] == 'users') {
			$sql = "SELECT name, email, domain, photo, money, tariff, payday, id, time, ban FROM users";
			$rows = attach_sql($connection, $sql, 'query');
			$users = [];
			foreach($rows as $row){
				$users[$row["id"]] = [
					"name" => $row['name'],
					"email" => $row['email'],
					"domain" => json_decode($row['domain'], JSON_UNESCAPED_UNICODE),
					"photo" => $row['photo'],
					"money" => $row["money"],
					"payday" => $row['payday'],
					"tariff" => $row['tariff'],
					"time" => $row['time'],
					"ban" => $row['ban'],
					"id" => $row['id'],
				];
			}
			$response['success']['users'] = json_encode($users, JSON_UNESCAPED_UNICODE);
		} elseif($lol['getSettings'] == 'reviews')  $response['success']['reviews'] = REVIEWS;
		elseif($lol['getSettings'] == 'news') $response['success']['news'] = NEWS;
		elseif($lol['getSettings'] == 'tariff') $response['success']['editions'] = EDITIONS;
		elseif($lol['getSettings'] == 'tools') $response['success']['tools'] = TOOLS;
		elseif($lol['getSettings'] == 'variables') $response['success']['variables'] = VARIABLES;
	} else if( // создание тарифа
		isset($lol['name']) && isset($lol['price']) && isset($lol['visits']) && isset($lol['leeds']) && trim($lol['leeds']) && isset($lol['clients']) && isset($lol['assistents'])
		&& isset($lol['departaments']) && isset($lol['domains']) && isset($lol['tasks']) && isset($lol['variants']) && isset($lol['leed_columns']) && isset($lol['limit']) && 
		isset($lol['about'])
		){
		$name = $lol['name']; 
		$price = $lol['price']; 
		$leeds = $lol['leeds'];
		$clients = $lol['clients']; 
		$assistents = $lol['assistents'];
		$departaments = $lol['departaments'];
		$domains = $lol['domains'];
		$tasks = $lol['tasks']; 
		$variants = $lol['variants'];
		$leed_columns = $lol['leed_columns']; 
		$client_columns = $lol['leed_columns'];
		$limit = $lol['limit']; 
		$visits = $lol['visits'];
		$about = $lol['about'];
		$tariff = [
			"img" => "new_mark.png",
			"name"=> $name,
			"cost" => [
				"value" => $price,
				"text" => "₽/мес"
			],
			"include" => [
				"unique_visits" => [
					"text_before" => "*",
					"value" => $visits,
					"text" => "посещений \/ месяц"
				],
				"crm_leeds" => [
					"text_before" => "",
					"value" => $leeds,
					"text" => "Лидов в CRM"
				],
				"crm_clients" => [
					"text_before" => "",
					"value" => $clients,
					"text" => "Клиентов в CRM"
				],
				"assistents" => [
					"text_before" => "",
					"value" => 5,
					"text" => "Ассистентов"
				],
				"departaments"=>[
					"text_before" => "",
					"value" => $departaments,
					"text" => "Отделов"
				],
				"domains" => [
					"text_before" => "",
					"value" => $domains,
					"text"=>"Домена"
				],
				"leed_columns"=>[
					"text_before" => "",
					"value" => $leed_columns,
					"text" => "Столбцов в CRM для лидов"
				],
				"client_columns" => [
					"text_before" => "",
					"value" => $client_columns,
					"text" => "Столбцов в CRM для клиентов"
				],
				"tasks" => [
					"text_before" => "",
					"value" => $tasks,
					"text" => "Задач в CRM"
				],
				"variants" => [
					"text_before" => "",
					"value" => $variants,
					"text" => "Вариантов в списке в CRM"
				],
				"unique_visits_limit" => [
					"text_before" => "* Посетитель сверх лимита=>",
					"value" => $limit,
					"text" => "₽"
				]
			],
			"personal_page_info" => [
				"tarif_text" => $about
			],
			"type" => "hidden",
		];
		$json["type"] = "new_tariff";
		$json["info"] = ["name" => $name, "tariff" => $tariff];
		$decode_tariff = json_encode($tariff, JSON_UNESCAPED_UNICODE);
		$sql = "INSERT INTO tariffs (id, tariff, type, name) VALUES (0, '$decode_tariff', 'visible', '$name')";
		if ($connection->query($sql) === TRUE) { 
			$response['success'] = ["new_tariff"=> $tariff, "new_tariff_index"=> $name];
			send_curl($json, $host);
		} else array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($_POST['user_profile'])){ // вход за клиента
		$boss_id = $_POST['user_profile'];
		$_SESSION['boss'] = $boss_id;
		if(isset($_SESSION['employee'])){
			$employee_boss = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
			if($employee_boss !=  $boss_id) unset($_SESSION['employee']);
		}
		$response['success'] = ["new_window" => "/engine/pages/profile"];
	} elseif(isset($_POST['assistent_profile'])){ // вход за ассистента
		$info = json_decode($_POST['assistent_profile'], JSON_UNESCAPED_UNICODE);
		$_SESSION['employee'] = json_encode(['personal_id' => $info['personal_id'], 'boss_id' => $info['boss_id'], "admin" => true], JSON_UNESCAPED_UNICODE);
		if(isset($_SESSION['boss'])){
			if($_SESSION['boss'] != $info['boss_id']) unset($_SESSION['boss']);
		}
		$response['success'] = ["new_window" => "/engine/consultant/assistent"];
	} elseif(isset($_POST['remove_user'])){ // удалить клиента
		$id = $_POST['remove_user'];
		$json["type"] = "delete_user";
		$json["info"] = ["id" => $id];
		send_curl($json, $host);
	} elseif(isset($_POST['user_money']) && isset($_POST['user_id'])){ // деньги пользователя
		$money = $_POST['user_money'];
		$user_id = $_POST['user_id'];
		$json["type"] = "set_money";
		$json["info"] = ["boss" => $user_id, "money" => $money];
		send_curl($json, $host);
	} elseif(isset($lol['remove_tariff'])){ // удалить тариф
	    $name = $lol['remove_tariff'];
        $sql = "DELETE FROM tariffs WHERE name = '$name'";
        if ($connection->query($sql) === TRUE) { 
			$json["type"] = "remove_tariff";
			$json["info"] = ["name" => $name];
			send_curl($json, $host);
		} else array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol['type1']) && isset($lol['index'])){ // изменить тариф (текст)
	    $type1 = $lol['type1'];
		if($type1 != 'type'){
			if(isset($lol['type2']) && trim($lol['type2']) != '') $type2 = $lol['type2'];
			else $type2 = false;
			$index = $lol['index'];
			if(isset($lol['fitchaindex']) && trim($lol['fitchaindex']) != '') $fitchaindex = $lol['fitchaindex'];
			else $fitchaindex = false;
			if(isset($lol['value'])) $value = $lol['value'];
			else $value = '';
			$info = EDITIONS[$index];
			if(!$type2) $info[$type1] = $value;
			elseif(!$fitchaindex) $info[$type1][$type2] = $value;
			else $info[$type1][$fitchaindex][$type2] = $value;
			$info = json_encode($info, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE tariffs SET tariff = '$info', name = '$index' WHERE name = '$index'";
		} else {
			$value = $lol['value'];
			$index = $lol['index'];
			$sql = "UPDATE tariffs SET type = '$value' WHERE name = '$index'";
			$info = EDITIONS[$index];
			$info[$type1] = $value;
		}
        if($connection->query($sql) === TRUE) { 
			$json["type"] = "change_tariff";
			$json["info"] = ["name" => $index, "tariff" => $info];
			send_curl($json, $host);
		} else array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($_POST['ban_type']) && isset($_POST['user_id'])){ // заблокировать / разблокировать 
		$user_id = $_POST['user_id'];
		$ban_type = $_POST['ban_type'];
		if($ban_type == 'ban') $sql = "UPDATE users SET ban = 'banned' WHERE id = '$user_id'";
		else  $sql = "UPDATE users SET ban = NULL WHERE id = '$user_id'";
		$connection->query($sql);
	} elseif(isset($_FILES["tariff_photo"]) && isset($lol["index"])){ // изменить тариф (фото)
		$index = $lol["index"];
		$photo = $_FILES["tariff_photo"];
		$data = save_file($photo, "tariff_photo", $index, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else $response['success'] = ["tariff_photo" => $data["text"], "tariff_index" => $index];
	} elseif(isset($lol['oldpass']) && isset($lol['newpass']) && isset($lol['repeatnewpass'])){ // пароль
		$oldpass = $lol['oldpass'];
		$newpass = $lol['newpass'];
		$repeatnewpass = $lol['repeatnewpass'];
		if(password_verify($oldpass, VARIABLES['password'])){
			if($newpass == $repeatnewpass){
				$hash = password_hash($newpass, PASSWORD_BCRYPT);
				$sql = "UPDATE variables SET value = '$hash' WHERE name = 'password'";
				if ($connection->query($sql) === TRUE) { 
					$json["type"] = "new_password";
					$json["info"] = ["value" => $hash];
					send_curl($json, $host);
				} else array_push($response['errors'], ERRORS['sql_error']);
			} else array_push($response['errors'], 'Пароли не сопадают!');
		} else array_push($response['errors'], 'Не верный пароль!');
	} elseif(isset($lol['name']) && isset($lol['value'])){ // переменные
		$name = $lol['name'];
		$value = $lol['value'];
		$sql = "UPDATE variables SET value = '$value' WHERE name = '$name'";
		if ($connection->query($sql) === TRUE) { 
			if($name == 'login') {
				$json["type"] = "new_login";
				$json["info"] = ["value" => $value];
				send_curl($json, $host);
			} elseif($name == 'starter_tariff'){
				$json["type"] = "default_tariff";
				$json["info"] = ["value" => $value];
				send_curl($json, $host);
			}
		} else array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($_FILES['photo']) && isset($lol['name']) && isset($lol['link']) && isset($lol['review']) && isset($lol['rating'])){ // создать отзыв
		$link = $lol['link'];
		$rating = $lol['rating'];
		$name = $lol['name'];
		$photo = $_FILES['photo'];
		$review = $lol['review'];
		$data = save_file($photo, "reviews_photos", null, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else {
			$photo_name = $data["text"];
			$review_id = uniqid();
			$today = date("Y-m-d");
			$sql = "INSERT INTO reviews (id, name, photo, link, review, review_id, time, rating) VALUES (0, '$name', '$photo_name', '$link', '$review', '$review_id', '$today', '$rating')";
			if ($connection->query($sql) === TRUE) {
				$review_info = [
					"name" => $name,
					"link" => $link,
					"text" => $review,
					"img" => $photo_name,
					"time" => $today,
					"rating" => $rating,
				];
				$response['success'] = ["new_review_index" => $review_id, "new_review" => $review_info];
			} else array_push($response['errors'], ERRORS['sql_error']);
		}
	} elseif(isset($lol['remove_review'])){ // удалить отзыв
	    $name = $lol['remove_review'];
        $sql = "DELETE FROM reviews WHERE review_id = '$name'";
        if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol['review_index']) && isset($lol['type']) && isset($lol['value'])){// изменить отзыв 
		$index = $lol['review_index'];
		$type = $lol['type'];
		$value = $lol['value'];
		$sql = "UPDATE reviews SET $type = '$value' WHERE review_id = '$index'";
		if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($_FILES['review_photo'])){// изменить фото на отзыве
		$index = $lol["index"];
		$photo = $_FILES["review_photo"];
		$data = save_file($photo, "reviews_photos", $index, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else $response['success'] = ["review_photo" => $data["text"], "review_index" => $index];
	} elseif(isset($_FILES['photo']) && isset($lol['name']) && isset($lol['short_info']) && isset($lol['info'])){ // создать новость
		$short_info = $lol['short_info'];
		$name = $lol['name'];
		$photo = $_FILES['photo'];
		$info = $lol['info'];
		$data = save_file($photo, "news_photos", null, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else{
			$today = date("Y-m-d");
			$photo_name = $data["text"];
			$news_id = uniqid();
			$sql = "INSERT INTO news (id, name, photo, info, short_info, time, news_id) VALUES (0, '$name', '$photo_name', '$info', '$short_info', '$today', '$news_id')";
			if ($connection->query($sql) === TRUE) {
				$review_info = [
					"name" => $name,
					"short_info" => $short_info,
					"info" => $info,
					"photo" => $photo_name,
					"time" => $today,
				];
				$response['success'] = ["new_news_index" => $news_id, "new_news" => $review_info];
			} else array_push($response['errors'], ERRORS['sql_error']);
		}
	} elseif(isset($lol['remove_news'])){ // удалить новость
	    $name = $lol['remove_news'];
        $sql = "DELETE FROM news WHERE news_id = '$name'";
        if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol['news_index']) && isset($lol['type']) && isset($_POST['value']) && trim($_POST['value'])){ // изменить новость
		$index = $lol['news_index'];
		$type = $lol['type'];
		$value = $lol['value'];
		$sql = "UPDATE news SET $type = '$value' WHERE news_id = '$index'";
		if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($_FILES['news_photo'])){ // изменить фото (новость)
		$index = $lol["index"];
		$photo = $_FILES["news_photo"];
		$data = save_file($photo, "news_photos", $index, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else $response['success'] = ["news_photo" => $data["text"], "news_index" => $index]; 
	} elseif(isset($_FILES['tool_photo'])){ // изменить фото (инструмент)
		$index = $lol["index"];
		$photo = $_FILES["tool_photo"];
		$data = save_file($photo, "tools_photos", $index, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else $response['success'] = ["tool_photo" => $data["text"], "tool_index" => $index];
	} elseif(isset($lol['remove_tool'])){ // удалить инструмент
	    $name = $lol['remove_tool'];
        $sql = "DELETE FROM tools WHERE tool_id = '$name'";
        if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol['tool_index']) && isset($lol['type']) && isset($lol['value'])){// изменить инструмент 
		$index = $lol['tool_index'];
		$value = $lol['value'];
		$type = $lol['type'];
		$sql = "UPDATE tools SET $type = '$value' WHERE tool_id = '$index'";
		if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($_FILES['photo']) && isset($lol['name']) && isset($lol['color']) && isset($lol['group']) && isset($lol['tool'])){ // создать инструмент
		$tool = $lol['tool'];
		$name = $lol['name'];
		$photo = $_FILES['photo'];
		$color = $lol['color'];
		$group = $lol['group'];
		$data = save_file($photo, "tools_photos", null, $connection, null);
		if($data["access"] == false) array_push($response['errors'], $data["text"]);
		else {
			$photo_name = $data["text"];
			$tool_id = uniqid();
			$sql = "INSERT INTO tools (id, name, photo, info, row, color, tool_id) VALUES (0, '$name', '$photo_name', '$tool', '$group', '$color', '$tool_id')";
			if ($connection->query($sql) === TRUE) {
				$tool_info = [
					"name" => $name,
					"info" => $tool,
					"photo" => $photo_name,
					"color" => $color,
				];
				$response['success'] = ["new_tool_index" => $tool_id, "new_tool" => $tool_info, "new_tool_row" => $group];
			} else array_push($response['errors'], ERRORS['sql_error']);
		}
	} elseif(isset($lol["header"]) && isset($lol["answer"]) && isset($lol["column"]) && isset($lol["item"])){ // вставить подвопрос в faq
		$column = $lol["column"];
		$item = $lol["item"];
		$answer = $lol["answer"];
		$header = $lol["header"];
		$item_array = PROBLEMS[$column][$item]["info"]["list"];
		$item_id = PROBLEMS[$column][$item]["id"];
		$new_array = ["list" => $item_array];
		$new_array["list"][$header]["answer"] = $answer;
		if(isset($lol['video'])) $new_array["list"][$header]["video"] = $lol['video'];
		else $new_array["list"][$header]["video"] = '';
		$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
		$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol["header"]) && isset($lol["column"]) && isset($lol["type"])){ // изменить faq
		$column = $lol["column"];
		$type = $lol["type"];
		if(isset($lol["innerheader"])) $innerheader = $lol["innerheader"];
		$item = $lol["header"];
		$value = $lol["value"];
		$item_array = PROBLEMS[$column][$item];
		$item_id = PROBLEMS[$column][$item]["id"];
		$sql = '';
		if($type == 'header') $sql = "UPDATE faq SET name = '$value' WHERE id = '$item_id'";
		elseif($type == 'answer'){
			$new_array = $item_array["info"];
			$new_array["answer"] = $value;
			$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		} elseif($type == 'video'){
			$new_array = $item_array["info"];
			$new_array["video"] = $value;
			$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		} elseif($type == 'inneranswer'){
			$new_array = ["list" => $item_array["info"]["list"]];
			$new_array["list"][$innerheader]["answer"] = $value;
			$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		} elseif($type == 'innervideo'){
			$new_array = ["list" => $item_array["info"]["list"]];
			$new_array["list"][$innerheader]["video"] = $value;
			$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		} elseif($type == "innerheader"){
			$new_array = ["list" => $item_array["info"]["list"]];
			unset($new_array["list"][$innerheader]);
			$new_array["list"][$value] = $item_array["info"]["list"][$innerheader];
			$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
			$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		}
		if($sql != ''){
			if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
		} else array_push($response['errors'], 'Не существующий тип!');
	} elseif(isset($lol["remove_inneritem"]) && isset($lol["remove_column"]) && isset($lol["remove_item"])){ // удалить подвопрос в faq
		$column = $lol["remove_column"];
		$item = $lol["remove_item"];
		$header = $lol["remove_inneritem"];
		$item_array = PROBLEMS[$column][$item]["info"]["list"];
		$item_id = PROBLEMS[$column][$item]["id"];
		$new_array = ["list" => $item_array];
		unset($new_array["list"][$header]);
		$new_array = json_encode($new_array, JSON_UNESCAPED_UNICODE);
		$sql = "UPDATE faq SET info = '$new_array' WHERE id = '$item_id'";
		if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol["change_column"]) && isset($lol["change_item"])){ // переместить вопрос в faq
		$column = $lol["change_column"];
		if($column == 0) $column2 = 1;
		else $column2 = 0;
		$item = $lol["change_item"];
		$item_id = PROBLEMS[$column][$item]["id"];
		$sql = "UPDATE faq SET faq_group = '$column2' WHERE id = '$item_id'";
		if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol["add_header"]) && (isset($lol["add_column"]) || $lol["add_column"] == 0) && (isset($lol["add_type"]) || $lol["add_type"] == 0)){ // создать вопрос в faq
		$column = $lol["add_column"];
		$header = $lol["add_header"];
		$type = $lol["add_type"];
		if(isset($lol["add_answer"])) $answer = $lol["add_answer"];
		if($type == 0){ 
			$info = ["answer" => $answer];
			if(isset($lol['video'])) $info["video"] = $lol['video'];
			else $info["video"] = '';
		} else $info = ["list" => []];
		$info = json_encode($info, JSON_UNESCAPED_UNICODE);
		$sql = "INSERT INTO faq (id, name, type, info, faq_group) VALUES (0, '$header', '$type', '$info', '$column')";
		if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
	} elseif(isset($lol["remove_faq_header"]) && isset($lol["remove_faq_column"])){ // удалить воарос в faq
		$header = $lol["remove_faq_header"];
		$column = $lol["remove_faq_column"];
		$sql = "DELETE FROM faq WHERE faq_group = '$column' and name = '$header'";
		if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
	} else array_push($response['errors'], ERRORS['empty_fields']);
} elseif(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['type']) && isset($_POST['info'])){
	$login = $_POST['login'];
	$password = $_POST['password'];
	if($login == VARIABLES['login'] && $password == VARIABLES['password']){
		$type = $_POST['type'];
		$info = json_decode($_POST['info'], JSON_UNESCAPED_UNICODE);
		if($type == 'pay_notification'){
			$sql = 'SELECT id, email, name FROM users WHERE pay_notification IS NULL AND ( ';
			foreach($info as $key => $user_id){
				$sql .= 'id = "'.$user_id.'"';
				if(count($info) - 1 != $key) $sql .= ' OR ';
				else $sql .= ')';
			}
			$rows = attach_sql($connection, $sql, 'query');
			$send_to = array();
			foreach($rows as $key => $row){
				$row_id = $row['id'];
				$sql = "UPDATE users SET pay_notification = 'true' WHERE id = '$row_id'";
				if($connection->query($sql) === true) array_push($send_to, ["email" => $row['email'], "name" => $row['name']]);
			}
			$sended_count = 0;
			while($sended_count < count($send_to)){
				$slice_send_to = array_slice($send_to, $sended_count, 35, true); 
				$sended_count += 35;
				send_mails(
					$slice_send_to,
					"Ваш тариф закончился", 
					"InterHelper", 
					"info@interhelper.ru", 
					"Fadkj123ADSFJ!",
					'tls', 
					587, 
					'tls://smtp.yandex.ru',
					'
						<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
						<html>
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
								<title>Ваш тариф закончился</title>
							</head>
							<body style="width:450px;height:300px;">
								<div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
									<img src="https://interhelper.ru/scss/imgs/interhelper_logo.png" alt="InterFire" style="background-color:#0ae;padding:10px;border-radius:10px;position: absolute; width: 115px; left: 14px; top: 12px;">
									<h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Ваш тариф закончился</h1>
									<p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Пополните ваш счёт, чтобы возобновить ваш тариф в Вашем <a href="https://interhelper.ru/engine/pages/tariff">личном кабинете</a> или перейдите на другой.</p>
								</div>
							</body>
						</html>
					'
				);
			}
			$response['success']['send_to'] = count($send_to);
		}
	} else array_push($response['errors'], 'Не верный логин или пароль.');
}
echo json_encode($response, JSON_UNESCAPED_UNICODE); 
if(isset($connection)) mysqli_close($connection);
?>