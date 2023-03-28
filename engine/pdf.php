<?php
if(isset($_GET["id"])){
    $uid = $_GET["id"];
    $domain = explode("!@!@2@!@!", $_GET["id"])[0];
} else{
    mysqli_close($connection);
    header("Location: /index"); 
    exit;
}
require_once '../scripts/libs/dompdf/autoload.inc.php';
include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
global $connection;
$sql = "SELECT email, name, message, SendTime, photo, departament, sender, adds FROM messages_with_users_guests  LEFT JOIN assistents  ON (( messages_with_users_guests.sender = assistents.id ) OR ( messages_with_users_guests.sender IS NULL AND messages_with_users_guests.sender != assistents.id )) WHERE ( messages_with_users_guests.domain = '$domain' AND (messages_with_users_guests.room = (SELECT id FROM rooms WHERE room = '$uid')) ) ORDER BY messages_with_users_guests.id ASC";
$query = mysqli_query($connection, $sql);
$rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
$table_rows = '';
foreach($rows as $message ){
    if($message["sender"] != null){
        if($message["message"] == null) $msg = "Фото";
        else $msg = $message["message"];
        $table_rows .= 
        '<tr class="assistent">
            <td>'.$message["email"].'</td>
            <td>'.$message["SendTime"].'</td>
            <td>'. $msg.'</td>
        </tr>';
    } else{ 
        if($message["message"] == null) $msg = "Фото";
        else $msg = $message["message"];
        $table_rows .=  '<tr>
            <td>Гость</td>
            <td>'.$message["SendTime"].'</td>
            <td>'.$msg.'</td>
        </tr>';
    }       
}
mysqli_close($connection);
use Dompdf\Dompdf;
$document = new Dompdf;
$html = '
<style>
    *{
        font-family: DejaVu Sans, sans-serif;
        position:relative !important;
    }
    table{
        border-collapse: collapse,
        width:100%,
    }
    td, th{
        border:1px solid #dddddd;
        text-align:center;
        padding:8px;
        overflow:auto;
    }
    .assistent{
        text-align:left;
        background-color:#00aaee;
        color:#fff;
    }
</style>
<body>
<h1>InterHelper</h1>
<h2>История переписки</h2>
<table>
    <tr>
        <td>Почта</td>
        <td>Время</td>
        <td>Сообщение</td>
    </tr>
    '.$table_rows.'
</table>
</body>
';
$document->loadHtml($html);
$document->setPaper('A4', 'landscape');
$document->render();
$document->stream("InterHelper", array("Attachment"=>0));
?>