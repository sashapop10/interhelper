<?php
	session_start();
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"]) && !isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$info = check_user($connection);
	if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
	if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/client_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/HelperCode/Helper"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php navigation('offline', $info); ?>
    <section id='container'>
        <?php section_header('Оффлайн форма', 'leftImg6.png'); ?>
        <div id = 'column1'>
            <div id='innerColumn'>
                <div class='wow bounceInUp' data-wow-delay='0.1s'>
                    <span style='margin-left:10px;' @click='change_btn("feedback_form_checkbox")' class='check_btn'><span :class='[{"checked_btn_span": !feedback_form_checkbox}, {"unchecked_btn_span": feedback_form_checkbox}]'></span></span>
                    <p class='WhiteBlack'>Включить форму обратной связи</p>
                </div>
                <h2 class='header1 wow bounceInUp' data-wow-delay='0.15s'>Сообщение обратной формы.</h2>
                <div class='wow bounceInUp' data-wow-delay='0.2s'>
                    <textarea @change="change('feedback_text')" name='feedback_text' class='changable_input' style='border-radius:10px;margin-top:10px;height:200px;background:rgba(0,0,0,0.8);color:#fff;width:350px;'>{{feedback_text}}</textarea>
                </div>
                <div class='wow bounceInUp' data-wow-delay='0.25s' style='display:flex;flex-direction:column;align-items:flex-start; margin-left:0;'>
                    <input id='minp' @change="change('feedback_target_email')" name='feedback_target_email' class='changable_input' style='border-radius:10px;background:rgba(0,0,0,0.8);width:auto; color:#fff;border-bottom:3px solid #0ae;width:350px;' v-model='feedback_target_email' type='mail' />
                    <p class='WhiteBlack' style='margin-top:20px; margin-left:0;'>Адрес электронной почты, на который отправляются сообщения посетителей из формы обратной связи.</p>
                </div>
                <div class='wow bounceInUp' data-wow-delay='0.3s'>
                    <span style='margin-left:10px;' @click='change_btn("feedback_input_checkbox_1")' class='check_btn'>
                        <span :class='[{"checked_btn_span": !feedback_input_checkbox_1}, {"unchecked_btn_span": feedback_input_checkbox_1}]'></span>
                    </span>
                    <p class='WhiteBlack'>Отобразить поле ввода имени</p>
                </div>
                <div class='wow bounceInUp' data-wow-delay='0.35s'>
                    <span style='margin-left:10px;' @click='change_btn("feedback_input_checkbox_2")' class='check_btn'>
                        <span :class='[{"checked_btn_span": !feedback_input_checkbox_2}, {"unchecked_btn_span": feedback_input_checkbox_2}]'></span>
                    </span>
                    <p class='WhiteBlack'>Отобразить поле ввода номера телефона</p>
                </div> 
                <div class='wow bounceInUp' data-wow-delay='0.4s'>
                    <span style='margin-left:10px;' @click='change_btn("feedback_input_checkbox_3")' class='check_btn'>
                        <span :class='[{"checked_btn_span": !feedback_input_checkbox_3}, {"unchecked_btn_span": feedback_input_checkbox_3}]'></span>
                    </span>
                    <p class='WhiteBlack'>Отобразить поле ввода E-mail</p>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>