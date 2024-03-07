<div class="search_result_price">
<?/* @var $item['firm'] Firm*/?>
<div class="search_result_content">
	<?$i=0;foreach ($items as $id=>$item) {$i++;?>
	<div class="border-bottom-block">
	<div class="search_result_cell price-list">
		<div class="price">
			<?if($item['price']){?><span class="price_field"><?=$item['price']?> <?=$item['currency']?></span><?}?>
			<?if($item['old_price'] !== null){?>
			<div class="old_price">
				<p><span style="display: inline;"><?=$item['old_price']?> <?=$item['currency']?></span></p>
			</div>
			<?}?>
			<span class="under_price"><?if($item['unit']){?>цена за <?=$item['unit']?><?}?></span>
			<?=app()->chunk()->set('firm', $item['firm'])->set('item', $item)->set('id', $id)->render('common.button_set_price_small')?>
		</div>
		<div class="image<?if(!$item['image']){?> no-image<?}?>" style="position: relative;"><a rel="nofollow" href="<?=$item['link']?>"><?if($item['image']){?><img src="<?=$item['image_thumb']?>" alt="<?=encode($item['name'])?>" /><?}?></a></div>
		<div class="title"><a <?if($item['flag_is_referral']){?> rel="nofollow"<?}?> href="<?=$item['link']?>"><?=$item['name']?></a></div>
		<div class="description">
			<p><?=$item['description_short_away']?></p><?if($item['production']){?><p>Производство: <?=$item['production']?></p><?}?>
		</div>
	</div>
	</div>
<?}?>
</div>
<button class="show_more_results js-show-more">Показать еще результаты</button>
</div>