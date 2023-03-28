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
    <section id="help_section">
        <div class="help_logo wow bounceInUp" data-wow-delay='0.1s'></div>
        <h1 class="WhiteBlack text_center wow bounceInUp" data-wow-delay='0.15s'>Чем вам помочь ?</h1>
        <div class="search_block wow bounceInUp"  data-wow-delay='0.17s'>
            <input style="margin-bottom:0;" type='text' class="search_input" placeholder='Поиск в справке' v-model='search_text' />
        </div>
        <div class="search_result_block">
            <a class="search_result_value wow bounceInUp" :data-wow-delay="res_count()+'s'" v-for="(elem, index) in search_res" :href="elem.link">{{index}}</a>
        </div>
        <div class="faq_block  wow bounceInUp" style="margin-top:20px;"  data-wow-delay='0.19s'> 
            <div class="faq_main">
                <span @click='open_ul()' class="faq_main_btn">Популярные вопросы</span>
                <ul class="faq_main_list close">
                    <li v-for="(elem, index) in faq['0']" v-if="elem.type != 1" class="faq_list_elem"><a :href="'/page/faqinfo?id='+index.replaceAll(' ', '%20')+'&group=0'">{{index}}</a></li>
                    <ul v-else class="faq_list2_elem close">
                        <span @click='open_ul()' class="faq_main_btn">{{index}}</span>
                        <ul class="faq_main_list close">
                            <li v-for="(innerElem, innerElemIndex) in elem.info.list" class="faq_list_elem"><a :href="'/page/faqinfo?id='+index.replaceAll(' ', '%20')+'&list_id='+innerElemIndex+'&group=0'">{{innerElemIndex}}</a></li>
                        </ul>
                    </ul>
                </ul>
            </div>
            <div class="faq_main">
                <span @click='open_ul()' class="faq_main_btn">Прочие вопросы</span>
                <ul class="faq_main_list">
                    <li v-for="(elem, index) in faq['1']" v-if="elem.type != 1" class="faq_list_elem"><a :href="'/page/faqinfo?id='+index.replaceAll(' ', '%20')+'&group=1'">{{index}}</a></li>
                    <ul v-else class="faq_list2_elem close">
                        <span @click='open_ul()' class="faq_main_btn faq_main2_btn">{{index}}</span>
                        <ul class="faq_main_list close">
                            <li v-for="(innerElem, innerElemIndex) in elem.info.list" class="faq_list_elem"><a :href="'/page/faqinfo?id='+index.replaceAll(' ', '%20')+'&list_id='+innerElemIndex+'&group=1'">{{innerElemIndex}}</a></li>
                        </ul>
                    </ul>
                </ul>
            </div>
        </div>
        <h2 class="td1 WhiteBlack  wow bounceInUp" style="margin-top:20px;" data-wow-delay='0.21s'>Не нашли ответа на Ваш вопрос? Заполните форму ниже, и мы с Вами свяжемся.</h2>
        <p v-if="time" class="wow bounceInUp" data-wow-delay='0.24s' style="color:tomato;">Вы уже отправляли письмо в службу поддержки, вы сможете отправить повтороно в {{time}}</p>
        <h2 class="WhiteBlack text_center font_size_20 wow bounceInUp" data-wow-delay='0.35s'>Введите почту по которой можно с вами связаться.</h2>
        <input v-model="mail" class="help_mail_input wow bounceInUp" data-wow-delay='0.4s' type="mail" placeholder="Почта обратной связи." />
        <h2 class="WhiteBlack text_center font_size_20 wow bounceInUp" data-wow-delay='0.45s'>Опишите вашу проблему, чтобы мы сразу же приступили к её решению !</h2>
        <textarea v-model="message" class="help_text_input wow bounceInUp" data-wow-delay='0.5s' placeholder="Опишите вашу проблему."></textarea>
        <button v-if="!load" class="help_send_btn wow bounceInUp" type="button" @click="send()" data-wow-delay='0.55s'>Отправить запрос</button>
        <span style="margin-top:20px;" class="preloader" v-else></span>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>