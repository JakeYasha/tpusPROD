<?= $bread_crumbs?>
<?

//if (APP_IS_DEV_MODE){
    ?>
<style>
    .adm-btn-success:hover {
        color: #fff;
        background-color: #449d44;
        border-color: #419641;
    }

    .adm-btn:focus, .adm-btn:hover {
        text-decoration: none;
    }
    .adm-btn:focus, .adm-btn.focus, .adm-btn:active:focus, .adm-btn:active.focus, .adm-btn.active:focus, .adm-btn.active.focus {
        outline: 5px auto -webkit-focus-ring-color;
        outline-offset: -2px;
    }
    .adm-btn {
        display: inline-block;
        font-weight: normal;
        line-height: 1.25;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        border-radius: 0.25rem;
    }
    .adm-btn-success {
        width: 100%;
        color: #fff;
        background-color: #5cb85c;
        border-color: #5cb85c;
    }
    
    
    .adm-news-modal{
        z-index: 99999;
        position: fixed;
        display: flex;
        flex-direction: column;
        justify-content: normal;
        align-items: stretch;
        align-content: space-around;
        width: 98%;
        height: 95%;
        top: 900%;
        left: 1%;
        background-color: #FFF;
        border-radius: 5px;
        border: 4px ridge black;
    }
    .adm-modal-show{
        top: 2%;
    }
    .adm-relative{
        position: relative;
        overflow-y: auto;
    }
    .adm-content{
        margin: 20px;
        font-size: 25px;
    }
    .adm-content>.adm-text *{
        all: revert;
    }
    .adm-content img{
        max-width: 100%;
        height: auto;
    }
      
    .adm-btn-close {
        min-width: 128px;
        color: #fff;
        background-color: #b85c5c;
        border-color: #b90724;
    }
