<?php
    session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    $reviews_photos_path = VARIABLES["photos"]["reviews_photos"]["upload_path"];
    $review_name = '';
	if(isset($_SESSION["boss"])){ 
		$user_id = $_SESSION["boss"];
		$sql = "SELECT photo, count(1) FROM users WHERE id = '$user_id'";
        $results = attach_sql($connection, $sql, 'row');
        $sql = "SELECT * FROM reviews WHERE review_id = '$user_id'";
        $review = attach_sql($connection, $sql, 'query');
        foreach($review as $item){
            $review_name = $item["name"];
        }
        if(intval($results[1]) != 0) $photo = str_replace(' ', '%20', $results[0]); 
        else {
		    unset($_SESSION['boss']); 
            header("Location: /index"); exit;
        }
        if($review_name == '') $body = "
            <h2 class='WhiteBlack wow bounceInUp' data-wow-delay='0.1s'>Оставить отзыв</h2>
            <p class='WhiteBlack wow bounceInUp' data-wow-delay='0.15s'>Ваш отзыв очень важен для нас</p>
            <form class='add_review bgblackwhite wow bounceInUp' data-wow-delay='0.2s' @submit.prevent='add_review'>
                <div class='left_part_add_review'>
                    <label class='review_photo_placeholder'>
                        <input onchange='readURL($(this).prop(\"files\")[0], \".review_photo_placeholder\")' style='display:none;' name='review_photo' type='file' />
                    </label>
                    <input v-model='name' name='review_name' placeholder='Название компании' class='WhiteBlack add_review_name' />
                    <div style='display:inline-flex;align-items:center;'>
                        <button style='cursor:pointer;' v-if='!loader' class='review_btn' type='submit'>отправить</button>
                        <span v-else class='load_span'></span>
                        <input style='cursor:pointer;' v-model='agreement' name='agreement' class=\"check_box fake_check_box\" id=\"submit_checkbox\" type=\"checkbox\">
                        <label for=\"submit_checkbox\"></label><p class='WhiteBlack' style='margin-top:10px;margin-left:10px;font-size:12px;'>Согласие на обработку <br/> персональных данных</p>
                    </div>
                </div>
                <div class='right_part_add_review'> 
                    <input name='review_rating' v-model='rating' style='display:none;' />
                    <div class='rating'>
                        <a @click='rate(1)' :style='{color: rating > 0 ? \"orange\" : \"\"}' class='star' title='Дать 1 звёзду'>★</a>
                        <a @click='rate(2)' :style='{color: rating > 1 ? \"orange\" : \"\"}' class='star' title='Дать 2 звёзды'>★</a>
                        <a @click='rate(3)' :style='{color: rating > 2 ? \"orange\" : \"\"}' class='star' title='Дать 3 звёзды'>★</a>
                        <a @click='rate(4)' :style='{color: rating > 3 ? \"orange\" : \"\"}' class='star' title='Дать 4 звёзды'>★</a>
                        <a @click='rate(5)' :style='{color: rating > 4 ? \"orange\" : \"\"}' class='star' title='Дать 5 звёзд'>★</a>
                    </div>
                    <input name='review_link' v-model='link' placeholder='Ссылка на сайт вашей компании' class='add_review_link WhiteBlack' />
                    <textarea class='review_textarea WhiteBlack' v-model='review' name='review_review' placeholder='Ваш отзыв'></textarea>
                </div>
            </form>
        ";
        else $body = "
            <h2 class='WhiteBlack wow bounceInUp' data-wow-delay='0.1s'>Редактировать ваш отзыв</h2>
            <p class='WhiteBlack wow bounceInUp' data-wow-delay='0.15s'>Ваш отзыв очень важен для нас</p>
            <form class='add_review wow bounceInUp'data-wow-delay='0.2s'  @submit.prevent='save_review'>
                <div class='left_part_add_review'>
                    <label class='review_photo_placeholder' style='background-color:transparent;background-size:cover;border:none;' :style='\"background-image:url($reviews_photos_path\"+photo+\");\"'>
                        <input onchange='readURL($(this).prop(\"files\")[0], \".review_photo_placeholder\")' style='display:none;' name='update_review_photo' type='file' />
                    </label>
                    <input v-model='name' name='update_review_name' placeholder='Название компании' class='add_review_name' />
                    <div style='display:inline-flex;align-items:center;'>
                        <button style='cursor:pointer;' v-if='!loader' class='review_btn' type='submit'>сохранить</button>
                        <span v-else class='load_span'></span>
                        <button style='cursor:pointer;' @click='remove_review' type='button' class='review_btn_remove'>удалить</button>
                        <input style='cursor:pointer;' v-model='agreement' name='agreement' class=\"check_box fake_check_box\" id=\"submit_checkbox\" type=\"checkbox\">
                    </div>
                </div>
                <div class='right_part_add_review'> 
                    <input name='update_review_rating' v-model='rating' style='display:none;' />
                    <div class='rating'>
                        <a @click='rate(1)' :style='{color: rating > 0 ? \"orange\" : \"\"}' class='star' title='Дать 1 звёзду'>★</a>
                        <a @click='rate(2)' :style='{color: rating > 1 ? \"orange\" : \"\"}' class='star' title='Дать 2 звёзды'>★</a>
                        <a @click='rate(3)' :style='{color: rating > 2 ? \"orange\" : \"\"}' class='star' title='Дать 3 звёзды'>★</a>
                        <a @click='rate(4)' :style='{color: rating > 3 ? \"orange\" : \"\"}' class='star' title='Дать 4 звёзды'>★</a>
                        <a @click='rate(5)' :style='{color: rating > 4 ? \"orange\" : \"\"}' class='star' title='Дать 5 звёзд'>★</a>
                    </div>
                    <input name='update_review_link' v-model='link' placeholder='Ссылка на сайт вашей компании' class='add_review_link' />
                    <textarea class='review_textarea' v-model='review' name='update_review_review' placeholder='Ваш отзыв'></textarea>
                </div>
            </form>
        ";
	} else $body = '';
    if(!isset($photo)) $photo = '';
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
    $reviews_photos_path = VARIABLES["photos"]["reviews_photos"]["upload_path"];
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
    <section id="reviews_section">
        <?php echo $body; ?>
        <h1 class="WhiteBlack wow bounceInUp" data-wow-delay='0.25s'>Отзывы</h1>
        <p class="WhiteBlack wow bounceInUp" style="text-align:center;" data-wow-delay='0.3s'>Чтобы оставить отзыв, вам необходмо войти в свой аккаунт и вернуться на эту страницу.</p>
        <div class="review_row wow bounceInUp bgblackwhite" data-wow-delay='0.35s' v-for="(review, index) in reviews">
            <a class="review_photo"  :href='review.link' :style="'background-image:url(<?php echo $reviews_photos_path; ?>'+review.img+');'"></a>
            <div class="review_info">
                <div class='rating rating2'>
                    <div>
                        <a :style='{color: review.rating > 0 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                        <a :style='{color: review.rating > 1 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                        <a :style='{color: review.rating > 2 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                        <a :style='{color: review.rating > 3 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                        <a :style='{color: review.rating > 4 ? "orange" : ""}' style='cursor:default;' class='star'>★</a>
                    </div>
                    <a style="font-size:17px;" :href="review.link" class="review_name" v-html="review.name"></a>
                </div>
                <p class="review_text WhiteBlack" v-html="review.text"></p>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>