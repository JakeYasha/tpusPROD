<?= $bread_crumbs?>
<div class="black-block">Модерация категорий YML</div>
<div class="search_result" style="border-top: none;">
	<div class="search_price_field">
		<form action="/firm-manager/yml/categories/" method="get">
			<div class="notice-dark-grey firm-user-banners">
				<? /* <label>Группа</label>
				  <select name="id_group" class="def js-subgroup-chooser">
				  <option value="0">---</option>
				  <? foreach ($groups as $item) {?>
				  <option<? if ((int) $filters['id_group'] === (int) $item->val('id_group')) {?> selected="selected"<? }?> value="<?= $item->val('id_group')?>"><?= $item->name()?></option>
				  <? }?>
				  </select>
				  <br/><label>Подгруппа</label>
				  <select name="id_subgroup" class="def">
				  <option value="0">---</option>
				  <? foreach ($subgroups as $item) {?>
				  <option <? if ((int) $filters['id_subgroup'] === (int) $item->val('id_subgroup')) {?> selected="selected"<? }?> class="<? if ((int) $filters['id_group'] !== (int) $item->val('id_group')) {?>hidden <? }?>js-view-group js-view-group-<?= $item->val('id_group')?>" value="<?= $item->val('id_subgroup')?>"><?= $item->name()?></option>
				  <? }?>
				  </select>
				  <? if ($managers) {?>
				  <br/><label>Менеджер</label>
				  <select name="id_manager" class="def">
				  <option value="0">---</option>
				  <? foreach ($managers as $item) {?>
				  <option <? if ((int) $filters['id_manager'] === (int) $item->id()) {?> selected="selected"<? }?> value="<?= $item->id()?>"><?= $item->name()?></option>
				  <? }?>
				  </select>
				  <? }?> */?>
				<? if ($firms) {?>
					<br/><label>Фирма</label>
					<select name="id_firm" class="def">
						<option value="0">---</option>
						<? foreach ($firms as $item) {?>
							<option <? if ((int) $filters['id_firm'] === (int) $item->id()) {?> selected="selected"<? }?> value="<?= $item->id()?>"><?= $item->name()?></option>
						<? }?>
					</select>
				<? }?>
				<?if($catalogs) {?>
				<br/><label>Каталог Т+</label>
				<select name="id_catalog" class="def">
				<option value="0">--- все ---</option>
				<? foreach ($catalogs as $key => $val) {?>
				<option <? if ((int) $filters['id_catalog'] === (int) $key) {?> selected="selected"<? }?> value="<?= $key?>"><?=$val?></option>
				<? }?>
				<?}?>
				</select>
				<br/>
				<label>Фиксация</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_fixed'] === 0 || $filters['flag_is_fixed'] === null) {?>checked="checked"<? }?> name="flag_is_fixed" value="0" />все</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_fixed'] === 1) {?>checked="checked"<? }?> name="flag_is_fixed" value="1" />зафиксированные</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_fixed'] === 2) {?>checked="checked"<? }?> name="flag_is_fixed" value="2" />не зафиксированные</label>
				<br/>
				<label>Каталог</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_id_catalog'] === 0 || $filters['flag_is_fixed'] === null) {?>checked="checked"<? }?> name="flag_id_catalog" value="0" />все</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_id_catalog'] === 1) {?>checked="checked"<? }?> name="flag_id_catalog" value="1" />есть совпадение</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_id_catalog'] === 2) {?>checked="checked"<? }?> name="flag_id_catalog" value="2" />нет совпадений</label>
				<br/>
				<label>Существует</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_catalog'] === 0 || $filters['flag_is_catalog'] === null) {?>checked="checked"<? }?> name="flag_is_catalog" value="0" />все</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_catalog'] === 1) {?>checked="checked"<? }?> name="flag_is_catalog" value="1" />не существует</label>
				<label class="inline"><input class="e-check-box" type="radio" <? if ($filters['flag_is_catalog'] === 2) {?>checked="checked"<? }?> name="flag_is_catalog" value="2" />существует</label>
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