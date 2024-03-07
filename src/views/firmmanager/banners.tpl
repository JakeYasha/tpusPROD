<?= $bread_crumbs?>
<a title="Выгрузить в pdf" target="_blank" class="download-btn" href="<?= app()->linkFilter('/firm-manager/banners-pdf/', $filters)?>"></a>
<div class="black-block">Баннеры</div>
<div class="cat_description">
	<a href="/page/show/opisanie-raboty-bannernoj-sistemy.htm" target="_blank">Схема размещения рекламных блоков и описание логики работы баннерной системы</a>
</div>	
<div class="search_result" style="border-top: none;">
	<div class="banners_by_types">
		<table class="default-table banner-table" style="width: 100%;">
			<thead>
				<tr>
					<th style="width: 35px;">#</th>
					<th style="width: 980px;">Рекламное место</th>
					<th style="max-width: 100px;">Количество</th>
				</tr>
			</thead>
			<tbody>
				<? $i = 1;
				foreach ($banner_count_by_types as $banner) {?>
					<tr>
						<td style="text-align: center;"><?= $i++?></td>
						<td><?= $banner['name']?></td>
						<td style="text-align: center;"><?= (isset($banner['count']) && $banner['count']) ? $banner['count'] : '0'?></td>
					</tr>
<? }?>
			</tbody>
		</table>
	</div>
	<div class="search_price_field">
		<form action="/firm-manager/banners/" method="get">
			<div class="notice-dark-grey firm-user-banners">
				<label>Место</label>
				<select name="type" class="def">
					<option value="all">---</option>
					<? foreach ($types as $key => $value) {?>
						<option<? if ($filters['type'] === $key) {?> selected="selected"<? }?> value="<?= $key?>"><?= $value?></option>
<? }?>
				</select>
				<br/><label>Группа</label>
				<select name="id_group" class="def js-subgroup-chooser">
					<option value="0">---</option>
					<? foreach ($groups as $item) {?>
						<option<? if ((int) $filters['id_group'] === (int) $item->val('id_group')) {?> selected="selected"<? }?> value="<?= $item->val('id_group')?>"><?= $item->name()?></option>
<? }?>
				</select>
				<br/><label>Подгруппа</label>
				<select name="id_subgroup" class="def js-catalog-chooser">
					<option value="0">---</option>
					<? foreach ($subgroups as $item) {?>
						<option <? if ((int) $filters['id_subgroup'] === (int) $item->val('id_subgroup')) {?> selected="selected"<? }?> class="<? if ((int) $filters['id_group'] !== (int) $item->val('id_group')) {?>hidden <? }?>js-view-group js-view-group-<?= $item->val('id_group')?>" value="<?= $item->val('id_subgroup')?>"><?= $item->name()?></option>
				<? }?>
				</select>
                <br/><label>Каталог</label>
                <select name="id_catalog" class="def">
                    <option value="0">---</option>
                    <? foreach ($catalogs as $item) { ?>
                        <option <? if ((int) $filters['id_catalog'] === (int) $item->val('id')) { ?> selected="selected"<? } ?> class="<? if ((int) $filters['id_subgroup'] !== (int) $item->val('id_subgroup')) { ?>hidden <? } ?>js-view-subgroup js-view-subgroup-<?= $item->val('id_subgroup') ?>" value="<?= $item->val('id') ?>"><?= $item->name() ?></option>
                <? } ?>
                </select>
<? if ($managers) {?>
					<br/><label>Менеджер</label>
					<select name="id_manager" class="def">
						<option value="0">---</option>
						<? foreach ($managers as $item) {?>
							<option <? if ((int) $filters['id_manager'] === (int) $item->id()) {?> selected="selected"<? }?> value="<?= $item->id()?>"><?= $item->name()?></option>
					<? }?>
					</select>
				<? }?>
<? if ($firms) {?>
					<br/><label>Фирма</label>
					<select name="id_firm" class="def">
						<option value="0">---</option>
						<? foreach ($firms as $item) {?>
							<option <? if ((int) $filters['id_firm'] === (int) $item->id()) {?> selected="selected"<? }?> value="<?= $item->id()?>"><?= $item->name()?></option>
					<? }?>
					</select>
<? }?>
				<br/><label>Тип баннера</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['max_count'] === 1) {?>checked="checked"<? }?> name="max_count" value="1" />по времени</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['max_count'] === 2) {?>checked="checked"<? }?> name="max_count" value="2" />по показам</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['max_count'] === 0) {?>checked="checked"<? }?> name="max_count" value="0" />все</label>
				<div class="delim"></div>
				<label>Активность</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['active'] === 1) {?>checked="checked"<? }?> name="active" value="1" />активные</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['active'] === 2) {?>checked="checked"<? }?> name="active" value="2" />не активные</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['active'] === 0) {?>checked="checked"<? }?> name="active" value="0" />все</label>
				<br/>
				<br/>
				<br/>
				<button type="submit" style="width: 200px;">Поиск</button>
			</div>
		</form>
	</div>
	<div class="delimiter-block"></div>
<? if ($items_count > 0) {?>
		<div class="cat_description">
			<p>Найдено: <?= $items_count?></p>
		</div>
	<? }?>
	<?= $items?>
<?= $pagination?>
</div>