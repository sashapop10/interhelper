<?php 
    header('Content-Type: text/csv; charset=utf-8');
    header("Cache-Control: public");
    $table = $_GET['table'];
    header("Content-Disposition: attachment; filename=".$table.".csv");
    header("Content-Transfer-Encoding: binary");
    session_start();
    include 'connection.php'; 
    include 'func.php';
    global $connection;
    include 'config.php';
    $curl_json = ["login" => VARIABLES["login"], "password"=> VARIABLES["password"], "type"=>"", "info" => []]; $host = SERVERPATH . '/admin';
    // определяем пользователя
    if(isset($_SESSION['employee'])) $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    if(isset($_SESSION['boss'])) $boss_id = $_SESSION['boss']; 
    if(isset($boss_id) && isset($_GET['table'])){ 
        $curl_json["type"] = "csv";
        $curl_json["info"] = ["boss_id" => $boss_id, "table"=> $_GET['table']];
        $data = json_decode(send_curl($curl_json, $host), JSON_UNESCAPED_UNICODE);
        echo $data['error'];
    } 
?>