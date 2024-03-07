<? /* if($items && app()->location()->currentId() != '76004'){ */ ?>
<? if ($items) { ?>
    <div class="hp_coupon_list_container">
        <h2>Спецпредложения, скидки и акции <?= app()->location()->currentName('prepositional') ?></h2>
        <ul class="hp_coupon_large_list clearfix">
            <? 
            $i = 0;
            $visible = true;
            foreach ($items as $k => $advert_module) { 
                $i++;
                ?>
                <li class="hp_coupon_large_item <?=$visible ? '' : 'hidden-coupon'?>">
                    <div class="hp_coupon_large">
                        <div class="hp_coupon_large_image">
                            <? if ($advert_module->hasMoreUrl()) { ?>
                                <? if ($advert_module->hasFullImage()) { ?>
                                    <a rel="nofollow" target="_blank" href="/advert-module/url/<?= $advert_module->id() ?>/" class="hp_company_site">
                                        <img src="<?= $advert_module->getFullImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                                    </a>
                                <? } else { ?>
                                    <a rel="nofollow" target="_blank" href="/advert-module/url/<?= $advert_module->id() ?>/" class="hp_company_site">
                                        <img src="<?= $advert_module->getImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                                    </a>
                                <? } ?>
                            <? } else if ($advert_module->hasUrl()) { ?>
                                <? if ($advert_module->hasFullImage()) { ?>
                                    <a rel="nofollow" target="_blank" href="/advert-module/item/<?= $advert_module->id() ?>/" class="hp_company_site">
                                        <img src="<?= $advert_module->getFullImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                                    </a>
                                <? } else { ?>
                                    <a rel="nofollow" target="_blank" href="/advert-module/item/<?= $advert_module->id() ?>/" class="hp_company_site">
                                        <img src="<?= $advert_module->getImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                                    </a>
                                <? } ?>
                            <? } else { ?>
                                <? if ($advert_module->hasFullImage()) { ?>
                                    <a rel="nofollow" href="#" onclick="return false;">
                                        <img src="<?= $advert_module->getFullImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                                    </a>
                                <? } else { ?>
                                    <a rel="nofollow" href="#" onclick="return false;">
                                        <img src="<?= $advert_module->getImage()->link() ?>" alt="<?= $advert_module->val('header') ?>">
                                    </a>
                                <? } ?>
                            <? } ?>
                        </div>
                        <? /*if (APP_IS_DEV_MODE && $advert_module->restrictions[0] != '') { ?>
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
                                 "><?= $advert_module->restrictions[0] ?></div>
                             <? }*/ ?>
                        <div class="hp_coupon_large_content">
                            <div class="hp_coupon_large_header">
                                <div class="hp_coupon_large_duration"><span class="clock_spr"> </span><? if ($advert_module->val('flag_is_infinite')) { ?>Постоянная<? } else { ?>c <?= date("d.m.Y", CDateTime::toTimestamp($advert_module->val('timestamp_beginning'))) ?> по <?= date("d.m.Y", CDateTime::toTimestamp($advert_module->val('timestamp_ending'))) ?><? } ?></div>
                                <div class="hp_coupon_large_title"><?= $advert_module->val('header') ?></div>
                            </div>
                            <div class="hp_coupon_large_text<?=(!$advert_module->hasUrl() && !$advert_module->hasMoreUrl())?' collapsed':''?>">
								<p><?= $advert_module->val('adv_text')?></p>
							</div>
                        </div>
                    </div>
                </li>
                <? if ($i % 12 == 0 && $i < count($items) ) { ?>
                    <div class="hp_coupon_delimiter <?=$visible ? '' : 'hidden-coupon'?>" onclick="$('.hp_coupon_large_item.hidden-coupon:lt(12)').removeClass('hidden-coupon'); $('.hp_coupon_delimiter').eq(0).remove(); $('.hp_coupon_delimiter').eq(0).removeClass('hidden-coupon');">Показать ещё</div>
                    <? $visible = false; ?>
                <? } ?>
            <? } ?>
        </ul>

    </div>

    <?
}?>