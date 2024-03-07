<?= $bread_crumbs ?>
<div class="black-block">Материалы</div>
<div class="search_result in-firm-manager" style="border-top: none;">
    <div class="delimiter-block"></div>
    <div class="search_price_field">
        <form action="/firm-manager/materials/" method="get">
            <input placeholder="Поиск по материалам..." class="e-text-field" type="text" name="query"<? if (isset($filters['query']) && $filters['query']) { ?> value="<?= $filters['query'] ?>"<? } ?> />
            <input type="submit" value="" class="submit">
        </form><?= $sorting ?>
    </div><br/>
    <a href="/firm-manager/material/" class="default-red-btn" style="margin-left: 20px;">+ Новый материал</a>
    <div class="delimiter-block"></div>
    <div class="cat_description">
        <p>Найдено: <?= $items_count ?></p>
    </div>
    <?= $items ?>
    <?= $pagination ?>
</div>