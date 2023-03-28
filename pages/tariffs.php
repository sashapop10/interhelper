<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/scss/imgs/interhelper_icon.svg" type="image/png">
    <title>InterHelper</title>
    <link rel="stylesheet" type="text/css" href="/scss/libs/reset.css">
    <link rel="stylesheet" href="/scss/main.css">
    <script type="text/javascript" src="/HelperCode/Helper"></script>
</head>
<body>
<section id="main">
        <div id="main-window">
            <h2 class="page-header">Превратите посетителей в <br/> покупателей</h2>
            <h3 class="page-underheader">Выбирите версию InterHelper, которая лучше всего<br/>подходит для решения ваших задач</h3>
            <div class="pricing-table">
                <div class="pricing-table-top">
                    <div class="pricing-table-tablename">
                        <h1 class="animation-text-left-grey hex-animation">Сравнение тарифов</h1>
                        <p>С попомощью данной таблицы, вы можете оценить возможности нашего проекта и выбрать себе самый подходящий тариф.</p>
                    </div>
                    <div v-for="(tariff, tariff_index) in editions" class="pricing-table-tariff-main v-cloak-off" v-cloak>
                        <h2 class="span_text_animation-center"><a class="tariff-table-tariff-name" href="#tariff-table-footer">{{tariff.name}}</a><span :style="'background:'+tariff.img+';'"></span></h2>
                        <a class="tariff-table-tariff-cost-btn" :style="'background:'+tariff.img+';'" href="#tariff-table-footer">
                            <p>{{tariff.cost.text ? tariff.cost.text.split('/')[0] : ''}}</p>
                            <p>{{tariff.cost.value}}</p>
                            <p><span>{{tariff.cost.text ? 'В' : ''}}</span><span>{{tariff.cost.text ? tariff.cost.text.split('/')[1] : ''}}{{tariff.cost.text ? '.' : ''}}</span></p>
                        </a>
                    </div>
                    <div class="pricing-table-tariff-main v-cloak-on" v-cloak>
                        <h2 class="span_text_animation-center v-cloak-gradient v-cloak-tariff-name"><span class="v-cloak-gradient"></span></h2>
                        <a class="tariff-table-tariff-cost-btn v-cloak-tariff-button v-cloak-gradient"></a>
                    </div>
                    <div class="pricing-table-tariff-main v-cloak-on" v-cloak>
                        <h2 class="span_text_animation-center v-cloak-gradient v-cloak-tariff-name"><span class="v-cloak-gradient"></span></h2>
                        <a class="tariff-table-tariff-cost-btn v-cloak-tariff-button v-cloak-gradient"></a>
                    </div>
                    <div class="pricing-table-tariff-main v-cloak-on" v-cloak>
                        <h2 class="span_text_animation-center v-cloak-gradient v-cloak-tariff-name"><span class="v-cloak-gradient"></span></h2>
                        <a class="tariff-table-tariff-cost-btn v-cloak-tariff-button v-cloak-gradient"></a>
                    </div>
                    <div class="pricing-table-tariff-main v-cloak-on" v-cloak>
                        <h2 class="span_text_animation-center v-cloak-gradient v-cloak-tariff-name"><span class="v-cloak-gradient"></span></h2>
                        <a class="tariff-table-tariff-cost-btn v-cloak-tariff-button v-cloak-gradient"></a>
                    </div>
                    <div class="pricing-table-tariff-main v-cloak-on" v-cloak>
                        <h2 class="span_text_animation-center v-cloak-gradient v-cloak-tariff-name"><span class="v-cloak-gradient"></span></h2>
                        <a class="tariff-table-tariff-cost-btn v-cloak-tariff-button v-cloak-gradient"></a>
                    </div>
                </div>
                <div class="pricing-table-bottom">
                    <div class="pricing-table-tool-info">
                        <div id="tariff-table-footer">
                            <p>Уникальных посетителей<p>
                            <p>За месяц.</p>
                        </div>
                        <div>
                            <p>Записей в CRM<p>
                            <p>Записи - строки в CRM.</p>
                        </div>
                        <div>
                            <p>Задач в CRM<p>
                            <p>Задача - напоминание по записи.</p>
                        </div>
                        <div>
                            <p>Таблиц в CRM<p>
                            <p>Таблица - уникальная страница для записей.</p>
                        </div>
                        <div>
                            <p>Столбцов в CRM<p>
                            <p>Столбец - поле Ваших записей.</p>
                        </div>
                        <div>
                            <p>Вариантов списка CRM<p>
                            <p>Варианты для специального типа столбца - "список".</p>
                        </div>
                        <div>
                            <p>Уникальных посетителей<p>
                            <p>За месяц.</p>
                        </div>
                        <div>
                            <p>Сотрудников<p>
                            <p>Аккаунты, на которые могут зайти Ваши сотрудники.</p>
                        </div>
                        <div>
                            <p>Доменов<p>
                            <p>Количество подключенных Вами сайтов.</p>
                        </div>
                        <div>
                            <p>Отделов<p>
                            <p>Отдел регулирует доступ вашим сотрудникам к инструментам InterHelper.</p>
                        </div>
                        <div>
                            <p>Разделов для шаблонов ответов<p>
                            <p>Чем подробнее - тем удобнее.</p>
                        </div>
                        <div>
                            <p>Шаблонов ответов<p>
                            <p>Чем больше - тем быстрее.</p>
                        </div>
                        <div>
                            <p>Подмен<p>
                            <p>Подменяйте контент на сайте, не залезая в код.</p>
                        </div>
                        <div>
                            <p>Рассылок</p>
                            <p>Заранее настроенные вами сообщения.</p>
                        </div>
                        <div>
                            <p>Рассылка почты</p>
                            <p>Стоимость за единицу.</p>
                        </div>
                        <div>
                            <p>Подключение</p>
                            <p>Выбрали тариф ? Подключите его перейдя по ссылке !</p>
                        </div>
                    </div>
                    <div class="pricing-table-tool-value v-cloak-off" v-for="(tariff, tariff_index) in editions"  v-cloak>
                        <div>
                            <p v-if="tariff.include.unique_visits.value != 0">{{tariff.include.unique_visits.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.crm_items.value != 0">{{tariff.include.crm_items.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.tasks.value != 0">{{tariff.include.tasks.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.assistents.value != 0">{{tariff.include.assistents.value}}</h2>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.domains.value != 0">{{tariff.include.domains.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.departaments.value != 0">{{tariff.include.departaments.value}}</h2>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.tables.value != 0">{{tariff.include.tables.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.table_columns.value != 0">{{tariff.include.table_columns.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.variants.value != 0">{{tariff.include.variants.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.fast_messages_dirs.value != 0">{{tariff.include.fast_messages_dirs.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.fast_messages.value != 0">{{tariff.include.fast_messages.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.swaper.value != 0">{{tariff.include.swaper.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.autosender.value != 0">{{tariff.include.autosender.value}}</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.mailer.value != 0">{{tariff.include.mailer.value}} ₽</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p v-if="tariff.include.unique_visits_limit.value != 0">{{tariff.include.unique_visits_limit.value}} ₽</p>
                            <p v-else><svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path d="M20.288 9.463a4.856 4.856 0 0 0-4.336-2.3 4.586 4.586 0 0 0-3.343 1.767c.071.116.148.226.212.347l.879 1.652.134-.254a2.71 2.71 0 0 1 2.206-1.519 2.845 2.845 0 1 1 0 5.686 2.708 2.708 0 0 1-2.205-1.518L13.131 12l-1.193-2.26a4.709 4.709 0 0 0-3.89-2.581 4.845 4.845 0 1 0 0 9.682 4.586 4.586 0 0 0 3.343-1.767c-.071-.116-.148-.226-.212-.347l-.879-1.656-.134.254a2.71 2.71 0 0 1-2.206 1.519 2.855 2.855 0 0 1-2.559-1.369 2.825 2.825 0 0 1 0-2.946 2.862 2.862 0 0 1 2.442-1.374h.121a2.708 2.708 0 0 1 2.205 1.518l.7 1.327 1.193 2.26a4.709 4.709 0 0 0 3.89 2.581h.209a4.846 4.846 0 0 0 4.127-7.378z"/></svg></p>
                        </div>
                        <div>
                            <p :style="'background:'+tariff.img+';'">подключить</p>
                        </div>
                    </div>
                    <div class="pricing-table-tool-value v-cloak-on" v-cloak>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                    </div>
                    <div class="pricing-table-tool-value v-cloak-on" v-cloak>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                    </div>
                    <div class="pricing-table-tool-value v-cloak-on" v-cloak>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                    </div>
                    <div class="pricing-table-tool-value v-cloak-on" v-cloak>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                    </div>
                    <div class="pricing-table-tool-value v-cloak-on" v-cloak>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                        <div>
                            <p class="v-cloak-gradient"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popular-questions">
                <h2 class="page-header">Часто задаваемые вопросы</h2>
                <div class="popular-questions-80">
                    <div>
                        <h2>У вас есть сайт для мобильных сайтов?</h2>
                        <p>Да, вы можете установить чат на мобильный сайт, мы поддерживаем все популярные мобильные браузеры.</p>
                    </div>
                    <div>
                        <h2>Как быстро я смогу усвоиться в инструментах?</h2>
                        <p>Благодаря простоте в использовании InterHelper, вы быстро сможете освоиться в личном кабинете и работать без проблем.</p>
                    </div>
                    <div>
                        <h2>Вы поддерживаете уникальные настройки под разные домены?</h2>
                        <p>В настройках домена вы сможете выбрать ему персональные настройки или общий.</p>
                    </div>
                    <div>
                        <h2>Вы даёте операторов или только чат?</h2>
                        <p>Мы предоставляем только программное обеспечение.</p>
                    </div>
                    <div>
                        <h2>Могут ли сотрудники, которые не общаются с клиентами, работать в InterHelper?</h2>
                        <p>Конечно, InterHelpr - это не просто чат на сайт, но и многие други инструменты, которыми могут пользоваться все члены команды, а с помощью отделов Вы сможете распределить к ним доступ.</p>
                    </div>
                    <div>
                        <h2>Что нужно, чтобы начать пользоваться инструментами InterHelper?</h2>
                        <p>Чтобы начать пользоваться InterHelper, вам нужно завести аккаунт InterHelper, подключить свой домен и создать сотрудника для того, чтобы общаться с посетителями.</p>
                    </div>
                </div>
            </div>
            <div class="connect-helper">
                <div class="connect-helper-80">
                    <svg class="form-logo" version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 546.76 380.96' style='enable-background:new 0 0 546.76 380.96;' xml:space='preserve'><g><path style='transform:rotateY(180deg); transform-origin:center center;' class='st0' d='M0,203.57V23.48C0,10.51,10.51,0,23.48,0h499.81c12.97,0,23.48,10.51,23.48,23.48l0,334.28c0,21.26-25.62,31.02-40.51,15.85c-4.28-4.36-8.86-8.49-13.73-12.38c-36.77-29.4-89.86-44.31-157.68-44.31H23.48C10.51,316.91,0,306.4,0,293.44V203.57z'/><circle cx='119.05' cy='157.87' r='35.08'/><circle  cx='244.91' cy='157.87' r='35.08'/><circle  cx='370.77' cy='157.87' r='35.08'/></g></svg>
                    <h2 class="page-header">Подключите InterHelper</h2>
                    <p class="page-underheader">Стартровый тариф - <span>бесплатно</span></p>
                    <button>Подключите InterHelper</button>
                </div>
            </div>
        </div>
    </section>
</body>
<script src="/scripts/hidden_scripts/main.js"></script>
</html>