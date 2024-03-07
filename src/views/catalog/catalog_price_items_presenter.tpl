<?/* @var $item['firm'] Firm*/?>
<?=app()->chunk()->render('adv.top_banners')?>

<?$i=0;$count=count($items);foreach ($items as $id => $item) {$i++;if($i==ceil($count/2)){?><?=app()->chunk()->render('adv.middle_banners')?><?}?>
<div class="search_result_cell border-bottom-block"><span class="number"><?=isset($item['number']) ? $item['number'] : $i?></span>
	<div class="price">
		<?if($item['price']){?><span class="price_field"><?=$item['price']?> <?=$item['currency']?></span><?}?>
		<?if($item['old_price'] !== null){?>
		<div class="old_price">
			<p><span><?=$item['old_price']?> <?=$item['currency']?></span></p>
		</div>
		<?}?>
		<?if($item['price_wholesale'] && $item['price_wholesale'] !== $item['price']){?>
		<span class="price_field under_price">опт: <?=$item['price_wholesale']?> <?= $item['currency']?></span>
		<?}?>
		<?if($item['unit']){?><span class="under_price unit">цена за <?=$item['unit']?></span><?}?>
		<?=app()->chunk()->set('firm', $item['firm'])->set('item', $item)->set('id', $id)->render('common.button_set_price_small')?>
	</div>
	<div class="image<?if(!$item['image']){?> no-image<?}?>" style="position: relative;"><a rel="nofollow" href="<?=$item['link']?>"><?if($item['image']){?><img src="<?=$item['image_thumb']?>" alt="<?=encode($item['name'])?>" /><?}?></a></div>
	<div class="title"><a href="<?=$item['link_tp']?>"><?=$item['name']?></a></div>
	<div class="description">
		<p><?=$item['description_short']?></p><?if($item['production']){?><p>Производство: <?=$item['production']?></p><?}?>
		<?=app()->chunk()->set('firm', $item['firm'])->render('firm.chunk_info')?>
	</div>	
</div>
<?}?>
<button class="show_more_results js-show-more">Показать еще результаты</button>