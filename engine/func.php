<?php
error_reporting( E_ALL ^ ( E_NOTICE | E_WARNING | E_DEPRECATED ) );
function htmlencrypt($str){ // шифр html
	return trim(
		str_replace("\\", "&bsol;", 
			str_replace(array("\r\n", "\r", "\n"), " ", 
				preg_replace('/\s+/', ' ', 
					htmlspecialchars(str_replace("`", "'", $str), ENT_QUOTES)
				)
			)
		)
	);
}
function account_check($info, $connection){ // проверка ДЕНЕГ и ТАРИФА БОССА
	$res = ["status" => true, "info" => null];
	$tariff_name = $info['tariff'];
	$money = $info['money'];
	$boss_id = $info['boss_id'];
	if($money < 0 && $info['payday'] == 0){
		$res['status'] = false;
		if($money < 0){ 
			if(isset($_SESSION['boss'])) $res['info'] = ERRORS["not_enouth_money_boss"]; 
			else $res['info'] = ERRORS["not_enouth_money_emlpoyee"]; 
		} else {
			if(isset($_SESSION['boss'])) $res['info'] = ERRORS["closed_tariff_boss"]; 
			else $res['info'] = ERRORS["closed_tariff_emlpoyee"]; 
		}
	} elseif($money >= 0 && $info['payday'] == 0){
		$sql = "SELECT tariff FROM tariffs WHERE name = '$tariff_name'";
		$tariff_info = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
		$tariff_cost = intval($tariff_info['cost']['value']);
		if($money - $tariff_cost >= 0){
			$curl_json = ["login" => VARIABLES["login"], "password"=>VARIABLES["password"], "type"=>"personal_pay", "info" =>  ["id" => $boss_id, "money" => $money, "tariff" => $tariff_name]];
			$host = SERVERPATH . '/admin';
			$data = send_curl($curl_json, $host);
			$data = json_decode($data, JSON_UNESCAPED_UNICODE);
			$text = $data['error'];
			if($data["success"] == true) $res['info'] = LOG['new_month'];
			else {
				$res['status'] = false;
				$res['info'] = $data['text'];
			}
		} else{
			$res['status'] = false;
			if($money < 0){ 
				if(isset($_SESSION['boss'])) $res['info'] = ERRORS["not_enouth_money_boss"]; 
				else $res['info'] = ERRORS["not_enouth_money_emlpoyee"]; 
			} else {
				if(isset($_SESSION['boss'])) $res['info'] = ERRORS["closed_tariff_boss"]; 
				else $res['info'] = ERRORS["closed_tariff_emlpoyee"]; 
			}
		}
	}
	return $res;
}
function check_user($connection){ // проверка СУЩЕСТВОВАНИЯ АККАУНТОВ и БАНА / ВОЗВРАЩАЕТ ДАННЫЕ О ПОЛЬЗОВАТЕЛЕ
	$result = ["status" => true, "info" => []];
	// url
	$url = explode('/', $_SESSION['url']);
    $url_page = $url[count($url) - 1];
    if(strrpos($url_page, '?')) $url_page = explode('?', $url_page)[0];
    $url_dir = $url[count($url) - 2];
	// определяем пользователя
    if(isset($_SESSION['employee'])){ 
        $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
        $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
        if(check_db_info($connection, 'assistents', 'id', $personal_id) == 0 || check_db_info($connection, 'users', 'id', $boss_id) == 0) {
			unset($_SESSION['employee_login_time']); 
            unset($_SESSION['employee']); 
			$result["status"] = false;
			$result["info"]["error"] = ERRORS['assistent_account_access']; 
			$result["info"]["new_url"] = '/index'; 
            return $result;
        }
		$sql = "SELECT count(1) FROM assistents WHERE id = '$personal_id' and ban IS NOT NULL";
		if(attach_sql($connection, $sql, 'row')[0] > 0)  {
			unset($_SESSION['employee_login_time']); 
			unset($_SESSION['employee']); 
			$result["status"] = false;
			$result["info"]["error"] = ERRORS['banned_assistent']; 
			$result["info"]["new_url"] = '/index'; 
			return $result;
		} 
		$sql = "SELECT login_time FROM assistents WHERE id = '$personal_id'";
		$last_login_time = attach_sql($connection, $sql, 'row')[0];
		if(date($_SESSION["employee_login_time"]) < date($last_login_time) && !array_key_exists('admin', json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)))  {
			unset($_SESSION['employee_login_time']); 
			unset($_SESSION['employee']); 
			$result["status"] = false;
			$result["info"]["error"] = 'В Ваш аккаунт авторизировались с другого браузера.'; 
			$result["info"]["new_url"] = '/index'; 
			return $result;
		} 
		$result["info"]['database'] = 'assistents';
		$sql = "SELECT departament FROM assistents WHERE id = '$personal_id'";
		$result["info"]['departament'] = attach_sql($connection, $sql, 'row')[0];
    } 
    if(isset($_SESSION['boss']) && $url_dir == 'pages'){
		$personal_id = $_SESSION['boss'];
        $boss_id = $_SESSION['boss']; 
        if(check_db_info($connection, 'users', 'id', $boss_id) == 0) {
            unset($_SESSION['boss']); 
            $result["status"] = false;
			$result["info"]["error"] = ERRORS['boss_account_access']; 
			$result["info"]["new_url"] = '/index'; 
            return $result;
        }
		$result["info"]['database'] = 'users';
    } 
	$sql = "SELECT ban FROM users WHERE id = '$boss_id'";
	if(attach_sql($connection, $sql, 'row')[0] == 'ban')  {
		$result["status"] = false;
		$result["info"]['error'] = ERRORS['banned_user']; 
		$result["info"]["new_url"] = '/index'; 
		return $result;
	} 
	if(count($result["info"]) > 0){
		$today = date('Y-m-d H:i:s');
		$database = $result["info"]['database'];
		$sql = "SELECT settings, tariff, domain, money, payday, email FROM users WHERE id = '$boss_id'";
		$boss_info = attach_sql($connection, $sql, 'row');
		$boss_departaments =  json_decode($boss_info[0], JSON_UNESCAPED_UNICODE)['departaments'];
		$boss_settings = $boss_info[0];
		$boss_tariff = $boss_info[1];
		$boss_money = intval($boss_info[2]);
		$boss_money = $boss_info[3];
		$boss_payday = $boss_info[4];
		$boss_email = $boss_info[5];
		$boss_domains = json_decode($boss_info[2], JSON_UNESCAPED_UNICODE); 
		if(isset($result["info"]['departament'])){
			$departament = $result["info"]['departament'];
			if(!isset($boss_departaments[$departament])){
				$result["status"] = false;
				$result["info"]['error'] = ERRORS['not_exist_departament'];
				$result["info"]["new_url"] = '/index'; 
				unset($_SESSION['employee']);
				return $result;
			} elseif($url_page != 'assistent' && $url_page != 'profile' && !isset($_SESSION['boss'])) {
				if($url_page == 'command_chat') $url_page = 'command';
				elseif($url_page == 'banned_chat') $url_page = 'banned';
				elseif($url_page == 'tasks') $url_page = 'crm';
				elseif($url_page == 'crm_settings') $url_page = 'crm';
				elseif($url_page == 'chat') $url_page = 'hub';
				elseif($url_page == 'payment') $url_page = 'tariff';
				elseif($url_page == 'dialog') $url_page = 'dialogs';
				if(!array_search($url_page, $boss_departaments[$departament]) && array_search($url_page, $boss_departaments[$departament]) !== 0){
					$result["status"] = false;
					$result["info"]['error'] = ERRORS['not_rules'];
					$result["info"]["new_url"] = '/engine/consultant/assistent'; 
					unset($_SESSION['employee']);
					return $result;
				}
			}
		}
		$sql = "UPDATE $database SET time = '$today' WHERE id = '$boss_id'";
	    $connection->query($sql);
		$result['info']['boss_id'] = $boss_id;
		$result['info']['domains'] = $boss_domains;
		$result['info']['departaments'] = $boss_departaments;
		$result['info']['settings'] = $boss_settings;
		$result['info']['tariff'] = $boss_tariff;
		$result['info']['today'] = $today;
		$result['info']['email'] = $boss_email;
		$result['info']['money'] = $boss_money;
		$result['info']['payday'] = $boss_payday;
		$account_check_info = account_check($result['info'], $connection);
		if(!$account_check_info['status']){
			$result["status"] = false;
			$result["info"]['error'] = $account_check_info['info'];
			$result["info"]["new_url"] = '/engine/pages/tariff'; 
			return $result;
		} elseif($account_check_info['info'] != null) $result["info"]['log'] = $account_check_info['info'];
		return $result;
	}
	$result["info"]['error'] = ERRORS['user_not_found'];
	$result["info"]['new_url'] = '/index';
	return $result;
}
function attach_sql($connection, $sql, $type){ // запрос к базе данных type = row / query
	$query = mysqli_query($connection, $sql);
	if($type == 'row') return mysqli_fetch_row($query);
	if($type == 'query') return mysqli_fetch_all($query, MYSQLI_ASSOC);
	return null;
}
function check_db_info($connection, $database, $column, $value){ // поиск строки в базе данных 
	$sql = "SELECT count(1) FROM $database WHERE $column = '$value'";
	$count = attach_sql($connection, $sql, 'row')[0];
	return $count;
} 
function navigation($active, $info){ // навигация личного кабинета
	$links = ["pages" => [], "consultant" => []];
	$url = explode('/', $_SESSION['url']);
    $url_page = $url[count($url) - 1];
    if(strrpos($url_page, '?')) $url_page = explode('?', $url_page)[0];
    $url_dir = $url[count($url) - 2];
	$links = ["consultant" => [], "pages" => []];
	if(isset($_SESSION['employee'])){
		array_push($links['consultant'], 'assistent');
		$departament = $info['info']['departament'];
		$boss_departaments = $info['info']['departaments'];
		foreach($boss_departaments[$departament] as $inner){
			$boss_pages = array("profile", "domains", "departaments", "tariff", "statistic", "assistents", "design", "dialogs", "offline", "options", "anticlicker", "swaper", "autosender", "mailer");
			if($inner == 'crm'){
				array_push($links["consultant"], 'tasks', 'crm');
			} elseif(array_search($inner, $boss_pages)) array_push($links['pages'], $inner);
			else array_push($links['consultant'], $inner);
		}
	} 
	if(isset($_SESSION['boss'])){
		if(!isset($_SESSION['employee'])) array_push($links['consultant'], 'login');
		$boss_links = ["pages" => ["profile", "domains", "departaments", "tariff", "statistic", "assistents", "design", "dialogs", "offline", "options", "anticlicker", "swaper", "autosender", "mailer"]];
		foreach($boss_links as $link_dir_key => $link_dir){
			foreach($link_dir as $link){
				if(!in_array($link, $links[$link_dir_key])) array_push($links[$link_dir_key], $link); 
			}
		}
	} 
	$result = "<header>";
	foreach($links as $dir_name => $pages){
		foreach($pages as $page_name){ $result .= "<div ".($active != $page_name ? 'onclick="location.href=`/engine/'.$dir_name.'/'.$page_name.'`;"' : '')." class='".($page_name == 'profile' && isset($_SESSION['employee']) ? 'new_dir' : '').' '.($active == $page_name ? 'page_active' : '').' '.($dir_name == 'consultant' ? 'assistent_pages' : '')." page_$page_name'><span style='background-image: url(\"".PAGES[$page_name]['photo']."\");'></span>".PAGES[$page_name]['name']."</div>"; }
	}
	$result .= "</header>";
	$result .= "<div class='header_control active_header_control' onclick='control(\"header_control\", \"header\", {\"left\": \"0\"}, {\"left\": \"-90px\"})'><span></span></div>";
	echo $result;
}

