<? if ($items) { ?>
    <table class="default-table rubric-table" style="width: 100%;">
        <tr>
            <th style="width: 35px;">#</th>
            <th style="width: 280px;">Наименование рубрики</th>
            <th>Статистика рубрики</th>
        </tr>
        <? foreach ($items as $item) { ?>
            <tr<? if (!$item['is_active']) { ?> style="opacity: .5"<? } ?>>
                <td style="text-align: center;"><?= $item['id'] ?></td>
                <td style="max-width: 400px;"><a href="/firm-manager/material-rubric/<?= $item['id'] ?>/" target="_blank"><?= $item['name'] ?></a></td>
                <td>
                    Материалов в рубрике: <?= $item['materials_count'] ?>
                    <? if ($item['last_added']) {
                        $last_added = $item['last_added'];
                        ?>
                        <br/><br/>Последний добавленный материал: <br/>
                        <?= date("d.m.Y H:i", CDateTime::toTimestamp($last_added->val('timestamp_last_updating'))) ?> /// <a href="<?= $last_added->link() ?>"><?=$last_added->name()?></a>
                    <? } ?>
                    <? if ($item['last_updated']) {
                        $last_updated = $item['last_updated'];
                        ?>
                        <br/><br/>Последний измененный материал:<br/>
                        <?= date("d.m.Y H:i", CDateTime::toTimestamp($last_updated->val('timestamp_last_updating'))) ?> /// <a href="<?= $last_updated->link() ?>"><?=$last_updated->name()?></a>
                    <? } ?>
                </td>
            </tr>
    <? } ?>
    </table>
<? } else { ?>
    <div class="cat_description">
        <p>Вы еще не создали ни одной рубрики</p>
    </div>
<? } ?>
