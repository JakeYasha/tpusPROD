<?= $bread_crumbs ?>
<?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
?>
<? if (!$item->isBlocked()) { ?>
    <div class="search-result offset-none">
        <div class="search-result-element-block firm-wrapper">
            <div class="element-image-block">
                <div class="image-wrapper">
                    <a href="<?= $item->link() ?>"><img<? if (!$item->hasLogo()) { ?> class="no-image"<? } ?> src="<?= $item->logoPath() ?>" alt="<?= str()->replace($item->name(), ['"'], ['&quot;']) ?>, <?= str()->replace($item->address(), ['"'], ['&quot;']) ?>"></a>
                </div>
            </div>
            <div class="element-info-block for-firm">
                <h1 class="element-name<? if ($item->isBlocked()) { ?> gray<? } else { ?> blue <? } ?>"><?= $item->name() ?></h1>
                <? /* if (!isset($show_rating) || $show_rating) {?><?= app()->chunk()->setArg($item)->render('rating.stars')?><? } else {?><?= app()->chunk()->setArg($item)->render('rating.only_button')?><?} */ ?>
                <?= app()->chunk()->setArg($item)->render('rating.only_button') ?>
                <? if ($item->hasActivity()) { ?><div class="element-description"><p><?= $item->activity() ?></p></div><? } ?>
                <? if (!$short_full_contacts) { ?><a href="#" class="js-show-contacts btn-base btn-grey show-contacts" data-firm-id="<?= $item->id() ?>"><span class="show-contacts-text">Показать контакты</span></a><? } ?>
                <div class="firm-contacts<? if (!$short_full_contacts) { ?> real-contacts js-show-contacts-wrapper<? } ?>">
                    <? if ($item->hasPhone()) { ?>
                        <div class="firm-contacts-line">
                            <span class="contact-type">Телефон:</span>
                            <span><?= $item->renderPhoneLinks() ?></span>
                        </div>
                    <? } ?>
                    <div class="firm-contacts-line">
                        <span class="contact-type">Адрес:</span>
                        <span><?= $item->address() ?></span>
                    </div>
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
                            <? /*if ($item->hasEmail()) { ?><?= $item->tEmail() ?><? }*/ ?>
                            <? if ($item->hasViber()) { ?><?= $item->viber() ?><? } ?>
                            <? if ($item->hasWhatsApp()) { ?><?= $item->whatsapp() ?><? } ?>
                            <? if ($item->hasSkype()) { ?><?= $item->skype() ?><? } ?>
                            <? if ($item->hasTelegram()) { ?><?= $item->telegram() ?><? } ?>
                            <? if ($item->hasVkontakte()) { ?><?= $item->vkontakte() ?><? } ?>
                            <?// if ($item->hasFacebook()) { ?><?//= $item->facebook() ?><?// } ?>
                            <? //if ($item->hasInstagram()) { ?><?//= $item->instagram() ?><?// } ?>
                        </div>
                    <? } ?>
                </div>
				<div class="buttons-block">
					<? if ($item->hasEmail()) {?><a class="btn-base btn-grey fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/<?= $item->id()?>/" rel="nofollow">Оставить сообщение</a><? } else {?><a class="btn-base btn-grey disabled" href="#" onclick="return false;" rel="nofollow">Оставить сообщение</a><? }?>
					<? if ($item->hasCellPhone() && $item->id_service() == 10) {?><a class="btn-base btn-grey fancybox fancybox.ajax" href="/firm-feedback/get-callback-form/<?= $item->id()?>/" rel="nofollow">Заказать звонок</a><? } else /*elseif ($item->id_service() == 10)*/ {?><a class="btn-base btn-grey disabled" href="#" onclick="return false;" rel="nofollow">Заказать звонок</a><? }?>
				</div>

            </div>
        </div>
    </div>
    <a class="js-firm-bottom-block" id="firm-bottom-block" style="font-size: 0"></a>
    <?= app()->chunk()->setArg($item)->render('adv.firm_top_banner') ?>
    <?= $bottom_block ?>
	<?= app()->chunk()->render('adv.bottom_banners')?>
<? } ?>
<?=
app()->adv()->renderRestrictions()?>
<?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
?>