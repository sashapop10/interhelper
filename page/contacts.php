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
    <section id="contacts_section">
        <h1 class="WhiteBlack tt1 wow bounceInUp">Контакты</h1>
        <p class="WhiteBlack td1 wow bounceInUp">Мы будем рады ответить на накопившиеся у вас вопросы, просто заполните форму ниже и мы постараемся их решить.</p>
        <div class="contact_info" >
            <div class="contacts wow slideInRight bgblackwhite WhiteBlack" style="padding:10px;">
                <p>Нужна консультация по продукту?</p>
                <p>Наша команда всегда на связи!</p>
                <p>Звполните форму или свяжитесь с нами другим удобным для вас способом.</p>
            </div>
           <form class="contact_form wow slideInLeft bgblackwhite" onsubmit="send_form()">
                <h2 class="contact_form_h2 WhiteBlack">Ваше имя</h2>
                <input class="contact_from_input" name="name" placeholder="Имя" />
                <h2 class="contact_form_h2 WhiteBlack">Ваша почта</h2>
                <input class="contact_from_input" name="email" placeholder="Почта" />
                <h2 class="contact_form_h2 WhiteBlack">Название ваше организации</h2>
                <input class="contact_from_input" name="organization" placeholder="Название организации *" />
                <h2 class="contact_form_h2 WhiteBlack">Ваше сообщение</h2>
                <textarea class="contact_from_message" name="message" placeholder="Ваше сообщение"></textarea>
                <button class="contact_from_btn" type="submit">Отправить</button>
                <span class="preloader" style="height:40px;width:40px;display:none;margin-top:10px;"></span>
            </form>
            <div class="contacts wow slideInRight bgblackwhite WhiteBlack">
                <h2 class="contacts_name" style="margin:0;">Почта</h2>
                <a class="WhiteBlack" href="mailto:info@interhelper.ru">info@interhelper.ru</a>
                <h2 class="contacts_name">Телефон</h2>
                <a class="WhiteBlack" href="tel:+74951284148" style="word-break:nowrap;">+7 (495) 128-41-48</a>
                <h2 class="contacts_name">Наша студия</h2>
                <a class="WhiteBlack" href="https://interfire.ru/">interfire.ru</a>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src="/scripts/router?script=main"></script>
</body>
</html>