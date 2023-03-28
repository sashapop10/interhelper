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
	$assistents_path = VARIABLES["photos"]["assistent_profile_photo"]["upload_path"];
	mysqli_close($connection);
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
	<script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
	<?php navigation('dialogs', $info); ?>
	<section id='container'>
		<?php section_header('Диалоги', 'leftImg4.png'); ?>
		<input @keyup="search()" placeholder="поиск" class="cards_search_input bgblackwhite WhiteBlack v-cloak-off" type="text" v-cloak/>
		<input @keyup="search()" placeholder="поиск" class="cards_search_input v-cloak-block v-cloak-on" v-cloak></div>
		<div v-if='Object.keys(userlist).length > 0' style='width:100%;border-radius:0;margin-top:0;'class='Online-List-User2 v-cloak-off' v-cloak>  
			<span class="OnlineUser wow bounceInUp" :style="'border-color:'+room.photo.color+';'" v-for="(room, index) in sort_mas(searchmas)">
				<p class='user_domain' v-for='domain in room["domains_list"]["domains"]' >{{ domain }}</p>
				<p v-if="room.crm"  style = "color:#f90;font-size:20px;">CRM</p>
				<span v-if="!room.crm" class="OnlineUser-image2" style="background-color:#252525;"  :style='"background-image:url(/visitors_photos/"+room.photo.img+");background-size:80%;background-color:"+room.photo.color+";"'></span>
				<span v-else class="OnlineUser-image2" style="background-color:#252525;"  :style='{backgroundImage: "url(/crm_files/"+crm_clients[index].photo+")"}'></span>
				<p style='color:#0ae;' style='margin-top:20px;'>Посетитель {{room['info']['ip'] }}</p>
				<p>{{ room['info']['geo']['country'] }} / {{ room['info']['geo']['city'] }}</p>
				<p v-if="room['forms'] != 0" style="color:#3EB489;font-weight:bold;font-size:12px;">Отправлено форм {{room['forms']}}</p>
				<p v-if="room['user_msgs'] != 0" style="color:#f90;font-weight:bold;font-size:12px;">Сообщений посетителя {{room['user_msgs']}}</p>
				<p v-if="room['assistent_msgs']!= 0" style="color:#0ae;font-weight:bold;font-size:12px;">Сообщений обслуживающих {{room['assistent_msgs']}}</p>
				<div class="room_property" v-if='Object.keys(room.properties.properties).length > 0' v-for="(property_value, property_name) in room.properties.properties" >
					<p style="color:#f90;word-break:break-word;" v-html="property_name"></p>
					<p style="color:#fff;word-break:break-word;" v-html="property_value"></p>
				</div>
				<p>
					Время на сайте
					{{
						((room.session_time||0) / 1000 / 60).toFixed(1)
					}} мин
				</p>
				<div class="consulation_list_block" v-if = 'room.serving_list.assistents.length > 0 '>
					<h2>Обслуживают</h2>
					<div class="consulation_list">
						<div v-for="assistent in room.serving_list.assistents" v-if="assistents[assistent]">
							<span :style='"background-image:url(<?php echo $assistents_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
							<p>{{assistents[assistent]["name"]}}</p>
						</div>
					</div>
				</div>
				<div class="consulation_list_block" v-if = 'room.served_list.assistents.length > 0 '>
					<h2>Обслуживали</h2>
					<div class="consulation_list">
						<div v-for="assistent in room.served_list.assistents" v-if="assistents[assistent]">
							<span :style='"background-image:url(<?php echo $assistents_path; ?>"+assistents[assistent][`photo`]+");"' class="cons_photo"></span>
							<p>{{assistents[assistent]["name"]}}</p>
						</div>
					</div>
				</div>
				<p style='color:#0ae;'>{{room['time'].split(' ')[0].split('-').reverse().join('.')}} <span style="font-weight:bold;color:#f90;">{{room['time'].split(' ')[1].split(':').splice(0, 2).join(':')}}</span></p>
				<button @click='changeChat(room["room_link"])' >Переписка</button>
			</span>
			<form @click="load_more()" v-if="check_count()" style="cursor:pointer;border-color:#0ae;width: 160px;height: 50px;" class="OnlineUser wow bounceInUp">
				<p style="color:#0ae;" >Загрузить ещё</p>
			</form>
		</div>
		<div v-else style='display:flex;justify-content:center;align-items:center;width:100%;border-radius:0;margin-top:0;'class='Online-List-User2 v-cloak-off' v-cloak> 
			<p style='color:#0ae;' class='text1'>Тут пока ничего нет...</p>
		</div>		
		<div class="Online-List-User2 v-cloak-on" style="border-radius:0;width:100%;" v-cloak>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
			<form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form> <form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form> <form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form> <form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form> <form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form> <form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form> <form class="OnlineUser wow bounceInUp">
				<span class="OnlineUser-image2 v-cloak-block"></span>
				<span class= "visitor_device v-cloak-block"></span>
				<span class="room_options room_options_close v-cloak-block" ></span>
				<p style="margin-top:30px !important;" class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<p class="v-cloak-text"></p>
				<button type="button" class="v-cloak-block"></button>   
			</form>
		</div>
	</section>
	<?php appendfooter(); ?>
	<script src="/scripts/libs/vue.js"></script>
	<script src="/server/node_modules/socket.io/client-dist/socket.io.js"></script>
	<script type="text/javascript" src="/scripts/router?script=main"></script>
</body>
</html>