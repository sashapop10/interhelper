<?php 
    require_once 'classList.php';
    $varaibles = new _variables();
    $status = new _status();
    $database = new _database();
    $methods = new _methods;
    $database->connect();
    $user = new _USER();
    $getSettings = new _get_settings();
    if($user->url['dir'] == 'pages') $page = new _visitor_pages();
    $database->close();
    $status->print();
?>