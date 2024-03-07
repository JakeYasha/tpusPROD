<?if($items){?>
<div class="jcarousel-wrapper">
	<div class="pop_item_slider jcarousel js-jcarousel">
		<ul>
		<?$i=0;foreach ($items as $item) {$i++;?>
			<li class="leaf">
				<div class="image"><a href="<?=$item['link']?>"><?if($item['image']){?><img src="<?=$item['image']?>" alt="<?=encode($item['name'])?>" /><?} else {?><img src="/css/img/no_img.png" alt="" /><?}?></a></div>
				<span class="title"><a href="<?=$item['link']?>"><?=$item['name']?></a></span>
				<span class="price"><?=$item['price']?> <?=$item['currency']?></span>
			</li>
		<?}?>
		</ul>
	</div>
	<a href="#" class="jcarousel-control-prev"></a>
		<a href="#" class="jcarousel-control-next"></a>
</div>
<?}?>