<?if(!$item->isBlocked()){?>
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-12">
            <?= $tabs?>
            <? if ($items) { /* || $branch_names['firm_branches'])) {*/ ?>
                <h2>Представительства и филиалы фирмы</h2>
                <a href="#" class="js-show-contacts btn-base btn-grey show-contacts" data-firm-id="<?=$item->id()?>"><span class="show-contacts-text">Все адреса</span></a>
                <?foreach ($items as $name => $firm_branches) {?>
                    <?  foreach ($firm_branches as $firm_branch) {?>
                        <div class="brand-list__item--service">
                            <div class="brand-list__item--info">Адрес:</div>
                            <div class="brand-list__item--content"><?= $firm_branch->address() ?></div>
                        </div>
                        <? if ($firm_branch->hasPhone()) { ?>
                            <div class="brand-list__item--service">
                                <div class="brand-list__item--info">Телефон:</div>
                                <div class="brand-list__item--content"><?= $firm_branch->renderPhoneLinks() ?></div>
                            </div>
                        <? } ?>
                        <? if ($firm_branch->hasModeWork()) { ?>
                            <div class="brand-list__item--service">
                                <div class="brand-list__item--info">Режим работы:</div>
                                <div class="brand-list__item--content"><?= $firm_branch->modeWork() ?></div>
                            </div>
                        <? } ?>
                        <? if ($firm_branch->hasEmail()) { ?>
                            <div class="brand-list__item--service">
                                <div class="brand-list__item--info">Email:</div>
                                <div class="brand-list__item--content">
                                    <a class="brand-list__item--link" href="/firm-feedback/get-feedback-form/<?= $firm_branch->id_firm() ?>/<?= $firm_branch->id_service() ?>/" rel="nofollow"><?= $firm_branch->firstemail() ?></a>
                                </div>
                            </div>
                        <? } ?>
                        <? if ($firm_branch->hasWeb()) { ?>
                            <div class="brand-list__item--service">
                                <div class="brand-list__item--info">Сайт:</div>
                                <div class="brand-list__item--content"><?= $firm_branch->renderWebLinks() ?></div>
                            </div>
                        <? } ?>
                        <? if ($firm_branch->hasMessengers() || $firm_branch->hasSocialNetworks()) { ?>
                            <div class="brand-list__item--service">
                                <div class="brand-list__item--info">&nbsp;</div>
                                <? /*if ($firm_branch->hasEmail()) { ?><?= $firm_branch->tEmail() ?><? }*/ ?>
                                <? if ($firm_branch->hasViber()) { ?><?= $firm_branch->viber() ?><? } ?>
                                <? if ($firm_branch->hasWhatsApp()) { ?><?= $firm_branch->whatsapp() ?><? } ?>
                                <? if ($firm_branch->hasSkype()) { ?><?= $firm_branch->skype() ?><? } ?>
                                <? if ($firm_branch->hasTelegram()) { ?><?= $firm_branch->telegram() ?><? } ?>
                                <? if ($firm_branch->hasVkontakte()) { ?><?= $firm_branch->vkontakte() ?><? } ?>
                                <?// if ($firm_branch->hasFacebook()) { ?><?//= $firm_branch->facebook() ?><?// } ?>
                                <?// if ($firm_branch->hasInstagram()) { ?><?//= $firm_branch->instagram() ?><?// } ?>
                            </div>
                        <? } ?>
                        <div class="divider"></div>
                    <?}?>
                <?}?>
                <div class="search-result-element-block firm-wrapper" style="padding: 0;">
                    <div class="element-info-block for-firm" style="padding: 0;">
                        <div class="buttons-block">
                            <a class="btn brand-list__action btn_outline btn_outline--primary" href="<?=$item->linkPricelist()?>" rel="nofollow">Посмотреть прайс-лист</a>
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
    <br/>
    <div class="pre_footer_adv_block">
        <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
<?} else {?>
    <br/>
    <div class="pre_footer_adv_block">
        <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
<?}?>