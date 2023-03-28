<?php
    session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    if(isset($_SESSION["ASSISTENT_loginkey"]) || isset($_SESSION['employee'])) { header("Location: /engine/consultant/assistent");  exit; }  
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/consultant_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
    <section class='ASSISTENT_LOGIN_CONTAINER'>
        <h2 class='header1' style='margin-bottom:20px;'>Вход на страницу сотрудника</h2>
        <form onsubmit="assistent_login(event);" class='send_ajax_form_assistent assistent_login_form_container' >
            <div  id='ASSISTENT_LOGIN_BLOCK'>
                <h2>Введите логин(почту) сотрудника:</h2>
                <input name='login' class ='ASSISTENT_INPUT' />
            </div>
            <div  id='ASSISTENT_PASSWORD_BLOCK'>
                <h2>Введите пароль сотрудника:</h2>
                <div style="width:300px;position:relative;">
                    <input type='password' name='password' class='ASSISTENT_INPUT' />
                    <span class="password_eye"></span>
                </div>
            </div>
            <button class = 'ASSISTENT_LOGIN_BTN' type='submit'>Войти</button>
            <div style='margin-top:100px;display:inline-flex;'>
                <p style='color:#fff;'>Нет сотрудника? Создайте его по ссылке </p><a href='/engine/pages/assistents' style='text-decoration:none;margin-left:10px; color:#0ae;text-transform:uppercase'> здесь</a><p>!</p>
            </div>
            <div style='margin-top:20px;display:inline-flex;'>
                <p> Забыли пароль ? Восстановите его перейдя по <a onclick='remember_pasword()' style='cursor:pointer;text-decoration:none;margin-left:10px; color:#f90;text-transform:uppercase' >ссылке</a></p>
            </div>
            <div style='margin-top:20px;display:inline-flex;'>
                <p>Вернуться на <a href='/index' style='text-decoration:none;margin-left:10px;margin-right:5px; color:#f90;text-transform:uppercase' > главную</a> или вернуться <a href='javascript:history.go(-1)' style='text-decoration:none;margin-left:10px; color:#0ae;text-transform:uppercase' >назад</a></p>
            </div>
        </form>
    </section>
</body>
<script type="text/javascript" src="/scripts/router?script=main"></script>
<?php appendfooter(); ?>
</html>