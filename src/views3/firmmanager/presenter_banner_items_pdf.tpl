<div class="delimiter-block"></div>
<h1>Статистика баннеров tovaryplus.ru</h1>
<table class="banner-table">
	<tr>
		<th style="width: 30px;">#</th>
		<th style="width: 300px;">Баннеры</th>
		<th style="width: 30%;">Подгруппы и рубрики каталога</th>
		<th style="max-width: 100px;">Период размещения / MAX показов</th>
		<?if($items[0] && isset($items[0]['count_clicks']) && $items[0]['count_clicks']){?>
            <th>Показы/<br/>Переходы</th>
            <th class="last">CTR</th>
		<?}?>
	</tr>
<?foreach ($items as $item) { if ($item['site'] == '727373') continue;?>
	<tr>
		<td>#<?=$item['id']?></td>
		<td style="width: 200px;"><a href="/firm-manager/set-firm/<?=$item['firm']->id()?>/" target="_blank"><?=$item['firm']->name()?></a><br/><br/><?=$item['block']?><br/><span class="grey">[<?=$item['banner_url']?>]</span></td>
		<td class="banner-table-subgroup">
            <?$i=0;$cnt=count($item['subgroups']);
            foreach($item['subgroups'] as $cat){$i++;?>
                <a target="_blank" href="<?=app()->link($cat->link())?>"><?=$cat->name()?></a>
                <?if($i!==$cnt){?>, <?}?>
            <?}?>
            <?if ($cnt > 0) {?><br/><br/><?}?>
            <?$i=0;$cnt=count($item['catalogs']);
            foreach($item['catalogs'] as $cat){$i++;?>
                <a target="_blank" href="<?=app()->link($cat->link())?>"><?=$cat->name()?></a>
                <?if($i!==$cnt){?>, <?}?>
            <?}?>
            <?if ($cnt > 0) {?><br/><br/><?}?>
            <p><?=$item['keywords']?></p></td>
		<td style="width: 200px"><?=$item['period']?></td>
		<?if($item['count_clicks']){?>
		<td><?=$item['count_shows']?>/<?=$item['count_clicks']?></td>
		<td class="last"><?=round($item['count_clicks']/$item['count_shows']*100, 2)?>%</td>
		<?}?>
	</tr>
<?}?>
</table>
<!-- ============================================== -->
<div class="delimiter-block"></div>
<h1>Статистика баннеров 727373.ru</h1>
<table class="banner-table">
	<tr>
		<th style="width: 30px;">#</th>
		<th style="width: 300px;">Баннеры</th>
		<th style="width: 30%;">Подгруппы и рубрики каталога</th>
		<th style="max-width: 100px;">Период размещения / MAX показов</th>
		<?if($items[0] && isset($items[0]['count_clicks']) && $items[0]['count_clicks']){?>
		<th>Показы/<br/>Переходы</th>
		<th class="last">CTR</th>
		<?}?>
	</tr>
<?foreach ($items as $item) { if ($item['site'] == 'tovaryplus') continue;?>
	<tr>
		<td>#<?=$item['id']?></td>
		<td style="width: 200px;"><a href="/firm-manager/set-firm/<?=$item['firm']->id()?>/" target="_blank"><?=$item['firm']->name()?></a><br/><br/><?=$item['block']?><br/><span class="grey">[<?=$item['banner_url']?>]</span></td>
		<td class="banner-table-subgroup">
            <?$i=0;$cnt=count($item['subgroups']);
            foreach($item['subgroups'] as $cat){$i++;?>
                <a target="_blank" href="<?=app()->link($cat->link())?>"><?=$cat->name()?></a>
                <?if($i!==$cnt){?>, <?}?>
            <?}?>
            <?if ($cnt > 0) {?><br/><br/><?}?>
            <?$i=0;$cnt=count($item['catalogs']);
            foreach($item['catalogs'] as $cat){$i++;?>
                <a target="_blank" href="<?=app()->link($cat->link())?>"><?=$cat->name()?></a>
                <?if($i!==$cnt){?>, <?}?>
            <?}?>
            <?if ($cnt > 0) {?><br/><br/><?}?>
            <p><?=$item['keywords']?></p></td>
		<td style="width: 200px"><?=$item['period']?></td>
		<?if($item['count_clicks']){?>
		<td><?=$item['count_shows']?>/<?=$item['count_clicks']?></td>
		<td class="last"><?=round($item['count_clicks']/$item['count_shows']*100, 2)?>%</td>
		<?}?>
	</tr>
<?}?>
</table>