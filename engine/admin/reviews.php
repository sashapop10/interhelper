<?php
	session_start();
    if (!isset($_SESSION["admin"])) { header("Location: /engine/admin/login");  exit; }
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . '/engine/config.php');
    $reviews_photos_path = VARIABLES["photos"]["reviews_photos"]["upload_path"];
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
	<?php admin_navigation('reviews'); ?>
    <section id='container'>
        <?php section_header('Reviews', 'review.png'); ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Создать отзыв</h2>
            <form id='add_assistent_block' @submit.prevent = 'add_review' method ='post' action = '/engine/changeSettings'>
                <h2 class='WhiteBlack' style='margin-top:10px;'>Фото:</h2>
                <label onchange='readURL($(event.target).prop("files")[0], ".photo_placeholder")' class='photo_placeholder'><input style='display:none;' name='photo' type='file'/></label>
                <input v-model='rating' name='rating' style='display:none;' /> 
                <div class='rating'>
                    <a @click='rating = 1' :style='{color: rating > 0 ? "orange" : ""}' class='star' title='Дать 1 звёзду'>★</a>
                    <a @click='rating = 2' :style='{color: rating > 1 ? "orange" : ""}' class='star' title='Дать 2 звёзды'>★</a>
                    <a @click='rating = 3' :style='{color: rating > 2 ? "orange" : ""}' class='star' title='Дать 3 звёзды'>★</a>
                    <a @click='rating = 4' :style='{color: rating > 3 ? "orange" : ""}' class='star' title='Дать 4 звёзды'>★</a>
                    <a @click='rating = 5' :style='{color: rating > 4 ? "orange" : ""}' class='star' title='Дать 5 звёзд'>★</a>
                </div>
                <div><h2 class='WhiteBlack'>Название компании:</h2><input style='border:2px solid #0ae;' name='name' type='text'/></div>
                <div><h2 class='WhiteBlack'>Ссылка на компанию:</h2><input style='border:2px solid #0ae;' name='link' type='text'/></div>
                <h2 class='WhiteBlack'>Отзыв:</h2>
                <textarea name='review' style='border:2px solid #0ae; border-radius:10px;background:#252525;color:#fff;resize:none;height:200px;padding:10px;outline:none;margin-top:10px;' placeholder='Содержание отзыва'></textarea>
                <button v-if='!loader' style='box-shadow:none;' type='submit'>Добавить</button>
                <span v-else class='load_span'></span>
            </form>
            <div v-if='!add' id='add_new' class='wow bounceInUp' data-wow-delay='0.15s'>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Добавить</div>
            </div>
            <div v-else id='add_new' >
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Закрыть</div>
            </div>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.2s'>Существующие отзывы:</h2>
            <div style='display:flex;flex-wrap:wrap;width:100%;align-items: flex-start;'>
                <div style='position:relative;' class='review_card wow bounceInUp' data-wow-delay='0.25s' v-for='(review, index) in reviews'>
                    <label style='cursor:pointer;' @change='change(index, "img")' class='review_photo' :style='"background-image:url(<?php echo $reviews_photos_path; ?>" + review.img + ")"' ><input style='display:none;' type='file'/></label>
                    <div class='rating'>
                        <a @click='rate(1, index)' :style='{color: review.rating > 0 ? "orange" : ""}' class='star' title='Дать 1 звёзду'>★</a>
                        <a @click='rate(2, index)' :style='{color: review.rating > 1 ? "orange" : ""}' class='star' title='Дать 2 звёзды'>★</a>
                        <a @click='rate(3, index)' :style='{color: review.rating > 2 ? "orange" : ""}' class='star' title='Дать 3 звёзды'>★</a>
                        <a @click='rate(4, index)' :style='{color: review.rating > 3 ? "orange" : ""}' class='star' title='Дать 4 звёзды'>★</a>
                        <a @click='rate(5, index)' :style='{color: review.rating > 4 ? "orange" : ""}' class='star' title='Дать 5 звёзд'>★</a>
                    </div>
                    <input @change='change(index, "time")' class='review_time' :value='review.time' type='date' style='background:transparent;outline:none;'/>
                    <input @change='change(index, "name")' placeholder='Имя комании' style='margin-top:0;'class='changable_input' :value='review.name' />
                    <button style='width:150px;' class ='remove_assistent' @click='remove_review(index)' type='submit'>Удалить</button>
                    <input @change='change(index, "link")' placeholder='Ссылка на компанию' :value='review.link' class='changable_input'/>
                    <textarea @change='change(index, "text")' placeholder='Отзыв комании' class='changable_input' style='height:150px;resize:vertical;' v-html='review.text'></textarea>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>