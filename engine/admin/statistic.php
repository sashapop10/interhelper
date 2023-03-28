<?php
	session_start();
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
    global $connection;
    include($_SERVER['DOCUMENT_ROOT'] . '/engine/config.php');
    if (isset($_SESSION["admin"]) && $_SESSION["admin"] != '') {
        $sql = "SELECT count(1) FROM users";
        $users_count = attach_sql($connection, $sql, 'row')[0];
        $sql = "SELECT count(1) FROM assistents";
        $assistentss_count = attach_sql($connection, $sql, 'row')[0];
        $sql = "SELECT count(1) FROM tasks";
        $tasks_count = attach_sql($connection, $sql, 'row')[0];
        $sql = "SELECT count(1) FROM messages_with_users_guests";
        $messages_with_users_guests_count = attach_sql($connection, $sql, 'row')[0];
        $sql = "SELECT count(1) FROM assistents_chat_messages";
        $assistents_chat_messages_count = attach_sql($connection, $sql, 'row')[0];
        $sql = "SELECT count(1) FROM banned";
        $bunned_count = attach_sql($connection, $sql, 'row')[0];
        $sql = "SELECT count(1) FROM rooms";
        $rooms_count = attach_sql($connection, $sql, 'row')[0];
    } else { header("Location: /engine/admin/login");  exit; }
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
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
	<?php admin_navigation('statistic'); ?>
    <section id='container'>
        <?php section_header('statistic', 'crm_statistic.png'); ?>
        <div id='middle_part'>
            <h2 class='header1 wow bounceInUp' data-wow-delay='0.1s'>Статистика из бд</h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.15s'>Количество пользователей <span style='color:#0ae;'><?php echo $users_count; ?></span></h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.2s'>Количество ассистентов <span style='color:#0ae;'><?php echo $assistentss_count; ?></span></h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.35s'>Количество задач <span style='color:#0ae;'><?php echo $tasks_count; ?></span></h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.4s'>Количество сообщений с посетителями <span style='color:#0ae;'><?php echo $messages_with_users_guests_count; ?></span></h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.45s'>Количество сообщений с ассистентами <span style='color:#0ae;'><?php echo $assistents_chat_messages_count; ?></span></h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.5s'>Количество заблокированных посетителей <span style='color:#0ae;'><?php echo $bunned_count; ?></span></h2>
            <h2 class='header1 wow bounceInUp WhiteBlack' data-wow-delay='0.55s'>Количество чатов с сообщениями <span style='color:#0ae;'><?php echo $rooms_count; ?></span></h2>
        </div>
    </section>
    <?php appendfooter(); ?>
</body>
<script type="text/javascript" src="/scripts/router?script=admin_page"></script>
</html>