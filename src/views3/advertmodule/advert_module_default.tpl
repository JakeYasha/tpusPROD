<div class="search_result">
	<div class="search_adv_block">
		<span class="title">РЕКЛАМА</span>
		<div class="search_adv_block_container">
			<div class="adv_module_block">
				<div class="image">
					<? if (isset($item)) {?>
						<? if ($item->hasFullImage()) {?>
							<a class="fancybox" href="<?= $item->getFullImage()->link()?>">
								<img alt="" src="<?= $item->getImage()->link()?>">
							</a>
						<? } else {?>
							<img alt="" src="<?= $item->getImage()->link()?>">
						<? }?>
					<? } else {?>
						<img alt="" src="#">
					<? }?>
				</div>
				<div class="adv-text">
					<div class="adv_module_title">
						<? if ($item->hasUrl()) {?><a rel="nofollow" href="/advert-module/item/<?=$item->id()?>/" target="_blank"><?= $item->val('header')?></a><? } else {?><?= $item->val('header')?><? }?>
					</div>
					<div class="adv_module_description">
						<p><?= $item->val('adv_text')?></p><span></span><? if ($item->hasUrl()) {?><p><a href="/advert-module/item/<?=$item->id()?>/">перейти на сайт</a></p><? }?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>