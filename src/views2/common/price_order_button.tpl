<? if ($item['is_yml']) { ?>
    <a class="btn-base btn-red w130" href="<?= $item['link'] ?>" rel="nofollow" target="_blank">В магазин</a>
    <? /* <a class="btn-base btn-red w130" href="<?=$item['link']?>" rel="nofollow" target="_blank"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить'))?></a> */ ?>
<? } elseif (!$item['is_yml']) { ?>
    <? if (isset($request_disabled)) { ?>
        <? if ($request_disabled) { ?>
            <a class="btn-base w130 fancybox fancybox.ajax disabled btn-grey" href="#" onclick="return false;" rel="nofollow"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить')) ?></a>
        <? } else { ?>
            <a class="btn-base w100pr fancybox fancybox.ajax btn-red" href="/price/get-request-form/?id_price=<?= $id ?>&amp;type=<?= $item['price'] ? 'order' : 'check' ?><? if (isset($is_for_sms) && $is_for_sms) { ?>&sms_mode=1<? } ?>" rel="nofollow"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить')) ?></a>
        <? } ?>
    <? } else { ?>
        <a class="btn-base w100pr fancybox fancybox.ajax btn-red" href="/price/get-request-form/?id_price=<?= $id ?>&amp;type=<?= $item['price'] ? 'order' : 'check' ?><? if (isset($is_for_sms) && $is_for_sms) { ?>&sms_mode=1<? } ?>" rel="nofollow"><?= in_array($item['id_subgroup'], [258, 440, 267, 269, 272, 285, 284, 386]) ? (isset($big_button) ? 'Записаться на прием' : 'Записаться') : ($item['price'] ? (isset($big_button) ? 'Заказать сейчас' : 'Заказать') : (isset($big_button) ? 'Уточнить наличие и цену' : 'Уточнить')) ?></a>
    <? } ?>
<? } else { ?>
    <a href="#" onclick="return false;" class="btn-base btn-grey w130 disabled">Уточнить</a>
<? } ?>