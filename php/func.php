<?php
//user ip, visits, lastvisit time
function ip($ip)
{
	//connection
	global $connection;
	$visitor_ip = $ip;
	$today = date("H:i:s");
	$no_ip = 'no';

	$sql = "SELECT ip FROM guest WHERE ip='$visitor_ip'";
	$resultcomand = mysqli_query($connection, $sql);
	$guest_ip = mysqli_fetch_row($resultcomand);

	$sql = "SELECT ip FROM users WHERE ip='$visitor_ip'";
	$resultcomand = mysqli_query($connection, $sql);
	$user_ip = mysqli_fetch_row($resultcomand);

		//if guest already created
	if($visitor_ip == $guest_ip[0] && $visitor_ip != $user_ip[0]){			
		$no_ip = 'yes';
		
		$sql2 = "UPDATE guest SET visits=visits + 1, visitTime='$today' WHERE ip='$visitor_ip'";
		if ($connection->query($sql2) === TRUE) {}
	
	}
		//if user already created
	elseif($visitor_ip != $guest_ip[0] && $visitor_ip == $user_ip[0]){
		$no_ip = 'yes';

		$sql2 = "UPDATE users SET visits=visits + 1, LastVisit='$today' WHERE ip='$visitor_ip'";
		if ($connection->query($sql2) === TRUE) {}
	}	
		//new guest
	if($no_ip == 'no' && $visitor_ip != $user_ip[0]){

		$sql2 = "INSERT INTO guest(id,ip, visits,visitTime) VALUES (0,'$visitor_ip',1,'$today')";
		if ($connection->query($sql2) === TRUE) {}
	}
	mysqli_close($connection);
	// if true --> mail request on
	// needs php code
	echo '<script>let first_message = true</script>';
	
}
//update content func (for logined users) 
function content($user_photo){
	if(isset($_SESSION["loginkey"])){
		// if loginned 
		
		$user_photo = str_replace(' ', '%20', $user_photo);
		echo '<script>$(document).ready(function(){
    $(".loginingmenu").remove();
    $(".removeable").remove();
     $(".removeable").remove();
     $("#footer_fourth_column").append("<div style=position:relative;><a href=/php/pages/profile.php class=user_Profile style =border:none;margin-left:10px;height:80px;width:80px;border-radius:50%;background-image:url('.$user_photo.');background-repeat:no-repeat;background-size:cover;background-position:center;cursor:pointer;></a><form class=ajax_login_form method=post action=/php/login.php><input style=display:none; name=remove /><input class=exit_button style=bottom:-120%;  type=submit></form></div>");
    
    $("#navmenu").append("<div style=position:relative;><a href=/php/pages/profile.php class=user_Profile style =height:80px;width:80px;background-image:url('.$user_photo.');border-radius:50%;background-repeat:no-repeat;background-size:cover;background-position:center;cursor:pointer;></a><form class=ajax_login_form method=post action=/php/login.php><input style=display:none; name=remove /><input class=exit_button   type=submit></form></div>");

    $("#menu").append("<li style=position:fixed;bottom:25%;left:50%;transform:translateX(-30%);height:120px;width:120px;><a href=/php/pages/profile.php class=user_Profile style =height:120px;width:120px;border-radius:50%;background-image:url('.$user_photo.');background-repeat:no-repeat;background-size:cover;background-position:center;cursor:pointer;></a><form class=ajax_login_form method=post action=/php/login.php><input style=display:none; name=remove /><input style=bottom:5%;right:25%;  class=exit_button  type=submit></form></li>");
});</script>';
	}
	// other
	else{
	// standart rule
	}
}

