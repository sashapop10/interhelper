<?php 
$response = ["errors" => [], "success" => []];
require_once 'connection.php';
include 'func.php';
global $connection;
$today = date('Y-m-d H:i:s');
include 'config.php';
$json = ["login" => VARIABLES["login"], "password"=>VARIABLES["password"], "type"=>"", "info" => []];
$host = SERVERPATH . '/admin';
$accepted_link = "
    <head>
        <script type='text/javascript' src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    </head>
    <p>
        Вы <span style='color:#0ae;'>успешно подтвердили</span> вашу почту ! </br>
        Для входа в личный кабинет перейдите по <a href='/engine/consultant/assistent'>ссылке</a>
    </p>
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
$unaccepted_link = "
    <p>
        Ссылка <span style='color:#f90;'>не дествительна</span> ! </br>
        Для возвращения переёдите по <a href='/index'>ссылке</a>
    </p>
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
if(isset($_GET["hash"])){ // подтверждение почты
    $hash = $_GET["hash"];
    $sql = "SELECT * FROM unconfimed_assistents WHERE hash = '$hash'";
    $results = attach_sql($connection, $sql, 'query');
    $mail = '';
    foreach($results as $result){
        $mail = $result["email"];
        $name = $result["name"];
        $password = $result["password"];
        $domain = $result["domain"];
        $phone = $result["phone"];
        $buttlecry = $result["buttlecry"];
        $departament = $result["departament"];
    }
    if($mail && $name && $password && $domain && $departament){
        if(!isset($buttlecry) || trim($buttlecry) == '') $buttlecry = $name.' , '.$departament; 
        $sql = "SELECT count(1) FROM assistents WHERE email = '$mail'";
        $email_exist = attach_sql($connection, $sql, 'row')[0];
        if($email_exist == 0){
            $sql = "INSERT INTO assistents(id, name, password, email, domain, photo, buttlecry, departament, gmessage) VALUES (0,'".$name."','".$password."','".$mail."','".$domain."', 'user.png','".$buttlecry."','".$departament."', 'false')";
            if ($connection->query($sql) === TRUE) {
                $sql = "SELECT id FROM assistents WHERE domain = '$domain' AND email = '$mail'";
                $user_id = attach_sql($connection, $sql, 'row')[0];
                $json["type"] = "add_assistent";
                $json["info"] = ["id" => $user_id, "hash"=> $hash, "email"=> $mail, "domain" => $domain, "departament" => $departament, "name" => $name, "buttlecry" => $buttlecry, "photo"=> "user.png"];
                send_curl($json, $host);	
                echo $accepted_link;
            }
        } else echo $unaccepted_link;
    } else echo $unaccepted_link;
    exit;
} elseif(isset($_GET['reset'])){ // сброс пароля переход по ссылке
    $hash = $_GET['reset'];
    $password = uniqid('', true);
    $hashpassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "SELECT email FROM assistents_password_reset_keys WHERE hash = '$hash'";
    $results = attach_sql($connection, $sql, 'query');
    foreach($results as $result){ $mail = $result["email"]; }
    if(isset($mail)){
        $sql = "DELETE FROM assistents_password_reset_keys WHERE hash = '$hash' or email = '$mail'";
        $connection->query($sql);
        $sql = "UPDATE assistents SET password = '$hashpassword' WHERE email = '$mail' ";
        $connection->query($sql);
        echo "<p>Ваш новый пароль - <span style='color:#0ae;'>$password</span> </br> Обязательно смените его в личном кабинете ! </br> Для возвращения перейдите по <a href='/engine/consultant/assistent'>ссылке</a></p><style>body{background:#252525; color: #fff; display:flex; justify-content:center; align-items:center;} a{color:#0ae;} p{color:#f90p; font-size:20px;}</style> ";
    } else echo "<p>Ссылка <span style='color:#f90;'>не действительна</span> ! </br> Для возвращения перейдите по <a href='/engine/consultant/assistent'>ссылке</a></p><style>body{background:#252525; color: #fff; display:flex; justify-content:center; align-items:center;} a{color:#0ae;} p{color:#f90p; font-size:20px;}</style> ";
    exit;
} elseif(isset($_POST['reset-password'])){ // сброс пароля
    $email = $_POST['reset-password'];
    $sql = "SELECT email FROM assistents WHERE email = '$email'";
    $results = attach_sql($connection, $sql, 'row');
    $confrim = '';
    if(isset($results)) $confrim = $results[0];
    $sql = "SELECT time FROM assistents_password_reset_keys WHERE email = '$email' ORDER BY assistents_password_reset_keys.time DESC";
    $reg_time = attach_sql($connection, $sql, 'row');
    $today = date('Y-m-d H:i:s');
    if(isset($reg_time)){
        if (strtotime($reg_time[0] . ' +30 seconds') > strtotime($today)) array_push($response['errors'], 'Для повторной отправки подождите 30 секунд !');
    } 
    if($email == $confrim && count($response['errors']) == 0){
        $key = uniqid('', true);
        $key .= $email;
        $key .= date("H:i:s"); 
        $hash = password_hash($key, PASSWORD_BCRYPT);
        $sql = "INSERT INTO assistents_password_reset_keys (id, hash, email, time) VALUES (0, '$hash', '$email', '$today')";
        $connection->query($sql);
        send_mails(array(['email' => $email]), 'Сброс пароля', VARIABLES['InterHelper'], VARIABLES['smtp_from'], VARIABLES['smtp_password'], VARIABLES['smtp_secure'], VARIABLES["smtp_port"], VARIABLES['smtp_host'], '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
            <html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Сброс пароля</title>
                </head>
                <body style="width:450px;height:300px;">
                    <div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                        <img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
                        <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Сброс пароля</h1>
                        <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Сбросьте пароль, перейдя по <a href="http://interhelper.ru/engine/assistent_login?reset='.$hash.'"> ссылке</a>.</p>
                        <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Если это были не вы, немедлено сообщите в службу поддержки !</p>
                    </div>
                </body>
            </html>'
        );
        $response['success'] = ["response" => "Письмо для сброса пароля отправлено вам на почту !"];
    } else array_push($response['errors'], 'Такой почты не существует !');
} elseif(isset($_POST['login']) && isset($_POST['password'])){ // вход
    $assistent_login = mb_strtolower($_POST['login']);
    $assistent_pass = $_POST['password'];
    $sql = "SELECT assistents.password, users.money, users.id AS boss_id, assistents.id AS personal_id FROM assistents LEFT JOIN users  ON (assistents.domain = users.id) WHERE assistents.email = '$assistent_login' ";     
    $rows = attach_sql($connection, $sql, 'query');
    if(isset($rows[0]["password"])){
        if(password_verify($assistent_pass, $rows[0]["password"])){
            if($rows[0]["money"] >= 0){
                session_start();
                $_SESSION["employee"] = json_encode(["personal_id" => $rows[0]['personal_id'], "boss_id" => $rows[0]['boss_id']], JSON_UNESCAPED_UNICODE);
                $response['success'] = ["link" => "/engine/consultant/assistent"];
                if(isset($_SESSION['boss'])){
                    $prevbossid = $_SESSION['boss'];
                    if($rows[0]['boss_id'] != $prevbossid){
                        unset($_SESSION["boss"]);
                        $response['success']['response'] = "Выполнен вход в аккаунт ассистента, однако он привязан к другому владельцу, поэтому, в целях безопасности, мы выполнили выход с вашего основного аккаунта.";
                    }
                }
                $_SESSION["employee_login_time"] = $today;
                $emp_id = $rows[0]['personal_id'];
                $sql = "UPDATE assistents SET login_time = '$today' WHERE id = '$emp_id'";
                if($connection->query($sql) === true){
                    $json["type"] = "reload_assistent";
                    $json["info"] = ["id" => $rows[0]['personal_id']];
                    send_curl($json, $host);
                } else { 
                    unset($response['success']);
                    unset($_SESSION["employee"]);
                    unset($_SESSION["employee_login_time"]);
                    $response['success'] = array();
                    array_push($response['errors'], ERRORS['sql_error']);
                }
            } else array_push($response['errors'], ERRORS['not_enouth_money_emlpoyee']);
        } else array_push($response['errors'], ERRORS['wrong_pass']);
    } else array_push($response['errors'], ERRORS['wrong_pass']);
} elseif(isset($_POST['exit'])){ // выход
    session_start();
    unset($_SESSION['employee']);
	$response['success'] = ["link"=>"/engine/consultant/login"];
} else array_push($response['errors'], ERRORS['empty_fields']);
echo json_encode($response, JSON_UNESCAPED_UNICODE); 
if(isset($connection)) mysqli_close($connection);
?>