<?=app()->chunk()->render('adv.top_banners')?>
<div class="search-result-content-wrapper">
	<div class="search-result-content display-table-type">
		<?$i=0;$count=count($items);foreach ($items as $id => $item) {$i++;?>
		<div class="search-result-element-block">
			<div class="element-image-block">
				<div class="image-wrapper<?if(!$item['image']){?> no-image<?}?>">
					<a rel="nofollow" href="<?=$item['link']?>"><?if($item['image']){?><img src="<?=$item['image_thumb']?>" alt="<?=encode($item['name'])?>" /><?}?></a>
				</div>
			</div>
			<div class="element-info-block for-product">
				<a href="<?=$item['link_tp']?>" class="element-name"><?=$item['name']?></a>
				<div class="product-price">
					<?if($item['old_price'] !== null){?>
					<span class="common-price have-discount"><?=$item['price']?> <?=$item['currency']?></span>
					<span class="old-price"><?=$item['old_price']?> <?=$item['currency']?></span>
					<?} else {?>
					<span class="common-price"><?=$item['price']?> <?=$item['currency']?></span>
					<?}?>
					<?if($item['unit']){?><span class="unit-type">цена за <?=$item['unit']?></span><?}?>
					<?if($item['price_wholesale'] && $item['price_wholesale'] !== $item['price']){?>
					<span class="unit-type">опт: <?=$item['price_wholesale']?> <?= $item['currency']?></span>
					<?}?>
				</div>
				<div class="element-description">
					<p><?=$item['is_yml'] ? $item['description_short_away'] : $item['description_short']?></p>
				</div>
				<? if(str()->length($item['vendor']) > 2) {?><span class="production-info"><span>Производитель:</span> <?=$item['vendor']?></span><?}
                elseif($item['production']){?><span class="production-info"><span>Производство:</span> <?=$item['production']?></span><?}?>
				<?=app()->chunk()->set('firm', $item['firm'])->set('item', $item)->set('id', $id)->render('common.button_set_price_small')?>
			</div>
		</div>
		<?if($i==ceil($count/2)){?><?=app()->chunk()->render('adv.middle_banners')?><?}?>
		<?}?>
	</div>
	<?=app()->chunk()->render('adv.side_banners')?>
</div>