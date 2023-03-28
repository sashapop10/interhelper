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
    $tools_photos_path = VARIABLES["photos"]["tools_photos"]["upload_path"];
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <link rel="stylesheet" href="/scss/main_page.css">
    <link rel="stylesheet" href="/scss/libs/animate.css">
	<script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <script src="/scripts/libs/wow.min.js"></script>
	<script type="text/javascript" src="/HelperCode/Helper"></script>
	<title>INTERHELPER - Больше чем онлайн консультант</title>
	<link rel='shortcut icon' href='/scss/imgs/interhelper_icon.svg' type='image/png'>
</head>
<body>
    <?php head3($file_path, $photo); ?>
    <section class="payments vue_el2 wow bounceInDown" style="margin:40px;margin-top:100px;">
        <h1 class="tt1 WhiteBlack">Способы оплаты на interhelper.ru</h1>
        <h2 class="WhiteBlack payment_type">Банковские карты</h2>
        <p style="max-width:700px;margin-top:10px;margin-bottom:10px;"class="WhiteBlack payment_info">Вы можете оплатить заказ, используя банковскую карту <strong>Visa, Mastercard или Мир</strong>. Для осуществления платежа вам потребуется сообщить данные вашей пластиковой карты. Передача этих сведений производится с соблюдением всех необходимых мер безопасности. Данные будут сообщены только на авторизационный сервер банка по защищенному каналу (протокол SSL 3.0). Информация передается в зашифрованном виде и сохраняется только на специализированном сервере платежной системы.</p>
        <h2 class="WhiteBlack payment_type">Платежные сервисы</h2>
        <div style="max-width:700px;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;">
            <div class="payment_block">
                <h2 class="WhiteBlack payment_name" style="margin-top:10px;">Сбербанк</h2>
                <p  class="WhiteBlack payment_info">Для оплаты (ввода реквизитов Вашей карты) Вы будете перенаправлены на платёжный шлюз ПАО СБЕРБАНК. Соединение с платёжным шлюзом и передача информации осуществляется в защищённом режиме с использованием протокола шифрования SSL. В случае если Ваш банк поддерживает технологию безопасного проведения интернет-платежей Veriﬁed By Visa, MasterCard SecureCode, MIR Accept, J-Secure для проведения платежа также может потребоваться ввод специального пароля. Настоящий сайт поддерживает 256-битное шифрование. Конфиденциальность сообщаемой персональной информации обеспечивается ПАО СБЕРБАНК. Введённая информация не будет предоставлена третьим лицам за исключением случаев, предусмотренных законодательством РФ. Проведение платежей по банковским картам осуществляется в строгом соответствии с требованиями платёжных систем МИР, Visa Int., MasterCard Europe Sprl, JCB.</p>
            </div>
            <div class="payment_block" style="margin-top:10px;">
                <h2 class="WhiteBlack payment_name">Интеркасса</h2>
                <p class="WhiteBlack payment_info">Интеркасса — доступный и безопасный способ платить за товары и услуги через интернет.</p>
            </div>
        </div>
    </section>
    <?php appendfooter2($file_path, $photo); ?>
	<?php login_menu(); ?>
    <script src='/scripts/libs/vue.js'></script>
    <script src="/scripts/router?script=main"></script>
</body>
</html>