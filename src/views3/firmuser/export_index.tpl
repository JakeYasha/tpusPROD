<?= $bread_crumbs ?>
<div class="black-block">Статистика email</div>
<div class="search_result">
    <? if ($dates_block) { ?>
        <ul class="date-block">
            <? foreach ($dates_block as $date) { ?>
                <li<? if ($date['active']) { ?> class="active"<? } ?>><a<? if ($date['active']) { ?> class="js-action"<? } ?> href="<?= $date['link'] ?>"><?= $date['name'] ?></a></li>
                <? } ?>
        </ul>
    <? } ?>
    <?= $items ?>
    <?= $pagination ?>
	<div class="attention-info">
		<p>Отчет "Статистика email, sms" показывает, сколько запросов было принято на формирование для абонента ответа с отправкой на его email или sms операторами справочной телефонной службы, 
		где в ответ на запрос была выдана информация о Вашей фирме. Для каждого запроса указывается какие именно предложения (товары или
		услуги из прайс-листа) были отмечены в экспортной сессии.</p>
	</div>
	<?=app()->chunk()->render('firmuser.call_support_block')?>
</div>