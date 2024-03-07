<div class="brand-list__item--service">
    <div class="brand-list__item--content">
        <a class="brand-list__item--link" href="<?= ($firm->hasWebPartner() ? ('/page/away/firm/' . $firm->id() . '/') : $firm->link()) ?>"><?=$firm->name()?></a>
    </div>
</div>

<?if ($firm->hasPhone()) {?>
    <div class="brand-list__item--service">
        <div class="brand-list__item--content">
            <?= $firm->renderPhoneLinks() ?>
        </div>
    </div>
<?}?>
<div class="brand-list__actions brand-list__actions_desktop">
    <? if (($firm->canSell() && $item['price']) || $item['flag_is_referral']) { ?>
        <?= app()->chunk()->set('firm', $firm)->set('item', $item)->render('common.price_buy_button') ?>
    <? } else { ?>
        <a href="#" onclick="return false;" class="btn brand-list__action btn_outline btn_outline--secondary disabled">В корзину</a>
    <? } ?>
    <? if ($firm->hasEmail() || $firm->hasCellPhone()) { ?>
        <?= app()->chunk()->set('id', $id)->set('item', $item)->set('request_disabled', false)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } else { ?>
        <?= app()->chunk()->set('id', $id)->set('item', $item)->set('request_disabled', true)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } ?>
</div>

<div class="brand-list__actions brand-list__actions_mobile">
    <? if (($firm->canSell() && $item['price']) || $item['flag_is_referral']) { ?>
        <?= app()->chunk()->set('firm', $firm)->set('item', $item)->render('common.price_buy_button') ?>
    <? } else { ?>
        <a href="#" onclick="return false;" class="btn brand-list__action btn_outline btn_outline--secondary disabled">В корзину</a>
    <? } ?>
    <? if ($firm->hasEmail() || $firm->hasCellPhone()) { ?>
        <?= app()->chunk()->set('id', $id)->set('item', $item)->set('request_disabled', false)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } else { ?>
        <?= app()->chunk()->set('id', $id)->set('item', $item)->set('request_disabled', true)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } ?>
    <? if ($item['is_yml']) { ?>
        <a rel="nofollow" target="_blank" href="<?= $item['link'] ?>" class="btn brand-list__action btn_outline btn_icon btn_outline--secondary"><span class="phone-icon"></span></a>
    <? } else if ($firm->hasPhone()) {?>
        <a rel="nofollow" href="#" class="btn brand-list__action btn_outline btn_icon btn_outline--primary js-open-modal" data-target="callForm<?=$firm->id()?>"><span class="phone-icon"></span></a>
    <? } else { ?>
        <a rel="nofollow" href="<?= $item['link'] ?>" class="btn brand-list__action btn_outline btn_icon btn_outline--primary" data-object-id="<?= $item['id'] ?>"><span class="phone-icon"></span></a>
    <? } ?>
    <?= app()->chunk()->setVar('heading', 'Телефоны')->setVar('firm', $firm)->render('forms.firm_call_static_form'); ?>
</div>