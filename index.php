<?php
	session_start();

	
	include './php/connection.php'; 
	include './php/func.php';
	if (isset($_SESSION["loginkey"])) {
	    include 'connection.php';
		global $connection;
		$user_mail = $_SESSION["loginkey"];
		$sql = "SELECT photo FROM users WHERE email = '$user_mail'";
		$resultcomand = mysqli_query($connection, $sql);
		$photo = mysqli_fetch_row($resultcomand);	
		
	}
	else{
		// else
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	ip($ip);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>InterHelper</title>
	<meta name="viewport" content="width=device-width, initial-scale=0.65">
	<link rel="stylesheet" type="text/css" href="scss/reset.css">

	<link rel="stylesheet" type="text/css" href="scss/main.css">
	<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
	<link rel="stylesheet" type="text/css" href="scss/media.css">
	
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<script type="text/javascript">

</script>
<!-- Login option -->
<?php
	content($photo[0]);
?>
<!-- Login option end-->
<!-- InterHelper -->
<link rel="stylesheet" type="text/css" href="HelperCode/helper.css">
<script type="text/javascript" src="HelperCode/Helper.js"></script>
<!-- InterHelper end-->
<!--header -->
<header>
	<div id="logo"><h1>InterHelper</h1></div>
	<div id="navmenu">
		<a class="ToSection2" href="#home_page">Главная</a>
		<a class="ToSection2" href="#instructions">О нас</a>
		<a class="loginingmenu" id="sign_in" href="#">начать</a>
		<a class="loginingmenu" id="sign_up" href="#">Войти</a>
	</div>
</header>	
<!-- header end-->
  <!-- burger -->
  <input type="checkbox" id="menu-toggle"/>
  <label id="trigger" for="menu-toggle"></label>
  <label id="burger" for="menu-toggle"></label>
  <ul id="menu">
    <li><a class="ToSection" href="#home_page">Главная</a></li>
    <li><a class="ToSection" href="#instructions">О InterHelper</a></li>
    <li class="removeable"><a class="ToSection loginingmenu" href="#home_page">Зарегистрироваться</a></li>
    <li class="removeable"><a class="ToSection loginingmenu" href="#home_page">Войти</a></li>
  </ul>	
  <!-- burger end-->
 <!--main section-->
<section id="home_page">
	<div id="Home_info">
		<h1>InterHelper</h1>
		<h2>WEB ASSISTENT</h2>
		<p id="home_text">Лучший интернет ассистент, для оживления вашего сайта,<br /> <br /> улучшения взаимодействия пользователя с сайтом и помощи ему.<br /><br /> Просто , но круто.</p>
		<div id="box_buttons">
			<div class="box loginingmenu2">
				<div class="box_img"></div>
				<div  class="box_text"><p>НАЧАТЬ</p></div>
			</div>
			<div class="box">
				<div class="box_img"></div>
				<div class="box_text"><p>ПОДРОБНЕЕ</p></div>
			</div>
			<a style="text-decoration: none; color: #fff;"  href="#reviews_block"  class="box ToSection2">
				<div class="box_img"></div>
				<div class="box_text"><p>ОТЗЫВЫ</p></div>
			</a>
		</div>
	</div>
	<div id="links">
		
		<h2>FOLLOW US</h2>
		<a href="#" class="follow_img"></a>
		<a href="#" class="follow_img"></a>
		<a href="https://interfire.ru/" class="follow_img"></a>
	</div>
</section>
		<!--main section end-->
		<!--about instruction-->
<div id="instructions">
	<div class="instruction"><h2>01</h2><p>Lorem ispum dolor sitame<br/> Donec sed odio duiraes<br/> VEL scelerisque nisl</p></div>
	<div class="instruction"><h2>02</h2><p>Lorem ispum dolor sitame <br/>Donec sed odio duiraes<br/> VEL scelerisque nisl</p></div>
	<div class="instruction"><h2>03</h2><p>Lorem ispum dolor sitame<br/> Donec sed odio duiraes <br/>VEL scelerisque nisl</p></div>
</div>
		<!--about instruction end-->
		<!--about section-->
<section id="about">
	<div id="about_helper_text">
		<p id="path">
			<span id="to_1">позиция</span> /<span id="to_2"> цвет</span> /<br />
			<span id="to_3"> online</span> /<span id="to_4"> offline</span>/<br />
			<span id="to_5"> сторона админа</span> /<span id="to_6"> сторона ассистента</span> /<br />
			<span id="to_7"> чёрный список</span> /<span id="to_8"> чат пользователя</span> /<br />
			<span id="to_9"> команды</span> /<span id="to_10"> чат ассистента</span> /<br />
			<span id="to_11"> настройки ассистента</span> /<span id="to_12"> настройки админа</span> / <br />
			<span id="to_13">подключение</span> /<span id="to_14">как начать</span> /</p>
			<h2>Do nothing - <br />and earn money.</h2>
	</div>
	<div id="slider_container">
		 <div class="swiper-container newcont1">
    <div class="swiper-wrapper newwrap1">
      <div id="slide_1" class="swiper-slide newslide1">photo position</div>
      <div id="slide_2" class="swiper-slide newslide1">photo color</div>
      <div id="slide_3" class="swiper-slide newslide1">photo online</div>
      <div id="slide_4" class="swiper-slide newslide1">photo offline</div>
      <div id="slide_5" class="swiper-slide newslide1">photo admin page</div>
      <div id="slide_6" class="swiper-slide newslide1">photo assistent page</div>
      <div id="slide_7" class="swiper-slide newslide1">photo black list</div>
      <div id="slide_8" class="swiper-slide newslide1">photo user chat</div>
      <div id="slide_9" class="swiper-slide newslide1">photo commands</div>
      <div id="slide_10" class="swiper-slide newslide1">photo assistent chat</div>
      <div id="slide_11" class="swiper-slide newslide1">photo assistent settings</div>
      <div id="slide_12" class="swiper-slide newslide1">photo admin settings</div>
      <div id="slide_13" class="swiper-slide newslide1">photo connection</div>
      <div id="slide_14" class="swiper-slide newslide1">photo how to start</div>
    </div>
  </div>
	</div>
</section>
		<!--about section end-->
		<!--Interfire example section-->
<section id='previw_section'>
	<h2 id='example' style="text-align: center;font-size: 2.5em;color: #fff;text-transform: uppercase; position: absolute; top: 100px;
	left: 50%;transform: translateX(-50%);">Пример</h2>
		<!--Interfire example-->
	<div id='interfire_example'>
		<div id='example_right_side'>
			<div id='example_top_photo_section'>
				<div id="IFWApromo"><h3>InterHelper</h3></div>
				<h2>
					<span>I</span>
					<span>N</span>
					<span>T</span>
					<span>E</span>
					<span>R</span>
					<span>F</span>
					<span>I</span>
					<span>R</span>
					<span>E</span>
				</h2>
				
				<p>студия веб разработки</p>
				<a href='https://interfire.ru/' id='to_original'>больше</a>
			</div>
			<div id='example_bottom_info'>
				<div style="display: flex;justify-content: center;align-items: center;flex-direction: column;margin-top: 50px;">
					<h2 class="example_section_name">Почему мы?</h2>
				<span class="example_section_span">whywe</span>
				</div>
				
			
			<div id='example_infos'>
				<div class='example_info'>
					<span class="example_info_img"></span>
					<h2 class="example_info_h2">Качество</h2>
					<p class="example_info_p">Качество проектов достигается засчёт профессионализма наших сотрудников, использующих последние IT технологии.</p>
				</div>
				<div class='example_info'>
					<span class="example_info_img"></span>
					<h2 class="example_info_h2">Опыт</h2>
					<p class="example_info_p">Благодаря нашему опыту, мы можем подсказать как сделать ваш проект ещё лучше или составить тех.задание за вас.</p>
				</div>
				<div class='example_info'>
					<span class="example_info_img"></span>
					<h2 class="example_info_h2">Команда</h2>
					<p class="example_info_p">На каждый проект мы распределяем людей по командам, чтобы выполнять ваши заказы быстро и качественно.</p>
				</div>
			</div>
			<div id='example_works'>
					<div style="display: flex;justify-content: center;align-items: center;flex-direction: column;margin-top: 50px;">
					<h2 class="example_section_name">Наши работы</h2>
					<span class="example_section_span">portfolio</span>
				    </div>
					<div id="example_works2">
						<a href="https://asbmsk.ru/" class="example_work"><div><span>#web</span><p>Проект автошколы</p></div></a>
						<a href="http://bauflex.interfire.ru/" class="example_work"><div><span>#web</span><p>Проект мебельного магазина</p></div></a>
						<a href="http://divagym.ru/" class="example_work"><div><span>#web</span><p>Проект спортивного клуба</p></div></a>
						<a href="https://play.google.com/store/apps/details?id=com.asbmsk.AsbMskPdd&hl=ru" class="example_work"><div><span>#mobile</span><p>Проект мобильного приложения АШ</p></div></a>
					</div>
					<a href="https://interfire.ru/portfolio" style="text-decoration: none;" class='example_more_button'>Больше портфолио</a>
			</div>	
				<div id='example_skills'>
					<div style="display: flex;justify-content: center;align-items: center;flex-direction: column;margin-top: 50px;">
					<h2 class="example_section_name">наши возможности</h2>
					<span class="example_section_span">skills</span>
				    </div>
					<div id='example_skills_img'></div>
					<h2 id='header3'>ЧТО МЫ МОЖЕМ,<br/> СПРОСИТЕ ВЫ, А МЫ ОТВЕТИМ!</h2>
					<p id='text3'>Студия <strong>INTERFIRE</strong> это сплочённая команда которая уже долгое время успешно занимается разработкой современных веб сайтов. Мы выполняем исключительно креативные, интерестные и сложные проекты. У нас богатый опыт работы как с Российскими, так и с зарубежными компаниями. Мы постоянно совершенствуем свои навыки, изучаем новые технологии и повышаем квалификацию. Каждый клиент для нас очень важен!</p>
					<p id='text4'>Вот то, чем мы владеем!</p>
					<div id="examples_programming_skills_block">
						<span class="examples_programming_skill"></span>
						<span class="examples_programming_skill"></span>
						<span class="examples_programming_skill"></span>
						<span class="examples_programming_skill"></span>
						<span class="examples_programming_skill"></span>
						<span class="examples_programming_skill"></span>
					</div>
				</div>
				<div id='example_feedback_section'>
					
					<div style="display: flex;justify-content: center;align-items: center;flex-direction: column;margin-top: 50px;"><h2 class="example_section_name">СВЯЗАТЬСЯ С НАМИ</h2>
					<span class="example_section_span">feedback</span>
					<div id="example_map_container"></div>
			    	</div>
					<form>
						<input type="text" name="name" placeholder="Name" />
						<input type="text" name="name" placeholder="E-MAIL" />
						<textarea placeholder="сообщение"></textarea>
						<button class='example_more_button'>оставить заявку</button>
					</form>
				</div>
				<div id='example_footer'>
					<h2>пример официального сайта interfire.ru</h2>
				</div>
			</div>
		</div>
	</div>
	<!--Interfire example end-->

</section>
		<!--Interfire example section end-->
		<!--reviews section-->
<section id="reviews_block">
	<div id="counter_block">
		<h2>Наш InterHelper уже установлен на</h2>
		<div id="counter">
			<div class="number-ticker" data-value="123456"></div>
		</div>
		<h2>сайтах по всему миру</h2>
	</div>
	<div id="slider_container2">
	<div class="swiper-container newcont2">
    <div class="swiper-wrapper">
      <div class="swiper-slide newslide2">
      	<div class="review">
		<p>Lorem ispum dolor sitame Lorem ispum dolor sitameLorem ispum dolor sitame Lorem ispum dolor sitame Lorem ispum dolor sitameLoremLorem ispum dolor sitameLorem ispum dolor sitame Lorem ispum dolor sitame</p>
		<div class="personal_img"></div>
		<h2 class="person">Name Surname, big boss COMPANY</h2>
		<a class="person_company" href="#">his comany</a>
	</div>
	</div>
	<div class="swiper-slide newslide2">
      	<div class="review">
		<p>Lorem ispum dolor sitame Lorem ispum dolor sitameLorem ispum dolor sitame Lorem ispum dolor sitame Lorem ispum dolor sitameLoremLorem ispum dolor sitameLorem ispum dolor sitame Lorem ispum dolor sitame</p>
		<div class="personal_img"></div>
		<h2 class="person">Name Surname, big boss COMPANY</h2>
		<a class="person_company" href="#">his comany</a>
		</div>
	</div>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>

	</div>
	<div id="time_container">
		<h2>Мы всегда рады помочь!</h2>
		<p style="text-align: center;">Наша служба поддержки работает<br/> 24 часа и 7 дней в неделю</p>
		<!-- clock -->
		<div id="time_img">
			
			<div class="clock">
				
			</div>
			
		
		</div>
	</div>
</section>
		<!--reviews section end-->
		<!--footer-->
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
		<a class="ToSection2" href="#home_page">Главная</a>
		<a class="ToSection2" href="#instructions">О InterHelper</a>
		<a href="https://interfire.ru/portfolio">О нас</a>
		<a href="#reviews_block" class="ToSection2">Отзывы</a>
	</div>
	<div class="column" id="footer_third_column">
		<h2 class="footer_header">Полезные ссылки</h2>
		<a href="#">Политика конфиденциальности</a>
		<a href="#">Поддержка</a>
	</div>
	<div class="column" id="footer_fourth_column">
	<h2 class="footer_header">Вход</h2>
	<a href="#" id="SignIn2" class="loginingmenu">Начать</a>
	<a href="#" id="SignUp2" class="loginingmenu">Войти</a>
	<span href="#home_page" class="ToSection2" id="To_up_button"></span>
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
			<!--footer end-->
			<!--login menu-->
<div id="loginingmenu">
	<div class="containerr" id="containerr">
	<div id="loginingmenuExit"></div>
	<div class="form-container sign-up-container">
		<form class="ajax_login_form" action="/php/login.php" method="post">
			<h2 id="create" style="color: #fff; font-size: 2em;position: relative;bottom: 20px;">Создать аккаунт</h2>
			<span style="font-size: 1em; color: #fff; position: relative;bottom: 20px;"></span>
			<input style="color: #fff;" type="text" name="User" placeholder="Name" />
			<input style="color: #fff;" type="email" name="Email" placeholder="Email" />
			<input style="color: #fff;" type="password" name="Password" placeholder="Password" />
			<button class="butr" type="submit">Начать</button>
		</form>
	</div>
	<div class="form-container sign-in-container">
		<form class="ajax_login_form" action="/php/login.php" method="post" style="color: #fff;">
			<h2 style="font-size: 2em; position: relative; bottom: 20px; " id="signinn">Войти</h2>
			
			<span style="font-size: 1.3em;  position: relative; bottom: 20px"></span>
			<input style="color: #fff;" type="email" placeholder="Email"name="EmailL" />
			<input style="color: #fff;" type="password" placeholder="Password"name="PasswordL" />
			<a href="#" style="font-size: 1em; color: #0ae; position: relative; top: 20px;">Забыли пароль?</a>
			<button class="butr" type="submit">Войти</button>
		</form>
	</div>
	<div class="overlay-container">
		<div class="overlay">
			<div class="overlay-panel overlay-left">
				<h2 style="font-size: 2.5em; position: relative;bottom: 50px;">С возвращением!</h2>
				<p style="font-size: 1.2em;">Чтобы оставаться на связи с нами, войдите, указав свою личную информацию</p>
				<button style="position: relative;
				top: 50px;" class="ghost" id="signIn">Войти</button>
			</div>
			<div class="overlay-panel overlay-right">
				<h2 style="font-size: 2.5em; position: relative;bottom: 50px;">Hello, Friend!</h2>
				<p style="font-size: 1.2em;">Введите свои личные данные и начните путешествие с нами</p>
				<button style="position: relative;
				top: 50px;" class="ghost" id="signUp">Начать</button>
			</div>
		</div>
	</div>
</div>
</div>
	<!-- login menu end-->

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
	// test zone
</script>
</body>
</html>