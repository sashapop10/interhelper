<?php
	session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php");
    $tariff_path = VARIABLES["photos"]["tariff_photo"]["upload_path"];
	$buy_tarif = "$('#loginingmenu').css('display', 'block'); containers.addClass('right-panel-active');";
	if(isset($_SESSION["boss"])){ 
		$buy_tarif = "window.location.href= '/engine/pages/tariff'";
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
    $edition = "Стартовый";
    if(isset($_GET["tariff"])) $edition = $_GET["tariff"];
    if(!array_key_exists($edition, EDITIONS)) $edition = "Стартовый";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
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
    <section class="price vue_el1">
        <div class="container" style="margin-top:50px;">
            <div class="price-wrapper" v-if="editions.hasOwnProperty('<?php echo $edition; ?>')"> 
                <h3 class="tt1 WhiteBlack">Выбранный тариф - {{editions['<?php echo $edition; ?>'].name}}</h3>
                <p class="td1 WhiteBlack">Можно выбрать тариф на ваше усмотрение</p>
                <div class="row" style="justify-content:center;">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="price-block wow bounceInUp bgblackwhite">
                            <img :src="'<?php echo $tariff_path; ?>'+editions['<?php echo $edition; ?>'].img" alt="" class="price-img wow bounceInUp" data-wow-delay="0.1s">
                            <h4 class="WhiteBlack">{{editions['<?php echo $edition; ?>'].name}}</h4>
                            <h5  class="WhiteBlack">{{editions['<?php echo $edition; ?>'].cost.value + ' ' + editions['<?php echo $edition; ?>'].cost.text}}</h5>
                            <div class="price-adv">
                                <p class="price-adv-block WhiteBlack" v-for="tarif_fitcha in editions['<?php echo $edition; ?>'].include">{{tarif_fitcha.text_before + ' ' + tarif_fitcha.value + ' ' + tarif_fitcha.text}}</p>
                            </div>
                            <a href="#" onclick="<?php echo $buy_tarif; ?>">Подключить</a>
                        </div>
                    </div>
					<div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
						<div class="price-block wow bounceInUp bgblackwhite">
							<h2 class="tt1 WhiteBlack">О тарифе</h2>
							<p class="td1  WhiteBlack">{{editions['<?php echo $edition; ?>'].personal_page_info.tarif_text}}</p>
						</div>
					</div>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
	<?php login_menu(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>