<?if($items){?>
    <table class="default-table banner-table" style="width: 100%;">
        <tr>
            <th style="width: 35px;">#</th>
            <th style="max-width: 100px;">Название</th>
            <th style="width: 40%;">Описание</th>
            <th style="max-width: 100px;">Действия</th>
        </tr>
    <?foreach ($items as $item) {?>
        <tr>
            <td><?=$item->val('number')?></td>
            <td><a href="/firm-manager/issue/<?=$item->id()?>/"><?=$item->name()?></a></td>
            <td><?=$item->val('short_text')?></td>
            <td>не задано</td>
        </tr>
    <?}?>
    </table>
<?} else {?>
    <div class="cat_description">
        <p>Нет данных</p>
    </div>
<?}?>
