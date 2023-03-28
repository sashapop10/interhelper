<?php
	// if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
	// 	$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	// 	header('HTTP/1.1 301 Moved Permanently');
	// 	header('Location: ' . $location);
	// 	exit;
	// }
	define("DBHOST","localhost");
	define("DBUSER","root");
	define("DBPASS","Fadkj123ADSFJ!");
	define("DBNAME","interhelper");
	if( isset($_SERVER['HTTPS'] ) ) { define("SERVERPATH","https://api.interfire.ru:5321"); }
	else{  define("SERVERPATH","http://api.interfire.ru:5320"); }
	global $connection;
	$connection = @mysqli_connect(DBHOST, DBUSER, DBPASS , DBNAME) or die("Нет соединения с базой данных");
	$connection->set_charset("utf8") or die("Нет соединения с базой данных");
	date_default_timezone_set("Europe/Moscow");
?>