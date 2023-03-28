<?php
	session_start();
	$_SESSION['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/func.php"); 
	include($_SERVER['DOCUMENT_ROOT'] . "/engine/config.php"); 
   // $file_path = VARIABLES['photos']['boss_profile_photo']['upload_path'];
	if (!isset($_SESSION["boss"]) && !isset($_SESSION["employee"])) { mysqli_close($connection); header("Location: /index");  exit; }
	$user_info = check_user($connection);
	if(!$user_info['status']){ mysqli_close($connection); header("Location: ".$user_info['info']['new_url']."?message=".$user_info['info']['error']); exit; } 
	if(isset($user_info['info']['log'])) echo "<script>alert('".$user_info['info']['log']."');</script>";
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InterHelper</title>
    <link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
    <link rel="stylesheet" href="/scss/main.css">
    <script type="text/javascript" src="/scripts/libs/jquery-3.6.0.min.js"></script>
    <!-- <script type="text/javascript" src="/HelperCode/Helper"></script> -->
</head>
<body>
    <?php nav('offline', $user_info); ?>
    <section id="main">
        <?php topbar('Feedback forms'); ?>
        <div class="main-window">
            <div class="underline-text">
                <h2 @click="new_form.open_status = !new_form.open_status">{{!new_form.open_status ? 'Создать форму' : 'Отменить'}}</h2>
                <span></span>
            </div>
            <div class="deffault-text">
                <p>Ваши формы: <span>01</span></p>
            </div>
            <transition-group class="feedback-frames-container" name="list-complete" tag="div">
                <div key="new-form" v-if="new_form.open_status" class="list-complete-item feedback-form-frame">
                    <div v-if="!new_form.conditions_folder && !new_form.email_folder" class="animation-input-block feedback-form-name">
                        <input class="animation-input" type="text" value="Введите позывной формы"/>
                        <span class="focus-border">
                    </div>
                    <textarea v-if="new_form.email_folder" placeholder="Введите почту или введите несколько через запятую. 5 Макс." class="feedback-form-text"></textarea>
                    <div v-if="new_form.email_folder" class="inline-block enable-feedback-mail">
                        <span class="check_btn"><span class="checked_btn_span"></span></span>
                        <p>Получать на почту</p>
                    </div>
                    <textarea v-if="!new_form.conditions_folder && !new_form.email_folder" placeholder="Текст формы" class="feedback-form-text"></textarea>
                    <button v-if="!new_form.conditions_folder && !new_form.email_folder" class="feedback-form-add-fild-btn button-animation">Добавить поле</button>
                    <button v-if="!new_form.conditions_folder && !new_form.email_folder" class="button-animation feedback-form-button" type="button">Отправить</button>
                    <div class="feedback-form-option-list">
                        <span @click="add_new_form()" class="tooltip" data-tooltip="Сохранить" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 49 49" style="enable-background:new 0 0 49 49;" xml:space="preserve"><g><rect x="27.5" y="5" width="6" height="10"/><path d="M39.914,0H0.5v49h48V8.586L39.914,0z M10.5,2h26v16h-26V2z M39.5,47h-31V26h31V47z"/><path d="M13.5,32h7c0.553,0,1-0.447,1-1s-0.447-1-1-1h-7c-0.553,0-1,0.447-1,1S12.947,32,13.5,32z"/><path d="M13.5,36h10c0.553,0,1-0.447,1-1s-0.447-1-1-1h-10c-0.553,0-1,0.447-1,1S12.947,36,13.5,36z"/><path d="M26.5,36c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71s-0.11-0.521-0.29-0.71c-0.37-0.37-1.04-0.37-1.41,0c-0.19,0.189-0.3,0.439-0.3,0.71c0,0.27,0.109,0.52,0.29,0.71C25.979,35.89,26.229,36,26.5,36z"/></g></svg>
                        </span>
                        <span @click="new_form.email_folder = false;new_form.conditions_folder = false;" v-if="new_form.conditions_folder || new_form.email_folder"  class="tooltip" data-tooltip="Отменить | Закрыть" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="249.499px" height="249.499px" viewBox="0 0 249.499 249.499" style="enable-background:new 0 0 249.499 249.499;" xml:space="preserve"><g><path d="M7.079,214.851l25.905,26.276c9.536,9.674,25.106,9.782,34.777,0.252l56.559-55.761l55.739,56.548c9.542,9.674,25.112,9.782,34.78,0.246l26.265-25.887c9.674-9.536,9.788-25.106,0.246-34.786l-55.742-56.547l56.565-55.754c9.667-9.536,9.787-25.106,0.239-34.786L216.52,8.375c-9.541-9.667-25.111-9.782-34.779-0.252l-56.568,55.761L69.433,7.331C59.891-2.337,44.32-2.451,34.65,7.079L8.388,32.971c-9.674,9.542-9.791,25.106-0.252,34.786l55.745,56.553l-56.55,55.767C-2.343,189.607-2.46,205.183,7.079,214.851z"/></g></svg>
                        </span>
                        <span @click="new_form.email_folder = false;new_form.conditions_folder=true;" v-if="!new_form.conditions_folder" class="tooltip" data-tooltip="Условия работы" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 466.008 466.008" style="enable-background:new 0 0 466.008 466.008;" xml:space="preserve"><g><g><path d="M233.004,0C104.224,0,0,104.212,0,233.004c0,128.781,104.212,233.004,233.004,233.004c128.782,0,233.004-104.212,233.004-233.004C466.008,104.222,361.796,0,233.004,0z M244.484,242.659l-63.512,75.511c-5.333,6.34-14.797,7.156-21.135,1.824c-6.34-5.333-7.157-14.795-1.824-21.135l59.991-71.325V58.028c0-8.284,6.716-15,15-15s15,6.716,15,15v174.976h0C248.004,236.536,246.757,239.956,244.484,242.659z"/></g></g></svg>
                        </span>
                        <span @click="new_form.email_folder = true;new_form.conditions_folder=false;" v-if="!new_form.email_folder" class="tooltip" data-tooltip="Почта" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 330.001 330.001" style="enable-background:new 0 0 330.001 330.001;" xml:space="preserve"><g id="XMLID_348_"><path id="XMLID_350_" d="M173.871,177.097c-2.641,1.936-5.756,2.903-8.87,2.903c-3.116,0-6.23-0.967-8.871-2.903L30,84.602L0.001,62.603L0,275.001c0.001,8.284,6.716,15,15,15L315.001,290c8.285,0,15-6.716,15-14.999V62.602l-30.001,22L173.871,177.097z"/><polygon id="XMLID_351_" points="165.001,146.4 310.087,40.001 19.911,40 "/></g></svg>
                        </span>
                    </div>
                    <p class="feedbackform-header" v-if="new_form.conditions_folder">Выбранные условия</p>
                    <ul class="feedbackform-conditions feedbackform-list" v-if="new_form.conditions_folder">
                        <li class="text-add">Вы пока ничего не выбрали</li>
                    </ul>
                    <p class="feedbackform-header" v-if="new_form.conditions_folder">Выбор условий</p>
                    <ul class="feedbackform-conditions" v-if="new_form.conditions_folder">
                        <ul class="feedbackform-list list">
                            <li class="list-controll list-controll-close">
                                <p>Свойства</p>
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.804 17.804" style="enable-background:new 0 0 17.804 17.804;" xml:space="preserve"><g><g id="c98_play"><path d="M2.067,0.043C2.21-0.028,2.372-0.008,2.493,0.085l13.312,8.503c0.094,0.078,0.154,0.191,0.154,0.313c0,0.12-0.061,0.237-0.154,0.314L2.492,17.717c-0.07,0.057-0.162,0.087-0.25,0.087l-0.176-0.04c-0.136-0.065-0.222-0.207-0.222-0.361V0.402C1.844,0.25,1.93,0.107,2.067,0.043z"/></g><g id="Capa_1_78_"></g></g></svg>
                            </li>
                            <li v-for="(prop, index) in conditions" v-if="prop.type == 'prop'">{{prop.text}}</li>
                        </ul>
                        <ul class="feedbackform-list">
                            <li class="list-controll">
                                <p>Условия</p>
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.804 17.804" style="enable-background:new 0 0 17.804 17.804;" xml:space="preserve"><g><g id="c98_play"><path d="M2.067,0.043C2.21-0.028,2.372-0.008,2.493,0.085l13.312,8.503c0.094,0.078,0.154,0.191,0.154,0.313c0,0.12-0.061,0.237-0.154,0.314L2.492,17.717c-0.07,0.057-0.162,0.087-0.25,0.087l-0.176-0.04c-0.136-0.065-0.222-0.207-0.222-0.361V0.402C1.844,0.25,1.93,0.107,2.067,0.043z"/></g><g id="Capa_1_78_"></g></g></svg>
                            </li>
                            <li v-for="(prop, index) in conditions" v-if="prop.type != 'prop'">
                                <div>
                                    <p>{{prop.text}}</p>
                                    <button>Добавить</button>
                                </div>
                                <select v-if='index == "activity_time" && index == "time"'>
                                    <option value="<">До</option>
                                    <option value=">">После</option>
                                </select>
                                <select v-if='index == "open_counter"'>
                                    <option value="<">Меньше</option>
                                    <option value=">">Больше</option>
                                </select>
                                <input v-if="index == 'link'" type="text" placeholder="Часть ссылки">
                                <input v-if="index == 'open_counter'" type="number" placeholder="кол. посещений">
                                <input v-if="index == 'personal_event'" type="text" placeholder="Объект (#el .el el)">
                                <input v-if="index == 'personal_event'" type="text" placeholder="Ивент (click)">
                                <select>
                                    <option value="and">Обязательно</option>
                                    <option value="or">Опционально</option>
                                </select>
                            </li>
                        </ul>
                    </ul>
                </div>
                <div key="asd" class="feedback-form-frame">
                    <div class="animation-input-block feedback-form-name">
                        <input disabled class="animation-input" type="text" value="Offline form"/>
                        <span class="focus-border">
                    </div>
                    <p class="feedback-form-text">
                        К сожалению, на данный момент нет активных консультантов, но вы можете задать свой вопрос через форму ниже. Мы обязательно Вам ответим.
                    </p>
                    <input disabled class="feedback-form-input" value="Ваше имя"/>
                    <input disabled class="feedback-form-input" value="Ваш номер телефона"/>
                    <textarea disabled class="feedback-form-textarea">Введите сообщение</textarea>
                    <button class="button-animation feedback-form-button" type="button">Отправить</button>
                    <div class="feedback-form-option-list">
                        <span class="tooltip" data-tooltip="Редактировать" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 55.25 55.25" style="enable-background:new 0 0 55.25 55.25;" xml:space="preserve"><g><path d="M0.523,51.933l-0.497,2.085c-0.016,0.067-0.02,0.135-0.022,0.202C0.004,54.234,0,54.246,0,54.259c0.001,0.114,0.026,0.225,0.065,0.332c0.009,0.025,0.019,0.047,0.03,0.071c0.049,0.107,0.11,0.21,0.196,0.296c0.095,0.095,0.207,0.168,0.328,0.218c0.121,0.05,0.25,0.075,0.379,0.075c0.077,0,0.155-0.009,0.231-0.027l2.086-0.497L0.523,51.933z"/><path d="M52.618,2.631c-3.51-3.508-9.219-3.508-12.729,0L3.827,38.693C3.81,38.71,3.8,38.731,3.785,38.749c-0.021,0.024-0.039,0.05-0.058,0.076c-0.053,0.074-0.094,0.153-0.125,0.239c-0.009,0.026-0.022,0.049-0.029,0.075c-0.003,0.01-0.009,0.02-0.012,0.03l-2.495,10.48L5.6,54.182l10.48-2.495c0.027-0.006,0.051-0.021,0.077-0.03c0.034-0.011,0.066-0.024,0.099-0.039c0.072-0.033,0.139-0.074,0.201-0.123c0.024-0.019,0.049-0.033,0.072-0.054c0.008-0.008,0.018-0.012,0.026-0.02l36.063-36.063C56.127,11.85,56.127,6.14,52.618,2.631z M17.157,47.992l0.354-3.183L39.889,22.43c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0L16.097,43.395l-4.773,0.53l0.53-4.773l22.38-22.378c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0L10.44,37.738l-3.183,0.354L34.94,10.409l9.9,9.9L17.157,47.992z M46.254,18.895l-9.9-9.9l1.414-1.414l9.9,9.9L46.254,18.895z M49.082,16.067l-9.9-9.9l1.415-1.415l9.9,9.9L49.082,16.067z"/></g></svg>
                        </span>
                        <span class="tooltip" data-tooltip="Сохранить" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 49 49" style="enable-background:new 0 0 49 49;" xml:space="preserve"><g><rect x="27.5" y="5" width="6" height="10"/><path d="M39.914,0H0.5v49h48V8.586L39.914,0z M10.5,2h26v16h-26V2z M39.5,47h-31V26h31V47z"/><path d="M13.5,32h7c0.553,0,1-0.447,1-1s-0.447-1-1-1h-7c-0.553,0-1,0.447-1,1S12.947,32,13.5,32z"/><path d="M13.5,36h10c0.553,0,1-0.447,1-1s-0.447-1-1-1h-10c-0.553,0-1,0.447-1,1S12.947,36,13.5,36z"/><path d="M26.5,36c0.27,0,0.52-0.11,0.71-0.29c0.18-0.19,0.29-0.45,0.29-0.71s-0.11-0.521-0.29-0.71c-0.37-0.37-1.04-0.37-1.41,0c-0.19,0.189-0.3,0.439-0.3,0.71c0,0.27,0.109,0.52,0.29,0.71C25.979,35.89,26.229,36,26.5,36z"/></g></svg>
                        </span>
                        <span class="tooltip" data-tooltip="Отменить | Закрыть" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="249.499px" height="249.499px" viewBox="0 0 249.499 249.499" style="enable-background:new 0 0 249.499 249.499;" xml:space="preserve"><g><path d="M7.079,214.851l25.905,26.276c9.536,9.674,25.106,9.782,34.777,0.252l56.559-55.761l55.739,56.548c9.542,9.674,25.112,9.782,34.78,0.246l26.265-25.887c9.674-9.536,9.788-25.106,0.246-34.786l-55.742-56.547l56.565-55.754c9.667-9.536,9.787-25.106,0.239-34.786L216.52,8.375c-9.541-9.667-25.111-9.782-34.779-0.252l-56.568,55.761L69.433,7.331C59.891-2.337,44.32-2.451,34.65,7.079L8.388,32.971c-9.674,9.542-9.791,25.106-0.252,34.786l55.745,56.553l-56.55,55.767C-2.343,189.607-2.46,205.183,7.079,214.851z"/></g></svg>
                        </span>
                        <span class="tooltip" data-tooltip="Статистика" class="button-animation">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="52px" height="52px" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve"><g><path d="M45.5,23.4L25,34.7c-1.4,0.7-3-0.3-3-1.8V8.4c0-1-1-1.8-1.9-1.5c-10,2.8-17.2,12.5-16,23.6c1.1,10.1,9.2,18.3,19.4,19.4C36.8,51.3,48,41,48,28c0-1.2-0.1-2.4-0.3-3.6C47.5,23.4,46.4,22.9,45.5,23.4z"/><path d="M27.7,28l19.7-10.5c1.2-0.6,1.6-2.2,0.8-3.3C43.7,8,36.7,3.5,28.7,2.2C27.3,1.9,26,3,26,4.4V27C26,27.9,26.9,28.4,27.7,28z"/></g></svg>
                        </span>
                        <span class="tooltip" data-tooltip="Условия работы" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 466.008 466.008" style="enable-background:new 0 0 466.008 466.008;" xml:space="preserve"><g><g><path d="M233.004,0C104.224,0,0,104.212,0,233.004c0,128.781,104.212,233.004,233.004,233.004c128.782,0,233.004-104.212,233.004-233.004C466.008,104.222,361.796,0,233.004,0z M244.484,242.659l-63.512,75.511c-5.333,6.34-14.797,7.156-21.135,1.824c-6.34-5.333-7.157-14.795-1.824-21.135l59.991-71.325V58.028c0-8.284,6.716-15,15-15s15,6.716,15,15v174.976h0C248.004,236.536,246.757,239.956,244.484,242.659z"/></g></g></svg>
                        </span>
                        <span class="tooltip" data-tooltip="Удалить форму" class="button-animation">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="555.377px" height="555.378px" viewBox="0 0 555.377 555.378" style="enable-background:new 0 0 555.377 555.378;" xml:space="preserve"><g><g><path d="M409.442,226.725h-293.76v328.653h293.76V226.725z M193.712,510.497h-15.3V271.605h15.3V510.497z M270.212,510.497h-15.3V271.605h15.3V510.497z M346.712,510.497h-15.301V271.605h15.301V510.497z"/><path d="M439.696,165.521l-59.808-34.783l31.897-54.847L281.292,0l-31.897,54.844l-63.633-37.007L155.508,69.86l253.934,147.685L439.696,165.521z M292.36,41.836l77.59,45.125l-16.515,28.394L275.842,70.23L292.36,41.836z"/></g></g></svg>
                        </span>
                    </div>
                </div>
            </transition-group>
        </div> 
        <?php footer_panel(); ?>
    </section>
</body>
<script src='/scripts/libs/vue.js'></script>
<script src="/scripts/router?script=main"></script>
</html>