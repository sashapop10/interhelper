<?php
session_start();
$response = ["errors" => [], "success" => []];
$response["success"]["loader"] = true;
include 'connection.php'; 
include 'func.php';
global $connection;
include 'config.php';
$today = date('Y-m-d H:i:s');
$today_date = date('Y-m-d');
$curl_json = ["login" => VARIABLES["login"], "password"=> VARIABLES["password"], "type"=>"", "info" => []]; $host = SERVERPATH . '/admin';
if(isset($_SERVER['HTTP_REFERER'])) $_SESSION['url'] = $_SERVER['HTTP_REFERER'];
$url = explode('/', $_SESSION['url']);
$url_page = $url[count($url) - 1];
if(strrpos($url_page, '?')) $url_page = explode('?', $url_page)[0];
$url_dir = $url[count($url) - 2];
$lol = $_POST;
foreach ($lol as $index => $value){ $lol[$index] = htmlencrypt($lol[$index]); }
// определяем пользователя
if(isset($_SESSION['employee'])){ 
    $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
    $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    if(check_db_info($connection, 'assistents', 'id', $personal_id) == 0 || check_db_info($connection, 'users', 'id', $boss_id) == 0) {
        unset($_SESSION['employee']); 
        array_push($response['errors'], ERRORS['assistent_account_access']);
        $boss_id = null; $personal_id = null;
    } else {
        $personal_database = 'assistents';
        $sql = "UPDATE assistents SET time = '$today' WHERE id = '$personal_id'";
        $connection->query($sql); 
        $sql = "SELECT departament FROM assistents WHERE id = '$personal_id'";
        $employee_departament = attach_sql($connection, $sql, 'row')[0];
    }
} 
if(isset($_SESSION['boss'])){
    $boss_id = $_SESSION['boss']; 
    if($url_dir == 'pages') $personal_id = $_SESSION['boss']; 
    if(check_db_info($connection, 'users', 'id', $boss_id) == 0) {
        unset($_SESSION['employee']); 
        array_push($response['errors'], ERRORS['boss_account_access']);
        $boss_id = null; $personal_id = null;
    } else {
        if($url_dir == 'pages') $personal_database = 'users';
        $sql = "UPDATE users SET time = '$today' WHERE id = '$boss_id'";
        $connection->query($sql); 
        $employee_departament = 'all';
    }
}
if(isset($personal_id) || isset($lol['getSettings'])){ 
    if(isset($personal_id)){
        $sql = "SELECT settings, tariff, domain, money, payday FROM users WHERE id = '$boss_id'";
        $boss_info = attach_sql($connection, $sql, 'row');
        $boss_departaments = json_decode($boss_info[0], JSON_UNESCAPED_UNICODE)['departaments'];
        $boss_settings = json_decode($boss_info[0], JSON_UNESCAPED_UNICODE);
        $boss_tariff = $boss_info[1];
        $boss_domains = json_decode($boss_info[2], JSON_UNESCAPED_UNICODE);
        $boss_money = intval($boss_info[3]);
        $boss_payday = $boss_info[4];
    }
    if(isset($lol['getSettings'])){ // получить данные
        if($lol['getSettings'] == 'get_boss_info' && isset($personal_id)){
            $sql = "SELECT name, email, settings, domain, photo, tariff, money, time FROM users WHERE id = '$boss_id'";
            $boss = attach_sql($connection, $sql, 'query')[0];
            $employee = null;
            if(isset($_SESSION['employee'])){
                $employee_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
                $sql = "SELECT name, email, departament, photo, buttlecry, time FROM assistents WHERE id = '$employee_id'";
                $employee = attach_sql($connection, $sql, 'query')[0];
            }
            $response["success"] = ['boss' => $boss, 'employee' => $employee];
        } elseif($lol['getSettings'] == 'get_boss_info') $response["success"] = ['boss' => null, 'employee' => null];
        elseif($lol['getSettings'] == 'editions') {
            $editions = EDITIONS;
            foreach($editions as $key => $editions_item){
                if($editions_item["cost"]["value"] == 0) $editions[$key]["cost"]["value"] = DESIGNATIONS["1"];
                foreach($editions_item["include"] as $key2 => $item){
                    if($item["value"] == 0){ $editions[$key]["include"][$key2]["value"] = DESIGNATIONS["0"]; }
                } 
                if($editions[$key]["type"] == "hidden") unset($editions[$key]);
            }
            $response["success"] = $editions;
        } elseif($lol['getSettings'] == 'domains') {
            $response['success']['domains'] = $boss_domains['domains'];
            $response['success']['design_domains'] = array_keys($boss_settings['InterHelperOptions']);
        } elseif($lol['getSettings'] == 'fitchas') $response["success"] = TOOLS;
        elseif($lol['getSettings'] == 'reviews') $response["success"] = REVIEWS;
        elseif($lol['getSettings'] == 'problems') $response["success"] = PROBLEMS;
        elseif($lol['getSettings'] == 'news') $response["success"] = NEWS;
        elseif($lol['getSettings'] == 'review' && isset($personal_id)){
            $sql = "SELECT * FROM reviews WHERE review_id = '$boss_id'";
            $review = attach_sql($connection, $sql, 'query');
            if(isset($review)) if(isset($review[0])) $response["success"] = $review[0];
        } elseif($lol['getSettings'] == 'statistic' && isset($personal_id)){
            $sql = "SELECT info, utm FROM statistic WHERE owner_id = '$boss_id'";
            $rows = attach_sql($connection, $sql, 'row');
            $statistic = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
            $utm = json_decode($rows[1], JSON_UNESCAPED_UNICODE);
            $response["success"] = array($statistic, $utm);
        } elseif($lol['getSettings'] == 'anticlicker' && isset($personal_id)){
            $sql = "SELECT info FROM statistic WHERE owner_id = '$boss_id'";
            $statistic = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE)['statistic'];
            $year = explode('-', $today_date)[0]; $months = explode('-', $today_date)[1];
            $adds_banned = 0; $adds_redirected = 0;
            if(array_key_exists($year, $statistic)){
                if(array_key_exists($months, $statistic[$year])){
                    foreach($statistic[$year][$months] as $key => $value){
                        if(array_key_exists("adds_banned", $statistic[$year][$months][$key])) $adds_banned += $statistic[$year][$months][$key]["adds_banned"];
                        if(array_key_exists("adds_redirected", $statistic[$year][$months][$key])) $adds_redirected += $statistic[$year][$months][$key]["adds_redirected"];
                    }
                }
            }
            $curl_json["type"] = "get_adds_users_mas";
            $curl_json["info"] = ["domain" => $boss_id];
            $response['success']['adds_visitors'] = json_decode(send_curl($curl_json, $host), JSON_UNESCAPED_UNICODE)['error'];
            $response['success']['adds_trys'] = $boss_settings['adds']['adds_trys'];
            $response['success']['autoban'] = $boss_settings['adds']['adds_autoban'];
            $response['success']['redirect'] = $boss_settings['adds']['adds_redirect'];
            $response['success']['adds_banned'] = $adds_banned;
            $response['success']['adds_redirected'] = $adds_redirected;
        } elseif($lol['getSettings'] == 'dialogs' && isset($personal_id)){
            $response['success']['rooms'] = json_encode(get_database_rooms($connection, $boss_id), JSON_UNESCAPED_UNICODE);
            $response['success']['assistents'] = get_database_assistents($connection, $boss_id);
            $response['success']['token'] = session_key('boss', $boss_id, VARIABLES, SERVERPATH.'/admin', null);
            $response['success']['dirname'] = SERVERPATH;
        } elseif($lol['getSettings'] == 'dialog' && isset($personal_id)){
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['token'] = session_key('boss', $boss_id, VARIABLES, SERVERPATH.'/admin', null);
            $response['success']['dirname'] = SERVERPATH;
        } elseif($lol['getSettings'] == 'offline' && isset($personal_id)){ // OFFLINE 1
            $response['success']['feedbackENABLED'] = $boss_settings['feedbackform']['feedbackENABLED'];
            $response['success']['feedbackTEXT'] = $boss_settings['feedbackform']['feedbackTEXT'];
            $response['success']['feedbackMAIL'] = $boss_settings['feedbackform']['feedbackMAIL'];
            $response['success']['feedbackformName'] = $boss_settings['feedbackform']['feedbackformName'];
            $response['success']['feedbackformEmail'] = $boss_settings['feedbackform']['feedbackformEmail'];
            $response['success']['feedbackformPhone'] = $boss_settings['feedbackform']['feedbackformPhone'];
        } elseif($lol['getSettings'] == 'feedback' && isset($personal_id)){ // OFFLINE 2
            $response['success']['forms'] = $boss_settings['feedback_form'];
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
        } elseif($lol['getSettings'] == 'departaments' && isset($personal_id)){ 
            $response['success'] = $boss_departaments;
        } elseif(($lol['getSettings'] == 'design' || $lol['getSettings'] == 'options') && isset($personal_id)){ 
            $response['success']['design'] = $boss_settings['InterHelperOptions'];
            $response['success']['domains'] = array_keys($boss_settings['InterHelperOptions']);
        } elseif($lol['getSettings'] == 'tariff' && isset($personal_id)){
            $editions = EDITIONS;
            foreach($editions as $key => $editions_item){
                if($editions_item["cost"]["value"] == 0) $editions[$key]["cost"]["value"] = DESIGNATIONS["1"];
                foreach($editions_item["include"] as $key2 => $item){
                    if($item["value"] == 0){ $editions[$key]["include"][$key2]["value"] = DESIGNATIONS["0"]; }
                } 
                if($editions[$key]["type"] == "hidden") unset($editions[$key]);
            }
            $response['success']['editions'] = $editions;
            $response['success']['departaments'] = count($boss_departaments);
            $response['success']['domains'] = count($boss_domains['domains']);
            $response['success']['money'] = $boss_money;
            $unused = 0; 
            if($boss_payday != 0){ 
                $now_date = date("Y-m-d");
                $now_date_unix = strtotime($now_date);
                $me_date_unix = strtotime($boss_payday);
                $unused = 30 - ($now_date_unix - $me_date_unix) / (60*60*24);
                $unused = $unused * intval(EDITIONS[$boss_tariff]["cost"]["value"]) / 30;
                $boss_payday = "Оплачен до " . date("Y-m-d", strtotime($boss_payday."+1 month"));
            } else $boss_payday = "Не оплачен"; 
            $response['success']['payday'] = $boss_payday;
            $response['success']['unused'] = $unused;
            $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
            $columns = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
            $response['success']['tables'] = count($columns);
            $response['success']['columns'] = 0;
            foreach($columns as $table){ $response['success']['columns'] += count($table); }
            $response['success']['tariff'] = $boss_tariff;
            $curl_json["type"] = "get_statistic";
            $curl_json["info"] = ["domain"=> $boss_id];
            $data = send_curl($curl_json, $host);
            $info = json_decode($data, JSON_UNESCAPED_UNICODE);
            if($info["success"] == true) foreach($info["error"] as $key => $value){ $response['success'][$key] = $value; }
            else array_push($response['errors'], $info["error"]);
        } elseif($lol['getSettings'] == 'autosender' && isset($personal_id)){
            $sql = "SELECT * FROM notifications WHERE owner_id = '$boss_id'";
            $rows = attach_sql($connection, $sql, 'query');
            $notifications = [];
            foreach($rows as $row){
                $notifications[$row['uid']] = [
                    "type" => $row['type'],
                    "conditions" => json_decode($row['conditions'], JSON_UNESCAPED_UNICODE),
                    "sender" => $row['name'],
                    'departament' => $row['departament'],
                    'text' => $row['text'],
                    'photo' => $row['photo'],
                    'name' => $row['notification_name'],
                    'adds' => json_decode($row['adds'], JSON_UNESCAPED_UNICODE),
                    'statistic' => json_decode($row['statistic']),
                ];
            }
            $response['success']['notifications'] = $notifications;
            $response['success']['fastmessages'] = $boss_settings['fastMessages'];
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
        } elseif($lol['getSettings'] == 'swaper' && isset($personal_id)){ 
            $response['success'] = (isset($boss_settings["swap"]) ? $boss_settings["swap"] : []);
        } elseif($lol['getSettings'] == 'assistents' && isset($personal_id)){ 
            $response['success']['departaments'] = $boss_departaments;
            $response['success']['token'] = session_key('boss', $boss_id, VARIABLES, SERVERPATH.'/admin', null);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['domains'] = count($boss_domains['domains']);
        } elseif($lol['getSettings'] == 'assistent' && isset($personal_id)){ 
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $sql = "SELECT departament, buttlecry, email, name, photo FROM assistents WHERE id = '$personal_id'";
            $result = attach_sql($connection, $sql, 'query')[0];
            $response['success']['info'] = $result;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
        } elseif($lol['getSettings'] == 'crm' && isset($personal_id)){
            $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
            $tables = attach_sql($connection, $sql, 'row')[0];
            $tables = json_decode($tables, JSON_UNESCAPED_UNICODE);
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['tables'] = $tables;
            $response['success']['today'] = $today_date;
            $response['success']['local_date'] = date("Y-m-d\TH:i");
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['mailer'] = $boss_settings['mailer'];
            $response['success']['fastmessages'] = $boss_settings['fastMessages'];
            $response['success']['domains'] = array_keys($boss_settings['InterHelperOptions']);
            if(isset($_POST['crm_type'])){
                $crm_type = $_POST['crm_type'];
                $curl_json["type"] = "change_crm_item";
                $curl_json["info"] = ["assistent_id"=> $personal_id, "domain" => $boss_id, "setting"=> "get_tasks"];
                $data = send_curl($curl_json, $host);
                $info = json_decode($data, JSON_UNESCAPED_UNICODE);
                if($info["success"] == true) $response['success']['tasks'] = $info["error"];    
                else array_push($response["errors"], $info['error']);
                $curl_json["type"] = "change_crm_item";
                $curl_json["info"] = ["domain" => $boss_id, "setting"=> "get_crm", "table" => $crm_type];
                $data = send_curl($curl_json, $host);
                $info = json_decode($data, JSON_UNESCAPED_UNICODE);
                if($info["success"] == true) $response['success']['crm'] = $info["error"];   
                else array_push($response["errors"], $info['error']);
            } else $response['success']['crm'] = [];
        } elseif($lol['getSettings'] == 'tasks' && isset($personal_id)){
            $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
            $tables = attach_sql($connection, $sql, 'row')[0];
            $tables = json_decode($tables, JSON_UNESCAPED_UNICODE);
            $response['success']['tables'] = $tables;
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $curl_json["type"] = "change_crm_item";
            $curl_json["info"] = ["assistent_id"=> $personal_id, "domain" => $boss_id, "setting"=> "get_tasks"];
            $data = send_curl($curl_json, $host);
            $info = json_decode($data, JSON_UNESCAPED_UNICODE);
            if($info["success"] == true) $response['success']['tasks'] = $info["error"];    
            else array_push($response["errors"], $info['error']);
            $curl_json["type"] = "change_crm_item";
            $curl_json["info"] = ["domain" => $boss_id, "setting"=> "get_crm", "table" => 'all'];
            $data = send_curl($curl_json, $host);
            $info = json_decode($data, JSON_UNESCAPED_UNICODE);
            if($info["success"] == true) $response['success']['items'] = $info["error"];   
            else array_push($response["errors"], $info['error']);
        } elseif($lol['getSettings'] == 'crm_settings' && isset($_POST['crm_type']) && isset($personal_id)){
            $crm_type = $_POST['crm_type'];
            $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
            $columns = attach_sql($connection, $sql, 'row')[0];
            $columns = json_decode($columns, JSON_UNESCAPED_UNICODE);
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['columns'] = $columns;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['max_count'] = EDITIONS[$boss_tariff]['include']['variants']['value'];
        } elseif($lol['getSettings'] == 'hub' && isset($personal_id)){
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['domains'] = $boss_domains['domains'];
            $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
            $response['success']['tables'] = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
            $response['success']['fastmessages'] = $boss_settings['fastMessages'];
            $response['success']['personal_id'] = $personal_id;
            $sql = "SELECT name, photo, id FROM assistents WHERE domain = '$boss_id'";
            $rows = attach_sql($connection, $sql, 'query');
            $assistents = [];
            foreach ($rows as $row) { 
                $assistents[$row["id"]] = [
                    "name"=> $row["name"], 
                    "photo"=> $row["photo"] 
                ]; 
            }
            $response['success']['assistents'] = $assistents;
            $curl_json["type"] = "change_crm_item";
            $curl_json["info"] = ["assistent_id"=> $personal_id, "domain" => $boss_id, "setting"=> "get_tasks"];
            $data = send_curl($curl_json, $host);
            $info = json_decode($data, JSON_UNESCAPED_UNICODE);
            if($info["success"] == true) $response['success']['tasks'] = $info["error"]; 
        } elseif($lol['getSettings'] == 'chat' && isset($_POST['room']) && isset($personal_id)){
            $room = $_POST['room'];
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['fastmessages'] = $boss_settings['fastMessages'];
            $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
            $response['success']['tables'] = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
            $sql = "SELECT notes, properties FROM rooms WHERE room = '$room'"; 
            $rows = attach_sql($connection, $sql, 'row');
            if(isset($rows)){ 
                $response['success']['notes'] = $rows[0]; 
                $response['success']['properties'] = $rows[1]; 
            }
            $sql = "SELECT buttlecry FROM assistents WHERE id = '$personal_id'";
            $response['success']['buttlecry'] = attach_sql($connection, $sql, 'row')[0];
            $curl_json["type"] = "change_crm_item";
            $curl_json["info"] = ["assistent_id"=> $personal_id, "domain" => $boss_id, "setting"=> "get_tasks"];
            $data = send_curl($curl_json, $host);
            $info = json_decode($data, JSON_UNESCAPED_UNICODE);
            if($info["success"] == true) $response['success']['tasks'] = $info["error"]; 
        } elseif($lol['getSettings'] == 'forms' && isset($personal_id)){
            $sql = " SELECT 
                offline_forms.name, offline_forms.uid, offline_forms.email, offline_forms.phone, offline_forms.message, rooms.room, banned.room_id, offline_forms.time
                        FROM offline_forms  LEFT JOIN rooms ON offline_forms.sender = rooms.id LEFT JOIN banned ON offline_forms.sender = banned.id
                    WHERE offline_forms.owner_id = '$boss_id';
            ";
            $rows = attach_sql($connection, $sql, 'query');
            $forms = [];
            foreach ($rows as $row) { 
                $max_date = 0;
                if(!isset($row["room"])) $row["room"] = $row["room_id"];
                if(!isset($forms[$row["room"]])) $forms[$row["room"]] = [];
                $time = $row["time"];
                $time_date = strtotime($time);
                if(!isset($forms[$row["room"]][$time_date])) $forms[$row["room"]][$time_date] = [];
                $time_local = explode(' ', $time)[1];
                $forms[$row["room"]][$time_date][$row["uid"]] = [
                    "phone" => $row["phone"],
                    "message" => $row["message"],
                    "name" => $row["name"],
                    "email" => $row["email"],
                    "time" => $time_local,
                ];
            }
            foreach ($forms as $key => $form){
                krsort($forms[$key]);
                $forms[$key]["index"] = $key; 
            }
            function sort_date($a, $b) {
                return array_key_first($b) - array_key_first($a);
            }
            usort($forms, "sort_date");
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['forms'] = $forms;
            $sql = "SELECT name, photo, id FROM assistents WHERE domain = '$boss_id'";
            $rows = attach_sql($connection, $sql, 'query');
            $assistents = [];
            foreach ($rows as $row) { 
                $assistents[$row["id"]] = [
                    "name"=> $row["name"], 
                    "photo"=> $row["photo"] 
                ]; 
            }
            $response['success']['assistents'] = $assistents;
        } elseif($lol['getSettings'] == 'banned' && isset($personal_id)){
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $sql = "SELECT name, photo, id FROM assistents WHERE domain = '$boss_id'";
            $rows = attach_sql($connection, $sql, 'query');
            $assistents = [];
            foreach ($rows as $row) { 
                $assistents[$row["id"]] = [
                    "name"=> $row["name"], 
                    "photo"=> $row["photo"] 
                ]; 
            }
            $response['success']['assistents'] = $assistents;
        } elseif($lol['getSettings'] == 'banned_chat' && isset($personal_id)){
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
        } elseif($lol['getSettings'] == 'command' && isset($personal_id)){
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['personal_id'] = $personal_id;
        } elseif($lol['getSettings'] == 'command_chat' && isset($_POST['room']) && isset($personal_id)){
            if($_POST['room'] != $boss_id){
                $room = explode('!@!@2@!@!', $_POST['room']);
                if($room[0] == $personal_id) $oponent_id = $room[1];
                else $oponent_id = $room[0];
                $sql = "SELECT email, departament, name FROM assistents WHERE domain = '$boss_id' AND id = '$oponent_id'"; 
                $rows = attach_sql($connection, $sql, 'row');
                $response['success']['oponent_email'] = (isset($rows[0]) ? $rows[0] : null);
                $response['success']['oponent_departament'] = (isset($rows[1]) ? $rows[1] : null);
                $response['success']['oponent_name'] = (isset($rows[2]) ? $rows[2] : null);
            } else {
                $response['success']['oponent_email'] = null;
                $response['success']['oponent_departament'] = null;
                $response['success']['oponent_name'] = null;
            }
            $response['success']['token'] = session_key('assistent', $boss_id, VARIABLES, SERVERPATH.'/admin', $personal_id);
            $response['success']['dirname'] = SERVERPATH;
            $response['success']['emojis'] = emojis($_SERVER['DOCUMENT_ROOT']);
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['personal_id'] = $personal_id;
            $response['success']['boss_id'] = $boss_id;
            $response['success']['fastmessages'] = $boss_settings['fastMessages'];
        } elseif($lol['getSettings'] == 'mailer' && isset($personal_id)){
            $response['success']['domains'] = array_keys($boss_settings['InterHelperOptions']);
            foreach($boss_settings['mailer'] AS $key => $value){
                $encoded = $boss_settings['mailer'][$key]['SMTPpassword'];
                if(isset($encoded) && $encoded != '') $boss_settings['mailer'][$key]['SMTPpassword'] = __decode(hexToStr($encoded),'#_sashapop10_#'.$boss_id);
            }
            $response['success']['mailer'] = $boss_settings['mailer'];
            $response['success']['regexp'] = VARIABLES['photos']['assistent_send_photo']['accepted_file_types'];
            $response['success']['fastmessages'] = $boss_settings['fastMessages'];
        } 
    } elseif( // личные данные (АССИСТЕНТ / БОСС)
        (isset($lol['personal_info_value']) && isset($lol['personal_info_column'])) || isset($_FILES['profile_photo'])
        ){ 
        if(isset($_FILES['profile_photo'])){
            $photo = $_FILES['profile_photo'];
            $data = save_file($photo, ($personal_database == 'users' ? 'boss_profile_photo' : 'assistent_profile_photo'), $personal_id, $connection, $boss_id);
            if($data["access"] == false) array_push($response['errors'], $data["text"]);
            else $response["success"]["photo"] = $data["text"];
        } else {
            $accepted_type = array('buttlecry', 'name', 'password', 'email');
            $type = $lol['personal_info_column'];
            $value = $_POST['personal_info_value'];
            if(array_search($type, $accepted_type) != -1){
                if($type == 'email'){
                    if(!filter_var($value, FILTER_VALIDATE_EMAIL)) array_push($response['errors'], ERRORS['uncorrect_new_email']);
                    if(strlen(trim($value)) < 3 || strlen(trim($value)) > 40) array_push($response['errors'], ERRORS['invalid_new_email']);
                    if(count($response['errors']) == 0){
                        $sql = "SELECT count(1) FROM $personal_database WHERE email='$value' AND id != '$personal_id'";
                        if(attach_sql($connection, $sql, 'row')[0] > 0) array_push($response['errors'], ERRORS['email_already_exist']);
                    }
                    $value = strtolower($value);
                } elseif($type == 'password'){
                    $value = json_decode($value, JSON_UNESCAPED_UNICODE);
                    $old_pass = $value['old']; $new_pass = $value['new']; $new_pass_repeat = $value['repeat'];
                    $sql = "SELECT password FROM $personal_database WHERE id = '$personal_id'"; 
                    $previous_pass = attach_sql($connection, $sql, 'row')[0];
                    if(strlen(trim($new_pass)) < 7 or strlen(trim($new_pass)) > 30) array_push($response['errors'], ERRORS['uncorrect_new_pass']); 
                    if($new_pass != $new_pass_repeat) array_push($response['errors'], ERRORS['wrong_new_pass_repeat']);
                    if(!password_verify($old_pass, $previous_pass)) array_push($response['errors'], ERRORS['wrong_old_pass']);
                    $value = password_hash($new_pass, PASSWORD_BCRYPT);
                }
                if(count($response['errors']) == 0){ 
                    if($type != 'password' && $type != 'email') $value = $lol['personal_info_value'];
                    $sql = "UPDATE $personal_database SET $type = '$value' WHERE id='$personal_id'";
                    if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']); 
                    else if($personal_database == 'assistents' && $type != 'password'){
                        $curl_json["type"] = "change_assistent";
                        $curl_json["info"] = ["assistent_id"=> $personal_id, "domain" => $boss_id, "setting"=> $type, "value" => $value];
                        send_curl($curl_json, $host);
                    } else if($type == 'password') $response['success']['response'] = 'Вы сменили пароль !';
                }
            } else array_push($response['errors'], ERRORS['error']); 
        } 
    } elseif( // Подмены (АССИСТЕНТ / БОСС)
        ((isset($lol['swap_from']) && isset($lol['swap_to']) && isset($lol['swap_id']) && isset($lol['swap_type'])) ||
        (
            (isset($lol['main_condition']) && isset($lol['second_condition']) && isset($lol['type']) && isset($lol['swap_id']) && isset($lol['uid'])) || 
            (isset($lol['type']) && isset($lol['swap_id']))
        ) ||
        (isset($lol["swap_time"]) && isset($lol['swap_id'])) ||
        isset($lol['remove_swap']) || isset($lol['remove_condition']) || isset($lol['swap_cache']) || 
        (isset($lol["swap_condition"]) && isset($lol['condition_type']) && isset($lol['condition_id'])) ||
        (isset($lol["swap_number"]) && isset($lol['swap_type'])) || isset($lol['swap_changename']) ||
        (isset($lol['swap_utmpart']) && isset($lol['swap_id']) && isset($lol['part_id'])) || 
        (isset($lol['utm_part_remove_id']) && isset($lol['swap_id'])) ||
        (isset($lol['utm_part']) && isset($lol['utm_part_id']) && isset($lol['swap_id'])) ||
        (isset($lol['utm_part_id']) && isset($lol['swap_id']) && isset($lol['utm_inner_part_remove_id'])) ||
        (isset($lol['discard_part']) && isset($lol['swap_id']))) && (isset($_SESSION['boss']) || in_array('swap', $boss_departaments[$employee_departament]))
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('swaper', $boss_departaments[$employee_departament]) : false))
        ){
        $sql = "SELECT settings FROM users WHERE id='$boss_id'";
        $settings = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        if(isset($lol['swap_id'])) $swap_id = $_POST['swap_id'];
        if(isset($lol['swap_from']) && isset($lol['swap_to']) && isset($lol['swap_id']) && isset($lol['swap_type'])){ // создать подмену
            if(count((isset($boss_settings['swap']) ? $boss_settings['swap'] : [])) < EDITIONS[$boss_tariff]["include"]["swaper"]["value"]){
                $swap_type = $_POST['swap_type'];
                $swap_from = $lol['swap_from'];
                $swap_to = $lol['swap_to'];
                if(!array_key_exists('swap', $settings)) $settings['swap'] = [];
                $settings['swap'][$swap_id] = [
                    "swap_to" => $swap_to,
                    "swap_from" => $swap_from,
                    "swap_time" => "always",
                    "swap_cache" => false,
                    "swap_type" => $swap_type,
                    "swap_changename" => true
                ];
                $response['success']['create_swap'] = true;
            } else array_push($response['errors'], ERRORS['limit']);
        } elseif(
            (isset($lol['main_condition']) && isset($lol['second_condition']) && isset($lol['type']) && isset($lol['swap_id']) && isset($lol['uid'])) || 
            (isset($lol['type']) && isset($lol['swap_id']))
        ){ // создать условие
            if(!array_key_exists("swap_if", $settings["swap"][$swap_id])) $settings["swap"][$swap_id]["swap_if"] = [];
            if(isset($lol['main_condition']) && isset($lol['second_condition']) && isset($lol['type']) && isset($lol['swap_id']) && isset($lol['uid'])){
                $settings["swap"][$swap_id]["swap_if"][$lol['uid']] = [
                    "main" => $lol['main_condition'],
                    "second" => $lol['second_condition'],
                    "type" => $lol['type'],
                ];
            } else {
                $settings["swap"][$swap_id]["swap_if"][$lol['type']] = $lol['type'];
                if(isset($_POST['uncheck'])){
                    if(array_key_exists($_POST['uncheck'], $settings["swap"][$swap_id]["swap_if"])) unset($settings["swap"][$swap_id]["swap_if"][array_search($_POST['uncheck'], $settings["swap"][$swap_id]["swap_if"])]);
                }
            }
        } elseif(isset($lol["swap_time"]) && isset($lol['swap_id'])){ // условие подмены время
            $swap_time = $_POST['swap_time'];
            $settings['swap'][$swap_id]["swap_time"] = $swap_time;
        } elseif(isset($lol['remove_swap'])){ // удалить подмену
            $swap_id = $_POST['remove_swap'];
            unset($settings['swap'][$swap_id]);
        } elseif(isset($lol['remove_condition']) && isset($lol['swap_id'])){ // удалить условие
            $condition_id = $_POST['remove_condition'];
            unset($settings['swap'][$swap_id]["swap_if"][$condition_id]);
        } elseif(isset($lol['swap_cache'])) { // кэширование
            $swap_id = $_POST['swap_cache'];
            $settings['swap'][$swap_id]['swap_cache'] = !$settings['swap'][$swap_id]['swap_cache'];
        } elseif(isset($lol['swap_changename'])) { // смена имени
            $swap_id = $_POST['swap_changename'];
            $settings['swap'][$swap_id]['swap_changename'] = !$settings['swap'][$swap_id]['swap_changename'];
        } elseif(isset($lol["swap_number"]) && isset($lol['swap_type'])){ // менять номера и название 
            $number = $lol['swap_number'];
            $type = $_POST['swap_type'];
            $settings['swap'][$swap_id][$type] = $number;
        } elseif(isset($lol['swap_utmpart']) && isset($lol['swap_id']) && isset($lol['part_id'])){ // utm parts add
            if(isset($settings["swap"][$swap_id]["swap_utmparts"]) ? count($settings["swap"][$swap_id]["swap_utmparts"]) <= 15 : true){
                $value = $lol['swap_utmpart'];
                $part_id = $_POST['part_id'];
                if(!array_key_exists("swap_utmparts", $settings["swap"][$swap_id])) $settings["swap"][$swap_id]["swap_utmparts"] = [];
                $settings['swap'][$swap_id]["swap_utmparts"][$part_id] = ["utm_part_name" => $value];
            } else array_push($response['errors'], ERRORS['limit']);
        } elseif(isset($lol['utm_part_remove_id'])){ // utm parts remove
            $utm_part_remove_id = $_POST['utm_part_remove_id'];
            unset($settings['swap'][$swap_id]["swap_utmparts"][$utm_part_remove_id]);
        } elseif(isset($lol['utm_part']) && isset($lol['utm_part_id']) && isset($lol['swap_id'])){ // менять часть метки
            $value = $lol['utm_part'];
            $utm_part_id = $_POST['utm_part_id'];
            $settings['swap'][$swap_id]["swap_utmparts"][$utm_part_id]["utm_part_name"] = $value;
        } elseif(isset($lol['utm_part_id']) && isset($lol['swap_id']) && isset($lol['utm_inner_part_remove_id'])){
            $utm_inner_part_remove_id = $_POST['utm_inner_part_remove_id'];
            $utm_part_id = $_POST['utm_part_id'];
            unset($settings['swap'][$swap_id]["swap_utmparts"][$utm_part_id]['results'][$utm_inner_part_remove_id]);
        } elseif(isset($lol['discard_part']) && isset($lol['swap_id'])){ // сброс наденных
            $discard_part = $_POST['discard_part'];
            unset($settings['swap'][$swap_id]["swap_utmparts"][$discard_part]['results']);
        } 
        $settings = json_encode($settings, JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE users SET settings = '$settings' WHERE id='$boss_id'";
        if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']); 
    } elseif( // Домены (АССИСТЕНТ / БОСС)
        (isset($lol['domain']) || isset($lol['remove_domain']))
         && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('domains', $boss_departaments[$employee_departament]) : false))
        ){ 
        if(isset($lol['domain'])){
            $new_domain = trim(strval($lol['domain']));
            if(strlen($new_domain) == 0) array_push($response['errors'], ERRORS['domain_not_exist']);
            if(count($boss_domains["domains"]) >= intval(EDITIONS[$boss_tariff]["include"]["domains"]["value"]) && EDITIONS[$boss_tariff]["include"]["domains"]["value"] != 0) array_push($response['errors'], ERRORS['domains_limit']);
            if(!filter_var(gethostbyname($new_domain), FILTER_VALIDATE_IP)) array_push($response['errors'], ERRORS['domain_not_exist']);
            $sql = "SELECT count(1) FROM users WHERE domain LIKE '%\"$new_domain\"%'";
            if(attach_sql($connection, $sql, 'row')[0] > 0) array_push($response['errors'], ERRORS['domain_already_exist']);
            if(count($response['errors']) == 0){
                array_push($boss_domains["domains"], $new_domain);
                $array_key = array_search($new_domain, $boss_domains['domains']);
                $boss_domains = json_encode($boss_domains, JSON_UNESCAPED_UNICODE);
                $sql = "UPDATE users SET domain = '$boss_domains' WHERE id='$boss_id'";
                if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                $response['success']["new_domain_key"] = $array_key;
                $response['success']['loader'] = true;
            }
        } else {
            $remove_domain_key = strval($lol['remove_domain']);
            unset($boss_domains["domains"][$remove_domain_key]); 
            $boss_domains = json_encode($boss_domains, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE users SET domain = '$boss_domains' WHERE id='$boss_id'";
            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
        }
    } elseif( // Отделы (АССИСТЕНТ / БОСС)
        (isset($lol['departament_add']) || isset($lol['departament_remove']) || isset($lol['departament_update']) || isset($_POST['departament_name_update']))
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('departaments', $boss_departaments[$employee_departament]) : false))
        ){ 
        if(isset($lol['departament_add'])){
            $new_departament = $_POST['departament_add'];
            $new_departament = preg_replace('/[^ a-zа-яё\d]/ui', '',$new_departament);
            if(str_replace(' ', '', $new_departament) == '') array_push($response['errors'], 'В названии отдела могут быть только буквы и цифры !');
            if(strlen($new_departament) > VARIABLES["departamentlen"]) array_push($response['errors'], ERRORS['alot_letters']);
            if(count($boss_departaments) > intval(EDITIONS[$boss_tariff]["include"]["departaments"]["value"]) && EDITIONS[$boss_tariff]["include"]["departaments"]["value"] != 0) array_push($response['errors'], ERRORS['limit']);
            if(array_key_exists($new_departament, $boss_departaments)) array_push($response['errors'], ERRORS['departament_already_exist']);
            if(count($response['errors']) == 0){    
                $boss_settings['departaments'][$new_departament] = array("crm", "forms", "hub", "command", "banned");
                $json = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
                $sql = "UPDATE users SET settings = '$json' WHERE id = '$boss_id'";
                if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                $response['success']['new_departament_key'] = $new_departament;
                $response['success']['new_departament_value'] = array("crm", "forms", "hub", "command", "banned");
            }
        } elseif(isset($lol['departament_remove'])) {
            $remove_departament_key = $lol['departament_remove'];
            unset($boss_settings['departaments'][$remove_departament_key]);
            $json = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE users SET settings = '$json' WHERE id = '$boss_id'";
            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
        } elseif(isset($_POST['departament_name_update'])){
            $info = json_decode($_POST['departament_name_update'], JSON_UNESCAPED_UNICODE);
            $prev = $info['prev'];
            $new_departament = preg_replace('/[^ a-zа-яё\d]/ui', '', $info['new']);
            if(str_replace(' ', '', $new_departament) == '') array_push($response['errors'], 'В названии отдела могут быть только буквы и цифры !');
            if(strlen($new_departament) > VARIABLES["departamentlen"]) array_push($response['errors'], ERRORS['alot_letters']);
            if(array_key_exists($new_departament, $boss_departaments)) array_push($response['errors'], ERRORS['departament_already_exist']);
            if(!array_key_exists($prev, $boss_departaments)) array_push($response['errors'], 'Отдел не существует !');
            if(count($response['errors']) == 0){    
                $boss_settings['departaments'][$new_departament] = $boss_settings['departaments'][$prev];
                unset($boss_settings['departaments'][$prev]);
                $json = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
                $sql = "UPDATE users SET settings = '$json' WHERE id = '$boss_id'";
                if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                else {
                    $sql = "UPDATE assistents SET departament = '$new_departament' WHERE departament = '$prev' AND domain = '$boss_id'";
                    if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                    else {
                        $curl_json["type"] = "change_departament";
                        $curl_json["info"] = ["domain" => $boss_id, "from" => $prev, "to" => $new_departament];
                        send_curl($curl_json, $host);
                    }
                }
            }
        } else {
            $info = json_decode($_POST['departament_update'], JSON_UNESCAPED_UNICODE);
            $departament = $info['departament'];
            $inner = $info['inner'];
            $status = $info['status'];
            if($status) array_push($boss_settings['departaments'][$departament], $inner);
            else unset($boss_settings['departaments'][$departament][array_search($inner, $boss_settings['departaments'][$departament])]);
            $json = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE users SET settings = '$json' WHERE id = '$boss_id'";
            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
        }
    } elseif( // настройки хелпера (АССИСТЕНТ / БОСС)
        ((isset($lol['chat_status_checkbox']) || isset($lol['PersonalSize']) || isset($_POST['personal_sizes']) ||
        isset($lol['InterHelper_button_position']) || isset($lol['notification_graphic_checkbox']) || 
        isset($lol['notification_audio_checkbox']) || isset($lol['feedback_form_checkbox']) ||
        isset($lol['feedback_text']) || isset($lol['feedback_target_email']) ||
        isset($lol['feedback_input_checkbox_1']) || isset($lol['feedback_input_checkbox_2']) ||
        isset($lol['feedback_input_checkbox_3']) || isset($lol['sys_name']) || isset($lol['SYSname_offline']) ||
        isset($lol['msgs_email']) || isset($lol['email_msgs_status'])) ||
        (isset($lol['interhelper_design_option_name']) && isset($lol['interhelper_design_option_val'])) ||
        isset($lol['helper_fmessage']) || isset($lol['adds'])) && 
        (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? (in_array('options', $boss_departaments[$employee_departament]) || in_array('design', $boss_departaments[$employee_departament]) || in_array('offline', $boss_departaments[$employee_departament]) || in_array('anticlicker', $boss_departaments[$employee_departament])) : false))
        ){
        if(isset($_POST['design_domain'])) $design_domain = $_POST['design_domain'];
        else $design_domain = 'deffault';
        if(isset($lol['chat_status_checkbox']) || isset($lol['PersonalSize'])){ // статус чата и статус персональных размеров
            if(isset($lol['chat_status_checkbox'])) $type = 'chat_status_checkbox'; 
            elseif(isset($lol['PersonalSize'])) $type = 'PersonalSize';
            $boss_settings['InterHelperOptions'][$design_domain][$type] = ($boss_settings['InterHelperOptions'][$design_domain][$type] == 'checked' ? 'uncheked' : 'checked');
        } elseif(isset($_POST['personal_sizes'])) { // персональные размеры
            $array = json_decode($_POST['personal_sizes'], JSON_UNESCAPED_UNICODE);
            $type = $array["type"]; $change = $array["value"];
            $boss_settings['InterHelperOptions'][$design_domain][$type] = $change;
        } elseif(isset($lol['InterHelper_button_position'])){ // расположение кнопки
            $change_position_for = $lol['InterHelper_button_position'];
            if(!isset($_POST['mobile'])) $boss_settings['InterHelperOptions'][$design_domain]['position_type'] = $change_position_for;
            else $boss_settings['InterHelperOptions'][$design_domain]['mobile_position_type'] = $change_position_for;
        } elseif(isset($lol['notification_graphic_checkbox']) || isset($lol['notification_audio_checkbox'])){ // статус уведомлений
            if(isset($lol['notification_graphic_checkbox'])) $type = 'graphic_invite_status';
            else $type = 'audio_invite_status';
            $boss_settings['InterHelperOptions'][$design_domain]['InterHelperInvitesOptions'][$type] = ($boss_settings['InterHelperOptions'][$design_domain]['InterHelperInvitesOptions'][$type] == 'checked' ? 'uncheked' : 'checked');
        } elseif(isset($lol['feedback_form_checkbox'])){ // оффлайн форма
            $boss_settings['feedbackform']['feedbackENABLED'] = ($boss_settings['feedbackform']['feedbackENABLED'] == 'checked' ? 'unchecked' : 'checked'); 
        } elseif(isset($lol['feedback_text'])){ // Сообщение с оффлайн формой
            $boss_settings['feedbackform']['feedbackTEXT'] = $lol['feedback_text'];
        } elseif(isset($lol['feedback_target_email'])){ // почта для формы
            $new_email = $_POST['feedback_target_email'];
            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL) && $new_email != '') array_push($response['errors'], ERRORS['uncorrect_new_email']);
            else $boss_settings['feedbackform']['feedbackMAIL'] = $new_email;
        } elseif(isset($lol['feedback_input_checkbox_1']) || isset($lol['feedback_input_checkbox_2']) || isset($lol['feedback_input_checkbox_3'])){ // поля формы
            if(isset($lol['feedback_input_checkbox_1'])) $type = 'feedbackformName';
            elseif(isset($lol['feedback_input_checkbox_2'])) $type = 'feedbackformPhone';
            else $type = 'feedbackformEmail'; 
            $boss_settings['feedbackform'][$type] = ($boss_settings['feedbackform'][$type] == 'checked' ? 'unchecked' : 'checked'); 
        } elseif(isset($lol['sys_name'])){
            $inviteSysName = $lol['sys_name'];
            $boss_settings['InterHelperOptions'][$design_domain]['SYSname'] = $inviteSysName;
            if(strlen($inviteSysName) > VARIABLES["sysnamelen"]) array_push($response['errors'], ERRORS['alot_letters']);
        } elseif(isset($lol['SYSname_offline'])){
            $inviteSysName = $lol['SYSname_offline'];
            $boss_settings['InterHelperOptions'][$design_domain]['SYSname_offline'] = $inviteSysName;
            if(strlen($inviteSysName) > VARIABLES["sysnamelen"]) array_push($response['errors'], ERRORS['alot_letters']);
        } elseif(isset($lol['interhelper_design_option_name']) && isset($lol['interhelper_design_option_val'])){ // цвета
            $design_option = $lol['interhelper_design_option_name']; $newbgcolor = $lol['interhelper_design_option_val'];
            $boss_settings['InterHelperOptions'][$design_domain][$design_option] = $newbgcolor;
        } else if(isset($lol['helper_fmessage']) && trim($lol['helper_fmessage']) != ''){ // первое сообщение
            $fmessage = $lol['helper_fmessage']; $boss_settings["InterHelperOptions"][$design_domain]["SYSFmessage"] = $fmessage;
            if(strlen($fmessage) > VARIABLES["fmessagelen"]) array_push($response['errors'], ERRORS['alot_letters']);
        } else if(isset($lol['adds'])){ // антисклик
            $mas = json_decode($_POST['adds'], JSON_UNESCAPED_UNICODE);
            $name = $mas['name'];
            if($name == 'adds_redirect' || $name == 'adds_autoban'){
                if($boss_settings["adds"][$name] == 'checked') $boss_settings["adds"][$name] = 'unchecked';
                else {
                    $boss_settings["adds"][$name] = 'checked';
                    if($name == 'adds_redirect') $boss_settings["adds"]['adds_autoban'] = 'unchecked';
                    else $boss_settings["adds"]['adds_redirect'] = 'unchecked';
                }		
            } else $boss_settings["adds"][$name] = $mas['value'];
        } else if(isset($lol['msgs_email'])) { // почта для собщений посетителя
            $new_email = $_POST['msgs_email'];
            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL) && $new_email != '') array_push($response['errors'], ERRORS['uncorrect_new_email']);
            else $boss_settings['InterHelperOptions'][$design_domain]['msgs_email'] = $new_email;
        } elseif(isset($lol['email_msgs_status'])){ // вкл / выкл сообщения посетителя на почту
            $boss_settings['InterHelperOptions'][$design_domain]['email_msgs_status'] = ($boss_settings['InterHelperOptions'][$design_domain]['email_msgs_status'] == 'checked' ? 'unchecked' : 'checked');
        }
        if(count($response['errors']) == 0){
            $json = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE users SET settings = '$json' WHERE id = '$boss_id'";
            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
        }
    } elseif( // рассылка (АССИСТЕНТ / БОСС)
        ((isset($_POST['notification_type']) && isset($_POST['notification_id'])) || 
        (isset($_FILES['notification_photo']) && isset($_POST['notification_departament']) &&
        isset($_POST['notification_sender']) && isset($_POST['notification_text']) &&
        isset($_POST['notification_conditions']) && isset($_POST['notification_type'])) &&
        (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('autosender', $boss_departaments[$employee_departament]) : false)))
        ){ 
        if(isset($_POST['notification_type']) && isset($_POST['notification_id'])){
            $type = $_POST['notification_type'];
            $notification_id = $_POST['notification_id'];
            if(isset($_POST['notification_value'])) $value = $_POST['notification_value'];
            if($type == 'remove_condition' || $type == 'add_condition'){
                if($type == 'add_condition') $value = json_decode($value, JSON_UNESCAPED_UNICODE);
                $sql = "SELECT conditions FROM notifications WHERE uid = '$notification_id'";
                $json = attach_sql($connection, $sql, 'row')[0];
                $json_array = json_decode($json, JSON_UNESCAPED_UNICODE); 
                if($type == 'remove_condition') unset($json_array[$value]);
                elseif(!array_key_exists('main', $value)){
                    $json_array[$value['type']] = $value['type'];
                    if(array_key_exists('uncheck', $value)){
                        if(array_key_exists($value['uncheck'], $json_array)) unset($json_array[array_search($value['uncheck'], $json_array)]);
                    }
                } else {
                    $json_array[$value['uid']] = $value;
                    unset($json_array[$value['uid']]["uid"]);
                }
                $json = json_encode($json_array, JSON_UNESCAPED_UNICODE); 
                $sql = "UPDATE notifications SET conditions = '$json' WHERE uid = '$notification_id' and owner_id = '$boss_id'";
                if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
            } elseif($type == 'photo') {
                $photo = $_FILES['notification_photo'];
                $data = save_file($photo, "notification_photos", $notification_id, $connection, $boss_id);
                $notification_photo = $data['text'];
                if($data["access"] == false) array_push($response['errors'], $data["text"]);
                else $response['success'] = ["status" => true, 'notification_photo' => $data['text'], "notification_id" => $notification_id];
            } elseif($type == 'remove'){
                $sql = "SELECT adds FROM notifications WHERE uid = '$notification_id'";
                $rows = attach_sql($connection, $sql, 'row');
                $adds = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
                foreach($adds as $add){
                    remove_file('notification_adds', null, null, null, $add);
                }
                remove_file('notification_photos', 'notifications', 'uid', $connection, $notification_id);
                $sql = "DELETE FROM notifications WHERE uid = '$notification_id' and owner_id = '$boss_id'";
                if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
            } elseif($type == 'remove_add') {
                $sql = "SELECT adds FROM notifications WHERE uid = '$notification_id'";
                $json = attach_sql($connection, $sql, 'row')[0];
                $json_array = json_decode($json, JSON_UNESCAPED_UNICODE); 
                if(isset($json_array[$value])){
                    $photo = $json_array[$value];
                    unset($json_array[$value]);
                    $json = json_encode($json_array, JSON_UNESCAPED_UNICODE); 
                    remove_file('notification_adds', null, null, null, $photo);
                    $sql = "UPDATE notifications SET adds = '$json' WHERE uid = '$notification_id' and owner_id = '$boss_id'";
                    if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
                }
            } elseif($type == 'adds'){
                if(isset($_FILES['notiffication_add0'])){
                    $sql = "SELECT adds FROM notifications WHERE uid = '$notification_id'";
                    $json = attach_sql($connection, $sql, 'row')[0];
                    $json_array = json_decode($json, JSON_UNESCAPED_UNICODE); 
                    $file_status = false;
                    foreach($_FILES as $file){
                        $filename = $file['name'];
                        $allowed_filetypes = VARIABLES["photos"]["notification_adds"]["accepted_types"];
                        $max_filesize = VARIABLES["photos"]["notification_adds"]["max_weight"];
                        $upload_path = $_SERVER['DOCUMENT_ROOT'].VARIABLES["photos"]["notification_adds"]["upload_path"];
                        $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
                        if(!in_array(strtolower($ext), $allowed_filetypes)){ array_push($response['errors'], ERRORS['not_accepted_file'] . $filename); continue; }
                        if(filesize($file['tmp_name']) > $max_filesize){ array_push($response['errors'], ERRORS['so_big_file'] . $filename); continue; }
                        if(!is_writable($upload_path)){ array_push($response['errors'], ERRORs['777_error']); continue; }
                        $filename = uniqid().uniqid() . $ext;
                        if(move_uploaded_file($file['tmp_name'], $upload_path . $filename)){ 
                            $file_status = true;
                            array_push($json_array, $filename);
                        } else array_push($response['errors'], ERRORS['file_load_error']);
                    }
                    if($file_status){ 
                        $json = json_encode($json_array, JSON_UNESCAPED_UNICODE); 
                        $sql = "UPDATE notifications SET adds = '$json' WHERE uid = '$notification_id' and owner_id = '$boss_id'";
                        if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
                        else $response['success'] = ["new_notification_adds" => $json_array, "notification_id" => $notification_id];
                    }
                } else array_push($response['errors'], ERRORS['empty_fields']);
            } else {
                $value = $lol['notification_value'];
                $sql = "UPDATE notifications SET $type = '$value' WHERE uid = '$notification_id'";
                if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
            }
        } else { 
            $sql = "SELECT count(1) FROM notifications WHERE owner_id = '$boss_id'";
            $count = attach_sql($connection, $sql, 'row')[0];
            if($count < EDITIONS[$boss_tariff]["include"]["autosender"]["value"]){
                $uid = uniqid();  $name = $lol['notification_sender']; $type = $lol['notification_type'];
                if($type != 'DOM') $text = $lol['notification_text'];
                else $text = str_replace("'", "\'", $_POST['notification_text']);
                $conditions = $_POST['notification_conditions']; $departament = $lol['notification_departament'];
                $photo = $_FILES['notification_photo']; $notification_name = $lol['notification_name'];
                $data = save_file($photo, "notification_photos", $uid, $connection, $boss_id);
                $notification_photo = $data['text'];
                $files_paths = array();
                if(isset($_FILES['notification_add0'])){
                    $files = $_FILES;
                    unset($files['notification_photo']);
                    foreach($files as $file){
                        $filename = $file['name'];
                        $allowed_filetypes = VARIABLES["photos"]["notification_adds"]["accepted_types"];
                        $max_filesize = VARIABLES["photos"]["notification_adds"]["max_weight"];
                        $upload_path = $_SERVER['DOCUMENT_ROOT'].VARIABLES["photos"]["notification_adds"]["upload_path"];
                        $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
                        if(!in_array(strtolower($ext), $allowed_filetypes)){ array_push($response['errors'], ERRORS['not_accepted_file'] . $filename); continue; }
                        if(filesize($file['tmp_name']) > $max_filesize){ array_push($response['errors'], ERRORS['so_big_file'] . $filename); continue; }
                        if(!is_writable($upload_path)){ array_push($response['errors'], ERRORs['777_error']); continue; }
                        $filename = uniqid().uniqid() . $ext;
                        if(move_uploaded_file($file['tmp_name'], $upload_path . $filename)) array_push($files_paths, $filename);
                        else array_push($response['errors'], ERRORS['file_load_error']);
                    }
                }
                $files_paths = json_encode($files_paths, JSON_UNESCAPED_UNICODE);
                $sql = "
                    INSERT INTO notifications (
                        id,
                        uid,
                        name,
                        text,
                        departament,
                        conditions,
                        type,
                        photo,
                        adds,
                        owner_id,
                        notification_name,
                        statistic
                    ) VALUES (
                        0,
                        '$uid',
                        '$name',
                        '$text',
                        '$departament',
                        '$conditions',
                        '$type',
                        '$notification_photo',
                        '$files_paths',
                        '$boss_id',
                        '$notification_name',
                        '{\"statistic\": {}}'
                    )
                ";
                if($connection->query($sql) === true) $response['success'] = [
                    "create_notification" => [
                        "uid" => $uid, 
                        "adds" => json_decode($files_paths, JSON_UNESCAPED_UNICODE), 
                        "photo" => $notification_photo
                    ]
                ];
                else array_push($response['errors'], ERRORS['sql_error']);
            } else array_push($response['errors'], ERRORS['limit']);
        }
    } elseif( // Быстрые сообщения (АССИСТЕНТ / БОСС)
        isset($_POST['fastMessages_type']) && isset($_POST['fastMessages_value'])
        ){ 
        $value = $_POST['fastMessages_value']; $type = $_POST['fastMessages_type']; $uid = uniqid();
        if($type == 'new_fast_message'){
            $count = 0;
            foreach($boss_settings["fastMessages"] as $dir){ $count += count($dir); }
            if($count < EDITIONS[$boss_tariff]["include"]["fast_messages"]["value"]){
                $value = json_decode($value, JSON_UNESCAPED_UNICODE);
                $boss_settings["fastMessages"][$value['column']][$uid] = htmlencrypt($value['value']);	
            } else array_push($response['errors'], ERRORS['limit']);
        } elseif($type == 'remove_fast_message'){
            $value = json_decode($value, JSON_UNESCAPED_UNICODE);
            unset($boss_settings["fastMessages"][$value['column']][$value['value']]);	
        } elseif($type == 'save_fast_message'){
            $value = json_decode($value, JSON_UNESCAPED_UNICODE);
            $boss_settings["fastMessages"][$value['column']][$value['uid']] = htmlencrypt($value['value']);	
        } elseif($type == 'new_chapter'){ 
            if(count($boss_settings["fastMessages"]) < EDITIONS[$boss_tariff]["include"]["fast_messages_dirs"]["value"]){
                $boss_settings["fastMessages"][$uid] = [ "chapter_name" => htmlencrypt($value), ];
            } else array_push($response['errors'], ERRORS['limit']);
        } elseif($type == 'remove_chapter') unset($boss_settings["fastMessages"][$value]);
        elseif($type == 'chapter_name'){
            $value = json_decode($value, JSON_UNESCAPED_UNICODE);
            $boss_settings["fastMessages"][$value['uid']]['chapter_name'] = htmlencrypt($value['value']);	
        }
        $boss_settings = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE users SET settings = '$boss_settings' WHERE id = '$boss_id'";
        if($connection->query($sql) === true) $response['success'] = ["fastMessages" => ["type" => $type, "value" => $value, "uid" => $uid]];
        else array_push($response['errors'], ERRORS['sql_error']);
    } elseif( // контроль тарифа (АССИСТЕНТ / БОСС)
        isset($lol['select_tariff']) && 
        (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('tariff', $boss_departaments[$employee_departament]) : false))
        ){ 
        $index = $lol['select_tariff'];
        $curl_json["type"] = "select_tariff";
        $curl_json["info"] = ["tariff"=> $index, "domain" => $boss_id];	
        $data = send_curl($curl_json, $host);
        $info = json_decode($data, JSON_UNESCAPED_UNICODE);
        if($info["success"] == true) $response['success'] = ["money" => $info["error"]["money"], "tariff" => $info["error"]["tariff"]];
        else array_push($response['errors'], $info["error"]);
    } elseif( // отзыв (БОСС)
        ((isset($lol['review_name']) && isset($lol['review_link']) && isset($lol['review_review']) && isset($lol['review_rating']) && isset($_FILES['review_photo'])) || 
        (isset($lol['update_review_name']) && isset($lol['update_review_link']) && isset($lol['update_review_review']) && isset($lol['update_review_rating'])) || isset($lol['remove_review'])) && isset($_SESSION['boss'])
        ){ 
        if(isset($lol['review_name']) && isset($lol['review_link']) && isset($lol['review_review'])  && isset($lol['review_rating']) && isset($_FILES['review_photo'])){ // создать отзыв
            $sql = "SELECT count(1) FROM reviews WHERE review_id = '$boss_id'";
            $count = attach_sql($connection, $sql, 'row')[0];
            if($count == 0){
                $link = $lol['review_link']; $name = $lol['review_name']; $photo = $_FILES['review_photo'];
                $rating = $lol['review_rating']; $review = $lol['review_review'];
                $data = save_file($photo, "reviews_photos", null, $connection, null);
                if($data["access"] == false) array_push($response['errors'], $data["text"]);
                else{
                    $photo_name = $data["text"]; $review_id = $boss_id; $today = date("Y-m-d");
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
                        $response['success'] = ["reload" => true];
                    } else array_push($response['errors'], ERRORS['sql_error']);
                }
            } else array_push($response['errors'], ERRORS['review_already_exist']);
        } elseif(isset($lol['update_review_name']) && isset($lol['update_review_link']) && isset($lol['update_review_review']) && isset($lol['update_review_rating'])) { // обновить отзыв
            $link = $lol['update_review_link']; $name = $lol['update_review_name']; $today = date("Y-m-d H:i:s");
            if($_FILES['update_review_photo']["size"] != 0){ 
                $photo = $_FILES['update_review_photo'];
                remove_file('reviews_photos', 'reviews', 'review_id', $connection, $boss_id);
            }
            else $photo = null;
            $rating = $lol['update_review_rating'];
            $review = $lol['update_review_review'];
            if(strlen($review) > VARIABLES["review_len"] || strlen($name) > VARIABLES["review_name_len"] || strlen($name) > VARIABLES["review_link_len"]) array_push($response['errors'], ERRORS['alot_letters']);
            else {
                if($_FILES['update_review_photo']["size"] != 0) $data = save_file($photo, "reviews_photos", null, $connection, null);
                else{ $data["access"] = true; $data["text"] = false; }
                if($data["access"] == false) array_push($response['errors'], $data["text"]);
                else{
                    if($data["text"] != false){
                        $photo_name = $data["text"];
                        $sql = "UPDATE reviews SET name = '$name', photo = '$photo_name', link = '$link', review = '$review', time = '$today', rating = '$rating' WHERE review_id = '$boss_id'";
                    } else $sql = "UPDATE reviews SET name = '$name', link = '$link', review = '$review', time = '$today', rating = '$rating' WHERE review_id = '$boss_id'";
                    $today = date("Y-m-d H:i:s");
                    if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                }
            }
        } elseif(isset($lol['remove_review'])){ // удалить отзыв
            $sql = "SELECT photo FROM reviews WHERE review_id = $boss_id";
            remove_file('reviews_photos', 'reviews', 'review_id', $connection, $boss_id);
            $sql = "DELETE FROM reviews WHERE review_id = $boss_id";
            if ($connection->query($sql) === TRUE) $response["success"]["reload"] = true;
            else array_push($response['errors'], ERRORS['sql_error']);
        }
    } elseif( // ассистенты (БОСС)
        ((isset($lol['assistent_name']) && isset($lol['assistent_departament']) && isset($lol['assistent_email']) && isset($lol['assistent_password']) && isset($lol['assistent_passwordSecondTime'])) ||
        isset($lol['remove_assistent']) || (isset($lol['assistent_id']) && isset($lol['changesName'])) || (isset($_FILES['assistent_changephoto']) && isset($lol['assistent_img_id']))
        || isset($lol['enter_assistent']) || isset($lol['ban_assistent'])) &&
        (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('assistents', $boss_departaments[$employee_departament]) : false))
        ){
        if(isset($lol['assistent_name']) && isset($lol['assistent_departament']) && isset($lol['assistent_email']) && isset($lol['assistent_password']) && isset($lol['assistent_passwordSecondTime'])){ // создать
            $name = $lol['assistent_name']; $departament = $lol['assistent_departament'];
            $email =  mb_strtolower($lol['assistent_email']); $password = $lol['assistent_password']; $password_repeat = $lol['assistent_passwordSecondTime'];
            $sql = "SELECT count(1) FROM assistents WHERE email = '$email'";
            $count = attach_sql($connection, $sql, 'row')[0];
            $sql = "SELECT time FROM unconfimed_assistents WHERE email = '$email' ORDER BY unconfimed_assistents.time DESC";
            $reg_time = attach_sql($connection, $sql, 'row');
            $sql = "SELECT count(1) FROM assistents WHERE owner_id = '$boss_id'";
            $today = date('Y-m-d H:i:s');
            $sql = "SELECT count(1) FROM assistents WHERE domain = '$boss_id'";
            $assistents_count = attach_sql($connection, $sql, 'row')[0];
            if(intval($assistents_count) >= intval(EDITIONS[$boss_tariff]["include"]["assistents"]["value"]) && EDITIONS[$boss_tariff]["include"]["assistents"]["value"] != 0) array_push($response['errors'], ERRORS['assistents_limit']);
            if(intval($count) > 0) array_push($response['errors'], ERRORS['email_already_exist']);
            if(isset($reg_time)){ if(strtotime($reg_time[0] . ' +30 seconds') > strtotime($today) ) array_push($response['errors'], ERRORS['create_assistent_repeat']); }
            if(isset($lol['assistent_buttleCry'])) $buttlecry = $lol['assistent_buttleCry']; 
            else $buttlecry = $name.' , '.$departament; 
            if(strlen(trim($password)) < 7 or strlen(trim($password)) > 30) array_push($response['errors'], ERRORS['uncorrect_new_pass']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) array_push($response['errors'], ERRORS['uncorrect_new_email']); 
            if(strlen(trim($email)) < 3 or strlen(trim($email)) > 40) array_push($response['errors'], ERRORS['invalid_new_email']); 
            if(count($boss_domains['domains']) == 0) array_push($response['errors'], ERRORS['domain_before']); 
            if($password != $password_repeat) array_push($response['errors'], ERRORS['wrong_old_pass']);
            if(count($response['errors']) == 0){
                $password = password_hash($password, PASSWORD_BCRYPT);
                $unique_hash_firstpart = password_hash($email, PASSWORD_BCRYPT);
                $unique_hash = uniqid($unique_hash_firstpart, true);
                $sql = "INSERT INTO unconfimed_assistents(id, name, password, email, domain, buttlecry, departament, hash, time) VALUES (0,'".$name."','".$password."','".$email."','".$boss_id."','".$buttlecry."','".$departament."', '".$unique_hash."', '".$today."')";
                if ($connection->query($sql) === TRUE) {
                    smtpmail('interhelper', $email, 'Приглашение вступить в ассистенты.', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
                        <html>
                            <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                <title>Подтверждение почты</title>
                            </head>
                            <body style="width:450px;height:300px;">
                                <div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                                    <img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
                                    <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Подтверждение почты</h1>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Подтвердите готовность стать ассистентом, перейдя по <a href="http://interhelper.ru/engine/assistent_login?hash='.$unique_hash.'">ссылке</a> </p>
                                </div>
                            </body>
                        </html>'
                    );
                    $response["success"]["response"] = "Консультант успешно добавлен. Для продолжения необходимо подтвердить почту !";
                } else array_push($response['errors'], ERRORS['sql_error']);
            }
        } elseif(isset($lol['remove_assistent'])){ // удалить
            $remove_id = $lol['remove_assistent'];
            remove_file($type, 'assistents', 'id', $connection, $remove_id);
            $sql = "DELETE FROM assistents WHERE id = '$remove_id' and domain = '$boss_id'";
            if ($connection->query($sql) === TRUE) { 
                $curl_json["type"] = "remove_assistent";
                $curl_json["info"] = ["email"=> $remove_id, "domain" => $boss_id];
                send_curl($curl_json, $host);
            } else array_push($response['errors'], ERRORS['sql_error']);
        } elseif(isset($lol['assistent_id']) && isset($lol['changesName'])){ // обновить
            $option = $lol['changesName']; $assistent_id = $lol['assistent_id']; $value = $lol['changesValue'];
            $sql = "SELECT count(1) FROM assistents WHERE domain = '$boss_id'";
            $count = attach_sql($connection, $sql, 'row')[0];
            if($count == 0) array_push($response['errors'], ERRORS['assistent_not_exist']);
            if(array_search($option, array('id', 'owner_id', 'time'))) array_push($response['errors'], ERRORS['no_access']);
            if($option == 'email'){
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) array_push($response['errors'], ERRORS['uncorrect_new_email']);
                else{
                    $sql = "SELECT count(1) FROM assistents WHERE email = '$value'";
                    $count = attach_sql($connection, $sql, 'row')[0];
                    if($count > 0) array_push($response['errors'], ERRORS['email_already_exist']);
                }
            }
            if(count($response['errors']) == 0){ 
                $sql = "UPDATE assistents SET $option = '$value' WHERE id = '$assistent_id' and domain = '$boss_id'";
                if ($connection->query($sql) === TRUE){ 
                    $curl_json["type"] = "change_assistent";
                    $curl_json["info"] = ["assistent_id"=> $assistent_id, "domain" => $boss_id, "setting"=> $option, "value" => $value];
                    send_curl($curl_json, $host);
                } else array_push($response['errors'], ERRORS['sql_error']);
            }
        } elseif(isset($_FILES['assistent_changephoto']) && isset($lol['assistent_img_id'])){ // фото
            $assistent_id = $lol['assistent_img_id']; $photo = $_FILES['assistent_changephoto'];
            $data = save_file($photo, "assistent_profile_photo", $assistent_id, $connection, $boss_id);
            if($data["access"] == false) array_push($response['errors'], $data["text"]);
            else{
                $curl_json["type"] = "change_assistent";
                $curl_json["info"] = ["assistent_id"=> $assistent_id, "domain" => $boss_id, "setting"=> "photo", "value" => $data["text"]];
                send_curl($curl_json, $host);
            }
        } elseif(isset($lol['enter_assistent'])){ // войти
            $enter_id = $lol['enter_assistent'];
            $sql = "SELECT count(1) FROM assistents WHERE id= '$enter_id' and domain = '$boss_id'";
            if(attach_sql($connection, $sql, 'row')[0] > 0){
                $_SESSION['employee'] = json_encode(['boss_id' => $boss_id, 'personal_id' => $enter_id], JSON_UNESCAPED_UNICODE);
                $response['success']['link'] = '/engine/consultant/assistent';
                $_SESSION["employee_login_time"] = $today;
                $sql = "UPDATE assistents SET login_time = '$today' WHERE id = '$enter_id'";
                if($connection->query($sql) === true){
                    $curl_json["type"] = "reload_assistent";
                    $curl_json["info"] = ["id" => $enter_id];
                    send_curl($curl_json, $host);
                } else { 
                    unset($response['success']);
                    unset($_SESSION["employee"]);
                    unset($_SESSION["employee_login_time"]);
                    $response['success'] = array();
                    array_push($response['errors'], ERRORS['sql_error']);
                }
            } else array_push($response['errors'], 'не существующий ассистент !');
        } elseif(isset($lol['ban_assistent'])){ // блокировка ассистента
            $ban_id = $lol['ban_assistent'];
            $sql = "SELECT count(1), ban FROM assistents WHERE id= '$ban_id' and domain = '$boss_id'";
            $rows = attach_sql($connection, $sql, 'row');
            if($rows[0] > 0){
                if(!isset($rows[1])) $sql = "UPDATE assistents SET ban = '$today' WHERE id = '$ban_id' AND domain = '$boss_id'";
                else $sql = "UPDATE assistents SET ban = NULL WHERE id = '$ban_id' AND domain = '$boss_id'";
                if($connection->query($sql) === true){
                    $curl_json["type"] = "ban_assistent";
                    $curl_json["info"] = ["ban_id" => $ban_id, "status" => isset($rows[1]) ? true : $today, "domain" => $boss_id];
                    send_curl($curl_json, $host);
                } else array_push($response['errors'], ERRORS['sql_error']);
            } else array_push($response['errors'], 'не существующий ассистент !');
        }
    } elseif( // добавить элмент CRM (АССИСТЕНТ)
        isset($lol['crm_item_add_table']) &&
        (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $table = $_POST["crm_item_add_table"];
        $curl_json["type"] = "change_crm_item";
        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "add", "table" => $table];
        $data = send_curl($curl_json, $host);
        $info = json_decode($data, JSON_UNESCAPED_UNICODE);
        if($info["success"] != true) array_push($response['errors'], $info["error"]);
    } elseif( // удалить элмент CRM (АССИСТЕНТ)
        isset($lol['crm_item_delete_index']) && isset($lol['crm_item_delete_table'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $crm_item_index = $lol['crm_item_delete_index'];
        $table = $_POST["crm_item_delete_table"];
        $curl_json["type"] = "change_crm_item";
        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "remove", "table" => $table, "item_index" => $crm_item_index];
        send_curl($curl_json, $host);
    } elseif( // фото элемента CRM (АССИСТЕНТ)
        isset($_FILES['crm_item_img']) && isset($_POST['crm_item_table'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $crm_item_index = $lol['crm_item_index'];
        $table = $lol['crm_item_table'];
        $crm_item_column = $lol["crm_item_column"];
        $photo = $_FILES['crm_item_img']; 
        if($crm_item_column == 'helper_photo') $type = "crm_item_photo"; 
        else $type = "crm_files"; 
        $data = save_file($photo, $type, $crm_item_index, $connection, $crm_item_column);
        if($data["access"] == false) array_push($response['errors'], $data["text"]);
        else{
            $curl_json["type"] = "change_crm_item";
            $curl_json["info"] = ["domain" => $boss_id, "table" => $table, "setting"=> "change", "value" => $data["text"], "item_index" => $crm_item_index, "crm_item_column" => $crm_item_column];
            send_curl($curl_json, $host);
        }
    } elseif( // настройки элемента CRM (АССИСТЕНТ)
        isset($_POST['crm_item']) && isset($_POST['crm_column']) && isset($_POST['table'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $crm_item_index = $lol['crm_item'];
        $crm_item_column = $lol["crm_column"];
        if(isset($lol["crm_value"])) $crm_item_value = $lol["crm_value"];
        else $crm_item_value = '';
        $table = $lol["table"];
        $curl_json["type"] = "change_crm_item";
        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "change", "value" => $crm_item_value, "table" => $table, "item_index" => $crm_item_index, "crm_item_column" => $crm_item_column];
        send_curl($curl_json, $host);
    } elseif( // перемещение строк CRM (АССИСТЕНТ)
        isset($_POST['teleport_index']) && isset($_POST['table_to']) && isset($_POST['table_from'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $item_index = $_POST['teleport_index'];
        $table_to = $_POST['table_to'];
        $table_from = $_POST['table_from'];
        $curl_json["type"] = "change_crm_item";
        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "teleport", "item_index" => $item_index, "table_to" => $table_to, "table_from" => $table_from];
        $data = send_curl($curl_json, $host);
        $info = json_decode($data, JSON_UNESCAPED_UNICODE);
        if($info["success"] == true){ 
            $sql = "UPDATE crm_items SET item_table = '$table_to' WHERE owner_id = '$boss_id' and uid = '$item_index'";
            if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
        } else array_push($response['errors'], $info['error']);
    } elseif( // удалить задачу (АССИСТЕНТ)
        isset($lol["crm_remove_task_index"])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $item_index = $lol["crm_remove_task_index"];
        $curl_json["type"] = "change_crm_item";
        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "delete_task", "item_index" => $item_index];
        $data = send_curl($curl_json, $host);
        $info = json_decode($data, JSON_UNESCAPED_UNICODE);
        if($info["success"] != true) array_push($response['errors'], $info["error"]);
    } elseif( // добавить задачу (АССИСТЕНТ)
        isset($lol["task_time"]) && isset($lol["task_text"]) && isset($lol["task_type"]) && isset($_POST["task_selected"]) && isset($lol["table"])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){  
        $table = $lol["table"];
        $task_time = $lol["task_time"];
        $task_type = $lol["task_type"];
        $task_selected = json_decode($_POST["task_selected"],JSON_UNESCAPED_UNICODE);
        $task_text = $lol["task_text"];
        $curl_json["type"] = "change_crm_item";
        $curl_json["info"] = ["assistent_id" => $personal_id, "domain" => $boss_id, "setting"=> "add_task", "table" => $table, "task_text" => $task_text, "task_selected" => $task_selected, "task_type" => $task_type, "task_time" => $task_time];
        $data = send_curl($curl_json, $host);
        $info = json_decode($data, JSON_UNESCAPED_UNICODE);
        if($info["success"] == true) $response['success'] = ["response" => "Вы добавили задачу !", "add_task" => $info['error']];  
        else array_push($response['errors'], $info["error"]);
    } elseif( // добавить колонку в CRM (АССИСТЕНТ)
        isset($_POST['type']) && isset($_POST['header']) && isset($_POST['table'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $table = $_POST['table'];
        $deffault = null; $variants = null;
        if(isset($_FILES['deffault_file'])){
            $photo = $_FILES['deffault_file'];
            $data = save_file($photo, 'crm_files', null, $connection, null);
            if($data["access"] == false) array_push($response['errors'], $data["text"]);
            else $deffault = $data['text'];
        } else if(isset($_POST['deffault']) && trim($_POST['deffault']) != ''){ 
            if($_POST['type'] != 5) $deffault = $lol['deffault'];
            else $deffault = json_decode($_POST['deffault'], JSON_UNESCAPED_UNICODE);
        }
        if(isset($_POST['variants']) && trim($_POST['variants']) != '' && $_POST['variants'] != 'Не выбрано'){
            $variants = json_decode($_POST['variants'], JSON_UNESCAPED_UNICODE);
            foreach($variants as $key => $value){
                $variants[$key] = htmlencrypt($value);
            }
            $variants_len = count($variants);
        } else $variants_len = 0;
        if(isset($_POST['priority'])) $priority = intval($lol['priority']);
        else $priority = 0;
        $header = $lol['header'];
        if(count($response['errors']) == 0){
            if($header != "helper_photo" && $header != "helper_name" && $header != "helper_info"){
                $type = $lol['type'];
                $sql = "SELECT crm.columns, users.tariff FROM crm LEFT JOIN users ON ( users.id = '$boss_id' ) WHERE crm.owner_id = '$boss_id'";
                $rows = attach_sql($connection, $sql, 'row');
                $json_array = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
                $count = 0;
                foreach($json_array as $inner_table){ $count += count($inner_table['table_columns']); }
                if(isset($json_array[$table])){
                    if($count < intval(EDITIONS[$rows[1]]["include"]["table_columns"]["value"]) || EDITIONS[$rows[1]]["include"]["table_columns"]["value"] == 0){
                        if(EDITIONS[$rows[1]]["include"]["variants"]["value"] == 0 || EDITIONS[$rows[1]]["include"]["variants"]["value"] >= $variants_len){
                            $column_index = uniqid();
                            $json_array[$table]['table_columns'][$column_index] = [
                                "type" => $type, 
                                "deffault" => $deffault, 
                                "variants" => $variants, 
                                'priority' => $priority,
                                "helper_column_name" => $header,
                            ];
                            $json_array = json_encode($json_array, JSON_UNESCAPED_UNICODE);
                            $sql = "UPDATE crm SET columns = '$json_array' WHERE owner_id = '$boss_id'";
                            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                            else {
                                $response['success']['photo'] = $data['text'];
                                $response['success']['column_index'] = $column_index;
                            }
                        } else array_push($response['errors'], ERRORS['limit']);
                    } else array_push($response['errors'], ERRORS['limit']);
                } else array_push($response['errors'], 'Таблицы не существует !');	
            } else array_push($response['errors'], 'Колонку к таким названием создать нельзя !');
        }	
    } elseif( // удалить CRM колонку (АССИСТЕНТ)
        isset($_POST['delete_column']) && isset($_POST['table'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $table = $_POST['table'];
        $header = $lol['delete_column'];
        $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
        $json_array = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        if(isset($json_array[$table])){
            if($json_array[$table]['table_columns'][$header]["type"] == 6){
                @unlink($_SERVER['DOCUMENT_ROOT'].'/crm_files/'.$json_array[$table]['table_columns'][$header]["deffault"]);
            }
            unset($json_array[$table]['table_columns'][$header]);
            $json_array = json_encode($json_array, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE crm SET columns = '$json_array' WHERE owner_id = '$boss_id'";
            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
        } else array_push($response['errors'], 'Таблицы не существует !');
    } elseif( // CRM колонку обновить (АССИСТЕНТ)
        isset($_POST['save_type']) && isset($_POST['save_header']) && isset($_POST['table']) && isset($_POST['column_index'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        $deffault = null;
        $variants = null;
        $table = $_POST['table'];
        $column_index = $_POST['column_index'];
        $header = $lol['save_header'];
        if(isset($_FILES['deffault_file'])){
            $photo = $_FILES['deffault_file'];
            $data = save_file($photo, 'crm_files', null, $connection, ['table' => $table, "column" => $column_index, "owner_id" => $boss_id]);
            if($data["access"] == false) array_push($response['errors'], $data["text"]);
            else $deffault = $data['text'];
        } elseif(isset($_POST['deffault']) && trim($_POST['deffault']) != ''){ 
            if($_POST['save_type'] != 5) $deffault = $lol['deffault'];
            else $deffault = json_decode($_POST['deffault'], JSON_UNESCAPED_UNICODE);
        }
        if(isset($_POST['variants']) && trim($_POST['variants']) != ''){
            $variants = json_decode($_POST['variants'], JSON_UNESCAPED_UNICODE);
            foreach($variants as $key => $value){
                $variants[$key] = htmlencrypt($value);
            }
            $variants_len = count($variants);
        } else $variants_len = 0;
        if(isset($_POST['priority'])) $priority = intval($lol['priority']);
        else $priority = 0;
        $type = $lol['save_type'];
        $sql = "SELECT crm.columns, users.tariff FROM crm LEFT JOIN users ON ( users.id = '$boss_id' ) WHERE crm.owner_id = '$boss_id'";
        $rows = attach_sql($connection, $sql, 'row');
        $json_array = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
        if(isset($json_array[$table])){
            if($column_index != "helper_photo" && $column_index != "helper_name" && $column_index != "helper_info"){
                if((EDITIONS[$rows[1]]["include"]["variants"]["value"] == 0 || EDITIONS[$rows[1]]["include"]["variants"]["value"] >= $variants_len) && isset($json_array[$table]['table_columns'][$column_index])){
                    unset($json_array[$table]['table_columns'][$column_index]);
                    $json_array[$table]['table_columns'][$column_index] = [
                        "type" => $type, 
                        "deffault" => $deffault, 
                        "variants" => $variants, 
                        "helper_column_name" => $header, 
                        'priority' => $priority
                    ];
                    $json_array = json_encode($json_array, JSON_UNESCAPED_UNICODE);
                    $sql = "UPDATE crm SET columns = '$json_array' WHERE owner_id = '$boss_id'";
                    if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
                    else $response['success']['photo'] = $data['text'];
                } else array_push($response['errors'], ERRORS['limit']);  
            } else array_push($response['errors'], 'Колонку к таким названием создать нельзя !');
        } else array_push($response['errors'], 'Таблицы не существует !');
    } elseif( // обновить стандартную колонку (АССИСТЕНТ)
        isset($_POST['display']) && isset($_POST['column_type']) && isset($_POST['deffault_header'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){ 
        if(isset($_POST['priority'])) $priority = intval($_POST['priority']);
        else $priority = 0;
        $display = $_POST['display'];
        $header = $_POST['deffault_header'];
        $table = $_POST['table'];
        $sql = "SELECT crm.columns FROM crm WHERE owner_id = '$boss_id'";
        $rows = attach_sql($connection, $sql, 'row');
        $json_array = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
        if(isset($json_array[$table])){
            $json_array[$table]['deffault_columns'][$header] = ["display" => $display, "priority" => $priority];
            $json_array = json_encode($json_array, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE crm SET columns = '$json_array' WHERE owner_id = '$boss_id'";
            if ($connection->query($sql) !== TRUE) array_push($response['errors'], ERRORS['sql_error']);
        } else array_push($response['errors'], 'Таблицы не существует !');	
    } elseif( // действия с выбранными посетителями
        isset($_POST['type']) && isset($_POST['selected_visitors'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('hub', $boss_departaments[$employee_departament]) : false))
        ){ 
        $selected_visitors = json_decode($_POST['selected_visitors'], JSON_UNESCAPED_UNICODE);
        $type = $_POST['type'];
        $reason = '';
        $message = '';
        if(isset($_POST['reason'])) $reason = $_POST['reason'];
        $curl_json["type"] = "selected_visitors_options";
        $curl_json["info"] = [
            "assistent_id"=> $assistent_id, 
            "type" => $type, 
            "token"=> $_SESSION["assistent_server_info"]["token"], 
            "selected_visitors" => $selected_visitors,
            "reason" => $reason,
        ];
        send_curl($curl_json, $host);
    } elseif( // консультация
        isset($_POST['room']) && isset($_POST['type'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('hub', $boss_departaments[$employee_departament]) : false))
        ){ 
        $room = $_POST['room'];
        $type = $_POST['type'];
        $curl_json["type"] = "consultation";
        $curl_json["info"] = ["type"=> $type, "domain" => $boss_id, "room" => $room, "assistent_id" => $personal_id];
        send_curl($curl_json, $host);
        if($type != 'finish') $response['success'] = ["link" => '/engine/consultant/chat?room='.$room];
    } elseif( // MESSAGE
        isset($_POST['message_to_guset']) && (isset($_POST['message_to_guest_type']) || isset($_POST['selected']))
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('hub', $boss_departaments[$employee_departament]) : false))
        ){ 
        $mode = null;
        if(isset($_POST["mode"])) $mode = $_POST["mode"];
        if(isset($_POST['message_to_guest_type'])){
            $message_type = $_POST["message_to_guest_type"];
            $curl_json["type"] = "consultant_message";
            $curl_json["info"] = [
                "token" => $_SESSION["assistent_server_info"]["token"], 
                "adds" => null,
                "room" => $_SESSION["assistent_room"],
                "chat_type" => $message_type,
                "message" => $_POST["message_to_guset"],
                "mode" => $mode,
                "message_htmlchars" => $lol["message_to_guset"],
            ];
        } else {
            $curl_json["type"] = "send_all_answer";
            $curl_json["info"] = [
                "token" => $_SESSION["assistent_server_info"]["token"], 
                "adds" => null,
                "users" => $_POST["selected"],
                "encrypted_message" => $_POST["message_to_guset"],
                "action" => $mode,
                "decrypted_message" => $lol["message_to_guset"],
            ];
        }
        send_curl($curl_json, $host);
    } elseif( // PHOTO && MESSAGE
        (isset($_FILES['SendImg1']) || isset($_FILES['SendToSelectedImg1']))
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('hub', $boss_departaments[$employee_departament]) : false))
        ){ 
        if(isset($_POST["message_type"])) $message_type = $_POST["message_type"];
        $message = str_replace("\n", "<br/>", $_POST["message"]);
        if(strpos($message, "<script") || strpos($message, "/script")) array_push($response['errors'], "Пока мы не реализовали отпраку скрипта вместе с файлами !");
        if(count($_FILES) <= VARIABLES["photos"]["assistent_send_photo"]["max_count"] && count($response['errors']) == 0){
            $files_paths = array();
            $file_status = false;
            foreach($_FILES as $file){
                $filename = $file['name'];
                $allowed_filetypes = VARIABLES["photos"]["assistent_send_photo"]["accepted_types"];
                $max_filesize = VARIABLES["photos"]["assistent_send_photo"]["max_weight"];
                $upload_path = $_SERVER['DOCUMENT_ROOT'].VARIABLES["photos"]["assistent_send_photo"]["upload_path"];
                $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
                if(in_array($ext,$allowed_filetypes)){
                    if(filesize($file['tmp_name']) <= $max_filesize){
                        if(is_writable($upload_path)){
                            $filename = uniqid().uniqid() . $ext;
                            if(move_uploaded_file($file['tmp_name'], $upload_path . $filename)){ 
                                $file_status = true;
                                array_push($files_paths, $filename);
                            } else array_push($response['errors'], 'При загрузке возникли ошибки. Попробуйте ещё раз.');
                        } else array_push($response['errors'], 'Невозможно загрузить фаил в папку. Установите права доступа - 777.');
                    } else array_push($response['errors'], 'Слишком большой файл.');
                } else array_push($response['errors'], 'Не поддерживаемый тип файла.');
            }
            if(isset($_FILES['SendImg1'])){
                $curl_json["type"] = "consultant_message";
                $curl_json["info"] = [
                    "token" => $_SESSION["assistent_server_info"]["token"], 
                    "adds" => $file_status ? $files_paths : null,
                    "room" => $_SESSION["assistent_room"],
                    "chat_type" => $message_type,
                    "message" => $message,
                    "message_status" => "visible",
                    "message_htmlchars" => $lol["message"],
                ];
            } else {
                $mode = null;
                if(isset($_POST["mode"])) $mode = $_POST["mode"];
                $curl_json["type"] = "send_all_answer";
                $curl_json["info"] = [
                    "token" => $_SESSION["assistent_server_info"]["token"], 
                    "adds" => $files_paths,
                    "users" => $_POST["selected"],
                    "encrypted_message" => $_POST["message"],
                    "action" => $mode,
                    "decrypted_message" => $lol["message"],
                ];
            }
            if(isset($_FILES['SendImg1']) || $file_status) send_curl($curl_json, $host);
        } else array_push($response['errors'], 'Слишком много файлов: '.count($_FILES));
    } elseif( // имя таблицы
        isset($_POST['prev_table_name']) && isset($_POST['new_table_name'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){
        $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
        $tables = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        $prev_table = $_POST['prev_table_name'];
        $new_table = trim(preg_replace('/[^ a-zа-яё\d]/ui', '', $_POST['new_table_name']));
        if(isset($tables[$prev_table]) && !isset($tables[$new_table])){
            $tables[$new_table] = $tables[$prev_table];
            unset($tables[$prev_table]);
            $tables = json_encode($tables, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE crm SET columns = '$tables' WHERE owner_id = '$boss_id'";
            $connection->query($sql);
            $sql = "UPDATE crm_items SET item_table = '$new_table' WHERE owner_id = '$boss_id' and item_table = '$prev_table'";
            if($connection->query($sql) === true){ 
                $response['success']['link'] = '/engine/consultant/crm_settings?type='.$new_table;
                $curl_json["type"] = "change_crm_item";
                $curl_json["info"] = ["domain" => $boss_id, "setting"=> "table_name", "new_table" => $new_table, 'prev_table' => $prev_table];
                send_curl($curl_json, $host);
            } else array_push($response['errors'], ERRORS['sql_error']);
        } else array_push($response['errors'], 'Таблицы не существует или Вы уже создали таблицу с таким именем!');
    } elseif( // удалить таблицу
        isset($_POST['table_remove'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){
        $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
        $tables = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        $table_remove = $lol['table_remove'];
        if(isset($tables[$table_remove])){
            foreach($tables as $table){
                foreach($table['table_columns'] as $column){
                    if($column['type'] != 6) continue;
                    @unlink($_SERVER['DOCUMENT_ROOT'].'/crm_files/'.$column["deffault"]);
                }
                if($table['deffault_columns']['helper_photo'] != 'user.png') @unlink($_SERVER['DOCUMENT_ROOT'].'/crm_files/'.$table['deffault_columns']['helper_photo']);
            }
            unset($tables[$table_remove]);
            $tables = json_encode($tables, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE crm SET columns = '$tables' WHERE owner_id = '$boss_id'";
            $connection->query($sql);
            $sql = "DELETE FROM crm_items WHERE owner_id = '$boss_id' and item_table = '$table_remove'";
            if($connection->query($sql) === true){ 
                $response['success']['link'] = '/engine/consultant/crm';
                $curl_json["type"] = "change_crm_item";
                $curl_json["info"] = ["domain" => $boss_id, "setting"=> "table_remove", "table_remove" => $table_remove];
                send_curl($curl_json, $host);
            } else array_push($response['errors'], ERRORS['sql_error']);
        } else array_push($response['errors'], 'Таблицы не существует!');
    } elseif( // добавить таблицу
        isset($_POST['table_add'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){
        $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
        $tables = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        $table_add = trim($_POST['table_add']);
        $table_add = preg_replace('/[^ a-zа-яё\d]/ui', '',$table_add);
        if(str_replace(' ', '', $table_add) != ''){
            if(count($tables) + 1 <= intval(EDITIONS[$boss_tariff]["include"]["tables"]["value"])){
                if(!isset($tables[$table_add])){
                    $tables[$table_add] = [
                        "table_columns" => [],
                        "deffault_columns" => [
                            "helper_info" => ["display"=>"false", "priority"=> -1, "deffault"=> ""],
                            "helper_name" => ["display"=>"false", "priority"=> -2, "deffault"=> "новый"],
                            "helper_photo"=> ["display"=>"false", "priority"=> -3, "deffault"=> "user.png"],
                        ],
                    ];
                    $tables = json_encode($tables, JSON_UNESCAPED_UNICODE);
                    $sql = "UPDATE crm SET columns = '$tables' WHERE owner_id = '$boss_id'";
                    if($connection->query($sql) === true){ 
                        $curl_json["type"] = "change_crm_item";
                        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "table_add", "table_add" => $table_add];
                        send_curl($curl_json, $host);
                    } else array_push($response['errors'], ERRORS['sql_error']);
                } else array_push($response['errors'], 'Таблица уже существует!');
            } else array_push($response['errors'], ERRORS['limit']);
        } else array_push($response['errors'], 'В названии таблицы могут присутствовать только буквы и цифры !');
    } elseif( // добавить таблицу
        isset($_POST['copy_table'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('crm', $boss_departaments[$employee_departament]) : false))
        ){
        $sql = "SELECT columns FROM crm WHERE owner_id = '$boss_id'";
        $tables = json_decode(attach_sql($connection, $sql, 'row')[0], JSON_UNESCAPED_UNICODE);
        $copy_table = trim($_POST['copy_table']);
        $copy_table = preg_replace('/[^ a-zа-яё\d]/ui', '', $copy_table);
        if(str_replace(' ', '', $copy_table) != ''){
            if(count($tables) + 1 <= intval(EDITIONS[$boss_tariff]["include"]["tables"]["value"])){
                $count = count($tables[$copy_table]['table_columns']);
                foreach($tables as $table) $count += count($table['table_columns']);
                if($count <= intval(EDITIONS[$boss_tariff]["include"]["table_columns"]["value"]) || intval(EDITIONS[$boss_tariff]["include"]["table_columns"]["value"]) == 0){
                    $table_add = $copy_table . ' (копия)';
                    $tables[$table_add] = $tables[$copy_table];
                    $tables = json_encode($tables, JSON_UNESCAPED_UNICODE);
                    $sql = "UPDATE crm SET columns = '$tables' WHERE owner_id = '$boss_id'";
                    if($connection->query($sql) === true){ 
                        $curl_json["type"] = "change_crm_item";
                        $curl_json["info"] = ["domain" => $boss_id, "setting"=> "table_add", "table_add" => $table_add];
                        send_curl($curl_json, $host);
                    } else array_push($response['errors'], ERRORS['sql_error']);
                } else array_push($response['errors'], ERRORS['limit']);
            } else array_push($response['errors'], ERRORS['limit']);
        } else array_push($response['errors'], 'В названии таблицы могут присутствовать только буквы и цифры !');
    } elseif( // mailer (АССИСТЕНТ / БОСС)
        isset($_POST['mailer_type'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('mailer', $boss_departaments[$employee_departament]) : false))
        ){ 
        if(isset($_POST['design_domain'])) $domain = $_POST['design_domain'];
        else $domain = 'deffault';
        $accepted_types = array("SMTPsecure", "SMTPserver", "SMTPport", "SMTPemail", "sender_name", "mail_name");
        $mailer_type = $_POST['mailer_type'];
        if(isset($_POST['mailer_value'])) $mailer_value = $lol['mailer_value'];
        else $mailer_value = '';
        if($mailer_type == 'SMTPpassword'){
            $key = '#_sashapop10_#'.$boss_id;
            $boss_settings['mailer'][$domain][$mailer_type] = strToHex(__encode($lol['mailer_value'], $key)); 
        } else if(($mailer_type == 'feedback_email' || $mailer_type == 'SMTPemail')) {
            if(!filter_var($mailer_value, FILTER_VALIDATE_EMAIL)) array_push($response['errors'], ERRORS['uncorrect_new_email']);
            $boss_settings['mailer'][$domain][$mailer_type] = $mailer_value; 
        } else if(array_search($mailer_type, $accepted_types) || array_search($mailer_type, $accepted_types) == 0){
            $boss_settings['mailer'][$domain][$mailer_type] = $mailer_value; 
        } else array_push($response['errors'], 'Не существующий тип !');
        $boss_settings = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE users SET settings = '$boss_settings' WHERE id = '$boss_id'";
        if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
    } elseif( // mailer send (АССИСТЕНТ / БОСС)
        isset($_POST['mailer_message'])  && isset($_POST['mailer_name']) && isset($_POST['sender_name']) && isset($_POST['mailer_info'])
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('mailer', $boss_departaments[$employee_departament]) : false))
        ){
        if(isset($_POST['design_domain'])) $domain = $_POST['design_domain'];
        else $domain = 'deffault'; 
        $response['success']['loader'] = false;
        $cost = EDITIONS[$boss_tariff]["include"]["mailer"]["value"];
        $info = json_decode($_POST['mailer_info'], JSON_UNESCAPED_UNICODE);
        $cost_for_all = $cost * count($info);
        if($boss_money - $cost_for_all >= 0){
            $mailer_message = $_POST['mailer_message'];
            $mailer_name = $_POST['mailer_name'];
            $sender_name = $_POST['sender_name'];
            if(count($info) > 0){
                if(strlen($_POST['mailer_message']) > 30 || isset($_FILES['mailer_files1'])){
                    $feedback_email = htmlspecialchars_decode($boss_settings['mailer'][$domain]['feedback_email'], ENT_QUOTES);
                    $SMTPemail = htmlspecialchars_decode($boss_settings['mailer'][$domain]['SMTPemail'], ENT_QUOTES);
                    $SMTPpassword = __decode(hexToStr($boss_settings['mailer'][$domain]['SMTPpassword']), '#_sashapop10_#'.$boss_id);
                    $SMTPsecure = htmlspecialchars_decode($boss_settings['mailer'][$domain]['SMTPsecure'], ENT_QUOTES);
                    $SMTPport = htmlspecialchars_decode($boss_settings['mailer'][$domain]['SMTPport'], ENT_QUOTES);
                    $SMTPhost = htmlspecialchars_decode($boss_settings['mailer'][$domain]['SMTPserver'], ENT_QUOTES);
                    $send_count = count($info);
                    $success = true;
                    $sended_count = 0;
                    while($sended_count < $send_count){
                        $send_to = array_slice($info, $sended_count, 35, true); 
                        $sended_count += 35;
                        $result = send_mails($send_to, $mailer_name, $sender_name, $SMTPemail, $SMTPpassword, $SMTPsecure, $SMTPport, $SMTPhost, $mailer_message);
                        if(count($result['errors']) > 0) array_push($response['errors'], 'Ошибка рассылки, проверьте вводимые данные. '. implode(',', $result['errors']));
                        else $success = true;
                    }     
                    if($success > 0){
                        $boss_money -= $cost * $send_count;
                        $response['success']['response'] = "Вы совершили рассылку."; 
                        $curl_json["type"] = "set_money";
                        $curl_json["info"] = ["boss" => $boss_id, "money"=> $boss_money];
                        send_curl($curl_json, $host);
                    }
                } else array_push($response['errors'], 'Длина письма должна быть больше 30 символов !');
            } else array_push($response['errors'], 'Внесите получателей Вашего письма !');
        } else array_push($response['errors'], 'На вашем счету не хватает денежных средств для совершения операции !');
    } elseif( // уникальный дизайн для домена вкл / выкл
        isset($_POST['personal_design_domain_status']) &&
        (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? (in_array('options', $boss_departaments[$employee_departament]) || in_array('design', $boss_departaments[$employee_departament]) || in_array('offline', $boss_departaments[$employee_departament]) || in_array('anticlicker', $boss_departaments[$employee_departament])) : false))
        ){
        $domain = $_POST['personal_design_domain_status'];
        if(in_array($domain, $boss_domains['domains'])){
            $status = array_key_exists($domain, $boss_settings['InterHelperOptions']);
            if($status){
                unset($boss_settings['InterHelperOptions'][$domain]);
                unset($boss_settings['mailer'][$domain]);
            } else {
                $boss_settings['InterHelperOptions'][$domain] = [
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
                    ]
                ];
                $boss_settings['mailer'][$domain] = [
                    "SMTPsecure" => "",
                    "SMTPserver" => "",
                    "SMTPport" => "",
                    "SMTPemail" => "",
                    "SMTPpassword" => "",
                    "sender_name" => "",
                    "mail_name" => ""
                ];
            }
            $boss_settings = json_encode($boss_settings, JSON_UNESCAPED_UNICODE);
            $sql = "UPDATE users SET settings = '$boss_settings' WHERE id = '$boss_id'";
            if($connection->query($sql) !== true) array_push($response['errors'], ERRORS['sql_error']);
        }
    } elseif( // notes / properties
        isset($_POST['note_type']) && isset($_POST['note_room']) && (isset($_POST['note_value']) || isset($_POST['note_id']))
        && (isset($_SESSION['boss']) ? true : (isset($boss_departaments[$employee_departament]) ? in_array('hub', $boss_departaments[$employee_departament]) : false))
        ){
        $room = $_POST['note_room'];
        if(strpos($room, $boss_id) !== false){
            $type = $_POST['note_type'];
            $_POST['note_id'];
            $sql = null;
            if($type == 'note'){
                $sql = "SELECT notes FROM rooms WHERE room = '$room'";
            } else if($type == 'property'){
                $sql = "SELECT properties FROM rooms WHERE room = '$room'";
            }
            if(isset($sql)){
                $rows = attach_sql($connection, $sql, 'row');
                if(isset($rows)){
                    $info = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
                    if(count(current($info)) + 1 <= 10 || isset($_POST['note_id'])){
                        if($type == 'note'){
                            if(isset($_POST['note_value'])){
                                $value = $_POST['note_value'];
                                $info['notes'][uniqid()] = htmlencrypt($value);
                            } else if(isset($_POST['note_id']) && !isset($_POST['note_inner_type'])){
                                unset($info['notes'][$_POST['note_id']]);
                            } else if(isset($_POST['note_inner_type'])){
                                if(isset($_POST['note_update_value'])) $value = htmlencrypt($_POST['note_update_value']);
                                else $value = '';
                                $info['notes'][$_POST['note_id']] = $value;
                            }
                        } else {
                            if(isset($_POST['note_value'])){
                                $value = json_decode($_POST['note_value'], JSON_UNESCAPED_UNICODE);
                                $info['properties'][htmlencrypt($value['name'])] = htmlencrypt($value['value']);
                            } else if(isset($_POST['note_id']) && !isset($_POST['note_inner_type'])) {
                                unset($info['properties'][$_POST['note_id']]);
                            } else if(isset($_POST['note_inner_type'])){
                                if(isset($_POST['note_update_value'])) $value = htmlencrypt($_POST['note_update_value']);
                                else $value = '';
                                if($_POST['note_inner_type'] == 'name' && $value != ''){
                                    $info['properties'][$value] = $info['properties'][$_POST['note_id']];
                                    unset($info['properties'][$_POST['note_id']]);
                                } else $info['notes'][$_POST['note_id']] = $value;
                            }
                        }
                        $save_info = json_encode($info, JSON_UNESCAPED_UNICODE);
                        if($type == 'note'){
                            $sql = "UPDATE rooms SET notes = '$save_info' WHERE room = '$room'";
                        } else {
                            $sql = "UPDATE rooms SET properties = '$save_info' WHERE room = '$room'";
                        }
                        if($connection->query($sql) === true){
                            $curl_json["type"] = "room_info";
                            $curl_json["info"] = [
                                "token"=> $_SESSION["assistent_server_info"]["token"], 
                                "room" => $room,
                                "settings" => [
                                    "value" => $info,
                                    "name" => ($type == 'note' ? 'notes' : 'properties'),
                                ]
                            ];
                            $data = json_decode(send_curl($curl_json, $host), JSON_UNESCAPED_UNICODE);
                            if($data['success']) $response['success']['create_note'] = ["type" => ($type == 'note' ? 'notes' : 'properties'), "value" => $info];
                            else array_push($response['errors'], $data['error']);
                        } else array_push($response['errors'], ERRORS['sql_error']);
                    } else array_push($response['errors'], ERRORS['limit']);
                }
            }
        }
    } else array_push($response['errors'], ERRORS['empty_fields']);
} 
// конец
echo json_encode($response, JSON_UNESCAPED_UNICODE); 
if(isset($connection)) mysqli_close($connection);
?>