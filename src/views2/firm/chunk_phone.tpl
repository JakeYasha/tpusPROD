<a href="tel:+<?=str()->sub(preg_replace('~[^0-9]~u', '', $item->phone()), 0, 11)?>" class="tel     brand-list__item--link" data-firm-id="<?=$item->id()?>"><?=$item->phone()?></a><?if($item->hasCellPhone()){?>, <a href="tel:+<?= preg_replace('~[^0-9]~u', '', $item->cellPhone())?>" class="tel brand-list__item--link" data-firm-id="<?=$item->id()?>"><?=$item->cellPhone()?></a><?}?>