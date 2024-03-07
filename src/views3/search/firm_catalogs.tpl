<div class="firm-catalog" style="margin-bottom: 20px;">
	<div class="black-block" style="margin: 0;">Подходящие рубрики каталога фирм по запросу &QUOT;<?= encode($query)?>&QUOT;</div>
	<? foreach ($matrix as $id_parent => $childs) {if(!isset($items[$id_parent]))continue;?>
		<h2 style="margin: 12px 0 3px 0;"><a href="<?= app()->link($items[$id_parent]->link($search_links ? $query : null))?>"><?= $items[$id_parent]->name()?></a></h2>
		<div class="tags_field">
			<ul>
				<? foreach ($childs as $id_catalog) {?>
					<li><a href="<?= app()->link($items[$id_catalog]->link($search_links ? $query : null))?>"><?= $items[$id_catalog]->name()?></a></li>
				<? }?>
			</ul>
			<? if (count($childs) > 6) {?>
				<div class="show_more">
					<div class="line"></div>
					<a href="#" class="js-show-more-tags"><span>Показать все</span></a>
				</div>
			<? }?>
		</div>
	<? }?>
</div>