//admin head
function checkuser(){
    if (isset($_SESSION["loginkey"])) {
        $email_for_check = $_SESSION["loginkey"]; 
        global $connection;
        $sql = "SELECT email FROM users WHERE email = '$email_for_check'";
        $resultcomand = mysqli_query($connection, $sql);
	    $check_emaiL_exist = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);
	    if($check_emaiL_exist[0] == ''){
			unset($_SESSION['loginkey']);
			session_destroy();
			
			$acceess = 'denied';
	    }
	    else{
	        $acceess = 'accepted';
	    }
    }
    else{
        $acceess = 'denied';
    }
}
function head(){
	echo "<header>
		<div id='top_header_part'>
			<div id='/php/pages/profile.php' class = 'target opt1'><p'>Настроки</p></div>
			<div id='/php/pages/code.php'  class='target opt2'><p>Получить код</p></div>
		</div>
		 <a href= '/index.php' class='specialtarget' style='color:#fff;text-align:center;text-decoration:none;padding:10px;display:flex;justify-content:center;align-items:center;min-height:100px;flex-direction:column;text-decoration:none;height:auto;' >На главную страницу</a>
		<div id='bottom_header_part'>
		   
			<div id='/php/pages/Departaments.php' class='target opt3'><p>Отделы</p></div>
			<div id='/php/pages/Assistents.php' class='target opt4'><p>Ассистенты</p></div>
			<div id='/php/pages/options.php' class='target opt5'><p>Настройки чата</p></div>
			<div id='/php/pages/invites.php' class='target opt6'><p>Приглашения в чат</p></div>
			<div id='/php/pages/messages.php' class='target opt7'><p>Системные сообщения</p></div>
			<div id='/php/pages/Offline.php' class='target opt8'><p>Offline форма</p></div>
			<div id='/php/pages/BlackList.php' class='target opt9'><p>Чёрный список</p></div>
			<div id='/php/pages/DialogList.php' class='target opt10'><p>Список диалогов</p></div>
			
		</div>
		</header>";

}
//consultant head
function head2(){
	echo "<header>
		<div id='top_header_part'>
			<div id='/php/consultant/assistent.php' class = 'target opt1'><p>Настройки</p></div>
			
		</div>
		<div id='bottom_header_part'>
			<div id='/php/consultant/Consultation.php' class='target opt2'><p>Консультация</p></div>
			<div id='/php/consultant/consultantchat.php' class='target opt3'><p style='text-align:center;'>Чат с консультантами</p></div>
			<div id='/php/consultant/userlist.php' class='target opt4'><p>Список пользователей</p></div>
						
		</div>
		</header>";

}
function appendfooter(){
    echo '
    <footer>
<div id="footer_top">	
	<div class="column" id="footer_first_column">
		<div id="footer_logo">
			<div id="foorer_logo_img"></div>
			<h2>InterHelper</h2>
		</div>
		<div id="footer_short_about">
			<h2 class="footer_header">О InterHelper</h2>
			<p>Лучший интернет ассистент <br /> для оживления вашего сайта.</p>
		</div>
		<div id="footer_contacts">
			<h2 class="footer_header">Контакты</h2>
			<div><div class="icon"></div><a href="https://interfire.ru/">interfire.ru</a></div>
			<div><div class="icon"></div><a href="mailto:#">ourmail@mail.com</a></div>
		</div>
	</div>
	<div class="column" id="footer_second_column">
		<h2 class="footer_header">Информация</h2>
		<a class="ToSection2" href="/index.php">Главная</a>
		<a class="ToSection2" href="/index.php#instructions">О InterHelper</a>
		<a href="https://interfire.ru/portfolio">О нас</a>
		<a href="/index.php#instructions#reviews_block" class="ToSection2">Отзывы</a>
	</div>
	<div class="column" id="footer_third_column">
		<h2 class="footer_header">Полезные ссылки</h2>
		<a href="#">Политика конфиденциальности</a>
		<a href="#">Поддержка</a>
	</div>
	
</div>
<div id = "footer_bottom">
	<div id="links2">
		<a href="#" class="follow_img"></a>
		<a href="#" class="follow_img"></a>
		<a href="https://interfire.ru/" class="follow_img"></a>
	</div>
	<div id="last_text"><span id="copyright"></span><p>2020 interfire. All Right reserved</p></div>
</div>
</footer>
<style>
footer{
margin-top:100px;
	height: 500px;
	width: calc(100% - 120px);
	left:120px;
	position: relative;
	display: flex;
	flex-direction: column;
	align-items: center;
}
#footer_top{
	height: 80%;
	width: 80%;
	display: inline-flex;
	position: relative;
	font-weight: 700;
}
.column{
	width: 33%;
	height: 100%;
	position: relative;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: flex-start;
}
#footer_bottom{
	height: 20%;
	width: 80%;
	display: inline-flex;
	justify-content:space-between;
	align-items: center;
	border-top: 2px solid #fff;
	color: #fff;
	
}
#last_text{
	display: inline-flex;
	justify-content: center;
	align-items: center;

}

