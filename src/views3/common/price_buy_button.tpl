<? if ($item['is_yml']) { ?>
    <a href="#" onclick="return false;" class="btn brand-list__action btn_outline btn_outline--secondary disabled">В корзину</a>
<? } else { ?>
    <a class="btn brand-list__action btn_outline btn_outline--primary btn_buy js-open-modal-ajax" href="#" data-target="messageForm" data-url="/app-ajax/add-to-cart/?price_id=<?= $item['id'] ?>&count=1" rel="nofollow">В корзину</a>
<?
}?>