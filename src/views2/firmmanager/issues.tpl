<?= $bread_crumbs ?>
<div class="black-block">Выпуски</div>
<div class="search_result in-firm-manager" style="border-top: none;">
    <div class="delimiter-block"></div>
    <div class="search_price_field">
        <form action="/firm-manager/issues/" method="get">
            <input placeholder="Поиск по выпускам..." class="e-text-field" type="text" name="query"<? if (isset($filters['query']) && $filters['query']) { ?> value="<?= $filters['query'] ?>"<? } ?> />
            <input type="submit" value="" class="submit">
        </form><?= $sorting ?>
    </div><br/>
    <a href="/firm-manager/issue/?mode=add" class="default-red-btn" style="margin-left: 20px;">+ Новый выпуск</a>
    <div class="delimiter-block"></div>
    <div class="cat_description">
        <p>Найдено: <?= $items_count ?></p>
    </div>
    <?= $items ?>
    <?= $pagination ?>
</div>