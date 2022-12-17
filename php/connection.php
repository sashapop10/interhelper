<?php
	define("DBHOST","localhost");
	define("DBUSER","root");
	define("DBPASS","");
	define("DBNAME","interhelper");
	$connection = @mysqli_connect(DBHOST, DBUSER, DBPASS , DBNAME) or die("Нет соединения с базой данных");
	mysqli_set_charset($connection, "utf8") or die("Нет соединения с базой данных");
?>