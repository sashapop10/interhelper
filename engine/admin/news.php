<?php
	session_start();
    if (!isset($_SESSION["admin"])) { header("Location: /engine/admin/login");  exit; }
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . '/engine/config.php');
    $news_photos_path = VARIABLES["photos"]["news_photos"]["upload_path"];
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/admin_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
</head>
<body>
	<?php admin_navigation('news'); ?>
    <section id='container'>
        <?php section_header('новости', 'newspaper.png'); ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Созать новость</h2>
            <form id='add_assistent_block' style='width:auto;' @submit.prevent = 'add_news' method ='post' action = '/engine/changeSettings'>
                <h2 class='WhiteBlack' style='margin-top:10px;'>Фото:</h2>
                <label onchange='readURL($(event.target).prop("files")[0], ".photo_placeholder")' class='photo_placeholder'><input style='display:none;' name='photo' type='file'/></label>
                <div><h2 class='WhiteBlack'>Название новости:</h2><input style='border:2px solid #0ae;width:250px;' name='name' type='text'/></div>
                <h2 class='WhiteBlack'>Краткое описание:</h2>
                <p class='text1'>Теги css и js и html разрешены к использованию</p>
                <textarea style='border:2px solid #0ae;border-radius:10px;background:#252525;color:#fff;resize:none;min-height:200px;height:200px;padding:10px;outline:none;margin-top:10px;' name='short_info' placeholder='Коротко о новости'></textarea>
                <h2 class='WhiteBlack' style="margin-top:10px;">Содержание:</h2>
                <p class='text1'>Теги css и js и html разрешены к использованию</p>
                <textarea style='border:2px solid #0ae;border-radius:10px;background:#252525;color:#fff;resize:none;min-height:200px;padding:10px;outline:none;margin-top:10px;' name='info' placeholder='Содержание новости'></textarea>
                <button v-if='!loader' style='box-shadow:none;' type='submit'>Добавить</button>
                <span v-else class='load_span'></span>
            </form>
            <div v-if='!add' id='add_new' class='wow bounceInUp' data-wow-delay='0.15s'>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Добавить</div>
            </div>
            <div v-else id='add_new' >
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Закрыть</div>
            </div>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.2s'>Существующие новости</h2>
            <div class='news_block wow bounceInUp' data-wow-delay='0.25s'>
                <div class='news_card' v-for='(item, index) in news'>
                    <label class='news_photo' @change='change(index, "photo")' style='cursor:pointer;' :style='"background-image: url(<?php echo $news_photos_path;?>"+item.photo+");"'><input type='file' style='display:none;' /></label>
                    <h2 class='header1' style='margin-top:10px;'>Название</h2>
                    <input @change='change(index, "name")' class='changable_input news_name' :value='item.name' type='text' />
                    <button style='width:150px;' class ='remove_assistent' @click='remove_news(index)' type='submit'>Удалить</button>
                    <h2 class='header1'>Краткое описание</h2>
                    <textarea @change='change(index, "short_info")' style='height:60px; resize:vertical;' class='changable_input news_short_info' type='text'>{{item.short_info}}</textarea>
                    <h2 class='header1'>Страница</h2>
                    <textarea @change='change(index, "info")'  style='margin-bottom:20px;height:100px; ' class='changable_input news_info' type='text'>{{item.info}}</textarea>
                    <input @change='change(index, "time")' class='news_time' :value='item.time' type='date' style='outline:none;'/>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter();?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>