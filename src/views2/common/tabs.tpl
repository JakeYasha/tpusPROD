<?if($tabs){?>
<div class="search-result-top<?if($additional_wrapper){?> <?=$additional_wrapper?><?}?>">
	<div class="search-result-navigation">
		<div class="search-menu">
			<?if($counters){?><span class="span-found">найдено:</span><?}?>
			<ul class="js-show-tabs-menu-wrapper">
				<?$i=-1;foreach($tabs as $tab){$i++;if(isset($tab['display']) && $tab['display']===false)continue;
				if($tab['label'] === 'На карте')continue;
				?>
					<?if($active_tab_index===$i){?>
					<li class="active"><span><?=$tab['label']?><?if(isset($counters[$i]) && $counters[$i] !== null){?> <span><?=$counters[$i]?></span><?}?></span></li>
					<?} else {?>
					<li><a <?if(isset($tab['nofollow']) && $tab['nofollow']===true){?>rel="nofollow"<?}?> href="<?=$tab['link']?>"><?=$tab['label']?><?if(isset($counters[$i]) && $counters[$i] !== null){?> <span><?=$counters[$i]?></span><?}?></a></li>
				<?}?>
				<?}?>
			</ul>
			<?/*if($additional_wrapper === 'firm-search-menu'){?><a href="#" class="show-menu js-show-tabs-menu"><span></span><span></span><span></span></a><?}*/?>
		</div>
		<?if($sorters && !$hide_sorting){?>
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
	</div>
	<?if($show_display_modes){?>
	<?= app()->chunk()->set('filters', $filters)->set('link', $link)->render('common.display_mode')?>
	<?} elseif(app()->getVar('on_map_link', false)) {?>
	<div class="display-type">
		<ul>
			<li><a rel="nofollow" href="<?=app()->getVar('on_map_link')?>" class="on-map">На карте</a></li>
		</ul>
	</div>
	<?}?>
</div>
<?}?>