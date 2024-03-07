<h1>123123123</h1><? if ($have_items) {
?>
<pre>
<?
print_r($have_items);

?>
</pre>
<table class="default-table rubric-table" style="width: 100%;">
        <tr>
            <th style="width: 35px;">#</th>
            <th style="width: 280px;">Наименование материала</th>
            <th>Дата</th>
            <th>Управление</th>
        </tr>
        <? foreach ($have_items as $item) { ?>
            <tr<? if (!$item->val('flag_is_active')) { ?> style="opacity: .5"<? } ?>>
                <td style="text-align: center;"><?= $item->id() ?></td>
                <td style="max-width: 400px;"><a href="/firm-manager/material/<?= $item->id() ?>/" target="_blank"><?= $item->name() ?></a></td>
                <td style="text-align:center;">
                    Дата создания: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_inserting'))) ?><br/>
                    Дата последнего изменения: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_last_updating'))) ?><br/>
                    Дата последней публикации: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_last_published'))) ?><br/>
                    <? if ($item->val('flag_is_time_limited')) {?>
                        Дата начала показа: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_start_show'))) ?><br/>
                        Дата окончания показа: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_end_show'))) ?><br/>
                    <? } ?>
                </td>
                <td style="text-align:center;">
                <button onclick="ajax_add_material(<?= $item->id() ?>);" class="material-button" title="Добавить в выпуск" alt="Добавить в выпуск" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>" style="width: 100%;">Добавить в выпуск</button>
                </td>
            </tr>
    <? } ?>
    </table>
<? } else { ?>
    <div class="cat_description">
        <p>Не добавлено ни одного материала</p>
    </div>

<?
}?>


