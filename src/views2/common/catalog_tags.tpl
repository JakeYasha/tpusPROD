<? if ($items) { if(!isset($mode)){$mode = 'tags';}?>
	<div class="tags_field">
		<ul>
			<? /** PriceCatalog $item*/?>
			<?
			foreach ($items as $item) {
				if (!$item instanceof \App\Model\FirmType) {
					$item = $item['catalog'];
				}
				if (!$item->exists()) continue;
				$_filter = [];
				if(isset($filter['mode']) && $filter['mode'] != 'map') {
					$_filter['mode'] = $filter['mode'];
				}
				?>
			<li><a href="<?= (isset($link) && $link) ? app()->linkFilter($link, array_merge($_filter, ['id_catalog' => $item->id()])) : app()->link(app()->linkFilter($item->link(), $_filter))?>"><?= $item->name($mode)?></a></li>
		<? }?>
		</ul>
	<? if (count($items) > 6) {?>
			<div class="show_more">
				<div class="line"></div>
				<a href="#" class="js-show-more-tags"><span>Показать все</span></a>
			</div>
	<? }?>
	</div> 
	<?
}?>