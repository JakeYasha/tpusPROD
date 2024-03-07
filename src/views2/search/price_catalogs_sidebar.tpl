<?if($matrix){?>
<ul class="js-sidebar-price-catalogs">
	<li><a href="<?=app()->linkFilter($url, $filters, ['id_catalog' => false])?>">Все разделы</a>
	<?$i=0;$count = count($matrix);foreach ($matrix as $id_parent => $childs) {$i++;?>
	<li<?if($i>3){?> class="js-sidebar-price-catalogs-elems"<?}?>><span><?= $items[$id_parent]->name()?></span>
		<?if($childs){?>
		<ul>
			<? foreach ($childs as $id_catalog) {if(!isset($items[$id_catalog]))continue;?>
			<li><a<?if((int)$filters['id_catalog'] === (int)$id_catalog){?> class="active"<?}?> href="<?=app()->linkFilter($url, $filters, ['id_catalog' => $items[$id_catalog]->id()])?>"><?= $items[$id_catalog]->name()?></a></li>
			<? }?>
		</ul>
		<?}?>
	</li>
	<?}?>
	<?if($count > 3) {?>
	<li class="sidebar-show-more"><a href="#" class="js-action js-sidebar-price-catalog-show-more">показать все</a></li>
	<?}?>
</ul>
<?}?>