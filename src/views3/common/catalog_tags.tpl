<? if ($items) { if(!isset($mode)){$mode = 'tags';}?>
    <div class="mdc-layout-grid__inner">
        <? $_i = 1; foreach ($items as $item) {
            if (!$item instanceof \App\Model\FirmType) {
                $item = $item['catalog'];
            }
            if (!$item->exists()) continue;
            $_filter = [];
            if(isset($filter['mode']) && $filter['mode'] != 'map') {
                $_filter['mode'] = $filter['mode'];
            }
            ?>
            <?if ($_i !== 10) {?>
                <div class="mdc-layout-grid__cell">
                    <a class="service-item" href="<?= (isset($link) && $link) ? app()->linkFilter($link, array_merge($_filter, ['id_catalog' => $item->id()])) : app()->link(app()->linkFilter($item->link(), $_filter))?>"><?= $item->name($mode)?></a>
                </div>
            <?} else {?>
                <div class="mdc-layout-grid__cell--span-12">
                    <div class="filter-form__box_hidden">
                        <div class="mdc-layout-grid__inner">
                            <div class="mdc-layout-grid__cell">
                                <a class="service-item" href="<?= (isset($link) && $link) ? app()->linkFilter($link, array_merge($_filter, ['id_catalog' => $item->id()])) : app()->link(app()->linkFilter($item->link(), $_filter))?>"><?= $item->name($mode)?></a>
                            </div>
            <?}?>
        <? $_i++; }?>
        <?if ($_i > 10) {?>
                        </div>
                    </div>
                    <div class="filter-form__expand" id="filter-form-expand_tags" style="margin-bottom: 1rem">
                        <span>Показать все</span>
                        <img alt="" src="/img3/arrow-bot.png">
                    </div>
                </div>
        <?}?>
	</div>
<?}?>