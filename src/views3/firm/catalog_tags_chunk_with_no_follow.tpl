<? if ($items) {?>
	<div class="tags_field">
		<ul class="list read-more__list product-list">
			<? /* @var $item PriceCatalog */?>
			<?
			foreach ($items as $item) {
				if (!$item instanceof FirmType) {
					$item = $item['catalog'];
				}
				if ($item == null || !$item->exists()) continue;
				$_filter = [];
				if(isset($filter['mode']) && $filter['mode'] != 'map') {
					$_filter['mode'] = $filter['mode'];
				}
				?>
			<li class="read-more__list--item"><a href="<?= (isset($link) && $link) ? app()->linkFilter($link, array_merge($_filter, ['id_catalog' => $item->id()])) : app()->link(app()->linkFilter($item->link(), $_filter))?>" rel="nofollow"><?= $item->name('tags')?></a></li>
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