<div class="delimiter-block"></div>
<h1>Статистика tovaryplus.ru</h1>
<table class="default-table statistic-table" style="width: 100%;">
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