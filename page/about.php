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
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/scss/main_page.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <title>INTERHELPER - Больше чем онлайн консультант</title>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
    <?php head3($file_path, $photo);?>
    <section id="about_section" style="margin-top:120px">
        <h1 class="tt1 WhiteBlack wow bounceInUp" data-wow-delay="0.1s" style="margin-top:20px;">Наша цель</h1>
        <div class="about_info" >
            <span class="about_photo target wow slideInLeft" data-wow-delay="0.15s"></span>    
            <p class="WhiteBlack about_text wow slideInRight" data-wow-delay="0.15s" style="text-align:start;">
                Компания <a href="https://interfire.ru" style="color:#0ae;">interfire</a> разработала инструмент по превращению посетителей в клиентов или покупателей. 
                Наша задача - максимально упростить жизнь своим клиентам.
                Мы используем проверенные решения для организации наших услуг, предоставляем актуальные <a style="color:#0ae;" href="/page/capabilitys">инструменты</a> для повышения лояльности к клиенту и конверсии с сайта.
            </p>
        </div>
        <h2 class="tt1 WhiteBlack wow bounceInUp" style="margin-top:20px;"  data-wow-delay="0.2s">Подход к работе</h2>
        <div class="about_info" >
            <span class="about_photo solution wow slideInLeft" data-wow-delay="0.25s"></span>
            <p class="WhiteBlack about_text wow slideInRight" data-wow-delay="0.25s" style="text-align:start;">
                Interhelper использует самые современные технологии и подходы к формированию IT-услуг. 
                Компания стремится удовлетворить любые запросы клиентов. А наши пользователи могут быть уверены в качестве предоставляемых услуг.
                Наши пользователи могут спать спокойно и не опасаться за сохранность данных своих клиентов и сотрудников. 
                Если Вы зададитесь вопросом «почему?», то ответ будет прост – наша компания работает в соответствии с Федеральным законом 152-ФЗ «О персональных данных».
            </p>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src="/scripts/router?script=main"></script>
</body>
</html>