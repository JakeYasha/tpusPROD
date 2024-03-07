<?= $bread_crumbs?>
<div class="black-block">Модерация предложений YML</div>
<div class="search_result" style="border-top: none;">
	<div class="search_price_field">
		<form action="/firm-manager/yml/offers/" method="get">
			<div class="notice-dark-grey firm-user-banners">
				<? if ($firms) {?>
					<br/><label>Фирма</label>
					<select name="id_firm" class="def">
						<option value="0">---</option>
						<? foreach ($firms as $item) {?>
							<option <? if ((int) $filters['id_firm'] === (int) $item->id()) {?> selected="selected"<? }?> value="<?= $item->id()?>"><?= $item->name()?></option>
						<? }?>
					</select>
				<? }?>
				<br/>
				<?/*<label>Группа/подгруппа</label>
				<select name="id_subgroup" class="def">
					<option value="0">не указано</option>
					<? foreach ($groups as $id_group => $group) {$subgroups_ids = $group_matrix[$group['id_group']];?>
					<optgroup label="<?=$group['name']?>"></optgroup>
						<? foreach ($subgroups_ids as $id_subgroup) {$subgroup = $subgroups[$id_subgroup];?>
						<option <? if ((int) $filters['id_subgroup'] === (int) $subgroup['id_subgroup']) {?> selected="selected"<? }?> value="<?= $subgroup['id_subgroup']?>"><?= $subgroup['name']?></option>
						<?}?>
					<?}?>
				</select>
				<br/>*/?>
				<label>Отображение</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_active'] === 0 || $filters['flag_is_active'] === null) {?>checked="checked"<? }?> name="flag_is_active" value="0" />все</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_active'] === 1) {?>checked="checked"<? }?> name="flag_is_active" value="1" />активные</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_active'] === 2) {?>checked="checked"<? }?> name="flag_is_active" value="2" />заблокированные</label>
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