#footer_logo{
	height: 80px;
	width: 100%;
	display: flex;
	justify-content: center;
	float: left;
	flex-direction: column;

}
#footer_short_about{
	position: relative;
	top: 50px;
	color: #fff;
	text-align: left;
	
}
#footer_short_about p {
	position: relative;
	top: 5px;
	font-size: 1em;
}
#footer_contacts{
	position: relative;
	top: 100px;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
}
#footer_contacts div{
	display: inline-flex;
	justify-content: center;
	align-items: center;
	margin-top: 5px;
	margin-bottom: 5px;
}
.icon{
	height: 30px;
	width: 30px;
	position: relative;
}
#footer_contacts div:nth-child(2) .icon{
	background: url(/scss/imgs/logo.png) no-repeat center center;
	background-size: cover;
	height: 30px;
	width: 30px;
	position: relative;
}
#footer_contacts div:nth-child(3) .icon{
	background: url(/scss/imgs/email.png) no-repeat center center;
	background-size: cover;
	height: 30px;
	width: 30px;
	position: relative;
}
#footer_contacts a{
	text-decoration: none;
	color: #fff;
	margin-left: 10px;
}
#foorer_logo_img{
	height: 60px;
	width: 60px;
	position: relative;
	bottom: 20px;
	background: url(/scss/imgs/logo.png) no-repeat center center;
	background-size: contain;
}
#footer_logo h2{
	color: #fff;
}
.footer_header{
	color: #0ae;
	font-size: 1.6em;
}
#footer_second_column{
	display: flex;
	justify-content: flex-start;
	align-items: flex-start;
	flex-direction: column;
}
#footer_second_column h2{
	position: relative;
	top: 20px;

}
#footer_second_column a{
	position: relative;
	top: 20px;
	color: #fff;
	margin-top: 20px;
	text-decoration: none;
}
#footer_third_column{
	display: flex;
	justify-content: flex-start;
	align-items: flex-start;
	flex-direction: column;
}
#footer_third_column h2{
	position: relative;
	top: 20px;

}
#footer_third_column a{
	position: relative;
	top: 20px;
	color: #fff;
	margin-top: 20px;
	text-decoration: none;
}

#To_up_button{
	position: absolute;
	bottom: 50px;
	right: 50px;
	height: 40px;
	width: 40px;
	border-radius: 5px;
	background: #0ae url(/scss/imgs/up-arrow.png) no-repeat center center;
	cursor: pointer;
	background-size: 80%;
}
#copyright{
	height: 15px;
	width: 15px;
	position: relative;
	background: url(/scss/imgs/copyright.png) no-repeat center center;
	background-size: contain;
	margin-right:5px;
}
#links2{
	width: auto;height: 50px;
	display: inline-flex;
	justify-content: center;
	align-items: center;
}
#links2 a:nth-child(1){
	background: url(/scss/imgs/vk.png) no-repeat center center;
	background-size: contain;
	transform: rotate(0deg);
	height: 30px;
	margin-left:10px;
	width: 40px;
}
#links2 a:nth-child(2){
	background: url(/scss/imgs/telegram.png) no-repeat center center;
	background-size: contain;
	transform: rotate(0deg);
	height: 30px;
	width: 30px;
	margin-left:10px;
}
#links2 a:nth-child(3){
	background: #000 url(/scss/imgs/logo.png) no-repeat center center;
	background-size: 80%;
	transform: rotate(0deg);
	height: 30px;
	width: 30px;
	margin-left:10px;
	border-radius:50%;
}
#footer_first_column a:hover {
    color: rgba(255,255,255,0.5);
    transition: 0.15s;
}
#footer_second_column a:hover {
    color: rgba(255,255,255,0.5);
    transition: 0.15s;
}
#footer_third_column a:hover {
    color: rgba(255,255,255,0.5);
    transition: 0.15s;
}
@media only screen and (max-width: 1100px){
	#footer_third_column{
		display: none;
	}
	#footer_first_column{
		width: 300px;
	}
	#footer_second_column{
		width:300px;
	}
	#footer_top{
	    align-items:center;
	}
	
}
@media only screen and (max-width: 800px){
	#footer_second_column{
		display:none;
	}
	
}
@media only screen and (max-width: 650px){
	#links2{
		display:none;
	}
	
}
</style>
    ';
}
function ajaxs(){
    echo "
    <script>
       //settings ajax
$('.changable_input').on('focusin', function(e){
elementValue = $(this).val();

});
$('.changable_input').focusout(function(){
    if(elementValue != $(this).val()){
    inputname = $(this).attr('name');
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: ''+inputname+'='+$(this).val(),
      
      success: function(data) {
        alert(data);
        location.reload();
        
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
        });
    }
    else{
        
    }  
});
//assistent info
$('.changable_assistent').on('focusin', function(e){
elementValue = $(this).val();
email = $(this).attr('data-email');
})
$('.changable_assistent').focusout(function(){
    if(elementValue != $(this).val()){
    inputname = $(this).attr('name');
    
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: {'changesName': inputname,'changesValue':  $(this).val(), 'assistent_email_global': email},
      
      success: function(data) {
        alert(data);
        
        location.reload();
        
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
        });
    }
    else{
        
    }  
});
    //checkbox
