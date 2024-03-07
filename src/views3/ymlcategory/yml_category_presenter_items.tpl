<? if ($items) {?>
	<table class="default-table banner-table" style="width: 100%;">
		<tr>
			<th style="width: 33px;text-align: left;">Фикс</th>
			<th>Категория YML</th>
			<th>Категория T+</th>
			<th></th>
		</tr>
		<? foreach ($items as $item) {?>
			<tr<? if (!$item['id_catalog']) {?> style="opacity: .8"<? }?>>
				<td<?if($item['flag_is_catalog'] == 0) { ?> style="background-color: #ffcaca;"<?}?>><input type="checkbox" class="js-yml-fix-rubric" data-id-category="<?= $item['id']?>"<? if ((int) $item['flag_is_fixed'] === 1) {?> checked="true"<? }?> /></td>
				<td<?if($item['flag_is_catalog'] == 0) { ?> style="background-color: #ffcaca;"<?}?>><?= $item['parent_name']?> / <?= $item['name']?><br/></td>
				<td<?if($item['flag_is_catalog'] == 0) { ?> style="background-color: #ffcaca;"<?}?>><?= $item['id_catalog'] ? '<a href="/app-ajax/get-catalog-name/?id_yml_category=' . $item['id'] . '" class="fancybox fancybox.ajax">' . $item['catalog_name'] . '</a>' : '<a href="/app-ajax/get-catalog-name/?id_yml_category=' . $item['id'] . '" class="fancybox fancybox.ajax">выбрать</a>'?><br/><?= $item['parent_catalog_name']?></td>
				<td<?if($item['flag_is_catalog'] == 0) { ?> style="background-color: #ffcaca;"<?}?>><?= $item['id_catalog'] ? '<a href="' . $item['link'] . '" target="_blank"><img src="/css/img/icon-eye.png" /></a>' : '-'?></td>
			</tr>
		<? }?>
	</table>
<? } else {?>
	<div class="cat_description">
		<p>Нет данных</p>
	</div>
<? }?>
