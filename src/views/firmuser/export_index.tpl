<?= $bread_crumbs ?>
<div class="black-block">Статистика email</div>
<div class="search_result">
    <? if ($dates_block) { ?>
        <ul class="date-block">
            <? foreach ($dates_block as $date) { ?>
                <li<? if ($date['active']) { ?> class="active"<? } ?>><a<? if ($date['active']) { ?> class="js-action"<? } ?> href="<?= $date['link'] ?>"><?= $date['name'] ?></a></li>
                <? } ?>
        </ul>
    <? } ?>
    <?= $items ?>
    <?= $pagination ?>
</div>