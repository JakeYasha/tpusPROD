<div class="mdc-layout-grid">
    <?= $bread_crumbs ?>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <? if (!$item->isBlocked()) { ?>
        <div class="mdc-layout-grid__inner" style="margin-top: 2rem">
            <div class="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone" <? if (!$item->hasLogo()) {?>itemscope itemtype="http://schema.org/ImageObject"<?}?>>
                <? if (!$item->hasLogo()) {?>
                    <h2 class="firm-dop-text" itemprop="name"><?=$item->name();?></h2>    
                <?}?>
                <a href="<?= $item->link() ?>"><img<? if (!$item->hasLogo()) { ?> class="tp-firm-logo no-image img-fluid"<? } else { ?> class="tp-firm-logo img-fluid" <? } ?> src="<?= $item->logoPath() ?>" itemprop="contentUrl" alt="<?= str()->replace($item->name(), ['"'], ['&quot;']) ?>, <?= str()->replace($item->address(), ['"'], ['&quot;']) ?>"  ></a>
                <? if (!$item->hasLogo()) {?>
                    <span class="firm-dop-text" itemprop="description"><?= $item->activity() ?></span>  
                <?}?>
                
            </div>
            <div class="mdc-layout-grid__cell--span-10-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                <h1 class="brand-list__item--heading <? if ($item->isBlocked()) { ?> gray<? } else { ?> blue <? } ?>"><?= $item->name() ?></h1>
                <? if ($item->hasActivity()) { ?><p class="brand-list__item--text"><?= $item->activity() ?></p><? } ?>
                <? if ($item->hasPhone()) { ?>
                    <div class="brand-list__item--service">
                        <div class="brand-list__item--info">Телефон:</div>
                        <div class="brand-list__item--content"><?= $item->renderPhoneLinks() ?></div>
                    </div>
                <? } ?>
                <div class="brand-list__item--service">
                    <div class="brand-list__item--info">Адрес:</div>
                    <div class="brand-list__item--content"><?= $item->address() ?></div>
                </div>
                <? if ($item->hasModeWork()) { ?>
                    <div class="brand-list__item--service">
                        <div class="brand-list__item--info">Режим работы:</div>
                        <div class="brand-list__item--content"><?= $item->modeWork() ?></div>
                    </div>
                <? } ?>

                <? if ($item->hasEmail()) { ?>
                    <div class="brand-list__item--service">
                        <div class="brand-list__item--info">Email:</div>
                        <div class="brand-list__item--content">
                            <a class="brand-list__item--link" href="/firm-feedback/get-feedback-form/<?= $item->id_firm() ?>/<?= $item->id_service() ?>/" rel="nofollow"><?= $item->firstemail() ?></a>
                        </div>
                    </div>
                <? } ?>
                <? if ($item->hasWeb()) { ?>
                    <div class="brand-list__item--service">
                        <div class="brand-list__item--info">Сайт:</div>
                        <div class="brand-list__item--content"><?= $item->renderWebLinks() ?></div>
                    </div>
                <? } ?>
                <? if ($item->hasMessengers() || $item->hasSocialNetworks()) { ?>
                    <div class="brand-list__item--service">
                        <div class="brand-list__item--info">&nbsp;</div>
                        <? /*if ($item->hasEmail()) { ?><?= $item->tEmail() ?><? }*/ ?>
                        <? if ($item->hasViber()) { ?><?= $item->viber() ?><? } ?>
                        <? if ($item->hasWhatsApp()) { ?><?= $item->whatsapp() ?><? } ?>
                        <? if ($item->hasSkype()) { ?><?= $item->skype() ?><? } ?>
                        <? if ($item->hasTelegram()) { ?><?= $item->telegram() ?><? } ?>
                        <? if ($item->hasVkontakte()) { ?><?= $item->vkontakte() ?><? } ?>
                        <? if ($item->hasFacebook()) { ?><?= $item->facebook() ?><? } ?>
                        <?// if ($item->hasInstagram()) { ?><?//= $item->instagram() ?><?// } ?>
                    </div>
                <? } ?>
                <div class="brand-list__actions brand-list__actions_desktop">
                    <? if ($item->hasEmail()) {?>
                        <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="feedbackForm" data-url="/firm-feedback/get-feedback-form/<?= $item->id()?>/" rel="nofollow">Отправить сообщение</a>
                    <? } else {?>
                        <a class="btn brand-list__action btn_outline btn_outline--secondary" href="#" onclick="return false;" rel="nofollow">Оставить сообщение</a>
                    <? }?>
                    <a rel="nofollow" class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="reviewForm" data-url="/firm-review/get-add-form/<?=$item->id()?>/">Добавить отзыв</a>
                    
                    <? if ($item->hasCellPhone() && $item->id_service() == 10) {?>
                        <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="callbackForm" data-url="/firm-feedback/get-callback-form/<?= $item->id()?>/" rel="nofollow">Заказать звонок</a>
                    <? } else /*elseif ($item->id_service() == 10)*/ {?>
                        <a class="btn brand-list__action btn_outline btn_outline--secondary" href="#" onclick="return false;" rel="nofollow">Заказать звонок</a>
                    <? }?>
                </div>
                <div class="brand-list__actions brand-list__actions_mobile">
                    <? if ($item->hasEmail()) {?>
                        <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="feedbackForm" data-url="/firm-feedback/get-feedback-form/<?= $item->id()?>/" rel="nofollow">Отправить сообщение</a>
                    <? } else {?>
                        <a class="btn brand-list__action btn_outline btn_outline--secondary" href="#" onclick="return false;" rel="nofollow">Оставить сообщение</a>
                    <? }?>

                    <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="reviewForm" data-url="/firm-review/get-add-form/<?=$item->id()?>/">Добавить отзыв</a>
                    
                    <? if ($item->hasCellPhone() && $item->id_service() == 10) {?>
                        <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="callbackForm" data-url="/firm-feedback/get-callback-form/<?= $item->id()?>/" rel="nofollow">Заказать звонок</a>
                    <? } else /*elseif ($item->id_service() == 10)*/ {?>
                        <a class="btn brand-list__action btn_outline btn_outline--secondary" href="#" onclick="return false;" rel="nofollow">Заказать звонок</a>
                    <? }?>
                </div>
            </div>
        </div>

        <?/*= app()->chunk()->setArg($item)->render('rating.only_button') */?>



        <div class="divider"></div>
        
        <?= $bottom_block ?>

        <?= app()->chunk()->render('adv.bottom_banners')?>
    <? } ?>
    <?= app()->adv()->renderRestrictions()?>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
</div>