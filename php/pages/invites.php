<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/php/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/php/func.php"); 
   
    checkuser();
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SESSION["loginkey"])) {
	    
	$clientEmail = strval($_SESSION["loginkey"]); 
	global $connection;
	$sql = "SELECT settings FROM users WHERE email='$clientEmail'";
	$resultcomand = mysqli_query($connection, $sql);
	$rows = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);
	foreach($rows as $row){
	$user_settings = $row['settings'];
	
	}
	
	
	$json_array = json_decode($user_settings, JSON_UNESCAPED_UNICODE);
	 foreach($json_array['InterHelperInvitesOptions']['InvitesForPages'] as $name => $values){
        
        $invite_show[] = $values['ShowAfterSeconds'];
        $invite_hide[] = $values['HideAfterSeconds'];
        $invite_message[] = $values['InviteText'];
       
        $invite_count += 1;
        $invite_name[] = $name;
    }
    
	$InvitesEvrywhereStatus = $json_array['InterHelperInvitesOptions']['InvitesForAllsystem'];
    $InvitescurrentStatus = $json_array['InterHelperInvitesOptions']['AlwaysShowInvite'];
    $InvitesSystemName = $json_array['InterHelperInvitesOptions']['SYSname'];
    $InvitesSystemText = $json_array['InterHelperInvitesOptions']['InviteText'];
    $InvitesSystemRetunText = $json_array['InterHelperInvitesOptions']['InviteForReturn'];
    $ShowAfterPagesCount = $json_array['InterHelperInvitesOptions']['ShowAfterPagesCount'];
    $ShowAfterSecondsCount = $json_array['InterHelperInvitesOptions']['ShowAfterSecondsCount'];
    $HideAfterSecondsCount = $json_array['InterHelperInvitesOptions']['HideAfterPagesCount'];
    
	}
	
	ip($ip);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<link rel="stylesheet" type="text/css" href="scss/reset.css">
	<link rel="stylesheet" type="text/css" href="scss/main.css">
	<link rel="stylesheet" type="text/css" href="scss/media.css">
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<link rel="stylesheet" type="text/css" href="/HelperCode/helper.css">
<script type="text/javascript" src="/HelperCode/Helper.js"></script>
	<?php
	head();
	if (isset($_SESSION["loginkey"])) {
		echo "
		
		<section id='container'>
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Chat invites</h2></div></div>
			<div id='middle_part'> 
					<div id = 'column1'>
						<div id='invites_block'>
						<div class='inline_box'><input class='checkbox2 changable_input2' type='checkbox' name='InvitesEvrywhereStatus' value='".$InvitesEvrywhereStatus."' ".$InvitesEvrywhereStatus." /><h2>Отправлять приглашения клиентам в системе</h2></div>
						<p class='text1'>Если вы установите его, помощники не смогут приглашать пользователей.</p>
						<div class='inline_box'><input class='checkbox2 changable_input2' type='checkbox' name='InvitesCurrentStatus' value='".$InvitescurrentStatus."' ".$InvitescurrentStatus." /><h2>Всегда отправляйте приглашения на страницах выбора</h2></div>
						<p class='text1'>Приглашения на страницах будут отображаться, даже если посетитель отклонил приглашения системы, <br/> если посетитель принимает приглашение, приглашения больше не будут отображаться.</p>
						
						<div class='inline_box'><input class='changable_input' style='border-left:3px solid #0ae;' name='Invite_sys_name' id='Invite_sys_name' value = '".$InvitesSystemName."'/><h2 class='header1' style='margin-top: 0px;margin-left:20px;margin-top:15px;'>- Имя системы</h2></div>
						<div>
							<h2>Текст системного приглашения</h2>
							<textarea class='changable_input'style='height:200px; border-top:3px solid #0ae;' name='InviteText'>".$InvitesSystemText."</textarea>
						</div>
						<div>
							<h2>Текст системного приглашения, если пользователь вернется обратно</h2>
							<textarea class='changable_input'style='height:200px; border-top:3px solid #0ae;' name='Invite_text_return'>".$InvitesSystemRetunText."</textarea>
						</div>
						<div>
						<h2>Показывать сообщение после <input type='number' name='inviteShowAfterPages' style='width:65px; height:30px; border-left:#0ae solid 3px; border-right:3px solid #0ae; text-align:center;' class='changable_input sec_count sec_count2' value ='".$ShowAfterPagesCount."' /> Страниц</h2>
						</div>
						<div>
						<h2>Показывать приглашение после <input type='number' name='inviteShowAfter' style='width:65px; height:30px; border-left:#0ae solid 3px; border-right:3px solid #0ae; text-align:center;' class='changable_input sec_count sec_count2' value ='".$ShowAfterSecondsCount."' /> Секунд</h2>
						</div>
						<div>
						<h2>Скрывать приглашение после <input type='number' name='inviteHideAfter' style='width:65px; height:30px; border-left:#0ae solid 3px; border-right:3px solid #0ae; text-align:center;' class='changable_input sec_count sec_count2' value ='".$HideAfterSecondsCount."' /> Секунд</h2>
						</div>
						<p class='text1'>Если вы выберете 0, тогда приглашение никогда не скроется</p>
						
						<h2 class='header1'>Приглашения для страниц</h2>
						<form class='send_ajax_form'  method='post' style='margin-top:50px;height:auto;padding:30px;width:100%; border:3px solid #fff; border-radius:10px;'>
						    <input name='url_page' placeholder='url страницы' style='height:40px;width:auto; padding-left:10px;background:#eee; color:#000;border:none; outline:none;' />
						    <h2 class='header2' style='margin-top:20px; color:#fff;font-size:1.2em;'>Текст приглашения для страницы</h2>
						    <textarea name='page_invite_text' style='background:#0ae; color:#fff;resize:none; height:80px; min-width:250px; width:auto;'></textarea>
						    <p class='text2' style='margin-top:20px; font-size:1.1em;'>Показывать приглашения в чат спустя <input type='number' style='width:60px; text-align:center;border:none;outline:none; background:#0ae; color:#fff; height:30px;' name='Current_invite_show_after' value='0' /> секунд</p>
						    <p class='text2' style='margin-top:20px; font-size:1.1em;'>Скрывать приглашения в чат через <input type='number' style='width:60px; text-align:center;border:none;outline:none; background:#0ae; color:#fff; height:30px;' name='Current_invite_hide_after' value='0' /> секунд*</p>
						    <button style='margin-top:20px; background:#0ae;height:50px;width:200px;border:none;outline:none;color:#fff;cursor:pointer;' type='submit'>Добавить</button>
						</form>
					</div>
					<h2 class='header1'>Список существующих приглашений</h2>
					<div id='invite_appenbody' style='display:inline-flex;height:auto;width:100%;margin-top:20px;'>
					   
					    
				
			</div>
			<a href= '/index.php' id ='return_to_home_page'></a>
		</section>
		";
	
	}
	else{
			echo '<script>window.location.replace("/index.php");</script>';
	}
	
	appendfooter();
	?>
