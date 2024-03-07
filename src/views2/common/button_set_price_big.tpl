
<div class="buttons-block"<? if (isset($style)) { ?> style="<?= $style ?>"<? } ?>>
    <? if ($firm->hasEmail() || $firm->hasCellPhone() || $item['flag_is_referral']) { ?>
        <?= app()->chunk()->set('class', 'get-price')->set('id', $id)->set('item', $item)->set('big_button', true)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } ?>
    <? if ($firm->hasCellPhone()) { ?>
        <a class="btn-base btn-red fancybox fancybox.ajax" href="/firm-feedback/get-callback-form/<?= $firm->id() ?>/" rel="nofollow">Заказ звонка</a>
    <? } ?>
    <? if (($firm->hasPhone() && !$firm->hasEmail() && !$firm->hasCellPhone()) && !$item['flag_is_referral']) { ?>
        <div class="attention-info contacts"><div>Заказ и информация только по телефону:<br><p><span class="r tel"><?= $firm->phone() ?></span></p></div></div>
    <? } ?>
</div>