<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$error_info = '';
$error_count = 0;
$response_info = '';
include 'connection.php';
global $connection;
$lol = $_POST;
foreach ($lol as $index){
	$some = array_search($index, $lol);
	$lol[$some] = str_replace("\n", " ", htmlspecialchars($lol[$some], ENT_QUOTES));
}
if(isset($lol["assistent_login"]) && isset($lol["assistent_pass"]) && isset($lol["status"])){
    $login = $lol["assistent_login"];
    $pass = $lol["assistent_pass"];
    $status = $lol["status"];
    $sql = "SELECT password FROM assistents WHERE email = '$login'";
    $query = mysqli_query($connection, $sql);
    $hash = mysqli_fetch_row($query);
    if(isset($hash)){  
        $hash = $hash[0];
        if(password_verify($pass, $hash)){
            $sql = "SELECT gmessage FROM assistents WHERE email = '$login'";
            $query = mysqli_query($connection, $sql); $gmessage = mysqli_fetch_row($query)[0];
            $today = date('Y-m-d').' 00:00:00'; $today_end = date('Y-m-d').' 23:59:59';
            $sql = "UPDATE assistents SET gmessage = 'false' WHERE email = '$login'";
            if($status) $connection->query($sql);
            $sql = "SELECT count(1) FROM tasks WHERE cast(time AS DATE) >= cast('$today' AS DATE) and cast(time AS DATE) <= cast('$today_end' AS DATE) and owner_id = (SELECT domain FROM assistents WHERE email = '$login') and (type = 1 or creator_id = (SELECT id FROM assistents WHERE email = '$login'))";
            $query = mysqli_query($connection, $sql); $tasks = mysqli_fetch_row($query)[0];
            $answer = ['info' => array('status' => 'yes'), "gmessage" => ($gmessage == "true"), "task" => $tasks];
            $answer = json_encode($answer, JSON_UNESCAPED_UNICODE);
            echo $answer;
        } else{ $error_info .= 'Ваш пароль или почта не существует!'; $error_count+=1; }
    } else { $error_info .= 'Ваш пароль или почта не существует!'; $error_count+=1; }
} else {  $error_info .= 'Поля не заполнены!'; $error_count +=1; }
if ($error_count >= 1 && strlen($response_info) === 0){ $error_json = array(); $error_json["info"] = array('status' => 'no', 'error_info' => $error_info, 'error_count' => $error_count); $error_json =  json_encode($error_json, JSON_UNESCAPED_UNICODE); echo $error_json; }
if($connection != '' || $connection != null || isset($connection)){  mysqli_close($connection);  }
?>