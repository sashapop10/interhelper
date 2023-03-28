<?
include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
global $connection;
$dataSet = $_POST;
if (!$dataSet) exit('Ошибка платежа');
unset($dataSet['ik_sign']); // удаляем из данных строку подписи
ksort($dataSet, SORT_STRING); // сортируем по ключам в алфавитном порядке элементы массива
array_push($dataSet, 'Y5lpv6nmAtRujpte'); // добавляем в конец массива "секретный ключ"
$signString = implode(':', $dataSet); // конкатенируем значения через символ ":"
$sign = base64_encode(md5($signString, true)); // берем MD5 хэш в бинарном виде по сформированной строке и кодируем в BASE64
if ($sign != $_POST['ik_sign']) exit('Ошибка обработки платежа');
$login = $_POST['ik_x_login'];
$money = $_POST['ik_am'];
$success = $_POST['ik_inv_st'];
if($success == "success"){
    if ($connection->connect_error) {  die("Connection failed: " . $connection->connect_error); }
    $sql = "UPDATE users SET money = money + $money WHERE id = \"$login\" ";
    if ($connection->query($sql) === TRUE) echo "Record updated successfully";
    else { echo "Error updating record: " . $connection->error; }
    $connection->close();
} else exit('Ошибка обработки платежа');
?>