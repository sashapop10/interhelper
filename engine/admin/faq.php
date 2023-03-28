<?php
    session_start();    
    if (!isset($_SESSION["admin"])) { header("Location: /engine/admin/login");  exit; }
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
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
	<?php admin_navigation('faq'); ?>
    <section id='container'>
        <?php section_header('FAQ', 'help.png'); ?>
        <div id='middle_part2'>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Создать FAQ</h2>
            <form id='add_assistent_block' @submit.prevent = 'add_faq' method ='post' >
                <div><h2 class='WhiteBlack'>Название FAQ:</h2><input style='border:2px solid #0ae;' v-model='header' name='name' type='text'/></div>
                <div>
                    <h2 class='WhiteBlack'>Тип:</h2>
                    <select style='border:2px solid #0ae;' name='group' v-model='selected_index'>
                        <option selected>Ответ</option>
                        <option>Список</option>
                    </select>
                </div>
                <div>
                    <h2 class='WhiteBlack'>Колонка:</h2>
                    <select style='border:2px solid #0ae;' name='group' v-model='column'>
                        <option selected>Часто задаваемые вопросы</option>
                        <option>Прочие вопросы</option>
                    </select>
                </div>
                <h2 v-if='!loader && selected_index != "Список"' style='color:#0ae;'>Решение FAQ:</h2>
                <textarea name='tool' v-if='selected_index != "Список"' style='border:2px solid #0ae;border-radius:10px;background:#252525;color:#fff;resize:none;height:200px;padding:10px;outline:none;margin-top:10px;' v-model='answer' placeholder='Решение FAQ'></textarea>
                <h2 style="margin-top:10px;color:#0ae;" v-if='!loader && selected_index != "Список"'>Ссылка на YouTube:</h2>
                <input v-if='selected_index != "Список"' style='border:2px solid #0ae;border-radius:10px;background:#252525;color:#fff;resize:none;padding:10px;outline:none;margin-top:10px;' v-model='video_link' placeholder='Ссылка на YouTube(при наличии)'/>
                <button v-if='!loader' style='box-shadow:none;' type='submit'>Добавить</button>
                <span v-else class='load_span'></span>
            </form>
            <div v-if='!add' id='add_new' class='wow bounceInUp' data-wow-delay='0.15s'>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Добавить</div>
            </div>
            <div v-else id='add_new' >
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Закрыть</div>
            </div>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Управление FAQ</h2>
        </div>
        <div id='middle_part' style='display:flex;flex-direction:column;'>
            <div v-for='(item2, index2) in mas' class='faq_group' style='margin-right:10px;'>
                <h2 class='WhiteBlack wow bounceInUp' data-wow-delay='0.125s' style='margin-bottom:10px;' v-if='index2 == 0'>Часто задаваемые вопросы</h2>
                <h2 class='WhiteBlack wow bounceInUp' data-wow-delay='0.125s' style='margin-bottom:10px;' v-else>Прочие вопросы</h2>
                <div style="display:inline-flex;flex-wrap:wrap;align-items:flex-start;justify-content:flex-start;">
                    <div class='faq_card' data-wow-delay='0.15s' v-for='(item, index) in mas[index2]'>
                        <input    @change='change_text(index2, index, null, "header")'   class='faq_header' :value='index' />
                        <textarea v-if="!item.info.list" @change='change_text(index2, index, null, "answer")' class='faq_answer'>{{item.info.answer}}</textarea>
                        <input    v-if="!item.info.list" placeholder="Ссылка на YouTube(при наличии)" @change='change_text(index2, index, null, "video")'  class='faq_video' :value="item.info.video"/>
                        <div>
                            <button @click='remove(index2, index)' class='remove_faq_group'>Удалить</button>
                            <button class='change_faq_group' @click='change_location(index2, index);'>Переместить</button>
                        </div>
                        <div v-if='!item.info.answer'>
                            <div v-for='(list_item, index3) in item.info.list' class='faq_innercard'>
                                <input @change='change_text(index2, index, index3, "innerheader")' class='faq_inner_header' :value='index3' />
                                <textarea @change='change_text(index2, index, index3, "inneranswer")' class='faq_answer'>{{list_item.answer}}</textarea>
                                <input placeholder="Ссылка на YouTube(при наличии)" @change='change_text(index2, index, index3, "innervideo")'  class='faq_video' :value="list_item.video"/>
                                <button class='remove_faq_innergroup' @click='remove_innergroup(index2, index, index3);'>Удалить</button>
                            </div>
                            <div style='display:none;' class='faq_innercard new_innercard'>
                                <input class='faq_inner_header' placeholder='Название' />
                                <input class='faq_answer' placeholder='Решение' />
                                <input class='faq_video' placeholder='Ссылка на YouTube(при наличии)' />
                                <button class='save_faq_innergroup' @click='save_innercard(index2, index);'>Сохранить</button>
                            </div>
                            <button class='add_faq_group' @click='new_innercard();'>Добавить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>