function nav($active_page, $user_info){ // навигация личного кабинета
	$url = explode('/', $_SESSION['url']);
    $url_page = $url[count($url) - 1];
    if(strrpos($url_page, '?')) $url_page = explode('?', $url_page)[0];
    $url_dir = $url[count($url) - 2];
	$links = [
		"consultant" => [], 
		"pages" => []
	];
	if(isset($_SESSION['employee'])){ // сотрудник 
		array_push($links['consultant'], 'assistent');
		$departament = $user_info['info']['departament'];
		$boss_departaments = $user_info['info']['departaments'];
		foreach($boss_departaments[$departament] as $inner){
			$boss_pages = array("domains", "departaments", "tariff", "statistic", "assistents", "design", "dialogs", "offline", "options", "anticlicker", "swaper", "autosender", "mailer", "profile");
			if($inner == 'crm') array_push($links["consultant"], 'tasks', 'crm');
			elseif(array_search($inner, $boss_pages)) array_push($links['pages'], $inner);
			else array_push($links['consultant'], $inner);
		}
	} 
	if(isset($_SESSION['boss'])){ // клиент
		if(!isset($_SESSION['employee'])) array_push($links['consultant'], 'login');
		$boss_links = [
			"pages" => [
				"profile", 
				"domains", 
				"departaments", 
				"tariff", 
				"statistic", 
				"assistents", 
				"design", 
				"dialogs", 
				"offline", 
				"options", 
				"anticlicker", 
				"swaper", 
				"autosender", 
				"mailer"
			]
		];
		foreach($boss_links as $link_dir_key => $link_dir){
			foreach($link_dir as $link){
				if(!in_array($link, $links[$link_dir_key])) array_push($links[$link_dir_key], $link); 
			}
		}
	} 
	$result = "
		<div id='navigation'>
			<ul>
				<li>
					<a href='/' class='brand-name'>
						<svg version='1.1' class='small-logo' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 546.76 380.96' style='enable-background:new 0 0 546.76 380.96;' xml:space='preserve'><g><path style='transform:rotateY(180deg); transform-origin:center center;' class='st0' d='M0,203.57V23.48C0,10.51,10.51,0,23.48,0h499.81c12.97,0,23.48,10.51,23.48,23.48l0,334.28c0,21.26-25.62,31.02-40.51,15.85c-4.28-4.36-8.86-8.49-13.73-12.38c-36.77-29.4-89.86-44.31-157.68-44.31H23.48C10.51,316.91,0,306.4,0,293.44V203.57z'/><circle class='st1' cx='119.05' cy='157.87' r='35.08'/><circle class='st1' cx='244.91' cy='157.87' r='35.08'/><circle class='st1' cx='370.77' cy='157.87' r='35.08'/></g></svg>
						<p>InterHelper</p>
					</a>
				</li>
		";
	$index = 0;
	foreach($links as $dir_name => $pages){
		foreach($pages as $page_name){
			$index++; 
			$result .= "
				<li 
				data-index='".$index."' class='".($page_name == 'profile' && isset($_SESSION['employee']) ? 'new_dir' : '').' '.($active_page == $page_name ? 'active-nav' : '').' '.($dir_name == 'consultant' ? 'assistent_pages' : '')." nav-link'>
				<a ".($active_page != $page_name ? 'href = /engine/'.$dir_name.'/'.$page_name.'' : '').">
					".CPAGES[$page_name]['photo']."<p>".CPAGES[$page_name]['name']."</p>".
				"</a></li>
			"; 
		}
	}
	$result .= "<li class='nav-selector'></li></ul></div>";
	echo $result;
}

function topbar($name){ // Шапка страницы
	echo "
		<header class='topbar'>
			<h1 class='section-name hex-animation'>$name</h1>
			<div class='nav-controll nav-controll-close'><span></span><span></span><span></span></div>
		</header>
	";
}

