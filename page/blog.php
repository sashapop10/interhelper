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
    $news_photos_path = VARIABLES["photos"]["news_photos"]["upload_path"];
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
    <h1 class="WhiteBlack wow bounceInUp" style="top:110px;position:relative;text-align:center;">Блог</h1>
    <section id="news_section">
        <div v-for="(item, index) in news" class="news_card wow bounceInUp"  data-wow-delay='0.1s'>
            <span class="news_date">{{item.time.split('T')[0].split('-').reverse().join('.')}}</span>
            <span class="news_photo" :style="'background-image:url(<?php echo $news_photos_path; ?>'+item.photo+');'"></span>
            <h2 class="news_name">{{item.name}}</h2>
            <p class="short_info">{{item.short_info}}</p>
            <span class="more_btn" @click="redirect(index)">Подробнее</span>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>