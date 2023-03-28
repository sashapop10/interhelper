<?php
    session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if(!isset($_GET["id"]) || !isset($_GET["group"])){ header("Location: /page/help"); exit;}
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
    $faq_group = $_GET["group"];
    $faq_id = str_replace("%20", " ", $_GET["id"]);
    $sql = "SELECT info FROM faq WHERE name = '$faq_id' and faq_group = '$faq_group'";
    $results = attach_sql($connection, $sql, 'row')[0];
    $faq_res = json_decode($results, JSON_UNESCAPED_UNICODE);
    if(isset($_GET["list_id"])){ 
        $faq_innerid = str_replace("%20", " ", $_GET["list_id"]);
        if(isset($faq_res["list"][$faq_innerid]["video"])) $faq_video = htmlspecialchars_decode($faq_res["list"][$faq_innerid]["video"], ENT_QUOTES);
        else $faq_video = null;
        $faq_res = htmlspecialchars_decode($faq_res["list"][$faq_innerid]["answer"], ENT_QUOTES);
    } else{ 
        if(isset($faq_res["video"])) $faq_video = htmlspecialchars_decode($faq_res["video"], ENT_QUOTES);
        else $faq_video = null;
        $faq_res = htmlspecialchars_decode($faq_res["answer"], ENT_QUOTES);
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
    <link rel="stylesheet" href="/scss/animate.css">
	<link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <title>INTERHELPER - Больше чем онлайн консультант</title>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
    <?php head3($file_path, $photo);?>
    <section id="about_section" style="margin-top:120px">
        <h1 class="tt1 WhiteBlack wow bounceInUp" data-wow-delay="0.1s" style="margin-top:20px;">
        <?php 
            if(!isset($faq_innerid)) echo $faq_id;
            else echo $faq_innerid;
        ?>
        </h1>
        <?php 
            if(isset($faq_video) && trim($faq_video) != '') echo '
            <div style="width:100%;display:flex;align-items:center;justify-content:center;margin-top:20px;margin-bottom:20px;">
                <iframe allowfullscreen width="560" height="315" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" src="'.$faq_video.'" frameborder="0"></iframe>
            </div>  
            ';
        ?>
        <p style="margin-top:20px;text-align:center;font-size:20px;font-weight:bold;" class="WhiteBlack wow bounceInUp" data-wow-delay="0.15s"><?php print_r($faq_res); ?></p> 
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src="/scripts/router?script=main"></script>
    <script src='/scripts/libs/vue.js'></script>
</body>
</html>