function footer_panel(){
	echo "
		<footer>
			<div class='footer-row'>
				<div class='footer-row-80'>
					<a href='/'>
						<svg version='1.1' class='small-logo' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 546.76 380.96' style='enable-background:new 0 0 546.76 380.96;' xml:space='preserve'><g><path style='transform:rotateY(180deg); transform-origin:center center;' class='st0' d='M0,203.57V23.48C0,10.51,10.51,0,23.48,0h499.81c12.97,0,23.48,10.51,23.48,23.48l0,334.28c0,21.26-25.62,31.02-40.51,15.85c-4.28-4.36-8.86-8.49-13.73-12.38c-36.77-29.4-89.86-44.31-157.68-44.31H23.48C10.51,316.91,0,306.4,0,293.44V203.57z'/><circle class='st1' cx='119.05' cy='157.87' r='35.08'/><circle class='st1' cx='244.91' cy='157.87' r='35.08'/><circle class='st1' cx='370.77' cy='157.87' r='35.08'/></g></svg>
						<p>InterHelper</p>
					</a>
				</div>
			</div>
			<div class='footer-row'>
				<div class='footer-row-80'>
					<div class='footer-column'>
						<h2 class='underline-text2'>О InterHelper</h2>
						<p>Лучший интернет ассистент <br/>
						для помощи в вашем бизнесе</p>
					</div>
					<div class='footer-column'>
						<h2 class='underline-text2'>Контакты</h2>
						<a href='https://interfire.ru'>
							<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 96.4 85' style='enable-background:new 0 0 96.4 85;' xml:space='preserve'><g ><g><polygon points='68.1,26.5 80.1,14.4 80.2,14.3 74.6,8.7 74.5,8.6 62.5,20.6 62.3,20.8 67.9,26.4 '/><rect x='18' y='13.5' transform='matrix(0.7071 -0.7071 0.7071 0.7071 -4.6095 23.9395)' width='17.2' height='8.1'/><polygon points='67.9,64.3 79.9,52.2 80.1,52.1 74.5,46.5 74.3,46.4 62.3,58.4 62.1,58.6 67.8,64.2 '/><rect x='18.2' y='51.3' transform='matrix(0.7071 -0.7071 0.7071 0.7071 -31.285 35.1438)' width='17.2' height='8.1'/><path d='M96.4,6.4v-8.5H0v8.5h5.6V36H0v8.9h5.6v29.6H0v8.5h96.4v-8.5h-5.6V44.8h5.6V36h-5.6V6.4H96.4z M43.9,44.8c-0.9,7.6-4.4,14-9.9,19.5c-5.5,5.5-12.7,9-20.3,9.9V44.8H43.9z M13.7,35.7V6.4h30.2C43,14,39.5,20.3,34,25.8C28.5,31.3,21.3,34.8,13.7,35.7z M34.6,36c5.8-4.2,10.5-9.9,13.6-16.4C51.3,26,56,31.7,61.8,36H34.6z M48.2,58c3.1,6.5,7.8,12.2,13.6,16.4H34.6C40.4,70.2,45.1,64.5,48.2,58z M82.7,44.8v29.3c-7.6-0.9-14.9-4.4-20.3-9.9c-5.5-5.5-9-11.8-9.9-19.5H82.7z M82.7,6.4v29.3c-7.6-0.9-14.9-4.4-20.3-9.9c-5.5-5.5-9-11.8-9.9-19.5H82.7z'/></g></g></svg>
							<p class='underline-text3'>Мы interfire.ru</p>
						</a>
						<a href='https://www.youtube.com/channel/UCnObj4J7fiML4n01GIXfXNg'>
							<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 310 310' style='enable-background:new 0 0 310 310;' xml:space='preserve'><g id='XMLID_822_'><path id='XMLID_823_' d='M297.917,64.645c-11.19-13.302-31.85-18.728-71.306-18.728H83.386c-40.359,0-61.369,5.776-72.517,19.938C0,79.663,0,100.008,0,128.166v53.669c0,54.551,12.896,82.248,83.386,82.248h143.226c34.216,0,53.176-4.788,65.442-16.527C304.633,235.518,310,215.863,310,181.835v-53.669C310,98.471,309.159,78.006,297.917,64.645z M199.021,162.41l-65.038,33.991c-1.454,0.76-3.044,1.137-4.632,1.137c-1.798,0-3.592-0.484-5.181-1.446c-2.992-1.813-4.819-5.056-4.819-8.554v-67.764c0-3.492,1.822-6.732,4.808-8.546c2.987-1.814,6.702-1.938,9.801-0.328l65.038,33.772c3.309,1.718,5.387,5.134,5.392,8.861C204.394,157.263,202.325,160.684,199.021,162.41z'/></g></svg>
							<p class='underline-text3'>Наш youtube</p>
						</a>
						<a href='https://vk.com/interfire'>
							<svg width='24px' height='24px' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg' data-name='Layer 1'><path d='M15.07294,2H8.9375C3.33331,2,2,3.33331,2,8.92706V15.0625C2,20.66663,3.32294,22,8.92706,22H15.0625C20.66669,22,22,20.67706,22,15.07288V8.9375C22,3.33331,20.67706,2,15.07294,2Zm3.07287,14.27081H16.6875c-.55206,0-.71875-.44793-1.70831-1.4375-.86463-.83331-1.22919-.9375-1.44794-.9375-.30206,0-.38544.08332-.38544.5v1.3125c0,.35419-.11456.5625-1.04162.5625a5.69214,5.69214,0,0,1-4.44794-2.66668A11.62611,11.62611,0,0,1,5.35419,8.77081c0-.21875.08331-.41668.5-.41668H7.3125c.375,0,.51044.16668.65625.55212.70831,2.08331,1.91669,3.89581,2.40625,3.89581.1875,0,.27081-.08331.27081-.55206V10.10413c-.0625-.97913-.58331-1.0625-.58331-1.41663a.36008.36008,0,0,1,.375-.33337h2.29169c.3125,0,.41662.15625.41662.53125v2.89587c0,.3125.13544.41663.22919.41663.1875,0,.33331-.10413.67706-.44788a11.99877,11.99877,0,0,0,1.79169-2.97919.62818.62818,0,0,1,.63544-.41668H17.9375c.4375,0,.53125.21875.4375.53125A18.20507,18.20507,0,0,1,16.41669,12.25c-.15625.23956-.21875.36456,0,.64581.14581.21875.65625.64582,1,1.05207a6.48553,6.48553,0,0,1,1.22912,1.70837C18.77081,16.0625,18.5625,16.27081,18.14581,16.27081Z'/></svg>
							<p class='underline-text3'>Наш вк</p>
						</a>
						<a href='tel:+74951284148'>
						<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 53.942 53.942' style='enable-background:new 0 0 53.942 53.942;' xml:space='preserve'><path d='M53.364,40.908c-2.008-3.796-8.981-7.912-9.288-8.092c-0.896-0.51-1.831-0.78-2.706-0.78c-1.301,0-2.366,0.596-3.011,1.68c-1.02,1.22-2.285,2.646-2.592,2.867c-2.376,1.612-4.236,1.429-6.294-0.629L17.987,24.467c-2.045-2.045-2.233-3.928-0.632-6.291c0.224-0.309,1.65-1.575,2.87-2.596c0.778-0.463,1.312-1.151,1.546-1.995c0.311-1.123,0.082-2.444-0.652-3.731c-0.173-0.296-4.291-7.27-8.085-9.277c-0.708-0.375-1.506-0.573-2.306-0.573c-1.318,0-2.558,0.514-3.49,1.445L4.7,3.986c-4.014,4.013-5.467,8.562-4.321,13.52c0.956,4.132,3.742,8.529,8.282,13.068l14.705,14.705c5.746,5.746,11.224,8.66,16.282,8.66c0,0,0,0,0.001,0c3.72,0,7.188-1.581,10.305-4.698l2.537-2.537C54.033,45.163,54.383,42.833,53.364,40.908z'/></svg>
							<p class='underline-text3'>Офис +7 (495) 128-41-48</p>
						</a>
					</div>
					<div class='footer-column'>
						<h2 class='underline-text2'>Информация</h2>
						<a class='underline-text3' href='/page/tariffs'>Сравнение тарифов</a>
						<a class='underline-text3' href='/page/capabilitys'>Инструменты</a>
						<a class='underline-text3' href='/page/contacts'>Контакты</a>
						<a class='underline-text3' href='/'>Главная</a>
						<a class='underline-text3' href='/page/reviews'>Отзывы</a>
						<a class='underline-text3' href='/page/about'>О нас</a>
					</div>
					<div class='footer-column'>
						<h2 class='underline-text2'>Реквизиты</h2>
						<p>ООО \"ИНТЕРФАЕР\"</p>
						<p>ОГРН 1217700531498</p>
						<p>ИНН 9704098045</p>
					</div>
					<div class='footer-column'>
						<h2 class='underline-text2'>Полезные ссылки</h2>
						<a class='underline-text3' href='/privacy'>Политика конфиденциальности</a>
						<a class='underline-text3' href='/public-offer'>Договор публичной оферты</a>
						<a class='underline-text3' href='/page/api'>API для разработчиков</a>
						<a class='underline-text3' href='/page/paymentoperation'>Способы оплаты</a>
						<a class='underline-text3' href='/page/blog'>Блог</a>
						<a class='underline-text3' href='/page/help'>FAQ</a>
					</div>
					<div class='footer-column'>
						<h2 class='underline-text2'>Войти</h2>
						<a class='underline-text3' href='/engine/employee/login'>Вход для сотрудников</a>
						<a class='underline-text3' href='/engine/client/login'>Вход для клиентов</a>
					</div>
				</div>
			</div>
			<div class='footer-row'>
				<div class='footer-row-80'>
					<p>© ".Date('Y')." InterHelper</p>
					<p>Тех. работы</p>
				</div>
			</div>
		</footer>
	";
}

