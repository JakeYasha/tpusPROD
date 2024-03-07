<div class="show_list_tab">
	<?$mode=$filters['display_mode'];?>
	<span class="display-list<?= (!isset($mode) || $mode === '' || $mode === 'list') ? ' active' : ''?>">
	<? if (!(!isset($mode) || $mode === '' || $mode === 'list')) {?><a href="<?= app()->linkFilter($link, array_merge($filters, ['display_mode' => null]))?>" title="Отображать списком"></a><?}?>
	</span>
	<span class="display-cell<?= (isset($mode) && $mode === 'table') ? ' active' : ''?>">
       <? if (!(isset($mode) && $mode === 'table')) {?> <a rel="nofollow" href="<?= app()->linkFilter($link, array_merge($filters, ['display_mode' => 'table']))?>" title="Отображать таблицей"></a><?}?>
        </span>
</div>