<? if ($item['is_yml']) { ?>
    <a class="btn brand-list__action btn_outline btn_outline--primary" href="<?= $item['link'] ?>" rel="nofollow" target="_blank">В магазин</a>
    <? /* <a class="btn-base btn-red w130" href="<?=$item['link']?>" rel="nofollow" target="_blank"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить'))?></a> */ ?>
<? } elseif (!$item['is_yml']) { ?>
    <? if (isset($request_disabled)) { ?>
        <? if ($request_disabled) { ?>
            <a class="btn brand-list__action btn_outline btn_outline--secondary disabled" href="#" onclick="return false;" rel="nofollow"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить')) ?></a>
        <? } else { ?>
            <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="requestForm" data-url="/price/get-request-form/?id_price=<?= $id ?>&amp;type=<?= $item['price'] ? 'order' : 'check' ?><? if (isset($is_for_sms) && $is_for_sms) { ?>&sms_mode=1<? } ?>" rel="nofollow"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить')) ?></a>
        <? } ?>
    <? } else { ?>
        <a class="btn brand-list__action btn_outline btn_outline--primary js-open-modal-ajax" href="#" data-target="requestForm" data-url="/price/get-request-form/?id_price=<?= $id ?>&amp;type=<?= $item['price'] ? 'order' : 'check' ?><? if (isset($is_for_sms) && $is_for_sms) { ?>&sms_mode=1<? } ?>" rel="nofollow"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить')) ?></a>
    <? } ?>
<? } else { ?>
    <a href="#" onclick="return false;" class="btn brand-list__action btn_outline btn_outline--secondary disabled">Уточнить</a>
<? } ?>