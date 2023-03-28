<?php
	session_start();
    if (!isset($_SESSION["admin"])) { header("Location: /engine/admin/login");  exit; }
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . '/engine/config.php');
	$variables = VARIABLES;
    unset($variables["password"]);
    unset($variables["photos"]);
    $variables = json_encode($variables, JSON_UNESCAPED_UNICODE);
    mysqli_close($connection);
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
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
</head>
<body>
	<?php admin_navigation('variables'); ?>
    <section id='container'>
        <?php section_header('переменные', 'variables.png'); ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Управление переменными</h2>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.15s'>Авторизация</h2>
            <div class='wow bounceInUp' data-wow-delay='0.2s' style='display:inline-flex;margin-top:15px;align-items:center;'><h2 class='WhiteBlack'>Логин</h2><input @change="change('login')" class='changable_input WhiteBlack' style='margin-left:10px;margin-top:0;' type='text' :value='mas.login'/></div>
            <div id='passwordblock' class='wow bounceInUp' data-wow-delay='0.25s'>
                <h2 class='WhiteBlack'>Пароль</h2>
                <p id='changepass' @click='pass = !pass' v-if='!pass'>Сменить пароль</p>
                <form @submit.prevent='changepass' v-else class='ajax_change_form' method='post' action='/engine/admonSettings' >
                    <div id=pass_top>
                        <input style='border:2px solid #0ae;' name='oldpass' type='password' placeholder='Старый пароль' />
                        <input style='border:2px solid #0ae;' name='newpass'  type='password' placeholder='Новый пароль' />
                        <input style='border:2px solid #0ae;' name='repeatnewpass'  type='password' placeholder='Повторите новый пароль' />
                    </div>
                    <div id='pass_floor' >
                        <button type='submit'>Сменить пароль</button>
                        <p @click='pass = !pass' id='cancelpass' >Отмена</p>
                    </div>
                </form>
            </div>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.3s'>Переменные</h2>
            <div class='wow bounceInUp' v-if="index != 'photos' && index != 'password'" data-wow-delay='0.35s' style='display:inline-flex;margin-top:15px;align-items:center;' v-for='(item, index) in mas'>
                <h2 class='WhiteBlack'>{{index}}</h2>
                <input @change="change(index)" class='changable_input WhiteBlack' style='margin-left:10px;margin-top:0;' type='text' :value='item'/>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>