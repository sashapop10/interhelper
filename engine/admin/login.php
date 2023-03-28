<?php
    session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    if(isset($_SESSION["admin"])) { header("Location: /engine/admin/users");  exit; }  
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/admin_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
    <section class='ASSISTENT_LOGIN_CONTAINER'>
        <h2 class='header1' style='margin-bottom:20px;'>Вход на страницу админа</h2>
        <form class='send_ajax_form_assistent assistent_login_form_container' >
            <div id='ASSISTENT_LOGIN_BLOCK'>
                <h2>Введите логин:</h2>
                <input name='login' class ='ASSISTENT_INPUT' />
            </div>
            <div  id='ASSISTENT_PASSWORD_BLOCK'>
                <h2>Введите пароль:</h2>
                <input type='password' name='pass' class='ASSISTENT_INPUT' />
            </div>
            <button class = 'ASSISTENT_LOGIN_BTN' type='submit'>Войти</button>
        </form>
    </section>
    <?php appendfooter(); ?>
</body>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>