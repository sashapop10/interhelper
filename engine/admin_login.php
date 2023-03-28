<?php 
$response = ["errors" => [], "success" => []];
require_once 'connection.php';
include 'func.php';
global $connection;
include 'config.php';
// вход
if(isset($_POST['login']) && isset($_POST['pass'])){
    $login = $_POST['login'];
    $pass = $_POST['pass'];
    if($login == VARIABLES["login"]){
        if(password_verify($pass, VARIABLES["password"])){
            session_start();
            $_SESSION["admin"] = $login;
            $response['success'] = ["reload" => true];
        } else array_push($response['errors'], 'Не верный логин или пароль!');
    } else array_push($response['errors'], 'Не верный логин или пароль!');
} elseif(isset($_POST['exit'])){ // выход
    session_start();
    unset($_SESSION['admin']);
    $response['success'] = ["reload" => true];
} else array_push($response['errors'], ERRORS['empty_fields']);
echo json_encode($response, JSON_UNESCAPED_UNICODE); 
if(isset($connection)) mysqli_close($connection);
?>