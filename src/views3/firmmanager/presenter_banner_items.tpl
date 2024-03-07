<?if($items){?>

<!--THDSFBH234234-->
<table class="default-table banner-table" style="width: 100%;">
	<tr>
		<th style="width: 35px;">#</th>
		<th style="width: 280px;">Баннеры</th>
		<th style="width: 40%;">Подгруппы и рубрики каталога</th>
		<th style="max-width: 100px;">Период размещения:</th>
		<th style="max-width: 100px;">Сайт</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?>>
		<td><?=$item['id']?></td>
		<td style="max-width: 400px;"><a href="/firm-manager/set-firm/<?=$item['firm']->id()?>/" target="_blank"><?=$item['firm']->name()?></a><br/><br/><?=$item['block']?></td>
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
		<td><?=$item['period']?>
            </td>
		<td><?=$item['site']?></td>
	</tr>


<?}?>
</table>



	
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>



