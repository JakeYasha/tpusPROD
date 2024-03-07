<? if (isset($items)) {?>
<div class="black-block">Выбор филиала для управления</div>
<div class="search_result in-firm-manager" style="border-top: none;">
    <div class="search_result_content ">
        <table class="default-table price-table">
            <tr>
                <th>Филиал</th>
                <th style="width: 60px;">Дата обновления</th>
                <th></th>
            </tr>
            <? $i = 0;
            foreach ($items as $item) {
                $i++; ?>
                <tr <? if ($item->isBlocked()) { ?> style="opacity: .5"<? } ?>>
                    <td>
                        <span class="ui-icon ui-icon-notice information tooltip" title="" style="display: inline-block;margin-top: -2px;float:left;"></span>
                        <div class="clearfix">
                            <a class="left_block" target="_blank" href="/firm-user/firm-branch/<?= $item->id() ?>/" style="max-width: calc(100% - 32px);"><?= $item->name() ?></a>
                            <br/>
                        </div>
                        <p class="description"><?= $item->address() ?></p>
                    </td>
                    <td><p style="text-align: right;"><?= date('d.m.Y', CDateTime::toTimestamp($item->val('timestamp_last_updating'))) ?></p><p class="description" style="text-align: right;"><?= date('H:i:s', CDateTime::toTimestamp($item->val('timestamp_inserting'))) ?></p></td>
                    <td style="vertical-align: middle; text-align: center;">
                        <a title="Удалить" onclick="return confirm('Подтвердите удаление...')" href="/firm-user/firm-branch/delete/?id=<?=$item->id()?>" class="delete-btn"></a>
                    </td>
                </tr>
            <? } ?>
        </table>
    </div>
</div>
<br/>
<a href="/firm-user/firm-branch/add/" class="default-red-btn" style="margin-left: 20px;">Добавить филиал</a>
<?} else {?>
<div class="black-block"><?= $title ?></div>
<div class="search_result in-firm-manager" style="border-top: none;">
    <div class="search_result_content ">
        <?= $form ?>
    </div>
</div>
<?}?>