<div class="button_set <?=$item['flag_is_referral']?>">
    <? if (($firm->canSell() && $item['price']) || $item['flag_is_referral']) { ?><?= app()->chunk()->set('firm', $firm)->set('item', $item)->render('common.price_buy_button') ?><? } ?>
    <? if ($firm->hasEmail() || $firm->hasCellPhone()) { ?><?= app()->chunk()->set('id', $id)->set('item', $item)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?><? } ?>
    <?/* if ($firm->hasCellPhone()) { ?><a class="cell_phone fancybox fancybox.ajax" href="/firm-feedback/get-callback-form/<?= $firm->id() ?>/" rel="nofollow">Перезвонить мне</a><? } */?>
</div>