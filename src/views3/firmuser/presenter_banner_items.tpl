<? if ($items) { ?>
    <table class="default-table banner-table" style="width: 100%;">
        <tr>
            <th>Баннеры</th>
            <th style="width: 40%;">Подгруппы и рубрики каталога</th>
            <th style="max-width: 100px;">Период размещения / MAX показов</th>
            <th>Показы</th>
            <th>Клики</th>
            <th>CTR</th>
        </tr>
        <? foreach ($items as $item) { ?>
            <tr>
                <td><?= $item['block'] ?></td>
                <td class="banner-table-subgroup">
                    <? if (is_array($item['subgroups']) && count($item['subgroups'])) {
                        foreach ($item['subgroups'] as $cat) { ?>
                            <a target="_blank" href="<?= app()->link($cat->link()) ?>"><?= $cat->name() ?></a>
                        <? } ?>
                        <hr/>
                    <? } ?>
                    <? if (is_array($item['catalogs']) && count($item['catalogs'])) {
                        foreach ($item['catalogs'] as $cat) { ?>
                            <a target="_blank" href="<?= app()->link($cat->link()) ?>"><?= $cat->name() ?></a>
                        <? } ?>
                        <hr/>
                    <? } ?>

                    <p><?= $item['keywords'] ?></p>
                </td>
                <td><?= $item['period'] ?></td>
                <td><?= $item['count_shows'] ?></td>
                <? if ($item['count_clicks'] > 0) { ?>
                    <td><a href="<?= $item['clicks_link'] ?>"><?= $item['count_clicks'] ?></a></td>
                <? } else { ?>
                    <td><?= $item['count_clicks'] ?></td>
            <? } ?>
                <td><?= $item['ctr'] ?>%</td>
            </tr>
    <? } ?>
    </table>
    <?= app()->chunk()->render('firmuser.call_support_block') ?>
<? } else { ?>
    <div class="cat_description">
        <p>Нет данных</p>
    </div>

<? } ?>
