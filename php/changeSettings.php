<?php
    session_start();
	$error_info = '';
    $error_count = 0;
    $user_mail = strval($_SESSION["loginkey"]);
        //pass
	if (isset($_POST['oldpass']) && trim($_POST['oldpass'] != '') && isset($_POST['newpass']) && trim($_POST['newpass'] != '') && isset($_POST['repeatnewpass']) && trim($_POST['repeatnewpass'] !='')) {
		if(isset($_SESSION["loginkey"])){
		    
		    // post data
			$oldpass = trim($_POST['oldpass']);
			$newpass = trim($_POST['newpass']);
			$repeatnewpass = trim($_POST['repeatnewpass']);
			//connection
			include 'connection.php';
			global $connection;
			//mail from session
			
			//password from database
			if(strlen(trim($newpass)) < 7 or strlen(trim($newpass)) > 30)
			{
			$error_info .= '/ Пароль должен быть больше 7 символов и меньше 30! /';
			$error_count+=1;
			}
			$sql = "SELECT password FROM users WHERE email = '$user_mail'";
			$resultcomand = mysqli_query($connection, $sql);
			$user_password = mysqli_fetch_row($resultcomand);
			$user_pass = $user_password[0];
			// if new = old
			if(password_verify($oldpass, $user_pass) && $error_count == 0){
			    
			    //if new = repeatnew
				if ($newpass = $repeatnewpass) {
				    $newpass = password_hash($newpass, PASSWORD_BCRYPT);
					$sql2 = "UPDATE users SET password = '$newpass' WHERE email = '$user_mail'";
					if ($connection->query($sql2) === TRUE) {
						$response_info = 'Настройки сохранены!';
					}
					else{
						$error_info .= '/ ошибка в sql! /';
						$error_count +=1;
					}
				}
				else{
					$error_info .= '/ Пароли не одинаковы! /';
					$error_count +=1;
				}
			}
			else{
				$error_info .= '/ Неправильный старый пароль /';
				$error_count +=1;
			}
		mysqli_close($connection);
		}
		else{
			$error_info .= '/ Как?! /';
			$error_count +=1;
		}
	}
	    //mail
	elseif(isset($_POST['changeEmail']) && trim($_POST['changeEmail']) != ''){
	       include 'connection.php';
			global $connection;
			$new_email = trim($_POST['changeEmail']);
			$old_email = $_SESSION["loginkey"];
			$sql = "SELECT email FROM users WHERE email='$new_email'";
			$resultcomand = mysqli_query($connection, $sql);
		    $mailExist = mysqli_fetch_row($resultcomand);
		    if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
               
            }else{
                $error_info .= '/ Почта указана не правильно! /';
			    $error_count+=1;
            }
		    if(strlen(trim($new_email)) < 3 or strlen(trim($new_email)) > 40)
		    {
			    $error_info .= '/ Почта должна быть больше 3 символов и меньше 40! /';
			    $error_count+=1;
		    }
		    if ($mailExist[0] == $new_email)
   			 {
   			    
   			 	$emailexist = 'exist';
				$error_info .= '/ Такая почта уже существует! /';
				$error_count+=1;
			
   			 }
   			if($emailexist != 'exist' && $error_count == 0){
   			    $sql = "UPDATE users SET email = '$new_email' WHERE email='$old_email'";
   			    if ($connection->query($sql) === TRUE) {
						$response_info = 'Настройки сохранены!';
				}
				else{
						$error_info .= '/ ошибка в sql! /';
						$error_count +=1;
				}
   			    $_SESSION["loginkey"] = $new_email;
   			    $response_info = 'Вы сменили почту';
   			}
   		
			mysqli_close($connection);
	}
	    //name
	elseif(isset($_POST['name']) && trim($_POST['name']) != ''){
	     include 'connection.php';
	     $name = $_POST['name'];
		 global $connection;
		 $sql = "UPDATE users SET name = '$name' WHERE email='$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
		}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
		 mysqli_close($connection);
	}
	 //user position
	elseif(isset($_POST['position']) && trim($_POST['position']) != ''){
	     include 'connection.php';
	     $position = $_POST['position'];
		 global $connection;
		 $sql = "UPDATE users SET position = '$position' WHERE email='$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
		}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
		 mysqli_close($connection);
	}
	//phone
	elseif(isset($_POST['phone']) && trim($_POST['phone']) != ''){
	     include 'connection.php';
	     $phone = $_POST['phone'];
		 global $connection;
		 $sql = "UPDATE users SET phone = '$phone' WHERE email='$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
		}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
		 mysqli_close($connection);
	}
	    //signa
	elseif(isset($_POST['signa']) && trim($_POST['signa']) != ''){
	     include 'connection.php';
	     $signa = $_POST['signa'];
		 global $connection;
		 $sql = "UPDATE users SET signa = '$signa' WHERE email='$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
		}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
		 mysqli_close($connection);
	}
	    //checkbox
	elseif(isset($_POST['userOperatorRules'])){
	     include 'connection.php';
	     global $connection;
	     $userOperatorRules = $_POST['userOperatorRules'];
	     if($userOperatorRules == 'checked'){
	         $userOperatorRules = 'unchecked';
	     }
	     else{
	         $userOperatorRules = 'checked';
	     }
	
		 $sql = "UPDATE users SET operatorRules = '$userOperatorRules' WHERE email='$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
		}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
		 mysqli_close($connection);
	}
	   //user img
	elseif($_FILES['fileimg'] != ''){
	     include 'connection.php';
	     global $connection;
	     $img = $_FILES['fileimg'];
	     $filename = $_FILES['fileimg']['name'];
	     $allowed_filetypes = array('.jpg','.gif','.bmp','.png','.ico');
		 $max_filesize = 11524288;
		 $upload_path = $_SERVER['DOCUMENT_ROOT'].'/user_photos/';
		 $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
     	if(!in_array($ext,$allowed_filetypes)) // Сверяем полученное расширение со списком допутимых расширений. 
     	die('Данный тип файла не поддерживается.'.$filename);
     	if(filesize($_FILES['fileimg']['tmp_name']) > $max_filesize) // Проверим размер загруженного файла.
     	die('Фаил слишком большой.');
     	if(!is_writable($upload_path)) // Проверяем, доступна ли на запись папка.
		die('Невозможно загрузить фаил в папку. Установите права доступа - 777.');
		// Загружаем фаил в указанную папку.
		$file_with_path = '/user_photos/'.$filename;
		if(move_uploaded_file($_FILES['fileimg']['tmp_name'],$upload_path . $filename))
		{
			$sql = "UPDATE users SET photo = '$file_with_path' WHERE email = '$user_mail'";
		} else {
			echo 'При загрузке возникли ошибки. Попробуйте ещё раз.';
		}
		if ($connection->query($sql) === TRUE) {
		    $response_info = 'Настройки сохранены!';
		}
		else{
		    $error_info .= '/ ошибка в sql! /';
			$error_count +=1;
		}
		 mysqli_close($connection);
	}
	    //domain
	elseif(isset($_POST['domain']) && trim($_POST['domain'] != '')){
	      include 'connection.php';
	      global $connection;   
	      $user_domain_adress = strval($_POST['domain']);
	      $sql = "SELECT domain FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $user_old_domain = mysqli_fetch_row($resultcomand);
	      if(filter_var(gethostbyname($user_domain_adress), FILTER_VALIDATE_IP))
            {
            $sql = "UPDATE assistents SET domain = '$user_domain_adress' WHERE domain ='$user_old_domain[0]'";
            if ($connection->query($sql) === TRUE) {
				$response_info = 'Данные ассистенов обновлены!';
	 	      }
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		      }
            
            $sql = "UPDATE users SET domain = '$user_domain_adress' WHERE email='$user_mail'";
		      if ($connection->query($sql) === TRUE) {
				$response_info .= ' Настройки сохранены!';
	 	      }
		      else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		      }
            }
            else{
                $error_info .= '/ домен не действителен! /';
				$error_count +=1;
            }
	      
	      mysqli_close($connection);
	}
	//departaments
	elseif(isset($_POST['departament_add']) && trim($_POST['departament_add']) != ''){
	    include 'connection.php';
	    global $connection;
	    // get option
	    $new_departament = $_POST['departament_add'];
	    //get his settings
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	    $resultcomand = mysqli_query($connection, $sql);
		$user_settings = mysqli_fetch_row($resultcomand);
		//if no settings
		if($user_settings[0] == ''){
		    $json_array = array(
		        'departaments' => array('departament/1' => $new_departament)    
		    );
		    
		    $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		    
		}
		// if settings exist
		else{
		    
		    $json_array = json_decode($user_settings[0], true);
		    // if json departament no
		    if ($json_array['departaments'] == ''){
		         $json_array += array('departaments' => array('departament/1' => $new_departament));
		         $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		        
		    }
		    // if departament exist
		    else{
		        end($json_array['departaments']);         
                $key = key($json_array['departaments']);
		        $departament_info = explode("/", $key);
		        $lastDepartamentNumer = intval($departament_info[1]) + 1;
		        $json_array['departaments'] += ['departament/'.strval($lastDepartamentNumer).'' => $new_departament];
		         $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		        
		    }
		}
		$sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	    
	    if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	 	}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
	    mysqli_close($connection);
	}
	    //remove departaments
	elseif(isset($_POST['departament_remove']) && trim($_POST['departament_remove'])){
	    include 'connection.php';
	    global $connection;
	    $remove_departament = $_POST['departament_remove'];
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	    $resultcomand = mysqli_query($connection, $sql);
		$user_settings = mysqli_fetch_row($resultcomand);
		$json_array = json_decode($user_settings[0], JSON_UNESCAPED_UNICODE);
		$key = array_search($remove_departament, $json_array['departaments']);
		unset($json_array['departaments'][$key]);
		$json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		$sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	    
	    if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	 	}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
	    mysqli_close($connection);
	}
	    //departaments checkboxes
	elseif(isset($_POST['departament_check_1']) || isset($_POST['departament_check_2']) || isset($_POST['departament_check_3'])){
	    include 'connection.php';
	    global $connection;
	    if(trim($_POST['departament_check_1'] != '')){
	       $checkbox = $_POST['departament_check_1'];
	       $checkboxkey = 'departament_check/1';
	    }
	    elseif(trim($_POST['departament_check_2'] != '')){
	        $checkbox = $_POST['departament_check_2'];
	        $checkboxkey = 'departament_check/2';
	    }
	    else{
	        $checkbox = $_POST['departament_check_3'];
	        $checkboxkey = 'departament_check/3';
	    }
	    if($checkbox == 'checked'){
	        $checkbox = 'unchecked';
	    }
	    else{
	         $checkbox = 'checked';
	    }
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	    $resultcomand = mysqli_query($connection, $sql);
		$user_settings = mysqli_fetch_row($resultcomand);
		$json_array = json_decode($user_settings[0], JSON_UNESCAPED_UNICODE);
		$json_array['departament_checkboxes'][$checkboxkey] = $checkbox; 
		$json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		$sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	    if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	 	}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
	
	    mysqli_close($connection);
	}
	    // add new assistent
	elseif(
	    
	    isset($_POST['assistent_name']) && trim($_POST['assistent_name']) != ''
	    && isset($_POST['assistent_departament']) && trim($_POST['assistent_departament']) != ''
	    && isset($_POST['assistent_email']) && trim($_POST['assistent_email']) != ''
	    && isset($_POST['assistent_password']) && trim($_POST['assistent_password']) != ''
	    && isset($_POST['assistent_passwordSecondTime']) && trim($_POST['assistent_passwordSecondTime']) != ''
	   
    ){
        include 'connection.php';
	    global $connection;
	    $assistent_name = $_POST['assistent_name'];
	    $assistent_departament = $_POST['assistent_departament'];
	    $assistent_email = $_POST['assistent_email'];
	    $assistent_password = $_POST['assistent_password'];
	    $assistent_passwordSecondTime = $_POST['assistent_passwordSecondTime'];
	    $sql = "SELECT email FROM assistents WHERE email = '$assistent_email'";
	    $resultcomand = mysqli_query($connection, $sql);
		$user_email_exist = mysqli_fetch_row($resultcomand);
		$sql = "SELECT domain FROM users WHERE email = '$user_mail'";
	    $resultcomand = mysqli_query($connection, $sql);
		$user_domain = mysqli_fetch_row($resultcomand);
		if($user_email_exist[0] == $assistent_email){
		    $exist = 'exist';
		    $error_info .= '/ Такая почта уже существует! /';
			$error_count+=1;
		}
		else{
		    $exist = 'notexist';
		}
	    if(isset($_POST['assistent_buttleCry']) && trim($_POST['assistent_buttleCry']) != ''){
	        $assistent_buttleCry = $_POST['assistent_buttleCry'];
	    }
	    else{
	        $assistent_buttleCry = $assistent_name.' , '.$assistent_departament;
	    }
	    if(isset($_POST['assistent_phone']) && trim($_POST['assistent_phone']) != ''){
	        $new_assistent_phone = $_POST['assistent_phone'];
	    }
	    else{
	        $new_assistent_phone = 'телефон не указан';
	    }
	   
	    
	     $new_assistent_img = '/user_photos/user.png';
	    
	    if(strlen(trim($assistent_password)) < 7 or strlen(trim($assistent_password)) > 30)
			{
			$error_info .= '/ Пароль должен быть больше 7 символов и меньше 30! /';
			$error_count+=1;
			}
		if (filter_var($assistent_email, FILTER_VALIDATE_EMAIL)) {
               
            }else{
                $error_info .= '/ Почта указана не правильно! /';
			    $error_count+=1;
            }
		if(strlen(trim($assistent_email)) < 3 or strlen(trim($assistent_email)) > 40)
		    {
			    $error_info .= '/ Почта должна быть больше 3 символов и меньше 40! /';
			    $error_count+=1;
		    }
	    if($user_domain[0] == ''){
	        $error_info .= '/ Сначало нужно заполнить домен в разделе - "Получить код"! /';
			$error_count+=1;
	    }
	    if($assistent_password != $assistent_passwordSecondTime){
	        $error_info .= '/ Пароли не совпадают! /';
		    $error_count +=1;
	    }
	    if($assistent_password == $assistent_passwordSecondTime && $error_count == 0 && $exist != 'exist'){
	    
		$assistent_domain = $user_domain[0];
		$assistent_password = password_hash($assistent_password, PASSWORD_BCRYPT);
	    $sql = "INSERT INTO assistents(id,name, password, email, ip, visits, lastvisit, domain,photo,phone,buttlecry,departament) VALUES (0,'".$assistent_name."','".$assistent_password."','".$assistent_email."','new',0,'new','".$assistent_domain."', '".$new_assistent_img."','".$new_assistent_phone."','".$assistent_buttleCry."','".$assistent_departament."')";
	    if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	 	}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
	        
	    }
	    else{
	        //useless
	    }
	}
	//delete assistent
	elseif(isset($_POST['remove_assistent'])){
	    include 'connection.php';
	    global $connection;
	    $remove_email = $_POST['remove_assistent'];
        $sql = "DELETE FROM assistents WHERE email = '$remove_email'";
        if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	 	}
		else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		}
        mysqli_close($connection);
	}
	    //assistent_changesettings
	elseif(isset($_POST['assistent_email_global']) && trim($_POST['assistent_email_global']) !='' && ((isset($_POST['changesValue']) && trim($_POST['changesValue']) != '') || ($_FILES['changesValue']))){
	    $option = $_POST['changesName'];
	    $assist_email = $_POST['assistent_email_global'];
	    if($option == 'assistent_changename'){
	         include 'connection.php';
	         global $connection;
	         $change_info = $_POST['changesValue'];
	         $sql = "UPDATE assistents SET name = '$change_info' WHERE email = '$assist_email'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	         mysqli_close($connection);
	    }
	    elseif($option == 'assistent_changedepartament'){
	        include 'connection.php';
	         global $connection;
	         $change_info = $_POST['changesValue'];
	         $sql = "UPDATE assistents SET departament = '$change_info' WHERE email = '$assist_email'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	         mysqli_close($connection);
	    }
	    elseif($option == 'assistent_changephone'){
	        include 'connection.php';
	         global $connection;
	         $change_info = $_POST['changesValue'];
	         if (preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', $change_info)){
	             
	         
	         $sql = "UPDATE assistents SET phone = '$change_info' WHERE email = '$assist_email'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	        }
	        else{
	            $error_info .= '/ Не правльный формат телефона! /';
				$error_count +=1;    
	        }
	         mysqli_close($connection);
	    }
	    elseif($option == 'assistent_changeemail'){
	        include 'connection.php';
	        global $connection;
	        $change_info = $_POST['changesValue'];
	        $sql = "SELECT email FROM assistents WHERE email = '$change_info'";
	        $resultcomand = mysqli_query($connection, $sql);
		    $user_email_exist = mysqli_fetch_row($resultcomand);
		    $exist = 'unexist';
		    if($user_email_exist[0] == $change_info){
		        $exist = 'exist';
		        $error_info .= '/ Такая почта уже существует! /';
			    $error_count+=1;
		    }
		     if (filter_var($change_info, FILTER_VALIDATE_EMAIL)) {
		     
		    if($error_count == 0 && $exist != 'exist'){
		        $sql = "UPDATE assistents SET email = '$change_info' WHERE email = '$assist_email'";
		        if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	    }
		        else{
			    	$error_info .= '/ ошибка в sql! /';
			    	$error_count +=1;
		        }
		    }
		     }
		    else{
		        $error_info .= '/ Не правильный формат почты! /';
			    $error_count+=1;
		    }
	        mysqli_close($connection);
	    }
	    elseif($option == 'assistent_changebuttlecry'){
	        include 'connection.php';
	        global $connection;
	        global $connection;
	         $change_info = $_POST['changesValue'];
	         $sql = "UPDATE assistents SET buttlecry = '$change_info' WHERE email = '$assist_email'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	        mysqli_close($connection);
	    }
	    
	    else{
	            
	        	$error_info .= '/ Поля не заполнены! /';
		        $error_count +=1;
	    }
	} 
	//change assistent img
	elseif($_FILES['assistent_changephoto']){
	        include 'connection.php';
	        $glob_mail = $_POST['assistent_email_gloabal_for_img'];
	         global $connection;
	         $img = $_FILES['assistent_changephoto'];
	         $filename = $_FILES['assistent_changephoto']['name'];
	         $allowed_filetypes = array('.jpg','.gif','.bmp','.png','.ico');
		     $max_filesize = 11524288;
		     $upload_path = $_SERVER['DOCUMENT_ROOT'].'/user_photos/';
		     $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
     	    if(!in_array($ext,$allowed_filetypes)) // Сверяем полученное расширение со списком допутимых расширений. 
     	    die('Данный тип файла не поддерживается.'.$filename);
     	    if(filesize($_FILES['assistent_changephoto']['tmp_name']) > $max_filesize) // Проверим размер загруженного файла.
     	    die('Фаил слишком большой.');
     	    if(!is_writable($upload_path)) // Проверяем, доступна ли на запись папка.
		    die('Невозможно загрузить фаил в папку. Установите права доступа - 777.');
		    // Загружаем фаил в указанную папку.
		    $file_with_path = '/user_photos/'.$filename;
		    if(move_uploaded_file($_FILES['assistent_changephoto']['tmp_name'],$upload_path . $filename))
		    {
			$sql = "UPDATE assistents SET photo = '$file_with_path' WHERE email = '$glob_mail'";
		    } else {
		    	echo 'При загрузке возникли ошибки. Попробуйте ещё раз.';
		    }
		    if ($connection->query($sql) === TRUE) {
		        $response_info = 'Настройки сохранены!';
		    }
		    else{
		        $error_info .= '/ ошибка в sql! /';
		    	$error_count +=1;
		    }
		     mysqli_close($connection);
	        
	}
	//interhelper options
	elseif(isset($_POST['InterHelperButtonColor']) || isset($_POST['InterHelperButtonTextColor']) || isset($_POST['InterHelperWindowColor']) || isset($_POST['InterHelperWindowTextColor'])){
	     include 'connection.php';
	     global $connection;
	     $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	     $resultcomand = mysqli_query($connection, $sql);
		 $json = mysqli_fetch_row($resultcomand);
		 $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		
		 $InterHelperOptions = $json_array['InterHelperOptions'];
	     if(isset($_POST['InterHelperButtonColor'])){
	         $InterHelperButtonColor = $json_array['InterHelperOptions']['bgcolor'];
	         $newbgcolor = $_POST['InterHelperButtonColor'];
	         $json_array['InterHelperOptions']['bgcolor'] = $newbgcolor;
	         $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	         $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     }
	     elseif(isset($_POST['InterHelperButtonTextColor'])){
	         $InterHelperButtonColor = $json_array['InterHelperOptions']['textcolor'];
	         $newbgcolor = $_POST['InterHelperButtonTextColor'];
	         $json_array['InterHelperOptions']['textcolor'] = $newbgcolor;
	         $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	         $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     }
	     elseif(isset($_POST['InterHelperWindowColor'])){
	         $InterHelperButtonColor = $json_array['InterHelperOptions']['windowbgcolor'];
	         $newbgcolor = $_POST['InterHelperWindowColor'];
	         $json_array['InterHelperOptions']['windowbgcolor'] = $newbgcolor;
	         $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	         $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     }
	     elseif(isset($_POST['InterHelperWindowTextColor'])){
	         $InterHelperButtonColor = $json_array['InterHelperOptions']['windowtextcolor'];
	         $newbgcolor = $_POST['InterHelperWindowTextColor'];
	         $json_array['InterHelperOptions']['windowtextcolor'] = $newbgcolor;
	         $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	         $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
	         if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     	}
		    else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     }
	     else{
	   
		$error_info .= '/ Поля не заполнены! /';
		$error_count +=1;
	    }
	     mysqli_close($connection);
	}
	    //button position
	elseif(isset($_POST['InterHelper_button_position'])){
	    include 'connection.php';
	     global $connection;
	     $change_position_for = $_POST['InterHelper_button_position'];
	     $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	     $resultcomand = mysqli_query($connection, $sql);
		 $json = mysqli_fetch_row($resultcomand);
		 $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 
		 if($change_position_for == 'first_position'){
		     $json_array['InterHelperOptions']['position_left'] = 'left:0%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:0%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'second_position'){
		     $json_array['InterHelperOptions']['position_left'] = 'left:50%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:0%;';
		     $json_array['InterHelperOptions']['transform_translate'] = 'transform:translateX(-50%);';
		 }
		 elseif($change_position_for == 'third_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:100%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:0%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'fourth_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:0%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:100%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'fith_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:50%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:100%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'sixth_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:100%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:100%;';
		     $json_array['InterHelperOptions']['transform_translate'] = 'transform:translateX(-50%);';
		 }
		 elseif($change_position_for == 'seventh_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:0%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:85%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'eighth_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:0%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:15%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'nineth_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:100%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:85%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 elseif($change_position_for == 'tenth_position'){
		      $json_array['InterHelperOptions']['position_left'] = 'left:100%;';
		     $json_array['InterHelperOptions']['position_top'] = 'top:15%;';
		     $json_array['InterHelperOptions']['transform_translate'] = '';
		 }
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     }
		 else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		 }
	    mysqli_close($connection);
	}
	// last message from assistent
	elseif(isset($_POST['end_message']) && trim($_POST['end_message']) != ''){
	    include 'connection.php';
	     global $connection;
	      $end_message = $_POST['end_message'];
	     $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	     $resultcomand = mysqli_query($connection, $sql);
		 $json = mysqli_fetch_row($resultcomand);
		 $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['SYSmessages']['endmessage'] = $end_message;
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE); 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     }
		 else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		 }
	       mysqli_close($connection);
	}
	    //feedback seconds send
	elseif(isset($_POST['feedback_sec']) && trim($_POST['feedback_sec']) != ''){
	    include 'connection.php';
	     global $connection;
	      $end_message = $_POST['feedback_sec'];
	     $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	     $resultcomand = mysqli_query($connection, $sql);
		 $json = mysqli_fetch_row($resultcomand);
		 $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['SYSmessages']['FEEDBACKafktimeout'] = $end_message;
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE); 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		 if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	     }
		 else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		 }
	       mysqli_close($connection);
	}
	//AFK time message block add
	elseif($_POST['add_new_message']){
	    include 'connection.php';
	      global $connection;
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  
		  end($json_array['SYSmessages']['AFKmessages']);         
          $key = key($json_array['SYSmessages']['AFKmessages']);
          
		  $message_info = explode("/", $key);
		  $last_message_number = intval($message_info[1]) + 1;
		  $json_array['SYSmessages']['AFKmessages'] += ['AFKmessage/'.strval($last_message_number).'' => array("AFKtimeout"=>"60","AFKmessage"=>"Подождите ответа консультанта.")];
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		   $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	         }
		     else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		     }
	       
	     mysqli_close($connection);
	}
	//AFK time message block remove
	elseif($_POST['time_message_remove']){
	    include 'connection.php';
	      global $connection;
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
	    $time_message_remove_name = $_POST['time_message_remove'];

		unset($json_array['SYSmessages']['AFKmessages'][$time_message_remove_name]);
		$json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		$sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
		
	    mysqli_close($connection);
	}
	//AFK message For changevalue
	elseif(isset($_POST['messageForchangevalue']) && trim($_POST['messageForchangevalue']) !='' && isset($_POST['messageForchangename']) && trim($_POST['messageForchangename'])){
	    include 'connection.php';
	      global $connection;
	      $postname = $_POST['messageForchangename'];
	      $postdata = $_POST['messageForchange'];
	      $postvalue = $_POST['messageForchangevalue'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		if($postname == 'timeMessageTimer')
		{
		    $json_array['SYSmessages']['AFKmessages'][$postdata]['AFKtimeout'] = $postvalue;
		    $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		}
		elseif($postname == 'timeMessageText'){
		    $json_array['SYSmessages']['AFKmessages'][$postdata]['AFKmessage'] = $postvalue;
		    $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		}
		$sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	    mysqli_close($connection);
	} 
	//feedback ABLE
	elseif(isset($_POST['feedback_form_checkbox'])){
	    include 'connection.php';
	      global $connection;
	      $check_info = $_POST['feedback_form_checkbox'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if($json_array['feedbackform']['feedbackENABLED'] == 'checked'){
		      $json_array['feedbackform']['feedbackENABLED'] = 'unchecked';
		  }
		  else{
		  $json_array['feedbackform']['feedbackENABLED'] = 'checked';
		  }
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	   mysqli_close($connection);
	}
	//feedback_text
	elseif(isset($_POST['feedback_text']) && trim($_POST['feedback_text']) != ''){
	    include 'connection.php';
	      global $connection;
	     $new_text = $_POST['feedback_text'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  $json_array['feedbackform']['feedbackTEXT'] = $new_text;
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     mysqli_close($connection);
	}
	//mail for feedback
	elseif(isset($_POST['feedback_target_email']) && trim($_POST['feedback_target_email']) != ''){
	    include 'connection.php';
	      global $connection;
	     $new_email = $_POST['feedback_target_email'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  $json_array['feedbackform']['feedbackMAIL'] = $new_email;
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
		  }
		  else{
		      $error_info .= '/ Не правильный формат почты! /';
				$error_count +=1;
		  }
	     mysqli_close($connection);
	}
	//name must filled
	elseif(isset($_POST['feedback_input_checkbox_1'])){
	    include 'connection.php';
	      global $connection;
	      $check_info = $_POST['feedback_input_checkbox_1'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if($json_array['feedbackform']['feedbackformName'] == 'checked'){
		      $json_array['feedbackform']['feedbackformName'] = 'unchecked';
		  }
		  else{
		  $json_array['feedbackform']['feedbackformName'] = 'checked';
		  }
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	   mysqli_close($connection);
	}
	//phone must filled
	elseif(isset($_POST['feedback_input_checkbox_2'])){
	    include 'connection.php';
	      global $connection;
	      $check_info = $_POST['feedback_input_checkbox_2'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if($json_array['feedbackform']['feedbackformPhone'] == 'checked'){
		      $json_array['feedbackform']['feedbackformPhone'] = 'unchecked';
		  }
		  else{
		  $json_array['feedbackform']['feedbackformPhone'] = 'checked';
		  }
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	   mysqli_close($connection);
	}
	//emailmust filled
	elseif(isset($_POST['feedback_input_checkbox_3'])){
	    include 'connection.php';
	      global $connection;
	      $check_info = $_POST['feedback_input_checkbox_3'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if($json_array['feedbackform']['feedbackformEmail'] == 'checked'){
		      $json_array['feedbackform']['feedbackformEmail'] = 'unchecked';
		  }
		  else{
		  $json_array['feedbackform']['feedbackformEmail'] = 'checked';
		  }
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	   mysqli_close($connection);
	}
	//send invites status
	elseif(isset($_POST['InvitesEvrywhereStatus'])){
	    include 'connection.php';
	      global $connection;
	       $check_info = $_POST['InvitesEvrywhereStatus'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if($json_array['InterHelperInvitesOptions']['InvitesForAllsystem'] == 'checked'){
		      $json_array['InterHelperInvitesOptions']['InvitesForAllsystem'] = 'unchecked';
		  }
		  else{
		  $json_array['InterHelperInvitesOptions']['InvitesForAllsystem'] = 'checked';
		  }
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     mysqli_close($connection);
	}
	//send invites for current pages status
	elseif(isset($_POST['InvitesCurrentStatus'])){
	    include 'connection.php';
	      global $connection;
	       $check_info = $_POST['InvitesCurrentStatus'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if($json_array['InterHelperInvitesOptions']['AlwaysShowInvite'] == 'checked'){
		      $json_array['InterHelperInvitesOptions']['AlwaysShowInvite'] = 'unchecked';
		  }
		  else{
		  $json_array['InterHelperInvitesOptions']['AlwaysShowInvite'] = 'checked';
		  }
		  $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		  $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    }
	     mysqli_close($connection);
	}
	//invite system name
	elseif(isset($_POST['Invite_sys_name']) && trim($_POST['Invite_sys_name']) != ''){
	    include 'connection.php';
	      global $connection;
	    $inviteSysName = $_POST['Invite_sys_name'];
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['InterHelperInvitesOptions']['SYSname'] = $inviteSysName; 
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    } 
	    mysqli_close($connection);
	}
	//invite system name
	elseif(isset($_POST['InviteText']) && trim($_POST['InviteText']) != ''){
	    include 'connection.php';
	      global $connection;
	    $inviteText = $_POST['InviteText'];
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['InterHelperInvitesOptions']['InviteText'] = $inviteText; 
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    } 
	    mysqli_close($connection);
	}
	// show after pages count
	elseif(isset($_POST['inviteShowAfterPages']) && trim($_POST['inviteShowAfterPages']) != ''){
	    include 'connection.php';
	      global $connection;
	    $inviteShowAfterPages = $_POST['inviteShowAfterPages'];
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['InterHelperInvitesOptions']['ShowAfterPagesCount'] = $inviteShowAfterPages; 
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    } 
	    mysqli_close($connection);
	}
	// show after seconds count
	elseif(isset($_POST['inviteShowAfter']) && trim($_POST['inviteShowAfter']) != ''){
	    include 'connection.php';
	      global $connection;
	    $inviteShowAfter = $_POST['inviteShowAfter'];
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['InterHelperInvitesOptions']['ShowAfterSecondsCount'] = $inviteShowAfter; 
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    } 
	    mysqli_close($connection);
	}
		// hide after seconds count
	elseif(isset($_POST['inviteHideAfter']) && trim($_POST['inviteHideAfter']) != ''){
	    include 'connection.php';
	      global $connection;
	    $inviteHideAfter = $_POST['inviteHideAfter'];
	    $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		 $json_array['InterHelperInvitesOptions']['HideAfterPagesCount'] = $inviteHideAfter; 
		 $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 
		 $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		   if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
	        }
		   else{
				$error_info .= '/ ошибка в sql! /';
				$error_count +=1;
		    } 
	    mysqli_close($connection);
	}
	//new costume invite
	elseif(
	    isset($_POST['url_page']) && trim($_POST['url_page']) != ''
	    && isset($_POST['page_invite_text']) && trim($_POST['page_invite_text']) != ''
	    && isset($_POST['Current_invite_show_after']) && trim($_POST['Current_invite_show_after']) != ''
	    && isset($_POST['Current_invite_hide_after']) && trim($_POST['Current_invite_hide_after']) != ''
	    ){
	       include 'connection.php';
	      global $connection; 
	      $url = $_POST['url_page'];
	      $text = $_POST['page_invite_text'];
	      $ShowSec = $_POST['Current_invite_show_after'];
	      $HideSec = $_POST['Current_invite_hide_after'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
		  if(array_key_exists($url, $json_array['InterHelperInvitesOptions']['InvitesForPages'])){
		      $error_info .= '/ Сообщение на этот url уже создано! /';
		      $error_count +=1;
		  }
		  if(filter_var($url, FILTER_VALIDATE_URL) && $error_count == 0){
		      $json_array['InterHelperInvitesOptions']['InvitesForPages'] += [''.$url.'' => array("InviteText"=>"".$text."","ShowAfterSeconds"=>"".$ShowSec."","HideAfterSeconds" => "".$HideSec."")];
		      $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
		 
		      $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		       if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
				
	            }
		       else{
			    	$error_info .= '/ ошибка в sql! /';
		    		$error_count +=1;
		        }
		  }
		  else{
		       $error_info .= '/ формат url не правильный! /';
		       $error_count +=1;
		  }
		  
		  
	       mysqli_close($connection);
	    
	}
	elseif(
	    isset($_POST['inviteName'])
	    && isset($_POST['inviteValue']) && trim($_POST['inviteValue']) != ''
	    && isset($_POST['InviteDataUrl'])
	    ){
	      include 'connection.php';
	      global $connection;
	        $inviteName = $_POST['inviteName'];
	        $inviteValue = $_POST['inviteValue'];
	        $InviteDataUrl = $_POST['InviteDataUrl'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
	      if($_POST['inviteName'] == 'url_page'){
	          if(array_key_exists($inviteValue, $json_array['InterHelperInvitesOptions']['InvitesForPages'])){
	               $error_info .= '/ Сообщение на этот url уже создано! /';
		           $error_count +=1;
	          }
	          else{
	           $text = $json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]['InviteText'];
	           $ShowSec = $json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]['ShowAfterSeconds'];
	           $HideSec = $json_array['InterHelperInvitesOptions']['InvitesForPages'][$inviteValue]['HideAfterSeconds'];
	           unset($json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]);
	           $json_array['InterHelperInvitesOptions']['InvitesForPages'] += [''.$inviteValue.'' => array("InviteText"=>"".$text."","ShowAfterSeconds"=>"".$ShowSec."","HideAfterSeconds" => "".$HideSec."")];
	           $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	           
	          }
	      }
	      elseif($_POST['inviteName'] == 'page_invite_text'){
	          $json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]['InviteText'] =  $inviteValue;      
	          $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	      }
	      elseif($_POST['inviteName'] == 'Current_invite_show_after2'){
	          $json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]['ShowAfterSeconds'] =  $inviteValue;      
	          $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	      }
	      elseif($_POST['inviteName'] == 'Current_invite_hide_after2'){
	          $json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]['HideAfterSeconds'] =  $inviteValue;      
	          $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	      }
	      else{
	          $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	      }
	       if($error_count == 0){
	            $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		      if ($connection->query($sql) === TRUE) {
		        $response_info = 'Настройки сохранены!';
	           }
		      else{
		    	$error_info .= '/ ошибка в sql! /';
		    	$error_count +=1;
		      }
	       }
	       mysqli_close($connection);
	    
	}
	elseif(isset($_POST['invite_remove'])){
	    include 'connection.php';
	      global $connection;
	      $InviteDataUrl = $_POST['invite_remove'];
	      $sql = "SELECT settings FROM users WHERE email = '$user_mail'";
	      $resultcomand = mysqli_query($connection, $sql);
		  $json = mysqli_fetch_row($resultcomand);
		  $json_array = json_decode($json[0], JSON_UNESCAPED_UNICODE);
          unset($json_array['InterHelperInvitesOptions']['InvitesForPages'][$InviteDataUrl]);
          $json = json_encode($json_array, JSON_UNESCAPED_UNICODE);
	            $sql = "UPDATE users SET settings = '$json' WHERE email = '$user_mail'";
		       if ($connection->query($sql) === TRUE) {
				$response_info = 'Настройки сохранены!';
				
	            }
		       else{
			    	$error_info .= '/ ошибка в sql! /';
		    		$error_count +=1;
		        }	    
		 mysqli_close($connection);
	}
	else{
	  
		$error_info .= '/ Поля не заполнены! /';
		$error_count +=1;
	}
	if ($error_count >= 1){
		echo 'Ошибки: '.$error_info.' Количество ошибок: '.$error_count;
	}
	else{
		echo $response_info;
	}
	
    
?>