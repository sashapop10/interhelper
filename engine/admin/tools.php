<?php
	session_start();
    if (!isset($_SESSION["admin"])) { header("Location: /engine/admin/login");  exit; }
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . '/engine/config.php');
    $tools_photos_path = VARIABLES["photos"]["tools_photos"]["upload_path"];
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
	<?php admin_navigation('tools'); ?>
    <section id='container'>
    <?php section_header('инструменты', 'tools.png'); ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Создать инструмент</h2>
            <form id='add_assistent_block' @submit.prevent = 'add_tool' method ='post' >
                <h2 class='WhiteBlack' style='margin-top:10px;'>Фото:</h2>
                <label onchange='readURL($(event.target).prop("files")[0], ".photo_placeholder")' class='photo_placeholder' ><input style='display:none;' name='photo' type='file'/></label>
                <div><h2 class='WhiteBlack'>Задний фон картинки</h2><label class='design_btns' :style='"background:"+selected_color+";"'><input class='color' style='display:none;' name='color' type = 'color' v-model='selected_color' ></label></div>
                <div><h2 class='WhiteBlack'>Название инструмента:</h2><input style='border:2px solid #0ae;border-radius:10px;background:#252525;color:#fff;' name='name' type='text'/></div>
                <div>
                    <h2 class='WhiteBlack'>Группа:</h2>
                    <select style='border:2px solid #0ae;border-radius:10px;background:#252525;color:#fff;' name='group' v-model='selected_index'>
                        <option selected>{{selected_index}}</option>
                        <option v-for='(column, row_index) in tools' v-if='row_index != selected_index'>{{row_index}}</option>
                    </select>
                </div>
                <h2 class='WhiteBlack'>Описание инструмента:</h2>
                <textarea name='tool' style='border:2px solid #0ae; border-radius:10px;background:#252525;color:#fff;resize:none;height:200px;padding:10px;outline:none;margin-top:10px;' placeholder='Описание инструмента'></textarea>
                <button v-if='!loader' style='box-shadow:none;' type='submit'>Добавить</button>
                <span v-else class='load_span'></span>
            </form>
            <div v-if='!add' id='add_new' class='wow bounceInUp' data-wow-delay='0.15s'>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Добавить</div>
            </div>
            <div v-else id='add_new' >
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Закрыть</div>
            </div>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.2s'>Существующие инструменты:</h2>
            <div style='display:flex;flex-direction:column;;overflow-x:hidden;width:100%;'>
                <div style='display:inline-flex;overflow-x:auto;align-items:flex-start;justify-content:flex-start;' v-for='(row, index) in tools'>
                    <div class='review_card wow bounceInUp' data-wow-delay='0.25s' v-for='(tool, tool_index) in row'>
                        <div style='width:100%;display:inline-flex;justify-content:space-between;'>
                            <label style='cursor:pointer;' @change='change(tool_index, "photo", index)' class='review_photo' :style='"background-size:60%;background-color:"+tool.color+";background-image:url(<?php echo $tools_photos_path; ?>" + tool.photo + ")"' ><input style='display:none;' type='file'/></label>
                            <label style='min-height:80px;min-width:80px;margin:10px;border:none;' @change='change(tool_index, "color", index)' class='design_btns' :style='"background:"+tool.color+";"'><input class='color' style='display:none;' name='InterHelperMessageWindowTextColor' type = 'color' :value='tool.color'></label>
                        </div>
                        <select class='changable_input' @change='change(tool_index, "row", index)'>
                            <option style='color:#000;' selected>{{index}}</option>
                            <option style='color:#000;' v-for='(column, row_index) in tools' v-if='row_index != index'>{{row_index}}</option>
                        </select>
                        <input @change='change(tool_index, "name", index)' placeholder='Имя комании' style='margin-top:0;'class='changable_input' :value='tool.name' />
                        <button style='width:150px;' class ='remove_assistent' @click='remove_tool(tool_index, index)' type='submit'>Удалить</button>
                        <textarea @change='change(tool_index, "info", index)' placeholder='Отзыв комании' class='changable_input' style='height:150px;resize:vertical;' v-html="tool.info"></textarea>
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