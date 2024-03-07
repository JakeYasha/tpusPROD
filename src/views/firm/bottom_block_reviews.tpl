<div class="item_info">
	<div class="search_result">
		<?= $tabs?>
		<?if($items){?>
			<div class="last_reviews w100 js-next-reviews-holder">
				<h2><?=app()->metadata()->getTitle()?></h2>
				<a class="more_review fancybox fancybox.ajax" href="/firm-review/get-add-form/<?=$firm->id()?>/" rel="nofollow">Добавить отзыв</a>
				<?foreach ($items as $item){?>
				<div class="review">
						<?= app()->chunk()->setArgs([$item['score'], true])->render('rating.stars')?>
					<div class="main_review">
						<div class="name_date">
							<span class="name"><?=$item['user']?></span>, <span class="date"><?=$item['date']?>:</span>
						</div>
						<p><?=strip_tags($item['text'])?></p>
					</div>
					<?if($item['reply_text']){?>
					<div class="main_review reply">
						<div class="name_date">
							<span class="name"><?=$item['reply_user_name']?></span>, <span class="date"><?=$item['reply_date']?>:</span>
						</div>
						<p><?=strip_tags($item['reply_text'])?></p>
					</div>
					<?}?>
				</div>
				<?}?>
			</div>
		<?}?>
		<?if($has_next){?>
		<a style="text-align: center; margin: 0; padding: 0; float: left; margin-top: -15px;" class="more_review js-next-btn js-action" data-holder=".js-next-reviews-holder" data-url="<?=$firm->link()?>?mode=review&ajax=1" data-page="1" href="#">показать еще</a>
		<?}?>
	</div>	
</div>
<div class="attention-info attention-reviews">
        <div>Комментарии и отзывы посетителей сайта TovaruPlus.ru являются выражением их личного мнения. Администрация сайта TovaruPlus.ru: 
                <ul>
                        <li>не несет ответственности за отзывы и комментарии посетителей сайта TovaruPlus.ru</li>
                        <li>не несет обязанности проверять достоверность сведений об обстоятельствах, на которых основаны мнения посетителей сайта TovaruPlus.ru</li>
                        <li>не вправе ограничивать конституционные права посетителей сайта TovaruPlus.ru, в том числе свободу слова, а также право передачи и распространения информации, если реализация данных прав не противоречит закону.</li>
                </ul>
        </div>
</div>