<?if($items){?>


<!--YSAYSADH32673627-->
<script type="text/javascript" src="/js/jquery-1.11.0.js"></script>
<script>

function convertUnixTimestampToReadableDate(unixTimestamp) {
  const date = new Date(unixTimestamp * 1000);
  const year = date.getFullYear();
  const month = ('0' + (date.getMonth() + 1)).slice(-2);
  const day = ('0' + date.getDate()).slice(-2);
  return `${day}.${month}.${year}`;
}


function updateBanner(idBanner, datetime) {
        $.ajax({
            url: '/firm-manager/ajax/banners/save',
            data: {
                id:idBanner,
                timestamp_ending:datetime + 14 * 24 * 60 * 60
            },
            type: 'post',
            dataType: 'json',
            async: false,
            success: function (response) {
                alert("Дата обновлена!");
                $('.item-period-'+idBanner).text("Дата завершения баннера: "+convertUnixTimestampToReadableDate(datetime + 14 * 24 * 60 * 60));
            },
            error: function (response) {
                $('.item-period-'+idBanner).text("Дата завершения баннера: "+convertUnixTimestampToReadableDate(datetime + 14 * 24 * 60 * 60));
                
            }
        });
    }
</script>


<table class="default-table banner-table" style="width: 100%;">
	<tr>
		<th style="width: 35px;">#</th>
		<th style="width: 280px;">Баннеры</th>
		<th style="width: 40%;">Подгруппы и рубрики каталога</th>
		<th style="max-width: 100px;">Период размещения -</th>
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
		<td><span class="item-period-<?=$item['id'];?>"><?=$item['period']?></span>
                <a href="javascript:void(0);" class="w-100 btn btn-success" onclick="updateBanner(<?=$item['id'];?>, <?=$item['end_date'];?>)">Продлить баннер на 14 дней</a></td>
		<td><?=$item['site']?></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
