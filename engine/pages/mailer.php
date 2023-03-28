<?php
	session_start();
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
    $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"]) && !isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$info = check_user($connection);
	if(!$info['status']){ mysqli_close($connection); header("Location: ".$info['info']['new_url']."?message=".$info['info']['error']); exit; } 
	if(isset($info['info']['log'])) echo "<script>alert('".$info['info']['log']."');</script>";
	mysqli_close($connection);
    $tariff=$info['info']['tariff'];
    $cost = EDITIONS[$tariff]['include']['mailer']['value'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
	<link rel="stylesheet" type="text/css" href="/scss/client_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
    <script type="text/javascript" src="/HelperCode/Helper"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php navigation('mailer', $info); ?>
    <section id='container'>
        <?php section_header('Рассылка почты', 'mailer.png'); ?>
        <?php create_book(''); ?>
        <div id = 'column1'>
            <h2 class='header1 wow bounceInUp v-cloak-off' data-wow-delay='0.05s' v-cloak>Настройки для</h2>
            <select class="changable_input wow bounceInUp v-cloak-off" @change="selected_domain = $event.target.value" data-wow-delay='0.05s' v-cloak>
                <option style="background:#252525;color:#fff;" :selected="selected_domain == domain" :value="domain" v-for="(domain, index) in domains">{{domain == 'deffault' ? 'по умолчанию' : domain}}</option>
            </select>
           <h2 class="header1 wow bounceInUp v-cloak-off" v-cloak data-wow-delay='0.1s'>Данные SMTP</h2>
           <p class="v-cloak-on v-cloak-text2" style="margin:0;width:200px;height:40px !important;" v-cloak></p>
           <p class="text1 wow bounceInUp v-cloak-off" data-wow-delay='0.2s' v-cloak>Стоимость рассылки за единицу: <?php echo $cost; ?> ₽</p>
           <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;margin-top:10px;width:400px" v-cloak></p>
            <div class="mailer_page_info wow bounceInUp v-cloak-off" data-wow-delay='0.3s' v-cloak>
                <div>
                    <h3 class="WhiteBlack">Адрес SMTP-сервера</h3>
                    <input v-model="SMTPserver" @change="change('SMTPserver')" placeholder="Адрес SMTP-сервера" type="text" class="changable_input">
                </div>
                <div>
                    <h3 class="WhiteBlack">Защита SMTP</h3>
                    <select v-model="SMTPsecure" @change="change('SMTPsecure')">
                        <option :selected="SMTPsecure == 'ssl'" value="ssl" >ssl</option>
                        <option :selected="SMTPsecure == 'tls'" value="tls" >tls</option>
                    </select>
                </div>
                <div>
                    <h3 class="WhiteBlack">SMTP порт</h3>
                    <input v-model="SMTPport"   @change="change('SMTPport')" placeholder="SMTP порт" type="text" class="changable_input">
                </div>
                <div>
                    <h3 class="WhiteBlack">SMTP почта</h3>
                    <input v-model="SMTPemail"  @change="change('SMTPemail')" placeholder="SMTP почта" type="text" class="changable_input">
                </div>
                <div>
                    <h3 class="WhiteBlack">SMTP пароль</h3>
                    <input v-model="SMTPpassword" @change="change('SMTPpassword')" placeholder="SMTP пароль" type="text" class="changable_input">
                </div>
            </div>
            <div class="mailer_page_info v-cloak-on" v-cloak>
                <div>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
                <div>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
                <div>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
                <div>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0;height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
                <div>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
            </div>
            <h2 class="header1 wow bounceInUp v-cloak-off" data-wow-delay='0.4s' v-cloak>Рассылка почты</h2>
            <p class="v-cloak-on v-cloak-text2" style="margin:0;margin-top:10px;width:410px;height:40px !important;" v-cloak></p>
            <div class="mailer_page_mail" >
                <div class="mailer_info v-cloak-off wow bounceInUp" v-cloak data-wow-delay='0.5s'>
                    <h3 class="WhiteBlack">Имя отправителя</h3>
                    <input v-model="sender_name" placeholder="Имя отправителя" @change="change('sender_name')" type="text" class="changable_input">
                </div>
                <div class="mailer_info v-cloak-on" v-cloak>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
                <p class="text1 v-cloak-off wow bounceInUp"  data-wow-delay='0.5s' v-cloak>Также служит именем поумолчанию в почтовой рассылке CRM</p>
                <p class="v-cloak-on v-cloak-text2" style="margin:0;width:410px;height:40px !important;margin-top:10px;" v-cloak></p>
                <div class="mailer_info v-cloak-off wow bounceInUp" data-wow-delay='0.5s' v-cloak>
                    <h3 class="WhiteBlack">Название письма</h3>
                    <input v-model="mail_name" @change="change('mail_name')" placeholder="Название письма" type="text" class="changable_input">
                </div>
                <div class="mailer_info v-cloak-on" v-cloak>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0height:40px !important;width:200px" v-cloak></p>
                    <p class="v-cloak-on v-cloak-text2" style="margin:0height:40px !important;margin-left:10px;width:200px" v-cloak></p>
                </div>
                <p class="text1 v-cloak-off wow bounceInUp" v-cloak data-wow-delay='0.5s'>Также служит именем письма поумолчанию в почтовой рассылке CRM</p>
                <p class="v-cloak-on v-cloak-text2" style="margin:0;width:410px;height:40px !important;margin-top:10px;" v-cloak></p>
                <div class="v-cloak-off wow bounceInUp" data-wow-delay='0.5s' v-cloak>
                    <h3 class="WhiteBlack" style="margin-top:10px;margin-bottom:10px;font-weight:bold;">Содержание письма</h3>
                    <p class="text1">html-тэги поддерживаются</p>
                    <div style="max-width:400px;background:#333;margin-top:10px;border:2px solid #000;border-radius:10px;width:100%;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;">
                        <div class="chat_footer" style="line-height:1.4;background:#333;border-bottom:2px solid #000;display:flex;align-items:flex-end;flex-direction:column;background:transparent" class="card-body message_panel_input">
                            <div type="text" style="border-bottom:2px solid #000;border-top-left-radius:10px;border-top-right-radius:10px; repeat center center;" contenteditable="true" aria-multiline="true" role="textbox"  class="bgblackwhite WhiteBlack chat_block_textarea form-control"></div>
                            <div class="btns_panel">
                                <span title="Быстрые команды" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                                <input @change="handleChange()" multiple name="myfile[]" class='add_photo'  id='add_photo' type='file' style='display:none;' />
                                <label title="Приложить фотографию" class="btns_panel_btn add_file" for="add_photo" ></label>
                            </div>   
                        </div> 
                        <div id="upload_files_preview" class="bgblackwhite WhiteBlack"> 
                            <div class="preview_img_block" v-for="(file, index) in files" :key="index">
                                <span class="preview_remove" @click="removeFile(index)"><span></span><span></span></span>
                                <span class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                                <p :title="file.name" style="background:#0ae;position:absolute;bottom:7px;left:0;color:#000;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.name}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="v-cloak-on"style="margin-top:10px;"  v-cloak>
                    <p class="v-cloak-text2" style="margin:0;width:200px;height:40px !important;" v-cloak></p>
                    <p class="v-cloak-text2" style="margin:0;margin-left:10px;width:200px;height:40px !important;" v-cloak></p>
                </div>
                <p class="text1 v-cloak-off" v-cloak><span style="color:#f90;">Внемание!</span> Мы не проверяем существование введёных Вами адресов e-mail</p>
                <p class="v-cloak-on v-cloak-text2" style="margin:0;width:410px;height:40px !important;margin-top:10px;" v-cloak></p>
                <div class="mailer_select bgblackwhite v-cloak-off" v-cloak>
                    <h3 class="WhiteBlack">Получатели</h3>
                    <div class="mailer_select_info">
                        <div style="width:300px;display:flex;flex-direction:column;">
                            <input v-model="recepient.name" placeholder="имя получателя (опционально)" style="margin:0;border-radius:10px;" class="changable_input" type="text"/>
                            <input v-model="recepient.email" placeholder="Почта получателя" style="margin-left:0;border-radius:10px;" class="changable_input" type="text"/>
                        </div>
                        <span class="add_item_mailer" @click="add_recepient"></span>
                    </div>
                    <div v-if="selected.length > 0">
                        <div class="mailer_selected" :style="{'border-bottom': index != selected.length - 1 ? '2px solid #fff' : 'none'}" v-for="(item, index) in selected" :key="index">
                            <div style="display:flex;flex-direction:column;justify-content:flex-start;width:100%;align-items:flex-start;">
                                <p>{{item.email}}</p>
                                <p v-if="item.name">{{item.name}}</p>
                            </div>
                            <span @click="remove_recepient(index)"></span>
                        </div>
                    </div>
                    <div v-else> 
                        <p style="text-align:center;width:100%;">Добавьте получателей через поля выше, <br/>чтобы  здесь что-то появилось</p>
                    </div>
                </div>
                <div class="v-cloak-block mailer_select v-cloak-on" style="height:200px;" v-cloak></div>
                <button v-cloak v-if="!loader" class="v-cloak-off send_mails WhiteBlack" @click="send_mails">Отправить</button>
                <div v-cloak style="min-height:60px;min-width:60px;margin:20px;" v-else class="v-cloak-off domain-loader"></div>
                <div v-cloak class="v-cloak-on WhiteBlack v-cloak-block" style="width:120px;height:40px;border-radius:10px;margin-top:10px;"></div>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>