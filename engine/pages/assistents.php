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
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
	<link rel="stylesheet" type="text/css" href="/scss/client_page.css">
	<link rel="stylesheet" type="text/css" href="/scss/libs/media.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
	<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
    <script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
    <?php navigation('assistents', $info); ?>
    <section id='container'>
        <?php section_header('Сотрудники', 'leftImg2.png'); ?>
        <div id='column1' v-cloak> 
            <form id='add_assistent_block' @submit.prevent = 'add_assistent' method ='post' action = '/engine/settings' class="v-cloak-off" v-cloak>
                <p class='text1'>* - не обязательный пункт</p>   
                <div><h2 class='WhiteBlack'>Имя:</h2><input  name='assistent_name' type='text'/></div>
                <div><h2 class='WhiteBlack'>Текст приветствия *:</h2><input  name='assistent_buttleCry' type='text'/></div>
                <div><h2 class='WhiteBlack'>Отдел:</h2><select name='assistent_departament' ><option disabled selected></option><option v-for="departament in departaments">{{departament}}</option></select></div>
                <div><h2 class='WhiteBlack'>Почта:</h2><input  name='assistent_email' type='text'/></div>
                <div><h2 class='WhiteBlack'>Пароль:</h2><input  name='assistent_password' type='password'/></div>
                <div><h2 class='WhiteBlack'>Пароль<br/>(второй раз):</h2><input  name='assistent_passwordSecondTime' type='password'/></div>
                <button v-if='!loader' type='submit'>Добавить</button>
                <span v-else class='load_span'></span>
            </form>
            <p class='wow bounceInUp text1 v-cloak-off' data-wow-delay='0.1s' v-cloak>Созданные сотрудники, также входят через официальный сайт InterHelper.ru</p>
            <p style='color:tomato;' v-if='!domains' class='wow bounceInUp text1 v-cloak-off' v-cloak data-wow-delay='0.12s'>Добавьте домен в разделе "получить код", чтобы добавить сотрудника.</p>
            <p style='color:tomato;' v-if='!departaments.length' class='wow bounceInUp text1 v-cloak-off' v-cloak data-wow-delay='0.14s'>Добавьте отдел в разделе "отделы", чтобы добавить сотрудника.</p>
            <div v-if='!add' id='add_new' class="v-cloak-off" v-cloak>
                <h2 class='header1'>Добавить нового сотрудника</h2>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Добавить</div>
            </div>
            <div v-else id='add_new' class="v-cloak-off" v-cloak>
                <div style='color:#0ae;' @click='ocform' id='add_new_assistent'>Закрыть</div>
            </div>
            <h4 style='color:#fff;margin-top:10px;' class='wow bounceInUp WhiteBlack v-cloak-off' v-cloak data-wow-delay='0.225s'>Количество сотрудников в сети: <span style='color:lightgreen;'>{{get_count('online')}}</span></h4>
            <h4 style='color:#fff;margin-top:10px;' class='wow bounceInUp WhiteBlack v-cloak-off' v-cloak data-wow-delay='0.24s'>Количество сотрудников вне сети: <span style='color:tomato;'>{{get_count('offline')}}</span></h4>
            <p   class="v-cloak-on v-cloak-text2" v-cloak style="margin:0;height:40px !important;width:400px;margin-top:10px;"></p>
            <p   class="v-cloak-on v-cloak-text2" v-cloak style="margin:0;height:40px !important;width:350px;margin-top:10px;"></p>
            <div class="v-cloak-on v-cloak-block" v-cloak style="height:60px;width:320px;border-radius:10px;margin-top:10px;"></div>
            <p   class="v-cloak-on v-cloak-text2" v-cloak style="margin:0;height:40px !important;width:300px;margin-top:10px;"></p>
            <p   class="v-cloak-on v-cloak-text2" v-cloak style="margin:0;height:40px !important;width:300px;margin-top:10px;"></p>
            <div id = 'column2' v-if='userlist' class='wow bounceInUp' data-wow-delay='0.25s'>
                <div v-if='Object.keys(userlist).length > 0' id='assistents_list'>
                    <div v-for='(elem, index) in sort_mas(userlist)' class='assistent bgblackwhite v-cloak-off' v-if='index != "public_room"' v-cloak>
                        <label class='assistent_img bgblackwhite' :style='"background-image:url(<?php echo $file_path; ?>"+elem.photo+"); background-repeat:no-repeat; background-size:cover;cursor:pointer;"'>
                            <input @change='change_assistent_photo(elem.id, "assistent_changephoto")' class='changable_assistent_photo' style='display:none;' type='file' />
                        </label>
                        <p style='font-size:1.6em;' :class='"assistent_status "+elem.status+""'>{{elem.status}}</p>
                        <p style='margin-top:10px;' class="WhiteBlack" :style='{"opacity": elem.status != "offline" ? "0" : "1"}'>Последняя активность</p>
                        <p :style='{"opacity": elem.status != "offline" ? "0" : "1"}' v-if="elem.time != 'новый'" style='color:#0ae;' class='assistent_status'>{{elem.time.split(' ')[0].split('-').reverse().join('.') }} <span style="color:#f90;font-weight:bold;">{{elem.time.split(' ')[1].split(':').slice(0, 2).join(':')}}</span></p>
                        <p :style='{"opacity": elem.status != "offline" ? "0" : "1"}' v-else style='color:#0ae;' class='assistent_status'>{{elem.time}}</span></p>
                        <input style="border-radius:10px;" @change='change_assistent_info(elem.id, "name", null)' class='changable_assistent' :value='elem.name' />
                        <div style="border-radius:10px;" class="departament_select">
                            <p @click="list()">{{elem.departament}}</p>
                            <ul class="departaments_list">
                                <li v-for='departament in departaments' v-if="departament != elem.departament" @click="list(); change_assistent_info(elem.id, 'departament', departament); elem.departament = departament;">{{departament}}</option>
                            </ul>
                        </div>
                        <input style="border-radius:10px;" @change='change_assistent_info(elem.id, "email", null)' class='changable_assistent' :value='elem.hab' />
                        <input style="border-radius:10px;" @change='change_assistent_info(elem.id, "buttlecry", null)' class='changable_assistent' :value='elem.buttlecry' />
                        <div style="display:inline-flex;">
                            <button style='width:150px;border-radius:10px;background:tomato;' class ='remove_assistent' @click='remove_assistent(elem.id, "remove_assistent")'>Удалить</button>
                            <button style='width:150px;border-radius:10px;background:#0ae;margin-left:20px;' class ='remove_assistent' @click='enter_assistent(elem.id)'>Войти</button>
                        </div>    
                        <button style='width:100%;border-radius:10px;' :style='"background:"+(typeof(elem.ban) == "string" ? "#fff" : "#000")+";color:"+(typeof(elem.ban) != "string" ? "#fff" : "#000")+";"' class ='remove_assistent' @click='ban_assistent(elem.id)'>{{typeof(elem.ban) == 'string' ? 'Разблокировать' : 'Заблокировать'}}</button>
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                    <div class='assistent bgblackwhite v-cloak-on' v-cloak>
                        <label class='assistent_img bgblackwhite v-cloak-block'></label>
                        <p class="v-cloak-text2" style="height:40px !important;width:100px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:120px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <p class="v-cloak-text2" style="height:40px !important;margin-top:10px;width:320px;"></p>
                        <div style="display:inline-flex;margin-top:10px;">
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;' ></div>
                            <div class="v-cloak-block" style='width:150px;border-radius:10px;height:50px;margin-left:20px;' ></div>
                        </div>    
                    </div>
                </div>
            </div>
            <div v-else id='assistents_list'>
                <p class='text1 WhiteBlack'> Тут пока никого нет !</p>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>