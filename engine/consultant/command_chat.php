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
    $personal_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['personal_id'];
    $boss_id = json_decode($_SESSION['employee'], JSON_UNESCAPED_UNICODE)['boss_id'];
    $sql = "SELECT buttlecry FROM assistents WHERE id = '$personal_id'";
    $buttlecry = attach_sql($connection, $sql, 'row')[0];
    $room_from_get = $_GET['room'];
    if($room_from_get != $boss_id && strrpos('!@!@2@!@!', $room_from_get)){
        $oponent_id = explode('!@!@2@!@!', isset($_GET['room']));
        if($oponent_id[0] != $personal_id) $oponent_id = $oponent_id[0];
        else $oponent_id = $oponent_id[1];
        $sql = "SELECT count(1) FROM assistents WHERE domain = '$boss_id' AND id = '$oponent_id'"; 
        if(attach_sql($connection, $sql, 'row')[0] == 0){ mysqli_close($connection); header("Location: /index");  exit; }
    }
    mysqli_close($connection);
    $file_path = VARIABLES['photos']['assistent_profile_photo']['upload_path'];
    $_SESSION["assistent_room"] = $_GET['room'];
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
</head>
<body>
    <?php navigation('command', $info); ?>
    <div id="app" v-cloak>
    <?php create_book($buttlecry); ?>
        <div id="chat_header" class="card-header text-white bgblackwhite">
            <div class="chat-header-info2 v-cloak-off" v-cloak>
                <div style='display:inline-flex;flex-wrap:wrap;'>
                    <h4 class="WhiteBlack" v-if = "oponent_email">Чат с {{oponent_email}}</h4>
                    <h4 class="WhiteBlack" v-else >Общий чат </h4>
                    <span :class="status" style="text-transform:uppercase;margin-left:10px;">{{status}}</span>
                </div>
                <div style='display:inline-flex;flex-wrap:wrap;margin-top:10px;' v-if='"<?php echo $room_from_get; ?>" != "<?php echo $boss_id; ?>"'>
                    <h4>{{oponent_name}}</h4>
                    <h4 style="color:#f90;margin-left:10px;">{{oponent_departament}}</h4>
                </div>
            </div>
            <div class="chat-header-info2 v-cloak-on" v-cloak>
               <div style="display:inline-flex;"><p class="v-cloak-text2" style="width:150px;margin-left:0;"></p><p class="v-cloak-text2" style="width:100px"></p></div>
                <p class="v-cloak-text2" style="margin-left:0;margin-top:10px;width:260px"></p>
            </div>
            <div class="chat-header-info2 v-cloak-off" v-cloak>
                <span data-tooltip="Вернуться к списку посетителей." style="margin-left:10px;" class="break_chat chat_header_btn" v-on:click="exit"><span></span><span></span></span>
            </div>
            <div class="chat-header-info2 v-cloak-on" v-cloak>
                <span style="margin-left:10px;" class="break_chat chat_header_btn v-cloak-block"></span>
            </div>
        </div>
        <div class="app_row">
            <div id="chat_container" style="width:100%;" class="card bg-info" >
                <ul id="chat_body" class="list-group list-group-flush text-right">
                    <div id ="typing2" v-if="typing" class="v-cloak-off" v-cloak><small style ="width:100%;" v-if="typing">{{typing}} пишет <span></span><span></span><span></span></small></div> 
                    <li class="list-message wow bounceInDown v-cloak-off" v-for="(message, message_index) in messages" v-cloak>
                         <span class="date" v-if="load_date(message.time.split(' ')[0].split('-').reverse().join('.'))">
                            <span class="WhiteBlack bgblackwhite">{{message.time.split(' ')[0].split('-').reverse().join('.')}}</span>
                        </span>
                        <div style="width:100%;display:flex;" :class="{'message-by-me':message.sender == '<?php echo $personal_id; ?>' && message.sender != 'offline_form' }">
                            <span class="list-message-block bgblackwhite">
                                <div class="thisuserimg bgblackwhite" v-if="(messages[message_index-1]||{}).sender != message.sender || message.time.split(' ')[0].split('-')[2] != (messages[message_index-1]||{'time': '00:00:00 00:00'}).time.split(' ')[0].split('-')[2]" v-bind:style='{ backgroundImage: "url(<?php echo $file_path; ?>"+message.photo+")"}'></div>
                                <div class="list-message-info">
                                    <small class="WhiteBlack">{{message.user}} <span style="color:#f90;font-weight:bold;">{{message.time.split(" ")[1].split(':').slice(0, 2).join(':')}}</span></small>
                                    <small class="WhiteBlack list-message-block-mail">{{message.departament}}</small>
                                </div>
                                <p class="list-message-block-message WhiteBlack" style="word-break: break-word;" :style="{'color': message.mode == 'js_mode' || message.mode == 'invisible' ? '#0ae' : '#fff'}" :class="{'form_message': message.sender == 'offline_form'}" v-if="message.message" v-html="find_emojis(message.message)"></p>
                                <div style="display:flex;flex-direction:column;" v-if = "message.message_adds">
                                    <img v-for ="add in JSON.parse(message.message_adds)" :src="files_path + add" style="display:block;max-height:250px;max-width:250px;margin:10px;" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) == -1"></img>
                                    <p style="margin:20px;margin-left:0;" v-for ="add in JSON.parse(message.message_adds)" v-if="regexp.indexOf(add.substr(add.lastIndexOf('.'), add.length)) != -1">
                                        <a class="download_btn" :href="files_path + add" download  >Скачать {{add.split('.').slice(-1)[0]}}</a>
                                    </p>
                                </div>
                            </span>
                        </div>
                    </li>
                    <li :style="{'display': !messages_loaded ? 'flex !important' : 'none'}" class="list-message wow bounceInDown v-cloak-on" v-cloak>
                        <span class="date"><span class="v-cloak-div bgblackwhite WhiteBlack"><?php echo date("d.m.Y"); ?></span></span>
                        <div style="width:100%;display:flex;">
                            <span class="list-message-block bgblackwhite">
                                <div class="thisuserimg bgblackwhite v-cloak-block"></div>
                                <div class="list-message-info">
                                    <small class="WhiteBlack"><span class="v-cloak-text2" style="width:200px"></span><span class="v-cloak-text2" style="width:100px"></span></small>
                                    <small class="list-message-block-mail v-cloak-text2" style="width:100%;"></small>
                                </div>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                            </span>
                        </div>
                    </li>
                    <li :style="{'display': !messages_loaded ? 'flex !important' : 'none'}" class="list-message wow bounceInDown v-cloak-on" v-cloak>
                        <span class="date"><span class="v-cloak-div bgblackwhite WhiteBlack"><?php echo date("d.m.Y"); ?></span></span>
                        <div style="width:100%;display:flex;"  class="message-by-me">
                            <span class="list-message-block bgblackwhite">
                                <div class="thisuserimg bgblackwhite v-cloak-block"></div>
                                <div class="list-message-info">
                                    <small class="WhiteBlack"><span class="v-cloak-text2" style="width:200px"></span><span class="v-cloak-text2" style="width:100px"></span></small>
                                    <small class="list-message-block-mail v-cloak-text2" style="width:100%;"></small>
                                </div>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                            </span>
                        </div>
                    </li>
                    <li :style="{'display': !messages_loaded ? 'flex !important' : 'none'}" class="list-message wow bounceInDown v-cloak-on" v-cloak>
                        <span class="date"><span class="v-cloak-div bgblackwhite WhiteBlack"><?php echo date("d.m.Y"); ?></span></span>
                        <div style="width:100%;display:flex;"  class="message-by-me">
                            <span class="list-message-block bgblackwhite">
                                <div class="list-message-info">
                                    <small class="WhiteBlack"><span class="v-cloak-text2" style="width:200px"></span><span class="v-cloak-text2" style="width:100px"></span></small>
                                    <small class="list-message-block-mail v-cloak-text2" style="width:100%;"></small>
                                </div>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                                <p class="list-message-block-messag v-cloak-text2" style="width:100%;margin:0;margin-top:10px;"></p>
                            </span>
                        </div>
                    </li>
                </ul>
                <div id="chat_footer" class="card-body bgblackwhite">
                    <div v-if="smiles_mode" class="smiles_container bgblackwhite v-cloak-off" v-cloak>
                        <div class="smiles_folder bgblackwhite" v-for="(folder, folder_name) in emojis">
                            <h2 onclick="smiles_folder()" class="smiles_name WhiteBlack">{{folder_name}}</h2>
                            <div class="smiles smiles_close">
                                <div class="smile" v-for="(smile, smile_key) in folder">
                                    <span @click="select_smile(smile_key, folder_name, smile)" class="smile_photo" :style="'background-image:url(/emojis/'+folder_name+'/'+smile"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div type="text" :contenteditable="load ? 'true': 'false'" aria-multiline="true" role="textbox"  class="chat_block_textarea form-control WhiteBlack"></div>
                    <div id="placeholder" class="WhiteBlack v-cloak-off" v-cloak>
                        {{
                            load ? "Введите ваше сообщение" : 'Идёт отправка вашего сообщения'
                        }}
                    </div>
                    <div class="v-cloak-text2 v-cloak-on" id="placeholder" v-cloak style="max-width:350px;"></div>
                    <div class="btns_panel v-cloak-off" v-cloak>
                        <span v-if="load" @click="smiles_mode = !smiles_mode;" class="btns_panel_btn send_smile"></span>
                        <span v-if="load" @click="commands_mode = !commands_mode" class="btns_panel_btn commands_list"></span>
                        <input @change="handleChange()" multiple name='addphoto' class='add_photo'  id='add_photo' type='file' style='display:none;' />
                        <label class="btns_panel_btn add_file" v-if="load" for="add_photo" ></label>
                        <button class="btns_panel_btn Send_message_button" v-if="load" style="cursor:pointer;" v-on:click = "send" type="button"></button> 
                        <span v-if="!load" class="msg_loader"></span>
                    </div>   
                    <div class="btns_panel v-cloak-on" v-cloak>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                        <span class="btns_panel_btn v-cloak-block" style="border-radius:50%;"></span>
                    </div>
                </div>
            </div>
        </div>
        <div id="upload_files_preview" class="v-cloak-off" v-cloak>
            <div class="preview_img_block" v-for="(file, index) in files">
                <span class="preview_remove" @click="removeFile(index)"><span></span><span></span></span>
                <span class="preview_img" style="background-image:url(/scss/imgs/document.png);background-size:contain;background-position:center;background-repeat:no-repeat;"></span>
                <p :title="file.name" style="position:absolute;bottom:7px;left:0;color:#fff;background:#0ae;padding:10px;border-radius: 10px; font-size: 18px;font-weight: bold;white-space: nowrap;max-width:120px;text-overflow: ellipsis;overflow: hidden;">{{file.name}}</p>
            </div>
        </div>
    </div>
    <?php appendfooter(); ?>
</body>
<script src="/scripts/libs/vue.js"></script>
<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
<script type="text/javascript" src="/scripts/router?script=main"></script>
</html>