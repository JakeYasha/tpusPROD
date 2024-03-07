<div class="item_info offset-none">
    <div class="search-result">
        <?= $tabs ?>
        <? if ($items) { ?>
            <div class="last_reviews w100 js-next-reviews-holder">
                <div class="reviews-header">
                    <h2><?= app()->metadata()->getTitle() ?></h2>
                    <a class="btn-base btn-red btn-add fancybox fancybox.ajax" href="/firm-review/get-add-form/<?= $firm->id() ?>/" rel="nofollow">Добавить отзыв</a>
                </div>
                <? foreach ($items as $item) { ?>
                    <div class="review">
                        <div class="main_review">
                            <?= app()->chunk()->setArgs([$item['score'], true])->render('rating.stars') ?><br/>
                            <div class="name_date">
                                <span class="name"><?= $item['user'] ?></span>, <span class="date"><?= $item['date'] ?>:</span>
                            </div>
                            <p><?= strip_tags($item['text']) ?></p>
                        </div>
                        <? if ($item['reply_text']) { ?>
                            <div class="main_review reply">
                                <div class="name_date">
                                    <span class="name"><?= $item['reply_user_name'] ?></span>, <span class="date"><?= $item['reply_date'] ?>:</span>
                                </div>
                                <p><?= strip_tags($item['reply_text']) ?></p>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
            </div>
        <? } ?>
        <? if ($has_next) { ?>
            <a class="btn-base btn-red btn-show-more js-next-btn js-action" data-holder=".js-next-reviews-holder" data-url="<?= $firm->link() ?>?mode=review&ajax=1" data-page="1" href="#">показать еще</a>
        <? } ?>
		<div class="search-result-element-block">
		<div class="element-info-block">
		<div class="buttons-block">
			<a class="btn-base btn-red w130 fancybox fancybox.ajax" href="/firm-review/get-add-form/<?= $firm->id() ?>/" rel="nofollow">Добавить отзыв</a>
			<a class="btn-base btn-grey btn-show-more js-next-btn js-action" data-holder=".js-next-reviews-holder" data-url="<?= $firm->link() ?>?mode=review&ajax=1" data-page="1" href="#">показать еще</a>
		</div>
		</div>
		</div>
        <div class="attention-info">
            <div>Комментарии и отзывы посетителей сайта TovaruPlus.ru являются выражением их личного мнения. Администрация сайта TovaruPlus.ru: 
                <ul>
                    <li>не несет ответственности за отзывы и комментарии посетителей сайта TovaruPlus.ru</li>
                    <li>не несет обязанности проверять достоверность сведений об обстоятельствах, на которых основаны мнения посетителей сайта TovaruPlus.ru</li>
                    <li>не вправе ограничивать конституционные права посетителей сайта TovaruPlus.ru, в том числе свободу слова, а также право передачи и распространения информации, если реализация данных прав не противоречит закону.</li>
                </ul>
            </div>
        </div>
    </div>	
</div>