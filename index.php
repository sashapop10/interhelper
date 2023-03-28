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
            mysqli_close($connection);
            header("Location: /index"); exit;
        }
	}
    $tariff_path = VARIABLES["photos"]["tariff_photo"]["upload_path"];
    $reviews_photos_path = VARIABLES["photos"]["reviews_photos"]["upload_path"];
    $tools_photos_path = VARIABLES["photos"]["tools_photos"]["upload_path"];
    if(!isset($photo)) $photo = '';
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	$sql = "SELECT count(email) FROM users";
	$count = attach_sql($connection, $sql, 'row')[0] + 100000 + 516;
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="0c2d5ab7afbec199" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="/scss/libs/slick.css">
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
	<link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <title>INTERHELPER - Больше чем онлайн консультант</title>
    <link rel="stylesheet" href="/scss/libs/animate.css">
	<script type="text/javascript" src="/HelperCode/Helper"></script>
    <link rel="stylesheet" href="/scss/main_page.css">
</head>
<body>
    <?php head3($file_path, $photo); ?>
    <header id="header" class="header">
        <div class="container">
            <div class="row header-top">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                    <div class="header-title" style="text-align:center">
                        <h1 class="wow bounceInDown" data-wow-delay="0.1s">Cамый простой</h1>
                        <h1 class="wow bounceInDown" data-wow-delay="0.5s">способ <span>увеличить</span></h1>
                        <h1 class="wow bounceInDown " data-wow-delay="1s"><span>онлайн-продажи</span></h1>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 wow bounceInDown">           
                    <div class="header-mail">
                        <button class="input_sigup" onclick="ym(86636315,'reachGoal','register')">Зарегистрироваться</button>
                        <input type="mail" placeholder="Введите ваш e-mail">
                    </div>
                    <div class="header-desc">
                        <span>InterHelper - </span>все для общения с клиентами в online: чат на сайт (онлайн консультант), телефония и обратные звонки, прием сообщений из соцсетей, мессенджеров, приложений и e-mail, корпоративный чат и встроенные CRM-возможности.
                    </div>
                </div>
            </div>
            <h3 class="header-mtitle">Преимущества</h3>
            <div class="row header-advan">   
                <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="header-adv-block wow slideInLeft">
                        <h3>Надёжность</h3>
                        <p>С нами вы не потеряете ни одного непонятливого клиента!</p>
                        <img src="/scss/imgs/a1.png" alt="">
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="header-adv-block wow bounceInUp">
                        <h3>Скорость</h3>
                        <p>Общайтесь со своими клиентами без задержек и трудностей!</p>
                        <img src="/scss/imgs/a2.png" alt="">
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="header-adv-block wow slideInRight">
                        <h3>Безопасность</h3>
                        <p>Ваши сообщения никто не сможет прочитать!</p>
                        <img src="/scss/imgs/a3.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section id="fffeeedbaccck">
        <form class="feedaack bgblackwhite">
            <div class="fform_part">
                <h2 class="WhiteBlack">Нужна помощь ?</h2>
            </div>
            <div class="fform_part">
                <div class="fform_part_part">
                    <input name="name" placeholder="Имя" type="text">
                    <input name="phone" placeholder="Телефон" type="text">
                    <button type="submit">Позвоните мне</button>
                </div>
                <div class="fform_part_part">
                    <p class="WhiteBlack" style="font-size:22px;">Заполните форму мы вам перезвоним</p>
                    <p class="WhiteBlack" style="margin-top:15px;font-size:22px;">Или напишите нам в Whatsapp</p>
                    <a class="whaaaatsaaap" href="whatsapp://send?text=&phone=+79261994471&abid=+79261994471">Написать в Whatsapp</a>
                </div>
            </div>
        </form>
    </section>
    <section class="why ">
        <div class="container">
            <div class="why-wrapper">
                <h3 class="tt1 WhiteBlack">Ни одно обращение не останется без ответа </h3>
                <p class="td1 WhiteBlack">Все наши инструменты созданы, чтобы работать с сотнями обращений клиентов каждый день</p>
                <img class="wow bounceInUp" src="/scss/imgs/ab-img.png" alt="">
            </div>
        </div>
    </section>
    <section class="price vue_el1" id="tarifs">
        <div class="container">
            <div class="price-wrapper">
                <h3 class="tt1 WhiteBlack">Наши тарифы</h3>
                <p class="td1 WhiteBlack">Можно выбрать тариф на ваше усмотрение</p>
                <div class="row" style="justify-content:center;">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4" v-for="(tarif, index) in editions" v-cloak>
                        <div class="price-block wow bounceInUp bgblackwhite">
                            <!-- <img :src="'<?php echo $tariff_path; ?>' + tarif.img" alt="" class="price-img wow bounceInUp" data-wow-delay="0.1s"> -->
                            <h4 class="WhiteBlack">{{tarif.name}}</h4>
                            <h5  class="WhiteBlack">{{tarif.cost.value + ' ' + tarif.cost.text}}</h5>
                            <h6 style='text-align:center;' class="WhiteBlack">{{tarif.personal_page_info.tarif_text}}</h6>
                            <a :href="'/page/tariff?tariff='+index+''" onclick="ym(86636315,'reachGoal','podrobneetarif')">Подробнее</a>
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="WhiteBlack">Если не хватает наших тарифов или функций, согласуем тариф под ключ.</h4>
        </div>
    </section>
    <section class="tools vue_el2" id="tools">
        <h2 class="tt1 WhiteBlack">Полный инструментарий продаж</h2>
        <div class="tools-row" v-for="row in fitchas">
            <div class="tool wow bounceInUp" data-wow-delay="0s" v-for="fitcha in row" v-cloak>
                <span :style="'background:url(<?php echo $tools_photos_path; ?>'+fitcha.photo+') no-repeat center center;background-size:60%; background-color: '+fitcha.color+';'" class="tool-img"></span>
                <p class="tool-info WhiteBlack">{{fitcha.name}}</p>
            </div>
        </div>
        <button class="sigup more-button wow bounceInUp" data-wow-delay="1.1s" onclick="window.location.href = '/page/capabilitys'">подробнее</button>	
    </section>
    <section class="try wow bounceInDown">
        <div class="try-wrapper bgblackwhite">
            <h3 class="tt1 WhiteBlack">Зарегистрируйтесь </br> и попробуйте InterHelper</h3>
            <p class="td1 WhiteBlack">Это бесплатно и займёт несколько минут</p>
            <div class="try-form">
                <input type="mail"  placeholder="Введите вашу почту">
                <button class="input_sigup" onclick="ym(86636315,'reachGoal','register')">Регистрация</button>
            </div>
        </div>
    </section>
    <section class="connection_counter">
        <div id="counter_block">
            <h2 class="WhiteBlack" style="text-align:center;font-weight:bold;"> <span style="color:#0ae;">InterHelper</span> уже установлен на</h2>
            <div id="counter">
                <div class="number-ticker WhiteBlack" data-value="<?php echo $count?>"></div>
            </div>
            <h2 class="WhiteBlack" style="text-align:center;">сайтах по всей России</h2>
        </div>
    </section>
    <section class="rew wow bounceInUp bgblackwhite" id="rew">
        <div class="container">
            <div class="rew-wrapper">
                <h3 class="tt1 WhiteBlack">Отзывы</h3>
                <p class="td1 WhiteBlack">Отзывы наших постоянных клиентов</p>
                <div class="rew-slider vue_el3">
                    <div class="rew-slider-block" v-for="review in reviews" v-cloak>
                        <a class="rew-slider-img wow bounceInUp" :href="review.link" data-wow-delay="0.5s" :style="'background-image: url(<?php echo $reviews_photos_path; ?>'+review.img+');cursor:pointer;background-size:90%;background-repeat:no-repeat;background-position:center;background-color:#fff;'"></a>
                        <div class='rating'>
                            <a class="wow bounceInUp" data-wow-delay="0.1s" :style='{color: review.rating > 0 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                            <a class="wow bounceInUp" data-wow-delay="0.2s" :style='{color: review.rating > 1 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                            <a class="wow bounceInUp" data-wow-delay="0.3s" :style='{color: review.rating > 2 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                            <a class="wow bounceInUp" data-wow-delay="0.4s" :style='{color: review.rating > 3 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                            <a class="wow bounceInUp" data-wow-delay="0.5s" :style='{color: review.rating > 4 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                        </div>
                        <a class="WhiteBlack" :href="review.link" style="color:#0ae;">{{review.name}}</a>
                        <p class="WhiteBlack">{{review.text}}</p>
                    </div>
                </div>
            </div>
            <div style="width:100%;display:flex;align-items:center;justify-content:center;">
                <button class="more-button wow bounceInUp" data-wow-delay="1.1s" onclick="window.location.href = '/page/reviews'">Больше отзывов</button>
            </div>
        </div>
    </section>
    <section class="faq">
        <div class="container" style="width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <div class="rew-wrapper" style="display:flex;flex-direction:column;">
                <h3 class="tt1 WhiteBlack">FAQ</h3>
                <p class="td1 WhiteBlack">Ответы на ваши вопросы</p>
                <p class="td1 WhiteBlack">Как создать аккаунт ?</p>
                <iframe allowfullscreen width="560" height="315" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" :src="faq[0]?.['Как создать аккаунт ?']?.info?.video" frameborder="0"></iframe>
            </div>
            <div style="display:flex;align-items:center;justify-content:center;margin-top:20px;">
                <button class="more-button wow bounceInUp" data-wow-delay="1.1s" onclick="window.location.href = '/page/help'">Больше ответов</button>
            </div>
        </div>
    </section>
    <section id="fffeeedbaccck">
        <form class="feedaack bgblackwhite">
            <div class="fform_part">
                <h2 class="WhiteBlack">Нужна помощь ?</h2>
            </div>
            <div class="fform_part">
                <div class="fform_part_part">
                    <input name="name" placeholder="Имя" type="text">
                    <input name="phone" placeholder="Телефон" type="text">
                    <button type="submit">Позвоните мне</button>
                </div>
                <div class="fform_part_part">
                    <p class="WhiteBlack" style="font-size:22px;">Заполните форму мы вам перезвоним</p>
                    <p class="WhiteBlack" style="margin-top:15px;font-size:22px;">Или напишите нам в Whatsapp</p>
                    <a class="whaaaatsaaap" href="whatsapp://send?text=&phone=+79261994471&abid=+79261994471">Написать в Whatsapp</a>
                </div>
            </div>
        </form>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src="/scripts/libs/wow.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src="/scripts/libs/slick.min.js"></script>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
<?php
  if(isset($_GET["response"])) echo "<script>alert('".str_replace('%20', ' ', $_GET['response'])."', 'error');</script>"; // убрать
  if(isset($_GET["message"])) echo "<script>alert('".str_replace('%20', ' ', $_GET['message'])."', 'error');</script>";
?>
<script>
    $('.feedaack').on('submit', function(e){
        e.preventDefault();
        if($('.fform_part_part input[name=name]').val().length <= 2 && $('.fform_part_part input[name=phone]').val().length <= 7){
            alert('Поля не заполнены.', 'error');
            return;
        }
        alert('Ожидайте, мы скоро с вами свяжимся.', 'success');
        send_ajax('/engine/login', $(this).serialize());
    })
</script>
</html>