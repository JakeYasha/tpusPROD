<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<h1>Статистика показов и кликов баннеров на 727373.ru</h1>
<table class="banner-table">
    <tr>
        <th style="width: 30px;">#</th>
        <th style="width: 300px;">Баннеры</th>
        <th style="width: 30%;">Рубрики и ключевые слова</th>
        <th style="max-width: 100px;">Период размещения / MAX показов</th>
        <th>Показы/<br/>Переходы</th>
        <th class="last">CTR</th>
    </tr>
    <? foreach ($items as $item) { ?>
        <tr>
            <td>#<?= $item['id'] ?></td>
            <td class="727373"><?= $item['block'] ?><br/><b style="font-size: 10px;display:none;"><?= $item['target_site'] ?></b></td>
            <td class="banner-table-subgroup">
                <?
                $i = 0;
                $cnt = count($item['subgroups']);
                if (is_array($item['subgroups']) && count($item['subgroups'])) {
                    foreach ($item['subgroups'] as $cat) {
                        $i++;
                        ?>
                        <a target="_blank" href="<?= app()->link($cat->link()) ?>"><?= $cat->name() ?></a>
                        <? if ($i !== $cnt) { ?>, <? } ?>
                        <?
                    }
                }
                if ($cnt > 0) {
                    ?><hr/><?
                }
                $i = 0;
                $cnt = isset($item['catalogs']) ? count($item['catalogs']) : 0;
                if (isset($item['catalogs']) && is_array($item['catalogs']) && count($item['catalogs'])) {
                    foreach ($item['catalogs'] as $cat) {
                        $i++;
                        ?>
                        <a target="_blank" href="<?= app()->link($cat->link()) ?>"><?= $cat->name() ?></a>
                        <? if ($i !== $cnt) {
                            ?>, <?
                        }
                    }
                }

                if ($cnt > 0) {
                    ?><hr/><? 
                } ?>
                <p><?= $item['keywords'] ?></p>
            </td>
            <td><?= $item['period'] ?></td>
            <td><?= $item['count_shows'] ?>/<?= $item['count_clicks'] ?></td>
            <td class="last"><?= $item['ctr'] ?>%</td>
        </tr>
    <? } ?>
</table>