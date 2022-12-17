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
	$selectors = '';
	$json_array = json_decode($user_settings, JSON_UNESCAPED_UNICODE);
	foreach($json_array['departaments'] as $content){
	    $selectors .= '<option>'.$content.'</option>';
	}
    $sql = "SELECT name, ip, phone, status, photo, buttlecry, departament, email,id FROM assistents";
    $resultcomand = mysqli_query($connection, $sql);
	$assistents = mysqli_fetch_all($resultcomand, MYSQLI_ASSOC);

	$assistent_count = 0;
	foreach($assistents as $assistent){
	    
	    $assistent_info_name[] = $assistent['name'];
	    $assistent_info_ip[] = $assistent['ip'];
	    $assistent_info_phone[] = $assistent['phone'];
	    $assistent_info_status[] = $assistent['status'];
	    $assistent_info_photo[] = $assistent['photo'];
	    $assistent_info_departament[] = $assistent['departament'];
	    $assistent_info_buttlecry[] = $assistent['buttlecry'];
	    $assistent_info_email[] = $assistent['email'];
	    $assistent_info_id[] = $assistent['id'];
	    $assistent_count+=1;
	}
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
	if (isset($_SESSION["loginkey"])) {
		head();
		echo "
		

		<section id='container'>
			<div id='section_top'><div id='logo'><h1>InterHelper</h1></div><div id='section_name'><h2>Assistents</h2></div></div>
			<div id='middle_part'> 
			
					<form id='add_assistent_block' class='send_ajax_form' method ='post' action = '/php/changeSettings.php'>
					     <p class='text1'>* - не обязательный пункт</p>   
					 	
						<div><h2>Имя:</h2><input name='assistent_name' type='text'/></div>
						<div><h2>Текст приветствия *:</h2><input name='assistent_buttleCry' type='text'/></div>
						<div><h2>Отдел:</h2><select name='assistent_departament'><option disabled selected></option>".$selectors."</select></div>
						<div><h2>Почта:</h2><input name='assistent_email' type='text'/></div>
						<div><h2>Пароль:</h2><input name='assistent_password' type='password'/></div>
						<div><h2>Пароль<br/>(второй раз):</h2><input name='assistent_passwordSecondTime' type='password'/></div>
						<div><h2>Телефон *:</h2><input id='new_assistent_phone' name='assistent_phone' type='phone'/></div>
						
						<button type='submit'>Добавить</button>
						
					</form>
					<p class='text1'>Созданные ассистенты, также входят через официальный сайт InterHelper.ru</p>
					<div id='add_new'>
						<h2 class='header1'>Добавить нового ассистента</h2>
						<div id='add_new_assistent'>Добавить</div>
					</div>
					<h2 class='header1'>Список ассистентов</h2>
					<div id = 'column2'>
					<div id='assistents_list'>
					
					</div>
					</div>
				
			</div>
		
		</section>
		<script>$('#add_new_assistent').on('click', ()=>{
			$('#add_assistent_block').css('max-height', '37em');
			});</script>
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
	background: url(scss/imgs/leftImg2.png) no-repeat center center;
	background-size: contain;
	}
</style>
<script type="text/javascript">
    let append_block = $('#assistents_list'); 
	$(document).ready(function() {
		$('.opt4').removeClass('target');
		$('.opt4').attr('class', 'active');
		$('.active p').css('color','#fff');
		$('.active p').css('opacity','1');
	});
	
	function addnewassistent(name,ip,phone,email,departament,status,photo,buttlecry,id){
		    var new_assistent = $('<div class=assistent ></div>')
		    var new_assistent_img = $('<div class=assistent_img style="background-image:url('+photo+'); background-repeat:no-repeat; background-size:cover;"></div>');
		    var new_assistent_name = $('<input data-email='+email+' class=changable_assistent name="assistent_changename" value="'+name+'" />');
		    var new_assistent_departament = $('<select data-email='+email+' class=changable_assistent name="assistent_changedepartament"><option selected disabled>'+departament+'</option><?php echo $selectors;  ?></select>');
		    var new_assistent_phone = $('<input data-email='+email+' class=changable_assistent name="assistent_changephone" value="'+phone+'" />');
		    var new_assistent_status = $('<p style="font-size:1.6em;" class="assistent_status '+status+'">'+status+'</p>');
		    var new_assistent_email = $('<input data-email='+email+' class="changable_assistent" data-email='+email+' name="assistent_changeemail" value='+email+' />');
		    var new_assistent_ip = $('<p style="font-size:1.3em;" class=assistent_ip>'+ip+'</p>');
		    var new_assistent_buttle_cry = $('<textarea style="height:auto; resize:none;" data-email='+email+' class=changable_assistent name="assistent_changebuttlecry" >'+buttlecry+'</textarea>');
		    
		  
		    var changephoto = $('<label style="width:150px;" class="edit_assistent"><input data-email='+email+' class="changable_assistent_photo" style="display:none;" name="assistent_changephoto" type="file" />Сменить фото</label>'); 
		    var form = "<form class='send_ajax_form'><input style='display:none;' name='remove_assistent' value='"+email+"' /><button style='width:150px;' class ='remove_assistent' type='submit'>Удалить</button></form>";
		    
		    new_assistent.append(new_assistent_img);
		    new_assistent.append(new_assistent_status);
		    new_assistent.append(new_assistent_ip);
		    new_assistent.append(new_assistent_name);
		    new_assistent.append(new_assistent_departament);
		    new_assistent.append(new_assistent_phone);
		    new_assistent.append(new_assistent_email);
		    new_assistent.append(new_assistent_buttle_cry);
	
		   new_assistent.append(changephoto);
		    new_assistent.append(form);
		    
		    append_block.append(new_assistent);
		    
		}
	 <?php
	 if (isset($_SESSION["loginkey"])) {
    for($i = 0; $i < $assistent_count; $i++){
            $user_Photo = $assistent_info_photo[$i];
		    $user_Photo = str_replace(' ', '%20',  $user_Photo);
		    echo "
		    
    addnewassistent('".$assistent_info_name[$i]."', '".$assistent_info_ip[$i]."', '".$assistent_info_phone[$i]."', '".$assistent_info_email[$i]."', '".$assistent_info_departament[$i]."', '".$assistent_info_status[$i]."', '".$user_Photo."', '".$assistent_info_buttlecry[$i]."', '".$assistent_info_id[$i]."');
		    ";
	}
	if($assistent_count == 0){
	    echo "
	    text = $('<p class=text1 >Тут пока никого нет</p>');
	    append_block.append(text);
	    ";
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