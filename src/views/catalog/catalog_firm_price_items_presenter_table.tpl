<div class="search_result search_result_price2">
	<? /* @var $item['firm'] Firm */?>
	<? $i = 0;
	foreach ($items as $id=>$item) {
		$i++;?>
		<div class="search_result_cell price-list">
			<div class="image<? if (!$item['image']) {?> no-image<?}?>"><a rel="nofollow" href="<?= $item['link']?>"><? if ($item['image']) {?><img src="<?= $item['image_thumb']?>" alt="<?=encode($item['name'])?>" /><? }?></a></div>
			<div class="title"><a <?if($item['flag_is_referral']){?> rel="nofollow"<?}?> href="<?= $item['link']?>"><?= $item['name']?></a></div>
			<div class="price">
				<?if($item['price']){?><span class="price_field"><?= $item['price']?> <?= $item['currency']?></span><?}?>
				<?if($item['unit']){?><span class="under_price">цена за <?=$item['unit']?></span><?}?>
				<?if($item['firm']->hasEmail()){?><?=app()->chunk()->set('id', $id)->set('item', $item)->render('common.price_order_button')?><?}?>
			</div>
		</div>
<? }?>
</div>