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
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
    <script type="text/javascript" src="/HelperCode/Helper"></script>
	<?php navigation('anticlicker', $info); ?>
    <section id='container'>
        <?php section_header('Антискликиватель', 'no-touch.png'); ?>
        <div id = 'column1' style="margin:20px;" >
            <h2 class='header1 wow bounceInUp'>Антискликиватель</h2>
            <div style='display:inline-flex;align-items:center;justify-content:space-between;margin-top:20px;' class='wow bounceInUp' data-wow-delay='0.1s'>
                <p class='WhiteBlack' style='font-size:20px;'>Включить автоматичсекий бан</p>
                <span style='margin-left:10px;' @click='autoban_enabled = !autoban_enabled; btns("adds_autoban");' class='check_btn'><span :class='[{"checked_btn_span": !autoban_enabled}, {"unchecked_btn_span": autoban_enabled}]'></span></span>
            </div>
            <div style='display:inline-flex;align-items:center;justify-content:space-between;margin-top:20px;' class='wow bounceInUp' data-wow-delay='0.15s'>
                <p class='WhiteBlack' style='font-size:20px;'>Включить переадресацию</p>
                <span style='margin-left:10px;' @click='redirect_enabled = !redirect_enabled; btns("adds_redirect");' class='check_btn'><span :class='[{"checked_btn_span": !redirect_enabled}, {"unchecked_btn_span": redirect_enabled}]'></span></span>
            </div>
            <div class='inline_margin_top_20 wow bounceInUp' data-wow-delay='0.2s' style='align-items:center;'>
                <p style='font-size:20px;' class='WhiteBlack'>Максимально кол-во переходов по ссылке</p>
                <input class='changable_input self_opts_inp' type='number' name='adds_trys' @change='btns("adds_trys")' v-model='adds_trys' />
            </div>
            <p style='margin-top:10px;' class='WhiteBlack wow bounceInUp' data-wow-delay='0.25s'>Заблокировано в этом месяце: <span style='color:tomato;'>{{adds_banned}}</span></p>
            <p style='margin-top:10px;' class='WhiteBlack wow bounceInUp' data-wow-delay='0.25s'>Переадресовано в этом месяце: <span style='color:tomato;'>{{adds_redirected}}</span></p>
            <div class='anti-clicker-table wow bounceInUp' style="border-radius:10px;" data-wow-delay='0.3s'> 
                <div class='anti-clicker-table-item '>
                    <div style='font-weight:700;' class='anti-clicker-table-item-part WhiteBlack bgblackwhite'>IP</div>
                    <div style='font-weight:700;' class='anti-clicker-table-item-part WhiteBlack bgblackwhite'>Количество переходов</div>
                    <div style='font-weight:700;' class='anti-clicker-table-item-part WhiteBlack bgblackwhite'>статус</div>
                </div>
                <div class='anti-clicker-table-item' v-for='item in sort_mas(list)' >
                    <div  class='bgblackwhite anti-clicker-table-item-part WhiteBlack'>{{item.ip}}</div>
                    <div  class='bgblackwhite anti-clicker-table-item-part WhiteBlack'>{{item.count}}</div>
                    <div v-if='item.count < adds_trys' class='anti-clicker-table-item-part bgblackwhite WhiteBlack'>лид</div>
                    <div style='color:tomato;' v-else class='anti-clicker-table-item-part bgblackwhite'>заблокирован</div>
                </div>
                <form @click="load_more()" v-if="check_count()" style='cursor:pointer;' class="more_btn bgblackwhite" >
                    <p class="WhiteBlack">Загрузить ещё</p>
                </form>
                <div v-if='Object.keys(list).length == 0' class='anti-clicker-table-nothing bgblackwhite WhiteBlack'>Тут пока никого нет</div>
            </div>
        </div>
    </section>
	<?php appendfooter();?>
    <script src='/scripts/libs/vue.js'></script>
    <script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>