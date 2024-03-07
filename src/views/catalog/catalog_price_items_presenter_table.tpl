<?=app()->chunk()->render('adv.top_banners')?>
<div class="search_result search_result_price2">
	<? /* @var $item['firm'] Firm */?>
	<? $i = 0;
	$count=count($items);foreach ($items as $id => $item) {$i++;if($i==ceil($count/2)){?><?=app()->chunk()->render('adv.middle_banners')?><?}$i++;?>
		<div class="search_result_cell">
			<div class="image<? if (!$item['image']) {?> no-image<?}?>"><a rel="nofollow" href="<?= $item['link']?>"><? if ($item['image']) {?><img src="<?= $item['image']?>" alt="<?= encode($item['name'])?>" /><? }?></a></div>
			<div class="title"><a href="<?= $item['link']?>"><?= $item['name']?></a></div>
			<div class="price">
				<?if($item['price']){?><span class="price_field"><?= $item['price']?> <?= $item['currency']?></span><?}?>
				<?if($item['old_price'] !== null){?>
				<div class="old_price" style="margin: -5px 0 5px 0;">
					<p><span><?=$item['old_price']?> <?=$item['currency']?></span></p>
				</div>
				<?}?>
				<?if($item['unit']){?><span class="under_price">цена за <?=$item['unit']?></span><?}?>
				<?if($item['firm']->hasEmail()){?><?=app()->chunk()->set('id', $id)->set('item', $item)->render('common.price_order_button')?><?}?>
			</div>
		</div>
<? }?>
</div>