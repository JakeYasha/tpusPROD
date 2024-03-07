<? if ($items) { ?>
    <div class="item_info">
        <div class="search_result">
            <div class="search_result_top red">
                <h2>Выгодные предложения от компаний <?= app()->location()->currentName('genitive') ?></h2>
                <a rel="nofollow" href="<?= app()->link('/firm-promo/') ?>">Смотреть все акции</a>
            </div>
            <div class="jcarousel-wrapper">
                <div class="pop_item_slider promo-slider jcarousel js-jcarousel">
                    <ul>
                        <?
                        $i = 0;
                        foreach ($items as $item) {
                            $i++;
                            ?>
                            <li class="leaf">
                                <div class="image">
                                    <? if ($item['flag_is_present']) { ?>
                                        <div class="promo-value present"></div>
                                    <? } elseif ($item['percent_value']) { ?>
                                        <div class="promo-value"><?= $item['percent_value'] ?>%</div>
                                    <? } else { ?>
                                        <div class="promo-value">SALE</div>
                                    <? } ?>
                                    <a href="<?= $item['link'] ?>"><? if ($item['image']) { ?><img src="<?= $item['image'] ?>" alt="<?= encode($item['name']) ?>" /><? } else { ?><img src="/css/img/no_img.png" alt="" /><? } ?></a></div>
                                <span class="title"><a href="<?= $item['link'] ?>"><?= $item['name'] ?></a></span>
                                <span class="price"><?= $item['flag_is_infinite'] ? 'Постоянная акция' : 'с ' . $item['time_beginning_short'] . ' по ' . $item['time_ending_short'] ?></span>
                            </li>
                        <? } ?>
                    </ul>
                </div>
                <a href="#" class="jcarousel-control-prev"></a>
                <a href="#" class="jcarousel-control-next"></a>
            </div>
        </div>
    </div>
    <?
}?>