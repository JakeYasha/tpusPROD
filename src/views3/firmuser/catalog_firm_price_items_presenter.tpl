<div class="search_result_price">
    <? /* @var $item['firm'] Firm */ ?>
    <div class="search_result_content ">
        <? 
        $edit_mode = false;
        if (isset($firm) && $firm->exists() && in_array((int)$firm->id_service(), [15,36])) {
            $edit_mode = true;
        }?>
        <table class="default-table price-table price-table2">
            <tr>
                <th style="width:30px;text-align:center;"><input type="checkbox" name="select_all"/></th>
                <th style="width: 30px;">&nbsp;</th>
                <th>Название</th>
                <th style="width: 80px;">Цена</th>
                <th style="width: 80px;">Можно купить</th>
                <? if ($edit_mode) { ?>
                    <th style="width: 60px;">&nbsp;</th>
                <? } ?>
            </tr>
            <? $i = 0;
            foreach ($items as $id => $item) {
                $i++; ?>
                <tr>
                    <td style="text-align: center;vertical-align: middle;"><input type="checkbox" name="delete" data-id="<?= $item['id'] ?>"/></td>
                    <td class="btn_cell">
                        <? if ($item['image']) { ?>
                            <a href="/firm-user/ajax/price-image-upload-dialog/<?= $item['id'] ?>/" class="js-action fancybox fancybox.ajax upload-btn js-upload-dialog-<?= $item['id'] ?>" title="Изменить фотографию"><img class="price-image" src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" /></a>
                        <? } else { ?>
                            <a href="/firm-user/ajax/price-image-upload-dialog/<?= $item['id'] ?>/" class="js-action fancybox fancybox.ajax table-btn upload-btn js-upload-dialog-<?= $item['id'] ?>" title="Загрузить фотографию"></a>
                        <? } ?>
                    </td>
                    <td><a target="_blank" href="<?= $item['link'] ?>"><?= $item['name'] ?></a></td>
                    <td><?= $item['price'] ?> <?= $item['currency'] ?></td>
                    <td style="text-align: center;vertical-align: middle;"><input type="checkbox" name="flag_is_active" data-id="<?= $item['id'] ?>"/></td>
                    <? if ($edit_mode) { ?>
                        <td style="vertical-align: middle; text-align: center;">
                            <a title="Изменить" href="/firm-user/price/edit/?id=<?=$item['id']?>" class="edit-btn"></a>
                            <a title="Удалить" onclick="return confirm('Подтвердите удаление...')" href="/firm-user/price/delete/?id=<?=$item['id']?>" class="delete-btn"></a>
                        </td>
                    <? } ?>

                </tr>
            <? } ?>
        </table>
        <br/>
        <!--/firm-user/price/delete/?id=521618-->
        <? if ($edit_mode) { ?>
            <a class="default-red-btn delete-selected-btn" style="margin-left: 20px;" title="Удалить выбранные">Удалить выбранные</a>
        <? } ?>
        <br/>
    </div>
    <button class="show_more_results js-show-more">Показать еще результаты</button>
</div>