<style type="text/css">
	#section_name::after{
	content: '';
	margin-left: 20px;
	position: relative;
	height: 40px;
	width: 40px;
	background: url(scss/imgs/leftImg4.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('.opt6').removeClass('target');
		$('.opt6').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
	 function createinvite(name,text2,show,hide){
	        var NewInvite = $("<div class='created_invite'></div>");
	        var text = $("<h2 class='header2 input_h2' >Текст приглашения для страницы</h2>");
	        var textarea =  $("<textarea name='page_invite_text' data-url='"+name+"' class='input_textarea changable_input_invite'>"+text2+"</textarea>");
	        var ShowSec = $("<p class='invite_text' >Показывать приглашения в чат спустя <input type='number' data-url='"+name+"' class='invite_seconds changable_input_invite' name='Current_invite_show_after2' value='"+show+"' /> секунд</p>");
	        var Hidesec = $("<p class='invite_text' >Скрывать приглашения в чат через <input type='number'  data-url='"+name+"'    class='invite_seconds changable_input_invite' name='Current_invite_hide_after2' value='"+hide+"' /> секунд*</p>");
	        var removebtn = $("<form class='send_ajax_form'><input style='display:none;' value='"+name+"' name= 'invite_remove' data-url='"+name+"'/><button type='submit' class='invite_remove'>Удалить</button>");
	        var InviteUrl = $("<input name='url_page' placeholder='url страницы' data-url='"+name+"' class='input_url changable_input_invite' value = "+name+" />");
	        
	        NewInvite.append(InviteUrl);
	        NewInvite.append(text);
	        NewInvite.append(textarea);
	        NewInvite.append(ShowSec);
	        NewInvite.append(Hidesec);
	        NewInvite.append(removebtn);
	       
	        
	        appendbody.append(NewInvite);
	    };
	let appendbody = $('#invite_appenbody');
	<?php 
	if (isset($_SESSION["loginkey"])) {
	   
	    if($invite_count == 0){
	      echo 'appendbody.append("<p class=text1 >Тут пока ничего нет</p>");'; 
	   }
	   else{
	    for($i = 0; $i < $invite_count; $i++){
	        
	        echo "createinvite('".$invite_name[$i]."','".$invite_message[$i]."','".$invite_show[$i]."','".$invite_hide[$i]."');";
	    }
	   }
    
	}
	?>
</script>
<?php
if (isset($_SESSION["loginkey"])) {
ajaxs();
}
?>

<script type="text/javascript" src="scripts/script.js"></script>
</body>
</html>