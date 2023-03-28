<?php
  session_start();
  $_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
  include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
  include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
  if(isset($_SESSION["boss"])){ 
      $user_id = $_SESSION["boss"];
      $sql = "SELECT photo, count(1) FROM users WHERE id = '$user_id'";
      $results = attach_sql($connection, $sql, 'row');
      if(intval($results[1]) != 0) $photo = str_replace(' ', '%20', $results[0]); 
      else {
          unset($_SESSION['boss']); 
          header("Location: /index"); exit;
      }
  }
  if(!isset($photo)) $photo = '';
  $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
  mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <link rel="stylesheet" href="/scss/libs/slick.css">
    <link rel="stylesheet" href="/scss/main_page.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
	<link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <title>INTERHELPER - Больше чем онлайн консультант</title>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
    <meta name="yandex-verification" content="0c2d5ab7afbec199" />
</head>
<body>
    <?php head3($file_path, $photo); ?>
    <section class="WhiteBlack" style="display:flex;align-items:center;justify-content:center;margin-top:70px;height:700px;flex-direction:column;">
        <h1 class="tt1">Ошибка платежа</h1>
        <a class="try_btn" style="margin-top:10px" href="/engine/pages/profile">Вернуться в личный кабинет</a>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
    <?php login_menu(); ?>
    <script src="/scripts/router?script=main"></script>
</body>
</html>