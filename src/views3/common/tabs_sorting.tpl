<?if($sorters){?>
	<div class="sort_by_field">
		<form action="<?=isset($link) && !empty($link) ? $link : '#'?>" method="get">
			<? foreach ($filters as $k=>$v) {?>
				<input type="hidden" name="<?=$k?>" value="<?=$v?>" />
			<?}?>
			<select name="sorting" id="sort_by" class="def sort-filter form__control brand-list__action brand-list__form-control js-onchange-sorting">
				<?foreach ($sorters as $sort_key => $sorter){?>
				<option<?if($active_sort_option === $sort_key){?> selected="selected"<?}?> value="<?=$sort_key?>"><?=str()->replace($sorter['name'], ' ', '&nbsp;')?></option>
				<?}?>
			</select>
		</form>
	</div>
<?}?>