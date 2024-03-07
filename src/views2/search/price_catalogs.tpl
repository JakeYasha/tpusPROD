<div class="firm-catalog" style="margin-bottom: 20px;">
    <div class="black-block" style="margin: 0;">Уточните категорию искомого товара или услуги по запросу &QUOT;<?= encode($query) ?>&QUOT;</div>
    <? foreach ($matrix as $id_parent => $childs) {
        if ((int) $id_parent === 0) { ?>
        <h2 style="margin: 12px 0 3px 0;"><a href="/<?= app()->location()->currentId() ?>/catalog/">Каталог товаров и услуг</a></h2>
        <div class="tags_field">
            <ul>
                <? foreach ($childs as $id_catalog) {
                    if (!isset($items[$id_catalog])) continue; ?>
                    <li><a href="<?= app()->link($items[$id_catalog]->link()) ?>"><?= $items[$id_catalog]->name() ?></a></li>
                <? } ?>
            </ul>
            <? if (count($childs) > 6) { ?>
            <div class="show_more">
                <div class="line"></div>
                <a href="#" class="js-show-more-tags"><span>Показать все</span></a>
            </div>
            <? } ?>
        </div>
    <? } else { ?>
        <h2 style="margin: 12px 0 3px 0;"><a href="<?= app()->link($items[$id_parent]->link()) ?>"><?= $items[$id_parent]->name() ?></a></h2>
        <div class="tags_field">
            <ul>
                <? foreach ($childs as $id_catalog) {
                    if (!isset($items[$id_catalog])) continue; ?>
                    <li><a href="<?= app()->link($items[$id_catalog]->link()) ?>"><?= $items[$id_catalog]->name() ?></a></li>
                <? } ?>
            </ul>
        <? if (count($childs) > 6) { ?>
            <div class="show_more">
                <div class="line"></div>
                <a href="#" class="js-show-more-tags"><span>Показать все</span></a>
            </div>
        <? } ?>
        </div>
    <? } ?>
<? } ?>
</div>