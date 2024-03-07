<?if(!$item->isBlocked()){?>
<div class="item_info offset-none">
	<div class="search-result">
		<?= $tabs?>
		<? if ($items) { /* || $branch_names['firm_branches'])) {*/ ?>
			<div class="delimiter-line"></div>
            <div class="firm-bottom-block cat_description">
                <h2>Представительства и филиалы фирмы</h2>
                <div class="search-result-element-block firm-wrapper firm-branches">
                    <div class="element-info-block for-firm">
                        <a href="#" class="js-show-contacts btn-base btn-grey show-contacts" data-firm-id="<?=$item->id()?>"><span class="show-contacts-text">Все адреса</span></a>
                        <div class="firm-contacts real-contacts js-show-contacts-wrapper">
                            <?foreach ($items as $name => $firm_branches) {?>
                                <?  foreach ($firm_branches as $firm_branch) {?>
                                    <div class="delimiter-line"></div> 	
                                    <div class="firm-contacts-line firm_branch">
                                        <span class="contact-type">Адрес:</span>
                                        <span><?= $firm_branch->address()?></span>
                                    </div>
                                    <? if ($firm_branch->hasPhone()) {?>
                                        <div class="firm-contacts-line">
                                            <span class="contact-type">Телефон:</span>
                                            <span><?= $firm_branch->renderPhoneLinks()?></span>
                                        </div>
                                    <? }?>
                                    <? if ($item->hasModeWork()) { ?>
                                        <div class="firm-contacts-line">
                                            <span class="contact-type">Режим работы:</span>
                                            <span><?= $item->modeWork() ?></span>
                                        </div>
                                    <? } ?>
                                    <? if ($item->hasEmail()) { ?>
                                        <div class="firm-contacts-line">
                                            <span class="contact-type">Email:</span>
                                            <span><a class="fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/<?= $item->id_firm() ?>/<?= $item->id_service() ?>/" rel="nofollow"><?= $item->firstemail() ?></a></span>
                                        </div>
                                    <? } ?>
                                    <? if ($item->hasWeb()) { ?>
                                        <div class="firm-contacts-line site-address">
                                            <span class="contact-type">Сайт:</span>
                                            <span><?= $item->renderWebLinks() ?></span>
                                        </div>
                                    <? } ?>
                                    <? if ($item->hasMessengers() || $item->hasSocialNetworks()) { ?>
                                        <div class="firm-contacts-line site-messengers">
                                            <span class="contact-type">&nbsp;</span>
                                            <? if ($item->hasEmail()) { ?><?= $item->tEmail() ?><? } ?>
                                            <? if ($item->hasViber()) { ?><?= $item->viber() ?><? } ?>
                                            <? if ($item->hasWhatsApp()) { ?><?= $item->whatsapp() ?><? } ?>
                                            <? if ($item->hasSkype()) { ?><?= $item->skype() ?><? } ?>
                                            <? if ($item->hasTelegram()) { ?><?= $item->telegram() ?><? } ?>
                                            <? if ($item->hasVkontakte()) { ?><?= $item->vkontakte() ?><? } ?>
                                            <? if ($item->hasFacebook()) { ?><?= $item->facebook() ?><? } ?>
                                            <? if ($item->hasInstagram()) { ?><?= $item->instagram() ?><? } ?>
                                        </div>
                                    <? } ?>

                                <? }?>
                            <? }?>
                            <div class="search-result-element-block firm-wrapper" style="padding: 0;">
                                <div class="element-info-block for-firm" style="padding: 0;">
                                    <div class="buttons-block">
                                        <a class="btn-base btn-red" href="<?=$item->linkPricelist()?>" rel="nofollow">Посмотреть прайс-лист</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>		
            </div>		
		<?}?>
	</div>
</div>		


<?if(isset($firm_branches_on_map)) {?>
    <?=$firm_branches_on_map?>
    <div class="map_field">
        <div id="map"></div>
    </div>
<?}?>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>
<?} else {?>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>
<?}?>