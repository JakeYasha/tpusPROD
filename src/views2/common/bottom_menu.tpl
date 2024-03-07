<?if (!empty($items)) {?>
<div class="footer-menu">
	<ul class="footer-menu-list">
<?	foreach ($items as $_item) {?>
		<li>
			<h3><?=$_item['name']?></h3>
<?		if (!empty($_item['subitems'])) {?>
			<ul class="footer-submenu">
<?			foreach ($_item['subitems'] as $_subitem) {?>
				<li><a href="<?=$_subitem['link']?>"><?=$_subitem['name']?></a></li>
<?			}?>
			</ul>
<?		}?>
		</li>
<?	}?>
	</ul>
</div>
<?}?>
