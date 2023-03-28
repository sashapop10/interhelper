<?php
    session_start();
    header('Access-Control-Allow-Origin: *');
    $today = date("Y-m-d H:i:s");
    include 'connection.php';
	include 'func.php';
    global $connection;
	include 'config.php';
    $curl_json = ["login" => VARIABLES["login"], "password" => VARIABLES["password"], "type" => "", "info" => []]; $host = SERVERPATH . '/admin';
    $response = ["errors" => [], "success" => []];
    $lol = $_POST;
    foreach ($lol as $index => $value){ $lol[$index] = htmlencrypt($lol[$index]); }
    if(stop_spam('guest_func')) array_push($response['errors'], 'Тише ковбой, куда такая спешка ? Так наши серва могут и не выдержать..');
    else{
        if(isset($lol["message"]) && isset($_POST["uid"])){ // сообщение
            $uid = $_POST["uid"];
            if(!strrpos($uid, '!@!@2@!@!')) return;
            $domain = stristr($uid, '!@!@2@!@!', true);
            $curl_json["type"] = "guest_message";
            $curl_json["info"] = [
                "domain" => $domain,
                "message" => $lol["message"],
                "uid" => $uid,
                "type" => "message"
            ];
            send_curl($curl_json, $host);
            if(isset($_POST['hostname'])){
                $hostname = $_POST['hostname'];
                $sql = "SELECT settings, name FROM users WHERE id = '$domain'";
                $rows = attach_sql($connection, $sql, 'row');
                $settings = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
                if(!array_key_exists($hostname, $settings['InterHelperOptions'])) $hostname = 'deffault';
                $status = ($settings['InterHelperOptions'][$hostname]['email_msgs_status'] == 'checked');
                $email = $settings['InterHelperOptions'][$hostname]['msgs_email'];
                $name = $rows[1];
                if($status){
                    $data = send_mails(
                        [0 => ['email' => $email, 'name' => $name]], 
                        'У вас новое сообщение, '.$_POST['hostname'].' !', 
                        VARIABLES['smtp_username'], 
                        VARIABLES['smtp_from'], 
                        VARIABLES['smtp_password'], 
                        'ssl',
                        VARIABLES['smtp_port'], 
                        VARIABLES['smtp_host'], 
                        '
                        <html>
                            <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                <title>У вас новое сообщение, '.$_POST['hostname'].'</title>
                            </head>
                            <body style="width:450px;height:300px;">
                                <div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                                    <img src="https://interhelper.ru/scss/imgs/interhelper_logo.png" alt="InterFire" style="background:#0ae;border-radius:10px;padding:10px;position: absolute; width: 125px; left: 14px; top: 12px;">
                                    <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">У вас новое сообщение, '.$_POST['hostname'].' !</h1>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">'.$_POST['message'].'</p>
                                </div>
                            </body>
                        </html>
                        '
                    );
                    if(count($data['errors']) > 0) $response['errors'] = $data['errors'];
                }
            }
        } elseif(isset($_FILES['files']) && isset($_POST["uid"])){ // фото
            $photo = $_FILES['files'];
            $uid = $_POST["uid"];
            if(!strrpos($uid, '!@!@2@!@!')) return;
            $domain = stristr($uid, '!@!@2@!@!', true);
            $data = save_file($photo, "guest_send_photo", null, null, null);
            if($data["access"] == false) $error = error($data["text"]);
            else {
                $curl_json["type"] = "guest_message";
                $curl_json["info"] = [
                    "domain" => $domain,
                    "message" => [$data["text"]],
                    "uid" => $uid,
                    "type" => "photo",
                ];
                send_curl($curl_json, $host);
            }
            if(isset($_POST['hostname'])){
                $hostname = $_POST['hostname'];
                $sql = "SELECT settings, name FROM users WHERE id = '$domain'";
                $rows = attach_sql($connection, $sql, 'row');
                $settings = json_decode($rows[0], JSON_UNESCAPED_UNICODE);
                if(!array_key_exists($hostname, $settings['InterHelperOptions'])) $hostname = 'deffault';
                $status = ($settings['InterHelperOptions'][$hostname]['email_msgs_status'] == 'checked');
                $email = $settings['InterHelperOptions'][$hostname]['msgs_email'];
                $name = $rows[1];
                if($status){
                    $data = send_mails(
                        [0 => ['email' => $email, 'name' => $name]], 
                        'У вас новое сообщение, '.$_POST['hostname'].' !', 
                        VARIABLES['smtp_username'], 
                        VARIABLES['smtp_from'], 
                        VARIABLES['smtp_password'], 
                        'ssl',
                        VARIABLES['smtp_port'], 
                        VARIABLES['smtp_host'], 
                        '
                        <html>
                            <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                <title>У вас новое сообщение, '.$_POST['hostname'].'</title>
                            </head>
                            <body style="width:450px;height:300px;">
                                <div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                                    <img src="https://interhelper.ru/scss/imgs/interhelper_logo.png" alt="InterFire" style="background:#0ae;border-radius:10px;padding:10px;position: absolute; width: 60px; left: 14px; top: 12px;">
                                    <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">У вас новое сообщение, '.$_POST['hostname'].' !</h1>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Отправлен файл.</p>
                                </div>
                            </body>
                        </html>
                        '
                    );
                    if(count($data['errors']) > 0) $response['errors'] = $data['errors'];
                }
            }
            
        } elseif(isset($_POST["get_emojis"])){ // получить emoji
            $emojis = json_encode(emojis($_SERVER['DOCUMENT_ROOT']), JSON_UNESCAPED_UNICODE);
            $response['success'] = ["emojis" => $emojis]; 
        } elseif( // отправка формы
            (!isset($_SESSION["guest_mail_try_time"]) || strtotime($_SESSION["guest_mail_try_time"] . '+5 minutes') <= strtotime($today))
            && isset($_POST["offline_message"]) && isset($_POST["uid"])
            ){ 
            if(isset($_POST["phone"])) $phone = $_POST["phone"];
            else $phone = 'не указано';
            if(isset($_POST["name"])) $name = $_POST["name"];
            else $name = 'не указано';
            if(isset($_POST["email"])) $email = $_POST["email"];
            else $email = 'не указано';
            $uid = $_POST["uid"];
            $message = $_POST["offline_message"];
            if(!strrpos($uid, '!@!@2@!@!')) return;
            $domain = stristr($uid, '!@!@2@!@!', true);
            $sql = "SELECT json_extract(settings, '$.feedbackform') AS settings FROM users WHERE id = '$domain'";
            $rows = attach_sql($connection, $sql, 'row')[0];
            $settings = json_decode($rows, JSON_UNESCAPED_UNICODE);
            $email_getter = $settings["feedbackMAIL"];
            $_SESSION["guest_mail_try_time"] = $today;
            smtpmail('interhelper', $email_getter, 'Запрос обратной связи', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
                <html>
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Запрошена обратная связь</title>
                    </head>
                    <body style="width:450px;height:300px;">
                        <div class="pismo" style=" width:450px;height:300px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                            <img src="http://interfire.ru/img/logo.png" alt="InterFire" style="position: absolute; width: 60px; left: 14px; top: 12px;">
                            <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Запрошена обратная связь</h1>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Имя: '.$name.' </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">почта: '.$email.' </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">номер телефона: '.$phone.' </p>
                            <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Сообщение: '.$message.' </p>
                        </div>
                    </body>
                </html>'
            );
            $curl_json["type"] = "guest_message";
            $curl_json["info"] = [
                "domain" => $domain,
                "message" => $lol["offline_message"],
                "uid" => $uid,
                "email" => (isset($lol["email"]) ? $lol["email"] : ""),
                "phone" => (isset($lol["phone"]) ? $lol["phone"] : ""),
                "name" => (isset($lol["name"]) ? $lol["name"] : ""),
                "type" => "offline_form",
            ];
            send_curl($curl_json, $host);
        } else array_push($response['errors'], ERRORS['empty_fields']);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE); 
    if(isset($connection)) mysqli_close($connection);
?>