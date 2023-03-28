<?php
    session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	if(isset($_SESSION["loginkey"]) && $_SESSION["loginkey"] != ''){ 
		$user_mail = $_SESSION["loginkey"];
		$sql = "SELECT photo, count(1) FROM users WHERE email = '$user_mail'";
		$results = attach_sql($connection, $sql, 'row');
        if(intval($results[1]) != 0) $photo = str_replace(' ', '%20', $results[0]); 
        else {
            unset($_SESSION['boss']); 
            header("Location: /index"); exit;
        }
	}
    if(!isset($photo)) $photo = '';
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
    if(isset($_GET['id'])) $news_index = $_GET['id'];
    else {header("Location: /page/blog"); exit;}
    if(isset(NEWS[$news_index])){
        $news = NEWS[$news_index];
        $news_photo = $news["photo"];
        $name = $news["name"];
        $news_photos_path = VARIABLES["photos"]["news_photos"]["upload_path"];
        $sql = "SELECT info FROM news WHERE news_id = '$news_index'";
        $query = mysqli_query($connection, $sql);
        $results = mysqli_fetch_row($query);
        $text = htmlspecialchars_decode($results[0], ENT_QUOTES);
    } else {header("Location: /page/blog"); exit;}
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
    <section id="self_news_section">
        <div class="news_photo_bg" style="background-image:url(<?php echo  $news_photos_path.$news_photo; ?>)">
            <h1 class="news_h2 wow bounceInUp"><?php echo $name; ?></h1>
        </div>
        <div class="news_block WhiteBlack">
            <div style="width:80%;word-break:break-word;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;">
                <?php echo $text; ?>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>