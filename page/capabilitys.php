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
    $tools_photos_path = VARIABLES["photos"]["tools_photos"]["upload_path"];
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <link rel="stylesheet" href="/scss/main_page.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
	<title>INTERHELPER - Больше чем онлайн консультант</title>
	<link rel='shortcut icon' href='/scss/imgs/interhelper_icon.svg' type='image/png'>
</head>
<body>
    <?php head3($file_path, $photo); ?>
    <section class="tools vue_el2" id="tools" style="margin-top:100px;">
        <h1 class="tt1 WhiteBlack">Полный иснтрументарий продаж</h1>
        <div class="tools-row width50p" style="flex-direction:column" v-for="row in fitchas">
            <div class="tool_row wow bounceInUp" style="justify-content:space-between;border-bottom:3px solid #0ae;" data-wow-delay="0s" v-for="fitcha in row" v-if="chet()">
                <span :style="'background:url(<?php echo $tools_photos_path; ?>'+fitcha.photo+') no-repeat center center;background-size:60%; background-color: '+fitcha.color+';'" class="tool-img"></span>
                <div style="display:flex;flex-direction:column;">
                    <p class="tool-info" style="text-align:right;margin-bottom:10px;color:#0ae !important;font-weight:900;font-size:20px;">{{fitcha.name}}</p>
                    <p class="tool-info WhiteBlack" style="text-align:right;">{{fitcha.info}}</p>
                </div>
            </div>
            <div class="tool_row wow bounceInUp" style="justify-content:space-between;border-bottom:3px solid #0ae;" data-wow-delay="0s" v-else>
                <div style="display:flex;flex-direction:column;">
                    <p class="tool-info" style="text-align:left;margin-bottom:10px;color:#0ae !important;font-weight:900;font-size:20px;">{{fitcha.name}}</p>
                    <p class="tool-info WhiteBlack" style="text-align:left;">{{fitcha.info}}</p>
                </div>
                <span :style="'background:url(<?php echo $tools_photos_path; ?>'+fitcha.photo+') no-repeat center center;background-size:60%; background-color: '+fitcha.color+';'" class="tool-img"></span>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
	<?php login_menu(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>