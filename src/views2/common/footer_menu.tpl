<?if($items){?>
<div class="footer_menu">
	<?  foreach ($items as $_item) {?>
	<ul>
		<li class="cms_tree_first"><?=$_item['name']?></li>
		<?foreach ($_item['subitems'] as $__item){?>
		<li><a href="<?=  str()->replace($__item['link'], '_L_', app()->location()->currentId())?>"><?=$__item['name']?></a></li>
		<?}?>
	</ul>
	<?}?>
</div>
<?}?>