</style>
<div>
    <div>
        <?
        //var_dump($ittest);
        ?>
        <div><a class="adm-btn adm-btn-success" onclick="show_admmodal('adm-news');" href="#">НОВОСТИ ТОВАРЫ+</a></div>
    </div>
    <div id="adm-news" class="adm-news-modal">
        
        <div style="position: relative;text-align: right;width: 100%;"><a class="adm-btn adm-btn-close" onclick="show_admmodal('adm-news');" href="#">ЗАКРЫТЬ</a></div>
        <div class="adm-relative">
            <div class="adm-content" style="font-family:Verdana,Arial,Helvetica,sans-serif;
	font-size:14px;
	line-height:1.3;"> 
                <div class="adm-text">
                    <h1>Добрый день, дорогие коллеги!</h1>
                    <p>Ввиду некоторых изменений в работе сервера, немного изменился процесс обмена между вашими БД Ратисс и каталогом сайта Товары+</p>
                    <p><br />Хочется выразить благодарность специалистам служб, что всегда были на связи и вместе мы смогли решить не мало возникающих проблем в работоспособности сайта и обмена. Ниже прилагается инструкция, которую вам следует передать вашим тех.специалистам, для настройки обмена.</p>


                    <p>
                        <h2>Инструкция:</h2> <br>

                        <ul>
                            <li>1) Скачайте дополнение для обмена по ссылке: <a href="/update/forUpdateProg/UpdateFor<?=app()->firmManager()->id_service();?>.zip" download>дополнение</a></li>
                            <li>2) Перенесите содержимое архива в папку, в которой находится программа обмена TPlusUpdate</li>
                            <li>3) Выполните обмен, как и раньше, используя TPlusUpdate и настройте регулярное выполнение обмена, раз в сутки*, в 23:00</li>
                            <li>4) После 3авершения обмена, запустите программу из архива ForTPLusUpdate<?=app()->firmManager()->id_service();?>.exe(ничего произойти не должно. Но! Если будет ошибка - сообщите мне через тг со скриншотами/описанием)<br>
        (настройте регулярное выполнение этого приложения, раз в сутки, в 23:20)</li>
                        </ul>
                        <br>
                        <i>Если дополнение не скачивается или пишутся ошибки при выполненнии/запуске нового приложения, напишите, пожалуйста, через телеграмм: <a href="https://t.me/altermiroshifre">Программист Т+</a></i>
                    </p>
                    <br>
                    <p>
                        <b>Для системных администраторов:</b> <br>
                            Для настройки автоматического запуска программы используйте планировщик заданий. Для запуска программы используйте следующие параметры (<b>а</b>  автозакрытие программы, <b>d</b> количество дней за которые снимать данные от текущего дня, <b>h</b> загрузка статистики в локальную базу) <br>
                            Пример для запуска обмена ежедневно: <i>C:\XXXXXXX\TPlusUpdate\TPlusUpdate.exe -a -d=3 -h</i> <br>
                            Пример для запуска обмена 1 раз в неделю:   <i>C:\XXXXXXX\TPlusUpdate\TPlusUpdate.exe -a -d=9 -h</i><br><br>

                        Если вы настроили запуск программы по расписанию только 1 раз в неделю, то и период снятия данных должен быть как минимум 8 дней!<br>
                        <b>Рекомендуемый конфиг для настройки обмена: <i>TPlusUpdate.exe -a -d=41 -h </i></b><br>
                            Для пункта 4, автозапуск, без ключей по инструкции ранее.

                    </p>
                    <br>
                    <p>
                        За последние два года, выявилось не мало трудностей в работе сайта. Ввиду этого, на 2023 год, запланированы важные изменения в работе всего портала. <br>
                        В рамках обновления, имеются планы по уходу со схемы обновления фирм и прайсов через программы Ратисс, ввиду того, что их опслуживание закончилось более 5 лет назад. Технологии уже сильно поменялись и старые методы работы, создают много неудобств и трудностей в обслуживании портала. Например, невозможность изменять информацию по фирмам в реальном времени. Сбои в работе статистики. Некорректные данные в карточках товаров и фирм.<br> Ввиду этого, были начаты работы по обновлению работы сайта и его внутренней структуры. На данный момент, резкого перехода <b>не планируется</b>. О нововведениях, будет сообщено в подобных новостных сообщениях. <br> Цель - не изменить ради замены. А найти решения, что были бы удобны для всех, как для вас, как специалистов, так и для посетителей портала. <br>
                        Ввиду активного развития и актуальности мессенджеров, имеет место быть разработка API для доступа к материалам сайта, например, через чат-боты. Так же, хорошо себя зарекомендовал раздел "газеты", с разносторонними материалами. На данный момент, на него приходится более 30% траффика отдельной службы. <br>
                        Уже сейчас, в разделе "баннеры", вы можете продлить завершающийся баннер на некоторое время. Так как баннеры относятся чисто к отдельным службам, то в ближайшем будущем, можно ожидать функционал ручного редакатирования баннеров для служб.<br>
                        Проблемы с поиском и 504 ошибка. - данная ошибка возникает всего в 1% случаев у пользователей. Но, это тоже не мало. Почему так происходит? Чтобы происходил быстрый поиск на сайте, каждые 10-20 минут, служба поиска перезагружается. Это длится пару секунд. Но если пользователь попадает в эти секунды, 504 ошибка может преследовать его на протяжении нескольких часов, а то и дней по ряду независящим от нас причинам. Реорганизация поиска, так же стоит на повестке тех.работ.  
                    </p>
                    <p>
                        На данный момент(14.03.2023), это все новости! Настройте, пожалуйста, обмен и в случае чего, на связи(если звонки, то только <b>с 11:00</b> по Мск).
                        <br><br>
                        С Уважением, тех.отдел сайта Товары+
                    </p>
                </div>



            </div>
        </div>
    </div>
</div>

<script>
    function show_admmodal(idel){
        el = document.getElementById(idel);
        el.classList.toggle("adm-modal-show");
    }

</script>
<?
//}

?>
<div class="black-block">Выбор фирмы для управления и просмотра статистики</div>
<div class="cat_description">
	<a href="/page/show/access-personal-account.htm" target="_blank">Инструкция по предоставлению фирмам доступа к личному кабинету</a>
</div>
<div class="search_result in-firm-manager" style="border-top: none;">
<div class="search_price_field">
	<form action="/firm-manager/" method="get">
		<input placeholder="Поиск по названию фирмы..." class="e-text-field" type="text" name="query"<?if($filters['query']){?> value="<?=$filters['query']?>"<?}?> />
		<input type="submit" value="" class="submit">
	</form><?=$sorting?>
</div><br/>
<?=$items?>
<?=$pagination?>
</div>