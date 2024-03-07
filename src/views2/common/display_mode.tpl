<?$mode=$filters['display_mode'];?>
<div class="display-type">
	<ul>
		<?if(app()->getVar('on_map_link', false)){?>
		<li><a rel="nofollow" href="<?=app()->getVar('on_map_link')?>" class="on-map">На карте</a></li>
		<?}?>
		<li>
			<? if (!(!isset($mode) || $mode === '' || $mode === 'list')) {?>
			<a class="list-type<?= (!isset($mode) || $mode === '' || $mode === 'list') ? ' active' : ''?>" href="<?= app()->linkFilter($link, array_merge($filters, ['display_mode' => null]))?>" title="Отображать списком">
				<span></span><span></span><span></span>
			</a>
			<?} else {?>
			<div class="list-type">
				<span></span><span></span><span></span>
			</div>
			<?}?>
		</li>
		<li>
			<? if (!(isset($mode) && $mode === 'table')) {?>
			<a class="table-type<?= (isset($mode) && $mode === 'table') ? ' active' : ''?>" rel="nofollow" href="<?= app()->linkFilter($link, array_merge($filters, ['display_mode' => 'table']))?>" title="Отображать таблицей">
				<span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
			</a>
			<?} else {?>
			<div class="table-type">
				<span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
			</div>
			<?}?>
		</li>
	</ul>
</div>