<?php
     session_start();
     $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
     include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
     include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
     if(isset($_SESSION["boss"])){ 
         $user_id = $_SESSION["boss"];
         $sql = "SELECT photo, count(1) FROM users WHERE id = '$user_id'";
         $results = attach_sql($connection, $sql, 'row');
         if(intval($results[1]) != 0) $photo = str_replace(' ', '%20', $results[0]); 
         else {
             unset($_SESSION['boss']); 
             header("Location: /index"); exit;
         }
     }
     if(!isset($photo)) $photo = '';
     $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
     mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <link rel="stylesheet" href="/scss/main_page.css">
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/scss/libs/animate.css">
	<link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <title>INTERHELPER - Больше чем онлайн консультант</title>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
    <?php head3($file_path, $photo);?>
    <section id="api_section">
        <h1 class="WhiteBlack tt1">Interhelper API для разработчиков</h1>
        <p class="tt1" style="color:#0ae ;">Работа с CRM</p>
        <p class="WhiteBlack td1">Для  работы с API, вам необходимо отправить POST запрос на ссылку <span style="color:#0ae;text-transform:initial;">https://api.interfire.ru:5321/client</span></p>
        <p class="WhiteBlack td1">Пример на <span style="color:#0ae;">PHP</span></p>
        <pre class="WhiteBlack code_block">
            $json["login"] = "Ваш логин"; <span style="color:lightgreen;">// Ваш логин</span>
	        $json["password"] = "Ваш пароль"; <span style="color:lightgreen;">// Ваш пароль</span>
            $json["type"] = "add"; <span style="color:lightgreen;">// add - записать, get - получить, csv - получить в формате csv</span>
            $json["info"]["columns"] = [<span style="color:lightgreen;"> // (только для add) Заполняем информацию колонка - значение</span>
                "Колонка" => "значение", 
                "Колонка" => "значение"
            ]; 
            $json["info"]["table"] = "table"; <span style="color:lightgreen;">// Ваша таблица</span>
	    $json = json_encode($json, JSON_UNESCAPED_UNICODE);  <span style="color:lightgreen;">// Превращаем в json</span>
            $ch = curl_init('https://api.interfire.ru:5321/client');
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($json)));
            $data = curl_exec($ch); <span style="color:lightgreen;">// Получаем ответ от сервера</span>
            curl_close($ch);
            echo $data;
        </pre>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src="/scripts/router?script=main"></script>
</body>
</html>