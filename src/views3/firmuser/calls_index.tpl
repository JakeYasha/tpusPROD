<?= $bread_crumbs ?>
<div class="black-block">Статистика звонков</div>
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
		<p>Отчет "Статистика звонков" показывает сколько звонков было принято операторами справочной телефонной службы, 
		где в ответ на запрос абонента была выдана информация о Вашей фирме. Для каждого звонка указывается какие именно предложения (товары или
		услуги из прайс-листа) были выданы, а в случае проведения процедуры переадресации звонка показывается телефон Вашей фирмы, куда был переведен
		звонок и результат переадресации.</p>
	</div>
	<?=app()->chunk()->render('firmuser.call_support_block')?>
</div>