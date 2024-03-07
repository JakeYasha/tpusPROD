<? if ($items) { ?>
    <table class="default-table rubric-table" style="width: 100%;">
        <tr>
            <th style="width: 35px;">#</th>
            <th style="width: 280px;">Наименование материала</th>
            <th>Анонс материала</th>
            <th>Статистика материала</th>
            <th>Управление</th>
        </tr>
        <? foreach ($items as $item) { ?>
            <tr<? if (!$item->val('flag_is_active')) { ?> style="opacity: .5"<? } ?>>
                <td style="text-align: center;"><?= $item->id() ?></td>
                <td style="max-width: 400px;"><a href="/firm-manager/material/<?= $item->id() ?>/" target="_blank"><?= $item->name() ?></a></td>
                <td><?= $item->val('short_text') ?></td>
                <td>
                    Дата создания: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_inserting'))) ?><br/>
                    Дата последнего изменения: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_last_updating'))) ?><br/>
                    Дата последней публикации: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_last_published'))) ?><br/>
                    <? if ($item->val('flag_is_time_limited')) {?>
                        Дата начала показа: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_start_show'))) ?><br/>
                        Дата окончания показа: <?= date("d.m.Y H:i", CDateTime::toTimestamp($item->val('timestamp_end_show'))) ?><br/>
                    <? } ?>
                </td>
                <td>
                <button class="material-button material-view-button" title="Посмотреть" alt="Посмотреть" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>"></button>
                <? if ($item->isPublished()) {?>
                    <button class="material-button material-unpublish-button" title="Закрыть" alt="Закрыть" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>"></button>
                    <button class="material-button material-publish-button" title="Опубликовать" alt="Опубликовать" style="display: none;" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>"></button>
                <? } else {?>
                    <button class="material-button material-unpublish-button" title="Закрыть" alt="Закрыть" style="display: none;" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>"></button>
                    <button class="material-button material-publish-button" title="Опубликовать" alt="Опубликовать" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>"></button>
                <? } ?>
                <button class="material-button material-remove-button" title="Удалить" alt="Удалить" data-id="<?= $item->id() ?>" data-preview_link="<?= $item->val('preview_link') ?>" data-mnemonic="<?= $item->val('mnemonic') ?>"></button>
                </td>
            </tr>
    <? } ?>
    </table>
<? } else { ?>
    <div class="cat_description">
        <p>Не найдено ни одного материала</p>
    </div>
<? } ?>
