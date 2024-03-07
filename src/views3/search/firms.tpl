<div class="mdc-layout-grid">
    <?= $bread_crumbs?>
    <? if ($has_results) {?>
        <div class="block-title">
            <div class="block-title__decore"></div>
            <h1 class="block-title__heading block-title__heading_h1">Результаты поиска по запросу &QUOT;<?= encode($query)?>&QUOT;</h1>
            <p>В результат поиска попали фирмы для которых было найдено вхождение слов поискового запроса &QUOT;<?= encode($query)?>&QUOT; в названии, кратком описании вида деятельности фирмы, адресе. Сортировка списка идет по релевантности с учетом количества вхождений слов запроса.</p>
        </div>
    <? } else {?>
        <div class="block-title">
            <p><?= $text?></p>
        </div>
    <? }?>
    <div class="search-result">
        <?= $tabs?>
    </div>
    <div class="item_info">
        <div class="search-result">
            <div class="search-result-content">
                <div class="firm-bottom-block">
                    <? if ($has_results) {?>
                        <?= $pagination?>
                        <?= $items?>
                        <?= $pagination?>
                        <?if($filters['mode'] === null){?>
                            <?=$firm_catalogs?>
                        <?}?>
                    <? }?>
                </div>
            </div>
        </div>
    </div>
    <?= app()->adv()->renderRestrictions()?>
    <?= app()->adv()->renderAgeRestrictions()?>
    <br/>
    <div class="pre_footer_adv_block">
            <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
</div>