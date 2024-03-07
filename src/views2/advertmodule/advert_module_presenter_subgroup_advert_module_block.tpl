<? /* if($items && app()->location()->currentId() != '76004'){ */?>
<? if ($items) {?>
	<div class="item_info">
		<div class="search_result">
			<!--noindex-->
			<div class="search_result_top red">
				<h2>Горячие предложения, акции и скидки</h2>
				<a rel="nofollow" href="<?= app()->link('/advert-module/')?>">Все скидки и акции</a>
			</div>
			<ul class="hp_coupon_list clearfix">

				<? foreach ($items as $k => $advert_module) {?>
					<li class="hp_coupon_item">
						<div class="hp_coupon">
							<div class="hp_coupon_header">
								<p class="hp_coupon_duration">c <?= CDateTime::gmt("d.m.Y", $advert_module['time_beginning_short'])?> по <?= CDateTime::gmt("d.m.Y", $advert_module['time_ending_short'])?></p>
								<p class="hp_coupon_title"><?= $advert_module['header']?></p>
							</div>
							<div class="hp_coupon_image">
								<? if (isset($advert_module['image']) && isset($advert_module['full_image'])) {?>
									<a class="fancybox" href="<?= $advert_module['full_image']?>">
										<img style="width: auto" src="<?= $advert_module['image']?>" alt="">
									</a>
								<? } else if (isset($advert_module['image'])) {?>
									<img style="width: auto" src="<?= $advert_module['image']?>" alt="">
								<? } else {?>
								<? }?>
							</div>
							<div class="hp_coupon_footer">
								<p><?= $advert_module['text']?><br/><span style="font-size: 11px; color: #A96060;"><?= $advert_module['about_string']?></span></p>
								<? if ($advert_module['url']) {?><a href="/advert-module/item/<?=$advert_module['id']?>/" class="hp_company_site" target="_blank">Сайт компании</a><? }?>
							</div>
						</div>
					</li>
				<? }?>
			</ul>
			<!--/noindex-->		
		</div>
	</div>
<?
}?>