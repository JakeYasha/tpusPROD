<? /* if($items && app()->location()->currentId() != '76004'){ */?>
<? if ($items) {?>
	<div class="hp_coupon_list_container">

		<ul class="hp_coupon_large_list clearfix">
			<? foreach ($items as $k => $advert_module) {?>
				<li class="hp_coupon_large_item">
					<div class="hp_coupon_large">
						<div class="hp_coupon_large_image">
							<? if ($advert_module->hasMoreUrl()) {?>
								<? if ($advert_module->hasFullImage()) {?>
									<a rel="nofollow" target="_blank" href="/advert-module/url/<?=$advert_module->id()?>/" class="hp_company_site">
										<img src="<?= $advert_module->getFullImage()->link()?>" alt="<?= $advert_module->val('header')?>">
									</a>
								<? } else {?>
									<a rel="nofollow" target="_blank" href="/advert-module/url/<?=$advert_module->id()?>/" class="hp_company_site">
										<img src="<?= $advert_module->getImage()->link()?>" alt="<?= $advert_module->val('header')?>">
									</a>
								<? }?>
							<? } else if ($advert_module->hasUrl()) {?>
								<? if ($advert_module->hasFullImage()) {?>
									<a rel="nofollow" target="_blank" href="/advert-module/item/<?=$advert_module->id()?>/" class="hp_company_site">
										<img src="<?= $advert_module->getFullImage()->link()?>" alt="<?= $advert_module->val('header')?>">
									</a>
								<? } else {?>
									<a rel="nofollow" target="_blank" href="/advert-module/item/<?=$advert_module->id()?>/" class="hp_company_site">
										<img src="<?= $advert_module->getImage()->link()?>" alt="<?= $advert_module->val('header')?>">
									</a>
								<? }?>
							<? } else {?>
								<? if ($advert_module->hasFullImage()) {?>
                                    <a rel="nofollow" href="#" onclick="return false;">
                                        <img src="<?= $advert_module->getFullImage()->link()?>" alt="<?= $advert_module->val('header')?>">
                                    </a>
								<? } else {?>
                                    <a rel="nofollow" href="#" onclick="return false;">
                                        <img src="<?= $advert_module->getImage()->link()?>" alt="<?= $advert_module->val('header')?>">
                                    </a>
								<? }?>
							<? }?>
						</div>
                                                <?if (APP_IS_DEV_MODE && $advert_module->restrictions[0] != '') {?>
                                                        <div class="hp_coupon_large_restriction" style="
                                                            background-color: #000;
                                                            opacity: 0.5;
                                                            color: #fff;
                                                            line-height: 10px;
                                                            text-transform: uppercase;
                                                            text-align: center;
                                                            font-size: 10px;
                                                            position: relative;
                                                            padding: 10px 5px;
                                                        "><?=$advert_module->restrictions[0]?></div>
                                                <?}?>
						<div class="hp_coupon_large_content">
							<div class="hp_coupon_large_header">
                                                                <div class="hp_coupon_large_duration"><span class="clock_spr"> </span><?if ($advert_module->val('flag_is_infinite')) {?>Постоянная<?} else {?>c <?= date("d.m.Y", CDateTime::toTimestamp($advert_module->val('timestamp_beginning')))?> по <?= date("d.m.Y", CDateTime::toTimestamp($advert_module->val('timestamp_ending')))?><?}?></div>
								<div class="hp_coupon_large_title"><?= $advert_module->val('header')?></div>
							</div>
							<div class="hp_coupon_large_text<?=(!$advert_module->hasUrl() && !$advert_module->hasMoreUrl())?' collapsed':''?>">
								<p><?= $advert_module->val('adv_text')?></p>
                                                                <?if($advert_module->hasPhones()){?><div class="hp_coupon_large_phones"><span class="phone_spr"> </span><?= $advert_module->val('phones')?></div><?}?>
                                                                <?if($advert_module->val('about_string')){?><div class="hp_coupon_large_about"><? $about_strings = explode("\n",$advert_module->val('about_string')); $j = 0; foreach($about_strings as $about_string) { $j++;?><span class="geo_spr"> </span><?=$about_string?><?=(count($about_strings) != $j ? '<br/><br/>' : '')?><?}?></div><?}?>
							</div>
							<div class="hp_coupon_large_controls">
								<div class="for_client_tabs_button_set">
									<? if ($advert_module->hasMoreUrl()) {?>
										<a class="button button-order" rel="nofollow" target="_blank" href="/advert-module/url/<?=$advert_module->id()?>/" style="width: <?=$advert_module->val('target_btn_name') == 'more' ? '90px' : '140px'?>;">
											<?= $advert_module->val('target_btn_name') == 'more' ? 'Подробнее' : ($advert_module->val('target_btn_name') == 'onlineshop' ? 'В интернет-магазин' : 'Получить промокод')?>
										</a>
									<? }?>

									<? if ($advert_module->hasEmail() || $advert_module->hasPhone()) {?>
										<a class="button button-order fancybox fancybox.ajax" rel="nofollow" target="_blank" href="/advert-module/get-request-form/?id_advert_module=<?= $advert_module->id()?>" style="width: 90px;">
											<?= $advert_module->val('callback_btn_name') == 'order' ? 'Заказать' : 'Записаться'?>
										</a>
									<? }?>
								</div>
							</div>
						</div>
					</div>
				</li>
			<? }?>
		</ul>

	</div>

<?
}?>