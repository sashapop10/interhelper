<?php 
    session_start();
    class _database{
        public $connection;
        private $DBHOST = '################################';
        private $DBUSER = '################################';
        private $DBPASS = '################################';
        public $DBNAME = '################################';
        public function connect(){
            if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
                $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $location);
                exit;
            }
            if(isset($_SERVER['HTTPS'])) $this->server = "################################";
            else $this->server = "################################";
            $this->connection = @mysqli_connect($this->DBHOST, $this->DBUSER, $this->DBPASS , $this->DBNAME) or die("Нет соединения с базой данных".$this->DBNAME);
            $this->connection->set_charset("utf8") or die("Нет соединения с базой данных");
            date_default_timezone_set("Europe/Moscow");
        }
        public function getDB($sql, $getType){
            $query = mysqli_query($this->connection, $sql);
            if($getType == 'row') return mysqli_fetch_row($query);
            if($getType == 'query') return mysqli_fetch_all($query, MYSQLI_ASSOC);
            return null;
        }
        public function prepareVar($var){ return mysqli_real_escape_string($this->connection, $var); }
        public function pasteDB($sql){ global $status; if($this->connection->query($sql) !== true) $status->error('sql_error'); }
        public function existDB($table, $column, $value){
            $sql = "SELECT count(1) FROM $table WHERE $column = '$value'";
            $count = $this->getDB($sql, 'row')[0];
            return ($count > 0);
        } 
        public function close(){ $this->connection->close(); }
    }
    class _status{
        public $status = ["errors" => [], "success" => []];
        private $ERRORS = [
            "assistent_account_access" => 'Упс, с вашей записью какие то пролемы! Пожалуйста обратитесь в поддержку.',
            "error" => 'Ошибка на стороне сервера !',
            "boss_account_access" => 'Упс, с вашей записью какие то пролемы! Пожалуйста обратитесь в поддержку.',
            "uncorrect_new_pass" => 'Пароль должен быть больше 7 символов и меньше 30!',
            "bad_email" => 'Почта указана неверно !',
            "invalid_new_email" => 'Почта должна быть больше 3 символов и меньше 40!',
            "sql_error" => "Ошибка в sql !",
            "wrong_old_pass" => "Пароли не совпадают!",
            "wrong_new_pass_repeat" => "Неправильный старый пароль!",
            "email_already_exist" => 'Такая почта уже существует!',
            "empty_fields" => 'Поля не заполнены!',
            "domains_limit" => 'Вы превысили количество доменов !',
            "domain_not_exist" => 'Домен не действителен!',
            "domain_already_exist" => 'Домен уже существует!',
            "alot_letters" => 'Превышено ограничение количества символов!',
            "departament_limit" => 'Вы превысили количество созданных отделов !',
            "departament_already_exist" => 'Вы уже создали этот отдел !',
            "not_accepted_file" => 'Не поддерживаемый тип файла. ',
            'so_big_file' => 'Слишком большой файл. ',
            '777_error' => 'Невозможно загрузить фаил в папку. Установите права доступа - 777.',
            'file_load_error' => 'При загрузке возникли ошибки. Попробуйте ещё раз.',
            'review_already_exist' => 'Вы уже оставили отзыв!',
            'assistents_limit' => 'Превышен лимит на создание ассистентов !',
            'create_assistent_repeat' => 'Для повторной отправки подождите 30 секунд !',
            'domain_before' => 'Сначала нужно заполнить домен в разделе - "Получить код"!',
            "assistent_not_exist" => 'Не существующий ассистент!',
            "no_access" => 'Нет доступа!',
            'column_name_repeat' => 'Вы уже создали колонку с таким названием !',
            'limit' => 'Превышен предел вашего тарифа !',
            'no_exist_table' => 'Таблицы не существует !',
            'column_invalid_name' => 'Колонку к таким названием создать нельзя !',
            'not_exist_departament' => 'Ваш отдел больше не существует !',
            'user_not_found' => 'Пользователь не найден !',
            'not_enouth_money_emlpoyee' => 'На балансе аккаунта Вашего босса закончились деньги, чтобы восстановить работу функций, пополните баланс аккаунта !',
            'not_enouth_money_boss' => 'На балансе Вашего аккаунта закончились деньги, чтобы восстановить работу функций, пополните баланс аккаунта !',
            'closed_tariff_boss' => 'Тариф Вашего аккаунта закончился, а денег на его продолжение не хватило, чтобы восстановить работу функций, пополните баланс аккаунта !',
            'closed_tariff_emlpoyee' => 'Тариф аккаунта Вашего босса закончился, а денег на его продолжение не хватило, чтобы восстановить работу функций, пополните баланс аккаунта !',
            'banned_assistent' => 'Ваш аккаунт временно преостановлен !',
            'banned_user' => 'Аккаунт временно преостановлен !',
            'account_not_exist' => 'Аккаунта не существует или Вы не подтвердили почту !',
            'wrong_pass' => 'Неверный пароль или логин !',
            'not_rules' => 'Нет доступа !',
            'so_fast' => 'Слишком быстро, подождите немного перед новой попыткой, наши сервера могут не выдержать..'
        ];
        public function error($error){ array_push($this->status["errors"], $this->ERRORS[$error]); }
        public function print(){ echo json_encode($this->status, JSON_UNESCAPED_UNICODE); }
    }
    class _variables{
        public $today; 
        public $date;
        public $feedback_mail = 'info@interhelper.ru';
        public $smtp_secure = "tls";
        public $smtp_username = "InterHelper";
        public $smtp_port = "587";
        public $smtp_host = "tls://smtp.yandex.ru";
        public $smtp_password = "Dad123kek123!";
        public $smtp_mail = "info@interhelper.ru";
        public  function __construct(){
            $this->today = date('Y-m-d H:i:s');
            $this->date = date('Y-m-d');
        }
    }
    class _server{
        private $server;
    }
    class _methods{
        public function IPOST(){
            $IPOST = $_POST;
            foreach ($IPOST as $index => $value){ 
                if(gettype($IPOST[$index]) == 'string'){
                    $status = $this->isJson($value);
                    if(!$status) $IPOST[$index] = $this->StringPtotection($IPOST[$index]);
                }
            }
            return $IPOST;
        } 
        public function StringPtotection($string){
            return trim(
                str_replace("\\", "&bsol;", 
                    str_replace(array("\r\n", "\r", "\n"), " ", 
                        preg_replace('/\s+/', ' ', 
                            htmlspecialchars(str_replace("`", "'", $string), ENT_QUOTES)
                        )
                    )
                )
            );
        }
        public function isJson($string) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
        public function sendmail(
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
    }
    class _USER{
        public $id;
        public $url = [];
        public $type;
        public $boss_id;
        public $departament;
        public $table;
        public function __construct(){
            global $database, $varaibles;
            if(isset($_SERVER['HTTP_REFERER'])) $_SESSION['url'] = $_SERVER['HTTP_REFERER'];
            $url_parts = explode('/', $_SESSION['url']);
            $this->url['page'] = $url_parts[count($url_parts) - 1];
            $this->url['dir'] = $url_parts[count($url_parts) - 2];
            if(strpos($this->url['page'], '?')) $this->url['page'] = explode('?', $this->url['page'])[0];
            if(isset($_SESSION['employee'])){ 
                $info = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE);
                $this->boss_id = $info['boss_id'];
                $this->id = $info['personal_id'];
                $this->type = "employee";
                $this->table = "employers";
                $status = $database->existDB($this->table, "id", $this->id);
                if(!$status){
                    unset($_SESSION['employee']); 
                    $status->error('assistent_account_access');
                    return;
                } 
                $sql = "UPDATE assistents SET time = '".$varaibles->today."' WHERE id = '".$this->id."'";
                $database->pasteDB($sql);
                $sql = "SELECT departament FROM assistents WHERE id = '".$this->id."'";
                $this->departament = $database->getDB($sql, 'row')[0];
            }
            if(isset($_SESSION['boss'])){
                $this->type = "boss";
                $this->boss_id = $_SESSION['boss'];
                if($this->url['dir'] === 'pages'){ 
                    $this->id = $this->boss_id;
                    $this->dataBase = "users";
                    $this->departament = "InterHelper_Boss";
                }
                $status = $database->existDB("users","id",$this->id);
                if(!$status){
                    unset($_SESSION['boss']); 
                    $status->error('boss_account_access');
                    return;
                } 
                $sql = "UPDATE users SET time = '".$varaibles->today."' WHERE id = '".$this->boss_id."'";
                $database->pasteDB($sql);
            }
            if(!$this->type) $this->type = 'guest';
        }
    }
    class _get_settings{
        public function __construct(){
            global $methods, $database, $status, $varaibles, $user;
            $IPOST = $methods->IPOST();
            if(!isset($IPOST['getSettings'])) return;
            foreach($IPOST['getSettings'] as $setting){
                if($setting == 'editions'){
                    $sql = "SELECT name, tariff, type FROM tariffs";
                    $query = $database->getDB($sql, 'query');
                    $editions = [];
                    foreach($query as $row){
                        if($row["type"] == 'hidden') continue;
                        $editions[$row["name"]] = json_decode($row["tariff"], JSON_UNESCAPED_UNICODE);
                        $editions[$row["name"]]["type"] = $row["type"];
                        if($editions[$row["name"]]['cost']['value'] == 0) $editions[$row["name"]]['cost']['value'] = 'бесплатно';
                    }
                    $status->status['success']['editions'] = $editions;
                } else if($setting == 'reviews'){
                    $sql = "SELECT * FROM reviews ORDER BY rating DESC";
                    $query = $database->getDB($sql, 'query');
                    $reviews = [];
                    foreach($query as $row){
                        if($row["rating"] < 3) continue;
                        $reviews[$row["review_id"]] = [
                            "name" => $row["name"],
                            "link" => $row["link"],
                            "text" => $row["review"],
                            "img" => $row["photo"],
                            "time" => $row["time"],
                            "rating" => $row["rating"],
                        ];
                        $status->status['success']['reviews'] = $reviews;
                    }
                } else if($setting == 'review'){
                    if($user->type != 'boss') continue;
                    $sql = "SELECT * FROM reviews WHERE review_id = '".$user->id."'";
                    $query = $database->getDB($sql, 'row');
                    if(isset($query)) if(isset($query[0])) foreach($query[0] as $option => $value){ $response["success"][$option] = $value; }
                    unset($response["success"]["id"]);
                }
            }
        }
    }
    class _visitor_pages{
        private function contacts(){
            global $methods, $status, $database, $varaibles, $user;
            $IPOST = $methods->IPOST();
            if(isset($IPOST['name']) && isset($IPOST['mail']) && isset($IPOST['message']) && isset($IPOST['topic'])){ // отправили нам письмо
                if(isset($_SESSION["guest_mail_try_time"]) && strtotime($_SESSION["guest_mail_try_time"] . ' +5 minutes') > strtotime($varaibles->today)){
                    $status->error('so_fast');
                    return;
                }
                if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
                    $status->error('bad_email');
                    return;
                }
                $_SESSION["guest_mail_try_time"] = $varaibles->today;
                $methods->sendmail(
                    array(['email' => $varaibles->feedback_mail, 'name' => 'InterHelper']), // адреса + имя
                    "Обращение в контакты",
                    $_POST['name'], 
                    $varaibles->smtp_mail,
                    $varaibles->smtp_password,
                    $varaibles->smtp_secure,
                    $varaibles->smtp_port, 
                    $varaibles->smtp_host,
                    '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
                        <html>
                            <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                <title>Обращение в контакты</title>
                            </head>
                            <body style="width:450px;height:800px;">
                                <div class="pismo" style=" width:450px;height:800px;background: rgb(237,222,237); background: -moz-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: -webkit-linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); background: linear-gradient(90deg, rgba(237,222,237,1) 16%, rgba(9,121,108,0.8354692218684349) 16%, rgba(255,0,250,0.46011908181241246) 100%); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#eddeed",endColorstr="#ff00fa",GradientType=1);">
                                    <img src="https://interhelper.ru/scss/imgs/interhelper_logo.png" alt="InterFire" style="padding:10px;background:#1972F5;border-radius:10px;position: absolute; width: 260px; left: 14px; top: 12px;">
                                    <h1 style="position: absolute; font-size: 25px; font-weight: bold; left: 118px; top: 0px;">Обращение в контакты</h1>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Почта: '.$_POST['mail'].'. </p>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">ID пользователя: '.($user->id ? $user->id : $user->type).'</p>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Имя: '.$_POST['name'].'. </p>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Тема: '.$_POST['topic'].'. </p>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Номер телефона: '.($_POST['phone'] ? $_POST['phone'] : 'Не заполнено').'. </p>
                                    <p style="position: absolute; left: 118px; font-size: 16px; font-weight: bold; top: 75px;">Сообщение: '.$IPOST['message'].'. </p>
                                </div>
                            </body>
                        </html>
                    '
                );
                $status->status['success']['alert'] = 'Письмо отправлено !';
            } else $status->error('empty_fields');
        }
        private function reviews(){
            global $methods, $status, $database, $varaibles, $user;
            $IPOST = $methods->IPOST();
            
        }
        public function __construct(){
            global $user;
            if($user->url['page'] == 'contacts') $this->contacts();
        }
    }
?>