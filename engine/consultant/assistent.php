<?php
    session_start();
    $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
    if (!isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
    $info = check_user($connection);
    if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
    if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
    mysqli_close($connection);
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<title>InterHelper</title>
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/consultant_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
</head>
<body>
	<?php navigation('assistent', $info); ?>
    <section id='app'>
        <?php section_header("Профиль", "admin.png"); team_msg_notification_body($file_path);  ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp v-cloak-off' data-wow-delay='0.05s' v-cloak>Ваш отдел: <span class='WhiteBlack' style='color:#fff;'>{{departament}}</span></h2>
            <p class='header1 v-cloak-on v-cloak-text2' style="max-width:300px;" v-cloak></p>
            <div id = 'img_first_part' class='wow bounceInUp v-cloak-off' data-wow-delay='0.1s' v-cloak>
                <input name='assistent_changephoto' @change="change('photo')" class='changable_assistent_photo' name=''  id='add_photo' type='file' style='display:none;'/>
                <label for='add_photo' :style='"height:100px;width:100px;display:block;background-image:url(<?php echo $file_path;?>"+photo+");background-repeat:no-repeat;background-position:center; background-size:cover;border-radius:50%;cursor:pointer;"' id='assistent_img_place'></label>
                <div id='userinfo'>
                    <input @change="change('name')" placeholder='Имя' name='name' class='changable_assistent' style='margin-top:0px;border-radius:10px;' type='text' v-model:value='name'/>
                    <input @change="change('email')" placeholder='Адрес элетронной почты' class='changable_assistent' name='email' style='margin-top:5px;border-radius:10px;' type='text' v-model:value='email'/>
                </div>
            </div>
            <div id = 'img_first_part' class='wow bounceInUp v-cloak-on' v-cloak>
                <label for='add_photo' style='height:100px;width:100px;display:block;border-radius:50%;' class="v-cloak-block" id='assistent_img_place'></label>
                <div id='userinfo'>
                    <p style="width:200px !important;border-radius:10px;margin-bottom:20px;" class="v-cloak-text2"></p>
                    <p style="width:200px !important;border-radius:10px;" class="v-cloak-text2"></p>
                </div>
            </div>
            <p class='text1 wow bounceInUp v-cloak-off' data-wow-delay='0.2s' v-cloak>Поддерживаемые форматы JPG,PNG,GIF.</p>
            <p class='header1 v-cloak-on v-cloak-text2' style="max-width:400px;" v-cloak></p>
            <div id='animablock'  class='wow bounceInUp v-cloak-off' data-wow-delay='0.25s' v-cloak>
                <h3 class='WhiteBlack' style='margin-right:10px;'><span style='color:tomato;'>Выключить</span> всплывающие <span style='color:#0ae;'>анимации</span></h3>
                <span @click="animations = !animations" class="check_btn"><span :class="[{'checked_btn_span': !animations}, {'unchecked_btn_span': animations}]"></span></span>
            </div>
            <div id='animablock' class='v-cloak-on'  v-cloak>
                <p class='v-cloak-text2' style='width:250px !important;margin-right:10px !important;'></p>
                <span class="check_btn v-cloak-block"><span class="checked_btn_span v-cloak-block"></span></span>
            </div>
            <div style="display: inline-flex; margin-top: 30px;" class='wow bounceInUp v-cloak-off' data-wow-delay='0.3s' v-cloak>
                <button id='log_out-exit_btn' class='exit_header_button WhiteBlack' onclick="window.top.postMessage('exit', '*');" @click="exit()">Выйти из аккаунта</button>
                <p @click='pass = !pass' class='WhiteBlack' v-if='!pass' id="changepass">Сменить пароль</p>
            </div>
            <div style="display: inline-flex; margin-top: 30px;" class='v-cloak-on' v-cloak>
                <button style="height:40px;width:200px;border-radius:10px;border:2px solid #000;" class='v-cloak-block'></button>
                <button style="height:40px;width:200px;border-radius:10px;border:2px solid #000;margin-left:10px;" class="v-cloak-block"></button>
            </div>
            <div v-if='pass' style='margin-bottom:30px;' class="v-cloak-off" v-cloak>
                <div id='pass_top'>
                    <div style="width:300px;position:relative;">
                        <input v-model:value='old' id="oldpass" type='password' placeholder='Старый пароль' />
						<span class="password_eye"></span>
					</div>
                    <div style="width:300px;position:relative;">
                        <input v-model:value='newpass' id="newpass" type='password' placeholder='Новый пароль' />
						<span class="password_eye"></span>
					</div>
                    <div style="width:300px;position:relative;">
                        <input v-model:value='repeat' id="repeatnewpass" type='password' placeholder='Повторите новый пароль' />
						<span class="password_eye"></span>
					</div>
                </div>
                <div id='pass_floor'>
                    <button @click="changepass()">Сменить пароль</button>
                    <p @click='pass = !pass' id='cancelpass'>Отмена</p>
                </div>
            </div>
            <div class='wow bounceInUp v-cloak-off' style='display:flex;flex-direction:column;' data-wow-delay='0.05s' v-cloak>
                <h2 class='header1'>Текст приветствия</h2>
                <input @change="change('buttlecry')" class='changable_assistent' name='buttlecry' style='border-radius:10px;height:60px; padding-left:10px;  outline:none;margin-top:20px;word-wrap:break-word; ' type='text' v-model:value='buttlecry'/>
                <p class='text1'>Текст, предлогаемый в качестве первого сообщения. Также являестя шаблоном сообщения.</p>
            </div>
            <div class='v-cloak-on' style='display:flex;flex-direction:column;' v-cloak>
                <p class='header1 v-cloak-text2' style="width:300px !important;"></p>
                <p class='header1 v-cloak-text2' style="margin-top:10px;width:400px !important;"></p>
                <p class='header1 v-cloak-text2' style="margin-top:10px;width:350px !important;"></p>
            </div>
        </div>
    </section>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
<?php appendfooter(); ?>
</html>