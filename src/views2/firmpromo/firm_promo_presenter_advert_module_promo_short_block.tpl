<div class="item_info">
    <div class="search_result">
        <h2>Товары и услуги <?= app()->location()->currentName('prepositional') ?></h2>
        <div class="promo-block-wrapper">
            <? if ($items) { ?>
                <ul class="promo-block clearfix">
                    <?
                    $i = 0;
                    $visible = true;
                    foreach ($items as $item) {
                        $i++;
                        ?>
                        <li class="leaf <?=$visible ? '' : 'hidden-leaf'?>">
                            <div class="image">
                                <a href="<?= $item['link'] ?>">
                                    <div class="image-wrapper">
                                        <? if ($item['image']) { ?><img src="<?= $item['image'] ?>" alt="<?= encode($item['name']) ?>" /><? } else { ?><img src="/css/img/no_img.png" alt="" /><? } ?>
                                    </div>
                                    <? if ($item['flag_is_present']) { ?>
                                        <div class="promo-value present"></div>
                                    <? } elseif ($item['percent_value']) { ?>
                                        <div class="promo-value"><?= $item['percent_value'] ?>%</div>
                                    <? } else { ?>
                                        <div class="promo-value">SALE</div>
                                    <? } ?>
                                </a>
                            </div>
                            <div class="title"><a href="<?= $item['link'] ?>"><?= $item['name'] ?></a></div>
                            <div class="firm"><?= $item['firm_name'] ?></div>
                        </li>
                        <? if ($i % 12 == 0 && $i < count($items) ) { ?>
                            <div class="leaf-delimiter <?=$visible ? '' : 'hidden-leaf'?>" onclick="$('.leaf.hidden-leaf:lt(12)').removeClass('hidden-leaf'); $('.leaf-delimiter').eq(0).remove(); $('.leaf-delimiter').eq(0).removeClass('hidden-leaf');">Показать ещё</div>
                            <? $visible = false; ?>
                        <? } ?>
                    <? } ?>
                </ul>
            <? } ?>
        </div>
    </div>
</div>