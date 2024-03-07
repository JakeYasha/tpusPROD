<button class="btn-phone-hider hide js-show-phone js-show-phone-<?=$item->id()?>" data-firm-phone="<?=$item->phone()?>" data-firm-first-phone="+<?=  preg_replace('~[^0-9]~', '', explode(',', $item->phone())[0])?>" data-firm-id="<?=$item->id()?>">
	<span class="btn-phone-hider-phone"><?=preg_replace('~(\+?[0-9]+[ -]\(?[0-9]+\)?)[ -].*~', '$1 XXX', $item->phone())?></span>
	<span class="btn-phone-hider-btn">Показать телефон</span>
</button>