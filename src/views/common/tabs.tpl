<?if($tabs){?>
<div class="search_result_top">
	<?if($counters){?><span class="found">найдено:</span><?}?>
	<ul class="search_tabs">
		<?$i=-1;foreach($tabs as $tab){$i++;if(isset($tab['display']) && $tab['display']===false)continue;?>
                        <?if($active_tab_index===$i){?>
                                <li class="cms_tree_current"><span class="tab_title"><?=$tab['label']?><?if(isset($counters[$i]) && $counters[$i] !== null){?> <span class="count"><?=$counters[$i]?></span><?}?></span></li>
                        <?} else {?>
                                <li><a <?if(isset($tab['nofollow']) && $tab['nofollow']===true){?>rel="nofollow"<?}?> href="<?=$tab['link']?>"><?=$tab['label']?><?if(isset($counters[$i]) && $counters[$i] !== null){?> <span class="count"><?=$counters[$i]?></span><?}?></a></li>
                        <?}?>
		<?}?>
	</ul>
	<?if($sorters && !$hide_sorting){?>
	<?if($show_display_modes){?>
	<?= app()->chunk()->set('filters', $filters)->set('link', $link)->render('common.display_mode')?>
	<?}?>
	<div class="sort_by_field">
		<form action="<?=isset($link) && !empty($link) ? $link : '#'?>" method="get">
			<? foreach ($filters as $k=>$v) {if($v){?>
				<input type="hidden" name="<?=$k?>" value="<?=$v?>" />
				<?}?>
			<?}?>
			<select name="sorting" id="sort_by" class="js-onchange-sorting">
				<?foreach ($sorters as $sort_key => $sorter){?>
				<option<?if($active_sort_option === $sort_key){?> selected="selected"<?}?> value="<?=$sort_key?>">Сортировать&nbsp;<?=str()->replace($sorter['name'], ' ', '&nbsp;')?></option>
				<?}?>
			</select>
		</form>
	</div>
	<?}?>
	<?if($groupers){?>
	<div class="sort_by_field">
		<form action="<?=isset($link) && !empty($link) ? $link : '#'?>" method="get">
			<? foreach ($filters as $k=>$v) {if($v && $k != 'group'){?>
				<input type="hidden" name="<?=$k?>" value="<?=$v?>" />
				<?}?>
			<?}?>
			<select name="group" id="sort_by" class="js-onchange-sorting">
				<?foreach ($groupers as $group_key => $grouper){?>
					<option<?if($active_group_option === $group_key){?> selected="selected"<?}?> value="<?=$group_key?>">Группировать <?=$grouper['name']?></option>
				<?}?>
			</select>
		</form>
	</div>
	<?}?>
</div>
<?}?>
