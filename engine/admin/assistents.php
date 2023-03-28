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
    <meta charset="utf-9">
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
	<?php admin_navigation('assistents'); ?>
    <section id='container'>
        <?php section_header('Ассистенты', 'operator.png'); ?>
        <div style="width:100%;display:flex;align-items:center;justify-content:center;flex-direction:column;">
            <p class="text1" style="margin-bottom:10px;">Поиск по заблокированным - banned</p>
            <input @keyup="search()" placeholder="поиск" class="crm_serch_input" type="text" />
            <div class="users_container">
                <div class="user_card" v-for="(user, index) in searchmas">
                    <span class="user_id">ID {{user.id}}</span>
                    <span class="user_photo" :style="'background-image:url(/assistent_photos/'+user.photo+');'"></span>
                    <p style="font-size:15px;color:#0ae;font-weight:bold;" v-for="domain in user.domain.domains">{{domain}}</p>
                    <p style="font-size:15px;color:#0ae;font-weight:bold;" v-if="user.domain.domains.length == 0">Нет созданных доменов</p>
                    <p><span style="color:#f90;font-weight:bold;">{{user.time.split(' ')[1].split(':').slice(0, 2).join(':')}}</span> {{user.time.split(' ')[0].split('-').reverse().join('.')}}</p>
                    <p>{{user.name}}</p>
                    <p>{{user.email}}</p>
                    <button style='border-color:#0ae;color:#0ae;' v-if='!user.ban' @click="profile(index, user.boss_id)">Перейти в профиль</button>
                </div>
            </div>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script src='/scripts/libs/vue.js'></script>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>