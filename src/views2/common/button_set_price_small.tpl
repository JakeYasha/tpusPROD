<a href="<?= ($firm->hasWebPartner() ? ('/page/away/firm/' . $firm->id() . '/') : $firm->link()) ?>" class="seller-type" title="Подробнее о фирме"><?= $firm->name() ?></a>
<?if ($firm->hasPhone()) {?>
<div class="firm-contacts real-contacts js-show-contacts-wrapper">
	<div class="firm-contacts-line">
		<span><?= $firm->renderPhoneLinks() ?></span>
	</div>
</div>
<?}?>
<div class="buttons-block <?= $item['flag_is_referral'] ?>">
    <? if (($firm->canSell() && $item['price']) || $item['flag_is_referral']) { ?>
        <?= app()->chunk()->set('firm', $firm)->set('item', $item)->render('common.price_buy_button') ?>
    <? } else { ?>
        <a href="#" onclick="return false;" class="btn-base btn-grey w130 disabled">В корзину</a>
    <? } ?>
    <? if ($firm->hasEmail() || $firm->hasCellPhone()) { ?>
        <?= app()->chunk()->set('id', $id)->set('item', $item)->set('request_disabled', false)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } else { ?>
        <?= app()->chunk()->set('id', $id)->set('item', $item)->set('request_disabled', true)->set('is_for_sms', !$firm->hasEmail())->render('common.price_order_button') ?>
    <? } ?>
    <? if ($item['is_yml']) { ?>
        <a rel="nofollow" target="_blank" href="<?= $item['link'] ?>" class="btn-base btn-grey to-firm turbo" title="Подробнее о предложении"></a>
    <? } else if ($firm->hasPhone()) {?>
        <a rel="nofollow" href="#firm-call-form-<?= $firm->id()?>" class="btn-base btn-grey to-firm call fancybox" data-object-id="<?=$firm->id()?>"></a>
    <? } else { ?>
        <a rel="nofollow" href="<?= $item['link'] ?>" class="btn-base btn-grey to-firm js-click-price" data-object-id="<?= $item['id'] ?>" title="Подробнее о предложении"></a>
    <? } ?>
    <?= app()->chunk()->setVar('heading', 'Телефоны')->setVar('firm', $firm)->render('forms.firm_call_static_form'); ?>
</div>