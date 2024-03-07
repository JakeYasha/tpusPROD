
<div class="brand-list__actions brand-list__actions_desktop"<? if (isset($style)) { ?> style="<?= $style ?>"<? } ?>>
    <? if ($firm->hasEmail() || $firm->hasCellPhone() || $item['flag_is_referral']) { ?>
        <?= app()->chunk()->set('class', 'get-price')->set('id', $id)->set('item', $item)->set('big_button', true)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } ?>
    <? if ($firm->hasCellPhone()) { ?>
        <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="callbackForm" data-url="/firm-feedback/get-callback-form/<?= $firm->id() ?>/" rel="nofollow">Заказать звонок</a>
    <? } else {?>
        <a class="btn brand-list__action btn_outline btn_outline--secondary" href="#" onclick="return false;" rel="nofollow">Заказать звонок</a>
    <? }?>
    <? if (($firm->hasPhone() && !$firm->hasEmail() && !$firm->hasCellPhone()) && !$item['flag_is_referral']) { ?>
        <div class="attention-info contacts"><div>Заказ и информация только по телефону:<br><p><span class="r tel"><?= $firm->phone() ?></span></p></div></div>
    <? } ?>
</div>