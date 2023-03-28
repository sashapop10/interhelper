<?
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php");
global $connection;
$response = ["errors" => [], "success" => []];
$today = date('Y-m-d H:i:s');
if( isset($_POST['message']) && isset($_POST['email'])){
    if(!isset($_SESSION["guest_mail_try_time"]) || strtotime($_SESSION["guest_mail_try_time"] . ' +5 minutes') <= strtotime($today)){ 
        $_SESSION["guest_mail_try_time"] = $today;
        if(isset($_SESSION["boss"])){
            $type = 'босс';
            $id = $_SESSION["boss"];
        } else if(isset($_SESSION["employee"])){
            $type = 'ассистент';
            $id = $_SESSION["employee"];
        } else {
            $type = 'гость';
            $id = 'Вход не выполнен';
        }
        $mail = $_POST['email'];
        $message = $_POST['message'];
        if(isset($_POST['name'])){
            $name = $_POST['name'];
            if(isset($_POST['organization'])) $organization = $_POST['organization'];
            else $organization = 'не заполнено';
            smtpmail('interhelper', $config['smtp_from'], 'Обращение в контакты', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
                <html>
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Обращение в контакты</title>
                    </head>
                    <body style="width:450px;height:800px;">
                        <div class="pismo" style=" width:450px;height:800px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                            <img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
                            <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Обращение в контакты</h1>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Почта: '.$mail.'. </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Почта с которой был выполнен вход ('.$type.'): '.$id.'. </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Имя: '.$name.'. </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Организация: '.$organization.'. </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Сообщение: '.$message.'. </p>
                        </div>
                    </body>
                </html>'
            );
        } else {
            smtpmail('interhelper', $config['smtp_from'], 'Обращение в службу поддержки', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
                <html>
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Обращение в службу поддержки</title>
                    </head>
                    <body style="width:450px;height:800px;">
                        <div class="pismo" style=" width:450px;height:800px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                            <img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
                            <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Обращение в службу поддержки</h1>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Указанная почта: '.$mail.'. </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Почта с которой был выполнен вход ('.$type.'): '.$id.'. </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Сообщение: '.$message.'. </p>
                        </div>
                    </body>
                </html>'
            );
        }
        $response['success'] = ["response" => "Вы отправили форму, ждите ответ на указанную почту."]; 
    } else array_push($response['errors'], 'Вы уже отправили форму ! Ждите ответ на электронную почту !');
} else array_push($response['errors'], 'Заполнены не все поля !');
echo json_encode($response, JSON_UNESCAPED_UNICODE); 
if(isset($connection)) mysqli_close($connection);
?>
