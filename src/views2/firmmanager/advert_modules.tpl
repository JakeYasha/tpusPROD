<?= $bread_crumbs?>
<a title="Выгрузить в pdf" target="_blank" class="download-btn" href="<?=app()->linkFilter('/firm-manager/advert-modules-pdf/', $filters)?>"></a>
<div class="black-block">Рекламные модули</div>
<div class="cat_description">
	<a href="/page/show/directions-advert-module.htm" target="_blank">Инструкция по работе с рекламными модулями</a>
</div>	
<div class="search_result" style="border-top: none;">

<div class="search_price_field">
	<form action="/firm-manager/advert-modules/" method="get">
		<div class="notice-dark-grey firm-user-banners">
			<label>Группа</label>
			<select name="id_group" class="def js-subgroup-chooser">
				<option value="0">---</option>
				<?foreach ($groups as $item) {?>
					<option<?if((int)$filters['id_group'] === (int)$item->val('id_group')){?> selected="selected"<?}?> value="<?=$item->val('id_group')?>"><?=$item->name()?></option>
				<?}?>
			</select>
			<br/><label>Подгруппа</label>
			<select name="id_subgroup" class="def">
				<option value="0">---</option>
				<?foreach ($subgroups as $item) {?>
					<option <?if((int)$filters['id_subgroup'] === (int)$item->val('id_subgroup')){?> selected="selected"<?}?> class="<?if((int)$filters['id_group'] !== (int)$item->val('id_group')){?>hidden <?}?>js-view-group js-view-group-<?=$item->val('id_group')?>" value="<?=$item->val('id_subgroup')?>"><?=$item->name()?></option>
				<?}?>
			</select>
			<?if($managers){?>
			<br/><label>Менеджер</label>
			<select name="id_manager" class="def">
				<option value="0">---</option>
				<?foreach ($managers as $item) {?>
					<option <?if((int)$filters['id_manager'] === (int)$item->id()){?> selected="selected"<?}?> value="<?=$item->id()?>"><?=$item->name()?></option>
				<?}?>
			</select>
			<?}?>
			<?if($firms){?>
			<br/><label>Фирма</label>
			<select name="id_firm" class="def">
				<option value="0">---</option>
				<?foreach ($firms as $item) {?>
					<option <?if((int)$filters['id_firm'] === (int)$item->id()){?> selected="selected"<?}?> value="<?=$item->id()?>"><?=$item->name()?></option>
				<?}?>
			</select>
			<?}?>
			<br/>
			<label>Активность</label>
			<label class="inline"><input class="e-check-box" type="radio" <?if($filters['active'] === 1){?>checked="checked"<?}?> name="active" value="1" />активные</label>
			<label class="inline"><input class="e-check-box" type="radio" <?if($filters['active'] === 2){?>checked="checked"<?}?> name="active" value="2" />не активные</label>
			<label class="inline"><input class="e-check-box" type="radio" <?if($filters['active'] === 0){?>checked="checked"<?}?> name="active" value="0" />все</label>
			<br/>
			<br/>
			<br/>
			<button type="submit" style="width: 200px;">Поиск</button>
		</div>
	</form>
</div>
<div class="delimiter-block"></div>
<?if($items_count > 0) {?>
        <div class="cat_description">
            <p>Найдено: <?=$items_count?></p>
        </div>
<?}?>
<?=$items?>
<?=$pagination?>
</div>