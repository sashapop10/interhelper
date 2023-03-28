<?php
session_start();
if(isset($_SESSION['employee']) || isset($_SESSION['boss'])){
    $boss_id = isset($_SESSION['boss']) ? $_SESSION['boss'] : json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
} else {
    header("Location: /index");
    exit;
}
include 'connection.php'; 
include 'func.php';
global $connection;
include 'config.php';
$response = ["errors" => [], "success" => []];
if(isset($_POST['make_order'])){
    $cost = $_POST['make_order'];
    if(intval($cost) < 10) $cost = 10;
    $host = '################################';
    $sql = "SELECT email FROM users WHERE id = '$boss_id'";
    $email = attach_sql($connection, $sql, 'row')[0];
    $curl_json = [
        "make_json" => false,
        "userName" => "################################", 
        "password"=> "################################",
        'amount'=> intval($_POST['make_order']) * 100,
        'returnUrl'=> '################################',
        'failUrl'=> '################################',
        'description'=> 'Пополнение счёта личного кабинета INTERHELPER',
        'language'=> 'RU',
        'clientId'=> $boss_id,
        'email'=> $email,
        'orderNumber'=> uniqid(),
    ]; 
    $data = send_curl($curl_json, $host);
    $data = json_decode($data, JSON_UNESCAPED_UNICODE);
    $response['success']['link'] = $data['formUrl'];
} else if(isset($_GET['orderId'])){
    $orderId = $_GET['orderId'];
    $host = 'https://securepayments.sberbank.ru/payment/rest/getOrderStatusExtended.do';
    $curl_json = ["make_json" => false, "userName" => "################################", "password"=> "################################", "orderId" => $orderId, "language" => "RU"]; 
    $data = send_curl($curl_json, $host);
    $data = json_decode($data, JSON_UNESCAPED_UNICODE);
    if(isset($data['orderNumber'])) $orderNumber = $data['orderNumber'];
    if(isset($data['orderStatus'])) $orderStatus = $data['orderStatus'];
    if(isset($data['amount'])) $amount = intval($data['amount']) / 100;
    if(isset($data['cardAuthInfo']['cardholderName'])) $cardholderName = $data['cardAuthInfo']['cardholderName'];
    $sql = "SELECT count(1) FROM orders WHERE orderId = '$orderId' OR orderNumber = '$orderNumber'";
    $rows = attach_sql($connection, $sql, 'row');
    $LastActionTime = date('Y-m-d H:i:s');
    if($rows[0] == 0){
        if($orderStatus != 2){
            header("Location: /page/error-pay");
            mysqli_close($connection);
            exit;
        }
        $sql = "UPDATE users SET money = money + $amount WHERE id = '$boss_id'";
        $connection->query($sql);
        $sql = "INSERT INTO orders(orderNumber, amount, cardholderName, account_id, LastActionTime, orderId) VALUES ('$orderNumber', $amount, '$cardholderName', '$boss_id', '$LastActionTime', '$orderId')";
        $connection->query($sql);
        header("Location: /engine/pages/payment?log=Вы%20пополнили%20счёт");
        mysqli_close($connection);
        exit;
    } else {
        header("Location: /page/error-pay");
        mysqli_close($connection);
        exit;
    }
}
echo json_encode($response, JSON_UNESCAPED_UNICODE); 
if(isset($connection)) mysqli_close($connection);
?>