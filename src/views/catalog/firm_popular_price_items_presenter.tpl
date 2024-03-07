<?if($items){?>
<h2>Фотовитрина предложений фирмы:</h2>
<div class="jcarousel-wrapper">
	<div class="pop_item_slider jcarousel js-jcarousel">
		<ul>
		<?$i=0;foreach ($items as $item) {$i++;?>
			<li class="leaf">
				<div class="image"><a<?if($item['flag_is_referral']){?> rel="nofollow"<?}?> href="<?=$item['link']?>"><?if($item['image_thumb']){?><img src="<?=$item['image_thumb']?>" alt="<?=$item['name']?>" /><?} elseif($item['image']){?><img src="<?=$item['image']?>" alt="<?=encode($item['name'])?>" /><?} else {?><img src="/css/img/no_img.png" alt="" /><?}?></a></div>
				<span class="title"><a<?if($item['flag_is_referral']){?> rel="nofollow"<?}?> href="<?=$item['link']?>"><?=$item['name']?></a></span>
				<?if($item['price']){?><span class="price"><?=$item['price']?> <?=$item['currency']?></span><?}?>
			</li>
		<?}?>
		</ul>
	</div>
	<a href="#" class="jcarousel-control-prev"></a>
		<a href="#" class="jcarousel-control-next"></a>
</div>
<?}?>