function login_menu(){ // форма входа в лк босса
	echo '
		<div id="loginingmenu">
			<div class="containerr" id="containerr">
				<div id="loginingmenuExit"></div>
				<div class="form-container sign-up-container">
					<form class="ajax_login_form">
						<h2 id="create" style="color: #fff; font-size: 2em;position: relative;">Создать аккаунт</h2>
						<span style=" color: #fff; position: relative;bottom: 20px;"></span>
						<input style="color: #fff;" type="text" name="user" placeholder="имя" />
						<input id="email_input" style="color: #fff;" type="email" name="email" placeholder="Почта" />
						<input id="phone_input" style="color: #fff;" type="phone" name="phone" placeholder="Телефон" />
						<div style="width:100%;position:relative;">
							<input style="color: #fff;" type="password" name="password" placeholder="Пароль" />
							<span class="password_eye"></span>
						</div>
						<p style="font-size:13px;color:#fff;">
						<input class="check_box fake_check_box" id="submit_checkbox" type="checkbox"/>
						<label for="submit_checkbox"></label> вы подтверждаете <a href="#" style="color:#0ae;">
						пользовательское соглашение</a> и принимаете <a href="#" style="color:#0ae;">политику конфеденциальности</a>.
						</p>
						<button class="butr reg_btr" type="submit">Начать</button>
						<span class="load_span"></span>
					</form>
				</div>
				<div class="form-container sign-in-container">
					<form class="ajax_register_form" style="color: #fff;">
						<h2 style="font-size: 2em; position: relative; bottom: 20px; " id="signinn">Войти</h2>
						<span style="font-size: 1.3em;  position: relative; bottom: 20px"></span>
						<input style="color: #fff;" type="email" placeholder="Логин" name="login" />
						<div style="width:100%;position:relative;">
							<input style="color: #fff;" type="password" placeholder="Пароль" name="pass" />
							<span class="password_eye"></span>
						</div>
						<a href="#" class="reset_pass" style=" color: #0ae; position: relative; top: 20px;">Забыли пароль?</a>
						<button class="butr" type="submit">Войти</button>
						<a href="/engine/consultant/login" style=" color: #0ae; position: relative; top: 20px;" >Вход для сотрудника</a>
					</form>
				</div>
				<div class="overlay-container">
					<div class="overlay">
						<div class="overlay-panel overlay-left">
							<h2 style="font-size: 2.5em; position: relative;bottom: 50px;">С возвращением!</h2>
							<p class="login_card_text">Чтобы оставаться на связи с нами, войдите, указав свою личную информацию</p>
							<button style="position: relative; top: 50px;" class="ghost" id="signIn">Войти</button>
						</div>
						<div class="overlay-panel overlay-right">
							<h2 style="font-size: 2.5em; position: relative;bottom: 50px;">Регистрация</h2>
							<p class="login_card_text">Введите свои личные данные и начните путешествие с нами</p>
							<button style="position: relative; top: 50px;" class="ghost" id="signUp">Начать</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	';
}
function head3($file_path, $photo){ // шапка на главной
	$login_content = '';
	if(isset($_SESSION["boss"])) $login_content = '
			<a href="/engine/pages/profile" style=position:relative;border:none;margin-left:10px;height:60px;width:60px;border-radius:50%;background-color:#fff;background-image:url('.$file_path.$photo.');background-repeat:no-repeat;background-size:cover;background-position:center;cursor:pointer;>
				<form class="ajax_login_form_out" method=post action=/engine/login>
					<input style=display:none; name="remove" />
					<button class="exit_button" style="bottom:-5%;width:25px;height:25px;bottom:5%;right:5%;"; type="submit"></button>
				</form>
			</a>
		';
	else $login_content = '
			<li class="nav-link nav-link-auth sigin"><a href="#"><img src="/scss/imgs/right.png" alt=""> Вход</a></li>
			<li class="nav-link nav-link-reg sigup"><a href="#">Регистрация</a></li>
	';
	echo '
	<nav class="nav">
		<div class="container">
			<div class="nav-wrapper">
				<div class="nav-logo">
					<a href="/"><img src="/scss/imgs/interhelper_logo.png" alt=""></a>
				</div>
				<div class="nav-links">
					<img src="/scss/imgs/menu.svg" alt="" class="open-menu">
					<ul class="nav-links-list">
						<li class="nav-link"><a href="/page/capabilitys">Возможности</a></li>
						<li class="nav-link"><a href="/page/about">О нас</a></li>
						<li class="nav-link"><a href="/page/tariffs">Тарифы</a></li>
						<li class="nav-link"><a href="/page/contacts">Контакты</a></li>
						<li class="nav-link"><a href="/page/reviews">Отзывы</a></li>
						<li class="nav-link"><a href="/page/help">FAQ</a></li>
						'.$login_content.'
					</ul>
				</div>
			</div>
		</div>
	</nav>
	';
}
function save_file($img, $type, $update_index, $connection, $more){ // сохраненить файл
	$filename = $img['name'];
	$allowed_filetypes = VARIABLES['photos'][$type]['accepted_types'];
	$max_filesize = VARIABLES['photos'][$type]['max_weight'];
	$upload_path = $_SERVER['DOCUMENT_ROOT'].VARIABLES['photos'][$type]['upload_path'];
	$img_type = substr($filename, strpos($filename,'.'), strlen($filename)-1);
	$filename = uniqid().uniqid().$img_type;
	 if(in_array(strtolower($img_type), $allowed_filetypes)){ // Сверяем полученное расширение со списком допутимых расширений. 
		 if(filesize($img['tmp_name']) <= $max_filesize){ // Проверим размер загруженного файла.
			  if(is_writable($upload_path)){ // Проверяем, доступна ли на запись папка.
				if(move_uploaded_file($img['tmp_name'],$upload_path.$filename)) { 
					$sql = null;
					if($type == 'boss_profile_photo'){
						remove_file($type, 'users', 'id', $connection, $update_index);
						$sql = "UPDATE users SET photo = '$filename' WHERE id = '$update_index'"; 
					} elseif($type == 'assistent_profile_photo'){ 
						remove_file($type, 'assistents', 'id', $connection, $update_index);
						$sql = "UPDATE assistents SET photo = '$filename' WHERE id = '$update_index' and domain = '$more'"; 
					} elseif($type == 'notification_photos'){ 
						remove_file($type, 'notifications', 'uid', $connection, $update_index);
						$sql = "UPDATE notifications SET photo = '$filename' WHERE uid = '$update_index' and owner_id = '$more'"; 
					} elseif($type == 'tariff_photo'){ 
						$info = EDITIONS[$update_index];
						$photo_before = $info["img"];
						remove_file($type, 'tariffs', null, $connection, $photo_before);
						$info["img"] = $filename;
						$info = json_encode($info, JSON_UNESCAPED_UNICODE);
						$sql = "UPDATE tariffs SET tariff = '$info' WHERE name = '$update_index'"; 
					} elseif($type == 'reviews_photos'){
						remove_file($type, 'reviews', 'review_id', $connection, $update_index);
						$sql = "UPDATE reviews SET photo = '$filename' WHERE review_id = '$update_index'"; 
					} elseif($type == 'news_photos'){
						remove_file($type, 'news', 'news_id', $connection, $update_index);
						$sql = "UPDATE news SET photo = '$filename' WHERE news_id = '$update_index'"; 
					} elseif($type == 'tools_photos'){
						remove_file($type, 'tools', 'tool_id', $connection, $update_index);
						$sql = "UPDATE tools SET photo = '$filename' WHERE tool_id = '$update_index'"; 
					} elseif($type == 'crm_item_photo') remove_file($type, 'crm_items', 'uid', $connection, $update_index);
					elseif($type == 'crm_files' && isset($update_index)) remove_file($type, 'crm_items', 'uid', $connection, ['uid' => $update_index, "column" => $more]);
					elseif($type == 'crm_files' && isset($more)) remove_file($type, 'crm', 'owner_id', $connection, $more);
					if(isset($sql)) { 
						if ($connection->query($sql) === TRUE) return ["access"=> true, "text"=> $filename];
						else return ["access"=> false, "text"=> ERRORS['sql_error']]; 
					} else return ["access"=> true, "text"=> $filename];
				} else return ["access"=> false, "text"=> ERRORS['file_load_error']];
			  } else return ["access"=> false, "text"=> ERRORS['777_error']];
		 } else return ["access"=> false, "text"=> ERRORS['so_big_file']]; 
	} else return ["access"=> false, "text"=> ERRORS['not_accepted_file']]; 
}
function remove_file($type, $database, $column, $connection, $update_index){ // удалить фото
	if($database != 'tariffs' && $database != 'crm_items' && $database != 'crm' && $type != 'notification_adds'){
		$sql = "SELECT photo FROM $database WHERE $column = '$update_index'";
		$query = mysqli_query($connection, $sql);
		$prev_photo = mysqli_fetch_row($query);
		if(!isset($prev_photo)) return;
		$prev_photo = $prev_photo[0];
		if(isset($prev_photo) && $prev_photo != 'user.png') @unlink($_SERVER['DOCUMENT_ROOT'].VARIABLES['photos'][$type]['upload_path'].$prev_photo);
	} elseif($database == 'crm_items' || $database == 'crm'){
		if(gettype($update_index) != "array"){
			$sql = "SELECT info  FROM $database WHERE $column = '$update_index'";
			$query = mysqli_query($connection, $sql);
			$prev_photo = json_decode(mysqli_fetch_row($query)[0], JSON_UNESCAPED_UNICODE)["helper_photo"];
			if(isset($prev_photo) && $prev_photo != 'user.png') @unlink($_SERVER['DOCUMENT_ROOT'].'/crm_files/'.$prev_photo);
		} else {
			if(isset($update_index['table'])){
				$inner_column = $update_index['column'];
				$table = $update_index['table'];
				$update_index = $update_index['owner_id'];
				$sql = "SELECT columns FROM $database WHERE $column = '$update_index'";
				$query = mysqli_query($connection, $sql);
				$prev_photo = json_decode(mysqli_fetch_row($query)[0], JSON_UNESCAPED_UNICODE)[$table]["table_columns"][$inner_column]['deffault'];
				if(isset($prev_photo) && $prev_photo != 'user.png') @unlink($_SERVER['DOCUMENT_ROOT'].'/crm_files/'.$prev_photo);
			} else {
				$inner_column = $update_index['column'];
				$update_index = $update_index['uid'];
				$sql = "SELECT info  FROM $database WHERE $column = '$update_index'";
				$query = mysqli_query($connection, $sql);
				$prev_photo = json_decode(mysqli_fetch_row($query)[0], JSON_UNESCAPED_UNICODE)[$inner_column];
				if(isset($prev_photo) && $prev_photo != 'user.png') @unlink($_SERVER['DOCUMENT_ROOT'].'/crm_files/'.$prev_photo);
			}
		}
	} else @unlink($_SERVER['DOCUMENT_ROOT'].VARIABLES['photos'][$type]['upload_path'].$update_index); 
}
function get_database_rooms($connection, $boss_id){ // получить сохранённые комнаты
	$sql = "
		SELECT 
			room, 
			info, 
			notes,
			properties, 
			time, 
			domains_list, 
			serving_list, 
			photo,
			session_time,
			served_list, 
			(SELECT count(1) FROM messages_with_users_guests WHERE rooms.id = messages_with_users_guests.room AND messages_with_users_guests.sender = 'offline_form') AS forms,
			(SELECT count(1) FROM messages_with_users_guests WHERE rooms.id = messages_with_users_guests.room AND (messages_with_users_guests.sender = '' OR messages_with_users_guests.sender IS NULL)) AS user_msgs,
			(SELECT count(1) FROM messages_with_users_guests WHERE rooms.id = messages_with_users_guests.room AND messages_with_users_guests.sender != '' AND messages_with_users_guests.sender !='offline_form' AND messages_with_users_guests.sender IS NOT NULL) AS assistent_msgs
		FROM rooms 
		WHERE rooms.room LIKE '%$boss_id!@!@2@!@!%' AND 
		(SELECT count(1) FROM messages_with_users_guests WHERE rooms.id = messages_with_users_guests.room) > 0
	";
	$rooms = attach_sql($connection, $sql, 'query');
	$rooms_list = array();
	foreach($rooms as $room){
		$rooms_list[$room['room']] = [
			'room_link' => $room['room'],
			'info' => json_decode($room['info'], JSON_UNESCAPED_UNICODE),
			'notes' => $room['notes'],
			'properties' => $room['properties'],
			'forms' => $room['forms'],
			'user_msgs' => $room['user_msgs'],
			'assistent_msgs' => $room['assistent_msgs'],
			'time' => $room['time'],
			'domains_list' => json_decode($room['domains_list'], JSON_UNESCAPED_UNICODE),
			'serving_list' => json_decode($room['serving_list'], JSON_UNESCAPED_UNICODE),
			'served_list' => json_decode($room['served_list'], JSON_UNESCAPED_UNICODE),
			'session_time' => $room['session_time'],
			'photo' => json_decode($room['photo'], JSON_UNESCAPED_UNICODE)
		];
	}
	return $rooms_list;
}
function get_database_assistents($connection, $boss_id){ // получить ассистентов
	$sql = "SELECT name, photo, id FROM assistents WHERE domain = '$boss_id'";
	$rows = attach_sql($connection, $sql, 'query');
	$assistents = [];
	foreach ($rows as $row) { 
		$assistents[$row["id"]] = [
			"name"=> $row["name"], 
			"photo"=> $row["photo"] 
		]; 
	}
	return $assistents;
}
function create_book($buttlecry){ // быстрые сообщения
	echo '
		<div :style="[{\'width\': fastmessages.chapters_mode ? \'230px\' : \'410px\'}, {\'top\': !commands_mode ? \'-350px\': \'0px\'}]" class="fast-message-container v-cloak-off" v-cloak>
			<div class="fast-message-chapter-menu bgblackwhite" :class="fastmessages.chapters_mode ? \'fast-message-chapter-menu-open\' : \'fast-message-chapter-menu-close\' ">
				<div class="fast-message-chapter" style="padding:10px;display:inline-flex;align-items:center;justify-content:space-between;">
					<p class="WhiteBlack">Разделы</p>
					<span data-tooltip="Добавить раздел" style="background: green;height:25px;width:25px;" @click="create_chapter(\'new\')" v-if="!(\'create_chapter\' in fastmessages)" class="fast-message-control-add">
						<span style="width:15px;"></span>
						<span style="width:15px;"></span>
					</span>
				</div>
				<input class="search_chapter bgblackwhite WhiteBlack" placeholder="Поиск по разделам" v-model="fastmessages.search_chapter"/>
				<div v-if="\'create_chapter\' in fastmessages"  style="background:transparent !important;display:flex;flex-direction:column;align-items:flex-start;jsutify-content:flex-start;" class="fast-message-chapter" >
					<div style="display:inline-flex;margin-bottom:5px;">
						<span @click="create_chapter(\'save\')" data-tooltip="Сохранить" style="background: green;height:25px;width:25px;margin-right:5px;"  class="fast-message-control-add">
							<span style="width:15px;"></span>
							<span style="width:15px;"></span>
						</span>
						<span @click="create_chapter(\'cancel\')" data-tooltip="Отменить" class="fast-message-chapter-remove"><span></span><span></span></span> 
					</div>
					<input placeholder="Название главы" style="margin:0;font-size:20px;width:95%;text-align:center;border-radius:10px;font-size:17px;background:rgba(0,0,0,0.8);padding-right:10px;" v-model="fastmessages.create_chapter" class="changable_input" />
				</div>
				<div v-if="(fastmessages.chapters?.[chapter]?.chapter_name||\'Главный раздел\').toLowerCase().indexOf(fastmessages.search_chapter.toLowerCase()) != -1" v-if="fastmessages.chapters.hasOwnProperty(chapter)" :style="{\'background\': fastmessages.selected_chapter == chapter ? \'#0ae\' : \'transparent\'}" @click="fastmessages.selected_chapter = chapter" class="fast-message-chapter" :key="chapter" v-for="(chapter_messages, chapter) in fastmessages.chapters">
					<span @click="remove_chapter(chapter)" data-tooltip="Удалить раздел" v-if="chapter != \'main\'" class="fast-message-chapter-remove"><span></span><span></span></span> 
					<p class="WhiteBlack">{{(chapter == \'main\' ? \'Главный раздел\': fastmessages.chapters[chapter].chapter_name )}}</p>
				</div>
			</div>
			<div :style="{\'border-bottom-left-radius\': fastmessages.chapters_mode ? \'0px\' : \'10px\'}"  class="fast-message-messaegs-menu bgblackwhite" >
				<div class="fast-message-control">
					<span @click="fastmessages.chapters_mode = !fastmessages.chapters_mode" class="fast-message-control-menu" :class="fastmessages.chapters_mode ? \'fast-message-messaegs-menu-open\' : \'fast-message-messaegs-menu-close\' "><span></span><span></span><span></span></span>
					<div class="fast-message-control-right-btns">
						<span @click="newfastmessage(\'new\')" data-tooltip="Создать новое сообщение" style="background: green;" v-if="!(fastmessages.chapters?.[fastmessages.selected_chapter]||{}).hasOwnProperty(\'create_fastmessage\')" class="fast-message-control-add"><span></span><span></span></span>
						<span @click="commands_mode = !commands_mode" data-tooltip="Закрыть окно"  style="background: #000;" class="fast-message-control-remove"><span></span><span></span></span>
					</div>
				</div>
				<div v-if="fastmessages.chapters?.hasOwnProperty(fastmessages.selected_chapter)" class="fast-message-selected-chapter">
					<p class="WhiteBlack" style="font-size:20px;" v-if="fastmessages.selected_chapter == \'main\'">Главный раздел</p>
					<input v-else @change="update_chapter()" class="WhiteBlack" style="border-radius:10px;outline:none;margin:0;font-size:20px;width:100%;text-align:center;background:transparent;border:none;" v-model="fastmessages.chapters[fastmessages.selected_chapter].chapter_name" class="changable_input" />
				</div>
				<input class="search_chapter bgblackwhite WhiteBlack" placeholder="Поиск по сообщениям" v-model="fastmessages.search_message"/>
				<div class="fast-message" v-if="(fastmessages.chapters?.[fastmessages.selected_chapter]||{}).hasOwnProperty(\'create_fastmessage\')">
					<textarea class="add_fastmessage-input bgblackwhite WhiteBlack" v-model="fastmessages.chapters[fastmessages.selected_chapter][\'create_fastmessage\']"></textarea>
					<div style="display:flex;flex-direction:column;">
						<span data-tooltip="Сохранить" class="add_fastmessage-btn" @click="newfastmessage(\'save\')" style="background:green;"><span></span><span></span></span>
						<span data-tooltip="Отменить" style="background:#000;" @click="newfastmessage(\'cancel\')" class="close_add_fastmessage-btn"><span></span><span></span></span>
					</div>		
				</div>
				<div class="fast-message"  v-if="fastmessages.selected_chapter == \'main\' && `'.$buttlecry.'`.toLowerCase().indexOf(fastmessages.search_message.toLowerCase()) != -1 && `'.$buttlecry.'`">
					<textarea  @change="update_fastmessage(\'buttlecry\')"  class="add_fastmessage-input fast_message_buttlecry bgblackwhite WhiteBlack" v-html="\''.$buttlecry.'\'"></textarea>
					<div style="display:flex;flex-direction:column;">
						<span data-tooltip="Скопировать сообщение" @click="copy_fastmessage(\'buttlecry\')" class="fast-message-copy-message"></span>
					</div>
				</div>
				<div class="fast-message" 
					v-if="
						uid != \'create_fastmessage\' && uid != \'chapter_name\' && 
						(fastmessages.chapters?.[fastmessages.selected_chapter]?.[uid]||\'\').toLowerCase().indexOf(fastmessages.search_message.toLowerCase()) != -1
					" 
					:key="uid" 
					v-for="(message, uid) in fastmessages.chapters[fastmessages.selected_chapter]"
				>
					<textarea @change="fastmessages.chapters[fastmessages.selected_chapter][uid] = $event.target.value; update_fastmessage(uid)" :class="\'fast_message_\'+uid" class="add_fastmessage-input bgblackwhite WhiteBlack" v-html="fastmessages.chapters[fastmessages.selected_chapter][uid]" ></textarea>	
					<div style="display:flex;flex-direction:column;">
						<span data-tooltip="Скопировать сообщение" @click="copy_fastmessage(uid)" class="fast-message-copy-message"></span>
						<span data-tooltip="Удалить сообщение" @click="remove_fastmessage(uid)" class="fast-message-remove-message"><span></span><span></span></span>
					</div>
				</div>
			</div>
		</div>
	';
}
function session_key($type, $domain, $server_info, $host, $assistent_id){ // ключ авторизации на сервере
	$uniqid = uniqid('', true);
	$curl_json = ["login" => $server_info["login"], "password"=>$server_info["password"], "type"=>"", "info" => []];
	$curl_json["type"] = "session_key";
	if($type == 'boss') $session = "server_info";
	else $session = "assistent_server_info";
	$curl_json["info"] = [
		"token"=> $uniqid, 
		"domain" => $domain, 
		"type" => $type,
		"assistent_id" => $assistent_id
	];
	if(isset($_SESSION[$session])){
		$data = $_SESSION[$session];
		$token = $data["token"];
		$curl_json["info"]["old_token"] = $token;
	}
	send_curl($curl_json, $host);
	$_SESSION[$session] = ["token" => $uniqid];
	return $uniqid;
}
function emojis($root){ // emojis
	$array = [];
	$files = scandir($root.'/emojis');
	foreach ($files as $folder_key => $folder_name) {
        if($folder_key == 0 || $folder_key == 1) continue;
        $folder = $root.'/emojis/'.$folder_name;
        if(is_dir($folder)){
			$array[$folder_name] = [];
            $folder_files = scandir($folder);
            foreach ($folder_files as $file_key => $file) {
                if($file_key == 0 || $file_key == 1) continue;
				$file_key_name = ':'.$folder_name.'-'.explode('.', $file)[0].'-'.end(explode('.', $file)).':';
				$array[$folder_name][$file_key_name] = $file;
            }
        }
    }
	return $array;
}
function send_curl($json, $host){ // курл POST на сервер
	if(!array_key_exists('make_json', $json)) $json = json_encode($json, JSON_UNESCAPED_UNICODE); 
	$ch = curl_init($host);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	if(gettype($json) == 'string') curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($json)) );
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
function section_header($name, $photo){ // раздел
	echo "
		<div id='section_top' class='wow bounceInDown'>
			<div id='logo'>
				<span onclick=\"window.location.href='/'\"></span>
				<h1 class='WhiteBlack' onclick=\"window.location.href='/'\">InterHelper</h1>
			</div>
			<div id='section_name'>
				<h2 class='WhiteBlack'>$name</h2>
				<span class='section_img' style='background-image: url(/scss/imgs/$photo);background-color:#252525;border-radius:50%;background-size:80%;padding:5px;'></span>
			</div>
		</div>
	";
}
function appendfooter(){ // футер в личных кабинетах
    echo '
    <footer id="footer">
        <div id="footer_top">	
	        <div class="column" id="footer_first_column">
		        <div id="footer_logo">
			        <div onclick="window.location.href = \'/\'" id="foorer_logo_img"></div>
			        <h2 style="cursor:pointer;" onclick="window.location.href = \'/\'" class="WhiteBlack">InterHelper</h2>
		        </div>
        		<div id="footer_short_about">
        			<h2 class="footer_header">О InterHelper</h2>
        			<p class="WhiteBlack">Лучший интернет ассистент <br /> для оживления вашего сайта</p>
        		</div>
        		<div id="footer_contacts">
        			<h2 class="footer_header">Контакты</h2>
        			<div><div class="icon"></div><a class="WhiteBlack" href="https://interfire.ru/">interfire.ru</a></div>
        			<div><div class="icon"></div><a class="WhiteBlack" href="mailto:info@interhelper.ru">info@interhelper.ru</a></div>
					<div><div class="icon" style="background-image: url(/scss/imgs/phone.png);background-size:17px;background-repeat:no-repeat;background-position:center;"></div><a class="WhiteBlack" href="tel:+74951284148">+7 (495) 128-41-48</a></div>
        		</div>
	        </div>
        	<div class="column" id="footer_second_column">
				<h2 class="footer_header">Информация</h2>
				<a class="WhiteBlack" href="/page/tariffs">Сравнение тарифов</a>
				<a class="WhiteBlack" href="/page/capabilitys">Инструменты</a>
				<a class="WhiteBlack" href="/page/contacts">Контакты</a>
				<a class="WhiteBlack" href="/">Главная</a>
				<a class="WhiteBlack" href="/page/reviews">Отзывы</a>
				<a class="WhiteBlack" href="/page/about" >О нас</a>
				<h2 style="margin-top: 20px;" class="footer_header">Реквизиты</h2>
				<p class="WhiteBlack" style="margin-left:0;margin-top: 10px;">ООО "ИНТЕРФАЕР"</p>
				<p class="WhiteBlack" style="margin-left:0;margin-top: 10px;">ОГРН 1217700531498</p>
				<p class="WhiteBlack" style="margin-left:0;margin-top: 10px;">ИНН 9704098045</p>
        	</div>
        	<div class="column" id="footer_third_column">
        		<h2 class="footer_header">Полезные ссылки</h2>
				<a class="WhiteBlack" href="/privacy">Политика конфиденциальности</a>
                <a class="WhiteBlack" href="/public-offer">Договор публичной оферты</a>
				<a class="WhiteBlack" href="/page/api">API для разработчиков</a>
				<a class="WhiteBlack" href="/page/paymentoperation">Способы оплаты</a>
        		<a class="WhiteBlack" href="/page/help">FAQ</a>
				<a class="WhiteBlack" href="/page/blog">Блог</a>
        	</div>
        </div>
        <div id = "footer_bottom">
        	<div id="links2">
        		<a href="https://vk.com/interfire" class="follow_img"></a>
        		<a href="#" class="follow_img"></a>
        		<a href="https://interfire.ru/" class="follow_img"></a>
        	</div>
        	<div id="last_text"><span id="copyright"></span><p class="WhiteBlack">'.date('Y').' InterHelper. All Right reserved</p></div>
        </div>
    </footer>
    ';
}
function page_loader(){ // preloader
	echo '
	<div class="page_loader">
		<span></span>
		<img src="/scss/imgs/interhelper_logo.png" alt="">
	</div>
	';
}
function stop_spam($type){ // спам в файл
	session_start();
	$today = date("Y-m-d H:i:s");
	if(isset($_SESSION[$type])){
		$flag = strtotime($today) < strtotime($_SESSION[$type]["time"]." +5 seconds");
		if($_SESSION[$type]['counter'] >= 5 && $flag) return true;
		elseif($flag) $_SESSION[$type]['counter']++;
		else $_SESSION[$type]['counter'] = 0;
		$_SESSION[$type]["time"] = $today;
	} else {
		$_SESSION[$type] = [
			"time" => $today,
			"counter" => 0,
		];
	}
	return false;
}
function appendfooter2($file_path, $photo){ // футер на главной
	$login_content = '';
	if(isset($_SESSION["loginkey"]) && $_SESSION["loginkey"] != '') $login_content = '
			<a href="/engine/pages/profile" style=background-color:#fff;position:relative;border:none;margin-left:10px;height:80px;width:80px;border-radius:50%;background-image:url('.$file_path.$photo.');background-repeat:no-repeat;background-size:cover;background-position:center;cursor:pointer;>
				<form class="ajax_login_form_out" method=post action=/engine/login>
					<input style=display:none; name="remove" />
					<button class="exit_button" style=bottom:-5%; type=submit></button>
				</form>
			</a>
		';
	else $login_content = '
			<a id="SignIn2" style="padding:0;cursor:pointer;border-radius:5px;border:2px solid #0ae;display:flex;justify-content:center;align-items:center;width:120px;height:30px;border-color:#fff;" class=" sigup">Регистрация</a>
			<a id="SignUp2" style="padding:0;cursor:pointer;width:120px;display:flex;justify-content:center;align-items:center;height:30px;border-color:#fff;" class=" sigin">Войти</a>
		';
    echo '
    <footer id="footer">
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript" >
	(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
	m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
	(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

	ym(86636315, "init", {
			clickmap:true,
			trackLinks:true,
			accurateTrackBounce:true,
			webvisor:true
	});
	</script>
	<noscript><div><img src="https://mc.yandex.ru/watch/86636315" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
        <div id="footer_top">	
            <div class="column wow slideInLeft" id="footer_first_column">
                <div id="footer_logo">
                    <div onclick="window.location.href = \'/\'" id="foorer_logo_img"></div>
                    <h2 style="cursor:pointer;" onclick="window.location.href = \'/\'" >InterHelper</h2>
                </div>
                <div id="footer_short_about">
                    <h2 class="footer_header">О InterHelper</h2>
                    <p>Лучший интернет ассистент <br /> для помощи в вашем бизнесе</p>
                </div>
                <div id="footer_contacts_white">
                    <h2 class="footer_header">Контакты</h2>
                    <div><div class="icon"></div><a href="https://interfire.ru/" onclick="ym(86636315,\'reachGoal\',\'perehodinterfire\')">interfire.ru</a></div>
					<div><div class="icon"></div><a href="mailto:info@interhelper.ru" onclick="ym(86636315,\'reachGoal\',\'clickpopochte\')">info@interhelper.ru</a></div>
					<div><div class="icon" style="background-image: url(/scss/imgs/phone.png);background-size:17px;background-repeat:no-repeat;background-position:center;"></div><a href="tel:+74951284148" onclick="ym(86636315,\'reachGoal\',\'clickpotelefonam\')">+7 (495) 128-41-48</a></div>
                </div>
            </div>
            <div class="column wow bounceInUp" id="footer_second_column">
				<h2 class="footer_header">Информация</h2>
				<a href="/page/tariffs">Сравнение тарифов</a>
				<a href="/page/capabilitys">Инструменты</a>
				<a href="/page/contacts">Контакты</a>
                <a href="/">Главная</a>
				<a href="/page/reviews">Отзывы</a>
                <a href="/page/about" >О нас</a>
				<h2 style="margin-top: 20px;" class="footer_header">Реквизиты</h2>
				<p style="color:#fff;margin-top: 10px;">ООО "ИНТЕРФАЕР"</p>
				<p style="color:#fff;margin-top: 10px;">ОГРН 1217700531498</p>
				<p style="color:#fff;margin-top: 10px;">ИНН 9704098045</p>
            </div>
            <div class="column wow bounceInUp" id="footer_third_column">
                <h2 class="footer_header">Полезные ссылки</h2>
                <a href="/privacy">Политика конфиденциальности</a>
                <a href="/public-offer">Договор публичной оферты</a>
				<a href="/page/api">API для разработчиков</a>
				<a href="/page/paymentoperation">Способы оплаты</a>
				<a href="/page/blog">Блог</a>
                <a href="/page/help">FAQ</a>
            </div>
            <div class="column wow slideInRight" id="footer_fourth_column">
            	<h2   class="footer_header">Вход</h2>
            	'.$login_content.'
                <span href="#header" class="ToSection2" id="To_up_button"></span>
            </div>
        </div>
        <div id = "footer_bottom_white">
            <div id="links2" class="wow bounceInUp">
                <a href="https://vk.com/interfire" class="follow_img"></a>
                <a href="#" class="follow_img"></a>
                <a href="https://interfire.ru/" class="follow_img"></a>
            </div>
            <div id="last_text" class="wow bounceInUp"><span id="copyright_white"></span><p>'.date('Y').' InterHelper. All Right reserved</p></div>
        </div>
    </footer>
    ';
}
function team_msg_notification(){ // уведомления 
	return "
		socket.on('consultant_message_notification', (data) => { 
			vue.notification_msg = data;
			if(!vue.room) $('.message_notification_form').css('right', '100px'); 
			else if(vue.room != data.sender || data.notification_type == 'assistent_chat') $('.message_notification_form').css('right', '100px'); 
			if(data.notification_type == 'assistent_chat'){
				if(vue?.userlist?.assistents && !data.type){
					vue.userlist.assistents[data.sender]['message'] = data.message;
					vue.userlist.assistents[data.sender]['message_adds'] = data.message_adds;
				} else if(vue?.userlist?.assistents?.public_room){
					vue.userlist.assistents['public_room']['message'] = data.message;
					vue.userlist.assistents['public_room']['message_adds'] = data.message_adds;
				}
			} else {
				if(!vue?.userlist?.rooms) return;
				vue.userlist.rooms[data.sender]['new_message']['message'] = data.message;
				vue.userlist.rooms[data.sender]['new_message']['status'] = 'unreaded';
				vue.userlist.rooms[data.sender]['new_message']['message_adds'] = data.message_adds;
			}
		}); 
	";
}
function team_msg_notification_body($path){ // уведомление о сообщении в командном чате
	echo "
		<div class='message_notification_form v-cloak-off' v-cloak>
			<span class='close_notification'><span></span><span></span></span>
			<div class='notification_info'>
				<div class='message_notification_form_photo' :style='\"background-image: url(\"+notification_msg.photo+\");\"'></div>
				<div class='message_notification_form_info'>
					<h3>{{notification_msg.user}}</h3>
					<h3>{{notification_msg.email}}</h3>
					<h3 style='color:#f90;'>{{notification_msg.departament}}</h3>
					<a style='color:#0ae;' v-if='notification_msg.link' :href='notification_msg.link'>Перейти в диалог</a>
				</div>
			</div>
			<span class='notification_time'>{{notification_msg.time.split(' ')[0].split('-').reverse().join('.')}} <span style='color:#f90'>{{notification_msg.time.split(' ')[1].split(':').slice(0,2).join(':')}}</span></span>
			<div style='overflow-y:auto;'>
				<p class='list-message-block-message' style='word-break: break-word;margin:10px;' v-if='notification_msg.message' v-html='find_emojis(notification_msg.message)'></p>
				<div style='display:flex;flex-direction:column;' v-if='notification_msg.message_adds'>
					<img v-for='add in JSON.parse(notification_msg.message_adds)' :src='\"/user_adds/\" + add' style='display:block;max-height:430px;max-width:430px;margin:10px;' v-if='regexp.indexOf(add.substr(add.lastIndexOf(\".\"), add.length)) == -1'></img>
					<p style='margin:20px;margin-left:10px;' v-for ='add in JSON.parse(notification_msg.message_adds)' v-if='regexp.indexOf(add.substr(add.lastIndexOf(\".\"), add.length)) != -1'>
						<a class='download_btn' :href='\"/user_adds/\" + add' download  >Скачать {{add.split('.').slice(-1)[0]}}</a>
					</p>
				</div>
			</div>
		</div>	
	";
}
function admin_navigation($active){ // ссылки для админа
	$links = ["admin" => ['faq', 'variables', 'tariff', 'statistic', 'users', 'assistents', 'tools', 'reviews', 'news']];
	$result = "<header>";
	foreach($links as $dir_name => $pages){
		foreach($pages as $page_name){ $result .= "<div ".($active != $page_name ? 'onclick="location.href=`/engine/'.$dir_name.'/'.$page_name.'`;"' : '')." class='".($active == $page_name ? 'page_active' : '').' '.($dir_name == 'consultant' ? 'assistent_pages' : '')." page_$page_name'><span style='background-image: url(\"".APAGES[$page_name]['photo']."\");'></span>".APAGES[$page_name]['name']."</div>"; }
	}
	$result .= "</header>";
	$result .= "<div class='header_control active_header_control' onclick='control(\"header_control\", \"header\", {\"left\": \"0\"}, {\"left\": \"-120px\"})'><span></span></div>";
	echo $result;
}
// почта
$config['smtp_username'] = 'info@interhelper.ru';
$config['smtp_port'] = '587';
$config['smtp_host'] = 'tls://smtp.yandex.ru';
$config['smtp_password'] = 'Fadkj123ADSFJ!';
$config['smtp_debug'] = true;
$config['smtp_charset'] = 'utf-8';
$config['smtp_from'] = 'info@interhelper.ru';
function smtpmail(string $to = null, $mail_to, $subject, $message,string $headers = null) {
	global $config;
	$error = '';
	$SEND =	"Date: ".date("D, d M Y H:i:s") . " UT\r\n";
	$SEND .= 'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
	if ($headers) $SEND .= $headers."\r\n\r\n";
	else {
			$SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
			$SEND .= "To: \"=?".$config['smtp_charset']."?B?".base64_encode($to)."=?=\" <$mail_to>\r\n";
			$SEND .= "MIME-Version: 1.0\r\n";
			$SEND .= "Content-Type: text/html; charset=\"".$config['smtp_charset']."\"\r\n";
			$SEND .= "Content-Transfer-Encoding: 8bit\r\n";
			$SEND .= "From: \"=?".$config['smtp_charset']."?B?".base64_encode($config['smtp_from'])."=?=\" <".$config['smtp_username'].">\r\n";
			$SEND .= "X-Priority: 3\r\n\r\n";
	}
	$SEND .=  $message."\r\n";
	if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
		if ($config['smtp_debug']) array_push($response['errors'],$errno."<br>".$errstr);
		return false;
	}
	if (!server_parse($socket, "220", __LINE__)) return false;
	fputs($socket, "EHLO " . $config['smtp_host'] . "\r\n");
	if (!server_parse($socket, "250", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Не могу отправить HELO!');
		fclose($socket);
		return false;
	}
	fputs($socket, "AUTH LOGIN\r\n");
	if (!server_parse($socket, "334", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Не могу найти ответ на запрос авторизаци.');
		fclose($socket);
		return false;
	}
	fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
	if (!server_parse($socket, "334", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Логин авторизации не был принят сервером!');
		fclose($socket);
		return false;
	}
	fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
	if (!server_parse($socket, "235", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Пароль не был принят сервером как верный! Ошибка авторизации!');
		fclose($socket);
		return false;
	}
	fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
	if (!server_parse($socket, "250", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Не могу отправить комманду MAIL FROM: ');
		fclose($socket);
		return false;
	}
	fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");
	if (!server_parse($socket, "250", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Не могу отправить комманду RCPT TO: ');;
		fclose($socket);
		return false;
	}
	fputs($socket, "DATA\r\n");
	if (!server_parse($socket, "354", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Не могу отправить комманду DATA');
		fclose($socket);
		return false;
	}
	fputs($socket, $SEND."\r\n.\r\n");
	if (!server_parse($socket, "250", __LINE__)) {
		if ($config['smtp_debug']) array_push($response['errors'], 'Не смог отправить тело письма. Письмо не было отправленно!');
		fclose($socket);
		return false;
	}
	fputs($socket, "QUIT\r\n");
	fclose($socket);
	return TRUE;
}
function server_parse($socket, $response, $line = __LINE__) {
	global $config;
	$error = '';
	while (@substr($server_response, 3, 1) != ' ') {
		if (!($server_response = fgets($socket, 256))) {
			if ($config['smtp_debug']) $error .= "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
			return false;
		}
	}
	if (!(substr($server_response, 0, 3) == $response)) {
		if ($config['smtp_debug']) $error .= "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
		return false;
	}
	return true;
}
function send_mails(
	$info, // адреса + имя
	$mail_name, // название рассылки
	$sender_name, // имя отправителя
	$smtp_login, // smtp почта отправителя
	$sender_pass, // пароль отправителя
	$SMTPSecure, 
	$port, 
	$host,
	$mail_body, // тело рассылки
	){
	if(!$mail_body) $mail_body = 'Новое сообщение !';
	$mail_response = array("success" => [], "errors" => []);
	require $_SERVER['DOCUMENT_ROOT'].'/engine/PHPMailer/PHPMailer.php';
	require $_SERVER['DOCUMENT_ROOT'].'/engine/PHPMailer/SMTP.php';
	require $_SERVER['DOCUMENT_ROOT'].'/engine/PHPMailer/Exception.php';
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	//$mail = new PHPMailer(true); //turn on the exception, it will throw exceptions on errors

	$mail->isSMTP(); // Set mailer to use SMTP
	$mail->CharSet = "UTF-8";
	$mail->Host = $host; // Specify main and backup SMTP servers
	$mail->SMTPAuth = true; // Enable SMTP authentication
	$mail->Username = $smtp_login; // SMTP username
	$mail->Password = $sender_pass; // SMTP password
	$mail->SMTPSecure = $SMTPSecure; // Enable TLS encryption, `ssl` also accepted
	$mail->Port = $port; // TCP port to connect to
	$mail->setFrom($smtp_login, $sender_name);
	foreach($info as $recipient_info){ // ADD recepients
		if(isset($recipient_info['name'])) $mail->addAddress($recipient_info['email'], $recipient_info['name']);
		else $mail->addAddress($recipient_info['email']);
	}
	//$mail->addReplyTo($smtp_login, $sender_name);
	// $mail->addCC('cc@example.com');
	// $mail->addBCC('bcc@example.com');
	if(isset($_FILES['mailer_files1']) || isset($_FILES['files'])){
		foreach($_FILES as $file){
			$mail->AddAttachment ($file['tmp_name'], $file['name']);
		}
	}
	
	$mail->SMTPOptions = array(
		'ssl' => array(
		'verify_peer' => false,
		'verify_peer_name' => false,
		'allow_self_signed' => true
		)
	);
	$mail->isHTML(true); // Set email format to HTML
	$mail->Subject = $mail_name;
	$mail->Body = $mail_body;
	$mail->AltBody = $mail_body;
	//$mail->SMTPDebug = 4; // Enable verbose debug output
	if(!$mail->send()) array_push($mail_response['errors'], 'Mailer Error: ' . $mail->ErrorInfo);
	return $mail_response;
}
function __encode($text, $key) {
	$td = mcrypt_module_open ("tripledes", '', 'cfb', '');
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
	if (mcrypt_generic_init ($td, $key, $iv) != -1) {
		$enc_text=base64_encode(mcrypt_generic ($td,$iv.$text));
		mcrypt_generic_deinit ($td);
		mcrypt_module_close ($td);
		return $enc_text; 
	} 
}
function strToHex($string) {
	$hex='';
	for ($i=0; $i < strlen($string); $i++) { $hex .= dechex(ord($string[$i])); }
	return $hex; 
}
function __decode($text, $key) {
	$td = mcrypt_module_open ("tripledes", '', 'cfb', '');
	$iv_size = mcrypt_enc_get_iv_size ($td);
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
	if (mcrypt_generic_init ($td, $key, $iv) != -1) {
		$decode_text = substr(mdecrypt_generic ($td, base64_decode($text)),$iv_size);
		mcrypt_generic_deinit ($td);
		mcrypt_module_close ($td);
		return $decode_text; 
	} 
}
function hexToStr($hex) {
	$string='';
	for ($i=0; $i < strlen($hex)-1; $i+=2) { $string .= chr(hexdec($hex[$i].$hex[$i+1])); }
	return $string; 
}
?>
