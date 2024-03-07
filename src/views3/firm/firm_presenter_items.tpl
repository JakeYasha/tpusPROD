<?= app()->chunk()->render('adv.top_banners')?>
<div class="brand-list">
		<?
		$j = 0;
		$cnt = count($items);
		foreach ($items as $item) {
			$j++;
			?>
			<?= app()->chunk()->set('active_catalog', isset($active_catalog) ? $active_catalog : null)->set('item', $item)->set('catalogs_count', isset($catalogs_count) ? $catalogs_count : [])->set('special_price_links', isset($special_price_links) ? $special_price_links : [])->set('catalogs', isset($catalogs) ? $catalogs : [])->set('childs', isset($childs) ? $childs : [])->set('presenter', $presenter)->render('firm.chunk_firm_list_element')?>
			<? if ($j == ceil($cnt / 2)) {?>
				<?= app()->chunk()->render('adv.middle_banners')?>
        <? }?>
<? } ?>
	<?/*= app()->chunk()->render('adv.side_banners')*/?>
</div>