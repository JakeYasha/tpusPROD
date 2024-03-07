<? if ($items) {?>
	<div class="tags_field">
		<ul>
			<? /* @var $item PriceCatalog */?>
			<?
			foreach ($items as $item) {
				if (!$item instanceof \App\Model\FirmType) {
					$item = $item['catalog'];
				}
				if (!$item->exists()) continue;
				?>
                    <li><a href="<?= (isset($link) && $link) ? ($item->id_subgroup() > 0 ? app()->linkFilter($link, array_merge($filter, ['page' => null, 'id_group' => $item->id_group(), 'id_subgroup' => $item->id_subgroup()])) : app()->linkFilter($link, array_merge($filter, ['page' => null, 'id_group' => $item->id_group()]))) : app()->link(app()->linkFilter($item->link(), $filter))?>"><?= $item->name()?></a></li>
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