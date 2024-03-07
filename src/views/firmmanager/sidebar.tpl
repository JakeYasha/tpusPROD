<div class="side_bar index_side_bar">
	<?=  app()->chunk()->render('common.header_tp_logo')?>
	<div class="side_menu_field">
		<ul class="main_top_menu">
			<?/*<li class="cms_tree_first cms_tree_last"><a style="width: 90%;" href="<?=$firm->link()?>"><?=$firm->name()?></a></li>*/?>
		</ul>
		<ul class="main_middle_menu">
			<?  foreach ($menu as $elem) {?>
			<li class="<?=isset($elem['last']) ? 'cms_tree_last' : (isset($elem['first']) ? 'cms_tree_first' : '')?><?=$elem['active'] ? ' active' : ''?>"><div class="clearfix"><a<?if($elem['count'] === null){?> style="width: 100%"<?}?> href="<?=$elem['link']?>" class="name"><?=$elem['name']?></a><?if($elem['count'] !== null){?><?if($elem['count']['new']>0){?><span class="count new"><a href="<?=$elem['link']?>">+<?=$elem['count']['new']?></a></span><?} else {?><span class="count new"><?=$elem['count']['all']?></span><?}?><?}?></div></li>
			<?}?>
		</ul>
	</div>
</div>