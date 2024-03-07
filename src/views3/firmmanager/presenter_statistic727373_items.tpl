<? if ($items) { ?>
    <table class="default-table statistic-table" style="width: 100%;max-width: 100%;">
        <tr>
            <th style="width: 35px;">#</th>
            <th>Наименование</th>
            <th>Тип</th>
            <th>Количество</th>
            <th>Сайт</th>
        </tr>
        <? foreach ($items as $item) { ?>
            <tr>
                <td><?= $item['num'] ?></td>
                <td><?= $item['name'] ?></td>
                <td><?= $item['type'] ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['site'] ?></td>
            </tr>
        <? } ?>
    </table>
<? } else { ?>
    <div class="cat_description">
        <p>Нет данных</p>
    </div>
<? } ?>