$('.changable_input2').on('click', function(){
    let inputname = $(this).attr('name');
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: ''+inputname+'='+$(this).val(),
      
      success: function(data) {
        alert(data);
        location.reload();
        
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
    });
});
    //img
$('.changable_input3').change(function(e){
   let inputname = $(this).attr('name');
   let fd = new FormData;
   fd.append(inputname, $(this).prop('files')[0]);

    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: fd,
      processData: false,
      contentType: false,
      success: function(data) {
        alert(data);
        location.reload();
        
     
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
    });
    
});
    //update img
$('.changable_assistent_photo').change(function(e){
   let inputname = $(this).attr('name');
   let fd = new FormData;
   let email_gloabal = $(this).attr('data-email');
   let email =  $(this).attr('data-email');
   fd.append(inputname, $(this).prop('files')[0]);
   fd.append('assistent_email_gloabal_for_img', email_gloabal);
   
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: fd,
      processData: false,
      contentType: false,
      success: function(data) {
        alert(data);
        location.reload();
        
     
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
    });
    
});
    //for forms
    $('.send_ajax_form').submit(function(e) {
    
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: $(this).serialize(),

      success: function(data) {
        alert(data);
        location.reload();

      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
        });
    
    });
    // for color with label
    $('.changable_color').change(function(e){
   let inputname = $(this).attr('name');
   let inputvalue = $(this).val();

    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: ''+inputname+'='+inputvalue,
      success: function(data) {
        alert(data);
        location.reload();
        
     
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
    });
    
});
    //for InterHelper position
 $('.change_position_ajax').on('click', function(e){
   let inputname = 'InterHelper_button_position';
   let inputvalue = $(this).attr('id');

    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: ''+inputname+'='+inputvalue,
      success: function(data) {
        alert(data);
        location.reload();
        
     
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
    });
    
});
    //add fast message
     $('.add_button_ajax').on('click', function(e){
   let inputname = $(this).attr('name');;
   let inputvalue = $(this).attr('value');
  
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: ''+inputname+'='+inputvalue,
      success: function(data) {
        alert(data);
        location.reload();
        
     
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
    });
    
});
       //settings ajax
$('.changable_input_atmessages').on('focusin', function(e){
elementValue = $(this).val();

});
$('.changable_input_atmessages').focusout(function(){
    if(elementValue != $(this).val()){
    elementData = $(this).attr('data-message');
    inputname = $(this).attr('name');
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: {messageForchangename:$(this).attr('name'),messageForchangevalue:$(this).val(),'messageForchange':elementData},
      
      success: function(data) {
        alert(data);
        location.reload();
        
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
        });
    }
    else{
        
    }  
});
//invite redactor
$('.changable_input_invite').on('focusin', function(e){
elementValue = $(this).val();

});
$('.changable_input_invite').focusout(function(){
    if(elementValue != $(this).val()){
    elementData = $(this).attr('data-url');
    inputname = $(this).attr('name');
    $.ajax({
      type: 'POST',
      url: '/php/changeSettings.php',
      data: {inviteName:$(this).attr('name'),inviteValue:$(this).val(),'InviteDataUrl':elementData},
      
      success: function(data) {
        alert(data);
        location.reload();
        
      },
      error: function() {
        alert('Ошибка в ajax запросе! ');

      }
        });
    }
    else{
        
    }  
});
   </script> ";
}
?>
