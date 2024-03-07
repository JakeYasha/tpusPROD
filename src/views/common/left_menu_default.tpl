<? if (!empty($items)) {?>
	<ul class="main_bottom_menu">
		<?
		$i = 0;
		foreach ($items as $_item) {
			$i++;
                        $class = array();
                        if ($i == 1) $class []= 'cms_tree_first';
                        if ($i == count($items)) $class []= 'cms_tree_last';
                        if ($_item['is_active']) $class []= 'active';
                        
			?>
			<li <?=(count($class) > 0 ? 'class="' . implode(' ', array_filter($class)) . '"' : '')?>>
                                <?if ($_item['is_active']) {?>
                                        <span <?if (!empty($_item['style_class'])) {?>class="menu_item <?=$_item['style_class']?>"<?} else {?>class="menu_item"<?}?>><?= $_item['name']?></span>
                                <?} else {?>
                                        <a href="<?= $_item['link']?>" <?if (!empty($_item['style_class'])) {?>class="<?=$_item['style_class']?>"<?}?>><?= $_item['name']?></a>
                                <?}?>
			</li>
		<? }?>
	</ul>
<? }?>


