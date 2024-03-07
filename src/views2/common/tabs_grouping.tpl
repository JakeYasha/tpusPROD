<?if($groupers){?>
	<div class="sort_by_field">
		<form action="<?=isset($link) && !empty($link) ? $link : '#'?>" method="get">
			<? foreach ($filters as $k=>$v) {?>
				<input type="hidden" name="<?=$k?>" value="<?=$v?>" />
			<?}?>
			<select name="group" id="sort_by" class="js-onchange-sorting">
				<?foreach ($groupers as $group_key => $grouper){?>
					<option<?if($active_group_option === $group_key){?> selected="selected"<?}?> value="<?=$group_key?>">Группировать <?=$grouper['name']?></option>
				<?}?>
			</select>
		</form>
	</div>
<?}?>